<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\AuthController;




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
Route::group(['middleware' => ['json.response']], function () {

  Route::group(['middleware' => 'web'], function () {
    // Route::get('login/google',[AuthController::class,'redirectToProvider']);// for website plugin 
    // Route::get('login/google/callback',[AuthController::class,'handleProviderCallback']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('login/socialmedia', [AuthController::class, 'login_google']);
    Route::post('password/forgotpassword', [AuthController::class, 'forgotpassword']);
    // Route::post('password/forgot-password', [AuthController::class, 'forgotpassword'])->name('passwords.sent');
    // Route::post('password/reset', [AuthController::class, 'reset']);
  });

  Route::middleware('auth:api')->group(function () {

    Route::post('resetpassword', [AuthController::class, 'ResetPassword']);
    Route::get('/logout',  [AuthController::class, 'logout']);
    Route::get('/user',  [AuthController::class, 'user']);

    // Route::get('/user',  [UserController::class, 'user']);

  });
});
