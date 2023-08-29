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
Route::get('size/list/{id}', [ProductController::class, 'sizes']);