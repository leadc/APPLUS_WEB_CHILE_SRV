<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/test', function (Request $request) {
    return "VALIDO";
});

/*
    Route::prefix('users')->group(function () {
        Route::post('login', 'UserController@Login')->middleware('loginToken');
        Route::post('checkLogin', 'UserController@CheckLogin')->middleware('CheckLogin');
        Route::get('getUsers', 'UserController@GetUsers')->middleware('autentication');
        Route::get('getUserById', 'UserController@GetUserById')->middleware('autentication');
        Route::post('createUser', 'UserController@CreateUser')->middleware('autentication');
        Route::post('setUser', 'UserController@SetUser')->middleware('autentication');
        Route::post('uploadImg', 'UserController@UploadImage')->middleware('autentication');
        Route::post('recoverPassword', 'UserController@RecoverPassword');
        Route::post('resetPassword', 'UserController@ResetPassword');
    });
 */