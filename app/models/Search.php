<?php

  /**
   * Search
   */

  class Search extends Eloquent {

    /**
     * Ignore words
     */
    static $ignoreWords = array('and', 'the', 'for', 'in', 'on', 'at', 'of');

    /** 
     * Table
     */
    protected $table = 'searches';
    /**
     * Set fillable
     */
    protected $fillable = array('id', 'query', 'location', 'bounds', 'user_id');

    /**
     * Set address
     */
    private $address = '';
    /**
     * Results
     */
    private $results = array();
    /**
     * Count
     */
    private $count = 0;
    /**
     * Set start
     */
    private $start = 0;
    /**
     * Set limit
     */
    private $limit = 0;
    /**
     * Used near
     */
    private $usedNear = false;

    /**
     * Properties
     */
    static $properties = array('address', 'results', 'count', 'start', 'limit');

    /**
     * Get
     */
    function __get($name) {
      // If exists
      if (in_array($name, static::$properties)) {
        // Return
        return $this->$name;
      }
      // Return
      return parent::__get($name);
    }

    /**
     * call
     */
    function __call($name, $args) {
      // Get near
      if (substr($name, 0, 3) == 'get') {
        // Get last
        $property = strtolower(substr($name, 3));

        if (in_array($property, static::$properties)) {
          // Return
          return $this->$property;
        }
      }
      // Return
      return parent::__call($name, $args);
    }

    /**
     * Used near
     */
    function usedNear() {
      // Return
      return $this->usedNear;
    }

    /**
     * Query
     */
    function initialize($start = 0, $limit = 5) {

      // Do query
      $keyword = parseQueryFilters($this->query, array(
        // Near, within, and budget
        'near',
        'within',
        'budget'
      ));
      // Get establishment
      $establishments = array();

      // Get filters
      $filters = isset($keyword['filters']) ? $keyword['filters'] : null;

      // Set address
      $address = null;
      // Use near
      $useNear = false;

      // Clean query
      $cleanQuery = '';
      // If there's query
      if (isset($keyword['query'])) {
        // Get clean query
        $cleanQuery = cleanupTags($keyword['query'], static::$ignoreWords, true);
      }

      // If near
      if (isset($filters['near']) && $filters['near']) {
        // Use near
        $useNear = true;
        // Set address
        $address = $filters['near'];
        // If within
      } elseif (isset($filters['within']) && $filters['within']) {
        // Set address
        $address = $filters['within'];
      }

      // Locate
      $locate = $address ? Maps::locate($address, $this->bounds ? $this->bounds() : null) : array();

      // Set address
      $this->address = $address ? $address : $this->location;

      // If there's locate
      if ($address && $locate) {
        // Loop through locate
        foreach ($locate as $loc) {
          // Declare establishment initially
          $establishment = new Establishment();
          // callback
          $callback = $useNear ? 'near' : 'within';
          // Call
          $establishment->$callback($loc->center->lat, $loc->center->lng, $loc->radius);
          // Add select
          $establishment->qAddSelect($establishment->qQuote($loc->address) . ' `from`');
          // Add
          $establishments[] = $establishment;
        }
      }

      // If there's no address
      if (!$address) {
        // Still filter
        $establishment = new Establishment();
        // Set center
        $center = $this->center();
        // Use within
        $establishment->within($center->lat, $center->lng, $this->radius());
        // Add select
        $establishment->qAddSelect($establishment->qQuote($this->location) . ' `from`');
        // Add to establishments
        $establishments[] = $establishment;
      }

      // If there's address, but no locate
      if ($address && !$locate) {
        // Return
        return $this;
      }

      // If there's still no establishments, append single
      if (!$establishments) $establishments[] = new Establishment();

      // Do search
      if ($cleanQuery) {
        // Loop through
        foreach ($establishments as $establishment) {
          // Set query
          $establishment->search($cleanQuery);
        }
      }

      // Do budget
      if (isset($filters['budget']) && $filters['budget']) {
        // Loop through
        foreach ($establishments as $establishment) {
          // Set budget
          $establishment->budget($filters['budget']);
        }
      }

      // Search
      $search = $establishments[0];
      // Counter
      $counter = new Establishment();

      // Union
      foreach ($establishments as $i=> $establishment) {
        // If first, skip
        if ($i == 0) continue;
        // Union
        $search->qUnion($establishment);
      }

      // Set from
      $finalSearch = new Establishment();
      // Set
      $finalSearch->qFrom($search, '`query`');

      // For counter
      $forCounter = clone $search;
      // Set final counter
      $finalCounter = new Establishment();
      // Add field and from
      $finalCounter->qSelect('count(*) `total`')
                   ->qFrom($forCounter, '`counter`');
      // Do count
      $getCount = $finalCounter->qGet(false);
      // Set count
      $count = $getCount[0]->total;

      // Order
      if ($cleanQuery) $finalSearch->qOrder('`relevance`', 'desc');
      // Limit
      $finalSearch->qLimit($start, $limit);

      // Set results
      $this->results = $finalSearch->qGet();
      // Set count
      $this->count = $count;
      // Set start
      $this->start = $start;
      // Set limit
      $this->limit = $limit;

      // Set near
      $this->usedNear = $useNear;

      // Return self
      return $this;
    }

    /**
     * Get first
     */
    function getFirst() {
      // Return
      return isset($this->results[0]) ? $this->results[0] : null;
    }

    /**
     * Execute a search
     */
    static function execute($query, User $user = null, $start = 0, $limit = 5) {

      /**
       * Create a search
       */
      $search = static::create(array(
        'query'   => $query,
        'location'=> ($user && $user->location) ? $user->location : 'Philippines',
        'bounds'  => ($user && $user->bounds) ? (string) $user->bounds : ((string) Maps::philippines()),
        'user_id' => $user ? $user->id : 0
      ));

      // Save
      $search->save();
      // Return with query
      return $search->initialize($start, $limit);
    }

    /**
     * Get user
     */
    function user() {
      // Get user
      return User::where('id', '=', $this->user_id)->first();
    }

    /**
     * Get bounds
     */
    function bounds() {
      // Return json
      return $this->bounds ? new Bounds($this->bounds) : false;
    }

    /**
     * Get center
     */
    function center() {
      // Return center
      return $this->bounds ? Maps::boundsCenter($this->bounds()) : false;
    }

    /**
     * Get radius
     */
    function radius() {
      // Return radius
      return $this->bounds ? Maps::boundsRadius($this->bounds()) : false;
    }

  }