<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes SanctumOnly
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Public Routes
Route::get('Products/list', [ProductController::class, 'list']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

//Protected Routes

Route::group(['middleware'=>['auth:sanctum']], function () {
    Route::post('Products/addProduct', [ProductController::class, 'addProduct']);
    Route::get('Products/getProduct/{id}', [ProductController::class, 'getProduct']);
    Route::delete('Products/delete/{id}', [ProductController::class, 'delete']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
