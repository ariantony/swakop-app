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

    // Masterdata
    Route::resource('user', App\Http\Controllers\UserController::class)->middleware('role:admin');
    Route::resource('product', App\Http\Controllers\ProductController::class)->middleware('role:admin');
    Route::resource('burden', App\Http\Controllers\BurdenController::class)->middleware('role:admin');
    Route::resource('price', App\Http\Controllers\PriceController::class)->middleware('role:admin');

    // Stock In
    Route::resource('in', App\Http\Controllers\InController::class)->middleware('role:admin');
    Route::post('/in/add', [App\Http\Controllers\InController::class, 'add'])->name('in.add')->middleware('role:admin');

    // Stock Out & Return
    Route::get('/transaction/history', [App\Http\Controllers\TransactionController::class, 'history'])->name('transaction.history');
    Route::get('/transaction/history/return', [App\Http\Controllers\TransactionController::class, 'returnHistory'])->name('transaction.return.history');
    Route::delete('/transaction/{transaction}/return', [App\Http\Controllers\TransactionController::class, 'retur'])->name('transaction.return');
    Route::resource('transaction', App\Http\Controllers\TransactionController::class);

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

    // Setting
    Route::prefix('setting/')->middleware('role:admin')->group(function () {
        Route::get('/', [App\Http\Controllers\SettingController::class, 'index'])->name('setting.index');
        Route::patch('master-password/update', [App\Http\Controllers\SettingController::class, 'masterPasswordUpdate'])->name('setting.master-password.update');
    });
});
