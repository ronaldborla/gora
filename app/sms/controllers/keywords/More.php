<?php

  /**
   * More Controller
   */

  class SmsKeywordMore extends SmsKeywordController {

    /**
     * Initialize
     */
    function initialize() {

      // Get more
      $more = $this->api->user->session('more');
      // If active
      if ($more->active()) {

        // Get options
        $options = $more->options();
        // Unsubscribe first
        $more->unsubscribe();

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
  }