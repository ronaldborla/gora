<?php

  /**
   * Category model
   */

  class Category extends Eloquent {

    // User table
    protected $table = 'category';

    // Fillable
    protected $fillable = array('name', 'description', 'tags', 'parent');

    /**
     * New category
     */
    static function createNew($fields, $parent = 0) {

      // If there's no name
      if (!isset($fields['name']) || !$fields['name']) {
        // Return
        return false;
      }
      // Set parent to fields
      $fields['parent'] = $parent;
      // If there's no tags
      if (!isset($fields['tags'])) {
        // Then copy from name
        $fields['tags'] = Str::lower($fields['name']);
      }

      // Find category
      $category = static::where('name', '=', Str::lower($fields['name']))->limit(1)->first();
      // If not found
      if (!$category || !$category->id) {
        // Create new
        $category = new static($fields);
        // Save
        $category->save();
      }
      // Return category
      return $category;
    }

  }