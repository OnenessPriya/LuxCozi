<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// admin guard
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'index'])->name('login');
        Route::post('/login/check',[App\Http\Controllers\Admin\AuthController::class, 'store'])->name('login.check');
        Route::get('forget-password', [App\Http\Controllers\Admin\ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
        Route::post('forget-password', [App\Http\Controllers\Admin\ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
        Route::get('reset-password/{token}', [App\Http\Controllers\Admin\ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
        Route::post('reset-password', [App\Http\Controllers\Admin\ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
    });
    // dashboard
        Route::get('/dashboard', [App\Http\Controllers\Admin\AuthController::class, 'show'])->name('dashboard');
        Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'destroy'])->name('logout');
        // products
        Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
        //categories
        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
        //collections
        Route::resource('collections', App\Http\Controllers\Admin\CollectionController::class);
        //users
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        //stores
        Route::resource('stores', App\Http\Controllers\Admin\StoreController::class);
    
});