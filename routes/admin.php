<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\CatalogueController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StoreController;
// admin guard
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/login', [AuthController::class, 'index'])->name('login');
        Route::post('/login/check',[AuthController::class, 'store'])->name('login.check');
        Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
        Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
        Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
        Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
    });
    // dashboard
        Route::get('/dashboard', [AuthController::class, 'show'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        // products
        Route::resource('products', ProductController::class);
        //categories
        Route::resource('categories', CategoryController::class);
        Route::get('/{id}/status', [CategoryController::class, 'status'])->name('categories.status');
        Route::get('/categories/csv/export', [CategoryController::class, 'csvExport'])->name('categories.csv.export');
        //collections
        Route::resource('collections', CollectionController::class);

        //catalogues
        Route::resource('catalogues', CatalogueController::class);
        //offers
        Route::resource('offers', OfferController::class);
        //users
        Route::resource('users', UserController::class);
        //stores
        Route::resource('stores', StoreController::class);
    
});