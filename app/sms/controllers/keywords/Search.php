<?php

  /**
   * Search
   */

  class SmsKeywordSearch extends SmsKeywordController {

    /**
     * Initialize
     */
    function initialize() {



      // return
      return $this;
    }

    /**
     * Search
     */
    function search($keyword = false) {

      if ($keyword === false) {
        $keyword = $this->args;
      }
      // Parse
      $parse = $this->api->filters($keyword, array(
        // Near, within, and budget
        'near',
        'within',
        'budget'
      ));

    }
  }