@extends('layouts.app')

@section('title', 'Add JO Tramper')

@push('styles')
    <style>
        .joTramperCreatePage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .joTramperCreatePage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .joTramperCreatePage .form-control:focus,
        .joTramperCreatePage .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .joTramperCreatePage .form-label {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .joTramperCreatePage .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .joTramperCreatePage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .joTramperCreatePage .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .joTramperCreatePage .btn-success:hover {
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
    </style>
@endpush

@section('content')
    <!-- FLOATING BADGE ALERT -->
    <div id="floatingBadgeAlert" class="floating-badge-alert">
        <i class="fas fa-circle-notch fa-spin" id="alertIcon"></i>
        <div class="alert-text" id="alertText">Processing...</div>
    </div>

    <div class="container-fluid joTramperCreatePage">
        <div class="row">
            <div class="col-lg-12">
                <!-- Page Header Card -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add JO Tramper</span>
                        <div class="d-flex gap-2 align-items-center">
                            <span id="autoSaveStatus" class="badge bg-secondary">
                                <i class="fas fa-circle"></i> Not Saved
                            </span>
                            <a href="{{ route('jo-tramper.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Hidden State -->
                <input type="hidden" id="current_jo_tramper_id" value="">
                <input type="hidden" id="is_header_saved" value="false">

                <form id="joTramperForm">
                    @csrf

                    <!-- JO Tramper Info Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <h6 class="mb-0"><i class="fas fa-ship me-2"></i>JO Tramper Information</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <!-- No JO Tramper (readonly, auto-generate) -->
                                <div class="col-md-6">
                                    <label class="form-label">No. JO</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="fas fa-file-alt"></i>
                                        </span>
                                        <input type="text" class="form-control fw-bold" value="{{ $previewNoJot }}"
                                            readonly style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Document number, generated automatically
                                    </small>
                                </div>

                                <!-- Tanggal JO -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">JO Date</label>
                                    <input type="date" name="tgl_jo_tram" id="tgl_jo_tram" class="form-control"
                                        value="{{ old('tgl_jo_tram', date('Y-m-d')) }}">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label required-field">Customer</label>
                                    <select name="id_md_cust" id="id_md_cust"
                                        class="form-select select2 @error('id_md_cust') is-invalid @enderror"
                                        data-placeholder="Search Customer...">
                                        <option value="">Select Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id_md_cust }}"
                                                {{ old('id_md_cust') == $customer->id_md_cust ? 'selected' : '' }}>
                                                {{ $customer->customer }}
                                                @if ($customer->no_customer)
                                                    ({{ $customer->no_customer }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_md_cust')
                                        <div class="invalid-feedback"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Port</label>
                                    <select name="id_md_port" id="id_md_port"
                                        class="form-select select2 @error('id_md_port') is-invalid @enderror"
                                        data-placeholder="Search Port...">
                                        <option value="">Select Port</option>
                                        @foreach ($ports as $port)
                                            <option value="{{ $port->id_md_port }}"
                                                {{ old('id_md_port') == $port->id_md_port ? 'selected' : '' }}>
                                                {{ $port->name_port }}
                                                @if ($port->no_port)
                                                    ({{ $port->no_port }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_md_port')
                                        <div class="invalid-feedback"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Vessel</label>
                                    <select name="id_md_vessel" id="id_md_vessel" class="form-select select2"
                                        data-placeholder="Search Vessel...">
                                        <option value=""></option>
                                        @foreach ($vessels as $vessel)
                                            <option value="{{ $vessel->id_md_vessel }}"
                                                {{ old('id_md_vessel') == $vessel->id_md_vessel ? 'selected' : '' }}>
                                                {{ $vessel->vessel_name }}
                                                @if ($vessel->no_imo)
                                                    (IMO: {{ $vessel->no_imo }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Start Date</label>
                                    <input type="date" name="date_start" id="date_start"
                                        class="form-control @error('date_start') is-invalid @enderror"
                                        value="{{ old('date_start') }}">
                                    @error('date_start')
                                        <div class="invalid-feedback"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">End Date</label>
                                    <input type="date" name="date_end" id="date_end"
                                        class="form-control @error('date_end') is-invalid @enderror"
                                        value="{{ old('date_end') }}">
                                    @error('date_end')
                                        <div class="invalid-feedback"><i
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required-field">Title</label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title') }}">
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

                    <!-- JO Tramper Items Card (disabled until header saved) -->
                    <div class="card shadow mb-4" id="itemsCard" style="position: relative;">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>JO Tramper Items</h6>
                                <div class="kurs-section d-flex gap-3 align-items-center mb-0"
                                    style="padding: 10px 15px;">
                                    <div>
                                        <label class="form-label mb-1">Kurs Date</label>
                                        <input type="date" name="global_tgl_kurs_usd" id="global_tgl_kurs_usd"
                                            class="form-control form-control-sm" style="min-width: 150px;">
                                    </div>
                                    <div>
                                        <label class="form-label mb-1">Kurs Rate</label>
                                        <input type="text" id="global_kurs_usd_display"
                                            class="form-control form-control-sm" style="min-width: 130px;">
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
                                    <!-- Category -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required-field">Category</label>
                                        <select id="input_category" class="form-select">
                                            <option value="">Select Category</option>
                                            @foreach ($invoices->groupBy('invoice_ctg') as $category => $invoiceGroup)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Item -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required-field">Item</label>
                                        <select id="input_item" class="form-select" disabled>
                                            <option value="">Select category first</option>
                                        </select>
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

                                    <!-- HPP -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">HPP (Ops Costs)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_hpp" class="form-control currency-input">
                                        </div>
                                    </div>

                                    <!-- Selling Price -->
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

                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <button type="button" class="btn btn-danger" id="resetAllBtn">
                                    <i class="fas fa-trash-alt me-1"></i>Reset All Items
                                </button>
                            </div>

                            <!-- Table -->
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
                        </div>
                    </div>
                </form>

                <!-- FINAL SAVE SECTION -->
                <div class="final-save-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div></div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="d-flex flex-column align-items-center">
                                <button type="button" class="btn btn-danger disabled" id="btnGeneratePdf"
                                    style="padding:15px 40px; border-radius:12px; font-weight:700; font-size:1.1rem; opacity:0.55; cursor:not-allowed; border-color:red; background-color:rgb(255, 237, 237); color:red"
                                    title="Save header first to generate PDF">
                                    <i class="fas fa-file-pdf me-2"></i> Generate Invoice
                                </button>
                                <small class="text-muted mt-1" id="pdfHintText">
                                    <i class="fas fa-info-circle me-1"></i>Save Jo Tramper Information first
                                </small>
                            </div>
                            <a href="{{ route('jo-tramper.index') }}"
                               class="btn btn-final-back"
                               style="padding:15px 40px; border-radius:12px; font-weight:700; font-size:1.1rem; margin-top:-25px">
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
        let isHeaderSaved = false;
        let itemsData = [];
        let globalItemNumber = 0;

        // Invoice data by category (from Blade)
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
        // RUPIAH FORMATTING
        // ========================================
        function formatRupiah(value) {
            let number = value.replace(/[^\d,]/g, '').replace(/\./g, '');
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
            return parseFloat((value || '').replace(/\./g, '').replace(',', '.')) || 0;
        }

        function formatNumber(amount) {
            return amount.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function setupRupiahInput(input) {
            input.addEventListener('input', function() {
                const cursorPos = this.selectionStart;
                const beforeCursor = this.value.substring(0, cursorPos);
                const formatted = formatRupiah(this.value);
                this.value = formatted;

                if (!beforeCursor.includes(',')) {
                    const digitsBeforeCursor = beforeCursor.replace(/\D/g, '').length;
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
                    const commaPos = this.value.indexOf(',');
                    const decDigits = (beforeCursor.split(',')[1] || '').length;
                    const newPos = commaPos + 1 + Math.min(decDigits, 2);
                    this.setSelectionRange(newPos, newPos);
                }
            });

            input.addEventListener('blur', function() {
                if (this.value) {
                    if (!this.value.includes(',')) {
                        this.value = this.value + ',00';
                    } else {
                        const parts = this.value.split(',');
                        if (!parts[1] || parts[1].length === 0) this.value = parts[0] + ',00';
                        else if (parts[1].length < 2) this.value = parts[0] + ',' + parts[1].padEnd(2, '0');
                    }
                }
            });
        }

        // Setup all inputs
        setupRupiahInput(document.getElementById('global_kurs_usd_display'));
        setupRupiahInput(document.getElementById('input_pendapatan_idr'));
        setupRupiahInput(document.getElementById('input_pendapatan_usd'));
        setupRupiahInput(document.getElementById('input_hpp'));

        // ========================================
        // ITEMS CARD DISABLED STATE
        // ========================================
        $(document).ready(function() {
            $('#itemsCard').addClass('items-card-disabled');
        });

        function enableItemsCard() {
            $('#itemsCard').removeClass('items-card-disabled');
            isHeaderSaved = true;
            $('#is_header_saved').val('true');
        }

        // ========================================
        // SAVE HEADER (AJAX) → REDIRECT TO EDIT
        // ========================================
        $('#btnSaveHeader').on('click', function() {
            saveHeaderToDatabase();
        });

        function saveHeaderToDatabase() {
            const custId = $('#id_md_cust').val();
            const portId = $('#id_md_port').val();
            const dateStart = $('#date_start').val();
            const dateEnd = $('#date_end').val();
            const title = $('#title').val();
            const note = $('#note').val();

            if (!custId || !portId || !dateStart || !dateEnd || !title) {
                showFloatingAlert('error', 'Please fill all required fields');
                return;
            }

            const formData = {
                id_md_cust: custId,
                id_md_port: portId,
                id_md_vessel: $('#id_md_vessel').val(), // tambah ini
                tgl_jo_tram: $('#tgl_jo_tram').val(),
                date_start: dateStart,
                date_end: dateEnd,
                title: title,
                note: note,
                _token: $('input[name="_token"]').val()
            };

            showFloatingAlert('saving', 'Saving header...');
            $('#btnSaveHeader').prop('disabled', true);

            $.ajax({
                url: '{{ route('jo-tramper.header.store') }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showFloatingAlert('success', 'Saved! Redirecting...');

                        if (response.redirect_url) {
                            setTimeout(() => {
                                window.location.href = response.redirect_url;
                            }, 1000);
                        } else {
                            showFloatingAlert('error', 'Saved but redirect URL missing');
                            $('#btnSaveHeader').prop('disabled', false);
                        }
                    } else {
                        showFloatingAlert('error', response.message || 'Failed to save');
                        $('#btnSaveHeader').prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    const message = xhr.responseJSON?.message || 'Failed to save';

                    if (errors) {
                        // Show first validation error
                        const firstError = Object.values(errors)[0];
                        showFloatingAlert('error', Array.isArray(firstError) ? firstError[0] : firstError);
                    } else {
                        showFloatingAlert('error', message);
                    }

                    $('#btnSaveHeader').prop('disabled', false);
                    console.error('Save header error:', xhr);
                }
            });
        }

        // ========================================
        // CATEGORY & ITEM SELECTION
        // ========================================
        document.getElementById('input_category').addEventListener('change', function() {
            const category = this.value;
            const itemSelect = document.getElementById('input_item');
            const invoices = invoicesByCategory[category] || [];

            itemSelect.innerHTML = '<option value="">Select Item</option>';
            invoices.forEach(inv => {
                itemSelect.innerHTML += `<option value="${inv.id}">${inv.type}</option>`;
            });
            itemSelect.disabled = !category;
        });

        // ========================================
        // AUTO-CALCULATE Selling Price
        // ========================================
        function calculateHargaJual() {
            const kurs = parseRupiah(document.getElementById('global_kurs_usd_display').value);
            const idr = parseRupiah(document.getElementById('input_pendapatan_idr').value);
            const usd = parseRupiah(document.getElementById('input_pendapatan_usd').value);
            const hpp = parseRupiah(document.getElementById('input_hpp').value);

            let hargaJual = 0;
            if (idr > 0) {
                hargaJual = idr + hpp;
            } else if (usd > 0) {
                hargaJual = (usd * kurs) + hpp;
            } else {
                hargaJual = hpp;
            }

            document.getElementById('input_harga_jual').value = formatNumber(hargaJual);
        }

        document.getElementById('input_pendapatan_idr').addEventListener('input', function() {
            if (parseRupiah(this.value) > 0) {
                document.getElementById('input_pendapatan_usd').value = '';
            }
            calculateHargaJual();
        });

        document.getElementById('input_pendapatan_usd').addEventListener('input', function() {
            if (parseRupiah(this.value) > 0) {
                document.getElementById('input_pendapatan_idr').value = '';
            }
            calculateHargaJual();
        });

        document.getElementById('input_hpp').addEventListener('input', calculateHargaJual);
        document.getElementById('global_kurs_usd_display').addEventListener('input', function() {
            document.getElementById('global_kurs_usd').value = parseRupiah(this.value);
            calculateHargaJual();
            recalculateAllRows();
        });

        // ========================================
        // ADD ITEM TO TABLE
        // ========================================
        document.getElementById('btnAddToTable').addEventListener('click', function() {
            const categoryEl = document.getElementById('input_category');
            const itemEl = document.getElementById('input_item');

            if (!categoryEl.value) {
                showFloatingAlert('error', 'Please select a category');
                return;
            }
            if (!itemEl.value) {
                showFloatingAlert('error', 'Please select an item');
                return;
            }

            const idr = parseRupiah(document.getElementById('input_pendapatan_idr').value);
            const usd = parseRupiah(document.getElementById('input_pendapatan_usd').value);
            const hpp = parseRupiah(document.getElementById('input_hpp').value);
            const hargaJual = parseRupiah(document.getElementById('input_harga_jual').value.replace(/\./g, '')
                .replace(',', '.'));

            globalItemNumber++;

            const noItemsRow = document.querySelector('.no-items-row');
            if (noItemsRow) noItemsRow.remove();

            const tbody = document.getElementById('itemsTableBody');
            const newRow = document.createElement('tr');
            newRow.setAttribute('data-item-number', globalItemNumber);
            newRow.setAttribute('data-invoice-id', itemEl.value);
            newRow.setAttribute('data-category', categoryEl.value);
            newRow.setAttribute('data-idr', idr);
            newRow.setAttribute('data-usd', usd);
            newRow.setAttribute('data-hpp', hpp);
            newRow.setAttribute('data-hargajual', hargaJual);

            newRow.innerHTML = `
                <td class="item-number-cell">${globalItemNumber}</td>
                <td class="category-cell">${categoryEl.value}</td>
                <td>${itemEl.options[itemEl.selectedIndex].text}</td>
                <td>${idr > 0 ? formatNumber(idr) : '-'}</td>
                <td>${usd > 0 ? formatNumber(usd) : '-'}</td>
                <td>${hpp > 0 ? formatNumber(hpp) : '-'}</td>
                <td>${formatNumber(hargaJual)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-row" onclick="removeItem(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(newRow);

            // Store hidden inputs for form submission
            addHiddenInputs(globalItemNumber, itemEl.value, categoryEl.value, idr, usd, hpp, hargaJual);

            // Reset form inputs
            categoryEl.value = '';
            document.getElementById('input_item').innerHTML = '<option value="">Select category first</option>';
            document.getElementById('input_item').disabled = true;
            document.getElementById('input_pendapatan_idr').value = '';
            document.getElementById('input_pendapatan_usd').value = '';
            document.getElementById('input_hpp').value = '';
            document.getElementById('input_harga_jual').value = '';

            updateGrandTotal();
        });

        function addHiddenInputs(index, invoiceId, category, idr, usd, hpp, hargaJual) {
            const form = document.getElementById('joTramperForm');

            const fields = [{
                    name: `items[${index}][id_md_invoice]`,
                    value: invoiceId
                },
                {
                    name: `items[${index}][invoice_ctg]`,
                    value: category
                },
                {
                    name: `items[${index}][pendapatan_idr]`,
                    value: idr
                },
                {
                    name: `items[${index}][pendapatan_usd]`,
                    value: usd
                },
                {
                    name: `items[${index}][hpp_ops]`,
                    value: hpp
                },
                {
                    name: `items[${index}][hargajual_idr]`,
                    value: hargaJual
                },
            ];

            fields.forEach(f => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = f.name;
                input.value = f.value;
                input.setAttribute('data-row-index', index);
                form.appendChild(input);
            });
        }

        // ========================================
        // REMOVE ITEM
        // ========================================
        function removeItem(button) {
            const row = button.closest('tr');
            const index = row.getAttribute('data-item-number');

            // Remove hidden inputs for this row
            document.querySelectorAll(`[data-row-index="${index}"]`).forEach(el => el.remove());

            row.remove();
            renumberAllItems();
            updateGrandTotal();

            const tbody = document.getElementById('itemsTableBody');
            if (tbody.querySelectorAll('tr').length === 0) {
                tbody.innerHTML = `
                    <tr class="no-items-row">
                        <td colspan="8">
                            <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                            <p class="mb-0 fw-bold">No data available</p>
                            <small class="text-muted">Fill the form above and click "Add to Table"</small>
                        </td>
                    </tr>
                `;
            }
        }

        // ========================================
        // RESET ALL
        // ========================================
        document.getElementById('resetAllBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to remove all items?')) {
                document.getElementById('itemsTableBody').innerHTML = `
                    <tr class="no-items-row">
                        <td colspan="8">
                            <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                            <p class="mb-0 fw-bold">No data available</p>
                            <small class="text-muted">Fill the form above and click "Add to Table"</small>
                        </td>
                    </tr>
                `;
                // Remove all hidden item inputs
                document.querySelectorAll('[data-row-index]').forEach(el => el.remove());
                globalItemNumber = 0;
                updateGrandTotal();
            }
        });

        // ========================================
        // RENUMBER ITEMS
        // ========================================
        function renumberAllItems() {
            const rows = document.querySelectorAll('#itemsTableBody tr[data-item-number]');
            rows.forEach((row, index) => {
                row.querySelector('.item-number-cell').textContent = index + 1;
                row.setAttribute('data-item-number', index + 1);
            });
            globalItemNumber = rows.length;
        }

        // ========================================
        // RECALCULATE ALL ROWS WHEN KURS CHANGES
        // ========================================
        function recalculateAllRows() {
            const kurs = parseRupiah(document.getElementById('global_kurs_usd_display').value);
            const rows = document.querySelectorAll('#itemsTableBody tr[data-item-number]');

            rows.forEach(row => {
                const idr = parseFloat(row.getAttribute('data-idr')) || 0;
                const usd = parseFloat(row.getAttribute('data-usd')) || 0;
                const hpp = parseFloat(row.getAttribute('data-hpp')) || 0;
                let hargaJual = 0;

                if (idr > 0) {
                    hargaJual = idr + hpp;
                } else if (usd > 0) {
                    hargaJual = (usd * kurs) + hpp;
                } else {
                    hargaJual = hpp;
                }

                row.setAttribute('data-hargajual', hargaJual);
                row.cells[6].textContent = formatNumber(hargaJual);

                // Update hidden input too
                const index = row.getAttribute('data-item-number');
                const hiddenInput = document.querySelector(`input[name="items[${index}][hargajual_idr]"]`);
                if (hiddenInput) hiddenInput.value = hargaJual;
            });

            updateGrandTotal();
        }

        // ========================================
        // GRAND TOTAL
        // ========================================
        function updateGrandTotal() {
            const rows = document.querySelectorAll('#itemsTableBody tr[data-item-number]');
            let totalIDR = 0,
                totalUSD = 0,
                totalHPP = 0,
                totalSelling = 0;

            rows.forEach(row => {
                totalIDR += parseFloat(row.getAttribute('data-idr')) || 0;
                totalUSD += parseFloat(row.getAttribute('data-usd')) || 0;
                totalHPP += parseFloat(row.getAttribute('data-hpp')) || 0;
                totalSelling += parseFloat(row.getAttribute('data-hargajual')) || 0;
            });

            document.getElementById('footerTotalIDR').textContent = formatNumber(totalIDR);
            document.getElementById('footerTotalUSD').textContent = formatNumber(totalUSD);
            document.getElementById('footerTotalHPP').textContent = formatNumber(totalHPP);
            document.getElementById('footerTotalSelling').textContent = formatNumber(totalSelling);
        }
    </script>
@endpush
