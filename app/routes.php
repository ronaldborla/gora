<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

return;

Route::get('', 'HomeController@home');
/**
 * Use SMS Controller
 */
Route::post('sms', 'SmsController@receiver');
/**
 * Test SMS Receiver
 */
Route::get('sms/test', 'SmsController@test');
Route::post('sms/test', 'SmsController@test');

Route::get('import/tripadvisor', 'ImportController@tripadvisor');
Route::post('import/tripadvisor', 'ImportController@tripadvisor');
Route::get('import/list', 'ImportController@elist');
Route::post('import/list', 'ImportController@elist');

/**
 * Refresh
 */
Route::get('refresh', function() {

  foreach (Establishment::all() as $establishment) {
    // Update
    $establishment->updateTags();
  }
  // Success
  echo 'Updated tags';

});

/**
 * Maps
 */
Route::get('maps', 'MapsController@home');

/*
    mockup
    author: abz
*/

Route::get('search', 'UsersController@search');
Route::get('announcements', 'UsersController@announcements');

Route::get('members/profile', 'MembersController@profile');
Route::get('members/subscriptions', 'MembersController@subscriptions');
Route::get('members/friends', 'MembersController@friends');

Route::get('home', function() {
    return View::make('home');
});
