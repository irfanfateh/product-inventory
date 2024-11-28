<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::controller(ProductController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/products', 'store');
    Route::put('/products/{index}', 'update');
});