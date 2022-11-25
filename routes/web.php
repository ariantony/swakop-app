<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->to('/login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Print Product 
    Route::get('product/print', [App\Http\Controllers\ProductController::class, 'print'])->name('product.print')->middleware('role:admin');
    Route::get('product/print/group', fn () => redirect()->route('product.print'))->middleware('role:admin');
    Route::get('product/print/price', fn () => redirect()->route('product.print'))->middleware('role:admin');
    Route::post('product/print/group', [App\Http\Controllers\ProductController::class, 'generateGroup'])->name('product.print.group')->middleware('role:admin');
    Route::post('product/print/price', [App\Http\Controllers\ProductController::class, 'generatePrice'])->name('product.print.price')->middleware('role:admin');
    Route::get('product/iframe/print/group', [App\Http\Controllers\ProductController::class, 'generateGroup'])->name('product.iframe.group')->middleware('role:admin');
    Route::get('product/iframe/print/price', [App\Http\Controllers\ProductController::class, 'generatePrice'])->name('product.iframe.price')->middleware('role:admin');

    // Edit Stock (Temp)
    Route::post('product/edit-stock', [App\Http\Controllers\ProductController::class, 'editStock'])->name('product.edit.stock')->middleware('role:admin');
    // Masterdata
    Route::resource('user', App\Http\Controllers\UserController::class)->middleware('role:admin');
    Route::get('product', [App\Http\Controllers\ProductController::class, 'index'])->name('product.index');
    Route::resource('product', App\Http\Controllers\ProductController::class)->except('index')->middleware('role:admin');
    Route::resource('burden', App\Http\Controllers\BurdenController::class)->middleware('role:admin');
    Route::resource('price', App\Http\Controllers\PriceController::class)->middleware('role:admin');
    Route::resource('group', App\Http\Controllers\GroupController::class)->middleware('role:admin');
    Route::resource('pricetag-group', App\Http\Controllers\PricetagGroupController::class)->middleware('role:admin');
    Route::delete('pricetag-group-remove-item/{pricetagGroup}/{product}', [App\Http\Controllers\PricetagGroupController::class, 'remove'])->name('pricetag-group.remove')->middleware('role:admin');

    // Conversion
    Route::resource('conversion', App\Http\Controllers\ConversionController::class)->middleware('role:admin');
    Route::post('/conversion/delete', [App\Http\Controllers\ConversionController::class, 'delete'])->name('conversion.delete')->middleware('role:admin');

    // Stock In
    Route::resource('in', App\Http\Controllers\InController::class)->middleware('role:admin');
    Route::post('/in/add', [App\Http\Controllers\InController::class, 'add'])->name('in.add')->middleware('role:admin');
    Route::post('/in/delete', [App\Http\Controllers\InController::class, 'delete'])->name('in.delete')->middleware('role:admin');

    // Stock Out & Return
    Route::get('/transaction/history', [App\Http\Controllers\TransactionController::class, 'history'])->name('transaction.history');
    Route::get('/transaction/return', [App\Http\Controllers\TransactionController::class, 'retur'])->name('transaction.return.index');
    Route::get('/transaction/history/return', [App\Http\Controllers\TransactionController::class, 'returnHistory'])->name('transaction.return.history');
    Route::patch('/transaction/{transaction}/return', [App\Http\Controllers\TransactionController::class, 'returnCreate'])->name('transaction.return');
    Route::post('/transaction/{transaction}/return/print', [App\Http\Controllers\TransactionController::class, 'returnPrint'])->name('transaction.return.print');
    Route::resource('transaction', App\Http\Controllers\TransactionController::class);

    // Return Stock
    Route::resource('return-stock', App\Http\Controllers\ReturnStockController::class);

    // Presence Report
    Route::get('presence/generate', fn () => redirect()->route('presence.index'))->middleware('role:admin');
    Route::resource('presence', App\Http\Controllers\PresenceController::class)->middleware('role:admin');
    Route::post('presence/generate', [App\Http\Controllers\PresenceController::class, 'generate'])->name('presence.generate')->middleware('role:admin');

    // Income Statement Report
    Route::get('income-statement/generate', fn () => redirect()->route('income.statement.index'))->middleware('role:admin');
    Route::resource('income-statement', App\Http\Controllers\IncomeStatementController::class)->middleware('role:admin')->name('index', 'income.statement.index');
    Route::post('income-statement/generate', [App\Http\Controllers\IncomeStatementController::class, 'generate'])->name('income.statement.generate')->middleware('role:admin');

    // Daily Sale Report
    Route::get('daily-report/generate', fn () => redirect()->route('daily.report.index'));
    Route::resource('daily-report', App\Http\Controllers\DailyController::class)->name('index', 'daily.report.index');
    Route::post('daily-report/generate', [App\Http\Controllers\DailyController::class, 'generate'])->name('daily.report.generate');
    Route::get('daily-report/iframe/print', [App\Http\Controllers\DailyController::class, 'generate'])->name('daily.report.iframe');

    // Goods Return Report
    Route::get('goods-return/generate', fn () => redirect()->route('return.report.index'))->middleware('role:admin');
    Route::resource('goods-return', App\Http\Controllers\ReturnController::class)->name('index', 'return.report.index')->middleware('role:admin');
    Route::post('goods-return/generate', [App\Http\Controllers\ReturnController::class, 'generate'])->name('return.report.generate')->middleware('role:admin');

    // Setting
    Route::prefix('setting/')->middleware('role:admin')->group(function () {
        Route::get('/', [App\Http\Controllers\SettingController::class, 'index'])->name('setting.index');
        Route::patch('master-password/update', [App\Http\Controllers\SettingController::class, 'masterPasswordUpdate'])->name('setting.master-password.update');
    });
});
