<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\{
    AuthController,
    BookInController,
    BookOutController,
    DistributionCenterController,
    FillBillController,
    FillOrderController,
    LocationController,
    ProductCategoryController,
    ProductController,
    ProfileController,
    SellBillController,
    SellOrderController,
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

Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

/**
 *    sharing routes between all users
 */

Route::middleware('auth:sanctum','role:superAdmin|distributionCenter|warehouseManger|Accountant')->group(function () {
    Route::get('user-profile', [ProfileController::class, 'myprofile']);
    Route::apiResource('product-category', ProductCategoryController::class)->only(['index', 'show']);
    Route::apiResource('product', ProductController::class)->only(['index', 'show']);
    Route::apiResource('sell-bill', SellBillController::class)->only(['show']);
});

/**
 * sharing routes between superadmin and Accountant
 */

Route::middleware('auth:sanctum','role:superAdmin|Accountant')->group(function () {
    Route::get('users-info', [ProfileController::class, 'profiles']);
    Route::apiResource('product-category', ProductCategoryController::class)->except(['index','show']);
    Route::apiResource('product', ProductController::class)->except(['index','show']);
    Route::apiResource('fill-order', FillOrderController::class)->only(['index']);
    Route::apiResource('fill-bill', FillBillController::class)->except(['index', 'show']);
    Route::get('fill-order/no-bills', [FillOrderController::class, 'fillOrderWithNoBill']);
    Route::apiResource('sell-order', SellOrderController::class)->only(['index']);
    Route::get('sell-order/order-bills', [SellOrderController::class, 'orderBills']);
    Route::get('sell-order/no-bills', [SellOrderController::class, 'sellOrderWithNoBill']);
    Route::apiResource('sell-bill', SellBillController::class)->except(['index','show']);
});

/**
 *  sharing routes between superadmin and warehouseManger
 */
Route::middleware('auth:sanctum','role:superAdmin|warehouseManger')->group(function () {
    Route::apiResource('book-in', BookInController::class)->except(['index', 'show']);
    Route::apiResource('book-out', BookOutController::class)->except(['index', 'show']);
});



/**
 *       sharing routes between superadmin and Accountant and warehouseManger
 */
Route::middleware('auth:sanctum','role:superAdmin|Accountant|warehouseManger')->group(function () {
    Route::apiResource('distribution-centers', DistributionCenterController::class)->only(['index','show']);
    Route::apiResource('store-category', StoreCategoryController::class)->only(['index', 'show']);
    Route::apiResource('store', StoreController::class)->only(['index', 'show']);
    Route::apiResource('fill-order', FillOrderController::class)->except(['index']);
    Route::apiResource('fill-bill', FillBillController::class)->only(['index', 'show']);
    Route::get('fill-order/order-bills', [FillOrderController::class, 'orderBills']);
    Route::get('fill-order/my-orders', [FillOrderController::class, 'myOrders']);
    Route::get('fill-bill/no-bookIns', [FillBillController::class, 'fillBillWithNoFullBookIn']);
    Route::apiResource('book-in', BookInController::class)->only(['index', 'show']);
    Route::apiResource('sell-bill', SellBillController::class)->only(['index']);
    Route::get('sell-bill/no-bookOuts', [SellBillController::class, 'sellBillWithNoFullBookOut']);
    Route::apiResource('book-out', BookOutController::class)->only(['index', 'show']);

});

/**
 *        sharing routes between superadmin and Accountant and distributionCenter

 */
Route::middleware('auth:sanctum','role:superAdmin|Accountant|distributionCenter')->group(function () {
    Route::apiResource('sell-order', SellOrderController::class)->only(['show']);
});

/**
 *    routes for super admin
 */
Route::middleware('auth:sanctum','role:superAdmin')->group(function () {
    Route::post('/register',[AuthController::class,'register']);
    Route::apiResource('store-category', StoreCategoryController::class)->except(['index','show']);
    Route::apiResource('store', StoreController::class)->except(['index', 'show']);
    Route::apiResource('state', StateController::class);
    Route::apiResource('location', LocationController::class);
    Route::apiResource('distribution-centers', DistributionCenterController::class)->except(['index','show']);
});

/**
 *    routes for distributionCenter
 */
Route::middleware('auth:sanctum','role:distributionCenter')->group(function () {
    Route::apiResource('sell-order', SellOrderController::class)->only(['store', 'update', 'destroy']);
    Route::get('sell-order/my-orders', [SellOrderController::class, 'myOrders']);

});




