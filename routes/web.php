<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('trip/{id?}', 'TripsController@map')->name('trip.map');
Route::get('trips', 'TripsController@index')->name('trip.index');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
