<?php

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
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


Route::post('login',[LoginController::class,'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    //products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/user-products', [ProductController::class, 'userProducts']);
    Route::get('/user-products-pagination', [ProductController::class, 'userProductsPaginated']);
    Route::post('product/store',[ProductController::class,'store']);
    Route::put('product/update',[ProductController::class,'update']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);

    //notifications
    Route::get('/user-notifications', [NotificationController::class, 'userNotification']);


    Route::post('/logout', [LoginController::class,'logout']);
    Route::post('/change-password', [LoginController::class,'changePassword']);
});
