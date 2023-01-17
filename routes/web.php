<?php

use Illuminate\Support\Facades\Route;

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


Route::get('/show_piece', 'CompanyController@index')->name('show_piece');
Route::get('/add_piece', 'CompanyController@create')->name('add_piece');
Route::post('/add_piece', 'CompanyController@store')->name('store_piece');
Auth::routes();

Route::get('posts', 'CompanyController@index');

