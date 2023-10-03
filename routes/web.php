<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Front\UserController;
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
	Route::get('store', [UserController::class, 'storeList'])->name('store.list');
	Route::get('order-csv-download', [UserController::class, 'orderCsv'])->name('order.csv.download');
	Route::get('product-wise-sales', [UserController::class, 'productorder'])->name('product.order');
	Route::get('product-order-csv-download', [UserController::class, 'productorderCsv'])->name('product.order.csv.download');
	Route::get('zone-wise-sales', [UserController::class, 'areaorder'])->name('zone.order');
	Route::get('zone-order-csv-download', [UserController::class, 'areaorderCsv'])->name('zone.order.csv.download');
	Route::get('activity', [UserController::class, 'activityList'])->name('activity.index');
	});
require 'admin.php';



