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
    static $properties = array('results', 'count', 'start', 'limit');

    /**
     * Get
     */
    function __get($name) {
      // If exists
      if (in_array($name, static::$properties)) {
        // Return
        return $this->$name;
      }
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
    function initialize($query, $start = 0, $limit = 5) {

      // Do query
      $keyword = parseQueryFilters($query, array(
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
      $locate = $address ? Maps::locate($address) : array();
      // If there's locate
      if ($locate) {
        // Loop through locate
        foreach ($locate as $loc) {
          // Declare establishment initially
          $establishment = new Establishment();
          // callback
          $callback = $useNear ? 'near' : 'within';
          // Call
          $establishment->$callback($loc['center']['lat'], $loc['center']['lng'], $loc['radius']);
          // Add select
          $establishment->qAddSelect($establishment->qQuote($loc['address']) . ' `from`');
          // Add
          $establishments[] = $establishment;
        }
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
     * Execute a search
     */
    static function execute($query) {
      /**
       * Create a search
       */
      $search = new static();
      // Return with query
      return $search->initialize($query);
    }

  }