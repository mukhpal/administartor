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


Route::post('register', 'App\Http\Controllers\Api\AuthController@register');
Route::post('login', 'App\Http\Controllers\Api\AuthController@login');
Route::post('emailotp', 'App\Http\Controllers\Api\AuthController@sendEmailOTP');
Route::post('verifyotp', 'App\Http\Controllers\Api\AuthController@verifyOTP');
Route::post('reset_password', 'App\Http\Controllers\Api\AuthController@resetPassword');

Route::middleware('auth:api')->group(function() {

    Route::get('user/{userId}/detail', 'App\Http\Controllers\Api\UserController@show');
    Route::post('user/change-password', 'App\Http\Controllers\Api\UserController@changePassword');
    Route::post('user/edit_profile', 'App\Http\Controllers\Api\UserController@editProfile');
    Route::post('user/remove_user', 'App\Http\Controllers\Api\UserController@deleteAccoutPermanentaly');
    
    Route::post('user/saverecording', 'App\Http\Controllers\Api\UserRecordingsController@saveRecording');
    Route::post('user/fetchrecording', 'App\Http\Controllers\Api\UserRecordingsController@fetchRecordings');
    Route::post('user/editrecording', 'App\Http\Controllers\Api\UserRecordingsController@editRecording');
    
    Route::post('get_jingles', 'App\Http\Controllers\Api\JingleController@getJingles');
    Route::post('user/feedback', 'App\Http\Controllers\Api\FeedbackController@userFeedback');

    Route::post('user/stripe_token', 'App\Http\Controllers\Api\StripeController@createToken');
    Route::post('user/payment', 'App\Http\Controllers\Api\StripeController@stripePayment');
});
