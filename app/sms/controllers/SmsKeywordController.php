<?php

  /**
   * General request
   */

  class SmsKeywordController extends SmsBaseController {

    /**
     * Args
     */
    protected $args;

    /**
     * Mapped keywords and aliases
     */
    static $keywordMap = array(
      'back'        => array('prev', 'previous'),
      'gora'        => array(),
      'help'        => array('?'),
      'location'    => array('loc'),
      'more'        => array('next'),
      'name'        => array(),
      'password'    => array('pwd', 'passwd'),
      'reserve'     => array('res', 'rsv'),
      'search'      => array('find'),
      'subscribe'   => array('sub'),
      'unsubscribe' => array('unsub'),
    );

    /**
     * Default keyword refers to the query 
     * which doesn't fall into any available keyword
     */
    const DEFAULT_KEYWORD = 'search';
    /**
     * Pull limit
     */
    const PULL_LIMIT = 5;

    /**
     * Get
     */
    function __get($name) {
      // Get class
      $class = get_called_class();
      // If name is keyword
      if ($class != 'SmsKeywordController' && $name == 'keyword') {
        // Extract name
        return strtolower(substr($class, 10));
      }
    }

    /**
     * Pull single only
     */
    function pullSingle(Establishment $establishment) {

      // Lines
      $lines = array($establishment->name);
      // Address
      if ($establishment->address) {
        // Add
        $lines[] = 'Located at ' . $establishment->address;
      }
      // If there are contacts
      if ($contacts = $establishment->contacts()) {
        // Loop
        foreach ($contacts as $type=> $contact) {
          // Set values
          $values = array();
          // Loop
          foreach ($contact as $con) {
            // Append
            $values[] = $con->value;
          }
          // Add to lines
          $lines[] = ucfirst(Contact::getTypes($type)) . ': ' . implode(', ', $values);
        }
      }
      // If there's price range
      if ($establishment->price_min || $establishment->price_max) {
        // Then set price
        $line = 'Price Range: ' . number_format($establishment->price_min);
        // If there's max
        if ($establishment->price_max) {
          // Add
          $line .= ' - ' . number_format($establishment->price_max);
        }
        // Add line
        $lines[] = $line;
      }

      // Add reserve or gora
      $lines[] = '';
      $lines[] = 'Reply with "gora", "reserve", or "subscribe".';

      // Set response
      $this->response = implode("\n", $lines);
      
      // Subscribe to single
      $this->api->user->session('single')->subscribe(array(
        // Set establishment id
        'id'=> $establishment->id
      ));

      return $this;
    }


    /**
     * Minify address
     */
    function minifyAddress($address) {
      // Phils
      $phils = 'philippines';
      // Phils len
      $philsLen = strlen($phils);
      // Addess len
      $addressLen = strlen($address);

      // If last word is philippines, remove it
      if (strtolower(substr($address, $addressLen - $philsLen)) == $phils) {
        // Remove
        $address = substr($address, 0, $addressLen - $philsLen);
      }

      // trim address
      $address = trim($address, ' ,;');
      // If no address, set as philippines
      if (!$address) $address = ucfirst($phils);

      // Return
      return $address;
    }


    /**
     * Pull multiple
     */
    function pullMultiple(Search $search) {

      // Minify address
      $address = $this->minifyAddress($search->getAddress());

      // Get establishments
      $establishments = $search->getResults();
      // Count
      $count = $search->getCount();
      // Get start
      $start = $search->getStart();
      // Get limit
      $limit = $search->getLimit();

      // Set next
      $next = $start + $limit;

      // Used near
      $usedNear = $search->usedNear();

      // Set if there's more
      $theresMore = $count > $next;

      // Set message
      $message = array();
      // Set suffix
      $suffix = ($usedNear ? 'near' : 'within') . ' ' . $address . PHP_EOL;

      // If start is 0
      if ($start == 0) {
        $message[] = 'We found ' . $count . ' results ' . $suffix;
      } else {
        // If there's more
        if ($theresMore) {
          // Left
          $left = $count - $next;
          // Set
          $message[] = $left . ' result' . ( ($left > 1) ? 's' : '' ) . ' left ' . $suffix;
        }
      }

      // Set options
      $options = array();
      // Loop through results
      foreach ($establishments as $i=> $result) {
        // Set letter
        $letter = chr($i+65);
        // Print
        $message[] = $letter . '. ' . $result->name . ($usedNear ? (' ('.number_format($result->distance / 1000, 2).'km)') : '');
        // Set letter and result
        $options[$letter] = $result->id;
      }

      // Append empty
      $message[] = '';
      // Set 
      $message[] = 'Reply with letter of your choice (e.g. "A") to view info' . (
        $theresMore ? (PHP_EOL . 'Reply with "more" to view more results.') : '.'
      );

      // Implode
      $this->response = implode(PHP_EOL, $message);

      // Unsubsribe to single
      $this->api->user->session('single')->unsubscribe();
      // Subscribe
      $this->api->user->session('select')->subscribe(array(
        'action'=> 'single',
        'options'=> $options
      ));

      // Get more
      $more = $this->api->user->session('more');
      // If there's more
      if ($theresMore) {
        // Subscribe
        $more->subscribe(array(
          'id'=> $search->id,
          'start'=> $next
        ));
      } else {
        // Unsubscribe
        $more->unsubscribe();
      }

      return $this;

    }

    /** 
     * Get navigation
     */
    function getNavigation($nav) {
      // Get more
      $navigation = $this->api->user->session($nav);
      // If active
      if ($navigation->active()) {

        // Get options
        $options = $navigation->options();
        // Unsubscribe first
        $navigation->unsubscribe();

        // If there's search
        if (isset($options['id'])) {

          // Get search
          $search = Search::where('id', '=', intval($options['id']))->first();
          // If there's any
          if ($search && $search->id) {
            // Get start
            $start = isset($options['start']) ? intval($options['start']) : 0;
            // Initialize
            $search->initialize($start);

            // If there's any
            if ($search->getResults()) {
              // Then pull multiple
              $this->pullMultiple($search);

              // Set back
              $back = $start - static::PULL_LIMIT;

              // If back is greater than 0
              if ($back >= 0) {
                // Add back
                $this->api->user->session('back')->subscribe(array(
                  'id'=> $search->id,
                  'start'=> $back
                ));
              }
            }

          }

        }

      }
      // If there's no response
      if (!$this->response) {
        // Set
        $this->response = 'You have no pending search. Please try searching again.';
      }
      // Return
      return $this;
    }

    /**
     * Set location
     */
    function setLocation(Location $location) {
      // Set location of user
      $this->api->user->location = $location->address;
      $this->api->user->bounds = (string) $location->bounds;
      // save
      $this->api->user->save();
      // Set response
      $this->response = 'Your location has been set to ' . $location->address . '.';

      return $this;
    }

    /**
     * Process
     */
    function initialize() {

      // If there's already a keyword, return
      if ($this->keyword) {
        // Return
        return $this;
      }

      // If there's no message
      if (!trim($this->api->message)) {
        // Return 
        return $this;
      }

      /**
       * Parse message
       */
      $parse = parseQuery($this->api->message);

      if (($parse = $this->fixKeywordArgs($parse)) === false) {
        // Set keyword as search
        $parse['keyword'] = static::DEFAULT_KEYWORD;
        // Args as whole message
        $parse['args'] = $this->api->message;
      }

      // call and return
      return $this->call($parse['keyword'], $parse['args']);
    }

    /**
     * Call keyword controller
     */
    function call($keyword, $args = '') {
      // Ucfirst
      $keyword = ucfirst($keyword);
      // Set controller
      $controller = 'SmsKeyword' . $keyword;
      // Full
      $filename = $this->keywordsDir() . $keyword . '.php';

      // If file doesn't exist, return
      if (!is_file($filename)) return $this;
      // Require
      require_once($filename);
      
      // Initialize controller
      $this->response = $controller::instance($this->api)
                                   ->setArgs($args)
                                   ->initialize()
                                   ->response();
      // Return
      return $this;
    }

    /**
     * Alias to
     */
    function alias($keyword) {
      // Return
      $this->call($keyword, $this->args);
      // Return
      return $this;
    }

    /**
     * Get keywords dir
     */
    function keywordsDir() {
      // Return
      return dirname(__FILE__) . '/keywords/';
    }

    /**
     * Check if keyword is valid
     */
    function fixKeywordArgs($keywordArgs) {
      // Find keyword
      foreach (static::$keywordMap as $keyword=> $aliases) {
        // If keyword matches
        if ($keywordArgs['keyword'] == $keyword) {
          // Return
          return $keywordArgs;
        }
        // Loop through aliases
        foreach ($aliases as $alias) {
          // If found
          if ($keywordArgs['keyword'] == $alias) {
            // Set correct keyword
            $keywordArgs['keyword'] = $keyword;
            // Return
            return $keywordArgs;
          }
        }
      }

      // Check if keyword is a single letter
      // And there are no args
      if (strlen($keywordArgs['keyword']) == 1 && !trim($keywordArgs['args'])) {
        // Get ord
        $ord = ord($keywordArgs['keyword']);
        // If letter
        if ($ord >= 97 && $ord <= 122) {
          // Set keyword args
          $keywordArgs['keyword'] = 'select';
          // Set args
          $keywordArgs['args'] = chr($ord - 32);
          // Return
          return $keywordArgs;
        }
      }
      // Return false
      return false;
    }

    /**
     * Set args
     */
    function setArgs($args) {
      // Set
      $this->args = $args;
      // return
      return $this;
    }
  }