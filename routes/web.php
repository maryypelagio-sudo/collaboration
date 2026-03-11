<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MaintenanceController;
use App\Models\Item;
use App\Models\Category;

Route::get('/', function () {
    return Inertia::render('Dashboard', [
        'stats' => [
            'total_items' => Item::count(),
            'low_stock' => Item::whereColumn('quantity', '<=', 'min_stock_level')->count(),
            'total_categories' => Category::count(),
        ],
        'recent_items' => Item::with('category')->latest()->take(6)->get(),
    ]);
})->name('dashboard');

Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class , 'index'])->name('index');
    Route::post('/items', [InventoryController::class , 'store'])->name('items.store');
    Route::put('/items/{item}', [InventoryController::class , 'update'])->name('items.update');
    Route::delete('/items/{item}', [InventoryController::class , 'destroy'])->name('items.destroy');
    Route::post('/items/{item}/adjust', [InventoryController::class , 'adjustStock'])->name('items.adjust');

    // Category Routes
    Route::post('/categories', [CategoryController::class , 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class , 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class , 'destroy'])->name('categories.destroy');
});

Route::prefix('borrowings')->name('borrowings.')->group(function () {
    Route::get('/', [\App\Http\Controllers\BorrowingController::class , 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\BorrowingController::class , 'store'])->name('store');
    Route::post('/{borrowing}/return', [\App\Http\Controllers\BorrowingController::class , 'return'])->name('return');
});

// Include report routes
require __DIR__.'/report.php';
