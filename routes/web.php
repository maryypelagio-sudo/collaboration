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
            'total_categories' => \App\Models\Category::count(),
            'active_borrowings' => \App\Models\Borrowing::where('status', 'borrowed')->count(),
            'pending_maintenance' => \App\Models\MaintenanceLog::whereIn('status', ['pending', 'in_progress'])->count(),
        ],
        'recent_items' => Item::with('category')->latest()->take(6)->get()->map(function ($item) {
            $item->is_borrowed = $item->is_borrowed;
            $item->current_maintenance = $item->current_maintenance;
            return $item;
        }),
    ]);
})->name('dashboard');

Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');
    Route::post('/items', [InventoryController::class, 'store'])->name('items.store');
    Route::put('/items/{item}', [InventoryController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [InventoryController::class, 'destroy'])->name('items.destroy');
    Route::post('/items/{item}/adjust', [InventoryController::class, 'adjustStock'])->name('items.adjust');
    Route::post('/items/{item}/archive', [InventoryController::class, 'archive'])->name('items.archive');
    Route::post('/items/{item}/restore', [InventoryController::class, 'restore'])->name('items.restore');

    // Category Routes
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

Route::prefix('borrowings')->name('borrowings.')->group(function () {
    Route::get('/', [\App\Http\Controllers\BorrowingController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\BorrowingController::class, 'store'])->name('store');
    Route::post('/{borrowing}/return', [\App\Http\Controllers\BorrowingController::class, 'return'])->name('return');
});

Route::prefix('maintenance')->name('maintenance.')->group(function () {
    Route::get('/', [MaintenanceController::class, 'index'])->name('index');
    Route::post('/', [MaintenanceController::class, 'store'])->name('store');
    Route::put('/{log}', [MaintenanceController::class, 'update'])->name('update');
});

// Include report routes
require __DIR__ . '/report.php';
