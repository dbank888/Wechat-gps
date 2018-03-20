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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/code/{id}', 'CodeController@show')->name('code.show');
Route::post('/code', 'CodeController@store')->name('code.store');
Route::post('/code/activation', 'CodeController@activation')->name('code.activation');
Route::post('/code/clear', 'CodeController@clear')->name('code.clear');

Route::get('/visit/{id}', 'VisitController@show')->name('visit.show');
Route::get('/visit/map/{x}/{y}', 'VisitController@map')->name('visit.map');
Route::get('/track/{code}', 'TrackController@show')->name('track.show');
Route::post('/track/statics', 'TrackController@statics')->name('track.statics');