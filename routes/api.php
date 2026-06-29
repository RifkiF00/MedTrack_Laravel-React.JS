<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAsetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| API ini dikonsumsi oleh aplikasi Android Kotlin (Staf Scanner)
|
*/

// Rute Publik (Tanpa Token)
Route::post('/login', [ApiAuthController::class, 'login']);

// Rute Terproteksi (Harus menggunakan Header: Authorization Bearer <token>)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/aset/{qr_code}', [ApiAsetController::class, 'getDetailByQr']);
    Route::post('/tracking', [ApiAsetController::class, 'updateLocation']);
});
