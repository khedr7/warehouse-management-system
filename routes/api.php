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

Route::middleware('auth:sanctum','role:superAdmin')->group(function () {

    Route::post('/register',[AuthController::class,'register']);
    Route::get('users-info', [ProfileController::class, 'profiles']);
    Route::get('user-profile', [ProfileController::class, 'myprofile']);



    Route::apiResource('store-category', StoreCategoryController::class);
    Route::apiResource('product-category', ProductCategoryController::class);
    Route::apiResource('state', StateController::class);
    Route::apiResource('location', LocationController::class);
    Route::apiResource('product', ProductController::class);
    Route::apiResource('store', StoreController::class);
    Route::apiResource('distribution-centers', DistributionCenterController::class);
    Route::get('fill-order/no-bills', [FillOrderController::class, 'fillOrderWithNoBill']);
    Route::apiResource('fill-order', FillOrderController::class);
    Route::get('fill-bill/no-bookIns', [FillBillController::class, 'fillBillWithNoFullBookIn']);
    Route::apiResource('fill-bill', FillBillController::class);
    Route::apiResource('book-in', BookInController::class);
    Route::get('sell-order/no-bills', [SellOrderController::class, 'sellOrderWithNoBill']);
    Route::apiResource('sell-order', SellOrderController::class);
    Route::get('sell-bill/no-bookOuts', [SellBillController::class, 'sellBillWithNoFullBookOut']);
    Route::apiResource('sell-bill', SellBillController::class);
    Route::apiResource('book-out', BookOutController::class);

});







Route::middleware('auth:sanctum','role:warehouseManger')->group(function () {

    Route::get('user-profile', [ProfileController::class, 'myprofile']);


    Route::apiResource('store-category', StoreCategoryController::class)->only('index', 'show');
    Route::apiResource('product-category', ProductCategoryController::class)->only('index', 'show');
    Route::apiResource('state', StateController::class)->only('index', 'show');
    Route::apiResource('location', LocationController::class)->only('index', 'show');
    Route::apiResource('product', ProductController::class)->only('index', 'show');
    Route::apiResource('store', StoreController::class)->only('index', 'show');
    Route::apiResource('distribution-centers', DistributionCenterController::class)->only('index', 'show');

    Route::get('fill-order/no-bills', [FillOrderController::class, 'fillOrderWithNoBill']);
    Route::apiResource('fill-order', FillOrderController::class);
    Route::get('fill-bill/no-bookIns', [FillBillController::class, 'fillBillWithNoFullBookIn']);
    Route::apiResource('fill-bill', FillBillController::class)->only('index', 'show');
    Route::apiResource('book-in', BookInController::class);
    Route::get('sell-order/no-bills', [SellOrderController::class, 'sellOrderWithNoBill']);
    Route::apiResource('sell-order', SellOrderController::class)->only('index', 'show');
    Route::get('sell-bill/no-bookOuts', [SellBillController::class, 'sellBillWithNoFullBookOut']);
    Route::apiResource('sell-bill', SellBillController::class)->only('index', 'show');
    Route::apiResource('book-out', BookOutController::class);




});





Route::middleware('auth:sanctum','role:distributionCenter')->group(function () {

    Route::get('user-profile', [ProfileController::class, 'myprofile']);


    Route::apiResource('product-category', ProductCategoryController::class)->only('index', 'show');
    Route::apiResource('product', ProductController::class)->only('index', 'show');
    Route::apiResource('distribution-centers', DistributionCenterController::class)->only('show');

    Route::apiResource('sell-order', SellOrderController::class)->only('show', 'store', 'update', 'destroy');

    Route::apiResource('sell-bill', SellBillController::class)->only('show');

});




Route::middleware('auth:sanctum','role:Accountant')->group(function () {

    Route::get('user-profile', [ProfileController::class, 'myprofile']);


    Route::apiResource('store-category', StoreCategoryController::class)->only('index', 'show');
    Route::apiResource('product-category', ProductCategoryController::class);
    Route::apiResource('state', StateController::class)->only('index', 'show');
    Route::apiResource('location', LocationController::class)->only('index', 'show');
    Route::apiResource('product', ProductController::class);
    Route::apiResource('store', StoreController::class)->only('index', 'show');
    Route::apiResource('distribution-centers', DistributionCenterController::class)->only('index', 'show');

    Route::get('fill-order/no-bills', [FillOrderController::class, 'fillOrderWithNoBill']);
    Route::apiResource('fill-order', FillOrderController::class);
    Route::get('fill-bill/no-bookIns', [FillBillController::class, 'fillBillWithNoFullBookIn']);
    Route::apiResource('fill-bill', FillBillController::class);
    Route::apiResource('book-in', BookInController::class)->only('index', 'show');
    Route::get('sell-order/no-bills', [SellOrderController::class, 'sellOrderWithNoBill']);
    Route::apiResource('sell-order', SellOrderController::class)->only('index', 'show');
    Route::get('sell-bill/no-bookOuts', [SellBillController::class, 'sellBillWithNoFullBookOut']);
    Route::apiResource('sell-bill', SellBillController::class);
    Route::apiResource('book-out', BookOutController::class)->only('index', 'show');;


});
