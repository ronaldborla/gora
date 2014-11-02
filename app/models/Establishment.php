<?php

  /**
   * Establishment model
   */

  class Establishment extends Query {

    // User table
    protected $table = 'establishments';

    // Fillable
    protected $fillable = array('id', 'name', 'address', 'lat', 'lng', 'price_min', 'price_max', 'tags', 'user_id', 'distance', 'from', 'relevance');

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
     * Within a certain radius
     */
    function within($lat, $lng, $radius = 1000) {
      // Set select
      $select = '(degrees(acos((sin(radians('.$lat.')) * sin(radians(lat))) + (cos(radians('.$lat.')) * cos(radians(lat)) * cos(radians('.$lng.' - lng))))) * 60 * 1.1515 * 1.609344 * 1000) `distance`';
      // Add select field
      $this->qAddSelect($select);
      // Having
      $this->qHaving('`distance` <= ' . $radius);
      // Return
      return $this;
    }

    /**
     * Near
     */
    function near($lat, $lng, $radius = 1000) {
      // Just extend radius by 200 and use within
      return $this->within($lat, $lng, $radius + 1000);
    }

    /**
     * Budget
     */
    function budget($range) {
      // Convert to lower case
      $range = Str::lower($range);
      // Str to use to split
      $str = array('-', 'to', ' ');

      $arrRange = array();
      // Loop through str
      foreach ($str as $s) {
        // If str is found
        if (strpos($range, $s) !== false) {
          // Explode
          $arrRange = explode($s, $range);
        }
      }
      // If still empty
      if (!$arrRange) $arrRange = array($range);
      // Get range
      $lo = isset($arrRange[0]) ? intval($arrRange[0]) : 0;
      // Get hi
      $hi = isset($arrRange[1]) ? intval($arrRange[1]) : 0;

      // If there's lo
      if ($lo && $hi) {
        // Set where
        $this->qWhere('((`price_min` <= ' . $lo . ' and `price_max` >= ' . $lo . ') or (`price_min` <= ' . $hi . ' and `price_max` >= ' . $hi . '))');
        // Or only one of them has value
      } elseif ($lo || $hi) {
        // Set budget
        $budget = $lo ? $lo : $hi;
        // Use budget
        $this->qWhere('(`price_min` <= ' . $budget . ' and `price_max` >= ' . $budget . ')');
      }
      // Return
      return $this;
    }

    /**
     * Search
     */
    function search($keyword) {
      // Query
      $select = 'match(tags) against ('. $this->qQuote($keyword) .' in boolean mode) `relevance`';
      // Add select
      $this->qAddSelect($select);
      // Add having
      $this->qHaving('`relevance` > 0');
      // Return
      return $this;
    }

    /**
     * Update tags
     */
    function updateTags() {
      // Set tags
      $tags = explode(' ', Str::ascii(Str::lower($this->name)));

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
      $this->tags = cleanupTags(implode(', ', array_unique($tags)));
      // Save
      $this->save();
      // Return
      return $this;
    }

    /**
     * Categories
     */
    function contacts() {
      // Get categories
      $contacts = Contact::where('establishment_id', '=', $this->id)
                         ->orderBy('order', 'asc')
                         ->get();
      // Arranged
      $allContacts = array();
      // Loop
      if (!$contacts->isEmpty()) {
        // Loop
        foreach ($contacts as $contact) {
          // Add
          if (!isset($allContacts[$contact->type])) {
            // Create
            $allContacts[$contact->type] = array();
          }
          // Append
          $allContacts[$contact->type][] = $contact;
        }
      }
      // Return
      return $allContacts;
    }

    /**
     * Make reservation
     */
    function makeReservation(User $user, $members = array()) {
      // Return
      return Reservation::make($this, $user, $members);
    }

    /**
     * Pull by gora
     */
    static function getGora(User $user) {
      // Pull a random restablishment
      // Note: in the future, there should be a much more intelligent way of pulling a restaurant
      $establishment = new Establishment();
      // Get bounds of user
      $bounds = $user->bounds ? $user->bounds() : Maps::philippines();
      // Get center of bounds
      $center = $bounds->getCenter();
      // Pull only those within the bounds of the current user
      $establishment->within($center->lat, $center->lng, $bounds->getRadius());

      // Pull randomly
      $establishment->qRand();
      // Get single
      $establishment->qLimit(0, 1);
      // Get and return
      return $establishment->qGet();

    }

  }