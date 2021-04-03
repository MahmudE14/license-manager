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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// license registration
Route::get('licenses/create', 'LicenseController@create');
Route::post('licenses', 'LicenseController@store');
// license verification
Route::get('licenses/verify', 'LicenseController@showVerify')->middleware('auth');
Route::post('licenses/verify', 'LicenseController@verify')->middleware('auth');
// generate license
Route::post('licenses/createKey', 'LicenseController@createKey');
// get user details
Route::get('getUserDetails', 'LicenseController@getUserDetails');

