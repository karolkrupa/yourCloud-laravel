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


Auth::routes();

Route::get('/', function() {
    return redirect('/files');
})->middleware('auth');

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
})->middleware('auth');

Route::get('/download/{id}', 'Api\FileController@authSend')->middleware('auth');
Route::get('/download/share/{id}', 'Api\FileController@sharedSend')->middleware('auth');

Route::get('/files/{path?}', 'MainController@index')->where('path', '(.*)')->middleware('auth');

Route::get('/settings/general', 'SettingsController@index')->middleware('auth');
Route::get('/settings/privacy', 'SettingsController@index')->middleware('auth');
Route::get('/settings/customization', 'SettingsController@index')->middleware('auth');









