<?php

  /**
   * Category model
   */

  class Category extends Eloquent {

    // User table
    protected $table = 'categories';

    // Fillable
    protected $fillable = array('name', 'description', 'tags', 'parent_id');

    /**
     * New category
     */
    static function createNew($fields, $parentId = 0) {

      // If there's no name
      if (!isset($fields['name']) || !$fields['name']) {
        // Return
        return false;
      }
      // Set parent to fields
      $fields['parent_id'] = $parentId;
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

    /**
     * Parent
     */
    function parent() {
      // Return parent
      return $this->belongsTo('Category', 'parent_id')->first();
    }

    /**
     * Get all tags, including parents
     */
    function getAllTags() {
      // Set tags
      $tags = array();
      // Set current category
      $category = $this;
      // While there's category
      while ($category && $category->id) {
        // Append tags
        $tags = array_merge($tags, explode(' ', Str::ascii(Str::lower($category->name))));
        // Get parent
        $category = $category->parent();
      }
      // Return tags
      return $tags;
    }

  }