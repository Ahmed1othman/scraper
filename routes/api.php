<?php

use App\Http\Controllers\API\LoginController;
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

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/user-products', [ProductController::class, 'userProducts']);
    Route::post('product/store',[ProductController::class,'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
});
