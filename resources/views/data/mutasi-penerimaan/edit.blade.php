@extends('layouts.app')

@section('title', 'Edit Receive Mutation')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        .mutasiEditPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, .08);
        }

        .mutasiEditPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .mutasiEditPage .form-control:focus,
        .mutasiEditPage .form-select:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 .2rem rgba(22, 163, 74, .25);
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: 600;
        }

        /* Floating badge */
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
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: #fff;
        }

        .floating-badge-alert.alert-error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
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

        /* Voucher accordion */
        .voucher-accordion-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 12px;
            overflow: hidden;
        }

        .voucher-accordion-header {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            padding: 14px 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #bbf7d0;
            transition: background .2s;
        }

        .voucher-accordion-header:hover {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        }

        .voucher-number-badge {
            background: #16a34a;
            color: #fff;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 700;
        }

        .voucher-accordion-body {
            padding: 20px;
            background: #fff;
        }

        /* Detail form */
        .add-form-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px dashed #16a34a;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all .3s;
        }

        .add-form-section.edit-mode {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border: 2px solid #3b82f6;
            box-shadow: 0 0 20px rgba(59, 130, 246, .3);
        }

        .add-form-section h6 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .add-form-section .form-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: .875rem;
        }

        .detail-edit-mode-box {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border: 2px solid #3b82f6;
            border-radius: 10px;
            padding: 20px;
        }

        .edit-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
        }

        .edit-info-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .edit-info-lbl {
            font-size: .68rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 4px;
        }

        .edit-info-val {
            font-size: .9rem;
            font-weight: 700;
            color: #1e293b;
        }

        .edit-info-val.clr-nilai {
            color: #0369a1;
        }

        /* Tipe pembayaran buttons */
        .btn-tipe-pem {
            border-radius: 20px;
            font-size: .82rem;
            font-weight: 600;
            transition: all .2s;
        }

        .btn-tipe-pem.active {
            box-shadow: 0 3px 10px rgba(0, 0, 0, .2);
            transform: translateY(-1px);
        }

        /* Detail table */
        .table-detail thead th {
            background-color: #14532d;
            color: #fff;
            border: 1px solid #14532d;
            padding: 10px 8px;
            font-weight: 600;
            font-size: .8rem;
            text-align: center;
            vertical-align: middle;
        }

        .table-detail tbody td {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: middle;
            font-size: .875rem;
            background-color: #fff;
        }

        .table-detail tbody td.num-cell {
            text-align: right;
        }

        .table-detail tbody tr:hover td {
            background-color: #f8f9fa;
        }

        .table-detail tfoot td {
            background: #14532d;
            color: #fff;
            font-weight: 700;
            padding: 10px 8px;
            font-size: .875rem;
        }

        .table-detail tfoot td.num-cell {
            text-align: right;
        }

        .jenis-badge {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: .75rem;
            font-weight: 600;
        }

        .jenis-tramper {
            background: #d1fae5;
            color: #065f46;
        }

        .jenis-other {
            background: #dbeafe;
            color: #1e40af;
        }

        .jenis-contract {
            background: #fef3c7;
            color: #92400e;
        }

        .jenis-general {
            background: #f3e8ff;
            color: #6b21a8;
        }

        .tipe-badge {
            padding: 2px 7px;
            border-radius: 20px;
            font-size: .7rem;
            font-weight: 600;
        }

        .tipe-uang-muka {
            background: #dbeafe;
            color: #1e40af;
        }

        .tipe-penerimaan {
            background: #d1fae5;
            color: #065f46;
        }

        .tipe-pengembalian {
            background: #fef3c7;
            color: #92400e;
        }

        /* Buttons */
        .btn-add-purple {
            background: linear-gradient(135deg, #16a34a, #15803d);
            border: none;
            color: #fff;
            padding: 10px 24px;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(22, 163, 74, .4);
            transition: all .3s;
        }

        .btn-add-purple:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, .5);
            color: #fff;
        }

        .btn-cancel-edit-style {
            background: linear-gradient(135deg, #bbb, #5c5c5c);
            border: none;
            color: #fff;
            padding: 10px 24px;
            border-radius: 20px;
            font-weight: 600;
            transition: all .3s;
        }

        .btn-cancel-edit-style:hover {
            transform: translateY(-2px);
            color: #fff;
        }

        .btn-final-back {
            background: linear-gradient(135deg, #868686, #5e5e5e);
            border: none;
            color: #fff;
            padding: 13px 36px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            transition: all .3s;
        }

        .btn-final-back:hover {
            transform: translateY(-2px);
            color: #fff;
        }

        .final-save-section {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 16px 20px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, .1);
            border-radius: 12px 12px 0 0;
            margin-top: 20px;
            z-index: 100;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: calc(1.5em + .75rem + 2px) !important;
            padding: .375rem .75rem !important;
            border: 2px solid #dee2e6 !important;
            border-radius: 8px !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 0 !important;
            color: #212529;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
        }

        body.modal-open {
            padding-right: 0 !important;
            overflow-y: scroll;
        }

        /* ── Tombol info kasbon ── */
        .btn-kasbon-info {
            background: none;
            border: 1px solid #93c5fd;
            color: #1d4ed8;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            font-size: 0.75rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            flex-shrink: 0;
            padding: 0;
        }

        .btn-kasbon-info:hover {
            background: #dbeafe;
            border-color: #3b82f6;
            transform: scale(1.15);
        }

        /* ── Modal Detail Kasbon ── */
        #modalKasbonInfo .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .18);
            overflow: hidden;
        }

        #modalKasbonInfo .modal-header {
            background: #D1FAE5;
            padding: 16px 22px;
            border: none;
        }

        #modalKasbonInfo .modal-header .modal-title {
            color: black;
            font-size: .9rem;
            font-weight: 700;
            letter-spacing: .01em;
        }

        #modalKasbonInfo .modal-header .modal-subtitle {
            color: #374151;
            font-size: .73rem;
            margin-top: 2px;
        }

        #modalKasbonInfo .ki-info-strip {
            display: flex;
            gap: 0;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        #modalKasbonInfo .ki-info-cell {
            flex: 1;
            padding: 10px 18px;
            border-right: 1px solid #e2e8f0;
        }

        #modalKasbonInfo .ki-info-cell:last-child {
            border-right: none;
        }

        #modalKasbonInfo .ki-info-lbl {
            font-size: .67rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #94a3b8;
            margin-bottom: 3px;
        }

        #modalKasbonInfo .ki-info-val {
            font-size: .82rem;
            font-weight: 600;
            color: #0f172a;
        }

        #modalKasbonInfo .ki-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .8rem;
        }

        #modalKasbonInfo .ki-table thead th {
            background: #D1FAE5;
            color: black;
            padding: 9px 12px;
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            white-space: nowrap;
            border: none;
        }

        #modalKasbonInfo .ki-table thead th.num {
            text-align: right;
        }

        #modalKasbonInfo .ki-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }

        #modalKasbonInfo .ki-table tbody tr:hover td {
            background: #f8fafc;
        }

        #modalKasbonInfo .ki-table tbody td {
            padding: 9px 12px;
            vertical-align: middle;
            background: #fff;
            color: #1e293b;
        }

        #modalKasbonInfo .ki-table tbody td.num {
            text-align: right;
            font-variant-numeric: tabular-nums;
            font-weight: 600;
        }

        #modalKasbonInfo .ki-table tfoot td {
            background: #f5f6f7;
            color: #000;
            padding: 9px 12px;
            font-weight: 700;
            font-size: .8rem;
            border: none;
            white-space: nowrap;
        }

        #modalKasbonInfo .ki-table tfoot td.num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }
    </style>
@endpush

@section('content')
    <div id="floatingBadgeAlert" class="floating-badge-alert">
        <i class="fas fa-circle-notch fa-spin" id="alertIcon"></i>
        <div class="alert-text" id="alertText">Processing...</div>
    </div>

    <div class="container-fluid mutasiEditPage">
        <div class="row">
            <div class="col-lg-12">

                {{-- Page Header --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #dcfce7">
                        <span class="fw-bold"><i class="fas fa-hand-holding-usd me-2"></i>Edit Receive Mutation</span>
                        <a href="{{ route('mutasi-penerimaan.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <input type="hidden" id="current_mutasi_id" value="{{ $mutasiPenerimaan->id }}">

                {{-- Header Card --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color: #dcfce7">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Receive Mutation Info</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Document No.</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#16a34a; color:white;"><i
                                            class="fas fa-hashtag"></i></span>
                                    <input type="text" class="form-control fw-bold"
                                        value="{{ $mutasiPenerimaan->nomor ?? '-' }}" readonly
                                        style="background-color:#f0fdf4; color:#14532d; letter-spacing:1px;">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Date</label>
                                <input type="date" id="edit_tanggal" class="form-control"
                                    value="{{ $mutasiPenerimaan->tanggal ? \Carbon\Carbon::parse($mutasiPenerimaan->tanggal)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Cek / BG / Ref</label>
                                <input type="text" id="edit_no_cek" class="form-control"
                                    value="{{ $mutasiPenerimaan->no_cek }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Exchange Rate</label>
                                <input type="text" id="edit_kurs_display" class="form-control"
                                    value="{{ $mutasiPenerimaan->kurs ? number_format($mutasiPenerimaan->kurs, 2, ',', '.') : '' }}"
                                    placeholder="0,00">
                                <input type="hidden" id="edit_kurs_value" value="{{ $mutasiPenerimaan->kurs ?? 0 }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Memo</label>
                                <textarea id="edit_memo" class="form-control" rows="3">{{ $mutasiPenerimaan->memo }}</textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn text-white" id="btnSaveHeader" style="background:#16a34a;">
                                <i class="fas fa-save me-1"></i> Update Header
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Vouchers Card --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #dcfce7">
                        <h6 class="mb-0">
                            <i class="fas fa-layer-group me-2"></i>Receive Vouchers
                            <span class="badge bg-secondary ms-2"
                                id="voucherCount">{{ $mutasiPenerimaan->vouchers->count() }}</span>
                        </h6>
                        <button type="button" class="btn btn-light btn-sm" id="btnShowAddVoucher">
                            <i class="fas fa-plus me-1"></i> Add Voucher
                        </button>
                    </div>
                    <div class="card-body p-4">

                        {{-- Add/Edit Voucher Form --}}
                        <div class="add-form-section mb-4" id="addVoucherSection" style="display:none;">
                            <h6 id="voucherFormTitle">
                                <i class="fas fa-plus-square" style="color:#16a34a;"></i> Add New Voucher
                            </h6>
                            <input type="hidden" id="editing_voucher_id" value="">
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Voucher No.</label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background:#16a34a; color:white;"><i
                                                class="fas fa-hashtag"></i></span>
                                        <input type="text" id="input_nomor_voucher" class="form-control fw-bold"
                                            placeholder="Auto-generated..." readonly
                                            style="background-color:#f0fdf4; color:#14532d; letter-spacing:1px;">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bank / Cash COA</label>
                                    <select id="input_voucher_coa" class="form-select"
                                        data-placeholder="Select Bank / Cash account...">
                                        <option value=""></option>
                                        @foreach ($chartOfAccounts as $coa)
                                            <option value="{{ $coa->id_md_chart_of_account }}"
                                                data-payment-type="{{ $coa->payment_type }}">
                                                [{{ $coa->payment_type }}] {{ $coa->no_account }} —
                                                {{ $coa->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Date Received</label>
                                    <input type="date" id="input_tgl_voucher" class="form-control">
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="form-label">Order</label>
                                    <input type="number" id="input_urutan" class="form-control" min="1"
                                        value="{{ $mutasiPenerimaan->vouchers->count() + 1 }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Notes</label>
                                    <input type="text" id="input_voucher_keterangan" class="form-control"
                                        placeholder="Voucher notes...">
                                </div>
                                <div class="col-12 d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-cancel-edit-style" id="btnCancelVoucher">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    <button type="button" class="btn btn-add-purple" id="btnSaveVoucher">
                                        <i class="fas fa-arrow-down me-1"></i> Save Voucher
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Voucher Accordion List --}}
                        <div id="voucherAccordion">
                            @forelse ($mutasiPenerimaan->vouchers->sortBy('urutan') as $voucher)
                                @include('data.mutasi-penerimaan._voucher_panel', [
                                    'voucher' => $voucher,
                                    'mutasiPenerimaan' => $mutasiPenerimaan,
                                    'chartOfAccounts' => $chartOfAccounts,
                                    'allCoa' => $allCoa,
                                    'departemenList' => $departemenList,
                                    'branchList' => $branchList,
                                ])
                            @empty
                                <div id="noVoucherMessage" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-4x mb-3 d-block"></i>
                                    <p class="fw-bold">No vouchers yet</p>
                                    <small>Click "Add Voucher" to get started</small>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="final-save-section">
        <div class="d-flex justify-content-end">
            <a href="{{ route('mutasi-penerimaan.index') }}" class="btn btn-final-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Delete detail modal --}}
    <div class="modal fade" id="detailConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; border:none;">
                <div class="modal-header"
                    style="background:linear-gradient(135deg,#ef4444,#dc2626); border-radius:12px 12px 0 0; border:none;">
                    <h5 class="modal-title text-white fw-bold"><i class="fas fa-trash-alt me-2"></i>Delete Detail</h5>
                </div>
                <div class="modal-body p-4">
                    <p>Delete this detail? This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-fill"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger flex-fill fw-bold" id="btnConfirmDeleteDetail">
                        <i class="fas fa-trash me-1"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Modal: Cash Advance Info ── --}}
    <div class="modal fade" id="modalKasbonInfo" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="modal-title"><i class="fas fa-book-open me-2"></i>Cash Advance Detail — <span
                                id="ki-nomor-title"></span></div>
                        <div class="modal-subtitle" id="ki-item-count"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-black btn-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="ki-info-strip">
                    <div class="ki-info-cell">
                        <div class="ki-info-lbl"><i class="fas fa-user me-1"></i>Customer</div>
                        <div class="ki-info-val" id="ki-customer">—</div>
                    </div>
                    <div class="ki-info-cell">
                        <div class="ki-info-lbl"><i class="fas fa-file-alt me-1"></i>JO No.</div>
                        <div class="ki-info-val" id="ki-jo">—</div>
                    </div>
                    <div class="ki-info-cell">
                        <div class="ki-info-lbl"><i class="fas fa-building me-1"></i>Department</div>
                        <div class="ki-info-val" id="ki-dept">—</div>
                    </div>
                    <div class="ki-info-cell">
                        <div class="ki-info-lbl"><i class="fas fa-map-marker-alt me-1"></i>Branch</div>
                        <div class="ki-info-val" id="ki-cabang">—</div>
                    </div>
                    <div class="ki-info-cell">
                        <div class="ki-info-lbl"><i class="fas fa-calendar me-1"></i>CA Date</div>
                        <div class="ki-info-val" id="ki-tgl">—</div>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="ki-table">
                            <thead>
                                <tr>
                                    <th style="width:44px;text-align:center;">No</th>
                                    <th>CA / JO Reference</th>
                                    <th class="num" style="width:175px;">CA Amount</th>
                                    <th class="num" style="width:175px;">LPJ Amount</th>
                                    <th class="num" style="width:155px;">Balance</th>
                                </tr>
                            </thead>
                            <tbody id="ki-tbody">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">—</td>
                                </tr>
                            </tbody>
                            <tfoot id="ki-tfoot"></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ============================================================
        // GLOBAL
        // ============================================================
        const mutasiId = {{ $mutasiPenerimaan->id }};
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const ROUTES = {
            headerUpdate: '/data/mutasi-penerimaan/header/update/' + mutasiId,
            voucherStore: '/data/mutasi-penerimaan/voucher/store',
            voucherUpdate: '/data/mutasi-penerimaan/voucher/update/',
            voucherDestroy: '/data/mutasi-penerimaan/voucher/destroy/',
            detailStore: '/data/mutasi-penerimaan/detail/store',
            detailShow: '/data/mutasi-penerimaan/detail/show/',
            detailUpdate: '/data/mutasi-penerimaan/detail/update/',
            detailDestroy: '/data/mutasi-penerimaan/detail/destroy/',
            joOptions: '/data/mutasi-penerimaan/jo-options',
            joInfo: '/data/mutasi-penerimaan/jo-info',
            kasbonOptions: '/data/mutasi-penerimaan/kasbon-options',
            kasbonInfo: '/data/mutasi-penerimaan/kasbon-info',
            voucherDetails: '/data/mutasi-penerimaan/voucher/',
            nextVoucherNo: '/data/mutasi-penerimaan/next-voucher-no',
        };

        // ============================================================
        // HELPERS
        // ============================================================
        function showFloatingAlert(type, message) {
            const el = $('#floatingBadgeAlert');
            el.removeClass('alert-saving alert-success alert-error hiding');
            $('#alertIcon').attr('class', type === 'saving' ? 'fas fa-circle-notch fa-spin' : type === 'success' ?
                'fas fa-check-circle' : 'fas fa-exclamation-circle');
            el.addClass(`alert-${type}`).addClass('show');
            $('#alertText').text(message);
            if (type !== 'saving') setTimeout(hideFloatingAlert, 3000);
        }

        function hideFloatingAlert() {
            $('#floatingBadgeAlert').addClass('hiding');
            setTimeout(() => $('#floatingBadgeAlert').removeClass('show hiding'), 400);
        }

        function fmt(n) {
            return Number(n).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function parseRp(v) {
            return parseFloat(String(v).replace(/\./g, '').replace(',', '.')) || 0;
        }

        function fmtRp(v) {
            let val = String(v).replace(/[^\d,]/g, '').replace(/\./g, '');
            if (!val) return ''; // ← izinkan kosong saat mengetik
            let [int, dec] = val.split(',');
            if (dec !== undefined && dec.length > 2) dec = dec.substring(0, 2);
            int = (int || '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            if (!int && dec === undefined) return '';
            return dec !== undefined ? (int || '0') + ',' + dec : int;
        }

        function setupRp(el) {
            if (!el) return;
            el.addEventListener('input', function() {
                this.value = fmtRp(this.value);
            });
            el.addEventListener('blur', function() {
                if (!this.value) {
                    this.value = '0,00';
                } else if (!this.value.includes(',')) {
                    this.value += ',00';
                }
            });
        }

        function escH(s) {
            return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                '&quot;');
        }

        function escAttr(str) {
            if (!str) return '';
            return String(str).replace(/"/g, '&quot;');
        }

        function showKasbonInfo(btn) {
            const nomor = btn.dataset.nomor || '—';
            const jo = btn.dataset.jo || '—';
            const cust = btn.dataset.cust || '—';
            const dept = btn.dataset.dep || '—';
            const cabang = btn.dataset.cab || '—';
            const tgl = btn.dataset.tgl || '—';
            const nilai = parseFloat(btn.dataset.nilai || 0);
            const pj = parseFloat(btn.dataset.pj || 0);
            const selisih = parseFloat(btn.dataset.selisih || 0);

            document.getElementById('ki-nomor-title').textContent = nomor;
            document.getElementById('ki-item-count').textContent = 'CA Summary';
            document.getElementById('ki-customer').textContent = cust;
            document.getElementById('ki-jo').textContent = jo;
            document.getElementById('ki-dept').textContent = dept;
            document.getElementById('ki-cabang').textContent = cabang;
            document.getElementById('ki-tgl').textContent = tgl;

            const items = JSON.parse(btn.dataset.items || '[]');
            const tbody = document.getElementById('ki-tbody');
            const tfoot = document.getElementById('ki-tfoot');
            const sCls = selisih > 0.001 ? 'color:#16a34a;' : selisih < -0.001 ? 'color:#dc2626;' : '';

            if (items.length > 0) {
                tbody.innerHTML = items.map((it, idx) => {
                    const bCls = it.selisih > 0.001 ? 'color:#16a34a;' : it.selisih < -0.001 ? 'color:#dc2626;' : '';
                    return `<tr>
<td class="text-center fw-bold" style="font-size:.82rem;">${idx + 1}</td>
<td style="font-size:.82rem;">${escH(it.label || '—')}</td>
<td class="num">${fmt(it.nilai_kasbon)}</td>
<td class="num">${fmt(it.pj)}</td>
<td class="num" style="${bCls}font-weight:700;">${fmt(it.selisih)}</td>
</tr>`;
                }).join('');
            } else {
                const joRef = (jo !== '—') ? ' <span style="color:#94a3b8;font-size:.78rem;">/ ' + escH(jo) + '</span>' : '';
                tbody.innerHTML = `<tr>
<td class="text-center fw-bold" style="font-size:.82rem;">1</td>
<td style="font-size:.82rem;">${escH(nomor)}${joRef}</td>
<td class="num">${fmt(nilai)}</td>
<td class="num">${fmt(pj)}</td>
<td class="num" style="${sCls}font-weight:700;">${fmt(selisih)}</td>
</tr>`;
            }
            tfoot.innerHTML = `<tr>
<td colspan="2" class="text-end fw-bold">GRAND TOTAL</td>
<td class="num"><span class="badge bg-dark rounded-1 me-1" style="font-size:.65rem;vertical-align:middle;">IDR</span>${fmt(nilai)}</td>
<td class="num"><span class="badge bg-dark rounded-1 me-1" style="font-size:.65rem;vertical-align:middle;">IDR</span>${fmt(pj)}</td>
<td class="num"><span class="badge bg-dark rounded-1 me-1" style="font-size:.65rem;vertical-align:middle;">IDR</span>${fmt(selisih)}</td>
</tr>`;

            new bootstrap.Modal(document.getElementById('modalKasbonInfo')).show();
        }

        function initS2(sel, placeholder) {
            $(sel).select2({
                theme: 'bootstrap-5',
                placeholder: placeholder || '-- Select --',
                allowClear: true,
                width: '100%'
            });
        }

        function fmtDate(ymd) {
            if (!ymd) return null;
            const m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const d = new Date(ymd);
            if (isNaN(d)) return ymd;
            return String(d.getDate()).padStart(2, '0') + '-' + m[d.getMonth()] + '-' + d.getFullYear();
        }

        $(document).ready(function() {
            setupRp(document.getElementById('edit_kurs_display'));
            $('#edit_kurs_display').on('input', function() {
                $('#edit_kurs_value').val(parseRp($(this).val()));
            });
            initS2('#input_voucher_coa', 'Select Bank / Cash account...');

            $(document).on('change', '#input_voucher_coa', function() {
                const pt = $(this).find('option:selected').data('payment-type');
                const $n = $('#input_nomor_voucher');
                if (!$(this).val() || !pt) {
                    $n.val('');
                    return;
                }
                $n.val('Generating...').css('opacity', .5);
                fetch(ROUTES.nextVoucherNo + '?payment_type=' + encodeURIComponent(pt))
                    .then(r => r.json()).then(d => $n.val(d.success ? d.nomor : '').css('opacity', 1))
                    .catch(() => $n.val('').css('opacity', 1));
            });

            @foreach ($mutasiPenerimaan->vouchers as $v)
                initVoucherPanelSelects({{ $v->id }});
            @endforeach
        });

        // ============================================================
        // UPDATE HEADER
        // ============================================================
        $('#btnSaveHeader').on('click', function() {
            const tanggal = $('#edit_tanggal').val();
            if (!tanggal) {
                showFloatingAlert('error', 'Date is required');
                return;
            }
            showFloatingAlert('saving', 'Saving header...');
            $.ajax({
                url: ROUTES.headerUpdate,
                method: 'PUT',
                data: {
                    tanggal,
                    no_cek: $('#edit_no_cek').val(),
                    kurs: parseRp($('#edit_kurs_display').val()),
                    memo: $('#edit_memo').val(),
                    _token: CSRF
                },
                success: r => r.success ? showFloatingAlert('success', 'Header updated!') :
                    showFloatingAlert('error', r.message || 'Failed'),
                error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Failed'),
            });
        });

        // ============================================================
        // VOUCHER
        // ============================================================
        $('#btnShowAddVoucher').on('click', function() {
            $('#addVoucherSection').slideDown(200);
            clearVoucherForm();
            $('#noVoucherMessage').hide();
            fetch(ROUTES.nextVoucherNo).then(r => r.json()).then(d => {
                if (d.success) $('#input_nomor_voucher').val(d.nomor);
            });
        });
        $('#btnCancelVoucher').on('click', function() {
            $('#addVoucherSection').slideUp(200);
            clearVoucherForm();
        });

        function clearVoucherForm() {
            $('#editing_voucher_id').val('');
            $('#voucherFormTitle').html('<i class="fas fa-plus-square" style="color:#16a34a;"></i> Add New Voucher');
            $('#addVoucherSection').removeClass('edit-mode');
            $('#btnSaveVoucher').html('<i class="fas fa-arrow-down me-1"></i> Save Voucher');
            $('#input_nomor_voucher').val('').css('opacity', 1);
            $('#input_voucher_keterangan').val('');
            $('#input_urutan').val($('.voucher-accordion-item').length + 1);
            $('#input_tgl_voucher').val('');
            $('#input_voucher_coa').val('').trigger('change.select2');
        }

        $('#btnSaveVoucher').on('click', function() {
            const editingId = $('#editing_voucher_id').val();
            const nomorVoucher = $('#input_nomor_voucher').val().trim();
            if (!nomorVoucher) {
                showFloatingAlert('error', 'Voucher number is required');
                return;
            }
            const payload = {
                nomor_voucher: nomorVoucher,
                urutan: $('#input_urutan').val(),
                keterangan: $('#input_voucher_keterangan').val(),
                tanggal: $('#input_tgl_voucher').val() || null,
                id_md_chart_of_account: $('#input_voucher_coa').val() || null,
            };
            editingId ? updateVoucher(editingId, payload) : storeVoucher(payload);
        });

        function storeVoucher(payload) {
            showFloatingAlert('saving', 'Saving voucher...');
            $.ajax({
                url: ROUTES.voucherStore,
                method: 'POST',
                data: {
                    ...payload,
                    id_mutasi_penerimaan: mutasiId,
                    _token: CSRF
                },
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', r.message || 'Failed');
                        return;
                    }
                    showFloatingAlert('success', 'Voucher added: ' + r.data.nomor_voucher);
                    $('#noVoucherMessage').hide();
                    appendVoucherPanel(r.data);
                    $('#addVoucherSection').slideUp(200);
                    clearVoucherForm();
                    updateVoucherCount(1);
                },
                error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Failed'),
            });
        }

        function updateVoucher(id, payload) {
            showFloatingAlert('saving', 'Updating voucher...');
            $.ajax({
                url: ROUTES.voucherUpdate + id,
                method: 'PUT',
                data: {
                    ...payload,
                    _token: CSRF
                },
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', r.message || 'Failed');
                        return;
                    }
                    showFloatingAlert('success', 'Voucher updated!');
                    const panel = $(`#voucher-panel-${id}`);
                    panel.find('.voucher-nomor-text').text(payload.nomor_voucher);
                    panel.find('.voucher-ket-text').text(payload.keterangan ? `— ${payload.keterangan}` : '');
                    panel.find('.voucher-number-badge').text('#' + payload.urutan);
                    panel.attr('data-tgl', payload.tanggal || '').attr('data-coa-id', payload
                        .id_md_chart_of_account || '');
                    const ftgl = payload.tanggal ? fmtDate(payload.tanggal) : null;
                    const ctxt = payload.id_md_chart_of_account ? ($('#input_voucher_coa option:selected')
                        .text() || '—') : null;
                    panel.find('.voucher-tgl-text').html(ftgl ?
                        `<i class="fas fa-calendar-alt me-1"></i>${ftgl}` :
                        `<i class="fas fa-calendar-alt me-1"></i><em>No date</em>`);
                    panel.find('.voucher-coa-text').html(ctxt ?
                        `<i class="fas fa-university me-1"></i>${ctxt}` :
                        `<i class="fas fa-university me-1"></i><em>No bank/cash</em>`);
                    $('#addVoucherSection').slideUp(200);
                    clearVoucherForm();
                },
                error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Failed'),
            });
        }

        function editVoucher(id) {
            const panel = $(`#voucher-panel-${id}`);
            $('#editing_voucher_id').val(id);
            $('#input_nomor_voucher').val(panel.find('.voucher-nomor-text').text());
            $('#input_voucher_keterangan').val(panel.find('.voucher-ket-text').text().replace('— ', ''));
            $('#input_urutan').val(panel.find('.voucher-number-badge').text().replace('#', ''));
            $('#input_tgl_voucher').val(panel.data('tgl') || '');
            $('#input_voucher_coa').val(panel.data('coa-id') || '').trigger('change.select2');
            $('#voucherFormTitle').html('<i class="fas fa-edit" style="color:#3b82f6;"></i> Edit Voucher');
            $('#addVoucherSection').addClass('edit-mode').slideDown(200);
            $('#btnSaveVoucher').html('<i class="fas fa-save me-1"></i> Update Voucher');
            $('html,body').animate({
                scrollTop: $('#addVoucherSection').offset().top - 100
            }, 400);
        }

        function deleteVoucher(id, name) {
            Swal.fire({
                title: 'Delete Voucher?',
                html: `<p>Voucher <strong>${name}</strong> and all its details will be permanently deleted.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete!',
            }).then(r => {
                if (!r.isConfirmed) return;
                showFloatingAlert('saving', 'Deleting voucher...');
                $.ajax({
                    url: ROUTES.voucherDestroy + id,
                    method: 'DELETE',
                    data: {
                        _token: CSRF
                    },
                    success: function(res) {
                        if (res.success) {
                            showFloatingAlert('success', 'Voucher deleted!');
                            $(`#voucher-panel-${id}`).fadeOut(300, function() {
                                $(this).remove();
                                updateVoucherCount(-1);
                                if ($('.voucher-accordion-item').length === 0) $(
                                    '#noVoucherMessage').show();
                            });
                        } else showFloatingAlert('error', res.message || 'Failed');
                    },
                    error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Failed'),
                });
            });
        }

        function updateVoucherCount(delta) {
            const b = $('#voucherCount');
            b.text(parseInt(b.text() || 0) + delta);
        }

        function appendVoucherPanel(v) {
            const ftgl = v.tanggal ? fmtDate(v.tanggal) : null;
            const ctxt = v.coa_display || null;
            const tglPill = ftgl ?
                `<span class="voucher-tgl-text" style="background:#d1fae5;color:#065f46;border-radius:20px;padding:1px 8px;font-weight:600;font-size:.75rem;"><i class="fas fa-calendar-alt me-1"></i>${ftgl}</span>` :
                `<span class="voucher-tgl-text" style="background:#f1f5f9;color:#94a3b8;border-radius:20px;padding:1px 8px;font-size:.75rem;"><i class="fas fa-calendar-alt me-1"></i><em>No date</em></span>`;
            const coaPill = ctxt ?
                `<span class="voucher-coa-text" style="background:#dbeafe;color:#1e40af;border-radius:20px;padding:1px 8px;font-weight:600;font-size:.75rem;"><i class="fas fa-university me-1"></i>${ctxt}</span>` :
                `<span class="voucher-coa-text" style="background:#f1f5f9;color:#94a3b8;border-radius:20px;padding:1px 8px;font-size:.75rem;"><i class="fas fa-university me-1"></i><em>No bank/cash</em></span>`;
            const html = `
<div class="voucher-accordion-item" id="voucher-panel-${v.id}" data-voucher-id="${v.id}" data-tgl="${v.tanggal||''}" data-coa-id="${v.id_md_chart_of_account||''}">
    <div class="voucher-accordion-header" onclick="toggleVoucherPanel(${v.id})">
        <div class="voucher-title" style="flex-direction:column;align-items:flex-start;gap:4px;">
            <div class="d-flex align-items-center gap-2">
                <span class="voucher-number-badge">#${v.urutan}</span>
                <strong class="voucher-nomor-text">${v.nomor_voucher}</strong>
                <span class="text-muted small voucher-ket-text">${v.keterangan ? '— ' + v.keterangan : ''}</span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">${tglPill}${coaPill}</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark detail-count-badge-${v.id}">0 detail</span>
            <button type="button" class="btn btn-sm btn-warning" onclick="event.stopPropagation(); editVoucher(${v.id})"><i class="fas fa-edit"></i></button>
            <button type="button" class="btn btn-sm btn-danger"  onclick="event.stopPropagation(); deleteVoucher(${v.id}, '${v.nomor_voucher}')"><i class="fas fa-trash"></i></button>
            <i class="fas fa-chevron-down toggle-icon-${v.id}"></i>
        </div>
    </div>
    <div class="voucher-accordion-body" id="voucher-body-${v.id}">
        ${buildDetailFormHtml(v.id)}
        <div class="table-responsive mt-2">
            <table class="table table-detail table-bordered" id="detailTable-${v.id}">
                <thead><tr>
                    <th class="text-center" width="80">Type</th>
                    <th class="text-center" width="100">Pmt Type</th>
                    <th>Cust / Release To</th><th>JO / Kasbon</th><th>Dept / Branch</th>
                    <th style="min-width:140px;" class="text-end">Amount (IDR)</th>
                    <th>Notes</th><th width="80" class="text-center">Action</th>
                </tr></thead>
                <tbody id="detailTableBody-${v.id}"><tr><td colspan="8" class="text-center text-muted py-3"><i class="fas fa-inbox fa-2x d-block mb-2"></i>No details yet</td></tr></tbody>
                <tfoot id="detailTableFoot-${v.id}"></tfoot>
            </table>
        </div>
    </div>
</div>`;
            $('#voucherAccordion').append(html);
            initVoucherPanelSelects(v.id);
        }

        // ============================================================
        // DETAIL FORM HTML (for newly appended vouchers via JS)
        // ============================================================
        function buildDetailFormHtml(voucherId) {
            const depOpts = (window._depList || []).map(d => `<option value="${d.id}">${escH(d.nama)}</option>`).join('');
            const brOpts = (window._brList || []).map(b => `<option value="${b.id}">${escH(b.nama)}</option>`).join('');

            return `
<div class="add-form-section mb-3" id="addDetailSection-${voucherId}">
    <h6 id="detailFormTitle-${voucherId}"><i class="fas fa-plus-square" style="color:#16a34a;"></i> Add Detail</h6>
    <div id="detailAddMode-${voucherId}">
        <div class="row g-3">
            <div class="col-12" id="jenisCol-${voucherId}">
                <label class="form-label fw-semibold required-field">Type</label>
                <select id="input_jenis-${voucherId}" class="form-select" onchange="onJenisChange(${voucherId})">
                    <option value="">-- Select --</option>
                    <option value="tramper">Tramper</option><option value="other">Other</option>
                    <option value="contract">Contract</option><option value="general">General</option>
                </select>
            </div>
            <div class="col-md-9" id="joCol-${voucherId}" style="display:none;">
                <label class="form-label fw-semibold required-field">Job Order</label>
                <select id="input_jo-${voucherId}" class="form-select select2-jo-${voucherId}" data-placeholder="Select JO..." onchange="onJoChange(${voucherId})"><option value=""></option></select>
            </div>
            <div class="col-md-9" id="genKasbonCol-${voucherId}" style="display:none;">
                <label class="form-label fw-semibold required-field">General Cash Advance</label>
                <select id="input_kasbon_gen-${voucherId}" class="form-select select2-kasbon-gen-${voucherId}" data-placeholder="Select CA..." onchange="onGeneralKasbonChange(${voucherId})"><option value=""></option></select>
            </div>
        </div>
       <div id="custPanel-${voucherId}" class="mt-3" style="display:none;">
    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
                <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Customer</div>
                <div id="joCustomerName-${voucherId}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
                <input type="hidden" id="input_id_md_cust-${voucherId}" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
                <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Vessel</div>
                <div id="joVesselName-${voucherId}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
            </div>
        </div>
        <div class="col-md-4">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
                <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Port</div>
                <div id="joPortName-${voucherId}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
                <input type="hidden" id="input_id_md_port-${voucherId}" value="">
            </div>
        </div>
    </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold required-field">Payment Type</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-primary btn-tipe-pem" data-tipe="uang_muka_pembayaran" onclick="selectTipePembayaran(${voucherId}, 'uang_muka_pembayaran', this)"><i class="fas fa-file-invoice-dollar me-1"></i> Down Payment</button>
                        <button type="button" class="btn btn-outline-success btn-tipe-pem" data-tipe="penerimaan_pembayaran" onclick="selectTipePembayaran(${voucherId}, 'penerimaan_pembayaran', this)"><i class="fas fa-hand-holding-usd me-1"></i> Payment Receipt</button>
                        <button type="button" class="btn btn-outline-warning btn-tipe-pem" data-tipe="pengembalian_lebih_bayar" onclick="selectTipePembayaran(${voucherId}, 'pengembalian_lebih_bayar', this)"><i class="fas fa-undo-alt me-1"></i> Refund</button>
                    </div>
                    <input type="hidden" id="input_tipe_pem-${voucherId}" value="">
                </div>
            </div>
        </div>
        <div id="genKasbonInfoPanel-${voucherId}" class="mt-3" style="display:none;">
            ${buildKasbonInfoPanelHtml('gen_' + voucherId)}
            <div id="genKasbonAlert-${voucherId}" class="mt-2" style="display:none;"></div>
            <div id="genKasbonForm-${voucherId}" style="display:none;">
                <div class="row g-3 mt-1">
<input type="hidden" id="input_dep_gen-${voucherId}" value="">
<input type="hidden" id="input_branch_gen-${voucherId}" value="">
<input type="hidden" id="input_release_to_gen-${voucherId}" value="">
<div class="col-md-4"><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;"><div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Department</div><div id="display_dep_gen-${voucherId}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div></div></div>
<div class="col-md-4"><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;"><div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Branch</div><div id="display_branch_gen-${voucherId}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div></div></div>
<div class="col-md-4"><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;"><div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Release To</div><div id="display_release_to_gen-${voucherId}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div></div></div>
<div class="col-md-12"><label class="form-label fw-semibold">Notes</label><input type="text" id="input_ket_gen-${voucherId}" class="form-control" placeholder="Notes..."></div>
                    <div class="col-12 d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-cancel-edit-style" onclick="resetDetailForm(${voucherId})"><i class="fas fa-times"></i> Cancel</button>
                        <button type="button" class="btn btn-add-purple" onclick="saveDetailGeneral(${voucherId})"><i class="fas fa-plus me-1"></i> Save Detail</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="pengembalianSection-${voucherId}" class="mt-3" style="display:none;">
            <div class="row g-3"><div class="col-md-6"><label class="form-label fw-semibold required-field">Cash Advance (from JO)</label><select id="input_kasbon-${voucherId}" class="form-select select2-kasbon-${voucherId}" data-placeholder="Select CA..." onchange="onKasbonChange(${voucherId})"><option value=""></option></select></div></div>
            <div id="kasbonInfoPanel-${voucherId}" class="mt-3" style="display:none;">
                ${buildKasbonInfoPanelHtml('kasbon_' + voucherId)}
                <div id="kasbonAlert-${voucherId}" class="mt-2" style="display:none;"></div>
                <div id="kasbonForm-${voucherId}" style="display:none;">
                    <div class="row g-3 mt-1">
<input type="hidden" id="input_dep-${voucherId}" value="">
<input type="hidden" id="input_branch-${voucherId}" value="">
<input type="hidden" id="input_release_to_ref-${voucherId}" value="">
<div class="col-md-4"><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;"><div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Department</div><div id="display_dep_ref-${voucherId}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div></div></div>
<div class="col-md-4"><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;"><div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Branch</div><div id="display_branch_ref-${voucherId}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div></div></div>
<div class="col-md-4"><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;"><div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Release To</div><div id="display_release_to_ref-${voucherId}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div></div></div>
<div class="col-md-12"><label class="form-label fw-semibold">Notes</label><input type="text" id="input_ket-${voucherId}" class="form-control" placeholder="Notes..."></div>
                        <div class="col-12 d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-cancel-edit-style" onclick="resetDetailForm(${voucherId})"><i class="fas fa-times"></i> Cancel</button>
                            <button type="button" class="btn btn-add-purple" onclick="saveDetailPengembalian(${voucherId})"><i class="fas fa-plus me-1"></i> Save Detail</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="manualSection-${voucherId}" class="mt-3" style="display:none;">
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label fw-semibold required-field">Department</label><select id="input_dep_manual-${voucherId}" class="form-select select2-dep-manual-${voucherId}" data-placeholder="Select Dept..."><option value=""></option>${depOpts}</select></div>
                <div class="col-md-4"><label class="form-label fw-semibold required-field">Branch</label><select id="input_branch_manual-${voucherId}" class="form-select select2-branch-manual-${voucherId}" data-placeholder="Select Branch..."><option value=""></option>${brOpts}</select></div>
                <div class="col-md-4"><label class="form-label fw-semibold required-field">Amount (IDR)</label><input type="text" id="input_nilai-${voucherId}" class="form-control" placeholder="0,00"><input type="hidden" id="input_nilai_raw-${voucherId}" value="0"></div>
                <div class="col-md-12"><label class="form-label fw-semibold">Notes</label><input type="text" id="input_ket_manual-${voucherId}" class="form-control" placeholder="Notes..."></div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-cancel-edit-style" onclick="resetDetailForm(${voucherId})"><i class="fas fa-times"></i> Cancel</button>
                    <button type="button" class="btn btn-add-purple" onclick="saveDetailManual(${voucherId})"><i class="fas fa-plus me-1"></i> Save Detail</button>
                </div>
            </div>
        </div>
    </div>
    <div id="detailEditMode-${voucherId}" style="display:none;" class="detail-edit-mode-box">
        <input type="hidden" id="editing_detail_id-${voucherId}" value="">
        <input type="hidden" id="editing_detail_tipe-${voucherId}" value="">
        <div class="edit-info-grid mb-3">
            <div class="edit-info-card"><div class="edit-info-lbl">Type</div><div class="edit-info-val"><span id="edit_jenis_badge-${voucherId}" class="jenis-badge"></span></div></div>
            <div class="edit-info-card"><div class="edit-info-lbl">Payment Type</div><div class="edit-info-val" id="edit_tipe_pem-${voucherId}">—</div></div>
            <div class="edit-info-card"><div class="edit-info-lbl">Customer</div><div class="edit-info-val" id="edit_customer-${voucherId}">—</div></div>
            <div class="edit-info-card"><div class="edit-info-lbl">JO / Kasbon</div><div class="edit-info-val" id="edit_ref-${voucherId}">—</div></div>
            <div id="edit_nilai_info_card-${voucherId}" class="edit-info-card"><div class="edit-info-lbl">Amount</div><div class="edit-info-val clr-nilai" id="edit_nilai_display-${voucherId}">—</div></div>
        </div>
        {{-- Editable fields: hanya untuk non-refund --}}
        <div id="edit_nonrefund_fields-${voucherId}" style="display:none;">
            <div class="row g-2 mb-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Department</label>
                    <select id="edit_dep-${voucherId}" class="form-select select2-edit-dep-${voucherId}" data-placeholder="Select Department...">
                        <option value=""></option>${depOpts}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Branch</label>
                    <select id="edit_branch-${voucherId}" class="form-select select2-edit-branch-${voucherId}" data-placeholder="Select Branch...">
                        <option value=""></option>${brOpts}
                    </select>
                </div>
<div class="col-md-4" id="edit_nilai_col-${voucherId}">
    <label class="form-label fw-semibold required-field">Amount (IDR)</label>
    <input type="text" id="edit_nilai_input-${voucherId}" class="form-control" placeholder="0,00">
    <input type="hidden" id="edit_nilai_raw-${voucherId}" value="0">
</div>
            </div>
        </div>
        <div class="mb-3" id="edit_release_to_wrap-${voucherId}" style="display:none;">
    <label class="form-label fw-semibold required-field">Release To</label>
    <input type="text" id="edit_release_to-${voucherId}" class="form-control" placeholder="Release to...">
</div>
        <div class="mb-3"><label class="form-label fw-semibold">Notes</label><input type="text" id="edit_keterangan-${voucherId}" class="form-control" placeholder="Notes..."></div>
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-cancel-edit-style" onclick="cancelDetailEdit(${voucherId})"><i class="fas fa-times me-1"></i> Cancel</button>
            <button type="button" class="btn btn-add-purple" onclick="submitDetailEdit(${voucherId})"><i class="fas fa-save me-1"></i> Update</button>
        </div>
    </div>
</div>`;
        }

        function buildKasbonInfoPanelHtml(pid) {
            return `<div style="position:relative;">
<button type="button" class="btn-kasbon-info" id="ki-info-btn-${pid}"
    onclick="showKasbonInfo(this)" title="View CA Detail"
    style="position:absolute;top:0;right:0;z-index:1;"
    data-nomor="" data-jo="" data-cust="" data-dep="" data-cab="" data-tgl=""
    data-nilai="0" data-pj="0" data-selisih="0">
    <i class="fas fa-info"></i>
</button>
</div>
<div class="row g-2 mb-2">
<div class="col-md-3"><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;"><div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">JO No.</div><div id="ki_nomor_jo-${pid}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div></div></div>
<div class="col-md-3"><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;"><div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Customer</div><div id="ki_customer-${pid}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div><input type="hidden" id="input_id_md_cust_ki-${pid}" value=""></div></div>
<div class="col-md-3"><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;"><div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Dept</div><div id="ki_dep-${pid}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div><input type="hidden" id="input_id_md_dep_ki-${pid}" value=""></div></div>
<div class="col-md-3"><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;"><div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Branch</div><div id="ki_branch-${pid}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div><input type="hidden" id="input_id_md_branch_ki-${pid}" value=""></div></div>
<div class="col-md-4"><div style="background:#fefce8;border:1px solid #fde047;border-radius:8px;padding:10px 14px;"><div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Refund Amount</div><div id="ki_selisih-${pid}" style="font-size:.95rem;font-weight:700;color:#d97706;">—</div><input type="hidden" id="input_nilai_ki-${pid}" value="0"></div></div>
</div>`;
        }

        // ============================================================
        // VOUCHER PANEL
        // ============================================================
        function toggleVoucherPanel(voucherId) {
            const $body = $(`#voucher-body-${voucherId}`);
            const $icon = $(`.toggle-icon-${voucherId}`);
            if ($body.is(':visible')) {
                $body.slideUp(200);
                $icon.css('transform', 'rotate(0deg)');
            } else {
                $body.slideDown(200);
                $icon.css('transform', 'rotate(180deg)');
                if (!$(`#detailTableBody-${voucherId}`).data('loaded')) {
                    loadVoucherDetails(voucherId);
                    $(`#detailTableBody-${voucherId}`).data('loaded', true);
                }
            }
        }

        function initVoucherPanelSelects(voucherId) {
            initS2(`.select2-jo-${voucherId}`, 'Select JO...');
            initS2(`.select2-kasbon-gen-${voucherId}`, 'Select General CA...');
            initS2(`.select2-kasbon-${voucherId}`, 'Select CA...');
            initS2(`.select2-dep-manual-${voucherId}`, 'Select Department...');
            initS2(`.select2-branch-manual-${voucherId}`, 'Select Branch...');
            setupRp(document.getElementById(`input_nilai-${voucherId}`));
            if (document.getElementById(`input_nilai-${voucherId}`)) {
                $(`#input_nilai-${voucherId}`).on('input', function() {
                    $(`#input_nilai_raw-${voucherId}`).val(parseRp($(this).val()));
                });
            }
            // edit dep/branch dan edit_nilai_input diinit lazy di editDetail (elemen masih hidden saat ini)
        }

        // ============================================================
        // DETAIL FORM — CASCADE LOGIC
        // ============================================================

        // Step 1: Jenis change
        function onJenisChange(voucherId) {
            const jenis = $(`#input_jenis-${voucherId}`).val();
            const $jenisCol = $(`#jenisCol-${voucherId}`);
            // reset all
            $(`#joCol-${voucherId}, #genKasbonCol-${voucherId}`).hide();
            $(`#custPanel-${voucherId}, #pengembalianSection-${voucherId}, #manualSection-${voucherId}`).hide();
            $(`#kasbonInfoPanel-${voucherId}, #genKasbonInfoPanel-${voucherId}`).hide();
            $(`#input_tipe_pem-${voucherId}`).val('');
            $(`#addDetailSection-${voucherId} .btn-tipe-pem`).removeClass('active');
            if (!jenis) {
                $jenisCol.removeClass('col-md-3').addClass('col-12');
                return;
            }
            $jenisCol.removeClass('col-12').addClass('col-md-3');

            if (jenis === 'general') {
                $(`#genKasbonCol-${voucherId}`).show();
                $(`#input_kasbon_gen-${voucherId}`).html('<option value="">Loading...</option>');
                $.get(ROUTES.kasbonOptions, {
                    jenis: 'general'
                }, function(r) {
                    let opts = '<option value=""></option>';
                    (r.data || []).forEach(k => {
                        opts +=
                            `<option value="${k.id}">${escH(k.nomor)}${k.label ? ' — ' + escH(k.label) : ''}</option>`;
                    });
                    $(`#input_kasbon_gen-${voucherId}`).html(opts).trigger('change.select2');
                });
            } else {
                $(`#joCol-${voucherId}`).show();
                $(`#input_jo-${voucherId}`).html('<option value="">Loading...</option>');
                $.get(ROUTES.joOptions, {
                    jenis
                }, function(r) {
                    let opts = '<option value=""></option>';
                    (r.data || []).forEach(j => {
                        opts +=
                            `<option value="${j.id}">${escH(j.nomor)}${j.label ? ' — ' + escH(j.label) : ''}</option>`;
                    });
                    $(`#input_jo-${voucherId}`).html(opts).trigger('change.select2');
                });
            }
        }

        // Step 2a: JO selected
        function onJoChange(voucherId) {
            const jenis = $(`#input_jenis-${voucherId}`).val();
            const joId = $(`#input_jo-${voucherId}`).val();
            $(`#custPanel-${voucherId}`).hide();
            $(`#pengembalianSection-${voucherId}, #manualSection-${voucherId}`).hide();
            $(`#input_tipe_pem-${voucherId}`).val('');
            $(`#addDetailSection-${voucherId} .btn-tipe-pem`).removeClass('active');
            if (!joId) return;

            $.get(ROUTES.joInfo, {
                jenis,
                jo_id: joId
            }, function(r) {
                if (!r.success) return;
                const d = r.data;
                $(`#joCustomerName-${voucherId}`).text(d.customer || '—');
                $(`#input_id_md_cust-${voucherId}`).val(d.id_md_cust || '');
                $(`#joVesselName-${voucherId}`).text(d.vessel || '—');
                $(`#joPortName-${voucherId}`).text(d.port || '—');
                $(`#input_id_md_port-${voucherId}`).val(d.id_md_port || '');
                $(`#custPanel-${voucherId}`).show();
            });
        }

        // Step 2b: General kasbon selected
        function onGeneralKasbonChange(voucherId) {
            const kasbonId = $(`#input_kasbon_gen-${voucherId}`).val();
            $(`#genKasbonInfoPanel-${voucherId}`).hide();
            $(`#genKasbonAlert-${voucherId}`).hide();
            $(`#genKasbonForm-${voucherId}`).hide();
            if (!kasbonId) return;
            const pid = 'gen_' + voucherId;
            $.get(ROUTES.kasbonInfo, {
                jenis: 'general',
                kasbon_id: kasbonId
            }, function(r) {
                if (!r.success) {
                    showFloatingAlert('error', 'Kasbon not found');
                    return;
                }
                fillKasbonInfoPanel(pid, r.data);
                $(`#genKasbonInfoPanel-${voucherId}`).show();
                // Cek LPJ dan selisih
                if (!r.data.has_lpj) {
                    $(`#genKasbonAlert-${voucherId}`).html(
                        `<div class="alert alert-warning py-2 mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Kasbon ini belum memiliki data LPJ. Refund tidak dapat dilakukan.</div>`
                    ).show();
                    $(`#genKasbonForm-${voucherId}`).hide();
                } else if (r.data.selisih <= 0) {
                    $(`#genKasbonAlert-${voucherId}`).html(
                        `<div class="alert alert-danger py-2 mb-0"><i class="fas fa-times-circle me-2"></i>Tidak ada selisih positif (sisa kasbon ≤ 0). Tidak ada yang perlu dikembalikan.</div>`
                    ).show();
                    $(`#genKasbonForm-${voucherId}`).hide();
                } else {
                    $(`#genKasbonAlert-${voucherId}`).hide();
                    $(`#genKasbonForm-${voucherId}`).show();
                }
            });
        }

        // Step 3: Tipe pembayaran selected
        function selectTipePembayaran(voucherId, tipe, btn) {
            $(`#addDetailSection-${voucherId} .btn-tipe-pem`).removeClass('active');
            $(btn).addClass('active');
            $(`#input_tipe_pem-${voucherId}`).val(tipe);
            $(`#pengembalianSection-${voucherId}, #manualSection-${voucherId}`).hide();
            $(`#kasbonInfoPanel-${voucherId}`).hide();

            if (tipe === 'pengembalian_lebih_bayar') {
                $(`#custPanel-${voucherId}`).hide();
                $(`#pengembalianSection-${voucherId}`).show();
                // Load kasbon options for this JO
                const jenis = $(`#input_jenis-${voucherId}`).val();
                const joId = $(`#input_jo-${voucherId}`).val();
                $(`#input_kasbon-${voucherId}`).html('<option value="">Loading...</option>');
                $.get(ROUTES.kasbonOptions, {
                    jenis,
                    jo_id: joId
                }, function(r) {
                    let opts = '<option value=""></option>';
                    (r.data || []).forEach(k => {
                        opts +=
                            `<option value="${k.id}">${escH(k.nomor)}${k.label ? ' — ' + escH(k.label) : ''}</option>`;
                    });
                    $(`#input_kasbon-${voucherId}`).html(opts).trigger('change.select2');
                });
            } else {
                const joId = $(`#input_jo-${voucherId}`).val();
                if (joId) $(`#custPanel-${voucherId}`).show();
                $(`#manualSection-${voucherId}`).show();
            }
        }

        // Step 3b: Kasbon selected (for pengembalian)
        function onKasbonChange(voucherId) {
            const jenis = $(`#input_jenis-${voucherId}`).val();
            const kasbonId = $(`#input_kasbon-${voucherId}`).val();
            $(`#kasbonInfoPanel-${voucherId}`).hide();
            $(`#kasbonAlert-${voucherId}`).hide();
            $(`#kasbonForm-${voucherId}`).hide();
            if (!kasbonId) return;
            const pid = 'kasbon_' + voucherId;
            $.get(ROUTES.kasbonInfo, {
                jenis,
                kasbon_id: kasbonId
            }, function(r) {
                if (!r.success) {
                    showFloatingAlert('error', 'Kasbon not found');
                    return;
                }
                fillKasbonInfoPanel(pid, r.data);
                $(`#kasbonInfoPanel-${voucherId}`).show();
                // Cek LPJ dan selisih
                if (!r.data.has_lpj) {
                    $(`#kasbonAlert-${voucherId}`).html(
                        `<div class="alert alert-warning py-2 mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Kasbon ini belum memiliki data LPJ. Refund tidak dapat dilakukan.</div>`
                    ).show();
                    $(`#kasbonForm-${voucherId}`).hide();
                } else if (r.data.selisih <= 0) {
                    $(`#kasbonAlert-${voucherId}`).html(
                        `<div class="alert alert-danger py-2 mb-0"><i class="fas fa-times-circle me-2"></i>Tidak ada selisih positif (sisa kasbon ≤ 0). Tidak ada yang perlu dikembalikan.</div>`
                    ).show();
                    $(`#kasbonForm-${voucherId}`).hide();
                } else {
                    $(`#kasbonAlert-${voucherId}`).hide();
                    $(`#kasbonForm-${voucherId}`).show();
                }
            });
        }

        function fillKasbonInfoPanel(pid, d) {
            $(`#ki_nomor_jo-${pid}`).text(d.nomor_jo || '—');
            $(`#ki_customer-${pid}`).text(d.customer || '—');
            $(`#ki_dep-${pid}`).text(d.departemen || '—');
            $(`#ki_branch-${pid}`).text(d.branch || '—');
            $(`#ki_selisih-${pid}`).text('IDR ' + fmt(d.selisih || 0));
            $(`#input_id_md_cust_ki-${pid}`).val(d.id_md_cust || '');
            $(`#input_id_md_dep_ki-${pid}`).val(d.id_md_dep || '');
            $(`#input_id_md_branch_ki-${pid}`).val(d.id_md_branch || '');
            $(`#input_nilai_ki-${pid}`).val(d.selisih || 0);

            // auto-fill form general jika pid diawali 'gen_'
            if (pid.startsWith('gen_')) {
                const voucherId = pid.replace('gen_', '');
                $(`#input_dep_gen-${voucherId}`).val(d.id_md_dep || '');
                $(`#input_branch_gen-${voucherId}`).val(d.id_md_branch || '');
                $(`#input_release_to_gen-${voucherId}`).val(d.release_to || '');
                $(`#display_dep_gen-${voucherId}`).text(d.departemen || '—');
                $(`#display_branch_gen-${voucherId}`).text(d.branch || '—');
                $(`#display_release_to_gen-${voucherId}`).text(d.release_to || '—');
            }

            // auto-fill form refund (tramper/other/contract) jika pid diawali 'kasbon_'
            if (pid.startsWith('kasbon_')) {
                const voucherId = pid.replace('kasbon_', '');
                $(`#input_dep-${voucherId}`).val(d.id_md_dep || '');
                $(`#input_branch-${voucherId}`).val(d.id_md_branch || '');
                $(`#input_release_to_ref-${voucherId}`).val(d.release_to || '');
                $(`#display_dep_ref-${voucherId}`).text(d.departemen || '—');
                $(`#display_branch_ref-${voucherId}`).text(d.branch || '—');
                $(`#display_release_to_ref-${voucherId}`).text(d.release_to || '—');
                $(`#display_vessel_ref-${voucherId}`).text(d.vessel || '—');
                $(`#display_port_ref-${voucherId}`).text(d.port || '—');
            }

            // Isi data-attribute pada tombol info agar showKasbonInfo() bisa membacanya
            const $btn = $(`#ki-info-btn-${pid}`);
            $btn.attr('data-nomor', d.nomor || '');
            $btn.attr('data-jo', d.nomor_jo || '');
            $btn.attr('data-cust', d.customer || '');
            $btn.attr('data-dep', d.departemen || '');
            $btn.attr('data-cab', d.branch || '');
            $btn.attr('data-tgl', d.tgl_kasbon || '');
            $btn.attr('data-nilai', d.nilai || 0);
            $btn.attr('data-pj', d.pj || 0);
            $btn.attr('data-selisih', d.selisih || 0);
            $btn.attr('data-items', JSON.stringify(d.items || []));
        }

        // ============================================================
        // SAVE DETAIL
        // ============================================================
        function saveDetailGeneral(voucherId) {
            const kasbonId = $(`#input_kasbon_gen-${voucherId}`).val();
            const dep = $(`#input_dep_gen-${voucherId}`).val();
            const branch = $(`#input_branch_gen-${voucherId}`).val();
            const releaseTo = $(`#input_release_to_gen-${voucherId}`).val().trim();
            const ket = $(`#input_ket_gen-${voucherId}`).val();
            if (!kasbonId) {
                showFloatingAlert('error', 'Cash advance is required');
                return;
            }
            const pid = 'gen_' + voucherId;
            submitDetail(voucherId, {
                jenis: 'general',
                tipe_pembayaran: 'pengembalian_lebih_bayar',
                id_kasbon_gen: kasbonId,
                id_md_cust: $(`#input_id_md_cust_ki-${pid}`).val(),
                id_md_dep: dep,
                id_md_branch: branch,
                release_to: releaseTo,
                keterangan: ket,
            });
        }

        function saveDetailPengembalian(voucherId) {
            const jenis = $(`#input_jenis-${voucherId}`).val();
            const kasbonId = $(`#input_kasbon-${voucherId}`).val();
            const dep = $(`#input_dep-${voucherId}`).val();
            const branch = $(`#input_branch-${voucherId}`).val();
            const releaseTo = $(`#input_release_to_ref-${voucherId}`).val();
            const ket = $(`#input_ket-${voucherId}`).val();
            if (!kasbonId) {
                showFloatingAlert('error', 'Cash advance is required');
                return;
            }
            const pid = 'kasbon_' + voucherId;
            const joId = $(`#input_jo-${voucherId}`).val();
            const custId = $(`#input_id_md_cust_ki-${pid}`).val();
            const payload = {
                jenis,
                tipe_pembayaran: 'pengembalian_lebih_bayar',
                id_md_cust: custId,
                id_md_dep: dep,
                id_md_branch: branch,
                release_to: releaseTo,
                keterangan: ket,
            };
            if (jenis === 'tramper') {
                payload.id_kasbon_tram = kasbonId;
                payload.id_jo_tram = joId;
            }
            if (jenis === 'other') {
                payload.id_kasbon_other = kasbonId;
                payload.id_jo_other = joId;
            }
            if (jenis === 'contract') {
                payload.id_kasbon_cont = kasbonId;
                payload.id_jo_cont = joId;
            }
            submitDetail(voucherId, payload);
        }

        function saveDetailManual(voucherId) {
            const jenis = $(`#input_jenis-${voucherId}`).val();
            const tipe = $(`#input_tipe_pem-${voucherId}`).val();
            const joId = $(`#input_jo-${voucherId}`).val();
            const dep = $(`#input_dep_manual-${voucherId}`).val();
            const branch = $(`#input_branch_manual-${voucherId}`).val();
            const nilai = parseRp($(`#input_nilai-${voucherId}`).val());
            const ket = $(`#input_ket_manual-${voucherId}`).val();
            const custId = $(`#input_id_md_cust-${voucherId}`).val();
            if (nilai <= 0 || !dep || !branch) {
                showFloatingAlert('error', 'Dept, Branch, and amount are required');
                return;
            }
            const payload = {
                jenis,
                tipe_pembayaran: tipe,
                id_md_dep: dep,
                id_md_branch: branch,
                id_md_cust: custId,
                nilai,
                keterangan: ket
            };
            if (jenis === 'tramper') payload.id_jo_tram = joId;
            if (jenis === 'other') payload.id_jo_other = joId;
            if (jenis === 'contract') payload.id_jo_cont = joId;
            submitDetail(voucherId, payload);
        }

        function submitDetail(voucherId, extraPayload) {
            showFloatingAlert('saving', 'Saving detail...');
            $.ajax({
                url: ROUTES.detailStore,
                method: 'POST',
                data: {
                    id_mutasi_voucher: voucherId,
                    ...extraPayload,
                    _token: CSRF
                },
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', r.message || 'Failed');
                        return;
                    }
                    showFloatingAlert('success', 'Detail added!');
                    resetDetailForm(voucherId);
                    loadVoucherDetails(voucherId);
                },
                error: xhr => showFloatingAlert('error', xhr.responseJSON?.errors ? Object.values(xhr.responseJSON
                    .errors).flat().join(', ') : (xhr.responseJSON?.message || 'Failed')),
            });
        }

        // ============================================================
        // RESET DETAIL FORM
        // ============================================================
        function resetDetailForm(voucherId) {
            $(`#input_jenis-${voucherId}`).val('');
            $(`#joCol-${voucherId}, #genKasbonCol-${voucherId}`).hide();
            $(`#custPanel-${voucherId}, #pengembalianSection-${voucherId}, #manualSection-${voucherId}`).hide();
            $(`#kasbonInfoPanel-${voucherId}, #genKasbonInfoPanel-${voucherId}`).hide();
            $(`#kasbonAlert-${voucherId}, #kasbonForm-${voucherId}`).hide();
            $(`#genKasbonAlert-${voucherId}, #genKasbonForm-${voucherId}`).hide();
            $(`#input_tipe_pem-${voucherId}`).val('');
            $(`#addDetailSection-${voucherId} .btn-tipe-pem`).removeClass('active');
            $(`#input_jo-${voucherId}, #input_kasbon_gen-${voucherId}, #input_kasbon-${voucherId}`)
                .val('').trigger('change.select2');
            $(`#input_dep-${voucherId}, #input_branch-${voucherId}`).val('').trigger('change.select2');
            $(`#input_dep_gen-${voucherId}, #input_branch_gen-${voucherId}`).val('').trigger('change.select2');
            $(`#input_dep_manual-${voucherId}, #input_branch_manual-${voucherId}`).val('').trigger('change.select2');
            $(`#input_nilai-${voucherId}`).val('');
            $(`#input_ket-${voucherId}, #input_ket_gen-${voucherId}, #input_ket_manual-${voucherId}`).val('');
            $(`#input_dep_gen-${voucherId}`).val('');
            $(`#input_branch_gen-${voucherId}`).val('');
            $(`#input_release_to_gen-${voucherId}`).val('');
            $(`#display_dep_gen-${voucherId}`).text('—');
            $(`#display_branch_gen-${voucherId}`).text('—');
            $(`#display_release_to_gen-${voucherId}`).text('—');
        }

        // ============================================================
        // DETAIL EDIT
        // ============================================================
        function editDetail(detailId, voucherId) {
            showFloatingAlert('saving', 'Loading detail...');
            $.get(ROUTES.detailShow + detailId, function(r) {
                if (!r.success) {
                    showFloatingAlert('error', 'Failed to load detail');
                    return;
                }
                hideFloatingAlert();
                const d = r.data;
                const isRefund = (d.tipe_pembayaran === 'pengembalian_lebih_bayar');

                $(`#detailAddMode-${voucherId}`).hide();
                $(`#detailEditMode-${voucherId}`).show();
                $(`#addDetailSection-${voucherId}`).addClass('edit-mode');

                const editLabel = '<i class="fas fa-edit" style="color:#3b82f6;"></i> Edit Detail';
                $(`#detailFormTitle-${voucherId}`).html(editLabel);

                $(`#editing_detail_id-${voucherId}`).val(detailId);
                $(`#editing_detail_tipe-${voucherId}`).val(d.tipe_pembayaran || '');
                $(`#edit_jenis_badge-${voucherId}`).attr('class', `jenis-badge jenis-${d.jenis}`).text(d.jenis);
                $(`#edit_tipe_pem-${voucherId}`).text(d.tipe_pembayaran_label || d.tipe_pembayaran || '—');
                $(`#edit_customer-${voucherId}`).text(d.customer || '—');
                $(`#edit_ref-${voucherId}`).text(d.nomor_kasbon || d.nomor_jo || '—');
                $(`#edit_keterangan-${voucherId}`).val(d.keterangan || '');
                if (d.jenis === 'general') {
                    $(`#edit_release_to_wrap-${voucherId}`).show();
                    $(`#edit_release_to-${voucherId}`).val(d.release_to || '');
                } else {
                    $(`#edit_release_to_wrap-${voucherId}`).hide();
                    $(`#edit_release_to-${voucherId}`).val('');
                }

                if (isRefund) {
                    // Refund: amount hanya info, tidak bisa diedit — tapi dept & branch bisa
                    $(`#edit_nilai_info_card-${voucherId}`).show();
                    $(`#edit_nilai_display-${voucherId}`).text('IDR ' + fmt(d.nilai || 0));
                    $(`#edit_nonrefund_fields-${voucherId}`).show(); // ← tampilkan
                    $(`#edit_nilai_col-${voucherId}`).hide(); // ← sembunyikan amount saja

                    // Lazy-init select2 untuk dep/branch (sama seperti non-refund)
                    const $editDep = $(`#edit_dep-${voucherId}`);
                    const $editBranch = $(`#edit_branch-${voucherId}`);
                    if (!$editDep.hasClass('select2-hidden-accessible')) {
                        $editDep.select2({
                            theme: 'bootstrap-5',
                            placeholder: 'Select Department...',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                    if (!$editBranch.hasClass('select2-hidden-accessible')) {
                        $editBranch.select2({
                            theme: 'bootstrap-5',
                            placeholder: 'Select Branch...',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                    $editDep.val(d.id_md_dep || null).trigger('change');
                    $editBranch.val(d.id_md_branch || null).trigger('change');
                } else {
                    // Non-refund: tampilkan field editable, sembunyikan info card amount
                    $(`#edit_nilai_info_card-${voucherId}`).hide();
                    const $nrFields = $(`#edit_nonrefund_fields-${voucherId}`);
                    $nrFields.show();

                    // Lazy-init select2 untuk edit dep/branch (baru setelah elemen visible)
                    const $editDep = $(`#edit_dep-${voucherId}`);
                    const $editBranch = $(`#edit_branch-${voucherId}`);
                    if (!$editDep.hasClass('select2-hidden-accessible')) {
                        $editDep.select2({
                            theme: 'bootstrap-5',
                            placeholder: 'Select Department...',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                    if (!$editBranch.hasClass('select2-hidden-accessible')) {
                        $editBranch.select2({
                            theme: 'bootstrap-5',
                            placeholder: 'Select Branch...',
                            allowClear: true,
                            width: '100%'
                        });
                    }

                    // Set nilai dep & branch
                    $editDep.val(d.id_md_dep || null).trigger('change');
                    $editBranch.val(d.id_md_branch || null).trigger('change');

                    // Set nilai amount
                    const nilaiFormatted = fmt(d.nilai || 0);
                    $(`#edit_nilai_input-${voucherId}`).val(nilaiFormatted);
                    $(`#edit_nilai_raw-${voucherId}`).val(d.nilai || 0);

                    // Setup rupiah formatter jika belum
                    const elNilaiInput = document.getElementById(`edit_nilai_input-${voucherId}`);
                    if (elNilaiInput && !elNilaiInput._rpSetup) {
                        setupRp(elNilaiInput);
                        $(`#edit_nilai_input-${voucherId}`).on('input', function() {
                            $(`#edit_nilai_raw-${voucherId}`).val(parseRp($(this).val()));
                        });
                        elNilaiInput._rpSetup = true;
                    }
                }

                $('html,body').animate({
                    scrollTop: $(`#addDetailSection-${voucherId}`).offset().top - 100
                }, 400);
            }).fail(() => showFloatingAlert('error', 'Failed to load detail'));
        }

        function cancelDetailEdit(voucherId) {
            $(`#editing_detail_id-${voucherId}`).val('');
            $(`#editing_detail_tipe-${voucherId}`).val('');
            $(`#detailFormTitle-${voucherId}`).html('<i class="fas fa-plus-square" style="color:#16a34a;"></i> Add Detail');
            $(`#addDetailSection-${voucherId}`).removeClass('edit-mode');
            $(`#detailEditMode-${voucherId}`).hide();
            $(`#detailAddMode-${voucherId}`).show();
            // Reset edit fields
            $(`#edit_dep-${voucherId}`).val(null).trigger('change');
            $(`#edit_branch-${voucherId}`).val(null).trigger('change');
            $(`#edit_nilai_input-${voucherId}`).val('');
            $(`#edit_nilai_raw-${voucherId}`).val('0');
            $(`#edit_keterangan-${voucherId}`).val('');
            $(`#edit_release_to-${voucherId}`).val('');
            $(`#edit_release_to_wrap-${voucherId}`).hide();
            $(`#edit_nonrefund_fields-${voucherId}`).hide();
            $(`#edit_nilai_col-${voucherId}`).show();
            $(`#edit_nilai_info_card-${voucherId}`).show();
            resetDetailForm(voucherId);
        }

        function submitDetailEdit(voucherId) {
            const detailId = $(`#editing_detail_id-${voucherId}`).val();
            const tipe = $(`#editing_detail_tipe-${voucherId}`).val();
            const keterangan = $(`#edit_keterangan-${voucherId}`).val();
            const isRefund = (tipe === 'pengembalian_lebih_bayar');

            const payload = {
                keterangan,
                release_to: $(`#edit_release_to-${voucherId}`).val() || null,
                _method: 'PUT',
                _token: CSRF
            };

            if (!isRefund) {
                const dep = $(`#edit_dep-${voucherId}`).val();
                const branch = $(`#edit_branch-${voucherId}`).val();
                const nilai = parseFloat($(`#edit_nilai_raw-${voucherId}`).val()) || 0;
                if (!nilai || nilai <= 0) {
                    showFloatingAlert('error', 'Amount harus diisi');
                    return;
                }
                payload.id_md_dep = dep;
                payload.id_md_branch = branch;
                payload.nilai = nilai;
            } else {
                // refund: dep & branch boleh dikirim, nilai tidak
                payload.id_md_dep = $(`#edit_dep-${voucherId}`).val();
                payload.id_md_branch = $(`#edit_branch-${voucherId}`).val();
            }

            showFloatingAlert('saving', 'Updating...');
            $.ajax({
                url: ROUTES.detailUpdate + detailId,
                method: 'POST',
                data: payload,
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', r.message || 'Failed');
                        return;
                    }
                    showFloatingAlert('success', 'Detail updated!');
                    cancelDetailEdit(voucherId);
                    loadVoucherDetails(voucherId);
                },
                error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Failed'),
            });
        }

        // ============================================================
        // DELETE DETAIL
        // ============================================================
        let _pendingDelId = null,
            _pendingDelVid = null;

        function deleteDetail(detailId, voucherId) {
            _pendingDelId = detailId;
            _pendingDelVid = voucherId;
            new bootstrap.Modal(document.getElementById('detailConfirmModal')).show();
        }
        $('#btnConfirmDeleteDetail').on('click', function() {
            if (!_pendingDelId) return;
            const id = _pendingDelId,
                vid = _pendingDelVid;
            bootstrap.Modal.getInstance(document.getElementById('detailConfirmModal')).hide();
            showFloatingAlert('saving', 'Deleting detail...');
            $.ajax({
                url: ROUTES.detailDestroy + id,
                method: 'DELETE',
                data: {
                    _token: CSRF
                },
                success: function(r) {
                    if (r.success) {
                        showFloatingAlert('success', 'Detail deleted!');
                        loadVoucherDetails(vid);
                    } else showFloatingAlert('error', r.message || 'Failed');
                },
                error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Failed'),
            });
            _pendingDelId = _pendingDelVid = null;
        });

        // ============================================================
        // LOAD & RENDER DETAIL TABLE
        // ============================================================
        function loadVoucherDetails(voucherId) {
            $.get(ROUTES.voucherDetails + voucherId + '/details', function(r) {
                if (r.success) renderDetailTable(voucherId, r.data);
                else $(`#detailTableBody-${voucherId}`).html(
                    `<tr><td colspan="8" class="text-center text-danger py-3"><i class="fas fa-exclamation-triangle me-1"></i> Failed to load</td></tr>`
                );
            }).fail(() => {
                $(`#detailTableBody-${voucherId}`).html(
                    `<tr><td colspan="8" class="text-center text-danger py-3"><i class="fas fa-exclamation-triangle me-1"></i> Failed to load</td></tr>`
                );
            });
        }

        function renderDetailTable(voucherId, details) {
            const tbody = $(`#detailTableBody-${voucherId}`);
            const tfoot = $(`#detailTableFoot-${voucherId}`);

            if (!details || details.length === 0) {
                tbody.html(
                    `<tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x d-block mb-2"></i>No details yet</td></tr>`
                );
                tfoot.html('');
                $(`.detail-count-badge-${voucherId}`).text('0 detail');
                return;
            }

            const tipeLabelMap = {
                'uang_muka_pembayaran': ['Down Payment', 'tipe-uang-muka'],
                'penerimaan_pembayaran': ['Payment Receipt', 'tipe-penerimaan'],
                'pengembalian_lebih_bayar': ['Refund', 'tipe-pengembalian'],
            };

            let html = '',
                totNilai = 0;
            details.forEach(d => {
                totNilai += parseFloat(d.nilai || 0);
                const ref = d.nomor_kasbon ? (d.nomor_jo ? `${escH(d.nomor_jo)} / ${escH(d.nomor_kasbon)}` : escH(d
                    .nomor_kasbon)) : (escH(d.nomor_jo) || '—');
                const coaTxt = d.coa_display || '—';
                const depBr = [d.departemen, d.branch].filter(Boolean).join(' / ') || '—';
                const [tpLabel, tpClass] = tipeLabelMap[d.tipe_pembayaran] || [d.tipe_pembayaran, 'tipe-uang-muka'];
                const custDisplay = d.jenis === 'general' ? (d.release_to || '—') : (d.customer || '—');
                html += `
<tr>
    <td class="text-center"><span class="jenis-badge jenis-${d.jenis}">${escH(d.jenis)}</span></td>
    <td class="text-center"><span class="tipe-badge ${tpClass}">${tpLabel}</span></td>
    <td style="font-size:.82rem;">${escH(custDisplay)}</td>
    <td style="font-size:.82rem;">${ref}</td>
    <td style="font-size:.78rem;">${escH(depBr)}</td>
    <td class="num-cell" style="font-weight:700;color:#0369a1;">${fmt(d.nilai || 0)}</td>
    <td style="font-size:.78rem;color:#6c757d;">${escH(d.keterangan || '—')}</td>
    <td class="text-center">
        <button class="btn btn-primary btn-sm d-block mx-auto mb-1" onclick="editDetail('${d.id}',${voucherId})" title="Edit"><i class="fas fa-edit"></i></button>
        <button class="btn btn-danger btn-sm d-block mx-auto" onclick="deleteDetail('${d.id}',${voucherId})" title="Delete"><i class="fas fa-trash"></i></button>
    </td>
</tr>`;
            });

            tbody.html(html);
            tfoot.html(`<tr>
    <td colspan="5" class="text-end fw-bold">GRAND TOTAL</td>
    <td class="num-cell" style="white-space:nowrap;"><span class="badge bg-secondary rounded-1 me-1" style="font-size:.62rem;vertical-align:middle;">IDR</span>${fmt(totNilai)}</td>
    <td colspan="2"></td>
</tr>`);
            $(`.detail-count-badge-${voucherId}`).text(details.length + ' detail');
        }

        // ============================================================
        // Pass master data to JS for newly appended vouchers
        // ============================================================
        window._allCoa = @json(($allCoa ?? collect())->map(fn($c) => ['id' => $c->id_md_chart_of_account, 'nomor' => $c->no_account, 'nama' => $c->account_name]));
        window._depList = @json(($departemenList ?? collect())->map(fn($d) => ['id' => $d->id_md_dep, 'nama' => $d->nama_dep]));
        window._brList = @json(($branchList ?? collect())->map(fn($b) => ['id' => $b->id_md_branch, 'nama' => $b->nama_branch]));
    </script>
@endpush
