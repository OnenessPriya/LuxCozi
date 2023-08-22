<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\VisitController;
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
Route::post('activity', [VisitController::class, 'activityList']);
//area list
Route::get('area/list/{id}', [VisitController::class, 'areaList']);