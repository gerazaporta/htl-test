<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\KeyController;
use App\Http\Controllers\Auth\LoginController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [LoginController::class, 'login']);


Route::resource('orders', OrderController::class);
Route::resource('vehicles', VehicleController::class)->only(['index']);
Route::resource('keys', KeyController::class)->only(['index']);
Route::resource('technicians', TechnicianController::class)->only(['index']);
