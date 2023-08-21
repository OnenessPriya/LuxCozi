<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

require 'admin.php';



