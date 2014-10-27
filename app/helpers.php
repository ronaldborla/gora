<?php

  /**
   * Helpers
   */

  /**
   * Cleanup tags used for searching
   */
  function cleanupTags($tags, $ignoreWords = array(), $addWildcard = false, $singularize = true) {
    // Convert to lowercase
    if (is_array($tags)) {
      // Set new tags
      $newTags = array();
      // Loop through each tags
      foreach ($tags as $tag) {
        // Recurse
        if ($newTag = cleanupTags($tag, $addWildcard)) {
          // Append
          $newTags[] = $newTag;
        }
      }
      // Return with imploded tags
      return $newTags ? implode('; ', $newTags) : '';
    }
    elseif (is_string($tags)) {
      // First is to convert unicode to ascii
      $tags = Str::ascii($tags);
      // Convert to lowercase
      $tags = Str::lower($tags);
      // Second is to convert some symbols to empty
      $tags = str_replace(array('`', '\''), '', $tags);
      // Replace all non-alphanumerics to space
      $tags = preg_replace('/[^a-z0-9\s]/', ' ', $tags);
      // Explode by spaces
      $arrTags = explode(' ', $tags);
      // Set new tags
      $newTags = array();
      // Loop through each
      foreach ($arrTags as $tag) {
        // Check if not empty
        if ($tag = trim($tag)) {
          // Singularize
          if ($singularize === true) $tag = Str::singular($tag);
          // If pluralize
          if ($singularize === false) $tag = Str::plural($tag);
          // Check if it's in the ignore words
          if (!in_array($tag, $ignoreWords)) {
            // Add to new tags padded with _ if less than 4 chars
            $newTags[] = str_pad($tag, 4, '_', STR_PAD_RIGHT).($addWildcard?'*':'');
          }
        }
      }
      // Implode and return
      return $newTags ? implode(' ', $newTags) : '';
    }
  }


  /**
   * Parse message
   */
  function parseQuery($query) {
    // Split by space
    $arrMessage = explode(' ', $query);

    // Get keyword
    $keyword = isset($arrMessage[0]) ? $arrMessage[0] : '';
    // Get args
    $args = isset($arrMessage[1]) ? implode(' ', array_slice($arrMessage, 1)) : '';

    // Return keyword and args
    return array(
      'keyword'=> strtolower($keyword),
      'args'=> $args
    );
  }

  /**
   * Get filters
   */
  function parseQueryFilters($query, $fields) {
    // Set filters
    $filters = array();
    // Locate all fields in message
    $filterPos = array();
    // If there are fields
    if (is_array($fields) && $fields) {
      // Loop through fields
      foreach ($fields as $field) {
        // Get length
        $len = strlen($field);
        // Find this field
        $pos = stripos($query, $field);

        if ($pos === false) continue;

        // Start
        $start = $pos + $len;

        if ((!isset($query[$pos - 1]) || $query[$pos - 1] == ' ') &&
            (!isset($query[$start]) || $query[$start] == ' ')) {
          // Set pos
          $filterPos[] = array(
            'field'=> $field,
            'len'=> $len,
            'pos'=> $pos,
            'start'=> $start
          );
        }
      }
    }
    // If there are filters
    if ($filterPos) {
      // Sort
      usort($filterPos, function($a, $b) {
        // Compare
        if ($a['pos'] == $b['pos']) return 0;
        if ($a['pos'] < $b['pos']) return -1;
        return 1;
      });
      // Loop through filters
      foreach ($filterPos as $i=> $fieldPos) {
        // Get length
        $length = false;
        // If there's a next
        if (isset($filterPos[$i + 1])) {
          // Get our length
          $length = $filterPos[$i + 1]['pos'] - $fieldPos['start'];
        }
        // Get content of field
        $filters[$fieldPos['field']] = trim(($length !== false) ?
                                            substr($query, $fieldPos['start'], $length) :
                                            substr($query, $fieldPos['start']));
      }
    }

    // Get query
    $remainingQuery = trim($filterPos ? substr($query, 0, $filterPos[0]['pos']) : $query);

    // echo $filterPos;

    // Return
    return array(
      'query'=> $remainingQuery,
      'filters'=> $filters
    );
  }