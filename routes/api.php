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
Route::get('/download-excel', [\App\Http\Controllers\API\TransaksiController::class, 'downloadExcel'])->name('download-excel');
Route::get('/download-pdf', [\App\Http\Controllers\API\TransaksiController::class, 'downloadPdf'])->name('download-pdf');
Route::get('/download-customer-report/{id}', [\App\Http\Controllers\API\CustomerController::class, 'getCustomerReport'])->name('download-excel-customer');
Route::get('/download-customer-all', [\App\Http\Controllers\API\CustomerController::class, 'getCustomerData'])->name('download-customer-all'); 
Route::get('/download-template', [\App\Http\Controllers\API\CustomerController::class, 'downloadTemplateCustomer'])->name('download-template');
Route::get('/get-cutomer-last-transaksi/{id}', [\App\Http\Controllers\API\TransaksiController::class, 'getLastTransaksiByCustomer']);  


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
    Route::get('/withdraws-by-customer/{id}', [\App\Http\Controllers\API\WithdrawController::class, 'getWithdrawByCustomer']);
    Route::get('/dashboard', [\App\Http\Controllers\API\DashboardController::class, '__invoke']);
    Route::apiResource('/pengumumen', \App\Http\Controllers\API\PengumumanController::class);
    Route::put('/admin/update/{id}', [\App\Http\Controllers\API\AdminController::class, 'updateNew']);
    // newDestroy
    Route::delete('/admin/destroy/{id}', [\App\Http\Controllers\API\AdminController::class, 'newDestroy']);
    // newStore
    Route::post('/admin/new-store', [\App\Http\Controllers\API\AdminController::class, 'newStore']);
    // upload excel upload-customer
    Route::post('/upload-customer', [\App\Http\Controllers\API\CustomerController::class, 'uploadDataCustomer']);
    // check unique phone number
    Route::get('/check-phone-number/{phone}', [\App\Http\Controllers\API\CustomerController::class, 'checkUniquePhone']);
    // checking Max Point
    Route::get('/check-max-point/{id}/{maxPoint}', [\App\Http\Controllers\API\CustomerController::class, 'checkingMaxPoint']);
    // get ids from customer
    Route::get('/get-ids', [\App\Http\Controllers\API\CustomerController::class, 'getIds']);
    // single send whatsapp
    Route::post('/send-whatsapp', [\App\Http\Controllers\API\BroadcastController::class, 'sendSingleMessage']);

   
});

