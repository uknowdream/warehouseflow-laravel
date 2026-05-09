<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockMoveController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::get('products/{product}/qr', [ProductController::class, 'qr'])->name('products.qr');

    Route::resource('warehouses', WarehouseController::class);
    Route::resource('locations', LocationController::class);
    Route::get('locations/{location}/qr', [LocationController::class, 'qr'])->name('locations.qr');
    Route::resource('users', UserController::class)->except(['show'])->middleware('admin');

    Route::get('stock-moves', [StockMoveController::class, 'index'])->name('stock-moves.index');
    Route::get('stock-moves/in', [StockMoveController::class, 'createIn'])->name('stock-moves.in.create');
    Route::post('stock-moves/in', [StockMoveController::class, 'storeIn'])->name('stock-moves.in.store');
    Route::get('stock-moves/out', [StockMoveController::class, 'createOut'])->name('stock-moves.out.create');
    Route::post('stock-moves/out', [StockMoveController::class, 'storeOut'])->name('stock-moves.out.store');
    Route::get('stock-moves/transfer', [StockMoveController::class, 'createTransfer'])->name('stock-moves.transfer.create');
    Route::post('stock-moves/transfer', [StockMoveController::class, 'storeTransfer'])->name('stock-moves.transfer.store');

    Route::resource('stock-opnames', StockOpnameController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('stock-opnames/{stockOpname}/scan', [StockOpnameController::class, 'scan'])->name('stock-opnames.scan');
    Route::post('stock-opnames/{stockOpname}/scan', [StockOpnameController::class, 'submitScan'])->name('stock-opnames.scan.submit');
    Route::post('stock-opnames/{stockOpname}/submit-approval', [StockOpnameController::class, 'submitApproval'])->name('stock-opnames.submit-approval');
    Route::post('stock-opnames/{stockOpname}/approve', [StockOpnameController::class, 'approve'])->name('stock-opnames.approve');
    Route::post('stock-opnames/{stockOpname}/reject', [StockOpnameController::class, 'reject'])->name('stock-opnames.reject');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
