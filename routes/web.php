<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Jobs\SendEmailJob;

Route::get('/', function () {
    return redirect()->route('invoices.index');
});

// Customer Routes
Route::resource('customers', CustomerController::class);

// Invoice Routes
Route::get('invoices/{id}/download-pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.download-pdf');
Route::resource('invoices', InvoiceController::class);
Route::get('/test-sqs', function () {
    SendEmailJob::dispatch();
    return 'Job pushed to SQS';
});

