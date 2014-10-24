<?php

  /**
   * Search
   */

  class SmsKeywordSearch extends SmsKeywordController {

    /**
     * Initialize
     */
    function initialize() {

      // Search
      $search = $this->search();
      // Limit 5
      $search->take(5);
      // Set text
      $results = array();
      // Get
      $get = $search->get();

      // Get
      if (!$get->isEmpty()) {
        // If single
        if ($get->count() == 1) {
          // Return
          return $this->single($get[0]);
        }

        foreach ($get as $i=> $establishment) {
          // Append to text
          $results[] = chr($i + 65) . '. ' . $establishment->name;
        }
      } else {
        // No results
        $results[] = 'No results found';
      }

      $this->response = implode("\n", $results);

      // return
      return $this;
    }

    /**
     * Single only
     */
    function single($establishment) {

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

      // Set response
      $this->response = implode("\n", $lines);

      return $this;
    }

    /**
     * Search
     */
    function search($keyword = false) {

      if ($keyword === false) {
        $keyword = $this->args;
      }
      // Do query
      $query = $this->api->filters($keyword, array(
        // Near, within, and budget
        'near',
        'within',
        'budget'
      ));

      // Get establishment
      $establishments = array();
      // Get filters
      $filters = isset($query['filters']) ? $query['filters'] : null;

      // Set address
      $address = null;
      // Use near
      $useNear = false;

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
          $establishment = Establishment::fillColumns();
          // Filter
          $establishments[] = $useNear ? 
            // Use near
            Establishment::near($loc['center']['lat'], $loc['center']['lng'], $loc['radius'], $establishment) :
            // Use within
            Establishment::within($loc['center']['lat'], $loc['center']['lng'], $loc['radius'], $establishment);
        }
      }

      // If there's still no establishments, append single
      if (!$establishments) $establishments[] = Establishment::fillColumns();

      // Do search
      if (isset($query['query']) && $query['query']) {
        // Loop through
        foreach ($establishments as $establishment) {
          // Set query
          Establishment::search($query['query'], $establishment);
        }
      }

      // Do budget
      if (isset($filters['budget']) && $filters['budget']) {
        // Loop through
        foreach ($establishments as $establishment) {
          // Set query
          Establishment::budget($filters['budget'], $establishment);
        }
      }

      // Search
      $search = $establishments[0];

      if (($count = count($establishments)) > 1) {
        // Loop
        for ($i = 1; $i < $count; $i++) {
          // Union
          $search->union($establishments[$i]->getQuery());
        }
      }
      // Return
      return $search;
    }
  }