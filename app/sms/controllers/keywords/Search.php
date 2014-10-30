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
      $search = Search::execute($this->args, $this->api->user, 0, static::PULL_LIMIT);
      // Set count
      $count = $search->getCount();

      // Unsubscribe back
      $this->api->user->session('back')->unsubscribe();

      // If there's any
      if ($count >= 1) {

        // If single
        if ($count == 1) {
          // Pull single
          $this->pullSingle($search->getFirst());
          // If multiple
        } else {
          // Pull multiple
          $this->pullMultiple($search);
        }

      } else {
        // Nothing
        $this->response = 'No results found. Try switching your current location to a broader address. Reply with "location <address>", e.g. "location metro manila".';
      }

      // return
      return $this;
    }

  }