<?php

  /**
   * Search
   */

  class SmsKeywordSearch extends SmsKeywordController {

    /**
     * Initialize
     */
    function initialize() {

      // Do search
      $search = Search::execute($this->args, 0, 5);
      // If there's any
      if ($search->getCount() > 0) {

        foreach ($search->getResults() as $i=> $result) {
          // Print
          echo '#' . ($i+1) . ' ' . $result->name . ($search->usedNear() ? (' ('.number_format($result->distance / 1000, 3).'km)') : ''),PHP_EOL;
        }

        echo PHP_EOL,'Found ',$search->getCount(),' establishment/s';

      } else {
        // Nothing
        $this->response = 'No result/s found';
      }
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

  }