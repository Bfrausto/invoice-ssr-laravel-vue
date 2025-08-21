<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\InvoicePdfController;
use Illuminate\Support\Facades\Route;


//Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'invoices'], function () {
        Route::get('/', [InvoiceController::class, 'index']);
        Route::post('/', [InvoiceController::class, 'store']);
        Route::get('/{invoice}', [InvoiceController::class, 'show']);
        Route::put('/{invoice}', [InvoiceController::class, 'update']);
        Route::post('/{invoice}/pdf', [InvoicePdfController::class, 'store']);
    });

    Route::post('/clients', [ClientController::class, 'store']);
    Route::post('/companies', [CompanyController::class, 'store']);

//});
