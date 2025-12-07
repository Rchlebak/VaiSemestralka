<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Definícia všetkých webových routes pre e-shop tenisiek
|
*/

// =============================================
// VEREJNÉ ROUTES (Frontend)
// =============================================

// Hlavná stránka - zoznam produktov
Route::get('/', [ProductController::class, 'index'])->name('home');

// Detail produktu
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

// Pokladňa
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

// Potvrdenie objednávky
Route::get('/order/success/{id}', [OrderController::class, 'success'])->name('order.success');

// Výber prihlásenia (user/admin)
Route::get('/login', [AuthController::class, 'showLoginChoice'])->name('login');

// =============================================
// ADMIN ROUTES
// =============================================

// Prihlásenie admina
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Chránené admin routes
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    // Dashboard (redirect na produkty)
    Route::get('/', function () {
        return redirect()->route('admin.products.index');
    })->name('dashboard');

    // CRUD Produkty
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // Varianty produktu
    Route::post('/products/{id}/variants', [AdminProductController::class, 'addVariant'])->name('products.variants.store');
    Route::put('/variants/{variantId}/stock', [AdminProductController::class, 'updateStock'])->name('variants.updateStock');
    Route::delete('/variants/{variantId}', [AdminProductController::class, 'deleteVariant'])->name('variants.destroy');

    // Obrázky
    Route::delete('/images/{imageId}', [AdminProductController::class, 'deleteImage'])->name('images.destroy');
    Route::post('/images/{imageId}/main', [AdminProductController::class, 'setMainImage'])->name('images.setMain');
});
