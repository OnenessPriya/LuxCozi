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
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ASMController;
use App\Http\Controllers\Api\SMController;
use App\Http\Controllers\Api\RSMController;
use App\Http\Controllers\Api\ZSMController;
use App\Http\Controllers\Api\NSMController;
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
//check login flag
Route::get('check/login/flag/{id}', [LoginController::class, 'checkLogin']);
//login flag update
Route::post('login/flag/update', [LoginController::class, 'loginflagStore']);
//catalogue
Route::get('catalogue', [CatalogueController::class, 'index']);
//scheme list
Route::get('scheme', [SchemeController::class, 'index']);
//area list for RSM & SM & ZSM & NSM
Route::get('area/list/{id}', [RSMController::class, 'areaList']);
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
//other activity
Route::post('other-activity/create', [VisitController::class, 'otheractivityStore']);
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
Route::get('no-order-history/{id}/{user_id}', [NoOrderReasonController::class, 'show']);
Route::post('no-order-reason/update', [NoOrderReasonController::class, 'update']);

// PLACE ORDER 
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

//cart
//cart list user wise
Route::get('cart/list/{id}/{user_id}', [CartController::class, 'show']);
//add to cart
Route::post('addTocart', [CartController::class, 'store']);
//cart remove
Route::get('cart/clear/{id}', [CartController::class, 'destroy']);
//cart update
Route::get('cart/qty/{cartId}/{q}',[CartController::class, 'update']);
// cart preview url
Route::get('cart/pdf/url/{id}/{user_id}', [CartController::class, 'PDF_URL']);
//cart preview pdf
Route::get('cart/pdf/view/{id}/{user_id}', [CartController::class, 'PDF_view']);
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
/** ASM **/
//inactive ASE report for ASM in dashboard
Route::get('inactive/ase/report/asm', [ASMController::class, 'inactiveAseListASM']);
//area list
Route::get('asm/area/list/{id}', [ASMController::class, 'areaList']);
//distributor list
Route::get('asm/distributor/list', [ASMController::class, 'distributorList']);
//store list
Route::get('asm/store', [ASMController::class, 'storeList']);
//store search for individual ASE's store
Route::get('asm/store/search', [ASMController::class, 'searchStore']);
//store create
Route::post('asm/store/create', [ASMController::class, 'storeCreate']);
//store details
Route::get('asm/store/details', [ASMController::class, 'storesShow']);
//inactive store list
Route::get('asm/inactive/store', [ASMController::class, 'inactiveStorelist']);
//store image create
Route::post('asm/store/image/create', [ASMController::class, 'imageCreate']);

//no order reason
Route::get('asm/no-order-history/{id}/{user_id}', [ASMController::class, 'noOrderReasonDetail']);
Route::post('asm/no-order-reason/update', [ASMController::class, 'noOrderReasonUpdate']);

// PLACE ORDER 
//cart
//cart list user wise
Route::get('asm/cart/list/{id}/{user_id}', [ASMController::class, 'cartList']);
//add to cart
Route::post('asm/addTocart', [ASMController::class, 'addToCart']);
//cart remove
Route::get('asm/cart/clear/{id}', [ASMController::class, 'cartDestroy']);
//cart update
Route::get('asm/cart/qty/{cartId}/{q}',[ASMController::class, 'cartUpdate']);
// cart preview url
Route::get('asm/cart/pdf/url/{id}/{user_id}', [ASMController::class, 'CartPDF_URL']);
//cart preview pdf
Route::get('asm/cart/pdf/view/{id}/{user_id}', [ASMController::class, 'CartPDF_view']);
//order 
//order list user wise
Route::get('asm/order/list/{id}', [ASMController::class, 'orderList']);
//place order
Route::post('asm/place-order', [ASMController::class, 'placeOrder']);
//order details
Route::get('asm/order/details/{id}', [ASMController::class, 'orderDetails']);
// order preview url
Route::get('asm/order/pdf/url/{id}', [ASMController::class, 'orderPDF_URL']);
//order preview pdf
Route::get('asm/order/pdf/view/{id}', [ASMController::class, 'orderPDF_view']);

//my order list
Route::post('asm/my-orders', [ASMController::class, 'myOrders']);
//store wise team report
Route::post('asm/store-wise-report', [ASMController::class, 'storeReportASM']);

//ASM wise ASE list
Route::get('asm/ase/list/{id}', [ASMController::class, 'aseList']);
//activity log ase wise
Route::get('asm/activity', [ASMController::class, 'activityList']);
//notification list
Route::post('asm/notification-list', [ASMController::class, 'notificationList']);
//notification update
Route::post('asm/read-notification', [ASMController::class, 'readNotification']);

//product wise team report for ASM
Route::post('asm/product-report-detail', [ASMController::class, 'productReportASM']);

//SM//
//inactive ASE report for ASM in dashboard
Route::get('inactive/ase/report/sm', [SMController::class, 'inactiveAseListSM']);
//store wise team report
Route::post('sm/store-wise-report', [SMController::class, 'storeReportSM']);
//product wise team report for ASM
Route::post('sm/product-wise-report', [SMController::class, 'productReportSM']);
//notification list
Route::post('sm/notification-list', [SMController::class, 'notificationList']);
//notification update
Route::post('sm/read-notification', [SMController::class, 'readNotification']);

//RSM//
//inactive ASE report for RSM in dashboard
Route::get('inactive/ase/report/rsm', [RSMController::class, 'inactiveAseListRSM']);
//store wise team report for RSM
Route::post('rsm/store-wise-report', [RSMController::class, 'storeReportRSM']);
//product wise team report for RSM
Route::post('rsm/product-wise-report', [RSMController::class, 'productReportRSM']);
//notification list
Route::post('rsm/notification-list', [RSMController::class, 'notificationList']);
//notification update
Route::post('rsm/read-notification', [RSMController::class, 'readNotification']);

//ZSM//
//inactive ASE report for ZSM in dashboard
Route::get('inactive/ase/report/zsm', [ZSMController::class, 'inactiveAseListZSM']);
//store wise team report for RSM
Route::post('zsm/store-wise-report', [ZSMController::class, 'storeReportZSM']);
//product wise team report for RSM
Route::post('zsm/product-wise-report', [ZSMController::class, 'productReportZSM']);
//notification list
Route::post('zsm/notification-list', [ZSMController::class, 'notificationList']);
//notification update
Route::post('zsm/read-notification', [ZSMController::class, 'readNotification']);

//NSM//
//inactive ASE report for NSM in dashboard
Route::get('inactive/ase/report/nsm', [NSMController::class, 'inactiveAseListNSM']);
//store wise team report for NSM
Route::post('nsm/store-wise-report', [NSMController::class, 'storeReportNSM']);
//product wise team report for NSM
Route::post('nsm/product-wise-report', [NSMController::class, 'productReportNSM']);
//notification list
Route::post('nsm/notification-list', [NSMController::class, 'notificationList']);
//notification update
Route::post('nsm/read-notification', [NSMController::class, 'readNotification']);
