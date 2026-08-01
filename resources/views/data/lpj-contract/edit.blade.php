@extends('layouts.app')

@section('title', 'Edit LPJ Contract')

@php
    $mergedItemsJs = collect($mergedItems)->map(
        fn($i) => [
            'id_kasbon_cont_item' => $i['id_kasbon_cont_item'],
            'id_kasbon_cont' => $i['id_kasbon_cont'],
            'id_jo_cont_item' => $i['id_jo_cont_item'],
            'id_kasbon_cont_no' => $i['id_kasbon_cont_no'],
            'invoice_typ' => $i['invoice_typ'],
            'invoice_ctg' => $i['invoice_ctg'],
            'nilai_hpp_cont_item' => $i['nilai_hpp_cont_item'],
            'nilai_kasbon' => $i['nilai_kasbon'],
            'total_kasbon' => $i['total_kasbon'],
            'id_lpj_cont_item' => $i['id_lpj_cont_item'],
            'amount_lpj' => $i['amount_lpj'],
            'has_lpj' => $i['has_lpj'],
            'id_md_chart_of_account' => $i['id_md_chart_of_account'] ?? null,
            'coa_no' => $i['coa_no'] ?? null,
            'coa_name' => $i['coa_name'] ?? null,
            'origin_lpj_cont' => $i['origin_lpj_cont'] ?? null,
        ],
    );
@endphp

@push('styles')
    <style>
        .lpjEditPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, .08);
        }

        .lpjEditPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .lpjEditPage .form-control:focus,
        .lpjEditPage .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, .25);
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: 600;
        }

        /* FLOATING ALERT */
        .floating-badge-alert {
            position: fixed;
            top: 80px;
            right: 30px;
            z-index: 9999;
            min-width: 260px;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .2);
            display: none;
            animation: slideInRight .4s ease-out;
            backdrop-filter: blur(10px);
        }

        .floating-badge-alert.show {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .floating-badge-alert.alert-saving {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #fff;
        }

        .floating-badge-alert.alert-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
        }

        .floating-badge-alert.alert-error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
        }

        .floating-badge-alert i {
            font-size: 1.3rem;
        }

        .floating-badge-alert .alert-text {
            flex: 1;
            font-weight: 600;
            font-size: .95rem;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0
            }

            to {
                transform: translateX(0);
                opacity: 1
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1
            }

            to {
                transform: translateX(400px);
                opacity: 0
            }
        }

        .floating-badge-alert.hiding {
            animation: slideOutRight .4s ease-in;
        }

        /* CONFIRM MODAL */
        .confirm-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px);
        }

        .confirm-modal-overlay.show {
            display: flex;
        }

        .confirm-modal-box {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .3);
            animation: modalIn .25s ease-out;
        }

        @keyframes modalIn {
            from {
                transform: scale(.9);
                opacity: 0
            }

            to {
                transform: scale(1);
                opacity: 1
            }
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

        .confirm-modal-box .modal-icon.danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .confirm-modal-box .modal-icon.warning {
            background: #fef3c7;
            color: #d97706;
        }

        .confirm-modal-box h5 {
            text-align: center;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .confirm-modal-box p {
            text-align: center;
            color: #6b7280;
            font-size: .9rem;
            margin-bottom: 24px;
        }

        .confirm-modal-box .modal-actions {
            display: flex;
            gap: 10px;
        }

        .confirm-modal-box .modal-actions .btn {
            flex: 1;
            padding: 10px;
            font-weight: 600;
        }

        /* INPUT CARD — green */
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

        .input-item-card .card-title-bar i {
            color: #3b82f6;
            font-size: 1rem;
        }

        .input-item-card .card-title-bar span {
            font-weight: 700;
            color: #1e40af;
            font-size: 1rem;
        }

        .info-field {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .info-field .info-label {
            font-size: .72rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 4px;
        }

        .info-field .info-value {
            font-size: .9rem;
            font-weight: 700;
            color: #1e40af;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .currency-group {
            display: flex;
            align-items: stretch;
        }

        .currency-group .currency-label {
            background: #2c3e50;
            color: #fff;
            padding: 0 14px;
            font-size: .7rem;
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
            color: #fff;
            padding: 10px 28px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
            transition: all .25s;
        }

        .btn-save-item-input:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(59, 130, 246, 0.45);
            color: white;
        }

        .btn-cancel-item-input {
            background: #fff;
            border: 1px solid #d1d5db;
            color: #6b7280;
            padding: 10px 22px;
            border-radius: 8px;
            font-weight: 600;
            transition: all .25s;
        }

        .btn-cancel-item-input:hover {
            background: #f3f4f6;
            color: #374151;
        }

        /* ITEMS TABLE */
        .table-lpj thead th {
            background: #2c3e50;
            color: #fff;
            border: 1px solid #2c3e50;
            padding: 12px 10px;
            font-weight: 600;
            font-size: .875rem;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
        }

        .table-lpj tbody td {
            border: 1px solid #dee2e6;
            padding: 10px 12px;
            vertical-align: middle;
            font-size: .875rem;
            background: #fff;
        }

        .table-lpj tbody tr:hover td {
            background: #f0fdf4;
        }

        .table-lpj tbody tr.tr-active td {
            background: #dcfce7 !important;
        }

        .table-lpj .category-cell {
            background: #ffffff !important;
            font-weight: 600;
            color: #000000;
            text-align: center;
            vertical-align: middle !important;
            border-right: 2px solid #dee2e6;
        }

        /* Blue row — item added from LPJ */
        .table-lpj tbody tr.row-from-lpj td {
            background: #eff6ff !important;
        }

        .table-lpj tbody tr.row-from-lpj:hover td {
            background: #dbeafe !important;
        }

        .table-lpj tbody tr.row-from-lpj.tr-active td {
            background: #bfdbfe !important;
        }

        .table-lpj tbody tr.row-from-lpj .category-cell {
            background: #dbeafe !important;
        }

        .table-lpj tbody tr.row-from-lpj .cell-readonly {
            background: #e0eeff !important;
        }

        .table-lpj tbody tr.row-from-lpj .coa-select-cell select {
            background: #eff6ff;
        }

        .cell-right {
            text-align: right;
        }

        .cell-center {
            text-align: center;
        }

        .cell-readonly {
            background: #f8f9fa !important;
            color: #495057;
            font-weight: 500;
        }

        /* COA display cell */
        .coa-display-cell {
            min-width: 190px;
        }

        .coa-display-badge {
            display: inline-block;
            font-size: .75rem;
            padding: 4px 8px;
            border-radius: 6px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #065f46;
            font-weight: 600;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .coa-empty-badge {
            display: inline-block;
            font-size: .72rem;
            padding: 3px 8px;
            border-radius: 6px;
            background: #f3f4f6;
            border: 1px dashed #d1d5db;
            color: #9ca3af;
            font-style: italic;
        }

        /* TFOOT */
        .table-lpj tfoot td {
            background: #fff;
            color: #2c3e50;
            font-weight: 700;
            padding: 14px 12px;
            font-size: .9rem;
            border: 1px solid #dee2e6;
        }

        .footer-currency-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .footer-currency-label {
            background: #2c3e50;
            color: #fff;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: .60rem;
            font-weight: 700;
            min-width: 44px;
            text-align: center;
            flex-shrink: 0;
        }

        .footer-value {
            color: #2c3e50;
            font-weight: 700;
            font-size: .70rem;
            text-align: right;
            flex: 1;
        }

        .btn-edit-row {
            background: #10b981;
            border-color: #10b981;
            padding: .3rem .6rem;
            font-size: .8rem;
            border-radius: 6px;
            transition: all .2s;
            color: #fff;
        }

        .btn-edit-row:hover {
            background: #059669;
            border-color: #059669;
            transform: scale(1.08);
            color: #fff;
        }

        .btn-clear-row {
            background: #ef4444;
            border-color: #ef4444;
            padding: .3rem .6rem;
            font-size: .8rem;
            border-radius: 6px;
            transition: all .2s;
            color: #fff;
        }

        .btn-clear-row:hover:not(:disabled) {
            background: #dc2626;
            border-color: #dc2626;
            transform: scale(1.08);
        }

        .btn-clear-row:disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        .items-count-badge {
            background: #10b981;
            color: #fff;
            font-size: .75rem;
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

        /* ADD ITEM SECTION */
        .add-item-form-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px dashed #10b981;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all .3s;
        }

        .add-item-form-section h6 {
            color: #065f46;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .add-item-form-section h6 i {
            color: #10b981;
            font-size: 1.2rem;
        }

        .add-item-form-section .form-control:focus,
        .add-item-form-section .form-select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.3rem rgba(16, 185, 129, .15);
        }

        /* LPJ ORIGIN NOTE */
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

        /* FINAL */
        .final-save-section {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 18px 20px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, .1);
            border-radius: 12px 12px 0 0;
            margin-top: 30px;
            z-index: 100;
        }

        .btn-final-back {
            background: linear-gradient(135deg, #868686, #5e5e5e);
            border: none;
            color: #fff;
            padding: 13px 35px;
            border-radius: 10px;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(27, 27, 27, .3);
            transition: all .3s;
        }

        .btn-final-back:hover {
            transform: translateY(-2px);
            color: #fff;
        }

        /* REALTIME VALIDATION */
        .validate-hint {
            font-size: .76rem;
            font-weight: 600;
            margin-top: 5px;
            min-height: 18px;
            transition: color .2s;
        }

        .validate-hint.hint-danger {
            color: #dc3545;
        }

        .validate-hint.hint-ok {
            color: #10b981;
        }

        .validate-hint.hint-info {
            color: #6b7280;
        }

        .currency-input.input-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 .2rem rgba(220, 53, 69, .2) !important;
        }

        .currency-input.input-valid {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 .2rem rgba(16, 185, 129, .15) !important;
        }

        /* Paksa semua cell tabel tidak meluber */
        .table-lpj thead th,
        .table-lpj tbody td,
        .table-lpj tfoot td {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: wrap;
        }

        /* Khusus kolom Description — boleh wrap */
        .table-lpj tbody td:nth-child(4),
        .table-lpj thead th:nth-child(4) {
            white-space: normal;
            /* word-break: break-word; */
        }

        /* Khusus kolom COA — boleh wrap */
        .table-lpj tbody td:nth-child(8),
        .table-lpj thead th:nth-child(8) {
            white-space: normal;
        }

        /* Badge COA di dalam kolom ikut wrap */
        .table-lpj tbody td:nth-child(8) .coa-display-badge,
        .table-lpj tbody td:nth-child(8) .coa-empty-badge {
            white-space: normal;
            word-break: break-word;
            overflow: visible;
            text-overflow: unset;
            max-width: 100%;
            display: block;
            /* biar full width cell */
            text-align: center;
        }

        /* Hapus selector nth-child untuk COA, ganti dengan class */
        .table-lpj tbody td.coa-display-cell {
            white-space: normal;
        }

        .table-lpj tbody td.coa-display-cell .coa-display-badge,
        .table-lpj tbody td.coa-display-cell .coa-empty-badge {
            white-space: normal;
            word-break: break-word;
            overflow: visible;
            text-overflow: unset;
            max-width: 100%;
            display: block;
            text-align: center;
        }
    </style>
@endpush

@section('content')

    <div id="floatingBadgeAlert" class="floating-badge-alert">
        <i id="alertIcon" class="fas fa-circle-notch fa-spin"></i>
        <div id="alertText" class="alert-text">Processing...</div>
    </div>

    {{-- Confirm Modal --}}
    <div class="confirm-modal-overlay" id="confirmModal">
        <div class="confirm-modal-box">
            <div class="modal-icon danger" id="confirmModalIcon"><i class="fas fa-exclamation-triangle"></i></div>
            <h5 id="confirmModalTitle">Are you sure?</h5>
            <p id="confirmModalDesc">This action cannot be undone.</p>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline-secondary" id="confirmModalCancel">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmModalOk">Yes, proceed</button>
            </div>
        </div>
    </div>

    {{-- Modal Refresh Cash Advances --}}
    <div class="modal fade" id="modalRefreshKasbon" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-black" style="background-color:#d1fae5;">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-sync-alt me-2" style="color:#059669;"></i> New Cash Advances Found
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        The following cash advances for JO <strong>{{ $lpj->joContract->no_jo_cont ?? '' }}</strong>
                        have not been included in this LPJ. Select the ones you want to add.
                    </p>
                    <div class="mb-2 d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSelectAllKasbon">
                            <i class="fas fa-check-double me-1"></i> Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeselectAllKasbon">
                            <i class="fas fa-times me-1"></i> Deselect All
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead style="background:#2c3e50; color:#fff;">
                                <tr>
                                    <th width="5%" class="text-center"><input type="checkbox" id="checkAllKasbon"></th>
                                    <th>CA Number</th>
                                    <th class="text-center">CA Date</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end">Total CA (IDR)</th>
                                </tr>
                            </thead>
                            <tbody id="newKasbonBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success fw-bold" id="btnAddSelectedKasbons">
                        <i class="fas fa-plus me-1"></i> Add Selected Cash Advances
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid lpjEditPage">
        <div class="row">
            <div class="col-lg-12">

                {{-- Page Header --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color:#d1fae5">
                        <span class="fw-bold">
                            <i class="fas fa-edit me-2" style="color:#059669;"></i>Edit LPJ Contract
                        </span>
                        <a href="{{ route('lpj-contract.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <input type="hidden" id="currentLpjId" value="{{ $lpj->id }}">
                <input type="hidden" id="currentLpjContStr" value="{{ $lpj->id_lpj_cont }}">
                <input type="hidden" id="currentJoContId" value="{{ $lpj->id_jo_cont }}">
                <input type="hidden" id="joKursValue" value="{{ $joKurs ? $joKurs->kurs_usd : 0 }}">

                {{-- HEADER CARD --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color:#d1fae5">
                        <h6 class="mb-0">
                            <i class="fas fa-file-invoice me-2" style="color:#059669;"></i>LPJ Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">LPJ Number</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#10b981;border-color:#10b981;">
                                        <i class="fas fa-file-alt text-white"></i>
                                    </span>
                                    <input type="text" class="form-control fw-bold" value="{{ $lpj->no_lpj_cont }}"
                                        readonly style="background:#e9ecef;color:#2c3e50;letter-spacing:1px;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required-field">Date</label>
                                <input type="date" id="date" class="form-control"
                                    value="{{ $lpj->date?->format('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Job Order</label>
                                <input type="text" class="form-control" readonly style="background:#e9ecef;"
                                    value="{{ $lpj->joContract->no_jo_cont ?? '' }} — {{ $lpj->joContract->title ?? '' }}">
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Job Order cannot be changed after the LPJ is created.
                                </small>
                            </div>

                            {{-- CA Summary --}}
                            <div class="col-md-12">
                                <div class="p-3 rounded-3 border"
                                    style="background:#f0fdf4;border-color:#bbf7d0!important;">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-receipt" style="color:#10b981;"></i>
                                        <span class="fw-bold" style="color:#065f46;">
                                            Related Cash Advances ({{ $lpj->kasbons->count() }} CA)
                                        </span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2" id="kasbonBadgeList">
                                        @foreach ($lpj->kasbons as $lpjKasbon)
                                            @if ($lpjKasbon->kasbonContract)
                                                <span class="badge"
                                                    style="background:#d1fae5;color:#065f46;font-size:.8rem;padding:6px 10px;">
                                                    <i
                                                        class="fas fa-check me-1"></i>{{ $lpjKasbon->kasbonContract->id_kasbon_cont }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="mt-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div class="fw-bold" style="color:#065f46;">
                                            Total CA Amount:
                                            <span class="ms-2" id="headerAmountDisplay">
                                                IDR {{ number_format($lpj->amount, 2, ',', '.') }}
                                            </span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-success fw-semibold"
                                            id="btnRefreshKasbons">
                                            <i class="fas fa-sync-alt me-1"></i> Refresh Cash Advances
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Remark</label>
                                <textarea id="note" class="form-control" rows="3">{{ $lpj->note }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Evidence File</label>
                                @if ($lpj->evidence)
                                    <div class="mb-2">
                                        <a href="{{ Storage::url($lpj->evidence) }}" target="_blank"
                                            class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-paperclip me-1"></i> View Current Evidence
                                        </a>
                                    </div>
                                @endif
                                <input type="file" id="evidenceFile" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Upload a new file to replace the current one. PDF / JPG / PNG,
                                    max 5MB</small>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-success px-4 fw-bold" id="btnSaveHeader">
                                <i class="fas fa-save me-1"></i> Update Header
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ITEMS CARD --}}
                <div class="card shadow mb-4" id="itemsCard">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color:#d1fae5">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2" style="color:#059669;"></i>LPJ Items
                            <span class="items-count-badge" id="itemsCountBadge">0</span>
                        </h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-success fw-semibold" data-bs-toggle="modal"
                                data-bs-target="#modalAddNewItem">
                                <i class="fas fa-plus me-1"></i> Add New Item
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" id="btnResetAllItems">
                                <i class="fas fa-trash-alt me-1"></i> Reset All LPJ Amounts
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">

                        {{-- INPUT CARD --}}
                        <div class="input-item-card" id="inputItemCard">
                            <div class="card-title-bar">
                                <i class="fas fa-pen-to-square"></i>
                                <span>Input LPJ Amount</span>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <div class="info-field">
                                        <div class="info-label">CA Number</div>
                                        <div class="info-value" id="infoKasbonNo">—</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="info-field">
                                        <div class="info-label">Category</div>
                                        <div class="info-value" id="infoInvoiceCtg">—</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="info-field">
                                        <div class="info-label">Item</div>
                                        <div class="info-value" id="infoInvoiceTyp">—</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="info-field">
                                        <div class="info-label">CA Amount (IDR)</div>
                                        <div class="info-value" id="infoNilaiKasbon">—</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="color:#1e40af; font-size:0.875rem;">
                                        LPJ Amount (IDR) <span style="color:#dc3545;">*</span>
                                    </label>
                                    <div class="currency-group">
                                        <span class="currency-label">IDR</span>
                                        <input type="text" id="inputAmountLpj" class="form-control currency-input"
                                            placeholder="0,00" autocomplete="off">
                                    </div>
                                    <small class="text-muted mt-1 d-block" id="inputAmountLpjHint"></small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="color:#1e40af; font-size:0.875rem;">
                                        Chart of Account
                                    </label>
                                    <select id="inputCoa" class="form-select" style="font-size:.875rem;">
                                        <option value="">— Select COA (optional) —</option>
                                        @foreach ($coaList as $coa)
                                            <option value="{{ $coa->id_md_chart_of_account }}">
                                                {{ $coa->no_account }} — {{ $coa->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2 mt-1">
                                    <button type="button" class="btn btn-cancel-item-input" id="btnCancelItemInput">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </button>
                                    <button type="button" class="btn btn-save-item-input" id="btnSaveItemInput">
                                        <i class="fas fa-save me-1"></i> Save LPJ Amount
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" id="activeKasbonContItemId" value="">
                            <input type="hidden" id="activeJoContItemId" value="">
                            <input type="hidden" id="activeRowIndex" value="">
                            <input type="hidden" id="activeHpp" value="0">
                            <input type="hidden" id="activeNilaiKasbon" value="0">
                        </div>

                        {{-- TABLE --}}
                        <div class="table-responsive">
                            <table class="table table-lpj table-bordered mb-0" style="table-layout: fixed; width: 100%;">

                                {{-- COLGROUP: satu-satunya cara paksa lebar kolom --}}
                                <colgroup>
                                    <col style="width: 6%;"> {{-- No --}}
                                    <col style="width: 10%;"> {{-- CA Number --}}
                                    <col style="width: 13%;"> {{-- Category --}}
                                    <col style="width: 22%;"> {{-- Description --}}
                                    <col style="width: 20%;"> {{-- HPP --}}
                                    <col style="width: 20%;"> {{-- CA Amount --}}
                                    <col style="width: 20%;"> {{-- LPJ Amount --}}
                                    <col style="width: 15%;"> {{-- COA --}}
                                    <col style="width: 10%;"> {{-- Action --}}
                                </colgroup>

                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">CA</th>
                                        <th class="text-center">Category</th>
                                        <th class="text-start">Item</th>
                                        <th class="text-center">HPP (IDR)</th>
                                        <th class="text-center">CA Amount (IDR)</th>
                                        <th class="text-center">LPJ Amount (IDR)</th>
                                        <th class="text-center">COA</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="lpjItemsBody">
                                    <tr id="loadingRow">
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="fas fa-circle-notch fa-spin me-2"></i> Loading data...
                                        </td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-center"><strong>GRAND TOTAL</strong></td>
                                        <td>
                                            <div class="footer-currency-wrap">
                                                <span class="footer-currency-label">IDR</span>
                                                <span class="footer-value" id="footerTotalHpp">0,00</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="footer-currency-wrap">
                                                <span class="footer-currency-label">IDR</span>
                                                <span class="footer-value" id="footerTotalKasbon">0,00</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="footer-currency-wrap">
                                                <span class="footer-currency-label">IDR</span>
                                                <span class="footer-value" id="footerTotalLpj">0,00</span>
                                            </div>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- LPJ ORIGIN NOTE --}}
                        <div class="lpj-origin-note mt-2" id="lpjOriginNote" style="display:none;">
                            <i class="fas fa-info-circle" style="color:#3b82f6; flex-shrink:0;"></i>
                            <span>
                                Rows highlighted in <strong>blue</strong> are items that were added from an LPJ page.
                            </span>
                        </div>



                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="final-save-section">
        <div class="d-flex justify-content-end">
            <a href="{{ route('lpj-contract.index') }}" class="btn btn-final-back">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════
         MODAL: ADD NEW ITEM FROM LPJ
    ═══════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalAddNewItem" tabindex="-1" aria-labelledby="modalAddNewItemLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                {{-- Modal Header --}}
                <div class="modal-header" style="background-color:#d1fae5; border-bottom:1px solid #bbf7d0;">
                    <h5 class="modal-title fw-bold" id="modalAddNewItemLabel">
                        <i class="fas fa-plus-square me-2" style="color:#059669;"></i> Add New Item from LPJ
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-4">

                    <p class="text-muted small mb-4">
                        This item will be automatically added to the selected JO and Cash Advance, then to LPJ items.
                    </p>

                    {{-- KURS INFO (readonly) --}}
                    <div class="p-3 rounded-3 mb-4 d-flex align-items-center gap-3 flex-wrap"
                        style="background:#f0fdf4; border:1px solid #bbf7d0;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-exchange-alt" style="color:#10b981;"></i>
                            <span class="fw-semibold" style="color:#065f46; font-size:.875rem;">
                                KURS:
                            </span>
                        </div>
                        @if ($joKurs)
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-bold" style="color:#059669; font-size:1rem;">
                                    IDR {{ number_format($joKurs->kurs_usd, 2, ',', '.') }}
                                </span>
                                <span class="text-muted small">
                                    per USD
                                    @if ($joKurs->tgl_kurs_usd)
                                        &middot; as of {{ $joKurs->tgl_kurs_usd->format('d/m/Y H:i') }}
                                    @endif
                                </span>
                                <span class="badge bg-success" style="font-size:.72rem;">Available</span>
                            </div>
                        @else
                            <span class="badge bg-secondary">No USD rate found for this JO</span>
                            <small class="text-muted">Only IDR income is available.</small>
                        @endif
                        <a href="{{ route('jo-contract.edit', $lpj->joContract->id_jo_cont ?? 0) }}" target="_blank"
                            class="btn btn-sm btn-outline-success ms-auto">
                            <i class="fas fa-external-link-alt me-1"></i> Edit Rate in JO
                        </a>
                    </div>
                    <div class="row g-2">
                        {{-- CA Target --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold required-field">Target Cash Advance</label>
                            <select id="newItemKasbonCont" class="form-select">
                                <option value="">-- Select Cash Advance --</option>
                                @foreach ($lpj->kasbons as $lpjKasbon)
                                    @if ($lpjKasbon->kasbonContract)
                                        <option value="{{ $lpjKasbon->kasbonContract->id_kasbon_cont }}">
                                            {{ $lpjKasbon->kasbonContract->id_kasbon_cont }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        {{-- Category --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold required-field">Category</label>
                            <select id="newItemCategory" class="form-select">
                                <option value="">-- Select Category --</option>
                                @foreach ($invoices->groupBy('invoice_ctg') as $cat => $inv)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Item --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold required-field">Item</label>
                            <select id="newItemInvoice" class="form-select" disabled>
                                <option value="">-- Select category first --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4 mt-1">

                        {{-- ══════════════════════════════════════
                             SECTION: JO DATA
                        ══════════════════════════════════════ --}}
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div
                                    style="width:28px;height:28px;border-radius:6px;background:#dcfce7;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-file-contract" style="color:#16a34a;font-size:.8rem;"></i>
                                </div>
                                <span
                                    style="font-weight:700;font-size:.8rem;color:#16a34a;
                                    text-transform:uppercase;letter-spacing:.8px;">
                                    JO Data
                                </span>
                                <div
                                    style="flex:1;height:1px;background:linear-gradient(to right,#bbf7d0,transparent);margin-left:6px;">
                                </div>
                            </div>
                            <div class="row g-3">
                                {{-- Income IDR --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Income (IDR)</label>
                                    <div class="currency-group">
                                        <span class="currency-label">IDR</span>
                                        <input type="text" id="newItemPendapatanIdr"
                                            class="form-control currency-input" placeholder="0,00">
                                    </div>
                                </div>
                                {{-- Income USD --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">
                                        Income (USD)
                                        @if (!$joKurs)
                                            <span class="badge bg-secondary ms-1"
                                                style="font-size:.65rem;">Unavailable</span>
                                        @endif
                                    </label>
                                    <div class="currency-group">
                                        <span class="currency-label">USD</span>
                                        <input type="text" id="newItemPendapatanUsd"
                                            class="form-control currency-input" placeholder="0,00"
                                            {{ !$joKurs ? 'disabled title="No USD rate in JO"' : '' }}>
                                    </div>
                                </div>
                                {{-- Selling Price (auto) --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Selling Price (IDR)</label>
                                    <div class="currency-group">
                                        <span class="currency-label" style="background:#6b7280;">IDR</span>
                                        <input type="text" id="newItemHargaJual" class="form-control currency-input"
                                            placeholder="0,00" readonly style="background:#f3f4f6;color:#6b7280;">
                                    </div>
                                </div>
                                {{-- HPP --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">HPP / Cost of Goods (IDR)</label>
                                    <div class="currency-group">
                                        <span class="currency-label">IDR</span>
                                        <input type="text" id="newItemHpp" class="form-control currency-input"
                                            placeholder="0,00">
                                    </div>
                                    <div id="hppHint" class="validate-hint hint-info"></div>
                                </div>
                            </div>
                        </div>

                        {{-- ══════════════════════════════════════
                             SECTION: CASH ADVANCE DATA
                        ══════════════════════════════════════ --}}
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div
                                    style="width:28px;height:28px;border-radius:6px;background:#dbeafe;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-cash-register" style="color:#2563eb;font-size:.8rem;"></i>
                                </div>
                                <span
                                    style="font-weight:700;font-size:.8rem;color:#2563eb;
                                    text-transform:uppercase;letter-spacing:.8px;">
                                    Cash Advance Data
                                </span>
                                <div
                                    style="flex:1;height:1px;background:linear-gradient(to right,#bfdbfe,transparent);margin-left:6px;">
                                </div>
                            </div>
                            <div class="row g-3">
                                {{-- CA Amount --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">
                                        CA Amount (IDR) <span style="color:#dc3545">*</span>
                                    </label>
                                    <div class="currency-group">
                                        <span class="currency-label">IDR</span>
                                        <input type="text" id="newItemNilaiKasbon" class="form-control currency-input"
                                            placeholder="0,00">
                                    </div>
                                    <div id="caHint" class="validate-hint hint-info"></div>
                                </div>
                            </div>
                        </div>

                        {{-- ══════════════════════════════════════
                             SECTION: LPJ DATA
                        ══════════════════════════════════════ --}}
                        <div class="col-12 mt-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div
                                    style="width:28px;height:28px;border-radius:6px;background:#fef3c7;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-file-invoice-dollar" style="color:#d97706;font-size:.8rem;"></i>
                                </div>
                                <span
                                    style="font-weight:700;font-size:.8rem;color:#d97706;
                                    text-transform:uppercase;letter-spacing:.8px;">
                                    LPJ Data
                                </span>
                                <div
                                    style="flex:1;height:1px;background:linear-gradient(to right,#fde68a,transparent);margin-left:6px;">
                                </div>
                            </div>
                            <div class="row g-3">
                                {{-- LPJ Amount --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        LPJ Amount (IDR) <span style="color:#dc3545">*</span>
                                    </label>
                                    <div class="currency-group">
                                        <span class="currency-label">IDR</span>
                                        <input type="text" id="newItemAmountLpj" class="form-control currency-input"
                                            placeholder="0,00">
                                    </div>
                                    <div id="lpjHint" class="validate-hint hint-info"></div>
                                </div>
                                {{-- COA --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Chart of Account</label>
                                    <select id="newItemCoa" class="form-select" style="font-size:.875rem;">
                                        <option value="">— Select COA —</option>
                                        @foreach ($coaList as $coa)
                                            <option value="{{ $coa->id_md_chart_of_account }}">
                                                {{ $coa->no_account }} — {{ $coa->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Validation Alert --}}
                    <div id="newItemValidationAlert" class="alert alert-danger mt-4 mb-0"
                        style="display:none; font-size:.875rem;"></div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer" style="border-top:1px solid #bbf7d0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success fw-bold px-4" id="btnAddNewItem">
                        <i class="fas fa-plus me-1"></i> Add Item to LPJ
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const currentLpjId = {{ $lpj->id }};
        const currentLpjContStr = '{{ $lpj->id_lpj_cont }}';
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const bulkSaveUrl = '{{ route('lpj-contract.items.bulk-save', $lpj->id) }}';
        const storeNewItemUrl = '{{ route('lpj-contract.item.store-new', $lpj->id) }}';
        const updateHeaderUrl = '/lpj-contract/header/update/{{ $lpj->id }}';
        const refreshKasbonsUrl = '{{ route('lpj-contract.refresh-kasbons', $lpj->id) }}';
        const addKasbonsUrl = '{{ route('lpj-contract.add-kasbons', $lpj->id) }}';

        let mergedItems = @json($mergedItemsJs);
        let confirmCallback = null;

        // COA list for inline select in table
        @php
            $coaListJs = $coaList
                ->map(
                    fn($c) => [
                        'id' => $c->id_md_chart_of_account,
                        'no' => $c->no_account,
                        'name' => $c->account_name,
                    ],
                )
                ->values()
                ->toArray();
        @endphp
        const coaList = {!! json_encode($coaListJs) !!};

        // ══════════════════════════════════════════════════════════
        // CONFIRM MODAL
        // ══════════════════════════════════════════════════════════
        function showConfirm({
            title,
            desc,
            okLabel = 'Yes, proceed',
            okClass = 'btn-danger',
            iconClass = 'danger'
        }, cb) {
            $('#confirmModalTitle').text(title);
            $('#confirmModalDesc').text(desc);
            $('#confirmModalOk').text(okLabel).removeClass().addClass(`btn ${okClass}`);
            $('#confirmModalIcon').removeClass('danger warning').addClass(iconClass);
            confirmCallback = cb;
            $('#confirmModal').addClass('show');
        }

        $('#confirmModalCancel, #confirmModal').on('click', function(e) {
            if (e.target === this) {
                $('#confirmModal').removeClass('show');
                confirmCallback = null;
            }
        });

        $('#confirmModalOk').on('click', function() {
            $('#confirmModal').removeClass('show');
            if (typeof confirmCallback === 'function') confirmCallback();
            confirmCallback = null;
        });

        // ══════════════════════════════════════════════════════════
        // FLOATING ALERT
        // ══════════════════════════════════════════════════════════
        function showFloatingAlert(type, message) {
            const $a = $('#floatingBadgeAlert'),
                $i = $('#alertIcon');
            $a.removeClass('alert-saving alert-success alert-error hiding');

            if (type === 'saving') {
                $a.addClass('alert-saving');
                $i.attr('class', 'fas fa-circle-notch fa-spin');
            }
            if (type === 'success') {
                $a.addClass('alert-success');
                $i.attr('class', 'fas fa-check-circle');
            }
            if (type === 'error') {
                $a.addClass('alert-error');
                $i.attr('class', 'fas fa-exclamation-circle');
            }

            $('#alertText').text(message);
            $a.addClass('show');

            if (type !== 'saving') {
                setTimeout(() => {
                    $a.addClass('hiding');
                    setTimeout(() => $a.removeClass('show hiding'), 400);
                }, 3000);
            }
        }

        // ══════════════════════════════════════════════════════════
        // NUMBER UTILS
        // ══════════════════════════════════════════════════════════
        function formatRupiah(v) {
            let n = v.toString().replace(/[^\d,]/g, '').replace(/\./g, '');
            if (!n) return '';
            let [int, dec = ''] = n.split(',');
            if (dec.length > 2) dec = dec.slice(0, 2);
            int = int.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return dec !== '' ? `${int},${dec}` : `${int},00`;
        }

        function parseRupiah(v) {
            return parseFloat((v || '0').toString().replace(/\./g, '').replace(',', '.')) || 0;
        }

        function formatNumber(v) {
            return parseFloat(v || 0).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function setupRupiahInput(el) {
            if (!el) return;
            el.addEventListener('input', function() {
                const cur = this.selectionStart;
                const before = this.value.substring(0, cur);
                this.value = formatRupiah(this.value);

                let digits = before.replace(/\D/g, '').length,
                    np = 0,
                    cnt = 0;
                for (let i = 0; i < this.value.length; i++) {
                    if (/\d/.test(this.value[i])) {
                        cnt++;
                        if (cnt === digits) {
                            np = i + 1;
                            break;
                        }
                    }
                }
                this.setSelectionRange(np, np);
            });
            el.addEventListener('blur', function() {
                if (this.value && !this.value.includes(',')) this.value += ',00';
            });
        }

        // ══════════════════════════════════════════════════════════
        // COA UTILS (display only — no inline select in table)
        // ══════════════════════════════════════════════════════════
        function buildCoaDisplay(index, coaId) {
            if (!coaId) {
                return `<span id="coaDisplay_${index}" style="color:#9ca3af;">-</span>`;
            }
            const coa = coaList.find(c => c.id === coaId);
            const label = coa ? `${coa.no} - ${coa.name}` : coaId;
            return `<span id="coaDisplay_${index}" style="font-size:.8rem;color:#2c3e50;">${label}</span>`;
        }

        function saveCoa(index, coaId) {
            const item = mergedItems[index];
            if (!item.id_kasbon_cont_item) return;

            mergedItems[index].id_md_chart_of_account = coaId || null;
            if (!item.has_lpj || item.amount_lpj <= 0) return;

            $.ajax({
                url: bulkSaveUrl,
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: JSON.stringify({
                    items: [{
                        id_kasbon_cont_item: item.id_kasbon_cont_item,
                        id_jo_cont_item: item.id_jo_cont_item,
                        amount_lpj: item.amount_lpj,
                        id_md_chart_of_account: coaId || null,
                    }]
                }),
                success: r => {
                    if (r.success) showFloatingAlert('success', 'COA saved!');
                    else showFloatingAlert('error', r.message || 'Failed to save COA');
                },
                error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to save COA')
            });
        }

        // ══════════════════════════════════════════════════════════
        // GROUP BY CATEGORY
        // ══════════════════════════════════════════════════════════
        function groupByCategory(items) {
            const groups = {},
                order = [];
            items.forEach(item => {
                const cat = item.invoice_ctg || 'Uncategorized';
                if (!groups[cat]) {
                    groups[cat] = [];
                    order.push(cat);
                }
                groups[cat].push(item);
            });
            return {
                groups,
                order
            };
        }

        // ══════════════════════════════════════════════════════════
        // RENDER TABLE
        // ══════════════════════════════════════════════════════════
        function renderTable() {
            const $tbody = $('#lpjItemsBody');
            $tbody.empty();
            $('#itemsCountBadge').text(mergedItems.length);

            if (!mergedItems.length) {
                $tbody.html(`<tr class="no-items-row"><td colspan="9">
            <i class="fas fa-inbox fa-3x d-block mb-3 text-muted opacity-50"></i>
            <p class="mb-1 fw-bold">No items found</p>
            <small>The selected Cash Advance has no items yet</small>
        </td></tr>`);
                $('#lpjOriginNote').hide();
                updateFooter();
                return;
            }

            // ── Group by CA Number terlebih dahulu, lalu by Category di dalam CA ──
            // Struktur: { kasbonNo: { category: [items] } }
            const kasbonOrder = []; // urutan CA Number
            const kasbonGroups = {}; // { kasbonNo: { catOrder: [], catGroups: {} } }

            mergedItems.forEach(item => {
                const kNo = item.id_kasbon_cont_no || '—';
                const cat = item.invoice_ctg || 'Uncategorized';

                if (!kasbonGroups[kNo]) {
                    kasbonGroups[kNo] = {
                        catOrder: [],
                        catGroups: {}
                    };
                    kasbonOrder.push(kNo);
                }
                if (!kasbonGroups[kNo].catGroups[cat]) {
                    kasbonGroups[kNo].catGroups[cat] = [];
                    kasbonGroups[kNo].catOrder.push(cat);
                }
                kasbonGroups[kNo].catGroups[cat].push(item);
            });

            let rowNo = 1,
                hasOriginItems = false;

            kasbonOrder.forEach(kNo => {
                const {
                    catOrder,
                    catGroups
                } = kasbonGroups[kNo];

                // Hitung total baris untuk rowspan kolom CA Number
                const kasbonRowspan = catOrder.reduce((sum, cat) => sum + catGroups[cat].length, 0);
                let isFirstKasbonRow = true;

                catOrder.forEach(category => {
                    const items = catGroups[category];
                    const catRowspan = items.length;
                    let isFirstCatRow = true;

                    items.forEach(item => {
                        const gIdx = mergedItems.indexOf(item);
                        const hasFilled = item.has_lpj && item.amount_lpj > 0;
                        const isOrigin = !!item.origin_lpj_cont;
                        if (isOrigin) hasOriginItems = true;

                        const originBadge = isOrigin ?
                            `<span class="badge ms-1" style="background:#3b82f6;font-size:.65rem;">From LPJ</span>` :
                            '';

                        const clearBtn = `
                    <button type="button" class="btn btn-danger btn-sm btn-clear-row"
                        onclick="clearItem(${gIdx})"
                        ${!hasFilled
                            ? 'disabled title="No LPJ amount to clear"'
                            : 'title="Clear LPJ amount"'}>
                        <i class="fas fa-trash"></i>
                    </button>`;

                        // Kolom CA Number — hanya muncul di baris pertama setiap kasbon
                        const kasbonCell = isFirstKasbonRow ?
                            `<td class="cell-center" rowspan="${kasbonRowspan}"
                           style="font-size:.8rem;
                                  font-weight:600; color:#000;
                                  border-right:2px solid #dee2e6; vertical-align:middle;">
                           ${kNo}
                       </td>` :
                            '';

                        // Kolom Category — hanya muncul di baris pertama setiap kategori dalam kasbon
                        const catCell = isFirstCatRow ?
                            `<td class="category-cell" rowspan="${catRowspan}">${category}</td>` :
                            '';

                        const rowClass = isOrigin ? 'item-row row-from-lpj' : 'item-row';

                        $tbody.append(`
                    <tr class="${rowClass}" id="row_${gIdx}"
                        data-index="${gIdx}"
                        data-category="${category}"
                        data-kasbon="${kNo}">
                        <td class="cell-center fw-bold text-muted">${rowNo++}</td>
                        ${kasbonCell}
                        ${catCell}
                        <td class="text-start">
                            <span class="fw-semibold" style="color:#2c3e50;">${item.invoice_typ}</span>
                            ${originBadge}
                        </td>
                        <td class="cell-readonly cell-right">${formatNumber(item.nilai_hpp_cont_item)}</td>
                        <td class="cell-readonly cell-right">${formatNumber(item.nilai_kasbon)}</td>
                        <td class="cell-right amount-lpj-cell">${formatNumber(item.amount_lpj)}</td>
                        <td class="coa-display-cell text-center">
    ${buildCoaDisplay(gIdx, item.id_md_chart_of_account)}
</td>
                        <td class="cell-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <button type="button" class="btn btn-success btn-sm btn-edit-row"
                                    onclick="openEditItem(${gIdx})" title="Input LPJ amount">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${clearBtn}
                            </div>
                        </td>
                    </tr>`);

                        isFirstKasbonRow = false;
                        isFirstCatRow = false;
                    });
                });
            });

            $('#lpjOriginNote').toggle(hasOriginItems);
            updateFooter();
        }

        function refreshRow(gIdx, amountLpj, saved) {
            const $row = $(`#row_${gIdx}`);
            $row.find('.amount-lpj-cell').text(formatNumber(amountLpj));
            $row.find('.btn-clear-row')
                .prop('disabled', !saved)
                .attr('title', saved ? 'Clear LPJ amount' : 'No LPJ amount to clear');
        }

        // ══════════════════════════════════════════════════════════
        // OPEN EDIT ITEM (input card)
        // ══════════════════════════════════════════════════════════
        function openEditItem(index) {
            const item = mergedItems[index];
            $('#infoInvoiceTyp').text(item.invoice_typ);
            $('#infoInvoiceCtg').text(item.invoice_ctg);
            $('#infoNilaiKasbon').text('IDR ' + formatNumber(item.nilai_kasbon));
            $('#infoKasbonNo').text(item.id_kasbon_cont_no);
            // Pre-fill nilai LPJ yang sudah tersimpan sebelumnya
            if (item.has_lpj && item.amount_lpj > 0) {
                $('#inputAmountLpj').val(formatRupiah(item.amount_lpj.toFixed(2).replace('.', ',')));
            } else {
                $('#inputAmountLpj').val('');
            }
            $('#inputAmountLpjHint').text('');
            $('#inputCoa').val(item.id_md_chart_of_account || '');
            $('#activeKasbonContItemId').val(item.id_kasbon_cont_item);
            $('#activeJoContItemId').val(item.id_jo_cont_item);
            $('#activeRowIndex').val(index);
            $('#activeHpp').val(item.nilai_hpp_cont_item);
            $('#activeNilaiKasbon').val(item.nilai_kasbon);

            $('.item-row').removeClass('tr-active');
            $(`#row_${index}`).addClass('tr-active');
            $('#inputItemCard').addClass('active');

            $('html,body').animate({
                scrollTop: $('#inputItemCard').offset().top - 120
            }, 400);
            setTimeout(() => $('#inputAmountLpj').focus(), 450);
        }

        $('#btnCancelItemInput').on('click', closeInputCard);

        function closeInputCard() {
            $('#inputItemCard').removeClass('active');
            $('#inputAmountLpj').val('');
            $('#inputAmountLpjHint').text('');
            $('#inputCoa').val('');
            $('#activeKasbonContItemId, #activeJoContItemId, #activeRowIndex').val('');
            $('#activeHpp').val('0');
            $('#activeNilaiKasbon').val('0');
            $('.item-row').removeClass('tr-active');
        }

        // ══════════════════════════════════════════════════════════
        // SAVE ITEM (input card)
        // ══════════════════════════════════════════════════════════
        $('#btnSaveItemInput').on('click', saveItemInput);
        $('#inputAmountLpj').on('keydown', e => {
            if (e.key === 'Enter') saveItemInput();
        });

        function saveItemInput() {
            const kasbonContItemId = $('#activeKasbonContItemId').val();
            const joContItemId = $('#activeJoContItemId').val();
            const rowIndex = parseInt($('#activeRowIndex').val());
            const amountLpj = parseRupiah($('#inputAmountLpj').val());
            const coaId = $('#inputCoa').val() || null;

            if (!kasbonContItemId) {
                showFloatingAlert('error', 'No item selected');
                return;
            }
            if (amountLpj <= 0) {
                showFloatingAlert('error', 'Please enter a LPJ Amount');
                return;
            }

            showFloatingAlert('saving', 'Saving...');
            $('#btnSaveItemInput').prop('disabled', true);

            $.ajax({
                url: bulkSaveUrl,
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: JSON.stringify({
                    items: [{
                        id_kasbon_cont_item: kasbonContItemId,
                        id_jo_cont_item: joContItemId,
                        amount_lpj: amountLpj,
                        id_md_chart_of_account: coaId,
                    }]
                }),
                success: function(r) {
                    if (r.success) {
                        showFloatingAlert('success', 'LPJ Amount cleared!');
                        mergedItems[index].amount_lpj = 0;
                        mergedItems[index].has_lpj = false;
                        mergedItems[index].id_md_chart_of_account = null;
                        if (parseInt($('#activeRowIndex').val()) === index) closeInputCard();
                        renderTable(); // re-render supaya rowspan tetap benar
                        updateFooter();
                        closeInputCard();
                    } else {
                        showFloatingAlert('error', r.message || 'Failed to save');
                    }
                },
                error: xhr => {
                    $('#btnSaveItemInput').prop('disabled', false);
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to save');
                }
            });
        }

        // ══════════════════════════════════════════════════════════
        // CLEAR ITEM
        // ══════════════════════════════════════════════════════════
        function clearItem(index) {
            const item = mergedItems[index];
            showConfirm({
                title: 'Clear LPJ Amount?',
                desc: `Remove LPJ amount for "${item.invoice_typ}"?`,
                okLabel: 'Yes, clear it',
                okClass: 'btn-danger',
                iconClass: 'danger'
            }, () => {
                showFloatingAlert('saving', 'Clearing...');
                $.ajax({
                    url: bulkSaveUrl,
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: JSON.stringify({
                        items: [{
                            id_kasbon_cont_item: item.id_kasbon_cont_item,
                            id_jo_cont_item: item.id_jo_cont_item,
                            amount_lpj: 0,
                            id_md_chart_of_account: null,
                        }]
                    }),
                    success: function(r) {
                        if (r.success) {
                            showFloatingAlert('success', 'LPJ Amount cleared!');
                            mergedItems[index].amount_lpj = 0;
                            mergedItems[index].has_lpj = false;
                            mergedItems[index].id_md_chart_of_account = null;
                            refreshRow(index, 0, false);
                            $(`#coaDisplay_${index}`).replaceWith(buildCoaDisplay(index, null));
                            if (parseInt($('#activeRowIndex').val()) === index) closeInputCard();
                            updateFooter();
                        } else {
                            showFloatingAlert('error', r.message || 'Failed');
                        }
                    },
                    error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Failed')
                });
            });
        }

        // ══════════════════════════════════════════════════════════
        // RESET ALL
        // ══════════════════════════════════════════════════════════
        $('#btnResetAllItems').on('click', function() {
            const filled = mergedItems.filter(i => i.has_lpj && i.amount_lpj > 0);
            if (!filled.length) {
                showFloatingAlert('error', 'No LPJ amounts to reset');
                return;
            }

            showConfirm({
                title: 'Reset All LPJ Amounts?',
                desc: `This will clear LPJ amounts for all ${filled.length} filled item(s).`,
                okLabel: 'Yes, reset all',
                okClass: 'btn-danger',
                iconClass: 'danger'
            }, () => {
                showFloatingAlert('saving', 'Resetting...');
                const payload = filled.map(i => ({
                    id_kasbon_cont_item: i.id_kasbon_cont_item,
                    id_jo_cont_item: i.id_jo_cont_item,
                    amount_lpj: 0,
                    id_md_chart_of_account: null,
                }));

                $.ajax({
                    url: bulkSaveUrl,
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: JSON.stringify({
                        items: payload
                    }),
                    success: function(r) {
                        if (r.success) {
                            showFloatingAlert('success', 'All LPJ amounts have been reset!');
                            mergedItems.forEach((item, i) => {
                                if (item.has_lpj) {
                                    mergedItems[i].amount_lpj = 0;
                                    mergedItems[i].has_lpj = false;
                                    mergedItems[i].id_md_chart_of_account = null;
                                }
                            });
                            closeInputCard();
                            renderTable(); // re-render supaya rowspan tetap benar
                            updateFooter();
                        } else {
                            showFloatingAlert('error', r.message || 'Failed');
                        }
                    },
                    error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Failed')
                });
            });
        });

        // ══════════════════════════════════════════════════════════
        // FOOTER TOTALS
        // ══════════════════════════════════════════════════════════
        function updateFooter() {
            let totalHpp = 0,
                totalKasbon = 0,
                totalLpj = 0;
            mergedItems.forEach(i => {
                totalHpp += parseFloat(i.nilai_hpp_cont_item) || 0;
                totalKasbon += parseFloat(i.nilai_kasbon) || 0;
                totalLpj += parseFloat(i.amount_lpj) || 0;
            });
            $('#footerTotalHpp').text(formatNumber(totalHpp));
            $('#footerTotalKasbon').text(formatNumber(totalKasbon));
            $('#footerTotalLpj').text(formatNumber(totalLpj));
        }

        // ══════════════════════════════════════════════════════════
        // UPDATE HEADER
        // ══════════════════════════════════════════════════════════
        $('#btnSaveHeader').on('click', function() {
            const date = $('#date').val();
            if (!date) {
                showFloatingAlert('error', 'Date is required');
                return;
            }

            showFloatingAlert('saving', 'Updating header...');
            $('#btnSaveHeader').prop('disabled', true);

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('id_jo_cont', $('#currentJoContId').val());
            formData.append('date', date);
            formData.append('note', $('#note').val());
            const evidenceFile = $('#evidenceFile')[0].files[0];
            if (evidenceFile) formData.append('evidence', evidenceFile);

            $.ajax({
                url: updateHeaderUrl,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(r) {
                    $('#btnSaveHeader').prop('disabled', false);
                    if (r.success) showFloatingAlert('success', 'Header updated successfully!');
                    else showFloatingAlert('error', r.message || 'Failed to update');
                },
                error: xhr => {
                    $('#btnSaveHeader').prop('disabled', false);
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to update header');
                }
            });
        });

        // ══════════════════════════════════════════════════════════
        // REFRESH CASH ADVANCES
        // ══════════════════════════════════════════════════════════
        $('#btnRefreshKasbons').on('click', function() {
            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin me-1"></i> Checking...');

            $.ajax({
                url: refreshKasbonsUrl,
                method: 'GET',
                success: function(r) {
                    $btn.prop('disabled', false)
                        .html('<i class="fas fa-sync-alt me-1"></i> Refresh Cash Advances');

                    if (!r.success) {
                        showFloatingAlert('error', r.message || 'Failed to refresh');
                        return;
                    }

                    if (r.found === 0) {
                        showFloatingAlert('success',
                            'All cash advances are already included in this LPJ.');
                        return;
                    }

                    const $tbody = $('#newKasbonBody');
                    $tbody.empty();
                    r.new_kasbons.forEach(k => {
                        $tbody.append(`<tr>
                            <td class="text-center">
                                <input type="checkbox" class="kasbon-check" value="${k.id_kasbon_cont}" checked>
                            </td>
                            <td class="fw-semibold">${k.id_kasbon_cont}</td>
                            <td class="text-center">${k.tgl_kasbon ?? '-'}</td>
                            <td class="text-center">${k.items_count} item${k.items_count !== 1 ? 's' : ''}</td>
                            <td class="text-end">IDR ${formatNumber(k.total_kasbon)}</td>
                        </tr>`);
                    });

                    $('#checkAllKasbon').prop('checked', true);
                    $('#modalRefreshKasbon').modal('show');
                },
                error: xhr => {
                    $btn.prop('disabled', false)
                        .html('<i class="fas fa-sync-alt me-1"></i> Refresh Cash Advances');
                    showFloatingAlert('error', xhr.responseJSON?.message ||
                        'Failed to refresh cash advances');
                }
            });
        });

        $('#checkAllKasbon').on('change', function() {
            $('.kasbon-check').prop('checked', $(this).is(':checked'));
        });
        $('#btnSelectAllKasbon').on('click', () => $('.kasbon-check, #checkAllKasbon').prop('checked', true));
        $('#btnDeselectAllKasbon').on('click', () => $('.kasbon-check, #checkAllKasbon').prop('checked', false));

        $('#btnAddSelectedKasbons').on('click', function() {
            const selected = $('.kasbon-check:checked').map((_, el) => el.value).get();
            if (!selected.length) {
                showFloatingAlert('error', 'Please select at least 1 cash advance');
                return;
            }

            $(this).prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin me-1"></i> Saving...');

            $.ajax({
                url: addKasbonsUrl,
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: JSON.stringify({
                    kasbon_ids: selected
                }),
                success: function(r) {
                    $('#btnAddSelectedKasbons').prop('disabled', false)
                        .html('<i class="fas fa-plus me-1"></i> Add Selected Cash Advances');
                    if (r.success) {
                        showFloatingAlert('success', r.message);
                        $('#modalRefreshKasbon').modal('hide');
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        showFloatingAlert('error', r.message || 'Failed to add cash advances');
                    }
                },
                error: xhr => {
                    $('#btnAddSelectedKasbons').prop('disabled', false)
                        .html('<i class="fas fa-plus me-1"></i> Add Selected Cash Advances');
                    showFloatingAlert('error', xhr.responseJSON?.message ||
                        'Failed to add cash advances');
                }
            });
        });

        // ══════════════════════════════════════════════════════════
        // ADD NEW ITEM — invoice by category
        // ══════════════════════════════════════════════════════════
        const invoicesByCategory = {
            @foreach ($invoices->groupBy('invoice_ctg') as $cat => $inv)
                '{{ $cat }}': [
                    @foreach ($inv as $invoice)
                        {
                            id: '{{ $invoice->id_md_invoice }}',
                            type: '{{ $invoice->invoice_typ }}'
                        },
                    @endforeach
                ],
            @endforeach
        };

        $('#newItemCategory').on('change', function() {
            const cat = $(this).val();
            const $sel = $('#newItemInvoice');
            if (!cat) {
                $sel.prop('disabled', true).html('<option value="">-- Select category first --</option>');
                return;
            }
            const invs = invoicesByCategory[cat] || [];
            let opts = '<option value="">-- Select Item --</option>';
            invs.forEach(i => {
                opts += `<option value="${i.id}">${i.type}</option>`;
            });
            $sel.prop('disabled', false).html(opts);
        });

        // ══════════════════════════════════════════════════════════
        // KURS VALUE
        // ══════════════════════════════════════════════════════════
        const joKursValue = parseFloat($('#joKursValue').val() || '0');

        // ══════════════════════════════════════════════════════════
        // REALTIME VALIDATION HELPERS (Add New Item Modal)
        // ══════════════════════════════════════════════════════════
        function getNewItemValues() {
            return {
                idr: parseRupiah($('#newItemPendapatanIdr').val()),
                usd: parseRupiah($('#newItemPendapatanUsd').val()),
                sell: parseRupiah($('#newItemHargaJual').val()),
                hpp: parseRupiah($('#newItemHpp').val()),
                ca: parseRupiah($('#newItemNilaiKasbon').val()),
                lpj: parseRupiah($('#newItemAmountLpj').val()),
            };
        }

        /**
         * Set hint text + input border colour.
         * type: 'danger' | 'ok' | 'info' | ''
         */
        function setHint($el, $input, msg, type) {
            $el.text(msg)
                .removeClass('hint-danger hint-ok hint-info')
                .addClass(type ? 'hint-' + type : '');

            if ($input) {
                $input.removeClass('input-invalid input-valid');
                if (type === 'danger') $input.addClass('input-invalid');
                if (type === 'ok') $input.addClass('input-valid');
            }
        }

        function recalcHargaJual() {
            const v = getNewItemValues();
            const sell = v.idr > 0 ? v.idr :
                (v.usd > 0 && joKursValue > 0 ? v.usd * joKursValue : 0);
            $('#newItemHargaJual').val(sell > 0 ? formatRupiah(sell.toFixed(2).replace('.', ',')) : '');
            updateAddItemHints();
        }

        function updateAddItemHints() {
            const v = getNewItemValues();
            const $hpp = $('#newItemHpp');
            const $ca = $('#newItemNilaiKasbon');
            const $hHint = $('#hppHint');
            const $cHint = $('#caHint');
            const $lHint = $('#lpjHint');

            // ── 1. HPP vs Selling Price ──────────────────────────
            if (v.sell > 0 && v.hpp > 0) {
                if (v.hpp > v.sell) {
                    setHint($hHint, $hpp,
                        `⚠ HPP cannot exceed the Selling Price (IDR ${formatNumber(v.sell)}).`,
                        'danger');
                } else {
                    setHint($hHint, $hpp,
                        ``,
                        '');
                }
            } else if (v.sell > 0) {
                setHint($hHint, $hpp, ``, 'info');
                $hpp.removeClass('input-invalid input-valid');
            } else {
                setHint($hHint, $hpp, '', '');
                $hpp.removeClass('input-invalid input-valid');
            }

            // ── 2. CA Amount vs HPP ──────────────────────────────
            if (v.hpp > 0 && v.ca > 0) {
                if (v.ca > v.hpp) {
                    setHint($cHint, $ca,
                        `⚠ CA Amount cannot exceed the HPP (IDR ${formatNumber(v.hpp)}).`,
                        'danger');
                } else {
                    setHint($cHint, $ca,
                        ``,
                        '');
                }
            } else if (v.hpp > 0) {
                setHint($cHint, $ca, ``, '');
                $ca.removeClass('input-invalid input-valid');
            } else {
                setHint($cHint, $ca, '', '');
                $ca.removeClass('input-invalid input-valid');
            }

            // ── 3. LPJ Amount — bebas, hanya info ───────────────
            if (v.lpj > 0) {
                const parts = [];
                if (v.hpp > 0) parts.push(`HPP IDR ${formatNumber(v.hpp)}`);
                if (v.ca > 0) parts.push(`CA IDR ${formatNumber(v.ca)}`);
                const info = parts.length ?
                    `` :
                    '';
                setHint($lHint, null, info, 'info');
            } else {
                setHint($lHint, null, '', '');
            }
        }

        // ── Rupiah inputs for modal ──────────────────────────────
        ['#newItemPendapatanIdr', '#newItemPendapatanUsd', '#newItemHpp',
            '#newItemNilaiKasbon', '#newItemAmountLpj', '#inputAmountLpj'
        ]
        .forEach(id => setupRupiahInput(document.querySelector(id)));

        // ── Event listeners ─────────────────────────────────────
        $('#newItemPendapatanIdr').on('input', function() {
            if (parseRupiah($(this).val()) > 0) $('#newItemPendapatanUsd').val('');
            recalcHargaJual();
        });

        $('#newItemPendapatanUsd').on('input', function() {
            if (parseRupiah($(this).val()) > 0) $('#newItemPendapatanIdr').val('');
            recalcHargaJual();
        });

        $('#newItemHpp').on('input blur', updateAddItemHints);
        $('#newItemNilaiKasbon').on('input blur', updateAddItemHints);
        $('#newItemAmountLpj').on('input blur', updateAddItemHints);

        // ── Reset validasi saat modal ditutup ───────────────────
        $('#modalAddNewItem').on('hidden.bs.modal', function() {
            ['#newItemHpp', '#newItemNilaiKasbon', '#newItemAmountLpj',
                '#newItemPendapatanIdr', '#newItemPendapatanUsd'
            ]
            .forEach(id => $(id).removeClass('input-invalid input-valid'));

            $('#hppHint').text('').removeClass('hint-danger hint-ok hint-info');
            $('#caHint').text('').removeClass('hint-danger hint-ok hint-info');
            $('#lpjHint')
                .text('')
                .removeClass('hint-danger hint-ok').addClass('hint-info');
        });

        // ══════════════════════════════════════════════════════════
        // SUBMIT: ADD NEW ITEM
        // Aturan submit:
        //   HPP        ≤ Selling Price  → error jika dilanggar
        //   CA Amount  ≤ HPP            → error jika dilanggar
        //   LPJ Amount → bebas, tidak ada validasi batas
        // ══════════════════════════════════════════════════════════
        $('#btnAddNewItem').on('click', function() {
            const kasbonCont = $('#newItemKasbonCont').val();
            const cat = $('#newItemCategory').val();
            const invoiceId = $('#newItemInvoice').val();
            const sellPrice = parseRupiah($('#newItemHargaJual').val());
            const hpp = parseRupiah($('#newItemHpp').val());
            const nilaiKasbon = parseRupiah($('#newItemNilaiKasbon').val());
            const amountLpj = parseRupiah($('#newItemAmountLpj').val());
            const coaId = $('#newItemCoa').val() || null;

            const errors = [];
            if (!kasbonCont || !cat || !invoiceId)
                errors.push('Cash Advance, Category, dan Item wajib diisi.');
            if (nilaiKasbon <= 0)
                errors.push('CA Amount wajib diisi.');
            if (amountLpj <= 0)
                errors.push('LPJ Amount wajib diisi.');
            if (sellPrice > 0 && hpp > sellPrice)
                errors.push(
                    `HPP (IDR ${formatNumber(hpp)}) tidak boleh melebihi Selling Price (IDR ${formatNumber(sellPrice)}).`
                );
            if (hpp > 0 && nilaiKasbon > hpp)
                errors.push(
                    `CA Amount (IDR ${formatNumber(nilaiKasbon)}) tidak boleh melebihi HPP (IDR ${formatNumber(hpp)}).`
                );
            // LPJ Amount: tidak ada validasi batas — bebas

            if (errors.length) {
                $('#newItemValidationAlert')
                    .html(errors.map(e => `<div>• ${e}</div>`).join(''))
                    .show();
                return;
            }
            $('#newItemValidationAlert').hide();

            showFloatingAlert('saving', 'Adding new item...');
            $('#btnAddNewItem').prop('disabled', true);

            $.ajax({
                url: storeNewItemUrl,
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: JSON.stringify({
                    id_kasbon_cont: kasbonCont,
                    id_md_invoice: invoiceId,
                    invoice_ctg: cat,
                    pendapatan_idr: parseRupiah($('#newItemPendapatanIdr').val()),
                    pendapatan_usd: parseRupiah($('#newItemPendapatanUsd').val()),
                    hpp_ops: hpp,
                    nilai_kasbon: nilaiKasbon,
                    amount_lpj: amountLpj,
                    id_md_chart_of_account: coaId,
                }),
                success: function(r) {
                    $('#btnAddNewItem').prop('disabled', false);
                    if (r.success) {
                        showFloatingAlert('success', 'New item added successfully!');
                        mergedItems.push(r.data);
                        renderTable();

                        // Reset form fields
                        $('#newItemKasbonCont, #newItemCategory, #newItemCoa').val('');
                        $('#newItemInvoice').prop('disabled', true)
                            .html('<option value="">-- Select category first --</option>');
                        $('#newItemPendapatanIdr, #newItemPendapatanUsd, #newItemHargaJual, ' +
                            '#newItemHpp, #newItemNilaiKasbon, #newItemAmountLpj').val('');
                        $('#hppHint, #caHint').text('').removeClass('hint-danger hint-ok hint-info');
                        $('#lpjHint')
                            .text('')
                            .removeClass('hint-danger hint-ok').addClass('hint-info');
                        $('#newItemValidationAlert').hide();
                        $('#modalAddNewItem').modal('hide');
                    } else {
                        showFloatingAlert('error', r.message || 'Failed to add item');
                    }
                },
                error: xhr => {
                    $('#btnAddNewItem').prop('disabled', false);
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to add item');
                }
            });
        });

        // ══════════════════════════════════════════════════════════
        // INIT
        // ══════════════════════════════════════════════════════════
        $(document).ready(function() {
            renderTable();
        });
    </script>
@endpush
