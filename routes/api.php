<?php

use Illuminate\Http\Request;
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
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [\App\Http\Controllers\API\AuthController::class, 'login'])->name('login');
Route::get('/export-excel', [\App\Http\Controllers\API\TransaksiController::class, 'ExportExcel']);
Route::get('/export-pdf', [\App\Http\Controllers\API\TransaksiController::class, 'ExportPdf']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\API\AuthController::class, 'logout']);
    Route::apiResource('/cabang', \App\Http\Controllers\API\CabangController::class);
    Route::apiResource('/kategori', \App\Http\Controllers\API\KategoriController::class);
    Route::apiResource('/customer', \App\Http\Controllers\API\CustomerController::class);
    Route::apiResource('/transaksi', \App\Http\Controllers\API\TransaksiController::class);
    Route::get('/transaksi-by-customer/{id}', [\App\Http\Controllers\API\TransaksiController::class, 'getTransaksiByCustomer']);
    Route::apiResource('/admin', \App\Http\Controllers\API\AdminController::class);
    Route::apiResource('/templates', \App\Http\Controllers\API\TemplateController::class);
    Route::apiResource('/broadcasts', \App\Http\Controllers\API\BroadcastController::class);
    Route::post('/broadcast-action', [\App\Http\Controllers\API\BroadcastController::class, 'broadcastAction']);
    Route::apiResource('/withdraws', \App\Http\Controllers\API\WithdrawController::class);
    Route::get('/dashboard', [\App\Http\Controllers\API\DashboardController::class, '__invoke']);
});

