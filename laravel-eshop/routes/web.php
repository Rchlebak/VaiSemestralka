<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;

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

// =============================================
// AUTENTIFIKÁCIA POUŽÍVATEĽOV
// =============================================

// Pre neprihlásených (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::get('/register', [UserAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [UserAuthController::class, 'register']);
});

// Pre prihlásených (auth)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [UserAuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserAuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserAuthController::class, 'updatePassword'])->name('profile.password');
});

// =============================================
// ADMIN ROUTES
// =============================================

// Prihlásenie admina (verejné)
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Chránené admin routes (vyžaduje admin middleware)
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

    // CRUD Kategórie
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
});
