<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
 API Routes//Sanc & Socialite 
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


// Route::post('Products/addProduct', [ProductController::class, 'addProduct']);
// Route::get('Products/getProduct/{id}', [ProductController::class, 'getProduct']);
// Route::delete('Products/delete/{id}', [ProductController::class, 'delete']);
// Route::post('logout', [AuthController::class, 'logout']);
// Route::get('gettoken', [AuthController::class, 'gettoken']);

//Protected Routes(session based by sanctum)
Route::group(['middleware'=>['auth:sanctum']], function () {
   Route::post('Products/addProduct', [ProductController::class, 'addProduct']);
   Route::get('Products/getProduct/{id}', [ProductController::class, 'getProduct']);
   Route::delete('Products/delete/{id}', [ProductController::class, 'delete']);
   Route::post('logout', [AuthController::class, 'logout']);
   Route::get('gettoken', [AuthController::class, 'gettoken']);
   
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {//<--- must call before any apirequest to get the bearertoken 
    return $request->user();
});


//Oauth by socialite
Route::get('login/{provider}', [OAuthController::class, 'redirectToProvider']);
Route::get('login/{provider}/callback', [OAuthController::class, 'handleProviderCallback']);
