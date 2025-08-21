<?php

use App\Http\Controllers\InvoicePageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::group(['prefix' => 'invoices'], function () {
    Route::get('new', [InvoicePageController::class, 'create'])
        ->name('invoice.create');
    Route::get('{invoice}/edit', [InvoicePageController::class, 'edit'])
        ->name('invoice.edit');
    Route::get('dashboard', [InvoicePageController::class, 'index'])
        ->name('invoice.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
