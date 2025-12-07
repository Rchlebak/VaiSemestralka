<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API endpoints pre JavaScript frontend
|
*/

// =============================================
// VEREJNÉ API
// =============================================

// Produkty
Route::get('/products', [ProductController::class, 'apiIndex']);
Route::get('/product/{id}', [ProductController::class, 'apiShow']);

// Objednávky
Route::post('/orders', [OrderController::class, 'apiStore']);

// Autentifikácia
Route::post('/auth/login', [AuthController::class, 'apiLogin']);
Route::post('/auth/logout', [AuthController::class, 'apiLogout']);

// =============================================
// ADMIN API (vyžaduje autentifikáciu)
// =============================================

Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/products', [AdminProductController::class, 'apiIndex']);
    Route::post('/products', [AdminProductController::class, 'apiStore']);
    Route::put('/products/{id}', [AdminProductController::class, 'apiUpdate']);
    Route::delete('/products/{id}', [AdminProductController::class, 'apiDestroy']);
});

