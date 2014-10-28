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

if(Sentry::check()) {

    $user       = Sentry::findUserByID(Sentry::getUser()->id);
    $groups     = $user->getGroups();

    foreach($groups as $group)
    {
        $grou_user =  $group->name;
    }

} else {

    Route::get('login', 'UsersController@login');
    Route::get('register', 'UsersController@register');
    Route::post('register-user', 'UsersController@registerUser');
}

Route::post('authenticate', 'UsersController@authenticate');

Route::get('search', 'UsersController@search');
Route::get('announcements', 'UsersController@announcements');

// members
Route::get('members/profile', 'MembersController@profile');
Route::get('members/subscriptions', 'MembersController@subscriptions');
Route::get('members/friends', 'MembersController@friends');

// clients
Route::get('clients/credits', 'ClientsController@credits');
Route::get('clients/profile', 'ClientsController@profile');
Route::get('clients/establishment', 'ClientsController@establishment');
Route::get('clients/subscriptions', 'ClientsController@subscriptions');
Route::get('clients/subscribers', 'ClientsController@subscribers');
Route::get('clients/friends', 'ClientsController@friends');
Route::get('clients/credits', 'ClientsController@credits');
Route::get('clients/dashboard', 'ClientsController@dashboard');


Route::get('home', 'HomeController@home');

Route::get('logout', function() {
    Sentry::logout();
    return Redirect::to('login');
});
