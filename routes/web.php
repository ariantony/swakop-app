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

    Route::resource('user', App\Http\Controllers\UserController::class);
    Route::resource('product', App\Http\Controllers\ProductController::class);
    Route::resource('burden', App\Http\Controllers\BurdenController::class);
    Route::resource('price', App\Http\Controllers\PriceController::class);
    Route::get('transaction/history', [App\Http\Controllers\TransactionController::class, 'history'])->name('transaction.history');
    Route::resource('transaction', App\Http\Controllers\TransactionController::class);
    Route::patch('burden/{burden}/toggle', [App\Http\Controllers\BurdenController::class, 'toggle'])->name('burden.toggle');
    Route::prefix('setting/')->group(function () {
        Route::get('/', [App\Http\Controllers\SettingController::class, 'index'])->name('setting.index');
        Route::patch('master-password/update', [App\Http\Controllers\SettingController::class, 'masterPasswordUpdate'])->name('setting.master-password.update');
    });
});
