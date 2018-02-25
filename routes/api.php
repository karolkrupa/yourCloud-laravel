<?php

use Illuminate\Http\Request;
use \App\Http\Controllers\Api\FilesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// File Api
Route::resource('v1/file', Api\FileController::class)->middleware('auth:api');
Route::post('v1/file/upload/{folderId}', 'Api\FileController@storeFile')->middleware('auth:api');


// Files Api
Route::get('v1/files/favorites/{id?}', 'Api\FilesController@showFavorites')->middleware('auth:api');
Route::get('v1/files/tag/{id}/{id2?}', 'Api\FilesController@showTag')->middleware('auth:api');
Route::get('v1/files/shareforme', 'Api\FilesController@showShareForMe')->middleware('auth:api');
Route::get('v1/files/sharebyme', 'Api\FilesController@showShareByMe')->middleware('auth:api');
Route::resource('v1/files', Api\FilesController::class)->middleware('auth:api');


// User Api
Route::get('v1/user/find/{nickname}', 'Api\UserController@findUser')->middleware('auth:api');
Route::post('v1/user/update/fullName/{userId?}', 'Api\UserController@updateFullName')->middleware('auth:api');
Route::post('v1/user/update/language', 'Api\UserController@updateLanguage')->middleware('auth:api');
Route::post('v1/user/update/password', 'Api\UserController@changePassword')->middleware('auth:api');


// Config Api
Route::get('v1/config', 'Api\ConfigController@getConfig')->middleware('auth:api');


Route::get('/test', function () {
    return ['test'];
})->middleware('auth:api');
