<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('api.')->group(function () {
    Route::post('/products', [App\Http\Controllers\ProductController::class, 'paginate'])->name('product.paginate');
    Route::post('/product/{product}/detail', [App\Http\Controllers\DetailController::class, 'paginate'])->name('product.detail.paginate');
    Route::post('/product/{product}/price', [App\Http\Controllers\PriceController::class, 'paginate'])->name('product.price.paginate');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'paginate'])->name('user.paginate');
    Route::post('/burdens', [App\Http\Controllers\BurdenController::class, 'paginate'])->name('burden.paginate');
});