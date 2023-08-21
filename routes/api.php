<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('login', [App\Http\Controllers\Api\LoginController::class, 'index']);

/** ASE **/
//start-visit
Route::post('visit/start', [App\Http\Controllers\Api\VisitController::class, 'visitStart']);
//check visit started or not
Route::get('check/visit/{id}', [App\Http\Controllers\Api\VisitController::class, 'checkVisit']);
//end-visit
Route::post('visit/end', [App\Http\Controllers\Api\VisitController::class, 'visitEnd']);
//activity store
Route::post('activity/create', [App\Http\Controllers\Api\VisitController::class, 'activityStore']);
//activity list
Route::post('activity', [App\Http\Controllers\Api\VisitController::class, 'activityList']);
//area list
Route::get('area/list/{id}', [App\Http\Controllers\Api\VisitController::class, 'areaList']);