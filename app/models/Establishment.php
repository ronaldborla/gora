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

  }