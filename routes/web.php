<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StockMoveController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}/qr', [ProductController::class, 'qr'])->name('products.qr');
    Route::resource('products', ProductController::class)
        ->except(['index', 'show'])
        ->middleware('master-data');

    Route::get('warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::resource('warehouses', WarehouseController::class)
        ->except(['index', 'show'])
        ->middleware('master-data');

    Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('locations/{location}/qr', [LocationController::class, 'qr'])->name('locations.qr');
    Route::resource('locations', LocationController::class)
        ->except(['index', 'show'])
        ->middleware('master-data');
    Route::resource('users', UserController::class)->except(['show'])->middleware('admin');
    Route::get('roles', [RoleController::class, 'index'])
        ->middleware('admin')
        ->name('roles.index');
    Route::get('audit-logs', [AuditLogController::class, 'index'])
        ->middleware('admin')
        ->name('audit-logs.index');

    Route::get('stock-moves', [StockMoveController::class, 'index'])->name('stock-moves.index');
    Route::middleware('stock-operator')->group(function () {
        Route::get('stock-moves/in', [StockMoveController::class, 'createIn'])->name('stock-moves.in.create');
        Route::post('stock-moves/in', [StockMoveController::class, 'storeIn'])->name('stock-moves.in.store');
        Route::get('stock-moves/out', [StockMoveController::class, 'createOut'])->name('stock-moves.out.create');
        Route::post('stock-moves/out', [StockMoveController::class, 'storeOut'])->name('stock-moves.out.store');
        Route::get('stock-moves/transfer', [StockMoveController::class, 'createTransfer'])->name('stock-moves.transfer.create');
        Route::post('stock-moves/transfer', [StockMoveController::class, 'storeTransfer'])->name('stock-moves.transfer.store');
    });

    Route::get('stock-opnames', [StockOpnameController::class, 'index'])->name('stock-opnames.index');
    Route::middleware('stock-operator')->group(function () {
        Route::get('stock-opnames/create', [StockOpnameController::class, 'create'])->name('stock-opnames.create');
        Route::post('stock-opnames', [StockOpnameController::class, 'store'])->name('stock-opnames.store');
        Route::get('stock-opnames/{stockOpname}/scan', [StockOpnameController::class, 'scan'])->name('stock-opnames.scan');
        Route::post('stock-opnames/{stockOpname}/scan', [StockOpnameController::class, 'submitScan'])->name('stock-opnames.scan.submit');
        Route::post('stock-opnames/{stockOpname}/submit-approval', [StockOpnameController::class, 'submitApproval'])->name('stock-opnames.submit-approval');
    });
    Route::post('stock-opnames/{stockOpname}/approve', [StockOpnameController::class, 'approve'])
        ->middleware('approve-opname')
        ->name('stock-opnames.approve');
    Route::post('stock-opnames/{stockOpname}/reject', [StockOpnameController::class, 'reject'])
        ->middleware('approve-opname')
        ->name('stock-opnames.reject');
    Route::get('stock-opnames/{stockOpname}', [StockOpnameController::class, 'show'])->name('stock-opnames.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
