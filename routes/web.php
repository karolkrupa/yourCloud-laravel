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
    return rederict('/files');
});

Route::get('/download/{id}', 'Api\FileController@authSend');
Route::get('/download/share/{id}', 'Api\FileController@sharedSend');

Route::get('/files/{path?}', 'MainController@index')->where('path', '(.*)');


Route::get('/logout', function () {
    Auth::logout();
    return "Wylogowany";
});






