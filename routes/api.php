<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group(['namespace' => 'App\Http\Controllers'], function () {

    Route::post('/register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::get('/verify-email/{token}', 'AuthController@verifyEmail')->name('email.verify');
});