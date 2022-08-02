<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\{
    AuthController,
    DistributionCenterController,
    LocationController,
    ProductCategoryController,
    ProductController,
    StateController,
    StoreCategoryController,
    StoreController
};

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

Route::apiResource('store-category', StoreCategoryController::class);
Route::apiResource('product-category', ProductCategoryController::class);
Route::apiResource('state', StateController::class);
Route::apiResource('location', LocationController::class);
Route::apiResource('product', ProductController::class);
Route::apiResource('store', StoreController::class);
Route::apiResource('distribution-centers', DistributionCenterController::class);

});


