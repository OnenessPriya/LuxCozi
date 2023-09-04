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
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
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
        Route::get('/products/{id}/status', [ProductController::class, 'status'])->name('products.status');
        Route::get('/products/csv/export', [ProductController::class, 'csvExport'])->name('products.csv.export');
        Route::post('/products/size', [ProductController::class, 'size'])->name('products.size');
        Route::post('/csv/upload', [ProductController::class, 'variationCSVUpload'])->name('products.variation.csv.upload');
        Route::post('/bulk/edit', [ProductController::class, 'variationBulkEdit'])->name('products.variation.bulk.edit');
        Route::post('/bulk/update', [ProductController::class, 'variationBulkUpdate'])->name('products.variation.bulk.update');
        // variation
        Route::post('/variation/color/add', [ProductController::class, 'variationColorAdd'])->name('products.variation.color.add');
        Route::post('/variation/color/position', [ProductController::class, 'variationColorPosition'])->name('products.variation.color.position');
        Route::post('/variation/color/status/toggle', [ProductController::class, 'variationStatusToggle'])->name('products.variation.color.status.toggle');
        Route::post('/variation/color/edit', [ProductController::class, 'variationColorEdit'])->name('products.variation.color.edit');
        Route::post('/variation/color/rename', [ProductController::class, 'variationColorRename'])->name('products.variation.color.rename');
        Route::post('/variation/color/fabric/upload', [ProductController::class, 'variationFabricUpload'])->name('products.variation.color.fabric.upload');
        Route::get('/variation/{productId}/color/{colorId}/delete', [ProductController::class, 'variationColorDestroy'])->name('products.variation.color.delete');
        Route::post('/variation/size/add', [ProductController::class, 'variationSizeUpload'])->name('products.variation.size.add');   
        Route::post('/variation/size/edit', [ProductController::class, 'variationSizeEdit'])->name('products.variation.size.edit');
        Route::get('/variation/{id}/size/remove', [ProductController::class, 'variationSizeDestroy'])->name('products.variation.size.delete');
        Route::post('/variation/image/add', [ProductController::class, 'variationImageUpload'])->name('products.variation.image.add');
        Route::post('/variation/image/remove', [ProductController::class, 'variationImageDestroy'])->name('products.variation.image.delete');
        //categories
        Route::resource('categories', CategoryController::class);
        Route::get('/categories/{id}/status', [CategoryController::class, 'status'])->name('categories.status');
        Route::get('/categories/csv/export', [CategoryController::class, 'csvExport'])->name('categories.csv.export');
        //collections
        Route::resource('collections', CollectionController::class);
        Route::get('/collections/{id}/status', [CollectionController::class, 'status'])->name('collections.status');
        Route::get('/collections/csv/export', [CollectionController::class, 'csvExport'])->name('collections.csv.export');
        //catalogues
        Route::resource('catalogues', CatalogueController::class);
        Route::get('/catalogues/{id}/status', [CatalogueController::class, 'status'])->name('catalogues.status');
        //offers
        Route::resource('schemes', OfferController::class);
        Route::get('/schemes/{id}/status', [OfferController::class, 'status'])->name('schemes.status');
        //users
        Route::resource('users', UserController::class);
        //stores
        Route::resource('stores', StoreController::class);
        //states
        Route::resource('states', StateController::class);
        Route::get('/states/{id}/status', [StateController::class, 'status'])->name('states.status');
        //areas
        Route::resource('areas', AreaController::class);
        Route::get('/areas/{id}/status', [AreaController::class, 'status'])->name('areas.status');
        //colors
        Route::resource('colors', ColorController::class);
        Route::get('/colors/{id}/status', [ColorController::class, 'status'])->name('colors.status');
        //sizes
        Route::resource('sizes', SizeController::class);
        Route::get('/sizes/{id}/status', [SizeController::class, 'status'])->name('sizes.status');
    
});