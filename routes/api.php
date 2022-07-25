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

Route::middleware(['auth:sanctum'])->name('api.')->group(function () {
    Route::get('/product/where-has-stock', [App\Http\Controllers\ProductController::class, 'whereHasStock'])->name('product.where.has.stock');
    Route::get('/product/without-group-and-price', [App\Http\Controllers\ProductController::class, 'withoutGroupAndPrice'])->name('product.without.group.and.price');
    Route::post('/product/paginate', [App\Http\Controllers\ProductController::class, 'paginate'])->name('product.paginate');
    Route::post('/product/{product}/detail', [App\Http\Controllers\DetailController::class, 'paginate'])->name('product.detail.paginate');
    Route::post('/product/{product}/price', [App\Http\Controllers\PriceController::class, 'paginate'])->name('product.price.paginate');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'paginate'])->name('user.paginate');
    Route::post('/burdens', [App\Http\Controllers\BurdenController::class, 'paginate'])->name('burden.paginate');
    Route::post('/transactions', [App\Http\Controllers\TransactionController::class, 'paginate'])->name('transaction.paginate');
    Route::post('/transaction/return', [App\Http\Controllers\TransactionController::class, 'returnPaginate'])->name('transaction.return.paginate');
    Route::post('/in', [App\Http\Controllers\InController::class, 'paginate'])->name('in.paginate');
    Route::post('/transactions/{transaction}/detail', [App\Http\Controllers\TransactionController::class, 'detailPaginate'])->name('transaction.detail.paginate');
    Route::post('/ins/{transaction}/detail', [App\Http\Controllers\InController::class, 'detailPaginate'])->name('in.detail.paginate');
    Route::post('/compare', [App\Http\Controllers\SettingController::class, 'compare'])->name('compare');
    Route::post('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/selling', [App\Http\Controllers\DashboardController::class, 'selling'])->name('selling');
    Route::post('/profit', [App\Http\Controllers\DashboardController::class, 'profit'])->name('profit');
});

Route::middleware(['auth:sanctum'])->get('/user', fn (Request $request) => $request->user())->name('api.user');