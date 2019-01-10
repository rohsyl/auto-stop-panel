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


Auth::routes(['register'=>false]);


Route::middleware('auth')->group(function(){

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('trip/{id?}', 'TripsController@map')->name('trip.map');
    Route::get('trips', 'TripsController@index')->name('trip.index');

    Route::get('stats', 'StatsController@index')->name('stats.index');

    Route::get('reports', 'ReportsController@index')->name('reports.index');
    Route::get('reports/{id}', 'ReportsController@details')->name('reports.details');
    Route::get('reports/{id}/read', 'ReportsController@read')->name('reports.readed');

    Route::get('alerts', 'AlertsController@index')->name('alerts.index');
    Route::get('alerts/{id}', 'AlertsController@details')->name('alerts.details');
    Route::get('alerts/{id}/read', 'AlertsController@read')->name('alerts.readed');

    Route::get('flag', 'FlagController@index')->name('flag.index');
    Route::get('reports/{id}/flag', 'FlagController@flag')->name('reports.flaged');
    Route::get('alerts/{id}/flag', 'FlagController@flag')->name('alerts.flaged');
    Route::get('flag/{id}/unflag', 'FlagController@unflag')->name('flag.unflaged');


});