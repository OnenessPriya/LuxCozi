<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\VisitController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\NoOrderReasonController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CatalogueController;
use App\Http\Controllers\Api\SchemeController;
use App\Http\Controllers\Api\ReportController;
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

// Login
Route::post('login', [LoginController::class, 'index']);
//catalogue
Route::get('catalogue', [CatalogueController::class, 'index']);
//scheme list
Route::get('scheme', [SchemeController::class, 'index']);
/** ASE **/
//start-visit
Route::post('visit/start', [VisitController::class, 'visitStart']);
//check visit started or not
Route::get('check/visit/{id}', [VisitController::class, 'checkVisit']);
//end-visit
Route::post('visit/end', [VisitController::class, 'visitEnd']);
//activity store
Route::post('activity/create', [VisitController::class, 'activityStore']);
//activity list
Route::get('activity', [VisitController::class, 'activityList']);
//area list
Route::get('area/list/{id}', [VisitController::class, 'areaList']);

//store list
Route::get('store', [StoreController::class, 'index']);
//store search
Route::get('store/search', [StoreController::class, 'search']);
//store search for individual ASE's store
Route::get('user/store/search', [StoreController::class, 'searchuserStore']);
//store create
Route::post('store/create', [StoreController::class, 'store']);
//store details
Route::get('store/details', [StoreController::class, 'show']);
//inactive store list
Route::get('inactive/store', [StoreController::class, 'inactiveStorelist']);
//store image create
Route::post('store/image/create', [StoreController::class, 'imageCreate']);
//distributor list
Route::get('distributor/list', [StoreController::class, 'distributorList']);

//area wise state list
Route::get('state/list', [StoreController::class, 'stateList']);

//no order reason
Route::get('no-order-reason', [NoOrderReasonController::class, 'index']);
Route::get('no-order-history/{id}', [NoOrderReasonController::class, 'show']);
Route::post('no-order-reason/update', [NoOrderReasonController::class, 'update']);

/* PLACE ORDER */
//category list
Route::get('category', [CategoryController::class, 'index']);
//collection list category wise
Route::get('collection/{id}', [CollectionController::class, 'show']);
//product list collection wise
Route::get('product/{id}', [ProductController::class, 'show']);
//color list
Route::get('color/list/{id}', [ProductController::class, 'colors']);
//size list
Route::get('size/list', [ProductController::class, 'sizes']);
/*cart*/
//cart list user wise
Route::get('cart/list/{id}/{user_id}', [CartController::class, 'show']);
//add to cart
Route::post('addTocart', [CartController::class, 'store']);
//cart remove
Route::get('cart/clear/{id}', [CartController::class, 'destroy']);
//cart update
Route::get('cart/qty/{cartId}/{q}',[CartController::class, 'update']);
// cart preview url
Route::get('cart/pdf/url/{id}', [CartController::class, 'PDF_URL']);
//cart preview pdf
Route::get('cart/pdf/view/{id}', [CartController::class, 'PDF_view']);
/* order */
//order list user wise
Route::get('order/list/{id}/{user_id}', [OrderController::class, 'index']);
//place order
Route::post('place-order', [OrderController::class, 'store']);
//order details
Route::get('order/details/{id}', [OrderController::class, 'show']);
// order preview url
Route::get('order/pdf/url/{id}', [OrderController::class, 'PDF_URL']);
//order preview pdf
Route::get('order/pdf/view/{id}', [OrderController::class, 'PDF_view']);

//my order list
Route::post('my-orders', [OrderController::class, 'myOrdersFilter']);
//dashboard order count
Route::post('store/order/count', [OrderController::class, 'dashboardCount']);
//report
//store wise report for ASE
Route::post('store-wise-report-ase', [ReportController::class, 'storeReportASE']);
//product wise report for ASE
Route::post('product-wise-report-ase', [ReportController::class, 'productReportASE']);