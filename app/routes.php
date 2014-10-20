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

/*
    mockup
    author: abz
*/
Route::get('mockup/home', function() {
    return View::make('home');
});

Route::get('import/tripadvisor', 'ImportController@tripadvisor');