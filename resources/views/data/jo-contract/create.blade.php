@extends('layouts.app')

@section('title', 'Add JO Contract')

@push('styles')
    <style>
        .joContractCreatePage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .joContractCreatePage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .joContractCreatePage .form-control:focus,
        .joContractCreatePage .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .joContractCreatePage .form-label {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .joContractCreatePage .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .joContractCreatePage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .joContractCreatePage .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .joContractCreatePage .btn-success:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: 600;
        }

        /* STATUS BADGE */
        .badge-saving {
            background-color: #fbbf24 !important;
            animation: pulse 1.5s infinite;
        }

        .badge-saved {
            background-color: #10b981 !important;
        }

        .badge-error {
            background-color: #ef4444 !important;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* READONLY STATE */
        .form-readonly {
            background-color: #f3f4f6 !important;
            cursor: not-allowed !important;
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
        }

        .add-item-form-section h6 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .add-item-form-section h6 i {
            color: var(--primary-green);
            font-size: 1.2rem;
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

        .btn-remove-row {
            padding: 0.4rem 0.6rem;
            font-size: 0.875rem;
            border-radius: 6px;
            transition: all 0.3s;
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

        /* ITEMS DISABLED STATE */
        .items-card-disabled {
            pointer-events: none;
            opacity: 0.6;
        }

        .items-card-disabled::after {
            content: "Please save header first to add items";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px 30px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
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

        #search_results .list-group-item-action:hover {
            background-color: #f0fdf4 !important;
        }

        #search_results mark {
            background: #fef08a;
            padding: 0 1px;
            border-radius: 2px;
        }

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
    <div class="container-fluid joContractCreatePage">
        <div class="row">
            <div class="col-lg-12">
                <!-- Page Header Card -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add JO Contract</span>
                        <div class="d-flex gap-2 align-items-center">
                            <span id="autoSaveStatus" class="badge bg-secondary">
                                <i class="fas fa-circle"></i> Not Saved
                            </span>
                            <a href="{{ route('jo-contract.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Hidden State -->
                <input type="hidden" id="current_jo_contract_id" value="">
                <input type="hidden" id="is_header_saved" value="false">

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
                                            <input type="text" class="form-control fw-bold" value="{{ $previewNoJo }}"
                                                readonly
                                                style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                        </div>
                                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Auto-generate on
                                            save</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required-field">JO date</label>
                                        <input type="date" name="tgl_jo_cont" id="tgl_jo_cont" class="form-control"
                                            value="{{ date('Y-m-d') }}">
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
                                            placeholder="Search contract by number..." autocomplete="off">
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
                                    <select name="id_md_cont" id="id_md_cont"
                                        class="form-select d-none @error('id_md_cont') is-invalid @enderror" required>
                                        <option value="">Select Contract</option>
                                    </select>

                                    @error('id_md_cont')
                                        <div class="invalid-feedback d-block"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror

                                    <!-- Contract Preview Card -->
                                    <div id="contract_preview" class="card mt-3" style="display: none;">
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
                                                            <td id="preview_no_contract">-</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Contract</strong></td>
                                                            <td>:</td>
                                                            <td id="preview_contract_name">-</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Customer</strong></td>
                                                            <td>:</td>
                                                            <td id="preview_customer">-</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table table-sm table-borderless">
                                                        <tr>
                                                            <td width="40%"><strong>Expenditure</strong></td>
                                                            <td width="5%">:</td>
                                                            <td id="preview_expenditure">-</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Start Date</strong></td>
                                                            <td>:</td>
                                                            <td id="preview_start_date">-</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>End Date</strong></td>
                                                            <td>:</td>
                                                            <td id="preview_end_date">-</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row" id="preview_note_section" style="display: none;">
                                                <div class="col-12">
                                                    <hr>
                                                    <strong>Note:</strong>
                                                    <p class="mb-0 text-muted" id="preview_note"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label required-field">Area</label>
                                    <select name="id_md_area" id="id_md_area"
                                        class="form-select @error('id_md_area') is-invalid @enderror">
                                        <option value="">Select Area</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id_md_area }}"
                                                {{ old('id_md_area') == $area->id_md_area ? 'selected' : '' }}>
                                                {{ $area->area }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_md_area')
                                        <div class="invalid-feedback"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required-field">Title</label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                                @error('title')
                                    <div class="invalid-feedback"><i
                                            class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback"><i
                                            class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Save Header Button -->
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-success" id="btnSaveHeader">
                                    <i class="fas fa-save me-1"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- JO Contract Items Card -->
                    <div class="card shadow mb-4" id="itemsCard">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>JO Contract Items</h6>
                                <!-- KURS SECTION PINDAH KE SINI -->
                                <div class="kurs-section d-flex gap-3 align-items-center mb-0"
                                    style="padding: 10px 15px;">
                                    <div>
                                        <label class="form-label mb-1">Kurs Date</label>
                                        <input type="date" name="global_tgl_kurs_usd" id="global_tgl_kurs_usd"
                                            class="form-control form-control-sm" style="min-width: 150px;" required>
                                    </div>
                                    <div>
                                        <label class="form-label mb-1">Kurs Rate</label>
                                        <input type="text" id="global_kurs_usd_display"
                                            class="form-control form-control-sm" style="min-width: 130px;" required>
                                        <input type="hidden" name="global_kurs_usd" id="global_kurs_usd"
                                            value="17600">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <!-- ADD ITEM FORM SECTION -->
                            <div class="add-item-form-section">
                                <h6><i class="fas fa-plus-square"></i> Add New Item</h6>

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

                                    <!-- Pendapatan IDR -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Income (IDR)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_pendapatan_idr"
                                                class="form-control currency-input">
                                        </div>
                                    </div>

                                    <!-- Pendapatan USD -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Income (USD)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">USD</span>
                                            <input type="text" id="input_pendapatan_usd"
                                                class="form-control currency-input">
                                        </div>
                                    </div>

                                    <!-- HPP -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">HPP (Ops Costs)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_hpp" class="form-control currency-input">
                                        </div>
                                    </div>

                                    <!-- Harga Jual (Auto Calculate) -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Selling Price (IDR)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_harga_jual"
                                                class="form-control currency-input" readonly
                                                style="background-color: #e9ecef;">
                                        </div>
                                    </div>

                                    <!-- Add Button -->
                                    <div class="col-md-12 mb-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-add-to-table w-100" id="btnAddToTable">
                                            <i class="fas fa-arrow-down"></i> Add to Table
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- ACTION BUTTONS FOR TABLE -->
                            <div class="action-buttons">
                                <button type="button" class="btn btn-danger" id="resetAllBtn">
                                    <i class="fas fa-trash-alt me-1"></i>Reset All Items
                                </button>
                            </div>

                            <!-- TABLE DISPLAY -->
                            <div class="table-responsive">
                                <table class="table table-items table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 15%;">Category</th>
                                            <th style="width: 15%;">Item</th>
                                            <th style="width: 13%;">Income (IDR)</th>
                                            <th style="width: 13%;">Income (USD)</th>
                                            <th style="width: 13%;">HPP (Ops Costs)</th>
                                            <th style="width: 13%;">Selling Price (IDR)</th>
                                            <th style="width: 8%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <tr class="no-items-row">
                                            <td colspan="8">
                                                <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                                                <p class="mb-0 fw-bold">No data available</p>
                                                <small class="text-muted">Fill the form above and click "Add to
                                                    Table"</small>
                                            </td>
                                        </tr>
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

                            @error('items')
                                <div class="alert alert-danger mt-3">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </form>
                <!-- FINAL SAVE SECTION -->
                <div class="final-save-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div></div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="d-flex flex-column align-items-center">
                                <button type="button" class="btn disabled" id="btnGeneratePdf"
                                    style="padding:15px 40px; border-radius:12px; font-weight:700; font-size:1.1rem; opacity:0.55; cursor:not-allowed; border-color:red; background-color:rgb(255, 237, 237); color:red"
                                    title="Save header first to generate PDF">
                                    <i class="fas fa-file-pdf me-2"></i> Generate Invoice
                                </button>
                                <small class="text-muted mt-1" id="pdfHintText">
                                    <i class="fas fa-info-circle me-1"></i>Save JO Contract Information first
                                </small>
                            </div>
                            <a href="{{ route('jo-contract.index') }}" class="btn btn-final-back"
                                style="margin-top:-25px">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Item -->
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_item_index">
                    <input type="hidden" id="edit_item_db_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select id="edit_category" class="form-select">
                                @foreach ($invoices->groupBy('invoice_ctg') as $category => $invoiceGroup)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Item</label>
                            <select id="edit_item" class="form-select">
                                <option value="">Select Item</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Income (IDR)</label>
                            <input type="text" id="edit_pendapatan_idr" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Income (USD)</label>
                            <input type="text" id="edit_pendapatan_usd" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">HPP (Ops Costs)</label>
                            <input type="text" id="edit_hpp" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Selling Price</label>
                            <input type="text" id="edit_harga_jual" class="form-control" readonly
                                style="background-color: #e9ecef;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="btnSaveEditItem">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Script Jo Contract Create --}}
@push('scripts')
    <script>
        // ========================================
        // GLOBAL VARIABLES
        // ========================================
        const contracts = @json($contracts);
        let selectedContract = null;
        let currentJoContractId = null;
        let isHeaderSaved = false;
        let itemsData = [];
        let globalItemNumber = 0;

        // Invoice data by category
        const invoicesByCategory = {
            @foreach ($invoices->groupBy('invoice_ctg') as $category => $invoiceGroup)
                '{{ $category }}': [
                    @foreach ($invoiceGroup as $invoice)
                        {
                            id: '{{ $invoice->id_md_invoice }}',
                            type: '{{ $invoice->invoice_typ }}'
                        },
                    @endforeach
                ],
            @endforeach
        };

        // ========================================
        // STATUS & TOAST FUNCTIONS
        // ========================================
        function updateSaveStatus(status, message) {
            showFloatingAlert(status, message);
        }

        // ========================================
        // FLOATING BADGE ALERT FUNCTIONS (GANTI TOAST)
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
        // HAPUS FUNCTION showToast - TIDAK DIPAKAI LAGI
        // ========================================
        // function showToast(type, message) { ... } ← HAPUS INI

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
            let cleaned = value.replace(/\./g, '').replace(',', '.');
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
        setupRupiahInput(document.getElementById('edit_pendapatan_idr'));
        setupRupiahInput(document.getElementById('edit_pendapatan_usd'));
        setupRupiahInput(document.getElementById('edit_hpp'));

        // ========================================
        // CONTRACT SEARCH - ENHANCED WITH INITIAL DATA DISPLAY
        // ========================================

        // Tampilkan semua data saat halaman load
        $(document).ready(function() {
            if (!isHeaderSaved) {
                $('#itemsCard').addClass('items-card-disabled').css('position', 'relative');
            }
        });

        // Tampilkan semua contract saat input fokus
        document.getElementById('contract_search').addEventListener('focus', function() {
            showAllContracts();
        });

        document.getElementById('contract_search').addEventListener('blur', function() {
            setTimeout(() => {
                hideSearchResults();
            }, 200);
        });


        function showAllContracts() {
            const searchType = document.querySelector('input[name="search_type"]:checked').value;
            displaySearchResults(contracts, '', searchType);
        }

        // ========================================
        // CONTRACT SEARCH (SAMA SEPERTI ORIGINAL)
        // ========================================
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
                showAllContracts(); // ← Tampilkan semua dengan filter baru
            });
        });

        document.getElementById('contract_search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const searchType = document.querySelector('input[name="search_type"]:checked').value;

            if (searchTerm.length === 0) {
                showAllContracts();
                return;
            }

            if (searchTerm.length < 1) {
                hideSearchResults();
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
        <div class="search-results-header d-flex justify-content-between px-3 py-1"
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

                // Highlight kolom yang aktif
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

        <span style="font-size:13px; white-space:nowrap; width:130px;">
            ${noContractHtml}
        </span>

        <span style="color:#adb5bd; padding:0 8px; user-select:none;">|</span>

        <span style="font-size:13px; white-space:nowrap; width:200px; overflow:hidden; text-overflow:ellipsis;">
            ${namaContractHtml}
        </span>

        <span style="color:#adb5bd; padding:0 8px; user-select:none;">|</span>

        <span style="font-size:13px; color:#6c757d; white-space:nowrap; width:160px; overflow:hidden; text-overflow:ellipsis;">
            ${customerHtml}
        </span>

        <span style="color:#adb5bd; padding:0 8px; user-select:none;">|</span>

        <span style="font-size:12px; color:#6c757d; white-space:nowrap; width:160px;">
            ${periode}
        </span>

        <span style="font-size:13px; white-space:nowrap; text-align:right; margin-left:auto; padding-left:16px; color:#2c3e50;">
            ${expenditure}
        </span>
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

        // Helper: highlight teks yang match
        function highlightText(text, term) {
            if (!term || !text) return text || '';
            const escaped = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return text.replace(new RegExp(`(${escaped})`, 'gi'),
                '<mark style="background:#fef08a;padding:0;border-radius:2px;">$1</mark>');
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
            const previewCard = document.getElementById('contract_preview');

            document.getElementById('preview_no_contract').textContent = contract.no_contract || '-';
            document.getElementById('preview_contract_name').textContent = contract.contract || '-';
            document.getElementById('preview_customer').textContent = contract.customer ? contract.customer.customer : '-';

            const expenditure = contract.expenditure ?
                new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(contract.expenditure) :
                '-';

            document.getElementById('preview_expenditure').textContent = expenditure;

            document.getElementById('preview_start_date').textContent = formatDate(contract.date_start);
            document.getElementById('preview_end_date').textContent = formatDate(contract.date_end);

            if (contract.note) {
                document.getElementById('preview_note').textContent = contract.note;
                document.getElementById('preview_note_section').style.display = 'block';
            } else {
                document.getElementById('preview_note_section').style.display = 'none';
            }

            previewCard.style.display = 'block';
        }

        function clearContractPreview() {
            document.getElementById('contract_preview').style.display = 'none';
            document.getElementById('id_md_cont').innerHTML = '<option value="">Select Contract</option>';
            selectedContract = null;
        }

        function hideSearchResults() {
            document.getElementById('search_results').style.display = 'none';
        }

        document.getElementById('clear_search').addEventListener('click', function() {
            document.getElementById('contract_search').value = '';
            hideSearchResults();
            clearContractPreview();
        });

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            return date.toLocaleDateString('en-US', options);
        }

        // ========================================
        // SAVE HEADER (REALTIME) - UPDATE
        // ========================================
        $('#btnSaveHeader').on('click', function() {
            saveHeaderToDatabase();
        });

        function saveHeaderToDatabase() {
            const contractId = $('#id_md_cont').val();
            const areaId = $('#id_md_area').val();
            const title = $('#title').val();
            const note = $('#note').val();
            const tglJo = $('#tgl_jo_cont').val();

            if (!contractId || !areaId || !title) {
                showFloatingAlert('error', 'Please fill all required fields');
                return;
            }

            const formData = {
                id_md_cont: contractId,
                id_md_area: areaId,
                tgl_jo_cont: tglJo,
                title: title,
                note: note,
                _token: $('input[name="_token"]').val()
            };

            showFloatingAlert('saving', 'Saving header...');

            $.ajax({
                url: '/jo-contract/header/store',
                method: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Full Response:', response);
                    console.log('Redirect URL:', response.redirect_url);

                    if (response.success) {
                        showFloatingAlert('success', 'saved! Redirecting...');

                        // Cek apakah redirect_url ada
                        if (response.redirect_url) {
                            console.log('Redirecting to:', response.redirect_url);

                            // Redirect setelah 1 detik (biar user sempat lihat success message)
                            setTimeout(() => {
                                window.location.href = response.redirect_url;
                            }, 1000);
                        } else {
                            console.error('No redirect_url in response');
                            showFloatingAlert('error', 'saved but redirect URL missing');
                        }
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Failed to save';
                    showFloatingAlert('error', message);
                    console.error('Error:', xhr);
                }
            });
        }

        // ========================================
        // CUSTOM CONFIRM DIALOG FUNCTION
        // ========================================
        function showConfirmDialog(message, onConfirm, onCancel) {
            // Create overlay
            const overlay = $('<div>')
                .css({
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    right: 0,
                    bottom: 0,
                    backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    zIndex: 9998,
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center'
                })
                .appendTo('body');

            // Create dialog
            const dialog = $(`
        <div style="
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            z-index: 9999;
        ">
            <div style="
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 20px;
            ">
                <i class="fas fa-question-circle" style="
                    font-size: 2rem;
                    color: #f59e0b;
                "></i>
                <div style="
                    flex: 1;
                    font-size: 1rem;
                    color: #2c3e50;
                    font-weight: 500;
                ">${message}</div>
            </div>
            <div style="
                display: flex;
                gap: 10px;
                justify-content: flex-end;
            ">
                <button class="btn-confirm-no" style="
                    background: #6c757d;
                    color: white;
                    border: none;
                    padding: 10px 25px;
                    border-radius: 8px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s;
                ">Cancel</button>
                <button class="btn-confirm-yes" style="
                    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    color: white;
                    border: none;
                    padding: 10px 25px;
                    border-radius: 8px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s;
                    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
                ">Yes, Proceed</button>
            </div>
        </div>
    `).appendTo(overlay);

            // Button hover effects
            dialog.find('.btn-confirm-yes').hover(
                function() {
                    $(this).css('transform', 'translateY(-2px)');
                },
                function() {
                    $(this).css('transform', 'translateY(0)');
                }
            );

            dialog.find('.btn-confirm-no').hover(
                function() {
                    $(this).css('background', '#5a6268');
                },
                function() {
                    $(this).css('background', '#6c757d');
                }
            );

            // Handle Yes
            dialog.find('.btn-confirm-yes').on('click', function() {
                overlay.remove();
                if (onConfirm) onConfirm();
            });

            // Handle No
            dialog.find('.btn-confirm-no').on('click', function() {
                overlay.remove();
                if (onCancel) onCancel();
            });

            // Handle click outside
            overlay.on('click', function(e) {
                if (e.target === this) {
                    overlay.remove();
                    if (onCancel) onCancel();
                }
            });
        }

        // ========================================
        // ITEMS MANAGEMENT (LANJUTAN DI PART 2)
        // ========================================
        // [Kode items management akan dilanjutkan di part 2]

        // Disable items card initially
        $(document).ready(function() {
            if (!isHeaderSaved) {
                $('#itemsCard').addClass('items-card-disabled').css('position', 'relative');
            }
        });
    </script>
@endpush
