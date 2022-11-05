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
    // Masterdata
    Route::get('/product/only-name', [App\Http\Controllers\ProductController::class, 'onlyName'])->name('product.only.name');
    Route::get('/product/where-has-stock', [App\Http\Controllers\ProductController::class, 'whereHasStock'])->name('product.where.has.stock');
    Route::get('/product/where-has-price', [App\Http\Controllers\ProductController::class, 'whereHasPrice'])->name('product.where.has.price');
    Route::get('/product/without-group-and-price', [App\Http\Controllers\ProductController::class, 'withoutGroupAndPrice'])->name('product.without.group.and.price');
    Route::post('/product/paginate', [App\Http\Controllers\ProductController::class, 'paginate'])->name('product.paginate');
    Route::post('/product/{product}/detail', [App\Http\Controllers\DetailController::class, 'paginate'])->name('product.detail.paginate');
    Route::post('/product/{product}/price', [App\Http\Controllers\PriceController::class, 'paginate'])->name('product.price.paginate');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'paginate'])->name('user.paginate');
    Route::post('/burdens', [App\Http\Controllers\BurdenController::class, 'paginate'])->name('burden.paginate');
    Route::post('/conversions', [App\Http\Controllers\ConversionController::class, 'paginate'])->name('conversion.paginate');
    Route::post('/groups', [App\Http\Controllers\GroupController::class, 'paginate'])->name('group.paginate');
    Route::post('/pricetag-groups', [App\Http\Controllers\PricetagGroupController::class, 'paginate'])->name('pricetag-group.paginate');

    // Stock In
    Route::post('/in', [App\Http\Controllers\InController::class, 'paginate'])->name('in.paginate');
    Route::post('/ins/{transaction}/detail', [App\Http\Controllers\InController::class, 'detailPaginate'])->name('in.detail.paginate');
    Route::post('/in/{detail}', [App\Http\Controllers\InController::class, 'show'])->name('in.show');

    // Stock Out & Return Sell
    Route::post('/transactions', [App\Http\Controllers\TransactionController::class, 'paginate'])->name('transaction.paginate')->middleware(['auth:sanctum']);
    Route::post('/transaction/return', [App\Http\Controllers\TransactionController::class, 'returnPaginate'])->name('transaction.return.paginate');
    Route::get('/transaction/{transaction}/find', [App\Http\Controllers\TransactionController::class, 'find'])->name('transaction.find');
    Route::post('/transactions/{transaction}/detail', [App\Http\Controllers\TransactionController::class, 'detailPaginate'])->name('transaction.detail.paginate');
    Route::get('/transactions/{transaction}/print', [App\Http\Controllers\TransactionController::class, 'invoicePrint'])->name('transaction.print');
    Route::get('/transactions/print/latest', [App\Http\Controllers\TransactionController::class, 'latestInvoicePrint'])->name('transaction.latest.print');

    // Setting
    Route::post('/compare', [App\Http\Controllers\SettingController::class, 'compare'])->name('compare');

    // Dashboard
    Route::post('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/selling', [App\Http\Controllers\DashboardController::class, 'selling'])->name('selling');
    Route::post('/profit', [App\Http\Controllers\DashboardController::class, 'profit'])->name('profit');

    // Return Buy
    Route::get('/return-stock/products', [App\Http\Controllers\ReturnStockController::class, 'products'])->name('return-stock.products');
    Route::post('/return-stock/history', [App\Http\Controllers\ReturnStockController::class, 'history'])->name('return-stock.history');

    Route::get('/product/{product}/price', [App\Http\Controllers\ProductController::class, 'price'])->name('product.price');
});

Route::middleware(['auth:sanctum'])->get('/user', fn (Request $request) => $request->user())->name('api.user');