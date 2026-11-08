<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Protected Application Routes
Route::middleware('auth')->group(function () {
    // Logout & Profile
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products (Barang / Inventaris)
    Route::post('/products/{product}/quick-restock', [ProductController::class, 'quickRestock'])->name('products.quick-restock');
    Route::resource('products', ProductController::class);

    // Categories (Kategori Barang)
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // Suppliers (Pemasok)
    Route::resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);

    // Transactions (Barang Masuk & Keluar)
    Route::get('/transactions', [StockTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create-in', [StockTransactionController::class, 'createIn'])->name('transactions.create-in');
    Route::get('/transactions/create-out', [StockTransactionController::class, 'createOut'])->name('transactions.create-out');
    Route::post('/transactions', [StockTransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}', [StockTransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions/{transaction}/print', [StockTransactionController::class, 'print'])->name('transactions.print');

    // Stock Adjustments (Stock Opname)
    Route::get('/adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
    Route::get('/adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
    Route::post('/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');

    // Reports (Laporan Inventaris & Mutasi)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
});
