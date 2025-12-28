<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Storage;
use Aws\Sns\SnsClient;

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
Route::get('/view-s3-text', function () {
    return Storage::disk('s3')->get('test/hello.txt');
});
Route::get('/test-sns', function () {

    $sns = new SnsClient([
        'region' => env('AWS_DEFAULT_REGION'),
        'version' => 'latest',
        'credentials' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],
    ]);

    $sns->publish([
        'TopicArn' => 'arn:aws:sns:us-east-1:073158195064:learnig-sns',
        'Message' => 'Hello from Laravel SNS 🎉',
        'Subject' => 'SNS Test',
    ]);

    return 'SNS message sent';
});
