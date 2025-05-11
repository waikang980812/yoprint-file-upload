<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [UploadController::class, 'index'])->name('upload');


route::prefix('uploads')->group(function() {
    Route::get('/list', [UploadController::class, 'list'])->name('upload.list');
    Route::post('/', [UploadController::class, 'store'])->name('upload.store');
});

route::prefix('products')->group(function(){
    Route::get('/', [ProductController::class, 'index'])->name('product');
    Route::get('/list', [ProductController::class, 'list'])->name('product.list');
});



