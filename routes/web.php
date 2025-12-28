<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Storage;

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
Route::get('/upload-test', function () {
    Storage::disk('s3')->put(
        'test/hello.txt',
        'Hello from Laravel S3'
    );

    return 'File uploaded to S3';
});

