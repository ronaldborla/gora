<?php

  /**
   * Category seeder
   */

  class CategorySeeder extends Seeder {

    /**
     * Run
     */
    public function run() {

      // Truncated
      $this->command->info('Creating major category..');

      // Find category first
      $category = Category::where('name', '=', 'restaurant')->limit(1)->first();
      // If there's nothing
      if (!$category || !$category->id) {
        // Create
        $category = Category::createNew(array('name'=> 'restaurant'));
        // Save
        $category->save();
      }

      // Created
      $this->command->info('Category created!');

    }
  }