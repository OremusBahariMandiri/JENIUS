<?php

use App\Http\Controllers\Data\JoContractController;
use App\Http\Controllers\Data\JoContractItemController;
use App\Http\Controllers\Master\AreaController;
use App\Http\Controllers\Master\ContractController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

// Semua route di bawah ini memerlukan login
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Customer Routes
    Route::prefix('master/customer')->name('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [CustomerController::class, 'restore'])->name('restore');
    });

    // Contract Routes
    Route::prefix('master/contract')->name('contract.')->group(function () {
        Route::get('/', [ContractController::class, 'index'])->name('index');
        Route::get('/create', [ContractController::class, 'create'])->name('create');
        Route::post('/', [ContractController::class, 'store'])->name('store');
        Route::get('/{id}', [ContractController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ContractController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ContractController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContractController::class, 'destroy'])->name('destroy');
    });

    // Area Routes
    Route::prefix('master/area')->name('area.')->group(function () {
        Route::get('/', [AreaController::class, 'index'])->name('index');
        Route::get('/create', [AreaController::class, 'create'])->name('create');
        Route::post('/', [AreaController::class, 'store'])->name('store');
        Route::get('/{id}', [AreaController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AreaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AreaController::class, 'update'])->name('update');
        Route::delete('/{id}', [AreaController::class, 'destroy'])->name('destroy');
    });

    // Invoice Routes
    Route::prefix('master/invoice')->name('invoice.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
    });

    // JO Contract Routes
    Route::prefix('data')->group(function () {
        Route::resource('jo-contract', JoContractController::class);
        Route::get('jo-contract-select', [JoContractController::class, 'getForSelect']);
        Route::post('jo-contract-bulk-delete', [JoContractController::class, 'bulkDelete']);

        // JO Contract Item Routes
        Route::resource('jo-contract-item', JoContractItemController::class);
        Route::post('jo-contract-item/{id}/restore', [JoContractItemController::class, 'restore']);
        Route::delete('jo-contract-item/{id}/force-delete', [JoContractItemController::class, 'forceDelete']);
        Route::post('jo-contract-item-bulk-delete', [JoContractItemController::class, 'bulkDelete']);
        Route::post('jo-contract-item-bulk-restore', [JoContractItemController::class, 'bulkRestore']);
    });

});