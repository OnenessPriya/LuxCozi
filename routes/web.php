<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Front\UserController;
use App\Http\Controllers\Front\ReportController;
Route::get('/cache-clear', function() {
	// \Artisan::call('route:cache');
	\Artisan::call('config:cache');
	\Artisan::call('cache:clear');
	\Artisan::call('view:clear');
	\Artisan::call('config:clear');
	\Artisan::call('view:cache');
	\Artisan::call('route:clear');
	dd('Cache cleared');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::middleware(['auth:web'])->name('front.')->group(function () {
	// notification
	Route::post('/read', [UserController::class, 'notificationRead'])->name('notification.read');
	 Route::prefix('user/')->name('user.')->group(function () {
		  Route::view('profile', 'front.profile.index')->name('profile');
		  Route::get('order', [UserController::class, 'order'])->name('order');
	});
	//sales person list
	Route::get('sales-person', [UserController::class, 'list'])->name('salesperson.list');
	//store wise sales
	Route::get('store/order', [ReportController::class, 'index'])->name('store.order.report');
	//store wise sales csv download 
	Route::get('order-csv-download', [ReportController::class, 'csvExport'])->name('order.csv.download');
	Route::get('/orders/{id}/pdf/export', [ReportController::class, 'pdfExport'])->name('orders.pdf');
	Route::get('/orders/{id}/csv/download', [ReportController::class, 'individualcsvExport'])->name('orders.report.csv');
	//product wise sales
	Route::get('product-wise-sales', [UserController::class, 'productorder'])->name('product.order');
	//product wise sales csv download 
	Route::get('product-order-csv-download', [UserController::class, 'productorderCsv'])->name('product.order.csv.download');
    //zone wise sales
	Route::get('zone-wise-sales', [UserController::class, 'areaorder'])->name('zone.order');
	//zone wise sales csv download
	Route::get('zone-order-csv-download', [UserController::class, 'areaorderCsv'])->name('zone.order.csv.download');
	//activity list
	Route::get('activity', [UserController::class, 'activityList'])->name('activity.index');
	//store list
	Route::get('store/list', [UserController::class, 'storeList'])->name('store.list');
	//store order details
	Route::get('store/order/details', [UserController::class, 'storeDetail'])->name('store.details');
	//team report
	Route::get('/ZSM/team/report', [ReportController::class, 'zsmreportIndex'])->name('team.order.report');
    Route::get('/ZSM/team/report/detail', [ReportController::class, 'zsmreportDetail'])->name('team.order.report.detail');
	});
require 'admin.php';



