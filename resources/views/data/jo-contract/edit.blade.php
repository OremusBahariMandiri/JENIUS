@extends('layouts.app')

@section('title', 'Edit JO Contract')

@push('styles')
    <style>
        .joContractEditPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .joContractEditPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .joContractEditPage .form-control:focus,
        .joContractEditPage .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .joContractEditPage .form-label {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .joContractEditPage .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .joContractEditPage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .joContractEditPage .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .joContractEditPage .btn-success:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: 600;
        }

        /* FLOATING BADGE ALERT */
        .floating-badge-alert {
            position: fixed;
            top: 80px;
            right: 30px;
            z-index: 9999;
            min-width: 250px;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            display: none;
            animation: slideInRight 0.4s ease-out;
            backdrop-filter: blur(10px);
        }

        .floating-badge-alert.show {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .floating-badge-alert.alert-saving {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
        }

        .floating-badge-alert.alert-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .floating-badge-alert.alert-error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .floating-badge-alert i {
            font-size: 1.3rem;
        }

        .floating-badge-alert .alert-text {
            flex: 1;
            font-weight: 600;
            font-size: 0.95rem;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .floating-badge-alert.hiding {
            animation: slideOutRight 0.4s ease-in;
        }

        .floating-badge-alert .spinner-border {
            width: 1.2rem;
            height: 1.2rem;
            border-width: 2px;
        }

        /* KURS SECTION */
        .kurs-section {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 10px;
            padding: 20px;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .kurs-section .form-label {
            color: rgba(255, 255, 255, 0.95);
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .kurs-section .form-control {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        /* ADD ITEM FORM SECTION */
        .add-item-form-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px dashed #10b981;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            transition: all 0.3s;
        }

        .add-item-form-section.edit-mode {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 2px solid #3b82f6;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }

        .add-item-form-section h6 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .add-item-form-section.edit-mode h6 {
            color: #1e40af;
        }

        .add-item-form-section h6 i {
            color: var(--primary-green);
            font-size: 1.2rem;
        }

        .add-item-form-section.edit-mode h6 i {
            color: #3b82f6;
        }

        .add-item-form-section .form-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.875rem;
        }

        .add-item-form-section .form-control,
        .add-item-form-section .form-select {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 0.6rem 0.75rem;
            transition: all 0.3s;
        }

        .add-item-form-section .form-control:focus,
        .add-item-form-section .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.3rem rgba(16, 185, 129, 0.15);
        }

        .add-item-form-section .currency-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .add-item-form-section .currency-label {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #2c3e50;
            padding: 0.6rem 0.75rem;
            font-size: 0.7rem;
            border-radius: 6px 0 0 6px;
            min-width: 50px;
            text-align: center;
            font-weight: 600;
        }

        .add-item-form-section .currency-input {
            border-radius: 0 6px 6px 0 !important;
            border-left: none !important;
        }

        .btn-add-to-table {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            transition: all 0.3s;
        }

        .btn-add-to-table:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
            color: white;
        }

        .btn-add-to-table i {
            margin-right: 8px;
        }

        .btn-edit-item {
            background: linear-gradient(135deg, #bbbbbb 0%, #5c5c5c 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(153, 153, 153, 0.4);
            transition: all 0.3s;
        }

        .btn-edit-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(128, 128, 128, 0.5);
            color: white;
        }

        /* TABLE STYLES */
        .table-items {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        .table-items thead th {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #2c3e50;
            padding: 12px 10px;
            font-weight: 600;
            font-size: 0.875rem;
            vertical-align: middle;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-items tbody td {
            border: 1px solid #dee2e6;
            padding: 10px;
            vertical-align: middle;
            background-color: #fff;
            text-align: center;
        }

        .table-items tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table-items .item-number-cell {
            font-weight: 600;
            color: #2c3e50;
            background-color: #ecf0f1 !important;
        }

        .table-items .category-cell {
            background: #e8f5e9 !important;
            color: #2c3e50;
            font-weight: 600;
        }

        .btn-remove-row,
        .btn-edit-row {
            padding: 0.4rem 0.6rem;
            font-size: 0.875rem;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .btn-edit-row {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .btn-edit-row:hover {
            background-color: #2563eb;
            border-color: #2563eb;
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
        }

        .btn-remove-row:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
        }

        .table-footer {
            background: #2c3e50;
            color: white;
            font-weight: 700;
        }

        .table-footer td {
            padding: 15px 10px;
            font-size: 0.95rem;
        }

        .currency-group-footer {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .currency-label-footer {
            background-color: #2c3e50;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.6rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 6px;
            min-width: 55px;
            text-align: center;
            font-weight: 600;
        }

        .value-footer {
            flex: 1;
            color: rgb(0, 0, 0);
            font-weight: 700;
            font-size: 0.95rem;
            padding-left: 10px;
        }

        .no-items-row td {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
            background: #f8f9fa !important;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* FINAL SAVE BUTTON */
        .final-save-section {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 20px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px 12px 0 0;
            margin-top: 30px;
            z-index: 100;
        }

        .btn-final-save {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            transition: all 0.3s;
        }

        .btn-final-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
            color: white;
        }

        .btn-final-save i {
            margin-right: 10px;
        }

        .btn-final-back {
            background: linear-gradient(135deg, #868686 0%, #5e5e5e 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 6px 20px rgba(27, 27, 27, 0.4);
            transition: all 0.3s;
        }

        .btn-final-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(49, 49, 49, 0.5);
            color: white;
        }

        .btn-final-back i {
            margin-right: 10px;
        }
    </style>
@endpush

@section('content')
    <!-- FLOATING BADGE ALERT -->
    <div id="floatingBadgeAlert" class="floating-badge-alert">
        <i class="fas fa-circle-notch fa-spin" id="alertIcon"></i>
        <div class="alert-text" id="alertText">Processing...</div>
    </div>

    <div class="container-fluid joContractEditPage">
        <div class="row">
            <div class="col-lg-12">
                <!-- Page Header Card -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit JO Contract</span>
                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{ route('jo-contract.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Hidden State -->
                <input type="hidden" id="current_jo_contract_id" value="{{ $joContract->id_jo_cont }}">

                <form id="joContractForm">
                    @csrf

                    <!-- JO Contract Info Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <h6 class="mb-0"><i class="fas fa-file-contract me-2"></i>JO Contract Information</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label required-field">Contract</label>

                                    <!-- Search Type Selection -->
                                    <div class="mb-2">
                                        <div class="btn-group" role="group">
                                            <input type="radio" class="btn-check" name="search_type" id="search_nomor"
                                                value="nomor" checked>
                                            <label class="btn btn-outline-primary btn-sm" for="search_nomor">
                                                <i class="fas fa-hashtag me-1"></i>Nomor
                                            </label>

                                            <input type="radio" class="btn-check" name="search_type" id="search_name"
                                                value="name">
                                            <label class="btn btn-outline-primary btn-sm" for="search_name">
                                                <i class="fas fa-file-contract me-1"></i>Name
                                            </label>

                                            <input type="radio" class="btn-check" name="search_type" id="search_customer"
                                                value="customer">
                                            <label class="btn btn-outline-primary btn-sm" for="search_customer">
                                                <i class="fas fa-user me-1"></i>Customer
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Search Input -->
                                    <div class="input-group mb-2">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" id="contract_search" class="form-control"
                                            placeholder="Search contract by number..." autocomplete="off"
                                            value="{{ $joContract->contract->no_contract }} - {{ $joContract->contract->contract }}">
                                        <button class="btn btn-outline-secondary" type="button" id="clear_search">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <!-- Search Results Dropdown -->
                                    <!-- Search Results Dropdown -->
                                    <div style="position: relative;">
                                        <div id="search_results" class="list-group"
                                            style="display: none; max-height: 250px; overflow-y: auto; position: absolute; top: 0; left: 0; right: 0; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 0 0 6px 6px;">
                                        </div>
                                    </div>

                                    <!-- Hidden Select for Form Submission -->
                                    <select name="id_md_cont" id="id_md_cont" class="form-select d-none" required>
                                        <option value="{{ $joContract->id_md_cont }}" selected>
                                            {{ $joContract->contract->no_contract }}
                                        </option>
                                    </select>

                                    @error('id_md_cont')
                                        <div class="invalid-feedback d-block"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror

                                    <!-- Contract Preview Card -->
                                    <div id="contract_preview" class="card mt-3" style="display: block;">
                                        <div class="card-header bg-light">
                                            <strong><i class="fas fa-file-contract me-2"></i>Selected Contract</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table table-sm table-borderless">
                                                        <tr>
                                                            <td width="40%"><strong>No. Contract</strong></td>
                                                            <td width="5%">:</td>
                                                            <td id="preview_no_contract">
                                                                {{ $joContract->contract->no_contract }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Contract</strong></td>
                                                            <td>:</td>
                                                            <td id="preview_contract_name">
                                                                {{ $joContract->contract->contract }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Customer</strong></td>
                                                            <td>:</td>
                                                            <td id="preview_customer">
                                                                {{ $joContract->contract->customer->customer ?? '-' }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table table-sm table-borderless">
                                                        <tr>
                                                            <td width="40%"><strong>Expenditure</strong></td>
                                                            <td width="5%">:</td>
                                                            <td id="preview_expenditure">
                                                                {{ $joContract->contract->expenditure ? 'Rp ' . number_format($joContract->contract->expenditure, 0, ',', '.') : '-' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Start Date</strong></td>
                                                            <td>:</td>
                                                            <td id="preview_start_date">
                                                                {{ $joContract->contract->date_start ? \Carbon\Carbon::parse($joContract->contract->date_start)->format('d M Y') : '-' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>End Date</strong></td>
                                                            <td>:</td>
                                                            <td id="preview_end_date">
                                                                {{ $joContract->contract->date_end ? \Carbon\Carbon::parse($joContract->contract->date_end)->format('d M Y') : '-' }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            @if ($joContract->contract->note)
                                                <div class="row" id="preview_note_section">
                                                    <div class="col-12">
                                                        <hr>
                                                        <strong>Note:</strong>
                                                        <p class="mb-0 text-muted" id="preview_note">
                                                            {{ $joContract->contract->note }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label required-field">Area</label>
                                    <select name="id_md_area" id="id_md_area" class="form-select">
                                        <option value="">Select Area</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id_md_area }}"
                                                {{ $joContract->id_md_area == $area->id_md_area ? 'selected' : '' }}>
                                                {{ $area->area }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required-field">Title</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ $joContract->title }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea name="note" id="note" class="form-control" rows="3">{{ $joContract->note }}</textarea>
                            </div>

                            <!-- Save Header Button -->
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-success" id="btnSaveHeader">
                                    <i class="fas fa-save me-1"></i> Update
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- JO Contract Items Card -->
                    <div class="card shadow mb-4" id="itemsCard">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>JO Contract Items</h6>
                                <!-- KURS SECTION -->
                                <div class="kurs-section d-flex gap-3 align-items-center mb-0"
                                    style="padding: 10px 15px;">
                                    <div>
                                        <label class="form-label mb-1">Kurs Date</label>
                                        <input type="datetime-local" name="global_tgl_kurs_usd" id="global_tgl_kurs_usd"
                                            class="form-control form-control-sm" style="min-width: 200px;">
                                    </div>
                                    <div>
                                        <label class="form-label mb-1">Kurs Rate</label>
                                        <input type="text" id="global_kurs_usd_display"
                                            class="form-control form-control-sm" style="min-width: 130px;">
                                        <input type="hidden" name="global_kurs_usd" id="global_kurs_usd"
                                            value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <!-- ADD ITEM FORM SECTION -->
                            <div class="add-item-form-section" id="addItemFormSection">
                                <h6 id="formSectionTitle">
                                    <i class="fas fa-plus-square"></i> Add New Item
                                </h6>

                                <!-- Hidden field for edit mode -->
                                <input type="hidden" id="editing_item_id" value="">

                                <div class="row">
                                    <!-- Category Selection -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label required-field">Category</label>
                                        <select id="input_category" class="form-select">
                                            <option value="">Select Category</option>
                                            @foreach ($invoices->groupBy('invoice_ctg') as $category => $invoiceGroup)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Item Selection -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label required-field">Item</label>
                                        <select id="input_item" class="form-select" disabled>
                                            <option value="">Select category first</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Note (Optional)</label>
                                        <textarea id="input_note" class="form-control" rows="5" placeholder="Add notes for this item..."></textarea>
                                    </div>

                                    <!-- Pendapatan IDR -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Pendapatan IDR</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_pendapatan_idr"
                                                class="form-control currency-input">
                                        </div>
                                    </div>

                                    <!-- Pendapatan USD -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Pendapatan USD</label>
                                        <div class="currency-group">
                                            <span class="currency-label">USD</span>
                                            <input type="text" id="input_pendapatan_usd"
                                                class="form-control currency-input">
                                        </div>
                                    </div>

                                    <!-- HPP -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">HPP (Biaya Ops)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_hpp" class="form-control currency-input">
                                        </div>
                                    </div>

                                    <!-- Harga Jual (Auto Calculate) -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Harga Jual (IDR)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_harga_jual"
                                                class="form-control currency-input" readonly>
                                        </div>
                                    </div>

                                    <!-- Add Button -->
                                    <div class="col-md-3 mb-3 d-flex align-items-end gap-2 ms-auto">
                                        <button type="button" class="btn btn-add-to-table flex-grow-1"
                                            id="btnAddToTable">
                                            <i class="fas fa-arrow-down"></i> Add to Table
                                        </button>
                                        <button type="button" class="btn btn-edit-item" id="btnCancelEdit"
                                            style="display: none;">
                                            <i class="fas fa-times"></i> Cancel Edit
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- ACTION BUTTONS FOR TABLE -->

                            <!-- TABLE DISPLAY -->
                            <div class="table-responsive">
                                <table class="table table-items table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 13%;">Category</th>
                                            <th style="width: 13%;">Item</th>
                                            <th style="width: 12%;">Pendapatan IDR</th>
                                            <th style="width: 12%;">Pendapatan USD</th>
                                            <th style="width: 12%;">HPP (Biaya Ops)</th>
                                            <th style="width: 12%;">Harga Jual (IDR)</th>
                                            <th style="width: 11%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        @if ($joContract->items->count() > 0)
                                            @php
                                                $groupedItems = $joContract->items->groupBy(function ($item) {
                                                    return $item->invoice->invoice_ctg;
                                                });
                                                $globalIndex = 1;
                                            @endphp

                                            @foreach ($groupedItems as $category => $items)
                                                @foreach ($items as $index => $item)
                                                    <tr class="item-row" data-item-id="{{ $item->id_jo_cont_item }}"
                                                        data-invoice-id="{{ $item->id_md_invoice }}"
                                                        data-category="{{ $item->invoice->invoice_ctg }}"
                                                        data-item-text="{{ $item->invoice->invoice_typ }}"
                                                        data-item-number="{{ $globalIndex }}">

                                                        <td class="item-number-cell">{{ $globalIndex }}</td>

                                                        @if ($index === 0)
                                                            <!-- Category cell with rowspan -->
                                                            <td class="category-cell" rowspan="{{ $items->count() }}">
                                                                {{ $category }}
                                                            </td>
                                                        @endif

                                                        <td class="item-text-cell">{{ $item->invoice->invoice_typ }}</td>
                                                        <td>{{ number_format($item->pendapatan_idr, 2, ',', '.') }}</td>
                                                        <td>{{ number_format($item->pendapatan_usd, 2, ',', '.') }}</td>
                                                        <td>{{ number_format($item->hpp_ops, 2, ',', '.') }}</td>
                                                        <td>{{ number_format($item->hargajual_idr, 2, ',', '.') }}</td>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-primary btn-sm btn-edit-row"
                                                                onclick="editItem('{{ $item->id_jo_cont_item }}')">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm btn-remove-row"
                                                                onclick="removeItem(this, '{{ $item->id_jo_cont_item }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @php $globalIndex++; @endphp
                                                @endforeach
                                            @endforeach
                                        @else
                                            <tr class="no-items-row">
                                                <td colspan="8">
                                                    <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                                                    <p class="mb-0 fw-bold">No data available</p>
                                                    <small class="text-muted">Fill the form above and click "Add to
                                                        Table"</small>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot class="table-footer">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>GRAND TOTAL</strong></td>
                                            <td>
                                                <div class="currency-group-footer">
                                                    <span class="currency-label-footer">IDR</span>
                                                    <span class="value-footer" id="footerTotalIDR">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="currency-group-footer">
                                                    <span class="currency-label-footer">USD</span>
                                                    <span class="value-footer" id="footerTotalUSD">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="currency-group-footer">
                                                    <span class="currency-label-footer">IDR</span>
                                                    <span class="value-footer" id="footerTotalHPP">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="currency-group-footer">
                                                    <span class="currency-label-footer">IDR</span>
                                                    <span class="value-footer" id="footerTotalSelling">0,00</span>
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="action-buttons d-flex justify-content-end">
                                <button type="button" class="btn btn-danger" id="resetAllBtn">
                                    <i class="fas fa-trash-alt me-1"></i>Reset All Items
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- FINAL SAVE SECTION -->
                <div class="final-save-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><i class="fas fa-info-circle me-2"></i>Ready to Save?</h6>
                            <small class="text-muted">Click the button to save all changes permanently</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('jo-contract.index') }}" class="btn btn-final-back">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            <button type="button" class="btn btn-final-save" id="btnFinalSave">
                                <i class="fas fa-save"></i> Save All Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        // ========================================
        // GLOBAL VARIABLES
        // ========================================
        const contracts = @json($contracts);
        let selectedContract = @json($joContract->contract);
        let currentJoContractId = {{ $joContract->id_jo_cont }};
        let globalItemNumber = {{ $joContract->items->count() }};

        // Invoice data by category
        const invoicesByCategory = {
            @foreach ($invoices->groupBy('invoice_ctg') as $category => $invoiceGroup)
                '{{ $category }}': [
                    @foreach ($invoiceGroup as $invoice)
                        {
                            id: '{{ $invoice->id_md_invoice }}',
                            type: '{{ $invoice->invoice_typ }}',
                            note: {!! json_encode($invoice->note ?? '') !!},
                        },
                    @endforeach
                ],
            @endforeach
        };

        // ========================================
        // RUPIAH FORMATTING
        // ========================================
        function formatRupiah(value) {
            let number = value.replace(/[^\d,]/g, '');
            number = number.replace(/\./g, '');
            if (number === '') return '';

            let parts = number.split(',');
            let integerPart = parts[0];
            let decimalPart = parts.length > 1 ? parts[1] : '';

            if (decimalPart.length > 2) {
                decimalPart = decimalPart.substring(0, 2);
            }

            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            if (parts.length > 1) {
                return integerPart + ',' + decimalPart;
            } else {
                return integerPart + ',00';
            }
        }

        function parseRupiah(value) {
            if (!value) return 0;
            let cleaned = value.toString().replace(/\./g, '').replace(',', '.');
            return parseFloat(cleaned) || 0;
        }

        function setupRupiahInput(input) {
            input.addEventListener('input', function(e) {
                let cursorPosition = this.selectionStart;
                let beforeCursor = this.value.substring(0, cursorPosition);

                let formatted = formatRupiah(this.value);
                this.value = formatted;

                if (!beforeCursor.includes(',')) {
                    let digitsBeforeCursor = beforeCursor.replace(/\D/g, '').length;
                    let newPos = 0;
                    let digitCount = 0;
                    for (let i = 0; i < this.value.length; i++) {
                        if (/\d/.test(this.value[i])) {
                            digitCount++;
                            if (digitCount === digitsBeforeCursor) {
                                newPos = i + 1;
                                break;
                            }
                        }
                    }
                    this.setSelectionRange(newPos, newPos);
                } else {
                    let commaPos = this.value.indexOf(',');
                    let decimalDigitsInput = beforeCursor.split(',')[1] || '';
                    let decimalDigits = decimalDigitsInput.length;
                    let newPos = commaPos + 1 + Math.min(decimalDigits, 2);
                    this.setSelectionRange(newPos, newPos);
                }
            });

            input.addEventListener('blur', function(e) {
                if (e.target.value) {
                    if (!e.target.value.includes(',')) {
                        e.target.value = e.target.value + ',00';
                    } else {
                        let parts = e.target.value.split(',');
                        if (parts[1] !== undefined) {
                            if (parts[1].length === 0) {
                                e.target.value = parts[0] + ',00';
                            } else if (parts[1].length < 2) {
                                e.target.value = parts[0] + ',' + parts[1].padEnd(2, '0');
                            }
                        }
                    }
                } else {
                    e.target.value = '';
                }
            });
        }

        function formatNumber(amount) {
            return amount.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Setup form inputs
        setupRupiahInput(document.getElementById('global_kurs_usd_display'));
        setupRupiahInput(document.getElementById('input_pendapatan_idr'));
        setupRupiahInput(document.getElementById('input_pendapatan_usd'));
        setupRupiahInput(document.getElementById('input_hpp'));

        // ========================================
        // CONTRACT SEARCH
        // ========================================

        // Tampilkan saat fokus saja
        document.getElementById('contract_search').addEventListener('focus', function() {
            const searchType = document.querySelector('input[name="search_type"]:checked').value;
            displaySearchResults(contracts, '', searchType);
        });

        // Sembunyikan saat blur (delay agar klik item sempat terproses)
        document.getElementById('contract_search').addEventListener('blur', function() {
            setTimeout(() => {
                hideSearchResults();
            }, 200);
        });

        // Filter saat mengetik
        document.getElementById('contract_search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const searchType = document.querySelector('input[name="search_type"]:checked').value;

            if (searchTerm.length === 0) {
                const searchType2 = document.querySelector('input[name="search_type"]:checked').value;
                displaySearchResults(contracts, '', searchType2);
                return;
            }

            const filteredContracts = contracts.filter(contract => {
                switch (searchType) {
                    case 'nomor':
                        return contract.no_contract && contract.no_contract.toLowerCase().includes(
                            searchTerm);
                    case 'name':
                        return contract.contract && contract.contract.toLowerCase().includes(searchTerm);
                    case 'customer':
                        return contract.customer && contract.customer.customer &&
                            contract.customer.customer.toLowerCase().includes(searchTerm);
                    default:
                        return false;
                }
            });

            displaySearchResults(filteredContracts, searchTerm, searchType);
        });

        // Ganti filter saat radio berubah
        document.querySelectorAll('input[name="search_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const searchInput = document.getElementById('contract_search');
                const placeholders = {
                    'nomor': 'Search contract by number...',
                    'name': 'Search contract by name...',
                    'customer': 'Search contract by customer name...'
                };
                searchInput.placeholder = placeholders[this.value];
                searchInput.value = '';
                hideSearchResults();
                clearContractPreview();
            });
        });

        document.getElementById('clear_search').addEventListener('click', function() {
            document.getElementById('contract_search').value = '';
            hideSearchResults();
            clearContractPreview();
        });

        function hideSearchResults() {
            document.getElementById('search_results').style.display = 'none';
        }

        function clearContractPreview() {
            document.getElementById('contract_preview').style.display = 'none';
            document.getElementById('id_md_cont').innerHTML = '<option value="">Select Contract</option>';
            selectedContract = null;
        }

        function highlightText(text, term) {
            if (!term || !text) return text || '';
            const escaped = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return text.replace(new RegExp(`(${escaped})`, 'gi'),
                '<mark style="background:#fef08a;padding:0;border-radius:2px;">$1</mark>');
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function selectContract(contractId) {
            selectedContract = contracts.find(c => c.id_md_cont == contractId);
            if (!selectedContract) return;

            const selectElement = document.getElementById('id_md_cont');
            selectElement.innerHTML =
                `<option value="${selectedContract.id_md_cont}" selected>${selectedContract.no_contract}</option>`;

            document.getElementById('contract_search').value = selectedContract.no_contract + ' - ' + selectedContract
                .contract;

            hideSearchResults();
            displayContractPreview(selectedContract);
        }

        function displayContractPreview(contract) {
            document.getElementById('preview_no_contract').textContent = contract.no_contract || '-';
            document.getElementById('preview_contract_name').textContent = contract.contract || '-';
            document.getElementById('preview_customer').textContent = contract.customer ? contract.customer.customer : '-';

            const expenditure = contract.expenditure ?
                new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(contract.expenditure) :
                '-';
            document.getElementById('preview_expenditure').textContent = expenditure;
            document.getElementById('preview_start_date').textContent = formatDate(contract.date_start);
            document.getElementById('preview_end_date').textContent = formatDate(contract.date_end);

            const noteSection = document.getElementById('preview_note_section');
            if (noteSection) {
                if (contract.note) {
                    document.getElementById('preview_note').textContent = contract.note;
                    noteSection.style.display = 'block';
                } else {
                    noteSection.style.display = 'none';
                }
            }

            document.getElementById('contract_preview').style.display = 'block';
        }

        function displaySearchResults(contractsList, searchTerm, searchType) {
            const resultsContainer = document.getElementById('search_results');

            const filterLabels = {
                'nomor': 'Nomor',
                'name': 'Contract',
                'customer': 'Customer'
            };
            const activeLabel = filterLabels[searchType] || 'Contract';

            if (contractsList.length === 0) {
                resultsContainer.innerHTML = `
            <div class="list-group-item text-center text-muted py-4">
                <i class="fas fa-search-minus mb-2 d-block" style="font-size:1.8rem;opacity:0.4;"></i>
                <p class="mb-0 small">No contracts found</p>
            </div>`;
                resultsContainer.style.display = 'block';
                return;
            }

            const headerHtml = `
        <div class="d-flex justify-content-between px-3 py-1"
             style="font-size:11px; color:#6c757d; background:#f8f9fa; border:1px solid #dee2e6; border-bottom:none; border-radius:6px 6px 0 0;">
            <small><i class="fas fa-filter me-1"></i>By ${activeLabel}</small>
            <small>${contractsList.length} result${contractsList.length > 1 ? 's' : ''}</small>
        </div>`;

            const itemsHtml = contractsList.map((contract, index) => {
                const isLast = index === contractsList.length - 1;
                const customerName = contract.customer ? contract.customer.customer : '-';
                const contractName = contract.contract || '-';
                const noContract = contract.no_contract || '-';

                const expenditure = contract.expenditure ?
                    new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(contract.expenditure) :
                    '-';

                const periode = (contract.date_start && contract.date_end) ?
                    `${formatDate(contract.date_start)} – ${formatDate(contract.date_end)}` :
                    '-';

                const noContractHtml = searchType === 'nomor' ? highlightText(noContract, searchTerm) : noContract;
                const namaContractHtml = searchType === 'name' ? highlightText(contractName, searchTerm) :
                    contractName;
                const customerHtml = searchType === 'customer' ? highlightText(customerName, searchTerm) :
                    customerName;

                const borderRadius = isLast ? 'border-radius:0 0 6px 6px;' : '';

                return `
            <a href="#" class="list-group-item list-group-item-action contract-search-item px-3 py-2"
               data-contract-id="${contract.id_md_cont}"
               style="display:flex; align-items:center; gap:0; ${borderRadius}">
                <span style="font-size:13px; white-space:nowrap; width:130px;">${noContractHtml}</span>
                <span style="color:#adb5bd; padding:0 8px; user-select:none;">|</span>
                <span style="font-size:13px; white-space:nowrap; width:200px; overflow:hidden; text-overflow:ellipsis;">${namaContractHtml}</span>
                <span style="color:#adb5bd; padding:0 8px; user-select:none;">|</span>
                <span style="font-size:13px; color:#6c757d; white-space:nowrap; width:160px; overflow:hidden; text-overflow:ellipsis;">${customerHtml}</span>
                <span style="color:#adb5bd; padding:0 8px; user-select:none;">|</span>
                <span style="font-size:12px; color:#6c757d; white-space:nowrap; width:160px;">${periode}</span>
                <span style="font-size:13px; white-space:nowrap; text-align:right; margin-left:auto; padding-left:16px; color:#2c3e50;">${expenditure}</span>
            </a>`;
            }).join('');

            resultsContainer.innerHTML = headerHtml + itemsHtml;
            resultsContainer.style.display = 'block';

            resultsContainer.querySelectorAll('.contract-search-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectContract(this.getAttribute('data-contract-id'));
                });
            });
        }

        // ========================================
        // FLOATING BADGE ALERT FUNCTIONS
        // ========================================
        function showFloatingAlert(type, message) {
            const alert = $('#floatingBadgeAlert');
            const icon = $('#alertIcon');
            const text = $('#alertText');

            alert.removeClass('alert-saving alert-success alert-error hiding');

            switch (type) {
                case 'saving':
                    alert.addClass('alert-saving');
                    icon.attr('class', 'fas fa-circle-notch fa-spin');
                    break;
                case 'success':
                    alert.addClass('alert-success');
                    icon.attr('class', 'fas fa-check-circle');
                    break;
                case 'error':
                    alert.addClass('alert-error');
                    icon.attr('class', 'fas fa-exclamation-circle');
                    break;
            }

            text.text(message);
            alert.addClass('show');

            if (type === 'success' || type === 'error') {
                setTimeout(() => {
                    hideFloatingAlert();
                }, 3000);
            }
        }

        function hideFloatingAlert() {
            const alert = $('#floatingBadgeAlert');
            alert.addClass('hiding');
            setTimeout(() => {
                alert.removeClass('show hiding');
            }, 400);
        }

        // ========================================
        // AUTO-CALCULATE HARGA JUAL (FIXED FORMULA)
        // ========================================
        function calculateHargaJual() {
            const kursRate = parseRupiah($('#global_kurs_usd_display').val());
            const idrValue = parseRupiah($('#input_pendapatan_idr').val());
            const usdValue = parseRupiah($('#input_pendapatan_usd').val());

            let hargaJual = 0;
            if (idrValue > 0) {
                hargaJual = idrValue;
            } else if (usdValue > 0 && kursRate > 0) {
                hargaJual = usdValue * kursRate;
            }

            $('#input_harga_jual').val(formatRupiah(hargaJual.toFixed(2).replace('.', ',')));
        }

        $('#input_pendapatan_idr').on('input', function() {
            if (parseRupiah($(this).val()) > 0) {
                $('#input_pendapatan_usd').val('');
            }
            calculateHargaJual();
        });

        $('#input_pendapatan_usd').on('input', function() {
            if (parseRupiah($(this).val()) > 0) {
                $('#input_pendapatan_idr').val('');
            }
            calculateHargaJual();
        });

        $('#input_hpp').on('input', function() {
            calculateHargaJual();
        });

        $('#global_kurs_usd_display').on('input', function() {
            $('#global_kurs_usd').val(parseRupiah($(this).val()));
            calculateHargaJual();
        });

        // ========================================
        // CATEGORY DROPDOWN CHANGE
        // ========================================
        $('#input_category').on('change', function() {
            const category = $(this).val();
            const itemSelect = $('#input_item');

            if (!category) {
                itemSelect.prop('disabled', true).html('<option value="">Select category first</option>');
                return;
            }

            const invoices = invoicesByCategory[category] || [];
            let options = '<option value="">Select Item</option>';

            invoices.forEach(invoice => {
                options +=
                    `<option value="${invoice.id}" data-note="${invoice.note}">${invoice.type}</option>`;
            });

            itemSelect.prop('disabled', false).html(options);
        });

        // Tambahkan event baru ini SETELAH event #input_category di atas
        $('#input_item').on('change', function() {
            const editingItemId = $('#editing_item_id').val();
            if (editingItemId) return; // Saat mode edit, jangan overwrite note dari DB

            const masterNote = $(this).find('option:selected').data('note') || '';
            $('#input_note').val(masterNote);
        });

        // ========================================
        // UPDATE HEADER
        // ========================================
        $('#btnSaveHeader').on('click', function() {
            const contractId = $('#id_md_cont').val();
            const areaId = $('#id_md_area').val();
            const title = $('#title').val();
            const note = $('#note').val();
            const kursDate = $('#global_tgl_kurs_usd').val();
            const kursRate = parseRupiah($('#global_kurs_usd_display').val());

            if (!contractId || !areaId || !title || !kursDate || !kursRate) {
                showFloatingAlert('error', 'Please fill all required fields');
                return;
            }

            const formData = {
                id_md_cont: contractId,
                id_md_area: areaId,
                title: title,
                note: note,
                global_tgl_kurs_usd: kursDate,
                global_kurs_usd: kursRate,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            showFloatingAlert('saving', 'Updating header and syncing kurs...');

            $.ajax({
                url: `/jo-contract/save-all/${currentJoContractId}`,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showFloatingAlert('success', 'Header updated and kurs synced to all items!');

                        // Reload page untuk refresh table dengan harga baru
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Failed to update header';
                    showFloatingAlert('error', message);
                }
            });
        });

        // ========================================
        // ADD/UPDATE ITEM TO TABLE (REALTIME SAVE)
        // ========================================
        $('#btnAddToTable').on('click', function() {
            const editingItemId = $('#editing_item_id').val();
            const category = $('#input_category').val();
            const itemId = $('#input_item').val();
            const itemText = $('#input_item option:selected').text();
            const pendapatanIDR = parseRupiah($('#input_pendapatan_idr').val());
            const pendapatanUSD = parseRupiah($('#input_pendapatan_usd').val());
            const hpp = parseRupiah($('#input_hpp').val());
            const hargaJual = parseRupiah($('#input_harga_jual').val());
            const kursRate = parseRupiah($('#global_kurs_usd_display').val()) || 0;

            // FIX: Ambil datetime dengan benar
            const kursDate = $('#global_tgl_kurs_usd').val() || null; // Format: 2026-05-26T14:30
            const note = $('#input_note').val();

            if (!category || !itemId) {
                showFloatingAlert('error', 'Please select category and item');
                return;
            }

            // Validate kurs if USD is filled
            if (pendapatanUSD > 0) {
                if (!kursDate || kursRate <= 0) {
                    showFloatingAlert('error', 'Kurs rate and date are required when USD is filled');
                    return;
                }
            }

            // If editing, call update function
            if (editingItemId) {
                updateItemToDatabase(editingItemId, category, itemId, itemText, pendapatanIDR, pendapatanUSD, hpp,
                    hargaJual, kursRate, kursDate, note);
                return;
            }

            // Otherwise, add new item
            showFloatingAlert('saving', 'Adding item...');

            $.ajax({
                url: '/jo-contract/item/store',
                method: 'POST',
                data: {
                    id_jo_cont: currentJoContractId,
                    id_md_invoice: itemId,
                    invoice_ctg: category,
                    pendapatan_idr: pendapatanIDR,
                    pendapatan_usd: pendapatanUSD,
                    hpp_ops: hpp,
                    kurs_usd: kursRate,
                    tgl_kurs_usd: kursDate,
                    note: note,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('=== ADD ITEM RESPONSE ===', response);

                    if (response.success) {
                        showFloatingAlert('success', 'Item added successfully!');

                        $('.no-items-row').remove();

                        const newItemId = response.data.id_jo_cont_item || response.data.id;

                        if (!newItemId || newItemId === 0 || newItemId === '0') {
                            console.error('CRITICAL ERROR: Invalid Item ID from server!');
                            showFloatingAlert('error',
                                'Item created but ID is invalid. Please refresh the page.');
                            return;
                        }

                        // Insert row dengan category grouping
                        insertRowWithCategoryGrouping(newItemId, category, itemText, response.data);

                        updateGrandTotal();
                        clearItemForm();

                        console.log('=== ITEM ADDED TO TABLE SUCCESSFULLY ===');
                    } else {
                        showFloatingAlert('error', 'Failed to add item');
                    }
                },
                error: function(xhr) {
                    console.error('=== ADD ITEM ERROR ===', xhr);
                    const message = xhr.responseJSON?.message || 'Failed to add item';
                    showFloatingAlert('error', message);
                }
            });
        });

        // ========================================
        // INSERT ROW WITH CATEGORY GROUPING
        // ========================================
        function insertRowWithCategoryGrouping(itemId, category, itemText, data) {
            globalItemNumber++;

            // Cari apakah category sudah ada
            let categoryExists = false;
            let insertAfterRow = null;
            let categoryRowspan = 0;

            $('#itemsTableBody tr.item-row').each(function() {
                const rowCategory = $(this).data('category');

                if (rowCategory === category) {
                    categoryExists = true;
                    insertAfterRow = $(this);

                    // Update rowspan jika ini first row dari category
                    const categoryCell = $(this).find('.category-cell');
                    if (categoryCell.length > 0) {
                        categoryRowspan = parseInt(categoryCell.attr('rowspan') || 1);
                        categoryCell.attr('rowspan', categoryRowspan + 1);
                    }
                }
            });

            let newRow;

            if (categoryExists) {
                // Category sudah ada - insert tanpa category cell
                newRow = `
            <tr class="item-row" data-item-id="${itemId}" data-category="${category}">
                <td class="item-number-cell">${globalItemNumber}</td>
                <td class="item-text-cell">${itemText}</td>
                <td>${formatNumber(data.pendapatan_idr)}</td>
                <td>${formatNumber(data.pendapatan_usd)}</td>
                <td>${formatNumber(data.hpp_ops)}</td>
                <td>${formatNumber(data.hargajual_idr)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-primary btn-sm btn-edit-row"
                        onclick="editItem('${itemId}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                        onclick="removeItem(this, '${itemId}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

                // Insert setelah row terakhir dari category yang sama
                insertAfterRow.after(newRow);
            } else {
                // Category baru - insert dengan category cell
                newRow = `
            <tr class="item-row" data-item-id="${itemId}" data-category="${category}">
                <td class="item-number-cell">${globalItemNumber}</td>
                <td class="category-cell" rowspan="1">${category}</td>
                <td class="item-text-cell">${itemText}</td>
                <td>${formatNumber(data.pendapatan_idr)}</td>
                <td>${formatNumber(data.pendapatan_usd)}</td>
                <td>${formatNumber(data.hpp_ops)}</td>
                <td>${formatNumber(data.hargajual_idr)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-primary btn-sm btn-edit-row"
                        onclick="editItem('${itemId}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                        onclick="removeItem(this, '${itemId}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

                $('#itemsTableBody').append(newRow);
            }

            renumberAllItems();
        }


        // ========================================
        // EDIT ITEM - LOAD TO FORM (FIXED)
        // ========================================
        function editItem(itemId) {
            console.log('=== EDIT ITEM START ===');
            console.log('Item ID:', itemId);
            console.log('Item ID type:', typeof itemId);
            console.log('Item ID is valid:', itemId && itemId != 0);

            if (!itemId || itemId == 0) {
                console.error('INVALID ITEM ID:', itemId);
                showFloatingAlert('error', 'Invalid item ID');
                return;
            }

            showFloatingAlert('saving', 'Loading item data...');

            const url = `/jo-contract/item/show/${itemId}`;
            console.log('Fetching URL:', url);

            // Fetch fresh data from server
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    console.log('SUCCESS Response:', response);

                    if (response.success) {
                        hideFloatingAlert();
                        const item = response.data;
                        console.log('Item data from server:', item);

                        // Set form to edit mode
                        $('#editing_item_id').val(itemId);
                        $('#formSectionTitle').html('<i class="fas fa-edit"></i> Edit Item');
                        $('#addItemFormSection').addClass('edit-mode');
                        $('#btnAddToTable').html('<i class="fas fa-save"></i> Update Item');
                        $('#btnCancelEdit').show();

                        // Set category
                        $('#input_category').val(item.invoice_ctg).trigger('change');

                        // Wait for items dropdown to populate
                        setTimeout(() => {
                            $('#input_item').val(item.id_md_invoice);

                            // Set monetary values
                            const pendapatanIDR = parseFloat(item.pendapatan_idr) || 0;
                            const pendapatanUSD = parseFloat(item.pendapatan_usd) || 0;
                            const hpp = parseFloat(item.hpp_ops) || 0;
                            const kursUSD = parseFloat(item.kurs_usd) || 0;

                            $('#input_pendapatan_idr').val(pendapatanIDR > 0 ? formatRupiah(
                                pendapatanIDR.toFixed(2).replace('.', ',')) : '');
                            $('#input_pendapatan_usd').val(pendapatanUSD > 0 ? formatRupiah(
                                pendapatanUSD.toFixed(2).replace('.', ',')) : '');
                            $('#input_hpp').val(formatRupiah(hpp.toFixed(2).replace('.', ',')));

                            // Set note
                            $('#input_note').val(item.note || '');

                            // Set kurs if USD is filled
                            if (pendapatanUSD > 0 && kursUSD > 0) {
                                $('#global_kurs_usd_display').val(formatRupiah(kursUSD.toFixed(2)
                                    .replace('.', ',')));
                                $('#global_kurs_usd').val(kursUSD);
                            }

                            // FIX: Set datetime dengan benar
                            if (item.tgl_kurs_usd) {
                                // Format dari server: "2026-05-26T14:30:00.000000Z" atau "2026-05-26T14:30"
                                // Format input datetime-local: "2026-05-26T14:30"
                                let datetimeValue = item.tgl_kurs_usd;

                                // Jika ada detik atau milidetik, buang
                                if (datetimeValue.includes(':')) {
                                    datetimeValue = datetimeValue.substring(0,
                                        16); // Ambil YYYY-MM-DDTHH:mm saja
                                }

                                $('#global_tgl_kurs_usd').val(datetimeValue);

                                console.log('Set datetime value:', datetimeValue);
                            }

                            // Recalculate harga jual
                            calculateHargaJual();

                            console.log('=== FORM POPULATED ===');
                        }, 300);

                        // Scroll to form
                        $('html, body').animate({
                            scrollTop: $('#addItemFormSection').offset().top - 100
                        }, 500);
                    } else {
                        console.error('Response success is false');
                        showFloatingAlert('error', 'Failed to load item data');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX ERROR:', {
                        status: status,
                        error: error,
                        xhr: xhr,
                        responseText: xhr.responseText
                    });

                    const message = xhr.responseJSON?.message || 'Failed to load item data';
                    showFloatingAlert('error', message);
                }
            });
        }

        // Cancel edit mode
        $('#btnCancelEdit').on('click', function() {
            clearItemForm();
            $('html, body').animate({
                scrollTop: $('#itemsTableBody').offset().top - 200
            }, 500);
        });

        // Update item to database (FIXED - USING PUT)
        // Update item to database (FIXED - USING PUT)
        function updateItemToDatabase(itemId, category, invoiceId, itemText, pendapatanIDR, pendapatanUSD, hpp, hargaJual,
            kursRate, kursDate, note) {

            console.log('=== UPDATE ITEM TO DATABASE ===');
            console.log('Item ID:', itemId);
            console.log('Category:', category);
            console.log('Invoice ID:', invoiceId);
            console.log('Kurs Date:', kursDate);
            console.log('Kurs Rate:', kursRate);
            console.log('Note:', note);

            showFloatingAlert('saving', 'Updating item...');

            $.ajax({
                url: `/jo-contract/item/update/${itemId}`,
                method: 'PUT',
                data: {
                    id_jo_cont: currentJoContractId, // ← TAMBAHKAN INI (REQUIRED)
                    id_md_invoice: invoiceId,
                    invoice_ctg: category,
                    pendapatan_idr: pendapatanIDR,
                    pendapatan_usd: pendapatanUSD,
                    hpp_ops: hpp,
                    kurs_usd: kursRate,
                    tgl_kurs_usd: kursDate,
                    note: note,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showFloatingAlert('success', 'Item updated successfully!');

                        const row = $(`.item-row[data-item-id="${itemId}"]`);
                        const oldCategory = row.data('category');

                        // Jika category berubah, perlu reorganisasi
                        if (oldCategory !== category) {
                            // Hapus row lama (tanpa AJAX delete)
                            removeRowWithoutDelete(row, oldCategory);

                            // Insert row baru dengan category baru
                            insertRowWithCategoryGrouping(itemId, category, itemText, response.data);
                        } else {
                            // Category sama, update in-place
                            row.attr('data-invoice-id', invoiceId);
                            row.attr('data-item-text', itemText);

                            row.find('.item-text-cell').text(itemText);

                            // Cek apakah row punya category cell atau tidak
                            const hasCategoryCell = row.find('.category-cell').length > 0;
                            const offset = hasCategoryCell ? 0 : -1; // Offset jika tidak ada category cell

                            row.find('td').eq(3 + offset).text(formatNumber(response.data.pendapatan_idr));
                            row.find('td').eq(4 + offset).text(formatNumber(response.data.pendapatan_usd));
                            row.find('td').eq(5 + offset).text(formatNumber(response.data.hpp_ops));
                            row.find('td').eq(6 + offset).text(formatNumber(response.data.hargajual_idr));
                        }

                        updateGrandTotal();
                        clearItemForm();

                        $('html, body').animate({
                            scrollTop: $(`.item-row[data-item-id="${itemId}"]`).offset().top - 200
                        }, 500);
                    }
                },
                error: function(xhr) {
                    console.error('=== UPDATE ITEM ERROR ===', xhr);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Status:', xhr.status);

                    // Parse error message
                    let errorMessage = 'Failed to update item';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON.errors) {
                            console.error('Validation Errors:', xhr.responseJSON.errors);
                            // Tampilkan validation errors
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join(', ');
                        }
                    }

                    showFloatingAlert('error', errorMessage);
                }
            });
        }

        // Clear item form
        function clearItemForm() {
            $('#editing_item_id').val('');
            $('#formSectionTitle').html('<i class="fas fa-plus-square"></i> Add New Item');
            $('#addItemFormSection').removeClass('edit-mode');
            $('#btnAddToTable').html('<i class="fas fa-arrow-down"></i> Add to Table');
            $('#btnCancelEdit').hide();

            $('#input_category').val('');
            $('#input_item').prop('disabled', true).html('<option value="">Select category first</option>');
            $('#input_pendapatan_idr').val('');
            $('#input_pendapatan_usd').val('');
            $('#input_hpp').val('');
            $('#input_harga_jual').val('');
            $('#input_note').val('');
        }

        // ========================================
        // REMOVE ITEM (REALTIME DELETE) - FIXED
        // ========================================
        function removeItem(button, itemId) {
            console.log('=== DELETE ITEM START ===', itemId);

            if (!itemId || itemId == 0) {
                console.error('Invalid item ID:', itemId);
                showFloatingAlert('error', 'Invalid item ID');
                return;
            }

            if (!confirm('Are you sure you want to delete this item?')) {
                return;
            }

            showFloatingAlert('saving', 'Deleting item...');

            $.ajax({
                url: `/jo-contract/item/destroy/${itemId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showFloatingAlert('success', 'Item deleted successfully!');

                        const row = $(button).closest('tr');
                        const category = row.data('category');

                        // Cek apakah row ini punya category cell
                        const categoryCell = row.find('.category-cell');

                        if (categoryCell.length > 0) {
                            // Row ini punya category cell
                            const rowspan = parseInt(categoryCell.attr('rowspan') || 1);

                            if (rowspan > 1) {
                                // Ada row lain dengan category yang sama
                                // Pindahkan category cell ke row berikutnya
                                const nextRow = row.next('.item-row[data-category="' + category + '"]');
                                if (nextRow.length > 0) {
                                    const newCategoryCell =
                                        `<td class="category-cell" rowspan="${rowspan - 1}">${category}</td>`;
                                    nextRow.find('.item-text-cell').before(newCategoryCell);
                                }
                            }
                        } else {
                            // Row ini tidak punya category cell, kurangi rowspan dari category cell sebelumnya
                            const prevRow = row.prevAll('.item-row[data-category="' + category + '"]').first();
                            if (prevRow.length > 0) {
                                const prevCategoryCell = prevRow.find('.category-cell');
                                if (prevCategoryCell.length > 0) {
                                    const currentRowspan = parseInt(prevCategoryCell.attr('rowspan') || 1);
                                    if (currentRowspan > 1) {
                                        prevCategoryCell.attr('rowspan', currentRowspan - 1);
                                    }
                                }
                            }
                        }

                        // Hapus row
                        row.remove();

                        renumberAllItems();
                        updateGrandTotal();

                        if ($('#itemsTableBody tr.item-row').length === 0) {
                            $('#itemsTableBody').html(`
                        <tr class="no-items-row">
                            <td colspan="8">
                                <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                                <p class="mb-0 fw-bold">No data available</p>
                                <small class="text-muted">Fill the form above and click "Add to Table"</small>
                            </td>
                        </tr>
                    `);
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Delete failed:', xhr);
                    const message = xhr.responseJSON?.message || 'Failed to delete item';
                    showFloatingAlert('error', message);
                }
            });
        }

        // ========================================
        // RESET ALL ITEMS
        // ========================================
        $('#resetAllBtn').on('click', function() {
            if (!confirm(
                    'Are you sure you want to remove all items? This will delete all items from the database.')) {
                return;
            }

            const itemIds = [];
            $('.item-row').each(function() {
                const itemId = $(this).data('item-id');
                if (itemId && itemId != 0) {
                    itemIds.push(itemId);
                }
            });

            if (itemIds.length === 0) {
                showFloatingAlert('error', 'No items to delete');
                return;
            }

            showFloatingAlert('saving', 'Deleting all items...');

            let deletePromises = itemIds.map(itemId => {
                return $.ajax({
                    url: `/jo-contract/item/destroy/${itemId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });

            Promise.all(deletePromises).then(() => {
                showFloatingAlert('success', 'All items deleted successfully!');

                $('#itemsTableBody').html(`
                    <tr class="no-items-row">
                        <td colspan="8">
                            <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                            <p class="mb-0 fw-bold">No data available</p>
                            <small class="text-muted">Fill the form above and click "Add to Table"</small>
                        </td>
                    </tr>
                `);

                globalItemNumber = 0;
                updateGrandTotal();
            }).catch(error => {
                showFloatingAlert('error', 'Some items could not be deleted');
                console.error('Bulk delete error:', error);
            });
        });

        // ========================================
        // FINAL SAVE
        // ========================================
        // ========================================
        // FINAL SAVE ALL CHANGES
        // ========================================
        $('#btnFinalSave').on('click', function() {
            const contractId = $('#id_md_cont').val();
            const areaId = $('#id_md_area').val();
            const title = $('#title').val();
            const note = $('#note').val();
            const kursDate = $('#global_tgl_kurs_usd').val();
            const kursRate = parseRupiah($('#global_kurs_usd_display').val());

            // Validation
            if (!contractId || !areaId || !title) {
                showFloatingAlert('error', 'Please fill all required fields (Contract, Area, Title)');
                return;
            }

            // if (!kursDate || !kursRate || kursRate <= 0) {
            //     showFloatingAlert('error', 'Please fill Kurs Rate and Date');
            //     return;
            // }

            if (!confirm('Save all changes and return to JO Contract list?')) {
                return;
            }

            showFloatingAlert('saving', 'Saving all changes...');

            const formData = {
                id_md_cont: contractId,
                id_md_area: areaId,
                title: title,
                note: note,
                global_tgl_kurs_usd: kursDate,
                global_kurs_usd: kursRate,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            console.log('=== SAVE ALL CHANGES ===', formData);

            $.ajax({
                url: `/jo-contract/save-all/${currentJoContractId}`,
                method: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Save All Response:', response);

                    if (response.success) {
                        showFloatingAlert('success', 'All changes saved successfully!');

                        // Redirect after 1.5 seconds
                        setTimeout(() => {
                            window.location.href = '{{ route('jo-contract.index') }}';
                        }, 1500);
                    } else {
                        showFloatingAlert('error', response.message || 'Failed to save changes');
                    }
                },
                error: function(xhr) {
                    console.error('Save All Error:', xhr);
                    const message = xhr.responseJSON?.message || 'Failed to save all changes';
                    showFloatingAlert('error', message);
                }
            });
        });

        // ========================================
        // UTILITY FUNCTIONS
        // ========================================
        function renumberAllItems() {
            $('.item-row').each(function(index) {
                $(this).find('.item-number-cell').text(index + 1);
                $(this).attr('data-item-number', index + 1);
            });
            globalItemNumber = $('.item-row').length;
        }

        function updateGrandTotal() {
            let totalIDR = 0;
            let totalUSD = 0;
            let totalHPP = 0;
            let totalSelling = 0;

            $('.item-row').each(function() {
                const idr = parseRupiah($(this).find('td:eq(3)').text());
                const usd = parseRupiah($(this).find('td:eq(4)').text());
                const hpp = parseRupiah($(this).find('td:eq(5)').text());
                const selling = parseRupiah($(this).find('td:eq(6)').text());

                totalIDR += idr;
                totalUSD += usd;
                totalHPP += hpp;
                totalSelling += selling;
            });

            $('#footerTotalIDR').text(formatNumber(totalIDR));
            $('#footerTotalUSD').text(formatNumber(totalUSD));
            $('#footerTotalHPP').text(formatNumber(totalHPP));
            $('#footerTotalSelling').text(formatNumber(totalSelling));
        }

        // Initialize grand total on page load
        $(document).ready(function() {
            updateGrandTotal();

            // Set existing kurs values if available
            @if ($joContract->items->first() && $joContract->items->first()->kurs_usd)
                $('#global_kurs_usd_display').val(formatRupiah(
                    '{{ number_format($joContract->items->first()->kurs_usd, 2, ',', '.') }}'));
                $('#global_kurs_usd').val('{{ $joContract->items->first()->kurs_usd }}');
            @endif

            @if ($joContract->items->first() && $joContract->items->first()->tgl_kurs_usd)
                // FIX: Set datetime dengan format yang benar
                $('#global_tgl_kurs_usd').val(
                    '{{ $joContract->items->first()->tgl_kurs_usd->format('Y-m-d\TH:i') }}');
            @endif
        });
    </script>
@endpush
