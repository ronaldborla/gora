<?php

  /**
   * Establishment model
   */

  class Establishment extends Eloquent {

    // User table
    protected $table = 'establishment';

    // Fillable
    protected $fillable = array('name', 'address', 'lat', 'lng', 'price_min', 'price_max', 'tags', 'user_id');

    /**
     * Update categories
     */
    function updateCategories($categories) {
      // Delete categories
      $this->getCategoryList()->delete();
      // If there's categories
      if (is_array($categories) && $categories) {
        // Order
        $order = 0;
        // Loop through each
        foreach ($categories as $category) {
          // If not category
          if (!($category instanceof Category)) {
            // Skip
            continue;
          }
          // Add to category list
          DB::table('category_list')->insert(array(
            // New category
            array('order'=> $order, 'category_id'=> $category->id, 'establishment_id'=> $this->id)
          ));
          // Increment order
          $order++;
        }
      }
      // Return self
      return $this;
    }

    /**
     * Get category list
     */
    function getCategoryList() {
      // Return
      return DB::table('category_list')->where('establishment_id', '=', $this->id);
    }

    /**
     * Get categories
     */
    function categories() {
      // Get list
      $list = $this->getCategoryList()->orderBy('order', 'asc')->lists('category_id');
      // If there's no list, exit
      if (!$list) return Category::where('id', '=', 0);
      // Set categories
      return Category::whereIn('id', $list);
    }

    /**
     * Fill *
     */
    static function fillColumns($query = null) {
      // Fill
      return $query ? $query->addSelect('*') : static::addSelect('*');
    }

    /**
     * Within a certain radius
     */
    static function within($lat, $lng, $radius = 1000, $query = null) {
      // Raw
      $rawSelect = DB::raw('(DEGREES(ACOS((SIN(RADIANS('.$lat.')) * SIN(RADIANS(lat))) + (COS(RADIANS('.$lat.')) * COS(RADIANS(lat)) * COS(RADIANS('.$lng.' - lng))))) * 60 * 1.1515 * 1.609344 * 1000) `distance`');
      // Set query
      $query = $query ? $query->addSelect($rawSelect) : static::addSelect($rawSelect);
      // Return
      return $query->having('distance', '<=', $radius);
    }

    /**
     * Near
     */
    static function near($lat, $lng, $radius = 1000, $query = null) {
      // Just extend radius by 200 and use within
      return static::within($lat, $lng, $radius + 200, $query);
    }

    /**
     * Search
     */
    static function search($keyword, $query = null) {
      // Set select
      $rawSelect = DB::raw('MATCH(tags) AGAINST ("'.mysql_real_escape_string($keyword).'" IN BOOLEAN MODE) `relevance`');
      // Set query
      $query = $query ? $query->addSelect($rawSelect) : static::addSelect($rawSelect);
      // Return
      return $query->having('relevance', '>', 0)->orderBy('relevance', 'desc');
    }

    /**
     * Update tags
     */
    function updateTags() {
      // Set tags
      $tags = array(Str::ascii(Str::lower($this->name)));

      // Add category tags
      $tags[] = 'restaurant';

      // Get categories
      $categories = $this->categories()->get();
      // If there are categories
      if ($categories) {
        // Loop through categories
        foreach ($categories as $category) {
          // Convert to ascii
          $tags = array_merge($tags, $category->getAllTags());
        }
      }
      // Update tags
      $this->tags = implode(', ', array_unique($tags));
      // Save
      $this->save();
      // Return
      return $this;
    }

  }