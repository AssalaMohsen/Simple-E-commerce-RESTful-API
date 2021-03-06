<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::group(['middleware' => 'auth:sanctum'], function () {
  Route::apiResource('/products', ProductController::class);
  Route::group(['prefix' => 'products'], function () {

    Route::apiResource('/{product}/reviews', ReviewController::class);
  });
  Route::apiResource('/products/{product}/orders',OrderController::class);
});



Route::group(['middleware' => ['guest:sanctum'], 'as' => 'auth.'], function () {
  Route::post('/login', [UserController::class, "login"]);
  Route::post('/register', [UserController::class, "register"]);
});

Route::group(['middleware' => ['auth:sanctum'],], function () {
Route::post('/logout', [UserController::class, "logout"]);
});
