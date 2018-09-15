<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => 'throttle:40,1','cors'], function () {

    Route::post('/registration', 'AuthController@newUserWizard');
    Route::post('/login', 'AuthController@login');
    Route::post('/logout', 'AuthController@logout');


    Route::get('/', 'ApiUserController@index');
    Route::get('/{id}', 'ApiUserController@show');
    Route::post('/create', 'ApiUserController@create');
    Route::post('/update/{id}', 'ApiUserController@update');
    Route::post('/delete/{id}', 'ApiUserController@delete');

});

