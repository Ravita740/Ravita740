<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return redirect()->route('invoices.index');
});

// Customer Routes
Route::resource('customers', CustomerController::class);

// Invoice Routes
Route::get('invoices/{id}/download-pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.download-pdf');
Route::resource('invoices', InvoiceController::class);
