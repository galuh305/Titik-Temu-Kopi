<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
    });

    Route::get('/pos', [PosController::class, 'create'])->name('pos.create');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/orders/{order}', [PosController::class, 'show'])->name('orders.show');
    Route::get('/reports', [ReportController::class, 'index'])->middleware('role:admin')->name('reports.index');
    Route::get('/reports/csv', [ReportController::class, 'csv'])->middleware('role:admin')->name('reports.csv');
});
