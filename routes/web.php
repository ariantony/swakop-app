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

    Route::resource('user', App\Http\Controllers\UserController::class)->middleware('role:admin');
    Route::resource('product', App\Http\Controllers\ProductController::class)->middleware('role:admin');
    Route::resource('burden', App\Http\Controllers\BurdenController::class)->middleware('role:admin');
    Route::resource('price', App\Http\Controllers\PriceController::class)->middleware('role:admin');
    Route::resource('in', App\Http\Controllers\InController::class)->middleware('role:admin');
    Route::post('/in/add', [App\Http\Controllers\InController::class, 'add'])->name('in.add')->middleware('role:admin');
    Route::get('transaction/history', [App\Http\Controllers\TransactionController::class, 'history'])->name('transaction.history');
    Route::resource('transaction', App\Http\Controllers\TransactionController::class);
    Route::patch('burden/{burden}/toggle', [App\Http\Controllers\BurdenController::class, 'toggle'])->name('burden.toggle')->middleware('role:admin');
    Route::resource('presence', App\Http\Controllers\PresenceController::class)->middleware('role:admin');
    Route::post('presence/generate', [App\Http\Controllers\PresenceController::class, 'generate'])->name('presence.generate')->middleware('role:admin');
    Route::prefix('setting/')->middleware('role:admin')->group(function () {
        Route::get('/', [App\Http\Controllers\SettingController::class, 'index'])->name('setting.index');
        Route::patch('master-password/update', [App\Http\Controllers\SettingController::class, 'masterPasswordUpdate'])->name('setting.master-password.update');
    });
});
