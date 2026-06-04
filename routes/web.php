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

Route::get('/', fn() => redirect()->route('login'));

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // ── Admin ─────────────────────────────────────────────────
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::resource('user', UserController::class);
        Route::get('user/api/select', [UserController::class, 'getForSelect'])->name('user.api.select');
        Route::get('user/api/statistics', [UserController::class, 'statistics'])->name('user.api.statistics');
        Route::get('user/{user}/access/edit', [UserAccessController::class, 'edit'])->name('user-access.edit');
        Route::put('user/{user}/access', [UserAccessController::class, 'update'])->name('user-access.update');
        Route::post('user/{user}/access/grant-full', [UserAccessController::class, 'grantFullAccess'])->name('user-access.grant-full');
        Route::delete('user/{user}/access/revoke-all', [UserAccessController::class, 'revokeAllAccess'])->name('user-access.revoke-all');
        Route::post('user/{user}/access/copy', [UserAccessController::class, 'copyAccess'])->name('user-access.copy');
    });

    // ── Master routes ─────────────────────────────────────────
    Route::prefix('master/customer')->name('customer.')->group(function () {
        Route::get('/export', [CustomerController::class, 'export'])->name('export');
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [CustomerController::class, 'restore'])->name('restore');
    });

    Route::prefix('master/contract')->name('contract.')->group(function () {
        Route::get('/export', [ContractController::class, 'export'])->name('export');
        Route::get('/', [ContractController::class, 'index'])->name('index');
        Route::get('/create', [ContractController::class, 'create'])->name('create');
        Route::post('/', [ContractController::class, 'store'])->name('store');
        Route::get('/{id}', [ContractController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ContractController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ContractController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContractController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('master/area')->name('area.')->group(function () {
        Route::get('/export', [AreaController::class, 'export'])->name('export');
        Route::get('/', [AreaController::class, 'index'])->name('index');
        Route::get('/create', [AreaController::class, 'create'])->name('create');
        Route::post('/', [AreaController::class, 'store'])->name('store');
        Route::get('/{id}', [AreaController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AreaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AreaController::class, 'update'])->name('update');
        Route::delete('/{id}', [AreaController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('master/invoice')->name('invoice.')->group(function () {
        Route::get('/export', [InvoiceController::class, 'export'])->name('export');
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('master/vessel')->name('vessel.')->group(function () {
        Route::get('/', [VesselController::class, 'index'])->name('index');
        Route::get('/create', [VesselController::class, 'create'])->name('create');
        Route::post('/', [VesselController::class, 'store'])->name('store');
        Route::get('/{id}', [VesselController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [VesselController::class, 'edit'])->name('edit');
        Route::put('/{id}', [VesselController::class, 'update'])->name('update');
        Route::delete('/{id}', [VesselController::class, 'destroy'])->name('destroy');
        Route::get('/api/select', [VesselController::class, 'getForSelect'])->name('api.select');
        Route::get('/api/vessel-types', [VesselController::class, 'getVesselTypes'])->name('api.vessel-types');
        Route::get('/api/flags', [VesselController::class, 'getFlags'])->name('api.flags');
        Route::get('/api/statistics', [VesselController::class, 'statistics'])->name('api.statistics');
        Route::post('/api/bulk-delete', [VesselController::class, 'bulkDelete'])->name('api.bulk-delete');
    });

    Route::prefix('master/port')->name('port.')->group(function () {
        Route::get('/', [PortController::class, 'index'])->name('index');
        Route::get('/create', [PortController::class, 'create'])->name('create');
        Route::post('/', [PortController::class, 'store'])->name('store');
        Route::get('/{id}', [PortController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PortController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PortController::class, 'update'])->name('update');
        Route::delete('/{id}', [PortController::class, 'destroy'])->name('destroy');
        Route::get('/api/select', [PortController::class, 'getForSelect'])->name('api.select');
        Route::get('/api/countries', [PortController::class, 'getCountries'])->name('api.countries');
        Route::get('/api/provinces', [PortController::class, 'getProvinces'])->name('api.provinces');
        Route::get('/api/statistics', [PortController::class, 'statistics'])->name('api.statistics');
        Route::post('/api/bulk-delete', [PortController::class, 'bulkDelete'])->name('api.bulk-delete');
    });

    Route::prefix('master/other')->name('other.')->group(function () {
        Route::get('/', [OtherController::class, 'index'])->name('index');
        Route::get('/create', [OtherController::class, 'create'])->name('create');
        Route::post('/', [OtherController::class, 'store'])->name('store');
        Route::get('/{id}', [OtherController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [OtherController::class, 'edit'])->name('edit');
        Route::put('/{id}', [OtherController::class, 'update'])->name('update');
        Route::delete('/{id}', [OtherController::class, 'destroy'])->name('destroy');
        Route::get('/api/select', [OtherController::class, 'getForSelect'])->name('api.select');
        Route::get('/api/codes', [OtherController::class, 'getCodes'])->name('api.codes');
        Route::get('/api/statistics', [OtherController::class, 'statistics'])->name('api.statistics');
        Route::post('/api/bulk-delete', [OtherController::class, 'bulkDelete'])->name('api.bulk-delete');
    });

    // ── Data routes ───────────────────────────────────────────
    Route::prefix('data')->group(function () {

        // ── JO Tramper — custom routes BEFORE resource ──────────
        Route::post('jo-tramper/header/store', [JoTramperController::class, 'storeHeader'])
            ->name('jo-tramper.header.store');
        Route::post('jo-tramper/header/update/{id}', [JoTramperController::class, 'updateHeader'])
            ->name('jo-tramper.header.update');
        Route::get('jo-tramper/item/show/{id}', [JoTramperController::class, 'showItem'])
            ->name('jo-tramper.item.show');
        Route::post('jo-tramper/item/store', [JoTramperController::class, 'storeItem'])
            ->name('jo-tramper.item.store');
        Route::put('jo-tramper/item/update/{id}', [JoTramperController::class, 'updateItem'])
            ->name('jo-tramper.item.update');
        Route::delete('jo-tramper/item/destroy/{id}', [JoTramperController::class, 'destroyItem'])
            ->name('jo-tramper.item.destroy');
        Route::get('jo-tramper/{id}/items', [JoTramperController::class, 'getItems'])
            ->name('jo-tramper.items.get');
        Route::post('jo-tramper/save-all/{id}', [JoTramperController::class, 'saveAllChanges'])
            ->name('jo-tramper.save-all');
        Route::get('jo-tramper-select', [JoTramperController::class, 'getForSelect']);
        Route::post('jo-tramper-bulk-delete', [JoTramperController::class, 'bulkDelete']);

        // Resource AFTER custom routes
        Route::resource('jo-tramper', JoTramperController::class);

        // ── JO Other ─────────────────────────────────────────────
        Route::post('jo-other/header/store', [JoOtherController::class, 'storeHeader'])
            ->name('jo-other.header.store');
        Route::post('jo-other/header/update/{id}', [JoOtherController::class, 'updateHeader'])
            ->name('jo-other.header.update');
        Route::get('jo-other/item/show/{id}', [JoOtherController::class, 'showItem'])
            ->name('jo-other.item.show');
        Route::post('jo-other/item/store', [JoOtherController::class, 'storeItem'])
            ->name('jo-other.item.store');
        Route::put('jo-other/item/update/{id}', [JoOtherController::class, 'updateItem'])
            ->name('jo-other.item.update');
        Route::delete('jo-other/item/destroy/{id}', [JoOtherController::class, 'destroyItem'])
            ->name('jo-other.item.destroy');
        Route::get('jo-other/{id}/items', [JoOtherController::class, 'getItems'])
            ->name('jo-other.items.get');
        Route::post('jo-other/save-all/{id}', [JoOtherController::class, 'saveAllChanges'])
            ->name('jo-other.save-all');
        Route::resource('jo-other', JoOtherController::class);
        Route::get('jo-other-select', [JoOtherController::class, 'getForSelect']);
        Route::post('jo-other-bulk-delete', [JoOtherController::class, 'bulkDelete']);

        // ── JO Contract ──────────────────────────────────────────
        Route::resource('jo-contract', JoContractController::class);

        // JO Contract Items
        Route::resource('jo-contract-item', JoContractItemController::class);
        Route::post('jo-contract-item/{id}/restore', [JoContractItemController::class, 'restore']);
        Route::delete('jo-contract-item/{id}/force-delete', [JoContractItemController::class, 'forceDelete']);
        Route::post('jo-contract-item-bulk-delete', [JoContractItemController::class, 'bulkDelete']);
        Route::post('jo-contract-item-bulk-restore', [JoContractItemController::class, 'bulkRestore']);
    });

    // ── JO Contract realtime routes ───────────────────────────
    Route::prefix('jo-contract')->name('jo-contract.')->group(function () {
        Route::post('/header/store', [JoContractController::class, 'storeHeader'])->name('header.store');
        Route::post('/header/update/{id}', [JoContractController::class, 'updateHeader'])->name('header.update');
        Route::get('/item/show/{id}', [JoContractController::class, 'showItem'])->name('item.show');
        Route::post('/item/store', [JoContractController::class, 'storeItem'])->name('item.store');
        Route::put('/item/update/{id}', [JoContractController::class, 'updateItem'])->name('item.update');
        Route::delete('/item/destroy/{id}', [JoContractController::class, 'destroyItem'])->name('item.destroy');
        Route::get('/{id}/items', [JoContractController::class, 'getItems'])->name('items.get');
        Route::post('/save-all/{id}', [JoContractController::class, 'saveAllChanges'])->name('save-all');
        Route::get('/for-select', [JoContractController::class, 'getForSelect'])->name('for-select');
        Route::post('/bulk-delete', [JoContractController::class, 'bulkDelete'])->name('bulk-delete');
    });
});
