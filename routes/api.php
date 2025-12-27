<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{CategoryController,ProductController};

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);