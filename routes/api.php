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



Route::get('v1/files/favorites/{id?}', 'Api\FilesController@showFavorites')->middleware('auth:api');
Route::get('v1/files/tag/{id}/{id2?}', 'Api\FilesController@showTag')->middleware('auth:api');
Route::resource('v1/file', Api\FileController::class)->middleware('auth:api');
Route::get('v1/user/find/{nickname}', 'Api\UserController@findUser')->middleware('auth:api');
// Route::get('v1/file/extended/{id}', 'Api\FileController@showExtended')->middleware('auth:api');

Route::get('v1/files/shareforme', 'Api\FilesController@showShareForMe')->middleware('auth:api');
Route::get('v1/files/sharebyme', 'Api\FilesController@showShareByMe')->middleware('auth:api');
Route::resource('v1/files', Api\FilesController::class)->middleware('auth:api');

Route::get('v1/config', 'Api\ConfigController@getConfig')->middleware('auth:api');

// Route::get('v1/files/extended/{id?}', 'Api\FilesController@showExtended')->middleware('auth:api');

Route::get('/test', function () {
    return ['test'];
})->middleware('auth:api');
