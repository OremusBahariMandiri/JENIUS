@extends('layouts.app')

@section('title', 'Add JO Other')

@push('styles')
    <style>
        .joOtherCreatePage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .joOtherCreatePage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .joOtherCreatePage .form-control:focus,
        .joOtherCreatePage .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .joOtherCreatePage .form-label {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .joOtherCreatePage .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .joOtherCreatePage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .joOtherCreatePage .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .joOtherCreatePage .btn-success:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: 600;
        }

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

        .table-items {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        .table-items thead th {
            background-color: #f8f9fa;
            color: #2c3e50;
            border: 1px solid #dee2e6;
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
        }

        .table-items tbody tr:hover td:not(.category-cell) {
            background-color: #f8f9fa;
        }

        /* Category Cell Styling - MERGED CELL */
        .category-cell {
            background: #f1f5f9 !important;
            color: #2c3e50 !important;
            font-weight: 600;
            font-size: 0.875rem;
            border-right: 2px solid #dee2e6 !important;
            text-align: center;
            vertical-align: middle;
            position: relative;
            padding: 10px !important;
        }

        .category-cell .category-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }

        .category-cell .category-name {
            font-weight: 700;
            font-size: 0.85rem;
            color: #1e293b;
        }

        .category-cell .category-count {
            background: #e2e8f0;
            color: #475569;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .category-cell .btn-remove-category {
            background: #dc3545;
            border: none;
            color: white;
            border-radius: 6px;
            width: 100%;
            max-width: 70px;
            padding: 4px 8px;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .category-cell .btn-remove-category:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        .table-items input,
        .table-items select {
            font-size: 0.875rem;
            padding: 0.5rem;
            border-radius: 6px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }

        .table-items input:focus,
        .table-items select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .currency-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .currency-label {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #2c3e50;
            padding: 0.5rem;
            font-size: 0.8rem;
            border-radius: 6px 0 0 6px;
            min-width: 50px;
            text-align: center;
            font-weight: 600;
        }

        .currency-input {
            border-radius: 0 6px 6px 0 !important;
            border-left: none !important;
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

        .table-footer .currency-label {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
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

        .item-number-cell {
            text-align: center;
            font-weight: 600;
            color: #2c3e50;
            background-color: #ecf0f1 !important;
            font-size: 0.9rem;
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

        /* BTN ADD TO TABLE */
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

        /* TABLE ITEMS */
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
            color: #000;
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

        .btn-edit-row {
            padding: 0.4rem 0.6rem;
            font-size: 0.875rem;
            border-radius: 6px;
            transition: all 0.3s;
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

    <div class="container-fluid joOtherCreatePage">
        <div class="row">
            <div class="col-lg-12">

                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add JO Other</span>
                        <div class="d-flex gap-2 align-items-center">
                            <span id="autoSaveStatus" class="badge bg-secondary"><i class="fas fa-circle"></i> Not
                                Saved</span>
                            <a href="{{ route('jo-other.index') }}" class="btn btn-light btn-sm"><i
                                    class="fas fa-arrow-left me-1"></i> Back</a>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="current_jo_other_id" value="">
                <input type="hidden" id="is_header_saved" value="false">

                <form id="joOtherForm">@csrf

                    {{-- Header Card --}}
                    <div class="card shadow mb-4">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>JO Other Information</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="row mb-3">
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
                                        <label class="form-label required-field">JO Date</label>
                                        <input type="date" name="tgl_jo_other" id="tgl_jo_other" class="form-control"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Customer</label>
                                    <select name="id_md_cust" id="id_md_cust" class="form-select select2"
                                        data-placeholder="Search Customer...">
                                        <option value=""></option>
                                        @foreach ($customers as $c)
                                            <option value="{{ $c->id_md_cust }}">{{ $c->customer }}@if ($c->no_customer)
                                                    ({{ $c->no_customer }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Other Type</label>
                                    <select name="id_md_other" id="id_md_other" class="form-select select2"
                                        data-placeholder="Search Other Type...">
                                        <option value=""></option>
                                        @foreach ($others as $o)
                                            <option value="{{ $o->id_md_other }}">{{ $o->other }}@if ($o->code)
                                                    ({{ $o->code }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Port</label>
                                    <select name="id_md_port" id="id_md_port" class="form-select select2"
                                        data-placeholder="Search Port...">
                                        <option value=""></option>
                                        @foreach ($ports as $p)
                                            <option value="{{ $p->id_md_port }}">{{ $p->name_port }}@if ($p->no_port)
                                                    ({{ $p->no_port }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Vessel</label>
                                    <select name="id_md_vessel" id="id_md_vessel" class="form-select select2"
                                        data-placeholder="Search Vessel...">
                                        <option value=""></option>
                                        @foreach ($vessels as $v)
                                            <option value="{{ $v->id_md_vessel }}">{{ $v->vessel_name }}@if ($v->no_imo)
                                                    (IMO: {{ $v->no_imo }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Start Date</label>
                                    <input type="date" name="date_start" id="date_start" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">End Date</label>
                                    <input type="date" name="date_end" id="date_end" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required-field">Title</label>
                                <input type="text" name="title" id="title" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea name="note" id="note" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-success" id="btnSaveHeader">
                                    <i class="fas fa-save me-1"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Items Card - disabled until header saved --}}
                    <div class="card shadow mb-4" id="itemsCard" style="position: relative;">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>JO Other Items</h6>
                                <div class="kurs-section d-flex gap-3 align-items-center mb-0"
                                    style="padding: 10px 15px;">
                                    <div>
                                        <label class="form-label mb-1">Kurs Date</label>
                                        <input type="date" id="global_tgl_kurs_usd"
                                            class="form-control form-control-sm" style="min-width: 150px;">
                                    </div>
                                    <div>
                                        <label class="form-label mb-1">Kurs Rate</label>
                                        <input type="text" id="global_kurs_usd_display"
                                            class="form-control form-control-sm" style="min-width: 130px;">
                                        <input type="hidden" id="global_kurs_usd" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="add-item-form-section">
                                <h6><i class="fas fa-plus-square"></i> Add New Item</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required-field">Category</label>
                                        <select id="input_category" class="form-select">
                                            <option value="">Select Category</option>
                                            @foreach ($invoices->groupBy('invoice_ctg') as $category => $ig)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required-field">Item</label>
                                        <select id="input_item" class="form-select" disabled>
                                            <option value="">Select category first</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Income (IDR)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_pendapatan_idr"
                                                class="form-control currency-input">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Income (USD)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">USD</span>
                                            <input type="text" id="input_pendapatan_usd"
                                                class="form-control currency-input">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">HPP (Ops Costs)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_hpp" class="form-control currency-input">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Selling Price (IDR)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_harga_jual"
                                                class="form-control currency-input" readonly
                                                style="background-color:#e9ecef;">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Note (Optional)</label>
                                        <textarea id="input_note" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-end">
                                        <button type="button" class="btn btn-add-to-table" id="btnAddToTable">
                                            <i class="fas fa-arrow-down"></i> Add to Table
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <button type="button" class="btn btn-danger" id="resetAllBtn">
                                    <i class="fas fa-trash-alt me-1"></i>Reset All Items
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-items table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:5%;">No</th>
                                            <th style="width:15%;">Category</th>
                                            <th style="width:15%;">Item</th>
                                            <th style="width:13%;">Income (IDR)</th>
                                            <th style="width:13%;">Income (USD)</th>
                                            <th style="width:13%;">HPP (Ops Costs)</th>
                                            <th style="width:13%;">Selling Price (IDR)</th>
                                            <th style="width:8%;">Action</th>
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
                                                <div class="currency-group-footer"><span
                                                        class="currency-label-footer">IDR</span><span class="value-footer"
                                                        id="footerTotalIDR">0,00</span></div>
                                            </td>
                                            <td>
                                                <div class="currency-group-footer"><span
                                                        class="currency-label-footer">USD</span><span class="value-footer"
                                                        id="footerTotalUSD">0,00</span></div>
                                            </td>
                                            <td>
                                                <div class="currency-group-footer"><span
                                                        class="currency-label-footer">IDR</span><span class="value-footer"
                                                        id="footerTotalHPP">0,00</span></div>
                                            </td>
                                            <td>
                                                <div class="currency-group-footer"><span
                                                        class="currency-label-footer">IDR</span><span class="value-footer"
                                                        id="footerTotalSelling">0,00</span></div>
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
                                <button type="button" class="btn disabled" id="btnGeneratePdf"
                                    style="padding:15px 40px; border-radius:12px; font-weight:700; font-size:1.1rem; opacity:0.55; cursor:not-allowed; border-color:red; background-color:rgb(255, 237, 237); color:red"
                                    title="Save header first to generate PDF">
                                    <i class="fas fa-file-pdf me-2"></i> Generate PDF
                                </button>
                                <small class="text-muted mt-1" id="pdfHintText">
                                    <i class="fas fa-info-circle me-1"></i>Save JO Other Information first
                                </small>
                            </div>
                            <a href="{{ route('jo-other.index') }}" class="btn btn-final-back" style="margin-top:-25px">
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
        let isHeaderSaved = false;
        let globalItemNumber = 0;

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

        // ── Floating Alert ─────────────────────────────────────
        function showFloatingAlert(type, message) {
            const alert = $('#floatingBadgeAlert');
            alert.removeClass('alert-saving alert-success alert-error hiding');
            if (type === 'saving') {
                alert.addClass('alert-saving');
                $('#alertIcon').attr('class', 'fas fa-circle-notch fa-spin');
            } else if (type === 'success') {
                alert.addClass('alert-success');
                $('#alertIcon').attr('class', 'fas fa-check-circle');
            } else {
                alert.addClass('alert-error');
                $('#alertIcon').attr('class', 'fas fa-exclamation-circle');
            }
            $('#alertText').text(message);
            alert.addClass('show');
            if (type !== 'saving') setTimeout(hideFloatingAlert, 3000);
        }

        function hideFloatingAlert() {
            const alert = $('#floatingBadgeAlert');
            alert.addClass('hiding');
            setTimeout(() => alert.removeClass('show hiding'), 400);
        }

        // ── Rupiah ─────────────────────────────────────────────
        function formatRupiah(value) {
            let number = value.replace(/[^\d,]/g, '').replace(/\./g, '');
            if (!number) return '';
            let [int, dec] = number.split(',');
            if (dec !== undefined && dec.length > 2) dec = dec.substring(0, 2);
            int = int.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return dec !== undefined ? int + ',' + dec : int + ',00';
        }

        function parseRupiah(value) {
            return parseFloat((value || '').replace(/\./g, '').replace(',', '.')) || 0;
        }

        function formatNumber(amount) {
            return Number(amount).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function setupRupiahInput(input) {
            input.addEventListener('input', function() {
                const cursorPos = this.selectionStart;
                const before = this.value.substring(0, cursorPos);
                this.value = formatRupiah(this.value);
                if (!before.includes(',')) {
                    const digits = before.replace(/\D/g, '').length;
                    let pos = 0,
                        cnt = 0;
                    for (let i = 0; i < this.value.length; i++) {
                        if (/\d/.test(this.value[i]) && ++cnt === digits) {
                            pos = i + 1;
                            break;
                        }
                    }
                    this.setSelectionRange(pos, pos);
                } else {
                    const cp = this.value.indexOf(',');
                    const dec = (before.split(',')[1] || '').length;
                    const np = cp + 1 + Math.min(dec, 2);
                    this.setSelectionRange(np, np);
                }
            });
            input.addEventListener('blur', function() {
                if (!this.value) return;
                if (!this.value.includes(',')) this.value += ',00';
                else {
                    const p = this.value.split(',');
                    if (!p[1] || !p[1].length) this.value = p[0] + ',00';
                    else if (p[1].length < 2) this.value = p[0] + ',' + p[1].padEnd(2, '0');
                }
            });
        }

        ['global_kurs_usd_display', 'input_pendapatan_idr', 'input_pendapatan_usd', 'input_hpp'].forEach(id => {
            const el = document.getElementById(id);
            if (el) setupRupiahInput(el);
        });

        // ── Disabled card until header saved ───────────────────
        $(document).ready(function() {
            $('#itemsCard').addClass('items-card-disabled');
        });

        // ── Save Header ────────────────────────────────────────
        $('#btnSaveHeader').on('click', function() {
            const custId = $('#id_md_cust').val();
            const otherId = $('#id_md_other').val();
            const portId = $('#id_md_port').val();
            const ds = $('#date_start').val();
            const de = $('#date_end').val();
            const title = $('#title').val();

            if (!custId || !otherId || !portId || !ds || !de || !title) {
                showFloatingAlert('error', 'Please fill all required fields');
                return;
            }

            showFloatingAlert('saving', 'Saving header...');
            $('#btnSaveHeader').prop('disabled', true);

            $.ajax({
                url: '{{ route('jo-other.header.store') }}',
                method: 'POST',
                data: {
                    id_md_cust: custId,
                    id_md_other: otherId,
                    id_md_port: portId,
                    id_md_vessel: $('#id_md_vessel').val(),
                    tgl_jo_other: $('#tgl_jo_other').val(),
                    date_start: ds,
                    date_end: de,
                    title: title,
                    note: $('#note').val(),
                    _token: $('input[name="_token"]').val()
                },
                success: function(r) {
                    if (r.success) {
                        showFloatingAlert('success', 'Saved! Redirecting...');
                        setTimeout(() => {
                            window.location.href = r.redirect_url;
                        }, 1000);
                    } else {
                        showFloatingAlert('error', r.message || 'Failed to save');
                        $('#btnSaveHeader').prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        const first = Object.values(errors)[0];
                        showFloatingAlert('error', Array.isArray(first) ? first[0] : first);
                    } else {
                        showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to save');
                    }
                    $('#btnSaveHeader').prop('disabled', false);
                }
            });
        });

        // ── Category / Item ────────────────────────────────────
        document.getElementById('input_category').addEventListener('change', function() {
            const itemSelect = document.getElementById('input_item');
            const invoices = invoicesByCategory[this.value] || [];
            itemSelect.innerHTML = '<option value="">Select Item</option>';
            invoices.forEach(inv => {
                itemSelect.innerHTML += `<option value="${inv.id}">${inv.type}</option>`;
            });
            itemSelect.disabled = !this.value;
        });

        // ── Selling Price ─────────────────────────────────────────
        function calculateHargaJual() {
            const kurs = parseRupiah($('#global_kurs_usd_display').val());
            const idr = parseRupiah($('#input_pendapatan_idr').val());
            const usd = parseRupiah($('#input_pendapatan_usd').val());
            let hj = 0;
            if (idr > 0) hj = idr;
            else if (usd > 0 && kurs > 0) hj = usd * kurs;
            $('#input_harga_jual').val(formatRupiah(hj.toFixed(2).replace('.', ',')));
        }

        $('#input_pendapatan_idr').on('input', function() {
            if (parseRupiah($(this).val()) > 0) $('#input_pendapatan_usd').val('');
            calculateHargaJual();
        });
        $('#input_pendapatan_usd').on('input', function() {
            if (parseRupiah($(this).val()) > 0) $('#input_pendapatan_idr').val('');
            calculateHargaJual();
        });
        $('#global_kurs_usd_display').on('input', function() {
            $('#global_kurs_usd').val(parseRupiah($(this).val()));
            calculateHargaJual();
        });

        // ── Add to Table (AJAX) ────────────────────────────────
        $('#btnAddToTable').on('click', function() {
            const category = $('#input_category').val();
            const itemId = $('#input_item').val();
            const itemText = $('#input_item option:selected').text();

            if (!category || !itemId) {
                showFloatingAlert('error', 'Please select category and item');
                return;
            }

            const pendapatanIDR = parseRupiah($('#input_pendapatan_idr').val());
            const pendapatanUSD = parseRupiah($('#input_pendapatan_usd').val());
            const hpp = parseRupiah($('#input_hpp').val());
            const hargaJual = parseRupiah($('#input_harga_jual').val());
            const kursRate = parseRupiah($('#global_kurs_usd_display').val()) || 0;
            const kursDate = $('#global_tgl_kurs_usd').val() || null;
            const note = $('#input_note').val();

            if (pendapatanUSD > 0 && (!kursDate || kursRate <= 0)) {
                showFloatingAlert('error', 'Kurs rate and date are required when USD is filled');
                return;
            }

            showFloatingAlert('saving', 'Adding item...');

            $.ajax({
                url: '/data/jo-other/item/store',
                method: 'POST',
                data: {
                    id_jo_other: $('#current_jo_other_id').val(),
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
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', 'Failed to add item');
                        return;
                    }
                    showFloatingAlert('success', 'Item added successfully!');
                    $('.no-items-row').remove();
                    insertRowWithCategoryGrouping(r.data.id_jo_other_item || r.data.id, category,
                        itemText, r.data);
                    updateGrandTotal();
                    clearItemForm();
                },
                error: function(xhr) {
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to add item');
                }
            });
        });

        function insertRowWithCategoryGrouping(itemId, category, itemText, data) {
            globalItemNumber++;
            let categoryExists = false,
                insertAfterRow = null;

            $('#itemsTableBody tr.item-row').each(function() {
                if ($(this).data('category') === category) {
                    categoryExists = true;
                    insertAfterRow = $(this);
                    const catCell = $(this).find('.category-cell');
                    if (catCell.length) catCell.attr('rowspan', parseInt(catCell.attr('rowspan') || 1) + 1);
                }
            });

            const actionBtns =
                `
            <button type="button" class="btn btn-primary btn-sm btn-edit-row" onclick="editItem('${itemId}')"><i class="fas fa-edit"></i></button>
            <button type="button" class="btn btn-danger btn-sm btn-remove-row" onclick="removeItem(this,'${itemId}')"><i class="fas fa-trash"></i></button>`;

            let newRow;
            if (categoryExists) {
                newRow = `<tr class="item-row" data-item-id="${itemId}" data-category="${category}">
                <td class="item-number-cell">${globalItemNumber}</td>
                <td class="item-text-cell">${itemText}</td>
                <td>${formatNumber(data.pendapatan_idr)}</td>
                <td>${formatNumber(data.pendapatan_usd)}</td>
                <td>${formatNumber(data.hpp_ops)}</td>
                <td>${formatNumber(data.hargajual_idr)}</td>
                <td class="text-center">${actionBtns}</td></tr>`;
                insertAfterRow.after(newRow);
            } else {
                newRow = `<tr class="item-row" data-item-id="${itemId}" data-category="${category}">
                <td class="item-number-cell">${globalItemNumber}</td>
                <td class="category-cell" rowspan="1">${category}</td>
                <td class="item-text-cell">${itemText}</td>
                <td>${formatNumber(data.pendapatan_idr)}</td>
                <td>${formatNumber(data.pendapatan_usd)}</td>
                <td>${formatNumber(data.hpp_ops)}</td>
                <td>${formatNumber(data.hargajual_idr)}</td>
                <td class="text-center">${actionBtns}</td></tr>`;
                $('#itemsTableBody').append(newRow);
            }
            renumberAllItems();
        }

        function clearItemForm() {
            $('#input_category').val('');
            $('#input_item').prop('disabled', true).html('<option value="">Select category first</option>');
            $('#input_note').val('');
            ['input_pendapatan_idr', 'input_pendapatan_usd', 'input_hpp', 'input_harga_jual'].forEach(id => $('#' + id).val(
                ''));
        }

        // ── Edit Item ──────────────────────────────────────────
        function editItem(itemId) {
            // On create page, items are immediately in DB (realtime save)
            // so we redirect to edit page which has full edit capability
            showFloatingAlert('error', 'Please use the Edit page to modify items');
        }

        // ── Remove Item ────────────────────────────────────────
        function removeItem(button, itemId) {
            if (!confirm('Are you sure you want to delete this item?')) return;
            showFloatingAlert('saving', 'Deleting item...');
            $.ajax({
                url: `/data/jo-other/item/destroy/${itemId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', 'Failed to delete item');
                        return;
                    }
                    showFloatingAlert('success', 'Item deleted!');
                    const row = $(button).closest('tr');
                    const category = row.data('category');
                    removeRowFromTable(row, category);
                    renumberAllItems();
                    updateGrandTotal();
                    if ($('#itemsTableBody tr.item-row').length === 0) {
                        $('#itemsTableBody').html(
                            `<tr class="no-items-row"><td colspan="8"><i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i><p class="mb-0 fw-bold">No data available</p></td></tr>`
                        );
                    }
                },
                error: function() {
                    showFloatingAlert('error', 'Failed to delete item');
                }
            });
        }

        function removeRowFromTable(row, category) {
            const catCell = row.find('.category-cell');
            if (catCell.length) {
                const span = parseInt(catCell.attr('rowspan') || 1);
                if (span > 1) {
                    const next = row.next(`.item-row[data-category="${category}"]`);
                    if (next.length) next.find('.item-text-cell').before(
                        `<td class="category-cell" rowspan="${span - 1}">${category}</td>`);
                }
            } else {
                const prev = row.prevAll(`.item-row[data-category="${category}"]`).first();
                const prevCat = prev.find('.category-cell');
                if (prevCat.length) {
                    const s = parseInt(prevCat.attr('rowspan') || 1);
                    if (s > 1) prevCat.attr('rowspan', s - 1);
                }
            }
            row.remove();
        }

        // ── Reset All ──────────────────────────────────────────
        $('#resetAllBtn').on('click', function() {
            if (!confirm('Delete all items?')) return;
            const ids = [];
            $('.item-row').each(function() {
                const id = $(this).data('item-id');
                if (id) ids.push(id);
            });
            if (!ids.length) return;
            showFloatingAlert('saving', 'Deleting all items...');
            Promise.all(ids.map(id => $.ajax({
                    url: `/data/jo-other/item/destroy/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })))
                .then(() => {
                    showFloatingAlert('success', 'All items deleted!');
                    $('#itemsTableBody').html(
                        `<tr class="no-items-row"><td colspan="8"><i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i><p class="mb-0 fw-bold">No data available</p></td></tr>`
                    );
                    globalItemNumber = 0;
                    updateGrandTotal();
                });
        });

        // ── Utility ────────────────────────────────────────────
        function renumberAllItems() {
            $('.item-row').each(function(i) {
                $(this).find('.item-number-cell').text(i + 1);
                $(this).attr('data-item-number', i + 1);
            });
            globalItemNumber = $('.item-row').length;
        }

        function updateGrandTotal() {
            let idr = 0,
                usd = 0,
                hpp = 0,
                sell = 0;
            $('.item-row').each(function() {
                const hasCat = $(this).find('.category-cell').length > 0;
                const off = hasCat ? 0 : -1;
                idr += parseRupiah($(this).find('td').eq(3 + off).text());
                usd += parseRupiah($(this).find('td').eq(4 + off).text());
                hpp += parseRupiah($(this).find('td').eq(5 + off).text());
                sell += parseRupiah($(this).find('td').eq(6 + off).text());
            });
            $('#footerTotalIDR').text(formatNumber(idr));
            $('#footerTotalUSD').text(formatNumber(usd));
            $('#footerTotalHPP').text(formatNumber(hpp));
            $('#footerTotalSelling').text(formatNumber(sell));
        }
    </script>
@endpush
