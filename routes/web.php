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

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', function () {
    return redirect('/' . Auth::user()->name);
})->middleware('auth');

//Route::resource('/folder', Folder::class);

Route::get('/test', function () {

});

Route::get('/logout', function () {
    Auth::logout();
    return "Wylogowany";
});

Route::any('{user_name}/{path?}', 'Folder@route')->where('path', '(.*)')->middleware('auth');
//Route::post('{user_name}/{path?}', 'Folder@store')->where('path', '(.*)')->middleware('auth');
