@extends('layouts.app')

@section('title', 'Edit JO Tramper')

@push('styles')
    <style>
        .joTramperEditPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .joTramperEditPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .joTramperEditPage .form-control:focus,
        .joTramperEditPage .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .joTramperEditPage .form-label {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .joTramperEditPage .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .joTramperEditPage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .joTramperEditPage .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .joTramperEditPage .btn-success:hover {
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

        .btn-cancel-edit-style {
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

        .btn-cancel-edit-style:hover {
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

        .table-items tbody td.num-cell {
            text-align: right;
        }

        .table-items tbody td.item-text-cell,
        .table-items tbody td.category-cell,
        .table-items tbody td.item-number-cell {
            text-align: left;
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
            padding-right: 10px;
            text-align: right;
        }

        .no-items-row td {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
            background: #f8f9fa !important;
        }

        /* FINAL SAVE */
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

    <div class="container-fluid joTramperEditPage">
        <div class="row">
            <div class="col-lg-12">

                <!-- Page Header Card -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit JO Tramper</span>
                    </div>
                </div>

                <!-- Hidden State -->
                <input type="hidden" id="current_jo_tramper_id" value="{{ $joTramper->id_jo_tram }}">

                <form id="joTramperForm">
                    @csrf

                    <!-- JO Tramper Info Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <h6 class="mb-0"><i class="fas fa-ship me-2"></i>JO Tramper Information</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label required-field">Customer</label>
                                    <select name="id_md_cust" id="id_md_cust" class="form-select select2"
                                        data-placeholder="Search Customer...">
                                        <option value=""></option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id_md_cust }}"
                                                {{ $joTramper->id_md_cust == $customer->id_md_cust ? 'selected' : '' }}>
                                                {{ $customer->customer }}
                                                @if ($customer->no_customer)
                                                    ({{ $customer->no_customer }})
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
                                        @foreach ($ports as $port)
                                            <option value="{{ $port->id_md_port }}"
                                                {{ $joTramper->id_md_port == $port->id_md_port ? 'selected' : '' }}>
                                                {{ $port->name_port }}
                                                @if ($port->no_port)
                                                    ({{ $port->no_port }})
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
                                        @foreach ($vessels as $vessel)
                                            <option value="{{ $vessel->id_md_vessel }}"
                                                {{ $joTramper->id_md_vessel == $vessel->id_md_vessel ? 'selected' : '' }}>
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
                                    <input type="date" name="date_start" id="date_start" class="form-control"
                                        value="{{ $joTramper->date_start ? $joTramper->date_start->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">End Date</label>
                                    <input type="date" name="date_end" id="date_end" class="form-control"
                                        value="{{ $joTramper->date_end ? $joTramper->date_end->format('Y-m-d') : '' }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required-field">Title</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ $joTramper->title }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea name="note" id="note" class="form-control" rows="3">{{ $joTramper->note }}</textarea>
                            </div>

                            <!-- Save Header Button -->
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-success" id="btnSaveHeader">
                                    <i class="fas fa-save me-1"></i> Update
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- JO Tramper Items Card -->
                    <div class="card shadow mb-4" id="itemsCard">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>JO Tramper Items</h6>
                                <!-- KURS SECTION -->
                                <div class="kurs-section d-flex gap-3 align-items-center mb-0" style="padding: 10px 15px;">
                                    <div>
                                        <label class="form-label mb-1">Kurs Date</label>
                                        @php
                                            $firstItem = $joTramper->items->first();
                                            $defaultDate =
                                                $firstItem && $firstItem->tgl_kurs_usd
                                                    ? $firstItem->tgl_kurs_usd->format('Y-m-d\TH:i')
                                                    : '';
                                        @endphp
                                        <input type="datetime-local" id="global_tgl_kurs_usd"
                                            class="form-control form-control-sm" value="{{ $defaultDate }}"
                                            style="min-width: 190px;">
                                    </div>
                                    <div>
                                        <label class="form-label mb-1">Kurs Rate</label>
                                        @php $defaultKurs = $firstItem && $firstItem->kurs_usd ? $firstItem->kurs_usd : 0; @endphp
                                        <input type="text" id="global_kurs_usd_display"
                                            class="form-control form-control-sm"
                                            value="{{ $defaultKurs > 0 ? number_format($defaultKurs, 2, ',', '.') : '' }}"
                                            style="min-width: 130px;">
                                        <input type="hidden" id="global_kurs_usd" value="{{ $defaultKurs }}">
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
                                    <!-- Category -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label required-field">Category</label>
                                        <select id="input_category" class="form-select select2"
                                            data-placeholder="Select Category" data-allow-clear="false">
                                            <option value=""></option>
                                            @foreach ($invoices->groupBy('invoice_ctg') as $category => $invoiceGroup)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Item -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label required-field">Item</label>
                                        <select id="input_item" class="form-select select2" disabled
                                            data-placeholder="Select category first" data-allow-clear="false">
                                            <option value=""></option>
                                        </select>
                                    </div>

                                    <!-- Note -->
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
                                                class="form-control currency-input" readonly
                                                style="background-color:#e9ecef;">
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="col-md-12 mb-3 d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-add-to-table" id="btnAddToTable">
                                            <i class="fas fa-arrow-down"></i> Add to Table
                                        </button>
                                        <button type="button" class="btn btn-cancel-edit-style" id="btnCancelEdit"
                                            style="display: none;">
                                            <i class="fas fa-times"></i> Cancel Edit
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- TABLE DISPLAY -->
                            <div class="table-responsive">
                                <table class="table table-items table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:5%;">No</th>
                                            <th style="width:13%;">Category</th>
                                            <th style="width:13%;">Item</th>
                                            <th style="width:12%;">Pendapatan IDR</th>
                                            <th style="width:12%;">Pendapatan USD</th>
                                            <th style="width:12%;">HPP (Biaya Ops)</th>
                                            <th style="width:12%;">Harga Jual (IDR)</th>
                                            <th style="width:11%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        @if ($joTramper->items->count() > 0)
                                            @php
                                                $groupedItems = $joTramper->items->groupBy(
                                                    fn($i) => $i->invoice->invoice_ctg,
                                                );
                                                $globalIndex = 1;
                                            @endphp
                                            @foreach ($groupedItems as $category => $items)
                                                @foreach ($items as $index => $item)
                                                    <tr class="item-row" data-item-id="{{ $item->id_jo_tram_item }}"
                                                        data-invoice-id="{{ $item->id_md_invoice }}"
                                                        data-category="{{ $item->invoice->invoice_ctg }}"
                                                        data-item-text="{{ $item->invoice->invoice_typ }}"
                                                        data-item-number="{{ $globalIndex }}">
                                                        <td class="item-number-cell">{{ $globalIndex }}</td>
                                                        @if ($index === 0)
                                                            <td class="category-cell" rowspan="{{ $items->count() }}">
                                                                {{ $category }}</td>
                                                        @endif
                                                        <td class="item-text-cell">{{ $item->invoice->invoice_typ }}</td>
                                                        <td class="num-cell">
                                                            {{ number_format($item->pendapatan_idr, 2, ',', '.') }}</td>
                                                        <td class="num-cell">
                                                            {{ number_format($item->pendapatan_usd, 2, ',', '.') }}</td>
                                                        <td class="num-cell">
                                                            {{ number_format($item->hpp_ops, 2, ',', '.') }}</td>
                                                        <td class="num-cell">
                                                            {{ number_format($item->hargajual_idr, 2, ',', '.') }}</td>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-primary btn-sm btn-edit-row"
                                                                onclick="editItem('{{ $item->id_jo_tram_item }}')">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm btn-remove-row"
                                                                onclick="removeItem(this, '{{ $item->id_jo_tram_item }}')">
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

                            <div class="d-flex justify-content-end mt-2">
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
                        {{-- <div>
                            <h6 class="mb-1"><i class="fas fa-info-circle me-2"></i>Ready to Save?</h6>
                            <small class="text-muted">Click the button to save all changes permanently</small>
                        </div> --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('jo-tramper.index') }}" class="btn btn-final-back">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            {{-- <button type="button" class="btn btn-final-save" id="btnFinalSave">
                                <i class="fas fa-save"></i> Save All Changes
                            </button> --}}
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
        const currentJoTramperId = '{{ $joTramper->id_jo_tram }}';
        let globalItemNumber = {{ $joTramper->items->count() }};

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
                setTimeout(hideFloatingAlert, 3000);
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
            if (!number) return '';
            let [int, dec] = number.split(',');
            if (dec !== undefined && dec.length > 2) dec = dec.substring(0, 2);
            int = int.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return (dec !== undefined) ? int + ',' + dec : int + ',00';
        }

        function parseRupiah(value) {
            if (!value) return 0;
            return parseFloat(value.toString().replace(/\./g, '').replace(',', '.')) || 0;
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
                if (!this.value.includes(',')) {
                    this.value += ',00';
                } else {
                    const p = this.value.split(',');
                    if (!p[1] || p[1].length === 0) this.value = p[0] + ',00';
                    else if (p[1].length < 2) this.value = p[0] + ',' + p[1].padEnd(2, '0');
                }
            });
        }

        // Setup all rupiah inputs
        ['global_kurs_usd_display', 'input_pendapatan_idr', 'input_pendapatan_usd', 'input_hpp'].forEach(id => {
            const el = document.getElementById(id);
            if (el) setupRupiahInput(el);
        });

        // ========================================
        // AUTO-CALCULATE HARGA JUAL
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
            if (parseRupiah($(this).val()) > 0) $('#input_pendapatan_usd').val('');
            calculateHargaJual();
        });

        $('#input_pendapatan_usd').on('input', function() {
            if (parseRupiah($(this).val()) > 0) $('#input_pendapatan_idr').val('');
            calculateHargaJual();
        });

        $('#input_hpp').on('input', calculateHargaJual);

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
            invoices.forEach(inv => {
                options += `<option value="${inv.id}" data-note="${inv.note ?? ''}">${inv.type}</option>`;
            });
            itemSelect.prop('disabled', false).html(options);
        });

        // Isi note dari master saat item dipilih (hanya saat mode tambah baru)
        let _skipNoteUpdate = false;

        $('#input_item').on('change', function() {
            if (_skipNoteUpdate) return;
            const masterNote = $(this).find('option:selected').data('note') || '';
            $('#input_note').val(masterNote);
        });

        // ========================================
        // UPDATE HEADER
        // ========================================
        $('#btnSaveHeader').on('click', function() {
            const custId = $('#id_md_cust').val();
            const portId = $('#id_md_port').val();
            const title = $('#title').val();
            const ds = $('#date_start').val();
            const de = $('#date_end').val();
            const note = $('#note').val();

            if (!custId || !portId || !title || !ds || !de) {
                showFloatingAlert('error', 'Please fill all required fields');
                return;
            }

            showFloatingAlert('saving', 'Updating header...');

            $.ajax({
                url: `/data/jo-tramper/header/update/${currentJoTramperId}`,
                method: 'POST',
                data: {
                    id_md_cust: custId,
                    id_md_port: portId,
                    id_md_vessel: $('#id_md_vessel').val(), // tambah ini
                    date_start: ds,
                    date_end: de,
                    title: title,
                    note: note,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(r) {
                    if (r.success) {
                        showFloatingAlert('success', 'Header updated successfully!');
                    } else {
                        showFloatingAlert('error', r.message || 'Failed to update header');
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to update header';
                    showFloatingAlert('error', msg);
                }
            });
        });

        // ========================================
        // ADD / UPDATE ITEM TO TABLE
        // ========================================
        $('#btnAddToTable').on('click', function() {
            const editingId = $('#editing_item_id').val();
            const category = $('#input_category').val();
            const itemId = $('#input_item').val();
            const itemText = $('#input_item option:selected').text();
            const note = $('#input_note').val();
            const pendapatanIDR = parseRupiah($('#input_pendapatan_idr').val());
            const pendapatanUSD = parseRupiah($('#input_pendapatan_usd').val());
            const hpp = parseRupiah($('#input_hpp').val());
            const hargaJual = parseRupiah($('#input_harga_jual').val());
            const kursRate = parseRupiah($('#global_kurs_usd_display').val()) || 0;
            const kursDate = $('#global_tgl_kurs_usd').val() || null;

            if (!category || !itemId) {
                showFloatingAlert('error', 'Please select category and item');
                return;
            }

            if (pendapatanUSD > 0 && (!kursDate || kursRate <= 0)) {
                showFloatingAlert('error', 'Kurs rate and date are required when USD is filled');
                return;
            }

            if (editingId) {
                updateItemToDatabase(
                    editingId, category, itemId, itemText,
                    pendapatanIDR, pendapatanUSD, hpp, hargaJual,
                    kursRate, kursDate, note
                );
                return;
            }

            showFloatingAlert('saving', 'Adding item...');

            $.ajax({
                url: '/data/jo-tramper/item/store',
                method: 'POST',
                data: {
                    id_jo_tram: currentJoTramperId,
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

                    const newItemId = r.data.id_jo_tram_item || r.data.id;
                    if (!newItemId) {
                        showFloatingAlert('error', 'Item created but ID is invalid. Please refresh.');
                        return;
                    }

                    insertRowWithCategoryGrouping(newItemId, category, itemText, r.data);
                    updateGrandTotal();
                    clearItemForm();
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to add item';
                    showFloatingAlert('error', msg);
                }
            });
        });

        // ========================================
        // INSERT ROW WITH CATEGORY GROUPING
        // ========================================
        function insertRowWithCategoryGrouping(itemId, category, itemText, data) {
            globalItemNumber++;

            let categoryExists = false;
            let insertAfterRow = null;

            $('#itemsTableBody tr.item-row').each(function() {
                if ($(this).data('category') === category) {
                    categoryExists = true;
                    insertAfterRow = $(this);
                    const catCell = $(this).find('.category-cell');
                    if (catCell.length) {
                        const span = parseInt(catCell.attr('rowspan') || 1);
                        catCell.attr('rowspan', span + 1);
                    }
                }
            });

            const actionBtns = `
        <button type="button" class="btn btn-primary btn-sm btn-edit-row"
            onclick="editItem('${itemId}')"><i class="fas fa-edit"></i></button>
        <button type="button" class="btn btn-danger btn-sm btn-remove-row"
            onclick="removeItem(this,'${itemId}')"><i class="fas fa-trash"></i></button>`;

            let newRow;
            if (categoryExists) {
                newRow = `<tr class="item-row" data-item-id="${itemId}" data-category="${category}" data-item-text="${itemText}">
            <td class="item-number-cell">${globalItemNumber}</td>
            <td class="item-text-cell">${itemText}</td>
<td class="num-cell">${formatNumber(data.pendapatan_idr)}</td>
<td class="num-cell">${formatNumber(data.pendapatan_usd)}</td>
<td class="num-cell">${formatNumber(data.hpp_ops)}</td>
<td class="num-cell">${formatNumber(data.hargajual_idr)}</td>
            <td class="text-center">${actionBtns}</td>
        </tr>`;
                insertAfterRow.after(newRow);
            } else {
                newRow = `<tr class="item-row" data-item-id="${itemId}" data-category="${category}" data-item-text="${itemText}">
            <td class="item-number-cell">${globalItemNumber}</td>
            <td class="category-cell" rowspan="1">${category}</td>
            <td class="item-text-cell">${itemText}</td>
<td class="num-cell">${formatNumber(data.pendapatan_idr)}</td>
<td class="num-cell">${formatNumber(data.pendapatan_usd)}</td>
<td class="num-cell">${formatNumber(data.hpp_ops)}</td>
<td class="num-cell">${formatNumber(data.hargajual_idr)}</td>
            <td class="text-center">${actionBtns}</td>
        </tr>`;
                $('#itemsTableBody').append(newRow);
            }

            renumberAllItems();
        }

        // ========================================
        // EDIT ITEM — LOAD TO FORM
        // ========================================
        function editItem(itemId) {
            if (!itemId) {
                showFloatingAlert('error', 'Invalid item ID');
                return;
            }

            showFloatingAlert('saving', 'Loading item data...');

            $.ajax({
                url: `/data/jo-tramper/item/show/${itemId}`,
                method: 'GET',
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', 'Failed to load item data');
                        return;
                    }
                    hideFloatingAlert();

                    const item = r.data;

                    $('#editing_item_id').val(itemId);
                    $('#formSectionTitle').html('<i class="fas fa-edit"></i> Edit Item');
                    $('#addItemFormSection').addClass('edit-mode');
                    $('#btnAddToTable').html('<i class="fas fa-save"></i> Update Item');
                    $('#btnCancelEdit').show();

                    $('#input_category').val(item.invoice_ctg).trigger('change');

                    setTimeout(() => {
                        _skipNoteUpdate = true;
                        $('#input_item').val(item.id_md_invoice).trigger('change.select2');

                        const idr = parseFloat(item.pendapatan_idr) || 0;
                        const usd = parseFloat(item.pendapatan_usd) || 0;
                        const hpp = parseFloat(item.hpp_ops) || 0;
                        const kurs = parseFloat(item.kurs_usd) || 0;

                        $('#input_pendapatan_idr').val(idr > 0 ? formatRupiah(idr.toFixed(2).replace(
                            '.', ',')) : '');
                        $('#input_pendapatan_usd').val(usd > 0 ? formatRupiah(usd.toFixed(2).replace(
                            '.', ',')) : '');
                        $('#input_hpp').val(formatRupiah(hpp.toFixed(2).replace('.', ',')));
                        $('#input_note').val(item.note || '');

                        if (usd > 0 && kurs > 0) {
                            $('#global_kurs_usd_display').val(formatRupiah(kurs.toFixed(2).replace('.',
                                ',')));
                            $('#global_kurs_usd').val(kurs);
                        }

                        if (item.tgl_kurs_usd) {
                            // Format: "2024-01-15T08:30" untuk datetime-local input
                            const dt = item.tgl_kurs_usd.substring(0, 16).replace(' ', 'T');
                            $('#global_tgl_kurs_usd').val(dt);
                        }

                        _skipNoteUpdate = false;

                        calculateHargaJual();
                    }, 300);

                    $('html, body').animate({
                        scrollTop: $('#addItemFormSection').offset().top - 100
                    }, 500);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to load item data';
                    showFloatingAlert('error', msg);
                }
            });
        }

        $('#btnCancelEdit').on('click', function() {
            clearItemForm();
            $('html, body').animate({
                scrollTop: $('#itemsTableBody').offset().top - 200
            }, 500);
        });

        // ========================================
        // UPDATE ITEM TO DATABASE
        // ========================================
        function updateItemToDatabase(
            itemId, category, invoiceId, itemText,
            pendapatanIDR, pendapatanUSD, hpp, hargaJual,
            kursRate, kursDate, note
        ) {
            showFloatingAlert('saving', 'Updating item...');

            $.ajax({
                url: `/data/jo-tramper/item/update/${itemId}`,
                method: 'PUT',
                data: {
                    id_jo_tram: currentJoTramperId,
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
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', 'Failed to update item');
                        return;
                    }
                    showFloatingAlert('success', 'Item updated successfully!');

                    const row = $(`.item-row[data-item-id="${itemId}"]`);
                    const oldCat = row.data('category');

                    if (oldCat !== category) {
                        removeRowFromTable(row, oldCat);
                        insertRowWithCategoryGrouping(itemId, category, itemText, r.data);
                    } else {
                        row.attr('data-item-text', itemText);
                        row.find('.item-text-cell').text(itemText);

                        // Offset: row dengan category cell = 0, tanpa = -1
                        const hasCat = row.find('.category-cell').length > 0;
                        const off = hasCat ? 0 : -1;
                        row.find('td').eq(3 + off).text(formatNumber(r.data.pendapatan_idr));
                        row.find('td').eq(4 + off).text(formatNumber(r.data.pendapatan_usd));
                        row.find('td').eq(5 + off).text(formatNumber(r.data.hpp_ops));
                        row.find('td').eq(6 + off).text(formatNumber(r.data.hargajual_idr));
                    }

                    updateGrandTotal();
                    clearItemForm();

                    $('html, body').animate({
                        scrollTop: $(`.item-row[data-item-id="${itemId}"]`).offset().top - 200
                    }, 500);
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors ?
                        Object.values(xhr.responseJSON.errors).flat().join(', ') :
                        xhr.responseJSON?.message || 'Failed to update item';
                    showFloatingAlert('error', errors);
                }
            });
        }

        // ========================================
        // REMOVE ITEM (REALTIME DELETE)
        // ========================================
        function removeItem(button, itemId) {
            if (!itemId) {
                showFloatingAlert('error', 'Invalid item ID');
                return;
            }
            if (!confirm('Are you sure you want to delete this item?')) return;

            showFloatingAlert('saving', 'Deleting item...');

            $.ajax({
                url: `/data/jo-tramper/item/destroy/${itemId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', 'Failed to delete item');
                        return;
                    }
                    showFloatingAlert('success', 'Item deleted successfully!');

                    const row = $(button).closest('tr');
                    const category = row.data('category');
                    removeRowFromTable(row, category);
                    renumberAllItems();
                    updateGrandTotal();

                    if ($('#itemsTableBody tr.item-row').length === 0) {
                        $('#itemsTableBody').html(emptyRowHtml());
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to delete item';
                    showFloatingAlert('error', msg);
                }
            });
        }

        function removeRowFromTable(row, category) {
            const catCell = row.find('.category-cell');
            if (catCell.length) {
                const span = parseInt(catCell.attr('rowspan') || 1);
                if (span > 1) {
                    const next = row.next(`.item-row[data-category="${category}"]`);
                    if (next.length) {
                        next.find('.item-text-cell').before(
                            `<td class="category-cell" rowspan="${span - 1}">${category}</td>`
                        );
                    }
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

        // ========================================
        // RESET ALL ITEMS
        // ========================================
        $('#resetAllBtn').on('click', function() {
            if (!confirm('Are you sure you want to remove ALL items? This will delete them from the database.'))
                return;

            const ids = [];
            $('.item-row').each(function() {
                const id = $(this).data('item-id');
                if (id) ids.push(id);
            });

            if (!ids.length) {
                showFloatingAlert('error', 'No items to delete');
                return;
            }

            showFloatingAlert('saving', 'Deleting all items...');

            Promise.all(ids.map(id => $.ajax({
                url: `/data/jo-tramper/item/destroy/${id}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            }))).then(() => {
                showFloatingAlert('success', 'All items deleted successfully!');
                $('#itemsTableBody').html(emptyRowHtml());
                globalItemNumber = 0;
                updateGrandTotal();
            }).catch(() => {
                showFloatingAlert('error', 'Some items could not be deleted');
            });
        });

        // ========================================
        // FINAL SAVE ALL CHANGES
        // ========================================
        $('#btnFinalSave').on('click', function() {
            const custId = $('#id_md_cust').val();
            const portId = $('#id_md_port').val();
            const title = $('#title').val();
            const ds = $('#date_start').val();
            const de = $('#date_end').val();

            if (!custId || !portId || !title || !ds || !de) {
                showFloatingAlert('error', 'Please fill all required fields');
                return;
            }

            if (!confirm('Save all changes and return to JO Tramper list?')) return;

            showFloatingAlert('saving', 'Saving all changes...');

            $.ajax({
                url: `/data/jo-tramper/save-all/${currentJoTramperId}`,
                method: 'POST',
                data: {
                    id_md_cust: custId,
                    id_md_port: portId,
                    id_md_vessel: $('#id_md_vessel').val(), // tambah ini
                    date_start: ds,
                    date_end: de,
                    title: title,
                    note: $('#note').val(),
                    global_kurs_usd: parseRupiah($('#global_kurs_usd_display').val()),
                    global_tgl_kurs_usd: $('#global_tgl_kurs_usd').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', r.message || 'Failed to save changes');
                        return;
                    }
                    showFloatingAlert('success', 'All changes saved successfully!');
                    setTimeout(() => {
                        window.location.href = '{{ route('jo-tramper.index') }}';
                    }, 1500);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to save all changes';
                    showFloatingAlert('error', msg);
                }
            });
        });

        // ========================================
        // UTILITY FUNCTIONS
        // ========================================
        function clearItemForm() {
            _skipNoteUpdate = false;
            $('#editing_item_id').val('');
            $('#formSectionTitle').html('<i class="fas fa-plus-square"></i> Add New Item');
            $('#addItemFormSection').removeClass('edit-mode');
            $('#btnAddToTable').html('<i class="fas fa-arrow-down"></i> Save to Table');
            $('#btnCancelEdit').hide();
            $('#input_category').val(null).trigger('change');
            $('#input_item').prop('disabled', true).html('<option value="">Select category first</option>');
            $('#input_note').val('');
            ['input_pendapatan_idr', 'input_pendapatan_usd', 'input_hpp', 'input_harga_jual']
            .forEach(id => $('#' + id).val(''));
        }

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

        function emptyRowHtml() {
            return `<tr class="no-items-row"><td colspan="8">
        <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
        <p class="mb-0 fw-bold">No data available</p>
        <small class="text-muted">Fill the form above and click "Save to Table"</small>
    </td></tr>`;
        }

        $(document).ready(function() {
            updateGrandTotal();
        });
    </script>
@endpush
