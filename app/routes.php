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
/*
    mockup
    author: abz
*/

Route::get('search', 'UsersController@search');
Route::get('announcements', 'UsersController@announcements');

Route::get('home', function() {
    return View::make('home');
});
