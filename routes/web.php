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
use App\Http\Controllers\Data\KasbonContractController;
use App\Http\Controllers\Data\KasbonGenController;
use App\Http\Controllers\Data\KasbonOtherController;
use App\Http\Controllers\Data\KasbonTramperController;
use App\Http\Controllers\Master\BranchController;
use App\Http\Controllers\Master\DepartemenController;
use App\Http\Controllers\Master\OtherController;
use App\Http\Controllers\Master\PortController;
use App\Http\Controllers\Master\ReleaseToController;
use App\Http\Controllers\Master\VesselController;
use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Auth::routes();

Route::middleware('auth')->patch('/user/theme', [ThemeController::class, 'update'])
    ->name('user.theme.update');

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

    Route::prefix('master/departemen')->name('departemen.')->group(function () {
        Route::get('/export', [DepartemenController::class, 'export'])->name('export');
        Route::get('/', [DepartemenController::class, 'index'])->name('index');
        Route::get('/create', [DepartemenController::class, 'create'])->name('create');
        Route::post('/', [DepartemenController::class, 'store'])->name('store');
        Route::get('/{id}', [DepartemenController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [DepartemenController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DepartemenController::class, 'update'])->name('update');
        Route::delete('/{id}', [DepartemenController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [DepartemenController::class, 'restore'])->name('restore');
    });

    Route::prefix('master/branch')->name('branch.')->group(function () {
        Route::get('/export', [BranchController::class, 'export'])->name('export');
        Route::get('/', [BranchController::class, 'index'])->name('index');
        Route::get('/create', [BranchController::class, 'create'])->name('create');
        Route::post('/', [BranchController::class, 'store'])->name('store');
        Route::get('/{id}', [BranchController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BranchController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BranchController::class, 'update'])->name('update');
        Route::delete('/{id}', [BranchController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [BranchController::class, 'restore'])->name('restore');
    });

    Route::prefix('master/release_to')->name('release_to.')->group(function () {
        Route::get('/export', [ReleaseToController::class, 'export'])->name('export');
        Route::get('/', [ReleaseToController::class, 'index'])->name('index');
        Route::get('/create', [ReleaseToController::class, 'create'])->name('create');
        Route::post('/', [ReleaseToController::class, 'store'])->name('store');
        Route::get('/{id}', [ReleaseToController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ReleaseToController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ReleaseToController::class, 'update'])->name('update');
        Route::delete('/{id}', [ReleaseToController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [ReleaseToController::class, 'restore'])->name('restore');
    });

    // ── Data routes ───────────────────────────────────────────
    Route::prefix('data')->group(function () {

        //================= JO TRAMPER ROUTE GROUP //=================
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
        Route::get('jo-tramper/{id}/export-pdf', [JoTramperController::class, 'exportPdf'])
            ->name('jo-tramper.export-pdf');

        // Resource AFTER custom routes
        Route::get('/jo-tramper/export', [JoTramperController::class, 'export'])
            ->name('jo-tramper.export');
        Route::resource('jo-tramper', JoTramperController::class);
        //================= JO TRAMPER ROUTE GROUP ====================//

        //================= JO OTHER ROUTE GROUP ====================//
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
        Route::get('jo-other/{id}/export-pdf', [JoOtherController::class, 'exportPdf'])
            ->name('jo-other.export-pdf');
        Route::get('/jo-other/export', [JoOtherController::class, 'export'])
            ->name('jo-other.export');
        Route::resource('jo-other', JoOtherController::class);
        Route::get('jo-other-select', [JoOtherController::class, 'getForSelect']);
        Route::post('jo-other-bulk-delete', [JoOtherController::class, 'bulkDelete']);
        //================= JO TRAMPER ROUTE GROUP ====================//


        //================= JO CONTRACT ROUTE GROUP ====================//
        Route::get('/jo-contract/export', [JoContractController::class, 'export'])
            ->name('jo-contract.export');
        Route::resource('jo-contract', JoContractController::class);

        // JO Contract Items

        Route::get('jo-contract/{id}/export-pdf', [JoContractController::class, 'exportPdf'])
            ->name('jo-contract.export-pdf');
        Route::resource('jo-contract-item', JoContractItemController::class);
        Route::post('jo-contract-item/{id}/restore', [JoContractItemController::class, 'restore']);
        Route::delete('jo-contract-item/{id}/force-delete', [JoContractItemController::class, 'forceDelete']);
        Route::post('jo-contract-item-bulk-delete', [JoContractItemController::class, 'bulkDelete']);
        Route::post('jo-contract-item-bulk-restore', [JoContractItemController::class, 'bulkRestore']);
        // Sync kurs semua item USD
        Route::post('jo-contract/items/sync-kurs/{id}', [JoContractController::class, 'syncItemsKurs'])
            ->name('jo-contract.sync-kurs');
    });

    // ── JO Contract realtime routes ───────────────────────────
    Route::prefix('jo-contract')->name('jo-contract.')->group(function () {
        Route::post('/header/store', [JoContractController::class, 'storeHeader'])->name('header.store');
        Route::post('/header/update/{id}', [JoContractController::class, 'updateHeader'])->name('header.update');
        Route::get('/item/show/{id}', [JoContractController::class, 'showItem'])->name('item.show');
        Route::post('/item/store', [JoContractController::class, 'storeItem'])->name('item.store');
        Route::put('/item/update/{id}', [JoContractController::class, 'updateItem'])->name('item.update');
        Route::delete('/item/destroy/{id}', [JoContractController::class, 'destroyItem'])->name('item.destroy');
        Route::patch('/item/update-kurs/{id}', [JoContractController::class, 'updateItemKurs'])->name('item.update-kurs');
        Route::get('/{id}/items', [JoContractController::class, 'getItems'])->name('items.get');
        Route::post('/save-all/{id}', [JoContractController::class, 'saveAllChanges'])->name('save-all');
        Route::get('/for-select', [JoContractController::class, 'getForSelect'])->name('for-select');
        Route::post('/bulk-delete', [JoContractController::class, 'bulkDelete'])->name('bulk-delete');
    });
    //================= JO CONTRACT ROUTE GROUP ====================//


    //================= KASBON CONTRACT ROUTE GROUP ====================//
    Route::get('/data/kasbon-contract/{id}/export-pdf', [KasbonContractController::class, 'exportPdf'])
        ->name('kasbon-contract.export-pdf');
    Route::get('/data/kasbon-contract/export', [KasbonContractController::class, 'export'])
        ->name('kasbon-contract.export');
    Route::post('kasbon-contract/header/store', [KasbonContractController::class, 'storeHeader'])
        ->name('kasbon-contract.header.store');

    Route::post('kasbon-contract/header/update/{id}', [KasbonContractController::class, 'updateHeader'])
        ->name('kasbon-contract.header.update');

    Route::get('kasbon-contract/item/show/{id}', [KasbonContractController::class, 'showItem'])
        ->name('kasbon-contract.item.show');

    Route::post('kasbon-contract/item/store', [KasbonContractController::class, 'storeItem'])
        ->name('kasbon-contract.item.store');

    Route::post('/kasbon-contract/items/bulk-save/{id}', [KasbonContractController::class, 'bulkSaveItems'])
        ->name('kasbon-contract.items.bulk-save');

    Route::put('kasbon-contract/item/update/{id}', [KasbonContractController::class, 'updateItem'])
        ->name('kasbon-contract.item.update');

    Route::delete('kasbon-contract/item/destroy/{id}', [KasbonContractController::class, 'destroyItem'])
        ->name('kasbon-contract.item.destroy');

    Route::get('kasbon-contract/{id}/items', [KasbonContractController::class, 'getItems'])
        ->name('kasbon-contract.items.get');

    Route::get('kasbon-contract/jo-items', [KasbonContractController::class, 'getJoContractItems'])
        ->name('kasbon-contract.jo-items.get');

    Route::resource('kasbon-contract', KasbonContractController::class);

    Route::get('kasbon-contract-select', [KasbonContractController::class, 'getForSelect'])
        ->name('kasbon-contract.select');

    Route::post('kasbon-contract-bulk-delete', [KasbonContractController::class, 'bulkDelete'])
        ->name('kasbon-contract.bulk-delete');
    // ================= END KASBON CONTRACT ROUTE GROUP ====================//

    //================= KASBON TRAMPER ROUTE GROUP ====================//
    Route::get('/data/kasbon-tramper/{id}/export-pdf', [KasbonTramperController::class, 'exportPdf'])
        ->name('kasbon-tramper.export-pdf');
    Route::get('/data/kasbon-tramper/export', [KasbonTramperController::class, 'export'])
        ->name('kasbon-tramper.export');
    Route::post('kasbon-tramper/header/store', [KasbonTramperController::class, 'storeHeader'])
        ->name('kasbon-tramper.header.store');

    Route::post('kasbon-tramper/header/update/{id}', [KasbonTramperController::class, 'updateHeader'])
        ->name('kasbon-tramper.header.update');

    Route::get('kasbon-tramper/item/show/{id}', [KasbonTramperController::class, 'showItem'])
        ->name('kasbon-tramper.item.show');

    Route::post('kasbon-tramper/item/store', [KasbonTramperController::class, 'storeItem'])
        ->name('kasbon-tramper.item.store');

    Route::post('/kasbon-tramper/items/bulk-save/{id}', [KasbonTramperController::class, 'bulkSaveItems'])
        ->name('kasbon-tramper.items.bulk-save');

    Route::put('kasbon-tramper/item/update/{id}', [KasbonTramperController::class, 'updateItem'])
        ->name('kasbon-tramper.item.update');

    Route::delete('kasbon-tramper/item/destroy/{id}', [KasbonTramperController::class, 'destroyItem'])
        ->name('kasbon-tramper.item.destroy');

    Route::get('kasbon-tramper/{id}/items', [KasbonTramperController::class, 'getItems'])
        ->name('kasbon-tramper.items.get');

    Route::get('kasbon-tramper/jo-items', [KasbonTramperController::class, 'getJoTramperItems'])
        ->name('kasbon-tramper.jo-items.get');

    Route::resource('kasbon-tramper', KasbonTramperController::class);

    Route::get('kasbon-tramper-select', [KasbonTramperController::class, 'getForSelect'])
        ->name('kasbon-tramper.select');

    Route::post('kasbon-tramper-bulk-delete', [KasbonTramperController::class, 'bulkDelete'])
        ->name('kasbon-tramper.bulk-delete');
    //================= END KASBON TRAMPER ROUTE GROUP ====================//

    //================= KASBON OTHER ROUTE GROUP ====================//
    Route::get('/data/kasbon-other/{id}/export-pdf', [KasbonOtherController::class, 'exportPdf'])
        ->name('kasbon-other.export-pdf');
    Route::get('/data/kasbon-other/export', [KasbonOtherController::class, 'export'])
        ->name('kasbon-other.export');
    Route::post('kasbon-other/header/store', [KasbonOtherController::class, 'storeHeader'])
        ->name('kasbon-other.header.store');
    Route::post('kasbon-other/header/update/{id}', [KasbonOtherController::class, 'updateHeader'])
        ->name('kasbon-other.header.update');
    Route::get('kasbon-other/item/show/{id}', [KasbonOtherController::class, 'showItem'])
        ->name('kasbon-other.item.show');
    Route::post('kasbon-other/item/store', [KasbonOtherController::class, 'storeItem'])
        ->name('kasbon-other.item.store');
    Route::post('/kasbon-other/items/bulk-save/{id}', [KasbonOtherController::class, 'bulkSaveItems'])
        ->name('kasbon-other.items.bulk-save');
    Route::put('kasbon-other/item/update/{id}', [KasbonOtherController::class, 'updateItem'])
        ->name('kasbon-other.item.update');
    Route::delete('kasbon-other/item/destroy/{id}', [KasbonOtherController::class, 'destroyItem'])
        ->name('kasbon-other.item.destroy');
    Route::get('kasbon-other/{id}/items', [KasbonOtherController::class, 'getItems'])
        ->name('kasbon-other.items.get');
    Route::get('kasbon-other/jo-items', [KasbonOtherController::class, 'getJoOtherItems'])
        ->name('kasbon-other.jo-items.get');
    Route::resource('kasbon-other', KasbonOtherController::class);
    Route::get('kasbon-other-select', [KasbonOtherController::class, 'getForSelect'])
        ->name('kasbon-other.select');
    Route::post('kasbon-other-bulk-delete', [KasbonOtherController::class, 'bulkDelete'])
        ->name('kasbon-other.bulk-delete');
    //================= END KASBON OTHER ROUTE GROUP ====================//

    //================= KASBON GENERAL ROUTE GROUP ====================//
    Route::get('/data/kasbon-gen/{id}/export-pdf', [KasbonGenController::class, 'exportPdf'])
        ->name('kasbon-gen.export-pdf');
    Route::get('/data/kasbon-gen/export', [KasbonGenController::class, 'export'])
        ->name('kasbon-gen.export');
    Route::post('kasbon-gen/header/store', [KasbonGenController::class, 'storeHeader'])
        ->name('kasbon-gen.header.store');
    Route::post('kasbon-gen/header/update/{id}', [KasbonGenController::class, 'updateHeader'])
        ->name('kasbon-gen.header.update');
    Route::get('kasbon-gen/item/show/{id}', [KasbonGenController::class, 'showItem'])
        ->name('kasbon-gen.item.show');
    Route::post('kasbon-gen/item/store', [KasbonGenController::class, 'storeItem'])
        ->name('kasbon-gen.item.store');
    Route::put('kasbon-gen/item/update/{id}', [KasbonGenController::class, 'updateItem'])
        ->name('kasbon-gen.item.update');
    Route::delete('kasbon-gen/item/destroy/{id}', [KasbonGenController::class, 'destroyItem'])
        ->name('kasbon-gen.item.destroy');
    Route::get('kasbon-gen/{id}/items', [KasbonGenController::class, 'getItems'])
        ->name('kasbon-gen.items.get');
    Route::resource('kasbon-gen', KasbonGenController::class);
    Route::get('kasbon-gen-select', [KasbonGenController::class, 'getForSelect'])
        ->name('kasbon-gen.select');
    Route::post('kasbon-gen-bulk-delete', [KasbonGenController::class, 'bulkDelete'])
        ->name('kasbon-gen.bulk-delete');
    //================= END KASBON GENERAL ROUTE GROUP ====================//

});
