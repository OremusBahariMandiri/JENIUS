@extends('layouts.app')

@section('title', 'Edit JO Other')

@push('styles')
    <style>
        .joOtherEditPage .table-items tbody tr.row-from-lpj td {
            background: #eff6ff !important;
        }

        .joOtherEditPage .table-items tbody tr.row-from-lpj:hover td {
            background: #dbeafe !important;
        }

        .joOtherEditPage .table-items tbody tr.row-from-lpj .category-cell {
            background: #dbeafe !important;
        }

        .joOtherEditPage .table-items tbody tr.row-from-lpj .item-number-cell {
            background: #dbeafe !important;
        }

        .lpj-origin-note {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            padding: 8px 14px;
            background: #eff6ff;
            border-radius: 8px;
            border-left: 3px solid #3b82f6;
            font-size: .8rem;
            color: #1e40af;
        }

        .joOtherEditPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .joOtherEditPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .joOtherEditPage .form-control:focus,
        .joOtherEditPage .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .joOtherEditPage .form-label {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .joOtherEditPage .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .joOtherEditPage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .joOtherEditPage .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .joOtherEditPage .btn-success:hover {
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
            align-items: stretch;
            gap: 0;
        }

        .add-item-form-section .currency-label {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #2c3e50;
            padding: 0 0.75rem;
            font-size: 0.7rem;
            border-radius: 6px 0 0 6px;
            min-width: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-weight: 600;
            white-space: nowrap;
        }

        .add-item-form-section .currency-input {
            border-radius: 0 6px 6px 0 !important;
            border-left: none !important;
            flex: 1;
        }

        .btn-add-to-table {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-add-to-table:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
            color: white;
        }

        .btn-cancel-edit-style {
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-cancel-edit-style:hover {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
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

        .currency-group-cell {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 5px;
            width: 100%;
        }
        .currency-label-cell {
            background-color: #e9ecef;
            color: #495057;
            border: 1px solid #ced4da;
            padding: 0.2rem 0.4rem;
            font-size: 0.72rem;
            border-radius: 4px;
            min-width: 36px;
            text-align: center;
            font-weight: 600;
            white-space: nowrap;
        }
        .value-cell {
            font-size: 0.88rem;
            font-weight: 600;
            color: #2c3e50;
            text-align: right;
            flex: 1;
        }

        .table-items tbody td.item-text-cell,
        .table-items tbody td.category-cell,
        .table-items tbody td.item-number-cell {
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
            justify-content: space-between;
            gap: 5px;
            width: 100%;
        }

        .currency-label-footer {
            background-color: #2c3e50;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.2rem 0.5rem;
            font-size: 0.78rem;
            border-radius: 4px;
            min-width: 42px;
            text-align: center;
            font-weight: 600;
        }

        .value-footer {
            flex: 1;
            color: #000;
            font-weight: 600;
            font-size: 0.88rem;
            text-align: right;
        }

        .no-items-row td {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
            background: #f8f9fa !important;
        }

        /* FINAL SAVE */
        /* Remove layout bottom padding so sticky bar is truly last */
        main.content {
            padding-bottom: 0 !important;
        }

        .final-save-section {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 20px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px 12px 0 0;
            margin-top: 20px;
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

    <div class="container-fluid joOtherEditPage">
        <div class="row">
            <div class="col-lg-12">

                <!-- Page Header -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit JO Other</span>
                    </div>
                </div>

                <input type="hidden" id="current_jo_other_id" value="{{ $joOther->id_jo_other }}">

                <form id="joOtherForm">
                    @csrf

                    <!-- Header Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>JO Other Information</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. JO</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="fas fa-file-alt"></i>
                                        </span>
                                        <input type="text" class="form-control fw-bold"
                                            value="{{ $joOther->no_jo_other ?? '-' }}" readonly
                                            style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">JO Date</label>
                                    <input type="date" name="tgl_jo_other" id="tgl_jo_other" class="form-control"
                                        value="{{ $joOther->tgl_jo_other ? $joOther->tgl_jo_other->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label required-field">Customer</label>
                                    <select name="id_md_cust" id="id_md_cust" class="form-select select2"
                                        data-placeholder="Search Customer...">
                                        <option value=""></option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id_md_cust }}"
                                                {{ $joOther->id_md_cust == $customer->id_md_cust ? 'selected' : '' }}>
                                                {{ $customer->customer }}
                                                @if ($customer->no_customer)
                                                    ({{ $customer->no_customer }})
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
                                        @foreach ($others as $other)
                                            <option value="{{ $other->id_md_other }}"
                                                {{ $joOther->id_md_other == $other->id_md_other ? 'selected' : '' }}>
                                                {{ $other->other }}
                                                @if ($other->code)
                                                    ({{ $other->code }})
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
                                                {{ $joOther->id_md_port == $port->id_md_port ? 'selected' : '' }}>
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
                                                {{ $joOther->id_md_vessel == $vessel->id_md_vessel ? 'selected' : '' }}>
                                                {{ $vessel->vessel_name }}
                                                @if ($vessel->no_imo)
                                                    (IMO: {{ $vessel->no_imo }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label required-field">Start Date</label>
                                    <input type="date" name="date_start" id="date_start" class="form-control"
                                        value="{{ $joOther->date_start ? $joOther->date_start->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label required-field">End Date</label>
                                    <input type="date" name="date_end" id="date_end" class="form-control"
                                        value="{{ $joOther->date_end ? $joOther->date_end->format('Y-m-d') : '' }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required-field">Title</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ $joOther->title }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea name="note" id="note" class="form-control" rows="3">{{ $joOther->note }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-success" id="btnSaveHeader">
                                    <i class="fas fa-save me-1"></i> Update
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Items Card -->
                    <div class="card shadow mb-4" id="itemsCard">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>JO Other Items</h6>
                                <div class="kurs-section d-flex gap-3 align-items-center mb-0"
                                    style="padding: 10px 15px;">
                                    <div>
                                        <label class="form-label mb-1">Kurs Date</label>
                                        @php
                                            $firstItem = $joOther->items->first();
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

                            <!-- Add Item Form Section -->
                            <div class="add-item-form-section" id="addItemFormSection">
                                <h6 id="formSectionTitle">
                                    <i class="fas fa-plus-square"></i> Add New Item
                                </h6>
                                <input type="hidden" id="editing_item_id" value="">

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label required-field">Category</label>
                                        <select id="input_category" class="form-select select2"
                                            data-placeholder="Select Category" data-allow-clear="false">
                                            <option value=""></option>
                                            @foreach ($invoices->groupBy('invoice_ctg') as $category => $ig)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label required-field">Item</label>
                                        <select id="input_item" class="form-select select2" disabled
                                            data-placeholder="Select category first" data-allow-clear="false">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Note (Optional)</label>
                                        <textarea id="input_note" class="form-control" rows="3" placeholder="Add notes for this item..."></textarea>
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
                                        <label class="form-label">Selling Price (IDR)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_harga_jual"
                                                class="form-control currency-input" readonly
                                                style="background-color:#fff;">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">HPP (Ops Costs)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_hpp" class="form-control currency-input">
                                        </div>
                                    </div>
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

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-items table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:5%;">No</th>
                                            <th style="width:13%;">Category</th>
                                            <th style="width:13%;">Item</th>
                                            <th style="width:12%;">Income (IDR)</th>
                                            <th style="width:12%;">Income (USD)</th>
                                            <th style="width:12%;">Selling Price (IDR)</th>
                                            <th style="width:12%;">HPP (Ops Costs)</th>
                                            <th style="width:11%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        @if ($joOther->items->count() > 0)
                                            @php
                                                $groupedItems = $joOther->items->groupBy(
                                                    fn($i) => $i->invoice->invoice_ctg,
                                                );
                                                $globalIndex = 1;
                                                $hasLpjItems = false;
                                            @endphp

                                            @foreach ($groupedItems as $category => $items)
                                                @foreach ($items as $index => $item)
                                                    @php
                                                        $isFromLpj = !empty($item->origin_lpj_other);
                                                        if ($isFromLpj) {
                                                            $hasLpjItems = true;
                                                        }
                                                    @endphp
                                                    <tr class="item-row {{ $isFromLpj ? 'row-from-lpj' : '' }}"
                                                        data-item-id="{{ $item->id_jo_other_item }}"
                                                        data-invoice-id="{{ $item->id_md_invoice }}"
                                                        data-category="{{ $item->invoice->invoice_ctg }}"
                                                        data-item-text="{{ $item->invoice->invoice_typ }}"
                                                        data-item-number="{{ $globalIndex }}"
                                                        data-origin-lpj="{{ $item->origin_lpj_other ?? '' }}">

                                                        <td class="item-number-cell">{{ $globalIndex }}</td>

                                                        @if ($index === 0)
                                                            <td class="category-cell" rowspan="{{ $items->count() }}">
                                                                {{ $category }}
                                                            </td>
                                                        @endif

                                                        <td class="item-text-cell text-start">
                                                            <span class="fw-semibold"
                                                                style="color:#2c3e50;">{{ $item->invoice->invoice_typ }}</span>
                                                            {{-- ← BADGE "From LPJ" seperti JO Contract --}}
                                                            @if ($isFromLpj)
                                                                <span class="badge ms-1"
                                                                    style="background:#3b82f6; font-size:.65rem;">
                                                                    From LPJ
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="currency-group-cell">
                                                                <span class="currency-label-cell">IDR</span>
                                                                <span class="value-cell">{{ number_format($item->pendapatan_idr, 2, ',', '.') }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="currency-group-cell">
                                                                <span class="currency-label-cell">USD</span>
                                                                <span class="value-cell">{{ number_format($item->pendapatan_usd, 2, ',', '.') }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="currency-group-cell">
                                                                <span class="currency-label-cell">IDR</span>
                                                                <span class="value-cell">{{ number_format($item->hargajual_idr, 2, ',', '.') }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="currency-group-cell">
                                                                <span class="currency-label-cell">IDR</span>
                                                                <span class="value-cell">{{ number_format($item->hpp_ops, 2, ',', '.') }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-primary btn-sm btn-edit-row me-1"
                                                                onclick="editItem('{{ $item->id_jo_other_item }}')">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm btn-remove-row"
                                                                onclick="removeItem(this, '{{ $item->id_jo_other_item }}')">
                                                                <i class="fas fa-trash-alt"></i>
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
                                                    <small class="text-muted">Fill the form above and click "Add to Table"</small>
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>

                                        {{-- TAX ROWS --}}
                                        <tbody id="taxTableBody">
                                            @foreach($pajakItems as $pi)
                                                @php $allTaxRowNumber = ($globalIndex ?? 1) + $loop->index; @endphp
                                                <tr class="tax-row"
                                                    data-tax-id="{{ $pi->id_jo_other_pajak }}"
                                                    data-raw="{{ $pi->nominal_pajak }}"
                                                    data-raw-hpp="{{ $pi->nominal_pajak_hpp }}"
                                                    data-include-selling="{{ $pi->include_selling ? 1 : 0 }}"
                                                    data-include-hpp="{{ $pi->include_hpp ? 1 : 0 }}">
                                                    <td class="item-number-cell text-center">{{ $allTaxRowNumber }}</td>
                                                    <td class="category-cell" style="background:#fffbea !important; color:#92400e;">Tax</td>
                                                    <td class="item-text-cell text-start">
                                                        <span class="fw-semibold" style="color:#2c3e50;">{{ $pi->nama_pajak }}</span>
                                                        <small class="text-muted ms-1">({{ $pi->persentase }}%)</small>
                                                    </td>
                                                    <td>
                                                        <div class="currency-group-cell">
                                                            <span class="currency-label-cell">IDR</span>
                                                            <span class="value-cell">0,00</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="currency-group-cell">
                                                            <span class="currency-label-cell">USD</span>
                                                            <span class="value-cell">0,00</span>
                                                        </div>
                                                    </td>
                                                    <td class="tax-selling">
                                                        <div class="currency-group-cell">
                                                            <span class="currency-label-cell">IDR</span>
                                                            <span class="value-cell">
                                                                {{ $pi->include_selling ? number_format($pi->nominal_pajak, 2, ',', '.') : '0,00' }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="tax-hpp">
                                                        <div class="currency-group-cell">
                                                            <span class="currency-label-cell">IDR</span>
                                                            <span class="value-cell">
                                                                {{ $pi->include_hpp ? number_format($pi->nominal_pajak_hpp, 2, ',', '.') : '0,00' }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-primary btn-sm btn-edit-row me-1"
                                                            onclick="editTax('{{ $pi->id_jo_other_pajak }}', '{{ $pi->id_md_pajak }}', '{{ addslashes($pi->nama_pajak) }}', {{ $pi->include_selling ? 1 : 0 }}, {{ $pi->include_hpp ? 1 : 0 }}, {{ $pi->persentase }}, {{ $pi->nominal_pajak }}, {{ $pi->nominal_pajak_hpp }})" title="Edit Tax">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                                                            onclick="deleteTax(this, '{{ $pi->id_jo_other_pajak }}')" title="Remove Tax">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
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

                                {{-- ← NOTE BIRU — pakai $hasLpjItems yang sudah di-set di loop atas --}}
                                <div class="lpj-origin-note mt-2" id="lpjOriginNote"
                                    style="{{ isset($hasLpjItems) && $hasLpjItems ? '' : 'display:none;' }}">
                                    <i class="fas fa-info-circle" style="color:#3b82f6; flex-shrink:0;"></i>
                                    <span>Rows highlighted in <strong>blue</strong> are items that were added from an
                                        LPJ page.</span>
                                </div>
                            </div>

                                {{-- TAX INPUT PANEL --}}
                                <div id="taxInputPanel" class="mt-3 p-3 rounded-3" style="background:#fffbf0; border:1px solid #ffc107;">
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <label class="form-label form-label-sm fw-semibold mb-1" style="color:#856404;">
                                                <i class="fas fa-percent me-1"></i> Tax Type
                                            </label>
                                            <select id="selectTax" class="form-select form-select-sm">
                                                <option value="">-- Select Tax --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-2 align-items-center">
                                        <div class="col-4">
                                            <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 h-100" style="background:#fffdf0; border:1px solid #ffe08a;">
                                                <div class="form-check mb-0 flex-shrink-0">
                                                    <input class="form-check-input" type="checkbox" id="taxIncludeSelling" checked>
                                                    <label class="form-check-label fw-semibold" for="taxIncludeSelling" style="font-size:0.82rem; color:#856404;">
                                                        Include in <span style="color:#2e7d32;">Selling</span>
                                                    </label>
                                                </div>
                                                <div id="wrapTaxAmountSelling" class="input-group input-group-sm flex-nowrap" style="min-width:0;">
                                                    <span class="input-group-text" style="font-size:0.75rem;">IDR</span>
                                                    <input type="text" id="taxAmount" class="form-control text-end fw-bold" readonly
                                                        style="background:#f0fff4; color:#2e7d32; font-size:0.82rem; min-width:0;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 h-100" style="background:#fff8f0; border:1px solid #ffd0a0;">
                                                <div class="form-check mb-0 flex-shrink-0">
                                                    <input class="form-check-input" type="checkbox" id="taxIncludeHpp">
                                                    <label class="form-check-label fw-semibold" for="taxIncludeHpp" style="font-size:0.82rem; color:#7b3f00;">
                                                        Include in <span style="color:#c0392b;">HPP</span>
                                                    </label>
                                                </div>
                                                <div id="wrapTaxAmountHpp" class="input-group input-group-sm flex-nowrap" style="min-width:0; display:none !important;">
                                                    <span class="input-group-text" style="font-size:0.75rem;">IDR</span>
                                                    <input type="text" id="taxAmountHpp" class="form-control text-end fw-bold" readonly
                                                        style="background:#fff0e8; color:#c0392b; font-size:0.82rem; min-width:0;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 d-flex align-items-center gap-2">
                                            <input type="hidden" id="editingTaxId" value="">
                                            <input type="hidden" id="editingTaxPersentase" value="">
                                            <button type="button" class="btn btn-warning btn-sm fw-bold flex-grow-1" id="addTaxBtn" style="height:36px;">
                                                <i class="fas fa-plus me-1"></i>Add Tax to Table
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm fw-bold" id="cancelEditTaxBtn" style="height:36px; display:none;">
                                                <i class="fas fa-times me-1"></i>Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {{-- END TAX INPUT PANEL --}}

                            <div class="d-flex justify-content-end mt-2">
                                <button type="button" class="btn btn-danger" id="resetAllBtn">
                                    <i class="fas fa-trash-alt me-1"></i>Reset All Items
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Final Save Section -->
                <div class="final-save-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div></div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('jo-other.export-pdf', $joOther->id_jo_other) }}" target="_blank"
                                class="btn"
                                style="padding:15px 40px; border-radius:12px; font-weight:700; font-size:1.1rem;
                                       border-color:red; background-color:rgb(255, 237, 237); color:red">
                                <i class="fas fa-file-pdf me-2"></i> Generate JO
                            </a>
                            <a href="{{ route('jo-other.index') }}" class="btn btn-final-back">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        // ── GLOBAL: editTax ──
        function editTax(taxId, pajakId, namaP, incSell, incHpp, persentase, nominalJual, nominalHpp) {
            $('#editingTaxId').val(taxId);
            $('#editingTaxPersentase').val(parseFloat(persentase) || 0);
            if ($('#selectTax').find('option[value="' + pajakId + '"]').length === 0) {
                var $opt = new Option(namaP, pajakId, true, true);
                $('#selectTax').append($opt);
            }
            $('#selectTax').val(pajakId).trigger('change');
            setTimeout(function() {
                $('#taxIncludeSelling').prop('checked', incSell == 1);
                $('#taxIncludeHpp').prop('checked', incHpp == 1);
                if (incSell == 1) {
                    $('#wrapTaxAmountSelling').css('display', 'flex');
                    $('#taxAmount').val((typeof formatNumber === 'function') ? formatNumber(parseFloat(nominalJual) || 0) : (parseFloat(nominalJual) || 0));
                } else { $('#wrapTaxAmountSelling').hide(); $('#taxAmount').val(''); }
                if (incHpp == 1) {
                    $('#wrapTaxAmountHpp').css('display', 'flex');
                    $('#taxAmountHpp').val((typeof formatNumber === 'function') ? formatNumber(parseFloat(nominalHpp) || 0) : (parseFloat(nominalHpp) || 0));
                } else { $('#wrapTaxAmountHpp').hide(); $('#taxAmountHpp').val(''); }
            }, 50);
            $('#addTaxBtn').removeClass('btn-warning').addClass('btn-success').html('<i class="fas fa-save me-1"></i>Update Tax');
            $('#cancelEditTaxBtn').show();
            $('html, body').animate({ scrollTop: $('#selectTax').offset().top - 150 }, 300);
        }
        // ── GLOBAL: deleteTax ──
        function deleteTax(btn, id) {
            var row = $(btn).closest('tr');
            var destroyUrl = '/data/jo-other/pajak/destroy/' + id;
            var csrf = '{{ csrf_token() }}';
            Swal.fire({ title: 'Remove Tax?', text: 'This tax entry will be removed.', icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Remove', cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: destroyUrl, type: 'POST',
                    data: { _method: 'DELETE', _token: csrf },
                    headers: { 'X-CSRF-TOKEN': csrf },
                    success: function(res) {
                        if (res.success) {
                            row.remove();
                            var baseCount = $('.item-row').length;
                            $('#taxTableBody tr.tax-row').each(function(i) { $(this).find('td:first').text(baseCount + i + 1); });
                            if (typeof updateGrandTotal === 'function') updateGrandTotal();
                            Swal.fire({ icon: 'success', title: 'Removed', text: 'Tax entry removed.', timer: 1200, showConfirmButton: false });
                        } else { Swal.fire({ icon: 'error', title: 'Failed', text: res.message || 'Could not remove.' }); }
                    },
                    error: function(xhr) { var e = xhr.responseJSON; Swal.fire({ icon: 'error', title: 'Failed', text: e && e.message ? e.message : 'Error.' }); }
                });
            });
        }
    </script>
    <script>
        const currentJoOtherId = '{{ $joOther->id_jo_other }}';
        const checkRelationsUrl = '{{ route('jo-other.item.check-relations', ':id') }}';
        const destroyItemUrl = '/data/jo-other/item/destroy/:id';
        let globalItemNumber = {{ $joOther->items->count() }};
        let _skipNoteUpdate = false;

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

        // ── Floating Alert ──────────────────────────────────────────
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

        // ── Rupiah ──────────────────────────────────────────────────
        function formatRupiah(value) {
            let number = value.replace(/[^\d,]/g, '').replace(/\./g, '');
            if (!number) return '';
            let [int, dec] = number.split(',');
            if (dec !== undefined && dec.length > 2) dec = dec.substring(0, 2);
            int = int.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return dec !== undefined ? int + ',' + dec : int + ',00';
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

        function calculateHargaJual() {
            const kurs = parseRupiah($('#global_kurs_usd_display').val());
            const idr = parseRupiah($('#input_pendapatan_idr').val());
            const usd = parseRupiah($('#input_pendapatan_usd').val());
            let hj = 0;
            if (idr > 0) hj = idr;
            else if (usd > 0 && kurs > 0) hj = usd * kurs;
            $('#input_harga_jual').val(formatRupiah(hj.toFixed(2).replace('.', ',')));
            validateHPP(); // ← TAMBAH INI
        }

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
                        '<i class="fas fa-exclamation-triangle me-1"></i>HPP must not exceed Selling Price (' +
                        $('#input_harga_jual').val() + ')';
                    hppInput.closest('.currency-group').after(warning);
                }

                if (clamp) {
                    $('#input_hpp').val($('#input_harga_jual').val());
                    hppInput.style.borderColor = '';
                    hppInput.style.boxShadow = '';
                    const warn = document.getElementById('hpp-warning');
                    if (warn) warn.remove();
                }
            } else {
                hppInput.style.borderColor = '';
                hppInput.style.boxShadow = '';
                const warn = document.getElementById('hpp-warning');
                if (warn) warn.remove();
            }
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

        $('#input_hpp').on('input', function() {
            validateHPP();
        });

        $('#input_hpp').on('blur', function() {
            validateHPP(true);
        });

        // ── Category dropdown ────────────────────────────────────────
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

        $('#input_item').on('change', function() {
            if (_skipNoteUpdate) return;
            const masterNote = $(this).find('option:selected').data('note') || '';
            $('#input_note').val(masterNote);
        });

        // ── Update Header ────────────────────────────────────────────
        $('#btnSaveHeader').on('click', function() {
            const custId = $('#id_md_cust').val();
            const otherId = $('#id_md_other').val();
            const portId = $('#id_md_port').val();
            const title = $('#title').val();
            const ds = $('#date_start').val();
            const de = $('#date_end').val();

            if (!custId || !otherId || !portId || !title || !ds || !de) {
                showFloatingAlert('error', 'Please fill all required fields');
                return;
            }

            showFloatingAlert('saving', 'Updating header...');

            $.ajax({
                url: `/data/jo-other/header/update/${currentJoOtherId}`,
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

        // ── syncLpjNote ──────────────────────────────────────────────
        function syncLpjNote() {
            if ($('.item-row.row-from-lpj').length > 0) $('#lpjOriginNote').show();
            else $('#lpjOriginNote').hide();
        }

        // ── Add / Update Item ────────────────────────────────────────
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

            if (hargaJual > 0 && hpp > hargaJual) {
                showFloatingAlert('error', 'HPP must not exceed Selling Price');
                $('#input_hpp').focus();
                return;
            }

            if (editingId) {
                updateItemToDatabase(editingId, category, itemId, itemText,
                    pendapatanIDR, pendapatanUSD, hpp, hargaJual, kursRate, kursDate, note);
                return;
            }

            showFloatingAlert('saving', 'Adding item...');

            $.ajax({
                url: '/data/jo-other/item/store',
                method: 'POST',
                data: {
                    id_jo_other: currentJoOtherId,
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
                    const newItemId = r.data.id_jo_other_item || r.data.id;
                    if (!newItemId) {
                        showFloatingAlert('error', 'Item created but ID invalid. Please refresh.');
                        return;
                    }
                    // Item baru dari form TIDAK punya origin_lpj_other → isFromLpj = false
                    insertRowWithCategoryGrouping(newItemId, category, itemText, r.data, false);
                    updateGrandTotal();
                    clearItemForm();
                    syncLpjNote();
                },
                error: function(xhr) {
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to add item');
                }
            });
        });

        // ── Insert Row ───────────────────────────────────────────────
        function insertRowWithCategoryGrouping(itemId, category, itemText, data, isFromLpj = false) {
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

            const actionBtns = `
                <button type="button" class="btn btn-primary btn-sm btn-edit-row me-1"
                    onclick="editItem('${itemId}')"><i class="fas fa-edit"></i></button>
                <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                    onclick="removeItem(this,'${itemId}')"><i class="fas fa-trash-alt"></i></button>`;

            const lpjBadge = isFromLpj ?
                `<span class="badge ms-1" style="background:#3b82f6; font-size:.65rem;">From LPJ</span>` :
                '';
            const rowClass = isFromLpj ? 'item-row row-from-lpj' : 'item-row';

            let newRow;
            if (categoryExists) {
                newRow = `<tr class="${rowClass}"
                    data-item-id="${itemId}"
                    data-category="${category}"
                    data-item-text="${itemText}"
                    data-origin-lpj="${isFromLpj ? '1' : ''}">
                    <td class="item-number-cell">${globalItemNumber}</td>
                    <td class="item-text-cell text-start">
                        <span class="fw-semibold" style="color:#2c3e50;">${itemText}</span>${lpjBadge}
                    </td>
                    <td><div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">${formatNumber(data.pendapatan_idr)}</span></div></td>
                    <td><div class="currency-group-cell"><span class="currency-label-cell">USD</span><span class="value-cell">${formatNumber(data.pendapatan_usd)}</span></div></td>
                    <td><div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">${formatNumber(data.hargajual_idr)}</span></div></td>
                    <td><div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">${formatNumber(data.hpp_ops)}</span></div></td>
                    <td class="text-center">${actionBtns}</td>
                </tr>`;
                insertAfterRow.after(newRow);
            } else {
                newRow = `<tr class="${rowClass}"
                    data-item-id="${itemId}"
                    data-category="${category}"
                    data-item-text="${itemText}"
                    data-origin-lpj="${isFromLpj ? '1' : ''}">
                    <td class="item-number-cell">${globalItemNumber}</td>
                    <td class="category-cell" rowspan="1">${category}</td>
                    <td class="item-text-cell text-start">
                        <span class="fw-semibold" style="color:#2c3e50;">${itemText}</span>${lpjBadge}
                    </td>
                    <td><div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">${formatNumber(data.pendapatan_idr)}</span></div></td>
                    <td><div class="currency-group-cell"><span class="currency-label-cell">USD</span><span class="value-cell">${formatNumber(data.pendapatan_usd)}</span></div></td>
                    <td><div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">${formatNumber(data.hargajual_idr)}</span></div></td>
                    <td><div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">${formatNumber(data.hpp_ops)}</span></div></td>
                    <td class="text-center">${actionBtns}</td>
                </tr>`;
                $('#itemsTableBody').append(newRow);
            }
            renumberAllItems();
        }

        // ── Edit Item ────────────────────────────────────────────────
        function editItem(itemId) {
            if (!itemId) {
                showFloatingAlert('error', 'Invalid item ID');
                return;
            }
            showFloatingAlert('saving', 'Loading item data...');

            $.ajax({
                url: `/data/jo-other/item/show/${itemId}`,
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

        // ── Update Item ──────────────────────────────────────────────
        function updateItemToDatabase(itemId, category, invoiceId, itemText,
            pendapatanIDR, pendapatanUSD, hpp, hargaJual, kursRate, kursDate, note) {

            showFloatingAlert('saving', 'Updating item...');

            $.ajax({
                url: `/data/jo-other/item/update/${itemId}`,
                method: 'PUT',
                data: {
                    id_jo_other: currentJoOtherId,
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
                        // Pertahankan status LPJ saat pindah kategori
                        const wasFromLpj = !!row.data('origin-lpj');
                        removeRowFromTable(row, oldCat);
                        insertRowWithCategoryGrouping(itemId, category, itemText, r.data, wasFromLpj);
                        syncLpjNote();
                    } else {
                        row.attr('data-item-text', itemText);
                        row.find('.item-text-cell').find('span.fw-semibold').text(itemText);
                        const hasCat = row.find('.category-cell').length > 0;
                        const off = hasCat ? 0 : -1;
                        row.find('td').eq(3 + off).text(formatNumber(r.data.pendapatan_idr));
                        row.find('td').eq(4 + off).text(formatNumber(r.data.pendapatan_usd));
                        row.find('td').eq(5 + off).text(formatNumber(r.data.hargajual_idr));
                        row.find('td').eq(6 + off).text(formatNumber(r.data.hpp_ops));
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

        // ── Remove Item ──────────────────────────────────────────────
        function removeItem(button, itemId) {
            if (!itemId || itemId == 0) {
                showFloatingAlert('error', 'Invalid item ID');
                return;
            }

            const row = $(button).closest('tr');
            const itemText = row.find('.item-text-cell .fw-semibold').text().trim() ||
                row.find('.item-text-cell').text().trim() ||
                'this item';
            const checkUrl = checkRelationsUrl.replace(':id', itemId);

            showFloatingAlert('saving', 'Checking item relations...');

            $.ajax({
                url: checkUrl,
                method: 'GET',
                success: function(response) {
                    hideFloatingAlert();
                    if (!response.success) {
                        showFloatingAlert('error', response.message || 'Failed to check relations');
                        return;
                    }
                    if (response.has_relations) {
                        showBlockedDeleteModal(itemText, response.kasbon_count, response.lpj_count);
                    } else {
                        showSimpleDeleteModal(button, itemId, itemText);
                    }
                },
                error: function() {
                    hideFloatingAlert();
                    showSimpleDeleteModal(button, itemId, itemText);
                }
            });
        }

        function showBlockedDeleteModal(itemText, kasbonCount, lpjCount) {
            $('#deleteItemModal').remove();

            const relParts = [];
            if (kasbonCount > 0) relParts.push(`<strong>${kasbonCount}</strong> Cash Advance item(s)`);
            if (lpjCount > 0) relParts.push(`<strong>${lpjCount}</strong> LPJ item(s)`);

            const html = `
            <div class="modal fade" id="deleteItemModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius:12px;border:none;box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                        <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:12px 12px 0 0;border:none;">
                            <h5 class="modal-title text-white fw-bold">
                                <i class="fas fa-ban me-2"></i>Cannot Delete Item
                            </h5>
                        </div>
                        <div class="modal-body p-4">
                            <p class="mb-2">The following item cannot be deleted:</p>
                            <div class="p-3 rounded mb-3" style="background:#fef3c7;border:1px solid #fcd34d;">
                                <strong style="color:#92400e;">${itemText}</strong>
                            </div>
                            <div class="alert alert-warning d-flex gap-2 align-items-start mb-3" style="border-radius:8px;">
                                <i class="fas fa-link mt-1" style="flex-shrink:0;"></i>
                                <div>
                                    <strong>This item is still used in:</strong><br>
                                    ${relParts.join('<br>')}
                                </div>
                            </div>
                            <div class="p-3 rounded" style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;font-size:.875rem;">
                                <i class="fas fa-info-circle me-2" style="color:#3b82f6;"></i>
                                To delete this item, first remove its related Cash Advance and LPJ records,
                                then come back to delete it here.
                            </div>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;

            $('body').append(html);
            const modal = new bootstrap.Modal(document.getElementById('deleteItemModal'));
            modal.show();
            $('#deleteItemModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }

        function showSimpleDeleteModal(button, itemId, itemText) {
            $('#deleteItemModal').remove();

            const html = `
            <div class="modal fade" id="deleteItemModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius:12px;border:none;box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                        <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:12px 12px 0 0;border:none;">
                            <h5 class="modal-title text-white fw-bold">
                                <i class="fas fa-trash-alt me-2"></i>Delete Item
                            </h5>
                        </div>
                        <div class="modal-body p-4">
                            <p class="mb-2">You are about to delete:</p>
                            <div class="p-3 rounded mb-3" style="background:#fee2e2;border:1px solid #fca5a5;">
                                <strong style="color:#dc2626;">${itemText}</strong>
                            </div>
                            <p class="mb-0 text-muted" style="font-size:.875rem;">
                                This item has no related Cash Advance or LPJ records. It is safe to delete.
                            </p>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4 gap-2">
                            <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-danger flex-fill fw-bold" id="btnConfirmDelete">
                                <i class="fas fa-trash me-1"></i> Yes, Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;

            $('body').append(html);
            const modal = new bootstrap.Modal(document.getElementById('deleteItemModal'));
            modal.show();

            $('#btnConfirmDelete').on('click', function() {
                modal.hide();
                $('#deleteItemModal').on('hidden.bs.modal', function() {
                    $(this).remove();
                    executeDeleteItem(button, itemId);
                });
            });
        }

        function executeDeleteItem(button, itemId) {
            showFloatingAlert('saving', 'Deleting item...');

            const deleteUrl = destroyItemUrl.replace(':id', itemId);

            $.ajax({
                url: deleteUrl,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showFloatingAlert('success', 'Item deleted successfully!');

                        const row = $(button).closest('tr');
                        const category = row.data('category');
                        const catCell = row.find('.category-cell');

                        if (catCell.length > 0) {
                            const rowspan = parseInt(catCell.attr('rowspan') || 1);
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
                        if (typeof syncLpjNote === 'function') syncLpjNote();

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
                    } else {
                        showFloatingAlert('error', response.message || 'Failed to delete item');
                    }
                },
                error: function(xhr) {
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to delete item');
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
                        `<td class="category-cell" rowspan="${span - 1}">${category}</td>`
                    );
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

        // ── Reset All ────────────────────────────────────────────────
        $('#resetAllBtn').on('click', function() {
            const ids = [];
            $('.item-row').each(function() {
                const id = $(this).data('item-id');
                if (id) ids.push(id);
            });

            if (!ids.length) {
                Swal.fire({ icon: 'info', title: 'No Items', text: 'There are no items to delete.', confirmButtonColor: '#6c757d' });
                return;
            }

            Swal.fire({
                title: 'Reset All Items?',
                text: 'All items and taxes will be permanently deleted from the database.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete All',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) return;

                showFloatingAlert('saving', 'Deleting all items...');

                const taxIds = [];
                $('#taxTableBody tr.tax-row').each(function() {
                    const tid = $(this).data('tax-id');
                    if (tid) taxIds.push(tid);
                });

                const csrf = $('meta[name="csrf-token"]').attr('content');

                Promise.all(ids.map(id => $.ajax({
                    url: `/data/jo-other/item/destroy/${id}`,
                    method: 'DELETE',
                    data: { _token: csrf }
                }))).then(() => {
                    return Promise.all(taxIds.map(tid => $.ajax({
                        url: `/data/jo-other/pajak/destroy/${tid}`,
                        type: 'POST',
                        data: { _method: 'DELETE', _token: csrf },
                        headers: { 'X-CSRF-TOKEN': csrf }
                    })));
                }).then(() => {
                    showFloatingAlert('success', 'All items and taxes deleted successfully!');
                    $('#itemsTableBody').html(emptyRowHtml());
                    $('#taxTableBody').empty();
                    globalItemNumber = 0;
                    updateGrandTotal();
                    syncLpjNote();
                }).catch(() => {
                    showFloatingAlert('error', 'Some items could not be deleted');
                });
            });
        });

        // ── Utilities ────────────────────────────────────────────────
        function clearItemForm() {
            _skipNoteUpdate = false;
            $('#editing_item_id').val('');
            $('#formSectionTitle').html('<i class="fas fa-plus-square"></i> Add New Item');
            $('#addItemFormSection').removeClass('edit-mode');
            $('#btnAddToTable').html('<i class="fas fa-arrow-down"></i> Add to Table');
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
            let idr = 0, usd = 0, hpp = 0, sell = 0;
            $('.item-row').each(function() {
                const hasCat = $(this).find('.category-cell').length > 0;
                const off = hasCat ? 0 : -1;
                const tds = $(this).find('td');
                idr  += parseRupiah(tds.eq(3 + off).find('.value-cell').text() || tds.eq(3 + off).text());
                usd  += parseRupiah(tds.eq(4 + off).find('.value-cell').text() || tds.eq(4 + off).text());
                sell += parseRupiah(tds.eq(6 + off).find('.value-cell').text() || tds.eq(6 + off).text());
                hpp  += parseRupiah(tds.eq(5 + off).find('.value-cell').text() || tds.eq(5 + off).text());
            });
            // Add tax rows
            $('#taxTableBody tr.tax-row').each(function() {
                const incSell = parseInt($(this).attr('data-include-selling')) || 0;
                const incHpp  = parseInt($(this).attr('data-include-hpp'))     || 0;
                const rawSell = parseFloat($(this).attr('data-raw'))            || 0;
                const rawHpp  = parseFloat($(this).attr('data-raw-hpp'))        || 0;
                if (incSell) sell += rawSell;
                if (incHpp)  hpp  += rawHpp;
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
                <small class="text-muted">Fill the form above and click "Add to Table"</small>
            </td></tr>`;
        }

        $(document).ready(function() {
            updateGrandTotal();
            syncLpjNote();
        });

        // ============================================================
        // TAX JAVASCRIPT
        // ============================================================
        {
            const joOtherId  = '{{ $joOther->id_jo_other }}';
            const storeUrl   = '{{ route("jo-other.pajak.store") }}';
            const selectUrl  = '{{ route("pajak.select") }}';
            const csrf       = '{{ csrf_token() }}';

            $('#selectTax').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Select Tax --',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: selectUrl,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) { return { q: params.term }; },
                    processResults: function(res) { return { results: res.data || [] }; },
                    cache: true
                }
            });

            function getItemsSellingTotal() {
                let total = 0;
                $('.item-row').each(function() {
                    const hasCat = $(this).find('.category-cell').length > 0;
                    const off = hasCat ? 0 : -1;
                    const tds = $(this).find('td');
                    total += parseRupiah(tds.eq(5 + off).find('.value-cell').text() || tds.eq(5 + off).text());
                });
                return total;
            }

            function getItemsHppTotal() {
                let total = 0;
                $('.item-row').each(function() {
                    const hasCat = $(this).find('.category-cell').length > 0;
                    const off = hasCat ? 0 : -1;
                    const tds = $(this).find('td');
                    total += parseRupiah(tds.eq(6 + off).find('.value-cell').text() || tds.eq(6 + off).text());
                });
                return total;
            }

            function recalcPreview(forcePct) {
                const sel = $('#selectTax').select2('data');
                if (!sel || !sel.length || !sel[0].id) { $('#taxAmount').val(''); $('#taxAmountHpp').val(''); return; }
                const pct      = (forcePct !== undefined) ? forcePct : (parseFloat(sel[0].persentase) || parseFloat($('#editingTaxPersentase').val()) || 0);
                const baseSell = getItemsSellingTotal();
                const baseHpp  = getItemsHppTotal();
                $('#taxAmount').val(formatNumber(Math.round(baseSell * pct / 100 * 100) / 100));
                $('#taxAmountHpp').val(formatNumber(Math.round(baseHpp  * pct / 100 * 100) / 100));
            }

            $('#selectTax').on('select2:select select2:unselect select2:clear', function(e) {
                if (e.type === 'select2:select' && e.params && e.params.data && e.params.data.persentase !== undefined) {
                    recalcPreview(parseFloat(e.params.data.persentase) || 0);
                } else {
                    recalcPreview();
                }
            });

            function syncAmountVisibility() {
                if ($('#taxIncludeSelling').is(':checked')) {
                    $('#wrapTaxAmountSelling').css('display', 'flex');
                } else {
                    $('#wrapTaxAmountSelling').hide();
                    $('#taxAmount').val('');
                }
                if ($('#taxIncludeHpp').is(':checked')) {
                    $('#wrapTaxAmountHpp').css('display', 'flex');
                } else {
                    $('#wrapTaxAmountHpp').hide();
                    $('#taxAmountHpp').val('');
                }
                recalcPreview();
            }
            $('#taxIncludeSelling').on('change', syncAmountVisibility);
            $('#taxIncludeHpp').on('change', syncAmountVisibility);
            syncAmountVisibility();

            function nextRowNumber() {
                return $('.item-row').length + $('#taxTableBody tr.tax-row').length + 1;
            }

            $('#addTaxBtn').on('click', function() {
                const pajakId   = $('#selectTax').val();
                const editingId = $('#editingTaxId').val();
                if (!pajakId) {
                    Swal.fire({ icon: 'warning', title: 'Select Tax', text: 'Please select a tax type first.', confirmButtonColor: '#ffc107' });
                    return;
                }
                const incSelling = $('#taxIncludeSelling').is(':checked') ? 1 : 0;
                const incHpp     = $('#taxIncludeHpp').is(':checked') ? 1 : 0;
                if (!incSelling && !incHpp) {
                    Swal.fire({ icon: 'warning', title: 'Select Target', text: 'Please check at least one of Selling Price or HPP.', confirmButtonColor: '#ffc107' });
                    return;
                }

                if (editingId) {
                    const btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');
                    $.ajax({
                        url: '/data/jo-other/pajak/update/' + editingId,
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf },
                        data: { _method: 'PUT', include_selling: incSelling, include_hpp: incHpp },
                        success: function(res) {
                            if (res.success) {
                                const row = $('#taxTableBody tr.tax-row[data-tax-id="' + editingId + '"]');
                                const zero_cell = '<div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">0,00</span></div>';
                                row.attr('data-raw', res.data.nominal_pajak)
                                   .attr('data-raw-hpp', res.data.nominal_pajak_hpp)
                                   .attr('data-include-selling', res.data.include_selling ? 1 : 0)
                                   .attr('data-include-hpp', res.data.include_hpp ? 1 : 0);
                                const sellingHtml = res.data.include_selling
                                    ? '<div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">' + formatNumber(res.data.nominal_pajak) + '</span></div>'
                                    : zero_cell;
                                const hppHtml = res.data.include_hpp
                                    ? '<div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">' + formatNumber(res.data.nominal_pajak_hpp) + '</span></div>'
                                    : zero_cell;
                                row.find('.tax-selling').html(sellingHtml);
                                row.find('.tax-hpp').html(hppHtml);
                                updateGrandTotal();
                                $('#editingTaxId').val('');
                                $('#editingTaxPersentase').val('');
                                $('#selectTax').val(null).trigger('change');
                                $('#taxIncludeSelling').prop('checked', true);
                                $('#taxIncludeHpp').prop('checked', false);
                                syncAmountVisibility();
                                $('#addTaxBtn').removeClass('btn-success').addClass('btn-warning')
                                    .html('<i class="fas fa-plus me-1"></i>Add Tax to Table');
                                $('#cancelEditTaxBtn').hide();
                            } else {
                                Swal.fire({ icon: 'error', title: 'Failed', text: res.message || 'Something went wrong.' });
                            }
                        },
                        error: function(xhr) {
                            const err = xhr.responseJSON;
                            Swal.fire({ icon: 'error', title: 'Failed', text: err && err.message ? err.message : 'Request failed.' });
                        },
                        complete: function() { btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Tax'); }
                    });
                    return;
                }

                const btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Adding...');
                $.ajax({
                    url: storeUrl,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                    data: { id_jo_other: joOtherId, id_md_pajak: pajakId, include_selling: incSelling, include_hpp: incHpp },
                    success: function(res) {
                        if (res.success) {
                            appendTaxRow(res.data, nextRowNumber());
                            updateGrandTotal();
                            recalcPreview();
                            $('#selectTax').val(null).trigger('change');
                            $('#taxIncludeSelling').prop('checked', true);
                            $('#taxIncludeHpp').prop('checked', false);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Failed', text: res.message || 'Something went wrong.' });
                        }
                    },
                    error: function(xhr) {
                        const err = xhr.responseJSON;
                        Swal.fire({ icon: 'error', title: 'Failed', text: err && err.message ? err.message : 'Request failed.' });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('<i class="fas fa-plus me-1"></i>Add Tax to Table');
                    }
                });
            });

            $('#cancelEditTaxBtn').on('click', function() {
                $('#editingTaxId').val('');
                $('#editingTaxPersentase').val('');
                $('#selectTax').val(null).trigger('change');
                $('#taxIncludeSelling').prop('checked', true);
                $('#taxIncludeHpp').prop('checked', false);
                syncAmountVisibility();
                $('#addTaxBtn').removeClass('btn-success').addClass('btn-warning')
                    .html('<i class="fas fa-plus me-1"></i>Add Tax to Table');
                $(this).hide();
            });

            function appendTaxRow(d, rowNum) {
                const zero_cell  = '<div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">0,00</span></div>';
                const sellingVal = d.include_selling
                    ? '<div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">' + formatNumber(d.nominal_pajak) + '</span></div>'
                    : zero_cell;
                const hppVal = d.include_hpp
                    ? '<div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">' + formatNumber(d.nominal_pajak_hpp) + '</span></div>'
                    : zero_cell;
                const taxId   = d.id;
                const pajakId = d.id_md_pajak;
                const nama    = d.nama_pajak;
                const persen  = parseFloat(d.persentase) || 0;
                const nomJual = parseFloat(d.nominal_pajak) || 0;
                const nomHpp  = parseFloat(d.nominal_pajak_hpp) || 0;
                const incSell = d.include_selling ? 1 : 0;
                const incHpp  = d.include_hpp     ? 1 : 0;
                const $tr = $('<tr class="tax-row"></tr>')
                    .attr('data-tax-id',         taxId)
                    .attr('data-raw',             d.nominal_pajak)
                    .attr('data-raw-hpp',         d.nominal_pajak_hpp)
                    .attr('data-include-selling', incSell)
                    .attr('data-include-hpp',     incHpp);
                $tr.append('<td class="item-number-cell text-center">' + rowNum + '</td>');
                $tr.append('<td class="category-cell" style="background:#fffbea !important;color:#92400e;">Tax</td>');
                $tr.append('<td class="item-text-cell text-start"><span class="fw-semibold" style="color:#2c3e50;">' + $('<span>').text(nama).html() + '</span><small class="text-muted ms-1">(' + persen + '%)</small></td>');
                $tr.append('<td><div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">0,00</span></div></td>');
                $tr.append('<td><div class="currency-group-cell"><span class="currency-label-cell">USD</span><span class="value-cell">0,00</span></div></td>');
                $tr.append('<td class="tax-selling">' + sellingVal + '</td>');
                $tr.append('<td class="tax-hpp">' + hppVal + '</td>');
                const $editBtn = $('<button type="button" class="btn btn-primary btn-sm btn-edit-row me-1" title="Edit Tax"><i class="fas fa-edit"></i></button>')
                    .on('click', function() { editTax(taxId, pajakId, nama, incSell, incHpp, persen, nomJual, nomHpp); });
                const $delBtn  = $('<button type="button" class="btn btn-danger btn-sm btn-remove-row" title="Remove Tax"><i class="fas fa-trash-alt"></i></button>')
                    .on('click', function() { deleteTax(this, taxId); });
                $tr.append($('<td class="text-center"></td>').append($editBtn).append($delBtn));
                $('#taxTableBody').append($tr);
            }
        } // end TAX block scope
    </script>
@endpush
