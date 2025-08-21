<?php

use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Support\Facades\Route;


    Route::group(['prefix' => 'invoices'], function () {
        Route::get('/', [InvoiceController::class, 'index']);
        Route::post('/', [InvoiceController::class, 'store']);
        Route::get('/{invoice}', [InvoiceController::class, 'show']);
        Route::put('/{invoice}', [InvoiceController::class, 'update']);
    });
