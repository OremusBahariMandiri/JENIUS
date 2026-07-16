@extends('layouts.app')

@section('title', 'Edit Cash Advance Contract')

@php
    $mergedItemsJs = collect($mergedItems)->map(
        fn($i) => [
            'id_jo_cont_item' => $i['id_jo_cont_item'],
            'invoice_typ' => $i['invoice_typ'],
            'invoice_ctg' => $i['invoice_ctg'],
            'hargajual_idr' => $i['hargajual_idr'],
            'hpp_ops' => $i['hpp_ops'],
            'id_kasbon_cont_item' => $i['id_kasbon_cont_item'],
            'nilai_hpp_cont_item' => $i['nilai_hpp_cont_item'],
            'nilai_kasbon' => $i['nilai_kasbon'],
            'total_kasbon' => $i['total_kasbon'],
            'has_kasbon' => $i['has_kasbon'],
        ],
    );
@endphp

@push('styles')
    <style>
        .kasbonContractEditPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .kasbonContractEditPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: 600;
        }

        .floating-badge-alert {
            position: fixed;
            top: 80px;
            right: 30px;
            z-index: 9999;
            min-width: 260px;
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
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
        }

        .floating-badge-alert.alert-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .floating-badge-alert.alert-error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .floating-badge-alert i { font-size: 1.3rem; }

        .floating-badge-alert .alert-text {
            flex: 1;
            font-weight: 600;
            font-size: 0.95rem;
        }

        @keyframes slideInRight {
            from { transform: translateX(400px); opacity: 0; }
            to   { transform: translateX(0);     opacity: 1; }
        }

        @keyframes slideOutRight {
            from { transform: translateX(0);     opacity: 1; }
            to   { transform: translateX(400px); opacity: 0; }
        }

        .floating-badge-alert.hiding { animation: slideOutRight 0.4s ease-in; }

        .confirm-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px);
        }

        .confirm-modal-overlay.show { display: flex; }

        .confirm-modal-box {
            background: white;
            border-radius: 16px;
            padding: 32px;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalIn 0.25s ease-out;
        }

        @keyframes modalIn {
            from { transform: scale(0.9); opacity: 0; }
            to   { transform: scale(1);   opacity: 1; }
        }

        .confirm-modal-box .modal-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 16px;
        }

        .confirm-modal-box .modal-icon.danger  { background: #fee2e2; color: #dc2626; }
        .confirm-modal-box .modal-icon.warning { background: #fef3c7; color: #d97706; }

        .confirm-modal-box h5 {
            text-align: center;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .confirm-modal-box p {
            text-align: center;
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 24px;
        }

        .confirm-modal-box .modal-actions { display: flex; gap: 10px; }
        .confirm-modal-box .modal-actions .btn { flex: 1; padding: 10px; font-weight: 600; }

        .input-item-card {
            border: 2px dashed #10b981;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            display: none;
        }

        .input-item-card.active {
            display: block;
            border: 2px solid #3b82f6;
            background: #f0f7ff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
        }

        .input-item-card .card-title-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid #bfdbfe;
        }

        .input-item-card .card-title-bar i    { color: #3b82f6; font-size: 1rem; }
        .input-item-card .card-title-bar span { font-weight: 700; color: #1e40af; font-size: 1rem; }

        .info-field {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .info-field .info-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-field .info-value {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e40af;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .currency-group { display: flex; align-items: stretch; }

        .currency-group .currency-label {
            background-color: #2c3e50;
            color: white;
            padding: 0 14px;
            font-size: 0.7rem;
            border-radius: 8px 0 0 8px;
            min-width: 52px;
            text-align: center;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .currency-group .currency-input {
            border-radius: 0 8px 8px 0 !important;
            border-left: none !important;
            font-size: 1.05rem;
            font-weight: 600;
        }

        .currency-group .currency-input:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25) !important;
        }

        .btn-save-item-input {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            color: white;
            padding: 10px 28px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
            transition: all 0.25s;
        }

        .btn-save-item-input:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(59,130,246,0.45); color: white; }

        .btn-cancel-item-input {
            background: white;
            border: 1px solid #d1d5db;
            color: #6b7280;
            padding: 10px 22px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.25s;
        }

        .btn-cancel-item-input:hover { background: #f3f4f6; color: #374151; }

        /* ===== ITEMS TABLE ===== */
        .table-kasbon thead th {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #2c3e50;
            padding: 12px 10px;
            font-weight: 600;
            font-size: 0.875rem;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
        }

        .table-kasbon tbody td {
            border: 1px solid #dee2e6;
            padding: 10px 12px;
            vertical-align: middle;
            font-size: 0.875rem;
            background-color: #fff;
        }

        .table-kasbon tbody tr:hover td    { background-color: #f0fdf4; }
        .table-kasbon tbody tr.tr-active td { background-color: #dbeafe !important; }

        /* ── Category merged cell ── */
        .table-kasbon .category-cell {
            background: #e8f5e9 !important;
            font-weight: 600;
            color: #2c3e50;
            text-align: center;
            vertical-align: middle !important;
            border-right: 2px solid #dee2e6;
        }

        .cell-right   { text-align: right; }
        .cell-center  { text-align: center; }
        .cell-readonly { background-color: #f8f9fa !important; color: #495057; font-weight: 500; }

        .table-kasbon tfoot td {
            background-color: #ffffff;
            color: #2C3E50;
            font-weight: 700;
            padding: 14px 12px;
            font-size: 0.9rem;
            border: 1px solid #dee2e6;
        }

        .footer-currency-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .footer-currency-label {
            background-color: #2C3E50;
            color: white;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: .78rem;
            font-weight: 700;
            min-width: 44px;
            text-align: center;
            flex-shrink: 0;
        }

        .footer-value {
            color: #2C3E50;
            font-weight: 700;
            font-size: .95rem;
            text-align: right;
            flex: 1;
        }

        .btn-edit-row {
            background-color: #3b82f6;
            border-color: #3b82f6;
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-edit-row:hover { background-color: #2563eb; border-color: #2563eb; transform: scale(1.08); }

        .btn-clear-row {
            background-color: #ef4444;
            border-color: #ef4444;
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-clear-row:hover:not(:disabled) { background-color: #dc2626; border-color: #dc2626; transform: scale(1.08); }
        .btn-clear-row:disabled { opacity: 0.35; cursor: not-allowed; transform: none !important; }

        .items-count-badge {
            background: #10b981;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 8px;
        }

        .no-items-row td {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
            background: #f8f9fa !important;
        }

        .final-save-section {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 18px 20px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px 12px 0 0;
            margin-top: 30px;
            z-index: 100;
        }

        .btn-final-back {
            background: linear-gradient(135deg, #868686, #5e5e5e);
            border: none;
            color: white;
            padding: 13px 35px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(27, 27, 27, 0.3);
            transition: all 0.3s;
        }

        .btn-final-back:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(49,49,49,0.45); color: white; }

        .jo-summary-card {
            background: #fff;
            border: 1.5px solid #10b981;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            transition: all 0.3s;
            animation: fadeInDown 0.35s ease-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .jo-summary-card .jo-meta-label {
            font-size: .72rem;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .jo-summary-card .jo-meta-value {
            font-size: .875rem;
            font-weight: 700;
            color: #2c3e50;
            margin-top: 2px;
        }

        #joItemsModal .modal-content  { border-radius: 12px; border: none; }
        #joItemsModal .modal-header   { background: #d1fae5; border-radius: 12px 12px 0 0; border-bottom: none; }
        #joItemsModal table thead th  { background: #2c3e50; color: white; border-color: #2c3e50; font-size: .85rem; padding: 10px; }
        #joItemsModal table tbody td  { font-size: .875rem; padding: 8px 10px; vertical-align: middle; }
        #joItemsModal table tbody tr:hover { background: #f0fdf4; }
        #joItemsModal tfoot td        { background: #f8f9fa; font-weight: 700; font-size: .875rem; padding: 10px; }
    </style>
@endpush

@section('content')

    {{-- FLOATING BADGE ALERT --}}
    <div id="floatingBadgeAlert" class="floating-badge-alert">
        <i class="fas fa-circle-notch fa-spin" id="alertIcon"></i>
        <div class="alert-text" id="alertText">Processing...</div>
    </div>

    {{-- CONFIRM MODAL --}}
    <div class="confirm-modal-overlay" id="confirmModal">
        <div class="confirm-modal-box">
            <div class="modal-icon" id="confirmModalIcon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h5 id="confirmModalTitle">Are you sure?</h5>
            <p id="confirmModalDesc">This action cannot be undone.</p>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline-secondary" id="confirmModalCancel">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmModalOk">Yes, proceed</button>
            </div>
        </div>
    </div>

    <div class="container-fluid kasbonContractEditPage">
        <div class="row">
            <div class="col-lg-12">

                {{-- PAGE HEADER --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold">
                            <i class="fas fa-edit me-2"></i>Edit Cash Advance Contract
                        </span>
                        <a href="{{ route('kasbon-contract.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <input type="hidden" id="current_kasbon_contract_id" value="{{ $kasbonContract->id }}">
                <input type="hidden" id="current_kasbon_cont_str" value="{{ $kasbonContract->id_kasbon_cont }}">
                <input type="hidden" id="original_id_jo_cont" value="{{ $kasbonContract->id_jo_cont }}">

                <form id="kasbonContractForm">
                    @csrf

                    {{-- ===== HEADER CARD ===== --}}
                    <div class="card shadow mb-4">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <h6 class="mb-0">
                                <i class="fas fa-money-bill-wave me-2"></i>Cash Advance Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Cash Advance No.</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="fas fa-file-alt"></i>
                                        </span>
                                        <input type="text" class="form-control fw-bold"
                                            value="{{ $kasbonContract->id_kasbon_cont }}" readonly
                                            style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Cash Advance Date</label>
                                    <input type="date" name="tgl_kasbon" id="tgl_kasbon" class="form-control"
                                        value="{{ $kasbonContract->tgl_kasbon ? $kasbonContract->tgl_kasbon->format('Y-m-d') : '' }}"
                                        required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label required-field">JO Contract</label>
                                    <select name="id_jo_cont" id="id_jo_cont" class="form-select select2-field" required>
                                        <option value="">-- Select JO Contract --</option>
                                        @foreach ($joContracts as $jo)
                                            <option value="{{ $jo->id_jo_cont }}"
                                                data-no="{{ $jo->no_jo_cont }}"
                                                data-title="{{ $jo->title }}"
                                                data-tgl="{{ optional($jo->tgl_jo_cont)->format('Y-m-d') ?? '' }}"
                                                data-contract="{{ $jo->contract->nama_cont ?? '—' }}"
                                                data-area="{{ $jo->area->area ?? '—' }}"
                                                data-note="{{ $jo->note ?? '' }}"
                                                {{ $kasbonContract->id_jo_cont == $jo->id_jo_cont ? 'selected' : '' }}>
                                                {{ $jo->no_jo_cont }} — {{ $jo->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted mt-1 d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Changing the JO Contract will reload the page to refresh the items list.
                                    </small>
                                </div>

                                {{-- JO Contract Summary Card --}}
                                <div class="col-md-12" id="joSummaryWrapper" style="display:none;">
                                    <div class="jo-summary-card">
                                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                            <div class="d-flex align-items-center gap-3" style="flex:1; min-width:0;">
                                                <div style="width:48px; height:48px; border-radius:10px; background:#d1fae5;
                                                    display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                    <i class="fas fa-file-contract" style="color:#059669; font-size:1.2rem;"></i>
                                                </div>
                                                <div style="min-width:0;">
                                                    <div id="joSummaryNo" style="font-weight:700; font-size:1.05rem; color:#2c3e50; letter-spacing:.4px;"></div>
                                                    <div id="joSummaryTitle" style="font-size:.875rem; color:#6b7280; margin-top:3px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></div>
                                                </div>
                                            </div>
                                            <button type="button" id="btnJoDetail"
                                                style="background:#2c3e50; color:#fff; border:none; border-radius:8px;
                                                    padding:8px 18px; font-size:.82rem; font-weight:600;
                                                    display:flex; align-items:center; gap:6px; cursor:pointer; white-space:nowrap; flex-shrink:0;"
                                                onmouseover="this.style.background='#1a252f'"
                                                onmouseout="this.style.background='#2c3e50'">
                                                <i class="fas fa-list-ul"></i> View Items
                                            </button>
                                        </div>
                                        <div style="border-top:1px solid #d1fae5; margin:14px 0;"></div>
                                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px 24px;">
                                            <div>
                                                <div class="jo-meta-label">Contract No.</div>
                                                <div class="jo-meta-value" id="joSummaryNo2" style="font-family:monospace; font-size:.82rem;"></div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Contract Date</div>
                                                <div class="jo-meta-value" id="joSummaryDate"></div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Total Items</div>
                                                <div class="jo-meta-value" id="joSummaryItemCount">
                                                    <i class="fas fa-circle-notch fa-spin" style="color:#10b981; font-size:.8rem;"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Title</div>
                                                <div class="jo-meta-value" id="joSummaryContract"></div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Area</div>
                                                <div class="jo-meta-value" id="joSummaryArea"></div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Note</div>
                                                <div class="jo-meta-value" id="joSummaryNote" style="font-size:.82rem; line-height:1.4; white-space:pre-wrap;">—</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Department</label>
                                    <select name="id_md_dep" id="id_md_dep" class="form-select select2-field" required>
                                        <option value="">-- Select Department --</option>
                                        @foreach ($departemens as $dep)
                                            <option value="{{ $dep->id_md_dep }}"
                                                {{ $kasbonContract->id_md_dep == $dep->id_md_dep ? 'selected' : '' }}>
                                                {{ $dep->nama_dep }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Branch</label>
                                    <select name="id_md_cabang" id="id_md_cabang" class="form-select select2-field" required>
                                        <option value="">-- Select Branch --</option>
                                        @foreach ($cabangs as $cabang)
                                            <option value="{{ $cabang->id_md_branch }}"
                                                {{ $kasbonContract->id_md_cabang == $cabang->id_md_branch ? 'selected' : '' }}>
                                                {{ $cabang->nama_branch }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Release To</label>
                                    <select name="id_md_release" id="id_md_release" class="form-select select2-field" required>
                                        <option value="">-- Select Release To --</option>
                                        @foreach ($releases as $rel)
                                            <option value="{{ $rel->id_md_release }}"
                                                {{ $kasbonContract->id_md_release == $rel->id_md_release ? 'selected' : '' }}>
                                                {{ $rel->nama_release ?? $rel->id_md_release }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Release Date</label>
                                    <input type="date" name="tgl_release" id="tgl_release" class="form-control"
                                        value="{{ $kasbonContract->tgl_release ? $kasbonContract->tgl_release->format('Y-m-d') : '' }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Note</label>
                                    <textarea name="note" id="note" class="form-control" rows="3">{{ $kasbonContract->note }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-success px-4" id="btnSaveHeader">
                                    <i class="fas fa-save me-1"></i> Update Header
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ===== ITEMS CARD ===== --}}
                    <div class="card shadow mb-4" id="itemsCard">
                        <div class="card-header text-black d-flex justify-content-between align-items-center"
                            style="background-color: #d1fae5">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                Cash Advance Items
                                <span class="items-count-badge" id="itemsCountBadge">0</span>
                            </h6>
                            <button type="button" class="btn btn-sm btn-danger" id="btnResetAllItems"
                                style="border-radius:8px;">
                                <i class="fas fa-trash-alt me-1"></i> Reset All CA Amounts
                            </button>
                        </div>
                        <div class="card-body p-4">

                            {{-- ===== INPUT CARD ===== --}}
                            <div class="input-item-card" id="inputItemCard">
                                <div class="card-title-bar">
                                    <i class="fas fa-pen-to-square"></i>
                                    <span>Input CA Amount</span>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="info-field">
                                            <div class="info-label">Description</div>
                                            <div class="info-value" id="infoInvoiceTyp">—</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="info-field">
                                            <div class="info-label">Category</div>
                                            <div class="info-value" id="infoInvoiceCtg">—</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-field">
                                            <div class="info-label">Selling Price (IDR)</div>
                                            <div class="info-value" id="infoHargaJual">—</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-field">
                                            <div class="info-label">Total CA / HPP (IDR)</div>
                                            <div class="info-value" id="infoTotalKasbon">—</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold" style="color:#1e40af; font-size:0.875rem;">
                                            CA Amount (IDR) <span style="color:#dc3545;">*</span>
                                        </label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="inputNilaiKasbon"
                                                class="form-control currency-input" placeholder="0,00"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2 mt-1">
                                        <button type="button" class="btn btn-cancel-item-input" id="btnCancelItemInput">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </button>
                                        <button type="button" class="btn btn-save-item-input" id="btnSaveItemInput">
                                            <i class="fas fa-save me-1"></i> Save CA Amount
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" id="activeJoContItemId" value="">
                                <input type="hidden" id="activeRowIndex" value="">
                            </div>

                            {{-- ===== TABLE ===== --}}
                            <div class="table-responsive">
                                <table class="table table-kasbon table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:4%;">No</th>
                                            <th style="width:13%;">Category</th>
                                            <th class="text-start" style="min-width:200px;">Description</th>
                                            <th style="width:15%;">Selling Price (IDR)</th>
                                            <th style="width:15%;">Total CA / HPP (IDR)</th>
                                            <th style="width:15%;">CA Amount (IDR)</th>
                                            <th style="width:10%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kasbonItemsBody">
                                        <tr id="loadingRow">
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="fas fa-circle-notch fa-spin me-2"></i> Loading data...
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-center pe-3">
                                                <strong>GRAND TOTAL</strong>
                                            </td>
                                            <td>
                                                <div class="footer-currency-wrap">
                                                    <span class="footer-currency-label">IDR</span>
                                                    <span class="footer-value" id="footerTotalHargaJual">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="footer-currency-wrap">
                                                    <span class="footer-currency-label">IDR</span>
                                                    <span class="footer-value" id="footerTotalHPP">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="footer-currency-wrap">
                                                    <span class="footer-currency-label">IDR</span>
                                                    <span class="footer-value" id="footerTotalCA">0,00</span>
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

                {{-- FINAL BACK --}}
                <div class="final-save-section">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('kasbon-contract.index') }}" class="btn btn-final-back">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== JO ITEMS DETAIL MODAL ===== --}}
    <div class="modal fade" id="joItemsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 20px 60px rgba(0,0,0,.15);">
                <div class="modal-header"
                    style="background:#d1fae5; border-radius:12px 12px 0 0; border-bottom:none; padding:1.25rem 1.5rem;">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="color:#2c3e50;">
                            <i class="fas fa-list-ul me-2" style="color:#059669;"></i>JO Contract Items
                        </h5>
                        <small id="joItemsModalSubtitle" style="color:#6b7280; font-size:.8rem; display:block; margin-top:2px;"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.5rem; min-height:220px;">
                    <div id="joItemsModalLoading" class="text-center py-5">
                        <i class="fas fa-circle-notch fa-spin fa-2x" style="color:#10b981;"></i>
                        <p class="mt-2 text-muted mb-0">Loading items...</p>
                    </div>
                    <div id="joItemsModalContent" style="display:none;">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" style="font-size:.875rem;">
                                <thead>
                                    <tr>
                                        <th style="width:5%; text-align:center;">No</th>
                                        <th>Invoice Type</th>
                                        <th>Category</th>
                                        <th style="text-align:right;">HPP Ops (IDR)</th>
                                        <th style="text-align:right;">Selling Price (IDR)</th>
                                    </tr>
                                </thead>
                                <tbody id="joItemsModalBody"></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" style="text-align:right; font-weight:700;">GRAND TOTAL</td>
                                        <td style="text-align:right; font-weight:700; color:#059669; font-family:monospace;" id="joItemsTotalHpp"></td>
                                        <td style="text-align:right; font-weight:700; font-family:monospace;" id="joItemsTotalSell"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div id="joItemsModalEmpty" style="display:none; text-align:center; padding:40px 20px; color:#6c757d;">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        <p class="fw-bold mb-0">No items found for this JO Contract</p>
                        <small>Items may not have been added yet.</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #d1fae5; padding:.75rem 1.5rem;">
                    <small id="joItemsModalCount" class="text-muted me-auto"></small>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const currentKasbonContractId = {{ $kasbonContract->id }};
        const currentKasbonContStr    = '{{ $kasbonContract->id_kasbon_cont }}';
        const originalIdJoCont        = '{{ $kasbonContract->id_jo_cont }}';
        const bulkSaveUrl             = '{{ route('kasbon-contract.items.bulk-save', $kasbonContract->id) }}';
        const updateHeaderUrl         = '/kasbon-contract/header/update/{{ $kasbonContract->id }}';
        const csrfToken               = $('meta[name="csrf-token"]').attr('content');

        let mergedItems     = @json($mergedItemsJs);
        let confirmCallback = null;
        let joItemsCache    = [];

        // ── CONFIRM MODAL ──
        function showConfirm({ title, desc, okLabel = 'Yes, proceed', okClass = 'btn-danger', iconClass = 'danger' }, callback) {
            $('#confirmModalTitle').text(title);
            $('#confirmModalDesc').text(desc);
            $('#confirmModalOk').text(okLabel).removeClass().addClass(`btn ${okClass}`);
            $('#confirmModalIcon').removeClass('danger warning').addClass(iconClass);
            confirmCallback = callback;
            $('#confirmModal').addClass('show');
        }

        $('#confirmModalCancel, #confirmModal').on('click', function (e) {
            if (e.target === this) { $('#confirmModal').removeClass('show'); confirmCallback = null; }
        });

        $('#confirmModalOk').on('click', function () {
            $('#confirmModal').removeClass('show');
            if (typeof confirmCallback === 'function') confirmCallback();
            confirmCallback = null;
        });

        // ── FLOATING ALERT ──
        function showFloatingAlert(type, message) {
            const $alert = $('#floatingBadgeAlert'), $icon = $('#alertIcon');
            $alert.removeClass('alert-saving alert-success alert-error hiding');
            switch (type) {
                case 'saving':  $alert.addClass('alert-saving');  $icon.attr('class', 'fas fa-circle-notch fa-spin'); break;
                case 'success': $alert.addClass('alert-success'); $icon.attr('class', 'fas fa-check-circle'); break;
                default:        $alert.addClass('alert-error');   $icon.attr('class', 'fas fa-exclamation-circle');
            }
            $('#alertText').text(message);
            $alert.addClass('show');
            if (type !== 'saving') setTimeout(hideFloatingAlert, 3000);
        }

        function hideFloatingAlert() {
            $('#floatingBadgeAlert').addClass('hiding');
            setTimeout(() => $('#floatingBadgeAlert').removeClass('show hiding'), 400);
        }

        // ── NUMBER UTILS ──
        function formatRupiah(value) {
            let number = value.toString().replace(/[^\d,]/g, '').replace(/\./g, '');
            if (number === '') return '';
            let parts = number.split(','), intPart = parts[0], dec = parts.length > 1 ? parts[1] : '';
            if (dec.length > 2) dec = dec.substring(0, 2);
            intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return parts.length > 1 ? intPart + ',' + dec : intPart + ',00';
        }

        function parseRupiah(value) {
            if (!value && value !== 0) return 0;
            return parseFloat(value.toString().replace(/\./g, '').replace(',', '.')) || 0;
        }

        function formatNumber(amount) {
            return parseFloat(amount || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function setupRupiahInput(input) {
            input.addEventListener('input', function () {
                let cursor = this.selectionStart, before = this.value.substring(0, cursor);
                this.value = formatRupiah(this.value);
                if (!before.includes(',')) {
                    let digits = before.replace(/\D/g, '').length, newPos = 0, count = 0;
                    for (let i = 0; i < this.value.length; i++) {
                        if (/\d/.test(this.value[i])) { count++; if (count === digits) { newPos = i + 1; break; } }
                    }
                    this.setSelectionRange(newPos, newPos);
                } else {
                    let commaPos = this.value.indexOf(',');
                    let decDigits = (before.split(',')[1] || '').length;
                    let newPos = commaPos + 1 + Math.min(decDigits, 2);
                    this.setSelectionRange(newPos, newPos);
                }
            });
            input.addEventListener('blur', function () {
                if (this.value) {
                    if (!this.value.includes(',')) this.value += ',00';
                    else {
                        let parts = this.value.split(',');
                        if (parts[1] !== undefined) {
                            if (parts[1].length === 0) this.value = parts[0] + ',00';
                            else if (parts[1].length < 2) this.value = parts[0] + ',' + parts[1].padEnd(2, '0');
                        }
                    }
                }
            });
        }

        // ── GROUP BY CATEGORY ──
        function groupByCategory(items) {
            const groups = {}, order = [];
            items.forEach(function (item) {
                const cat = item.invoice_ctg || 'Uncategorized';
                if (!groups[cat]) { groups[cat] = []; order.push(cat); }
                groups[cat].push(item);
            });
            return { groups, order };
        }

        // ── RENDER TABLE — category merged (rowspan) ──
        function renderTable() {
            const $tbody = $('#kasbonItemsBody');
            $tbody.empty();
            $('#itemsCountBadge').text(mergedItems.length);

            if (!mergedItems.length) {
                $tbody.html(`
                    <tr class="no-items-row">
                        <td colspan="7">
                            <i class="fas fa-inbox fa-3x d-block mb-3 text-muted opacity-50"></i>
                            <p class="mb-1 fw-bold">No items found for this JO Contract</p>
                            <small>Make sure the selected JO Contract has items</small>
                        </td>
                    </tr>`);
                updateFooter();
                return;
            }

            const { groups, order } = groupByCategory(mergedItems);
            let rowNo = 1;

            order.forEach(function (category) {
                const items   = groups[category];
                const rowspan = items.length;

                items.forEach(function (item, catIndex) {
                    const globalIndex = mergedItems.indexOf(item);
                    const hasFilled   = item.has_kasbon && item.nilai_kasbon > 0;

                    const statusBadge = hasFilled
                        ? `<span class="badge bg-success ms-1" style="font-size:0.65rem;">Saved</span>`
                        : `<span class="badge bg-secondary ms-1" style="font-size:0.65rem;">Not filled</span>`;

                    const clearBtn = `
                        <button type="button" class="btn btn-danger btn-sm btn-clear-row"
                            onclick="clearItem(${globalIndex})"
                            ${!hasFilled ? 'disabled title="No CA amount to clear"' : 'title="Clear CA amount"'}>
                            <i class="fas fa-trash"></i>
                        </button>`;

                    // Category cell hanya di baris pertama tiap group
                    const categoryCell = catIndex === 0
                        ? `<td class="category-cell" rowspan="${rowspan}">${category}</td>`
                        : '';

                    const row = `
                        <tr class="item-row" id="row_${globalIndex}"
                            data-index="${globalIndex}"
                            data-jo-item="${item.id_jo_cont_item}"
                            data-category="${category}">
                            <td class="cell-center fw-bold text-muted">${rowNo++}</td>
                            ${categoryCell}
                            <td class="text-start">
                                <span class="fw-semibold" style="color:#2c3e50;">${item.invoice_typ}</span>
                                ${statusBadge}
                            </td>
                            <td class="cell-readonly cell-right">${formatNumber(item.hargajual_idr)}</td>
                            <td class="cell-readonly cell-right">${formatNumber(item.hpp_ops)}</td>
                            <td class="cell-right nilai-kasbon-cell">${formatNumber(item.nilai_kasbon)}</td>
                            <td class="cell-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" class="btn btn-primary btn-sm btn-edit-row"
                                        onclick="openEditItem(${globalIndex})" title="Edit CA amount">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    ${clearBtn}
                                </div>
                            </td>
                        </tr>`;

                    $tbody.append(row);
                });
            });

            updateFooter();
        }

        // ── Helper: refresh satu baris setelah save/clear ──
        function refreshRowAfterSave(globalIndex, nilaiKasbon, saved) {
            const $row = $(`#row_${globalIndex}`);
            $row.find('.nilai-kasbon-cell').text(formatNumber(nilaiKasbon));
            $row.find('.badge')
                .removeClass(saved ? 'bg-secondary' : 'bg-success')
                .addClass(saved ? 'bg-success' : 'bg-secondary')
                .text(saved ? 'Saved' : 'Not filled');
            $row.find('.btn-clear-row')
                .prop('disabled', !saved)
                .attr('title', saved ? 'Clear CA amount' : 'No CA amount to clear');
        }

        // ── OPEN EDIT ITEM ──
        function openEditItem(index) {
            const item = mergedItems[index];
            $('#infoInvoiceTyp').text(item.invoice_typ);
            $('#infoInvoiceCtg').text(item.invoice_ctg);
            $('#infoHargaJual').text(formatNumber(item.hargajual_idr));
            $('#infoTotalKasbon').text(formatNumber(item.hpp_ops));
            $('#inputNilaiKasbon').val('');
            $('#activeJoContItemId').val(item.id_jo_cont_item);
            $('#activeRowIndex').val(index);
            $('.item-row').removeClass('tr-active');
            $(`#row_${index}`).addClass('tr-active');
            $('#inputItemCard').addClass('active');
            $('html, body').animate({ scrollTop: $('#inputItemCard').offset().top - 120 }, 400);
            setTimeout(() => $('#inputNilaiKasbon').focus(), 450);
        }

        $('#btnCancelItemInput').on('click', closeInputCard);

        function closeInputCard() {
            $('#inputItemCard').removeClass('active');
            $('#inputNilaiKasbon').val('');
            $('#activeJoContItemId').val('');
            $('#activeRowIndex').val('');
            $('.item-row').removeClass('tr-active');
        }

        // ── SAVE ITEM ──
        $('#btnSaveItemInput').on('click', saveItemInput);
        $('#inputNilaiKasbon').on('keydown', function (e) { if (e.key === 'Enter') saveItemInput(); });

        function saveItemInput() {
            const joContItemId = $('#activeJoContItemId').val();
            const rowIndex     = parseInt($('#activeRowIndex').val());
            const nilaiKasbon  = parseRupiah($('#inputNilaiKasbon').val());

            if (!joContItemId) { showFloatingAlert('error', 'No item selected'); return; }
            if (nilaiKasbon <= 0) { showFloatingAlert('error', 'Please enter a CA Amount'); return; }

            showFloatingAlert('saving', 'Saving...');
            $('#btnSaveItemInput').prop('disabled', true);

            $.ajax({
                url: bulkSaveUrl,
                method: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: JSON.stringify({ items: [{ id_jo_cont_item: joContItemId, nilai_kasbon: nilaiKasbon }], _token: csrfToken }),
                success: function (response) {
                    $('#btnSaveItemInput').prop('disabled', false);
                    if (response.success) {
                        showFloatingAlert('success', 'CA Amount saved successfully!');
                        mergedItems[rowIndex].nilai_kasbon = nilaiKasbon;
                        mergedItems[rowIndex].has_kasbon   = true;
                        refreshRowAfterSave(rowIndex, nilaiKasbon, true);
                        updateFooter();
                        closeInputCard();
                    } else {
                        showFloatingAlert('error', response.message || 'Failed to save');
                    }
                },
                error: function (xhr) {
                    $('#btnSaveItemInput').prop('disabled', false);
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to save item');
                }
            });
        }

        // ── CLEAR SINGLE ITEM ──
        function clearItem(index) {
            const item = mergedItems[index];
            showConfirm({
                title: 'Clear CA Amount?',
                desc: `This will remove the CA amount for "${item.invoice_typ}".`,
                okLabel: 'Yes, clear it',
                okClass: 'btn-danger',
                iconClass: 'danger',
            }, function () {
                showFloatingAlert('saving', 'Clearing...');
                $.ajax({
                    url: bulkSaveUrl,
                    method: 'POST',
                    contentType: 'application/json',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    data: JSON.stringify({ items: [{ id_jo_cont_item: item.id_jo_cont_item, nilai_kasbon: 0 }], _token: csrfToken }),
                    success: function (response) {
                        if (response.success) {
                            showFloatingAlert('success', 'CA Amount cleared!');
                            mergedItems[index].nilai_kasbon = 0;
                            mergedItems[index].has_kasbon   = false;
                            refreshRowAfterSave(index, 0, false);
                            if (parseInt($('#activeRowIndex').val()) === index) closeInputCard();
                            updateFooter();
                        } else {
                            showFloatingAlert('error', response.message || 'Failed to clear');
                        }
                    },
                    error: function (xhr) {
                        showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to clear item');
                    }
                });
            });
        }

        // ── RESET ALL ──
        $('#btnResetAllItems').on('click', function () {
            const filledCount = mergedItems.filter(i => i.has_kasbon && i.nilai_kasbon > 0).length;
            if (filledCount === 0) { showFloatingAlert('error', 'No CA amounts to reset'); return; }

            showConfirm({
                title: 'Reset All CA Amounts?',
                desc: `This will clear the CA amount for all ${filledCount} filled item(s).`,
                okLabel: 'Yes, reset all',
                okClass: 'btn-danger',
                iconClass: 'danger',
            }, function () {
                showFloatingAlert('saving', 'Resetting all CA amounts...');
                const payload = mergedItems.filter(i => i.has_kasbon && i.nilai_kasbon > 0)
                    .map(i => ({ id_jo_cont_item: i.id_jo_cont_item, nilai_kasbon: 0 }));

                $.ajax({
                    url: bulkSaveUrl,
                    method: 'POST',
                    contentType: 'application/json',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    data: JSON.stringify({ items: payload, _token: csrfToken }),
                    success: function (response) {
                        if (response.success) {
                            showFloatingAlert('success', 'All CA amounts have been reset!');
                            mergedItems.forEach(function (item, index) {
                                if (item.has_kasbon && item.nilai_kasbon > 0) {
                                    mergedItems[index].nilai_kasbon = 0;
                                    mergedItems[index].has_kasbon   = false;
                                    refreshRowAfterSave(index, 0, false);
                                }
                            });
                            closeInputCard();
                            updateFooter();
                        } else {
                            showFloatingAlert('error', response.message || 'Failed to reset');
                        }
                    },
                    error: function (xhr) {
                        showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to reset CA amounts');
                    }
                });
            });
        });

        // ── UPDATE FOOTER ──
        function updateFooter() {
            let totalHargaJual = 0, totalHPP = 0, totalCA = 0;
            mergedItems.forEach(function (item) {
                totalHargaJual += parseFloat(item.hargajual_idr) || 0;
                totalHPP       += parseFloat(item.hpp_ops)       || 0;
                totalCA        += parseFloat(item.nilai_kasbon)   || 0;
            });
            $('#footerTotalHargaJual').text(formatNumber(totalHargaJual));
            $('#footerTotalHPP').text(formatNumber(totalHPP));
            $('#footerTotalCA').text(formatNumber(totalCA));
        }

        // ── JO ITEMS MODAL ──
        function renderJoItemsModal(items) {
            $('#joItemsModalLoading').hide();
            if (!items || !items.length) { $('#joItemsModalEmpty').show(); return; }

            let rows = '', totalHpp = 0, totalSell = 0;
            items.forEach(function (item, idx) {
                const hpp = parseFloat(item.hpp_ops || 0), sell = parseFloat(item.hargajual_idr || 0);
                totalHpp += hpp; totalSell += sell;
                rows += `
                    <tr>
                        <td style="text-align:center;">${idx + 1}</td>
                        <td style="font-weight:600;">${item.invoice_typ || item.id_jo_cont_item}</td>
                        <td>
                            <span style="background:#d1fae5; color:#065f46; padding:3px 10px;
                                border-radius:20px; font-size:.78rem; font-weight:600;">
                                ${item.invoice_ctg || '—'}
                            </span>
                        </td>
                        <td style="text-align:right; font-family:monospace;">${formatNumber(hpp)}</td>
                        <td style="text-align:right; font-family:monospace;">${formatNumber(sell)}</td>
                    </tr>`;
            });
            $('#joItemsModalBody').html(rows);
            $('#joItemsTotalHpp').text(formatNumber(totalHpp));
            $('#joItemsTotalSell').text(formatNumber(totalSell));
            $('#joItemsModalCount').text(items.length + ' item' + (items.length !== 1 ? 's' : '') + ' found');
            $('#joItemsModalContent').show();
        }

        // ── UPDATE HEADER ──
        $('#btnSaveHeader').on('click', function () {
            const idJoCont  = $('#id_jo_cont').val();
            const idDep     = $('#id_md_dep').val();
            const idCabang  = $('#id_md_cabang').val();
            const idRelease = $('#id_md_release').val();
            const tglKasbon = $('#tgl_kasbon').val();

            if (!idJoCont || !idDep || !idCabang || !idRelease || !tglKasbon) {
                showFloatingAlert('error', 'Please fill in all required fields');
                return;
            }

            showFloatingAlert('saving', 'Updating header...');
            $('#btnSaveHeader').prop('disabled', true);

            $.ajax({
                url: updateHeaderUrl,
                method: 'POST',
                data: {
                    id_jo_cont:   idJoCont,
                    id_md_dep:    idDep,
                    id_md_cabang: idCabang,
                    id_md_release: idRelease,
                    tgl_kasbon:   tglKasbon,
                    tgl_release:  $('#tgl_release').val(),
                    note:         $('#note').val(),
                    _token:       csrfToken,
                },
                success: function (response) {
                    $('#btnSaveHeader').prop('disabled', false);
                    if (response.success) {
                        showFloatingAlert('success', 'Header updated successfully!');
                        if (idJoCont != originalIdJoCont) {
                            showFloatingAlert('saving', 'JO Contract changed — reloading page...');
                            setTimeout(() => window.location.reload(), 1200);
                        }
                    } else {
                        showFloatingAlert('error', response.message || 'Failed to update header');
                    }
                },
                error: function (xhr) {
                    $('#btnSaveHeader').prop('disabled', false);
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to update header');
                }
            });
        });

        // ── INIT ──
        $(document).ready(function () {
            $('.select2-field').select2({ theme: 'bootstrap-5', width: '100%' });

            function populateJoSummary(idJoCont) {
                if (!idJoCont) return;
                const $opt = $('#id_jo_cont').find(`option[value="${idJoCont}"]`);
                const no = $opt.data('no') || idJoCont, rawDate = $opt.data('tgl') || '';

                $('#joSummaryNo').text(no);
                $('#joSummaryNo2').text(no);
                $('#joSummaryTitle').text($opt.data('title') || '—');
                $('#joSummaryContract').text($opt.data('title') || '—');
                $('#joSummaryArea').text($opt.data('area') || '—');
                $('#joSummaryNote').text($opt.data('note') || '—');
                $('#joSummaryDate').text(rawDate
                    ? new Date(rawDate).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
                    : '—');
                $('#joSummaryItemCount').html('<i class="fas fa-circle-notch fa-spin" style="color:#10b981; font-size:.8rem;"></i>');
                $('#joSummaryWrapper').show();

                $.ajax({
                    url: '{{ route('kasbon-contract.jo-items.get') }}',
                    method: 'GET',
                    data: { id_jo_cont: idJoCont },
                    success: function (r) {
                        const n = r.success ? r.data.length : 0;
                        $('#joSummaryItemCount').text(n + ' item' + (n !== 1 ? 's' : ''));
                        if (r.success) joItemsCache = r.data;
                    },
                    error: function () { $('#joSummaryItemCount').text('—'); }
                });
            }

            populateJoSummary($('#id_jo_cont').val());

            $('#id_jo_cont').on('change', function () {
                const id = $(this).val();
                joItemsCache = [];
                if (!id) { $('#joSummaryWrapper').hide(); return; }
                populateJoSummary(id);
            });

            $(document).on('click', '#btnJoDetail', function () {
                const id = $('#id_jo_cont').val();
                if (!id) return;
                const $opt = $('#id_jo_cont').find('option:selected');
                $('#joItemsModalSubtitle').text(($opt.data('no') || id) + ' — ' + ($opt.data('title') || ''));
                $('#joItemsModalLoading').show();
                $('#joItemsModalContent').hide();
                $('#joItemsModalEmpty').hide();
                $('#joItemsModal').modal('show');

                if (joItemsCache.length > 0) {
                    renderJoItemsModal(joItemsCache);
                } else {
                    $.ajax({
                        url: '{{ route('kasbon-contract.jo-items.get') }}',
                        method: 'GET',
                        data: { id_jo_cont: id },
                        success: function (r) { joItemsCache = r.success ? r.data : []; renderJoItemsModal(joItemsCache); },
                        error: function () { renderJoItemsModal([]); }
                    });
                }
            });

            setupRupiahInput(document.getElementById('inputNilaiKasbon'));
            renderTable();
        });
    </script>
@endpush