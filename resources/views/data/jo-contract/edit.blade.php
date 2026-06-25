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

        /* Radio button group — override Bootstrap blue dengan hijau */
        .btn-check:checked+.btn-outline-primary {
            background-color: #059669 !important;
            border-color: #059669 !important;
            color: white !important;
        }

        .btn-outline-primary {
            color: #059669 !important;
            border-color: #059669 !important;
        }

        .btn-outline-primary:hover {
            background-color: #d1fae5 !important;
            border-color: #059669 !important;
            color: #059669 !important;
        }

        .btn-check:focus+.btn-outline-primary {
            box-shadow: 0 0 0 0.2rem rgba(5, 150, 105, 0.25) !important;
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
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">No. JO</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success text-white">
                                                <i class="fas fa-file-alt"></i>
                                            </span>
                                            <input type="text" class="form-control fw-bold"
                                                value="{{ $joContract->no_jo_cont ?? '-' }}" readonly
                                                style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required-field">JO date</label>
                                        <input type="date" name="tgl_jo_cont" id="tgl_jo_cont" class="form-control"
                                            value="{{ $joContract->tgl_jo_cont ? $joContract->tgl_jo_cont->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
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

                                    <!-- Income (IDR) -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Income (IDR)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_pendapatan_idr"
                                                class="form-control currency-input">
                                        </div>
                                    </div>

                                    <!-- Income (USD) -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Income (USD)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">USD</span>
                                            <input type="text" id="input_pendapatan_usd"
                                                class="form-control currency-input">
                                        </div>
                                    </div>


                                    <!-- Selling Price (Auto Calculate) -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Selling Price (IDR)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_harga_jual"
                                                class="form-control currency-input" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">HPP (Ops Costs)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_hpp" class="form-control currency-input">
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
                                            <th style="width: 12%;">Income (IDR)</th>
                                            <th style="width: 12%;">Income (USD)</th>
                                            <th style="width: 12%;">Selling Price (IDR)</th>
                                            <th style="width: 12%;">HPP (Ops Costs)</th>
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
                                                        data-item-number="{{ $globalIndex }}"
                                                        data-pendapatan-usd="{{ $item->pendapatan_usd }}"
                                                        data-kurs-usd="{{ $item->kurs_usd ?? 0 }}">

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
                                                        <td>{{ number_format($item->hargajual_idr, 2, ',', '.') }}</td>
                                                        <td>{{ number_format($item->hpp_ops, 2, ',', '.') }}</td>
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
                                                    <span class="value-footer" id="footerTotalSelling">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="currency-group-footer">
                                                    <span class="currency-label-footer">IDR</span>
                                                    <span class="value-footer" id="footerTotalHPP">0,00</span>
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
                        <div></div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('jo-contract.export-pdf', $joContract->id_jo_cont) }}" target="_blank"
                                class="btn"
                                style="padding:15px 40px; border-radius:12px; font-weight:700; font-size:1.1rem; border-color:red; background-color:rgb(255, 237, 237); color:red">
                                <i class="fas fa-file-pdf me-2"></i> Generate Invoice
                            </a>
                            <a href="{{ route('jo-contract.index') }}" class="btn btn-final-back">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
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

            if (decimalPart.length > 2) decimalPart = decimalPart.substring(0, 2);

            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            return parts.length > 1 ?
                integerPart + ',' + decimalPart :
                integerPart + ',00';
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
                    let newPos = 0,
                        digitCount = 0;
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
                    let decimalDigits = (beforeCursor.split(',')[1] || '').length;
                    let newPos = commaPos + 1 + Math.min(decimalDigits, 2);
                    this.setSelectionRange(newPos, newPos);
                }
            });

            input.addEventListener('blur', function() {
                if (this.value) {
                    if (!this.value.includes(',')) {
                        this.value = this.value + ',00';
                    } else {
                        let parts = this.value.split(',');
                        if (parts[1] !== undefined) {
                            if (parts[1].length === 0) this.value = parts[0] + ',00';
                            else if (parts[1].length < 2) this.value = parts[0] + ',' + parts[1].padEnd(2, '0');
                        }
                    }
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
        // LOAD KURS EXISTING DARI ITEMS
        // ========================================
        function loadExistingKurs() {
            // Ambil dari item pertama yang punya kurs_usd via blade
            @php
                $firstUsdItem = $joContract->items->first(fn($i) => $i->kurs_usd > 0);
            @endphp

            @if ($firstUsdItem)
                const existingKurs = {{ (float) $firstUsdItem->kurs_usd }};
                const existingDate = '{{ $firstUsdItem->tgl_kurs_usd?->format('Y-m-d\TH:i') ?? '' }}';

                if (existingKurs > 0) {
                    $('#global_kurs_usd_display').val(
                        formatRupiah(existingKurs.toFixed(2).replace('.', ','))
                    );
                    $('#global_kurs_usd').val(existingKurs);
                }
                if (existingDate) {
                    $('#global_tgl_kurs_usd').val(existingDate);
                }
            @endif
        }

        // ========================================
        // GLOBAL KURS CHANGE → UPDATE ALL USD ITEMS
        // ========================================
        $('#global_kurs_usd_display').on('input', function() {
            $('#global_kurs_usd').val(parseRupiah($(this).val()));
            calculateHargaJual();
        });

        function updateRowSellingPrice(row, newHargaJual) {
            const hasCategoryCell = row.find('.category-cell').length > 0;
            const offset = hasCategoryCell ? 0 : -1;
            // Kolom Selling Price ada di index ke-5 (dengan category cell) atau 4 (tanpa)
            row.find('td').eq(5 + offset).text(formatNumber(newHargaJual));
        }

        // ========================================
        // CONTRACT SEARCH
        // ========================================
        document.getElementById('contract_search').addEventListener('focus', function() {
            const searchType = document.querySelector('input[name="search_type"]:checked').value;
            displaySearchResults(contracts, '', searchType);
        });

        document.getElementById('contract_search').addEventListener('blur', function() {
            setTimeout(() => hideSearchResults(), 200);
        });

        document.getElementById('contract_search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const searchType = document.querySelector('input[name="search_type"]:checked').value;

            if (searchTerm.length === 0) {
                displaySearchResults(contracts, '', searchType);
                return;
            }

            const filteredContracts = contracts.filter(contract => {
                switch (searchType) {
                    case 'nomor':
                        return contract.no_contract?.toLowerCase().includes(searchTerm);
                    case 'name':
                        return contract.contract?.toLowerCase().includes(searchTerm);
                    case 'customer':
                        return contract.customer?.customer?.toLowerCase().includes(searchTerm);
                    default:
                        return false;
                }
            });

            displaySearchResults(filteredContracts, searchTerm, searchType);
        });

        document.querySelectorAll('input[name="search_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const placeholders = {
                    'nomor': 'Search contract by number...',
                    'name': 'Search contract by name...',
                    'customer': 'Search contract by customer name...'
                };
                document.getElementById('contract_search').placeholder = placeholders[this.value];
                document.getElementById('contract_search').value = '';
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
            return new Date(dateString).toLocaleDateString('en-US', {
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
            document.getElementById('preview_customer').textContent = contract.customer?.customer ?? '-';

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
                nomor: 'Nomor',
                name: 'Contract',
                customer: 'Customer'
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
                const customerName = contract.customer?.customer ?? '-';
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
                    `${formatDate(contract.date_start)} – ${formatDate(contract.date_end)}` : '-';

                const noContractHtml = searchType === 'nomor' ? highlightText(noContract, searchTerm) : noContract;
                const namaHtml = searchType === 'name' ? highlightText(contractName, searchTerm) : contractName;
                const customerHtml = searchType === 'customer' ? highlightText(customerName, searchTerm) :
                    customerName;
                const borderRadius = isLast ? 'border-radius:0 0 6px 6px;' : '';

                return `
                <a href="#" class="list-group-item list-group-item-action contract-search-item px-3 py-2"
                   data-contract-id="${contract.id_md_cont}"
                   style="display:flex; align-items:center; gap:0; ${borderRadius}">
                    <span style="font-size:13px;white-space:nowrap;width:130px;">${noContractHtml}</span>
                    <span style="color:#adb5bd;padding:0 8px;">|</span>
                    <span style="font-size:13px;white-space:nowrap;width:200px;overflow:hidden;text-overflow:ellipsis;">${namaHtml}</span>
                    <span style="color:#adb5bd;padding:0 8px;">|</span>
                    <span style="font-size:13px;color:#6c757d;white-space:nowrap;width:160px;overflow:hidden;text-overflow:ellipsis;">${customerHtml}</span>
                    <span style="color:#adb5bd;padding:0 8px;">|</span>
                    <span style="font-size:12px;color:#6c757d;white-space:nowrap;width:160px;">${periode}</span>
                    <span style="font-size:13px;white-space:nowrap;text-align:right;margin-left:auto;padding-left:16px;color:#2c3e50;">${expenditure}</span>
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
        // FLOATING BADGE ALERT
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
                setTimeout(() => hideFloatingAlert(), 3000);
            }
        }

        function hideFloatingAlert() {
            const alert = $('#floatingBadgeAlert');
            alert.addClass('hiding');
            setTimeout(() => alert.removeClass('show hiding'), 400);
        }

        // ========================================
        // AUTO-CALCULATE SELLING PRICE
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
            validateHPP();
        }

        $('#input_pendapatan_idr').on('input', function() {
            if (parseRupiah($(this).val()) > 0) $('#input_pendapatan_usd').val('');
            calculateHargaJual();
        });

        $('#input_pendapatan_usd').on('input', function() {
            if (parseRupiah($(this).val()) > 0) $('#input_pendapatan_idr').val('');
            calculateHargaJual();
        });

        $('#input_hpp').on('input', function() {
            validateHPP();
        });
        $('#input_hpp').on('blur', function() {
            validateHPP(true);
        });

        function validateHPP(clamp = false) {
            const hargaJual = parseRupiah($('#input_harga_jual').val());
            const hpp = parseRupiah($('#input_hpp').val());
            const hppInput = document.getElementById('input_hpp');

            if (hargaJual <= 0) return;

            if (hpp > hargaJual) {
                hppInput.style.borderColor = '#dc3545';
                hppInput.style.boxShadow = '0 0 0 0.2rem rgba(220,53,69,0.25)';

                if (!document.getElementById('hpp-warning')) {
                    const warning = document.createElement('small');
                    warning.id = 'hpp-warning';
                    warning.style.color = '#dc3545';
                    warning.style.fontWeight = '600';
                    warning.innerHTML =
                        `<i class="fas fa-exclamation-triangle me-1"></i>HPP tidak boleh melebihi Selling Price (${$('#input_harga_jual').val()})`;
                    hppInput.closest('.currency-group').after(warning);
                }

                if (clamp) {
                    $('#input_hpp').val($('#input_harga_jual').val());
                    hppInput.style.borderColor = '';
                    hppInput.style.boxShadow = '';
                    document.getElementById('hpp-warning')?.remove();
                }
            } else {
                hppInput.style.borderColor = '';
                hppInput.style.boxShadow = '';
                document.getElementById('hpp-warning')?.remove();
            }
        }

        // ========================================
        // CATEGORY DROPDOWN
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

        $('#input_item').on('change', function() {
            if ($('#editing_item_id').val()) return;
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
            const tglJo = $('#tgl_jo_cont').val();

            if (!contractId || !areaId || !title) {
                showFloatingAlert('error', 'Please fill all required fields');
                return;
            }

            showFloatingAlert('saving', 'Updating header...');

            $.ajax({
                url: `/jo-contract/header/update/${currentJoContractId}`,
                method: 'POST',
                data: {
                    id_md_cont: contractId,
                    id_md_area: areaId,
                    tgl_jo_cont: tglJo,
                    title: title,
                    note: $('#note').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(r) {
                    if (r.success) showFloatingAlert('success', 'Header updated successfully!');
                    else showFloatingAlert('error', r.message || 'Failed to update header');
                },
                error: function(xhr) {
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to update header');
                }
            });
        });

        // ========================================
        // ADD / UPDATE ITEM TO TABLE
        // ========================================
        // ========================================
        // ADD / UPDATE ITEM TO TABLE
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
            const inputKursRate = parseRupiah($('#global_kurs_usd_display').val()) || 0;
            const inputKursDate = $('#global_tgl_kurs_usd').val() || null;
            const note = $('#input_note').val();

            if (!category || !itemId) {
                showFloatingAlert('error', 'Please select category and item');
                return;
            }

            if (pendapatanUSD > 0 && (!inputKursDate || inputKursRate <= 0)) {
                showFloatingAlert('error', 'Kurs rate dan date wajib diisi untuk item USD');
                $('#global_kurs_usd_display').focus();
                return;
            }

            if (hargaJual > 0 && hpp > hargaJual) {
                showFloatingAlert('error', 'HPP tidak boleh melebihi Selling Price');
                $('#input_hpp').focus();
                return;
            }

            // Cek apakah kurs berbeda dengan item USD lain yang sudah ada di tabel
            if (pendapatanUSD > 0 && inputKursRate > 0) {
                const existingKurs = getExistingKursFromTable();

                if (existingKurs !== null && existingKurs !== inputKursRate) {
                    // Tampilkan konfirmasi — kurs berbeda
                    showKursConflictConfirm(
                        existingKurs,
                        inputKursRate,
                        inputKursDate,
                        // Callback: user setuju → simpan + update semua
                        function() {
                            proceedSaveItem(editingItemId, category, itemId, itemText,
                                pendapatanIDR, pendapatanUSD, hpp, hargaJual,
                                inputKursRate, inputKursDate, note, true);
                        },
                        // Callback: user tolak → simpan dengan kurs lama (paksa pakai existingKurs)
                        function() {
                            const hargaJualWithOldKurs = pendapatanUSD * existingKurs;
                            proceedSaveItem(editingItemId, category, itemId, itemText,
                                pendapatanIDR, pendapatanUSD, hpp, hargaJualWithOldKurs,
                                existingKurs, inputKursDate, note, false);
                        }
                    );
                    return;
                }
            }

            // Kurs sama atau item IDR — langsung simpan
            proceedSaveItem(editingItemId, category, itemId, itemText,
                pendapatanIDR, pendapatanUSD, hpp, hargaJual,
                inputKursRate, inputKursDate, note, false);
        });

        // ========================================
        // AMBIL KURS EXISTING DARI TABEL
        // Kembalikan nilai kurs item USD pertama di tabel, atau null jika tidak ada
        // ========================================
        function getExistingKursFromTable() {
            let existingKurs = null;
            $('#itemsTableBody tr.item-row').each(function() {
                const usdVal = parseFloat($(this).data('pendapatan-usd')) || 0;
                const kurs = parseFloat($(this).data('kurs-usd')) || 0;
                if (usdVal > 0 && kurs > 0) {
                    existingKurs = kurs;
                    return false; // break
                }
            });
            return existingKurs;
        }

        // ========================================
        // KONFIRMASI KURS BERBEDA
        // ========================================
        function showKursConflictConfirm(oldKurs, newKurs, newDate, onConfirm, onReject) {
            // Hapus modal lama jika ada
            $('#kursConflictModal').remove();

            const oldFormatted = formatNumber(oldKurs);
            const newFormatted = formatNumber(newKurs);

            const modalHtml = `
    <div class="modal fade" id="kursConflictModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                <div class="modal-header" style="background:linear-gradient(135deg,#fbbf24,#f59e0b); border-radius:12px 12px 0 0; border:none;">
                    <h5 class="modal-title text-white fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Perbedaan Kurs Terdeteksi
                    </h5>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-3">Item ini memiliki kurs yang <strong>berbeda</strong> dengan item USD lain di tabel:</p>
                    <div class="d-flex gap-3 mb-3">
                        <div class="flex-fill text-center p-3 rounded" style="background:#fee2e2; border:1px solid #fca5a5;">
                            <div style="font-size:0.75rem; color:#991b1b; font-weight:600; margin-bottom:4px;">KURS ITEM LAIN</div>
                            <div style="font-size:1.1rem; font-weight:700; color:#dc2626;">IDR ${oldFormatted}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-arrow-right text-muted"></i>
                        </div>
                        <div class="flex-fill text-center p-3 rounded" style="background:#d1fae5; border:1px solid #6ee7b7;">
                            <div style="font-size:0.75rem; color:#065f46; font-weight:600; margin-bottom:4px;">KURS ITEM INI</div>
                            <div style="font-size:1.1rem; font-weight:700; color:#059669;">IDR ${newFormatted}</div>
                        </div>
                    </div>
                    <div class="alert alert-warning py-2 mb-0" style="font-size:0.875rem;">
                        <i class="fas fa-info-circle me-1"></i>
                        Jika <strong>Ya, update semua</strong> — kurs seluruh item USD akan diubah ke <strong>IDR ${newFormatted}</strong> dan harga jual dihitung ulang.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4 gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-fill" id="btnKursReject">
                        <i class="fas fa-times me-1"></i>Tidak, pakai kurs lama
                    </button>
                    <button type="button" class="btn btn-warning flex-fill fw-bold" id="btnKursConfirm">
                        <i class="fas fa-check me-1"></i>Ya, update semua
                    </button>
                </div>
            </div>
        </div>
    </div>`;

            $('body').append(modalHtml);

            const modal = new bootstrap.Modal(document.getElementById('kursConflictModal'));
            modal.show();

            $('#btnKursConfirm').on('click', function() {
                modal.hide();
                $('#kursConflictModal').on('hidden.bs.modal', function() {
                    $(this).remove();
                    onConfirm();
                });
            });

            $('#btnKursReject').on('click', function() {
                modal.hide();
                $('#kursConflictModal').on('hidden.bs.modal', function() {
                    $(this).remove();
                    onReject();
                });
            });
        }

        // ========================================
        // PROCEED SAVE ITEM
        // syncAllKurs: true → setelah simpan, update kurs semua item USD lain
        // ========================================
        function proceedSaveItem(editingItemId, category, itemId, itemText,
            pendapatanIDR, pendapatanUSD, hpp, hargaJual,
            kursRate, kursDate, note, syncAllKurs) {

            if (editingItemId) {
                updateItemToDatabase(editingItemId, category, itemId, itemText,
                    pendapatanIDR, pendapatanUSD, hpp, hargaJual,
                    kursRate, kursDate, note, syncAllKurs);
                return;
            }

            // ADD NEW
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
                    if (response.success) {
                        showFloatingAlert('success', 'Item added successfully!');
                        $('.no-items-row').remove();

                        const newItemId = response.data.id_jo_cont_item || response.data.id;
                        if (!newItemId || newItemId === 0) {
                            showFloatingAlert('error', 'Item created but ID is invalid. Please refresh.');
                            return;
                        }

                        insertRowWithCategoryGrouping(newItemId, category, itemText, response.data, kursRate);
                        updateGrandTotal();
                        clearItemForm();

                        // Update kurs global field agar sinkron
                        syncKursDisplayField(kursRate, kursDate);

                        if (syncAllKurs) {
                            syncAllOtherUsdItems(newItemId, kursRate, kursDate);
                        }
                    } else {
                        showFloatingAlert('error', 'Failed to add item');
                    }
                },
                error: function(xhr) {
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to add item');
                }
            });
        }

        // ========================================
        // UPDATE SEMUA ITEM USD LAIN (SELAIN ITEM YANG BARU DISIMPAN)
        // ========================================
        function syncAllOtherUsdItems(excludeItemId, kursRate, kursDate) {
            const usdItems = [];

            $('#itemsTableBody tr.item-row').each(function() {
                const id = $(this).data('item-id');
                const usdVal = parseFloat($(this).data('pendapatan-usd')) || 0;

                if (usdVal > 0 && id != excludeItemId) {
                    usdItems.push({
                        id,
                        row: $(this),
                        usdVal
                    });
                }
            });

            if (usdItems.length === 0) return;

            showFloatingAlert('saving', `Menyamakan kurs ${usdItems.length} item lain...`);

            const promises = usdItems.map(item => {
                const newHargaJual = item.usdVal * kursRate;

                return $.ajax({
                    url: `/jo-contract/item/update-kurs/${item.id}`,
                    method: 'PATCH',
                    data: {
                        kurs_usd: kursRate,
                        tgl_kurs_usd: kursDate || null,
                        hargajual_idr: newHargaJual,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                }).then(response => {
                    if (response.success) {
                        // Update data-kurs-usd di row
                        item.row.attr('data-kurs-usd', kursRate);
                        // Update selling price di tabel
                        updateRowSellingPrice(item.row, newHargaJual);
                    }
                });
            });

            Promise.all(promises)
                .then(() => {
                    showFloatingAlert('success', 'Kurs semua item berhasil disamakan!');
                    updateGrandTotal();
                })
                .catch(() => {
                    showFloatingAlert('error', 'Gagal update kurs beberapa item');
                });
        }

        // Sync tampilan field kurs global
        function syncKursDisplayField(kursRate, kursDate) {
            if (kursRate > 0) {
                $('#global_kurs_usd_display').val(formatRupiah(kursRate.toFixed(2).replace('.', ',')));
                $('#global_kurs_usd').val(kursRate);
            }
            if (kursDate) {
                $('#global_tgl_kurs_usd').val(kursDate);
            }
        }

        function updateRowSellingPrice(row, newHargaJual) {
            const hasCategoryCell = row.find('.category-cell').length > 0;
            const offset = hasCategoryCell ? 0 : -1;
            row.find('td').eq(5 + offset).text(formatNumber(newHargaJual));
        }

        // ========================================
        // INSERT ROW WITH CATEGORY GROUPING
        // ========================================
        function insertRowWithCategoryGrouping(itemId, category, itemText, data, kursRate = 0) {
            globalItemNumber++;

            let categoryExists = false;
            let insertAfterRow = null;

            $('#itemsTableBody tr.item-row').each(function() {
                if ($(this).data('category') === category) {
                    categoryExists = true;
                    insertAfterRow = $(this);

                    const categoryCell = $(this).find('.category-cell');
                    if (categoryCell.length > 0) {
                        categoryCell.attr('rowspan', parseInt(categoryCell.attr('rowspan') || 1) + 1);
                    }
                }
            });

            const pendapatanUsd = data.pendapatan_usd || 0;
            const actionBtns = `
            <button type="button" class="btn btn-primary btn-sm btn-edit-row" onclick="editItem('${itemId}')">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-danger btn-sm btn-remove-row" onclick="removeItem(this, '${itemId}')">
                <i class="fas fa-trash"></i>
            </button>`;

            let newRow;

            if (categoryExists) {
                newRow = `
                <tr class="item-row"
                    data-item-id="${itemId}"
                    data-category="${category}"
                    data-pendapatan-usd="${pendapatanUsd}"
                    data-kurs-usd="${kursRate}">
                    <td class="item-number-cell">${globalItemNumber}</td>
                    <td class="item-text-cell">${itemText}</td>
                    <td>${formatNumber(data.pendapatan_idr)}</td>
                    <td>${formatNumber(data.pendapatan_usd)}</td>
                    <td>${formatNumber(data.hargajual_idr)}</td>
                    <td>${formatNumber(data.hpp_ops)}</td>
                    <td class="text-center">${actionBtns}</td>
                </tr>`;
                insertAfterRow.after(newRow);
            } else {
                newRow = `
                <tr class="item-row"
                    data-item-id="${itemId}"
                    data-category="${category}"
                    data-pendapatan-usd="${pendapatanUsd}"
                    data-kurs-usd="${kursRate}">
                    <td class="item-number-cell">${globalItemNumber}</td>
                    <td class="category-cell" rowspan="1">${category}</td>
                    <td class="item-text-cell">${itemText}</td>
                    <td>${formatNumber(data.pendapatan_idr)}</td>
                    <td>${formatNumber(data.pendapatan_usd)}</td>
                    <td>${formatNumber(data.hargajual_idr)}</td>
                    <td>${formatNumber(data.hpp_ops)}</td>
                    <td class="text-center">${actionBtns}</td>
                </tr>`;
                $('#itemsTableBody').append(newRow);
            }

            renumberAllItems();
        }

        // ========================================
        // EDIT ITEM
        // ========================================
        function editItem(itemId) {
            if (!itemId || itemId == 0) {
                showFloatingAlert('error', 'Invalid item ID');
                return;
            }

            showFloatingAlert('saving', 'Loading item data...');

            $.ajax({
                url: `/jo-contract/item/show/${itemId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        hideFloatingAlert();
                        const item = response.data;

                        $('#editing_item_id').val(itemId);
                        $('#formSectionTitle').html('<i class="fas fa-edit"></i> Edit Item');
                        $('#addItemFormSection').addClass('edit-mode');
                        $('#btnAddToTable').html('<i class="fas fa-save"></i> Update Item');
                        $('#btnCancelEdit').show();

                        $('#input_category').val(item.invoice_ctg).trigger('change');

                        setTimeout(() => {
                            $('#input_item').val(item.id_md_invoice);

                            const pendapatanIDR = parseFloat(item.pendapatan_idr) || 0;
                            const pendapatanUSD = parseFloat(item.pendapatan_usd) || 0;
                            const hpp = parseFloat(item.hpp_ops) || 0;
                            const kursUSD = parseFloat(item.kurs_usd) || 0;

                            $('#input_pendapatan_idr').val(pendapatanIDR > 0 ? formatRupiah(
                                pendapatanIDR.toFixed(2).replace('.', ',')) : '');
                            $('#input_pendapatan_usd').val(pendapatanUSD > 0 ? formatRupiah(
                                pendapatanUSD.toFixed(2).replace('.', ',')) : '');
                            $('#input_hpp').val(formatRupiah(hpp.toFixed(2).replace('.', ',')));
                            $('#input_note').val(item.note || '');

                            // Kurs sudah ada di global field — tidak perlu di-set ulang
                            // tapi jika item ini punya kurs berbeda (edge case), tampilkan di global
                            if (pendapatanUSD > 0 && kursUSD > 0) {
                                const currentGlobalKurs = parseRupiah($('#global_kurs_usd_display')
                                    .val());
                                if (currentGlobalKurs <= 0) {
                                    $('#global_kurs_usd_display').val(formatRupiah(kursUSD.toFixed(2)
                                        .replace('.', ',')));
                                    $('#global_kurs_usd').val(kursUSD);
                                }
                                if (!$('#global_tgl_kurs_usd').val() && item.tgl_kurs_usd) {
                                    $('#global_tgl_kurs_usd').val(item.tgl_kurs_usd.substring(0, 16));
                                }
                            }

                            calculateHargaJual();
                        }, 300);

                        $('html, body').animate({
                            scrollTop: $('#addItemFormSection').offset().top - 100
                        }, 500);
                    } else {
                        showFloatingAlert('error', 'Failed to load item data');
                    }
                },
                error: function(xhr) {
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to load item data');
                }
            });
        }

        $('#btnCancelEdit').on('click', function() {
            clearItemForm();
            $('html, body').animate({
                scrollTop: $('#itemsTableBody').offset().top - 200
            }, 500);
        });

        function updateItemToDatabase(itemId, category, invoiceId, itemText,
            pendapatanIDR, pendapatanUSD, hpp, hargaJual, kursRate, kursDate, note, syncAllKurs = false) {

            showFloatingAlert('saving', 'Updating item...');

            $.ajax({
                url: `/jo-contract/item/update/${itemId}`,
                method: 'PUT',
                data: {
                    id_jo_cont: currentJoContractId,
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

                        // Update data attribute kurs

                        row.attr('data-kurs-usd', kursRate);
                        row.attr('data-pendapatan-usd', pendapatanUSD);

                        syncKursDisplayField(kursRate, kursDate);

                        if (syncAllKurs) {
                            syncAllOtherUsdItems(itemId, kursRate, kursDate);
                        }

                        // Sync kurs global field
                        syncKursDisplayField(kursRate, kursDate);

                        if (syncAllKurs) {
                            syncAllOtherUsdItems(editingItemId, kursRate, kursDate);
                        }


                        if (oldCategory !== category) {
                            removeRowWithoutDelete(row, oldCategory);
                            insertRowWithCategoryGrouping(itemId, category, itemText, response.data);
                        } else {
                            row.attr('data-invoice-id', invoiceId);
                            row.attr('data-item-text', itemText);
                            row.find('.item-text-cell').text(itemText);

                            const hasCategoryCell = row.find('.category-cell').length > 0;
                            const offset = hasCategoryCell ? 0 : -1;

                            row.find('td').eq(3 + offset).text(formatNumber(response.data.pendapatan_idr));
                            row.find('td').eq(4 + offset).text(formatNumber(response.data.pendapatan_usd));
                            row.find('td').eq(5 + offset).text(formatNumber(response.data.hargajual_idr));
                            row.find('td').eq(6 + offset).text(formatNumber(response.data.hpp_ops));
                        }

                        updateGrandTotal();
                        clearItemForm();

                        $('html, body').animate({
                            scrollTop: $(`.item-row[data-item-id="${itemId}"]`).offset().top - 200
                        }, 500);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to update item';
                    if (xhr.responseJSON?.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                    } else if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showFloatingAlert('error', errorMessage);
                }
            });
        }

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
        // REMOVE ITEM
        // ========================================
        function removeItem(button, itemId) {
            if (!itemId || itemId == 0) {
                showFloatingAlert('error', 'Invalid item ID');
                return;
            }

            if (!confirm('Are you sure you want to delete this item?')) return;

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
                        const categoryCell = row.find('.category-cell');

                        if (categoryCell.length > 0) {
                            const rowspan = parseInt(categoryCell.attr('rowspan') || 1);
                            if (rowspan > 1) {
                                const nextRow = row.next(`.item-row[data-category="${category}"]`);
                                if (nextRow.length > 0) {
                                    nextRow.find('.item-text-cell').before(
                                        `<td class="category-cell" rowspan="${rowspan - 1}">${category}</td>`
                                    );
                                }
                            }
                        } else {
                            const prevRow = row.prevAll(`.item-row[data-category="${category}"]`).first();
                            const prevCatCell = prevRow.find('.category-cell');
                            if (prevCatCell.length > 0) {
                                const cur = parseInt(prevCatCell.attr('rowspan') || 1);
                                if (cur > 1) prevCatCell.attr('rowspan', cur - 1);
                            }
                        }

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
                            </tr>`);
                        }
                    }
                },
                error: function(xhr) {
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to delete item');
                }
            });
        }

        // Helper: hapus row dari DOM tanpa AJAX delete (dipakai saat edit category berubah)
        function removeRowWithoutDelete(row, category) {
            const categoryCell = row.find('.category-cell');
            if (categoryCell.length > 0) {
                const rowspan = parseInt(categoryCell.attr('rowspan') || 1);
                if (rowspan > 1) {
                    const nextRow = row.next(`.item-row[data-category="${category}"]`);
                    if (nextRow.length > 0) {
                        nextRow.find('.item-text-cell').before(
                            `<td class="category-cell" rowspan="${rowspan - 1}">${category}</td>`
                        );
                    }
                }
            } else {
                const prevRow = row.prevAll(`.item-row[data-category="${category}"]`).first();
                const prevCatCell = prevRow.find('.category-cell');
                if (prevCatCell.length > 0) {
                    const cur = parseInt(prevCatCell.attr('rowspan') || 1);
                    if (cur > 1) prevCatCell.attr('rowspan', cur - 1);
                }
            }
            row.remove();
        }

        // ========================================
        // RESET ALL ITEMS
        // ========================================
        $('#resetAllBtn').on('click', function() {
            if (!confirm(
                    'Are you sure you want to remove all items? This will delete all items from the database.'))
                return;

            const itemIds = [];
            $('.item-row').each(function() {
                const itemId = $(this).data('item-id');
                if (itemId && itemId != 0) itemIds.push(itemId);
            });

            if (itemIds.length === 0) {
                showFloatingAlert('error', 'No items to delete');
                return;
            }

            showFloatingAlert('saving', 'Deleting all items...');

            Promise.all(itemIds.map(itemId =>
                $.ajax({
                    url: `/jo-contract/item/destroy/${itemId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })
            )).then(() => {
                showFloatingAlert('success', 'All items deleted successfully!');
                $('#itemsTableBody').html(`
                <tr class="no-items-row">
                    <td colspan="8">
                        <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                        <p class="mb-0 fw-bold">No data available</p>
                        <small class="text-muted">Fill the form above and click "Add to Table"</small>
                    </td>
                </tr>`);
                globalItemNumber = 0;
                updateGrandTotal();
            }).catch(() => {
                showFloatingAlert('error', 'Some items could not be deleted');
            });
        });

        // ========================================
        // UTILITY
        // ========================================
        function renumberAllItems() {
            $('.item-row').each(function(index) {
                $(this).find('.item-number-cell').text(index + 1);
                $(this).attr('data-item-number', index + 1);
            });
            globalItemNumber = $('.item-row').length;
        }

        function updateGrandTotal() {
            let totalIDR = 0,
                totalUSD = 0,
                totalHPP = 0,
                totalSelling = 0;

            $('.item-row').each(function() {
                const hasCategoryCell = $(this).find('.category-cell').length > 0;
                const off = hasCategoryCell ? 0 : -1;

                totalIDR += parseRupiah($(this).find('td').eq(3 + off).text());
                totalUSD += parseRupiah($(this).find('td').eq(4 + off).text());
                totalSelling += parseRupiah($(this).find('td').eq(5 + off).text());
                totalHPP += parseRupiah($(this).find('td').eq(6 + off).text());
            });

            $('#footerTotalIDR').text(formatNumber(totalIDR));
            $('#footerTotalUSD').text(formatNumber(totalUSD));
            $('#footerTotalSelling').text(formatNumber(totalSelling));
            $('#footerTotalHPP').text(formatNumber(totalHPP));
        }

        // ========================================
        // INIT
        // ========================================
        $(document).ready(function() {
            updateGrandTotal();
            loadExistingKurs();
        });

        function loadExistingKurs() {
            @php
                $firstUsdItem = $joContract->items->first(fn($i) => $i->kurs_usd > 0);
            @endphp
            @if ($firstUsdItem)
                const kursVal = {{ (float) $firstUsdItem->kurs_usd }};
                const kursDate = '{{ $firstUsdItem->tgl_kurs_usd?->format('Y-m-d\TH:i') ?? '' }}';

                if (kursVal > 0) {
                    $('#global_kurs_usd_display').val(
                        formatRupiah(kursVal.toFixed(2).replace('.', ','))
                    );
                    $('#global_kurs_usd').val(kursVal);
                }
                if (kursDate) {
                    $('#global_tgl_kurs_usd').val(kursDate);
                }
            @endif
        }
    </script>
@endpush
