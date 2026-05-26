<?php

use App\Http\Controllers\Admin\UserAccessController;
use App\Http\Controllers\Data\JoContractController;
use App\Http\Controllers\Data\JoContractItemController;
use App\Http\Controllers\Master\AreaController;
use App\Http\Controllers\Master\ContractController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\InvoiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Data\JoOtherController;
use App\Http\Controllers\Data\JoTramperController;
use App\Http\Controllers\Master\OtherController;
use App\Http\Controllers\Master\PortController;
use App\Http\Controllers\Master\VesselController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

// Semua route di bawah ini memerlukan login
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


    // User Management Routes
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::resource('user', UserController::class);
        Route::get('user/api/select', [UserController::class, 'getForSelect'])->name('user.api.select');
        Route::get('user/api/statistics', [UserController::class, 'statistics'])->name('user.api.statistics');

        // User Access Routes
        Route::get('user/{user}/access/edit', [UserAccessController::class, 'edit'])->name('user-access.edit');
        Route::put('user/{user}/access', [UserAccessController::class, 'update'])->name('user-access.update');
        Route::post('user/{user}/access/grant-full', [UserAccessController::class, 'grantFullAccess'])->name('user-access.grant-full');
        Route::delete('user/{user}/access/revoke-all', [UserAccessController::class, 'revokeAllAccess'])->name('user-access.revoke-all');
        Route::post('user/{user}/access/copy', [UserAccessController::class, 'copyAccess'])->name('user-access.copy');
    });

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

    // Master Vessel Routes
    Route::prefix('master/vessel')->name('vessel.')->group(function () {
        Route::get('/', [VesselController::class, 'index'])->name('index');
        Route::get('/create', [VesselController::class, 'create'])->name('create');
        Route::post('/', [VesselController::class, 'store'])->name('store');
        Route::get('/{id}', [VesselController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [VesselController::class, 'edit'])->name('edit');
        Route::put('/{id}', [VesselController::class, 'update'])->name('update');
        Route::delete('/{id}', [VesselController::class, 'destroy'])->name('destroy');

        // API Routes
        Route::get('/api/select', [VesselController::class, 'getForSelect'])->name('api.select');
        Route::get('/api/vessel-types', [VesselController::class, 'getVesselTypes'])->name('api.vessel-types');
        Route::get('/api/flags', [VesselController::class, 'getFlags'])->name('api.flags');
        Route::get('/api/statistics', [VesselController::class, 'statistics'])->name('api.statistics');
        Route::post('/api/bulk-delete', [VesselController::class, 'bulkDelete'])->name('api.bulk-delete');
    });

    // Master Port Routes
    Route::prefix('master/port')->name('port.')->group(function () {
        Route::get('/', [PortController::class, 'index'])->name('index');
        Route::get('/create', [PortController::class, 'create'])->name('create');
        Route::post('/', [PortController::class, 'store'])->name('store');
        Route::get('/{id}', [PortController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PortController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PortController::class, 'update'])->name('update');
        Route::delete('/{id}', [PortController::class, 'destroy'])->name('destroy');

        // API Routes
        Route::get('/api/select', [PortController::class, 'getForSelect'])->name('api.select');
        Route::get('/api/countries', [PortController::class, 'getCountries'])->name('api.countries');
        Route::get('/api/provinces', [PortController::class, 'getProvinces'])->name('api.provinces');
        Route::get('/api/statistics', [PortController::class, 'statistics'])->name('api.statistics');
        Route::post('/api/bulk-delete', [PortController::class, 'bulkDelete'])->name('api.bulk-delete');
    });

    // Master Other Routes
    Route::prefix('master/other')->name('other.')->group(function () {
        Route::get('/', [OtherController::class, 'index'])->name('index');
        Route::get('/create', [OtherController::class, 'create'])->name('create');
        Route::post('/', [OtherController::class, 'store'])->name('store');
        Route::get('/{id}', [OtherController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [OtherController::class, 'edit'])->name('edit');
        Route::put('/{id}', [OtherController::class, 'update'])->name('update');
        Route::delete('/{id}', [OtherController::class, 'destroy'])->name('destroy');

        // API Routes
        Route::get('/api/select', [OtherController::class, 'getForSelect'])->name('api.select');
        Route::get('/api/codes', [OtherController::class, 'getCodes'])->name('api.codes');
        Route::get('/api/statistics', [OtherController::class, 'statistics'])->name('api.statistics');
        Route::post('/api/bulk-delete', [OtherController::class, 'bulkDelete'])->name('api.bulk-delete');
    });

    // JO Contract Routes
    Route::prefix('data')->group(function () {
        Route::resource('jo-contract', JoContractController::class);
        Route::resource('jo-tramper', JoTramperController::class);
        Route::get('jo-tramper-select', [JoTramperController::class, 'getForSelect']);
        Route::post('jo-tramper-bulk-delete', [JoTramperController::class, 'bulkDelete']);

        Route::resource('jo-other', JoOtherController::class);
        Route::get('jo-other-select', [JoOtherController::class, 'getForSelect']);
        Route::post('jo-other-bulk-delete', [JoOtherController::class, 'bulkDelete']);

        // JO Contract Item Routes
        Route::resource('jo-contract-item', JoContractItemController::class);
        Route::post('jo-contract-item/{id}/restore', [JoContractItemController::class, 'restore']);
        Route::delete('jo-contract-item/{id}/force-delete', [JoContractItemController::class, 'forceDelete']);
        Route::post('jo-contract-item-bulk-delete', [JoContractItemController::class, 'bulkDelete']);
        Route::post('jo-contract-item-bulk-restore', [JoContractItemController::class, 'bulkRestore']);
    });

    // JO Contract - Realtime Auto-Save Routes
    Route::prefix('jo-contract')->name('jo-contract.')->group(function () {
        // Header operations (Realtime auto-save)
        Route::post('/header/store', [JoContractController::class, 'storeHeader'])->name('header.store');
        Route::post('/header/update/{id}', [JoContractController::class, 'updateHeader'])->name('header.update');

        // Item operations (Realtime auto-save)
        Route::get('/item/show/{id}', [JoContractController::class, 'showItem'])->name('item.show');
        Route::post('/item/store', [JoContractController::class, 'storeItem'])->name('item.store');
        Route::put('/item/update/{id}', [JoContractController::class, 'updateItem'])->name('item.update');
        Route::delete('/item/destroy/{id}', [JoContractController::class, 'destroyItem'])->name('item.destroy');
        Route::get('/{id}/items', [JoContractController::class, 'getItems'])->name('items.get');

        // Save All Changes (NEW)
        Route::post('/save-all/{id}', [JoContractController::class, 'saveAllChanges'])->name('save-all');

        // Additional API endpoints
        Route::get('/for-select', [JoContractController::class, 'getForSelect'])->name('for-select');
        Route::post('/bulk-delete', [JoContractController::class, 'bulkDelete'])->name('bulk-delete');
    });
});
