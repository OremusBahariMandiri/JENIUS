@extends('layouts.app')

@section('title', 'Edit Mutasi Pembayaran')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        /* ══════════════════════════════════════════════
                                                                                                                                                                                                                                                                                                                                                                                                                   PAGE WRAPPER
                                                                                                                                                                                                                                                                                                                                                                                                                ══════════════════════════════════════════════ */
        .mutasiEditPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .mutasiEditPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .mutasiEditPage .form-control:focus,
        .mutasiEditPage .form-select:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 0.2rem rgba(22, 163, 74, 0.25);
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: 600;
        }

        /* ══════════════════════════════════════════════
                                                                                                                                                                                                                                                                                                                                                                                                                   FLOATING BADGE ALERT
                                                                                                                                                                                                                                                                                                                                                                                                                ══════════════════════════════════════════════ */
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
            animation: slideOutRight 0.4s ease-in;
        }

        /* ══════════════════════════════════════════════
                                                                                                                                                                                                                                                                                                                                                                                                                   VOUCHER ACCORDION
                                                                                                                                                                                                                                                                                                                                                                                                                ══════════════════════════════════════════════ */
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
            transition: background 0.2s;
        }

        .voucher-accordion-header:hover {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        }

        .voucher-accordion-header .voucher-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .voucher-number-badge {
            background: #16a34a;
            color: #fff;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .voucher-accordion-body {
            padding: 20px;
            background: #fff;
        }

        /* ══════════════════════════════════════════════
                                                                                                                                                                                                                                                                                                                                                                                                                   ADD / EDIT FORM SECTION
                                                                                                                                                                                                                                                                                                                                                                                                                ══════════════════════════════════════════════ */
        .add-form-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px dashed #16a34a;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .add-form-section.edit-mode {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border: 2px solid #3b82f6;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }

        .add-form-section h6 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .add-form-section.edit-mode h6 {
            color: #1e40af;
        }

        .add-form-section .form-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.875rem;
        }

        /* Edit mode box (detail) */
        .detail-edit-mode-box {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border: 2px solid #3b82f6;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.2);
        }

        /* Edit info cards — LPJ style */
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
            word-break: break-all;
        }

        .edit-info-val.clr-nilai {
            color: #0369a1;
        }

        .edit-info-val.clr-pj {
            color: #15803d;
        }

        .edit-info-val.clr-sel-neg {
            color: #dc2626;
        }

        .edit-info-val.clr-sel-pos {
            color: #16a34a;
        }

        /* ══════════════════════════════════════════════
                                                                                                                                                                                                                                                                                                                                                                                                                   BUTTONS
                                                                                                                                                                                                                                                                                                                                                                                                                ══════════════════════════════════════════════ */
        .btn-add-purple {
            background: linear-gradient(135deg, #16a34a, #15803d);
            border: none;
            color: #fff;
            padding: 10px 24px;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.4);
            transition: all 0.3s;
        }

        .btn-add-purple:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.5);
            color: #fff;
        }

        .btn-cancel-edit-style {
            background: linear-gradient(135deg, #bbb, #5c5c5c);
            border: none;
            color: #fff;
            padding: 10px 24px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s;
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
            transition: all 0.3s;
        }

        .btn-final-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(49, 49, 49, 0.4);
            color: #fff;
        }

        /* ══════════════════════════════════════════════
                                                                                                                                                                                                                                                                                                                                                                                                                   DETAIL TABLE (tabel hasil)
                                                                                                                                                                                                                                                                                                                                                                                                                ══════════════════════════════════════════════ */
        .table-detail thead th {
            background-color: #14532d;
            color: #fff;
            border: 1px solid #14532d;
            padding: 10px 8px;
            font-weight: 600;
            font-size: 0.8rem;
            text-align: center;
            vertical-align: middle;
        }

        .table-detail tbody td {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: middle;
            font-size: 0.875rem;
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
            font-size: 0.875rem;
        }

        .table-detail tfoot td.num-cell {
            text-align: right;
        }

        .no-detail-row td {
            text-align: center;
            padding: 30px 20px;
            color: #6c757d;
            background: #f8f9fa !important;
        }

        .jenis-badge {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
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
            background: #dcfce7;
            color: #14532d;
        }

        /* ══════════════════════════════════════════════
                                                                                                                                                                                                                                                                                                                                                                                                                   KASBON SUMMARY TABLE (dalam form tambah detail)
                                                                                                                                                                                                                                                                                                                                                                                                                ══════════════════════════════════════════════ */
        .kasbon-summary-wrap {
            border: 1px solid #14532d;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .kasbon-summary-header {
            background: #14532d;
            color: #fff;
            padding: 10px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .table-kasbon-summary {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table-kasbon-summary thead th {
            background: #166534;
            color: #fff;
            padding: 9px 10px;
            font-size: 0.73rem;
            font-weight: 600;
            text-align: center;
            border: none;
            white-space: nowrap;
        }

        .table-kasbon-summary thead th.text-start {
            text-align: left;
        }

        .table-kasbon-summary thead th.text-end {
            text-align: right;
        }

        .table-kasbon-summary tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.15s;
        }

        .table-kasbon-summary tbody tr:hover td {
            background: #f0fdf4;
        }

        .table-kasbon-summary tbody tr.sudah-dipakai td {
            background: #f8fafc;
            color: #94a3b8;
        }

        .table-kasbon-summary tbody tr.sudah-dipakai:hover td {
            background: #f1f5f9;
        }

        .table-kasbon-summary tbody td {
            padding: 9px 10px;
            font-size: 0.82rem;
            vertical-align: middle;
            background: #fff;
        }

        .table-kasbon-summary tbody td.num-cell {
            text-align: right;
            font-variant-numeric: tabular-nums;
            font-weight: 600;
        }

        .table-kasbon-summary tfoot td {
            background: #14532d;
            color: #fff;
            padding: 9px 10px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .table-kasbon-summary tfoot td.num-cell {
            text-align: right;
        }

        /* Warna nominal */
        .clr-nilai {
            color: #16a34a;
        }

        .clr-pj {
            color: #0ea5e9;
        }

        .clr-pos {
            color: #d97706;
        }

        .clr-zero {
            color: #16a34a;
        }

        .clr-neg {
            color: #dc2626;
        }

        .clr-muted {
            color: #94a3b8;
        }

        /* Tombol info (ikon i bulat) */
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

        /* Tombol tambah di tabel kasbon */
        .btn-tambah-kasbon {
            background: linear-gradient(135deg, #16a34a, #15803d);
            border: none;
            color: #fff;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
        }

        .btn-tambah-kasbon:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(22, 163, 74, 0.35);
        }

        .btn-tambah-kasbon:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        body.modal-open {
            padding-right: 0 !important;
            overflow-y: scroll;
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
            color: black;
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

        #modalKasbonInfo .ki-table tbody td.cat-cell {
            color: #64748b;
            font-size: .78rem;
        }

        #modalKasbonInfo .ki-table tfoot td {
            background: #f5f6f7;
            color: #000000;
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



        /* ══════════════════════════════════════════════
                                                                                                                                                                                                                                                                                                                                                                                                                   FINAL SAVE SECTION
                                                                                                                                                                                                                                                                                                                                                                                                                ══════════════════════════════════════════════ */
        .final-save-section {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 16px 20px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px 12px 0 0;
            margin-top: 20px;
            z-index: 100;
        }

        /* ══════════════════════════════════════════════
                                                                                                                                                                                                                                                                                                                                                                                                                   SELECT2
                                                                                                                                                                                                                                                                                                                                                                                                                ══════════════════════════════════════════════ */
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px) !important;
            padding: 0.375rem 0.75rem !important;
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

        /* Toggle items button */
        .btn-toggle-items {
            background: none;
            border: 1px solid #e2e8f0;
            color: #64748b;
            width: 22px;
            height: 22px;
            border-radius: 4px;
            font-size: 0.7rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            flex-shrink: 0;
            padding: 0;
        }

        .btn-toggle-items:hover:not(:disabled) {
            background: #e0f2fe;
            border-color: #7dd3fc;
            color: #0369a1;
        }

        .btn-toggle-items .toggle-chev {
            transition: transform 0.2s;
            display: inline-block;
        }

        /* Item sub-rows */
        .table-kasbon-summary tr.kasbon-item-row td {
            background: #f0fdf4 !important;
            border-bottom: 1px solid #dcfce7;
            font-size: 0.79rem;
            color: #374151;
        }

        .table-kasbon-summary tr.kasbon-item-row.sudah-dipakai td {
            opacity: 0.6;
        }

        .detail-jo-group-row td {
            background: #f0fdf4 !important;
            font-weight: 700;
            font-size: 0.8rem;
            color: #14532d;
            border-top: 2px solid #bbf7d0 !important;
            cursor: pointer;
        }

        .detail-jo-group-row:hover td {
            background: #dcfce7 !important;
        }

        /* Kategori badge */
        .badge-kategori {
            font-size: 0.68rem;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <!-- FLOATING BADGE ALERT -->
    <div id="floatingBadgeAlert" class="floating-badge-alert">
        <i class="fas fa-circle-notch fa-spin" id="alertIcon"></i>
        <div class="alert-text" id="alertText">Processing...</div>
    </div>

    <div class="container-fluid mutasiEditPage">
        <div class="row">
            <div class="col-lg-12">

                <!-- Page Header Card -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #dcfce7">
                        <span class="fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Edit Payment Mutation</span>
                    </div>
                </div>

                <!-- Hidden State -->
                <input type="hidden" id="current_mutasi_id" value="{{ $mutasiPembayaran->id }}">

                <!-- ==================== HEADER CARD ==================== -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color: #dcfce7">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Payment Mutation Info</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Nomor (readonly) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Document No.</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#16a34a; color:white;">
                                        <i class="fas fa-hashtag"></i>
                                    </span>
                                    <input type="text" class="form-control fw-bold"
                                        value="{{ $mutasiPembayaran->nomor ?? '-' }}" readonly
                                        style="background-color:#f0fdf4; color:#14532d; letter-spacing:1px;">
                                </div>
                            </div>

                            <!-- Tanggal -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Date</label>
                                <input type="date" id="edit_tanggal" class="form-control"
                                    value="{{ $mutasiPembayaran->tanggal ? \Carbon\Carbon::parse($mutasiPembayaran->tanggal)->format('Y-m-d') : '' }}">
                            </div>

                            <!-- No Cek -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Cek / BG</label>
                                <input type="text" id="edit_no_cek" class="form-control"
                                    value="{{ $mutasiPembayaran->no_cek }}">
                            </div>

                            <!-- Kurs -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kurs</label>
                                <input type="text" id="edit_kurs_display" class="form-control"
                                    value="{{ $mutasiPembayaran->kurs ? number_format($mutasiPembayaran->kurs, 2, ',', '.') : '' }}"
                                    placeholder="0,00">
                                <input type="hidden" id="edit_kurs_value" value="{{ $mutasiPembayaran->kurs ?? 0 }}">
                            </div>

                            <!-- Memo -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Memo</label>
                                <textarea id="edit_memo" class="form-control" rows="3">{{ $mutasiPembayaran->memo }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn text-white" id="btnSaveHeader" style="background:#16a34a;">
                                <i class="fas fa-save me-1"></i> Update Header
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ==================== VOUCHERS CARD ==================== -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #dcfce7">
                        <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>Payment Vouchers
                            <span class="badge bg-secondary ms-2"
                                id="voucherCount">{{ $mutasiPembayaran->vouchers->count() }}</span>
                        </h6>
                        <button type="button" class="btn btn-light btn-sm" id="btnShowAddVoucher">
                            <i class="fas fa-plus me-1"></i> Add Voucher
                        </button>
                    </div>
                    <div class="card-body p-4">

                        <!-- ADD VOUCHER FORM -->
                        <div class="add-form-section mb-4" id="addVoucherSection" style="display:none;">
                            <h6 id="voucherFormTitle">
                                <i class="fas fa-plus-square" style="color:#16a34a;"></i> Add New Voucher
                            </h6>
                            <input type="hidden" id="editing_voucher_id" value="">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Voucher No.</label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background:#16a34a; color:white;">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="text" id="input_nomor_voucher" class="form-control fw-bold"
                                            placeholder="Auto-generated..." readonly
                                            style="background-color:#f0fdf4; color:#14532d; letter-spacing:1px;">
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Urutan</label>
                                    <input type="number" id="input_urutan" class="form-control" min="1"
                                        value="{{ $mutasiPembayaran->vouchers->count() + 1 }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Notes</label>
                                    <input type="text" id="input_voucher_keterangan" class="form-control"
                                        placeholder="Voucher notes...">
                                </div>
                                {{-- NEW: Cash Out Date --}}
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Cash Out Date</label>
                                    <input type="date" id="input_tgl_keluar" class="form-control">
                                </div>
                                {{-- NEW: Bank / Cash COA --}}
                                <div class="col-md-9 mb-3">
                                    <label class="form-label">Bank / Cash</label>
                                    <select id="input_voucher_coa" class="form-select"
                                        data-placeholder="Select Bank / Cash account...">
                                        <option value=""></option>
                                        @foreach ($chartOfAccounts as $coa)
                                            <option value="{{ $coa->id_md_chart_of_account }}">
                                                {{ $coa->no_account }} —
                                                {{ $coa->account_name ?? ($coa->nama_account ?? '') }}
                                            </option>
                                        @endforeach
                                    </select>
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

                        <!-- VOUCHER ACCORDION LIST -->
                        <div id="voucherAccordion">
                            @forelse($mutasiPembayaran->vouchers->sortBy('urutan') as $voucher)
                                @include('data.mutasi-pembayaran._voucher_panel', [
                                    'voucher' => $voucher,
                                    'joTypes' => $joTypes ?? [],
                                    'chartOfAccounts' => $chartOfAccounts,
                                ])
                            @empty
                                <div id="noVoucherMessage" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-4x mb-3 d-block"></i>
                                    <p class="fw-bold">Belum ada voucher</p>
                                    <small>Click "Add Voucher" to get started</small>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- FINAL BACK SECTION -->
    <div class="final-save-section">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('mutasi-pembayaran.index') }}" class="btn btn-final-back">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- MODAL: Delete Detail Blocked --}}
    <div class="modal fade" id="detailBlockedModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; border:none;">
                <div class="modal-header"
                    style="background:linear-gradient(135deg,#f59e0b,#d97706); border-radius:12px 12px 0 0; border:none;">
                    <h5 class="modal-title text-white fw-bold">
                        <i class="fas fa-ban me-2"></i>Tidak Dapat Dihapus
                    </h5>
                </div>
                <div class="modal-body p-4" id="detailBlockedBody"></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: Confirm Delete Detail --}}
    <div class="modal fade" id="detailConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; border:none;">
                <div class="modal-header"
                    style="background:linear-gradient(135deg,#ef4444,#dc2626); border-radius:12px 12px 0 0; border:none;">
                    <h5 class="modal-title text-white fw-bold">
                        <i class="fas fa-trash-alt me-2"></i>Hapus Detail
                    </h5>
                </div>
                <div class="modal-body p-4" id="detailConfirmBody">
                    <p>Delete this detail?</p>
                </div>
                <div class="modal-footer border-0 gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-fill"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger flex-fill fw-bold" id="btnConfirmDeleteDetail">
                        <i class="fas fa-trash me-1"></i> Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKasbonInfo" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <div class="modal-title">
                            <i class="fas fa-book-open me-2"></i>
                            Cash Advance Detail — <span id="ki-nomor-title"></span>
                        </div>
                        <div class="modal-subtitle" id="ki-item-count"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-black btn-sm" data-bs-dismiss="modal"></button>
                </div>

                <div class="ki-info-strip" id="ki-info-strip">
                    <div class="ki-info-cell">
                        <div class="ki-info-lbl"><i class="fas fa-user me-1"></i>Release</div>
                        <div class="ki-info-val" id="ki-release">—</div>
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
                    <div class="ki-info-cell">
                        <div class="ki-info-lbl"><i class="fas fa-paper-plane me-1"></i>Tgl Release</div>
                        <div class="ki-info-val" id="ki-tgl-release">—</div>
                    </div>
                </div>
                <div id="ki-keterangan-wrap" style="padding:14px 20px;display:none;">
                    <div
                        style="background:#fefce8;border:1px solid #fde047;border-radius:8px;padding:12px 16px;font-size:.84rem;line-height:1.5;">
                        <span style="color:#854d0e;font-weight:700;"><i class="fas fa-comment-alt me-2"></i>Notes:</span>
                        <span id="ki-keterangan" style="color:#1e293b;margin-left:4px;"></span>
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="ki-table">
                            <thead>
                                <tr>
                                    <th style="width:44px;text-align:center;">No</th>
                                    <th style="width:130px;">Category</th>
                                    <th>Description</th>
                                    <th class="num" style="width:155px;">CA Amount</th>
                                    <th class="num" style="width:165px;">LPJ Amount</th>
                                    <th class="num" style="width:145px;">Balance</th>
                                </tr>
                            </thead>
                            <tbody id="ki-tbody">
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">—</td>
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
        // GLOBAL VARS
        // ============================================================
        const mutasiId = {{ $mutasiPembayaran->id }};
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const ROUTES = {
            headerUpdate: '/data/mutasi-pembayaran/header/update/' + mutasiId,
            voucherStore: '/data/mutasi-pembayaran/voucher/store',
            voucherUpdate: '/data/mutasi-pembayaran/voucher/update/',
            voucherDestroy: '/data/mutasi-pembayaran/voucher/destroy/',
            detailStore: '/data/mutasi-pembayaran/detail/store',
            detailShow: '/data/mutasi-pembayaran/detail/show/',
            detailUpdate: '/data/mutasi-pembayaran/detail/update/',
            detailDestroy: '/data/mutasi-pembayaran/detail/destroy/',
            joOptions: '/data/mutasi-pembayaran/jo-options',
            kasbonOptions: '/data/mutasi-pembayaran/kasbon-options',
            kasbonInfo: '/data/mutasi-pembayaran/kasbon-info',
            joKasbonSummary: '/data/mutasi-pembayaran/jo-kasbon-summary',
            voucherDetails: '/data/mutasi-pembayaran/voucher/',
            nextVoucherNo: '/data/mutasi-pembayaran/next-voucher-no',
        };

        // ============================================================
        // FLOATING BADGE ALERT
        // ============================================================
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
            if (type === 'success' || type === 'error') setTimeout(hideFloatingAlert, 3000);
        }

        function hideFloatingAlert() {
            const alert = $('#floatingBadgeAlert');
            alert.addClass('hiding');
            setTimeout(() => alert.removeClass('show hiding'), 400);
        }

        // ============================================================
        // RUPIAH FORMAT
        // ============================================================
        function formatRupiah(value) {
            let number = String(value).replace(/[^\d,]/g, '').replace(/\./g, '');
            if (!number) return '';
            let [int, dec] = number.split(',');
            if (dec !== undefined && dec.length > 2) dec = dec.substring(0, 2);
            int = (int || '0').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return (dec !== undefined) ? int + ',' + dec : int + ',00';
        }

        function parseRupiah(value) {
            if (!value) return 0;
            return parseFloat(String(value).replace(/\./g, '').replace(',', '.')) || 0;
        }

        function formatNumber(amount) {
            return Number(amount).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function setupRupiahInput(el) {
            if (!el) return;
            el.addEventListener('input', function() {
                this.value = formatRupiah(this.value);
            });
            el.addEventListener('blur', function() {
                if (this.value && !this.value.includes(',')) this.value += ',00';
            });
        }

        // ============================================================
        // SELECT2 INIT
        // ============================================================
        function initSelect2(selector, parent, placeholder) {
            $(selector).select2({
                theme: 'bootstrap-5',
                dropdownParent: parent ? $(parent) : undefined,
                placeholder: placeholder || '-- Pilih --',
                allowClear: true,
                width: '100%',
            });
        }

        $(document).ready(function() {
            initSelect2('.select2-header', null, null);
            setupRupiahInput(document.getElementById('edit_kurs_display'));
            $('#edit_kurs_display').on('input', function() {
                $('#edit_kurs_value').val(parseRupiah($(this).val()));
            });
            // Select2 — Add Voucher form: Bank/Cash COA
            $('#input_voucher_coa').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select Bank / Cash account...',
                allowClear: true,
                width: '100%',
            });
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
            showFloatingAlert('saving', 'Menyimpan header...');
            $.ajax({
                url: ROUTES.headerUpdate,
                method: 'PUT',
                data: {
                    tanggal,
                    no_cek: $('#edit_no_cek').val(),
                    kurs: parseRupiah($('#edit_kurs_display').val()),
                    memo: $('#edit_memo').val(),
                    _token: CSRF
                },
                success: r => r.success ? showFloatingAlert('success', 'Header updated successfully!') :
                    showFloatingAlert('error', r.message || 'Gagal'),
                error: xhr => {
                    const msg = xhr.responseJSON?.errors ?
                        Object.values(xhr.responseJSON.errors).flat().join(', ') :
                        (xhr.responseJSON?.message || 'Gagal');
                    showFloatingAlert('error', msg);
                }
            });
        });

        // ============================================================
        // VOUCHER — ADD / EDIT / DELETE
        // ============================================================
        $('#btnShowAddVoucher').on('click', function() {
            $('#addVoucherSection').slideDown(200);
            clearVoucherForm();
            $('#noVoucherMessage').hide();

            // Auto-generate nomor voucher langsung saat form dibuka
            const $nomorInput = $('#input_nomor_voucher');
            $nomorInput.val('Generating...').css('opacity', 0.5);
            fetch(ROUTES.nextVoucherNo)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        $nomorInput.val(data.nomor).css('opacity', 1);
                    } else {
                        $nomorInput.val('').css('opacity', 1);
                    }
                })
                .catch(() => $nomorInput.val('').css('opacity', 1));
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
            $('#input_nomor_voucher, #input_voucher_keterangan').val('');
            $('#input_urutan').val($('.voucher-accordion-item').length + 1);
            // NEW fields
            $('#input_tgl_keluar').val('');
            $('#input_voucher_coa').val('').trigger('change.select2');
        }
        $('#btnSaveVoucher').on('click', function() {
            const editingId = $('#editing_voucher_id').val();
            const nomorVoucher = $('#input_nomor_voucher').val().trim();
            const urutan = $('#input_urutan').val();
            const keterangan = $('#input_voucher_keterangan').val().trim();
            const tglKeluar = $('#input_tgl_keluar').val(); // NEW
            const coaId = $('#input_voucher_coa').val(); // NEW
            if (!nomorVoucher) {
                showFloatingAlert('error', 'Voucher number is required');
                return;
            }
            editingId
                ?
                updateVoucher(editingId, nomorVoucher, urutan, keterangan, tglKeluar, coaId) :
                storeVoucher(nomorVoucher, urutan, keterangan, tglKeluar, coaId);
        });

        function storeVoucher(nomorVoucher, urutan, keterangan, tglKeluar, coaId) {
            showFloatingAlert('saving', 'Menyimpan voucher...');
            $.ajax({
                url: ROUTES.voucherStore,
                method: 'POST',
                data: {
                    id_mutasi_pembayaran: mutasiId,
                    nomor_voucher: nomorVoucher,
                    urutan,
                    keterangan,
                    tgl_keluar: tglKeluar || null,
                    id_md_chart_of_account: coaId || null,
                    _token: CSRF
                },
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', r.message || 'Gagal');
                        return;
                    }
                    showFloatingAlert('success', 'Voucher added: ' + r.data.nomor_voucher);
                    $('#noVoucherMessage').hide();
                    appendVoucherPanel(r.data);
                    $('#addVoucherSection').slideUp(200);
                    clearVoucherForm();
                    updateVoucherCount(1);
                },
                error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Gagal menyimpan voucher')
            });
        }

        function updateVoucher(id, nomorVoucher, urutan, keterangan, tglKeluar, coaId) {
            console.log('coaId raw:', coaId, typeof coaId);
            showFloatingAlert('saving', 'Memperbarui voucher...');
            $.ajax({
                url: ROUTES.voucherUpdate + id,
                method: 'PUT',
                data: {
                    id_mutasi_pembayaran: mutasiId,
                    nomor_voucher: nomorVoucher,
                    urutan,
                    keterangan,
                    tgl_keluar: tglKeluar || null,
                    id_md_chart_of_account: coaId || null,
                    _token: CSRF
                },
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', r.message || 'Gagal');
                        return;
                    }
                    showFloatingAlert('success', 'Voucher updated successfully!');
                    const panel = $(`#voucher-panel-${id}`);
                    panel.find('.voucher-nomor-text').text(nomorVoucher);
                    panel.find('.voucher-ket-text').text(keterangan ? `— ${keterangan}` : '');
                    panel.find('.voucher-number-badge').text('#' + urutan);
                    // NEW: update data attributes + info pills
                    panel.attr('data-tgl-keluar', tglKeluar || '');
                    panel.attr('data-coa-id', coaId || '');
                    const fmtTgl = tglKeluar ? formatVoucherDate(tglKeluar) : null;
                    const coaText = coaId ?
                        ($('#input_voucher_coa option:selected').text() || '—') :
                        null;
                    panel.find('.voucher-tgl-text').html(
                        fmtTgl ?
                        `<i class="fas fa-calendar-alt me-1"></i>${fmtTgl}` :
                        `<i class="fas fa-calendar-alt me-1"></i><em>No date</em>`
                    );
                    panel.find('.voucher-coa-text').html(
                        coaText ?
                        `<i class="fas fa-university me-1"></i>${coaText}` :
                        `<i class="fas fa-university me-1"></i><em>No bank/cash</em>`
                    );
                    $('#addVoucherSection').slideUp(200);
                    clearVoucherForm();
                },
                error: function(xhr) {
                    let msg = 'Gagal memperbarui voucher';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        else if (xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }
                    }
                    showFloatingAlert('error', msg);
                    console.error('Update voucher error:', xhr.status, xhr.responseJSON);
                }
            });
        }

        function editVoucher(id) {
            const panel = $(`#voucher-panel-${id}`);
            $('#editing_voucher_id').val(id);
            $('#input_nomor_voucher').val(panel.find('.voucher-nomor-text').text());
            $('#input_voucher_keterangan').val(panel.find('.voucher-ket-text').text().replace('— ', ''));
            $('#input_urutan').val(panel.find('.voucher-number-badge').text().replace('#', ''));
            // NEW: read tgl_keluar and coa id from data attributes
            $('#input_tgl_keluar').val(panel.data('tgl-keluar') || '');
            const coaId = panel.data('coa-id') || '';
            $('#input_voucher_coa').val(coaId).trigger('change.select2');
            $('#voucherFormTitle').html('<i class="fas fa-edit" style="color:#3b82f6;"></i> Edit Voucher');
            $('#addVoucherSection').addClass('edit-mode').slideDown(200);
            $('#btnSaveVoucher').html('<i class="fas fa-save me-1"></i> Update Voucher');
            $('html, body').animate({
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
                cancelButtonText: 'Cancel',
            }).then(result => {
                if (!result.isConfirmed) return;
                showFloatingAlert('saving', 'Menghapus voucher...');
                $.ajax({
                    url: ROUTES.voucherDestroy + id,
                    method: 'DELETE',
                    data: {
                        _token: CSRF
                    },
                    success: function(r) {
                        if (r.success) {
                            showFloatingAlert('success', 'Voucher deleted successfully!');
                            $(`#voucher-panel-${id}`).fadeOut(300, function() {
                                $(this).remove();
                                updateVoucherCount(-1);
                                if ($('.voucher-accordion-item').length === 0) $(
                                    '#noVoucherMessage').show();
                            });
                        } else {
                            showFloatingAlert('error', r.message || 'Gagal menghapus voucher');
                        }
                    },
                    error: xhr => showFloatingAlert('error', xhr.responseJSON?.message ||
                        'Gagal menghapus voucher')
                });
            });
        }

        function updateVoucherCount(delta) {
            const badge = $('#voucherCount');
            badge.text(parseInt(badge.text() || 0) + delta);
        }

        function appendVoucherPanel(voucher) {
            const html = buildVoucherPanelHtml(voucher);
            $('#voucherAccordion').append(html);
            initVoucherPanelSelects(voucher.id);
        }

        // Helper: format YYYY-MM-DD → "dd-Mon-YYYY" for display in voucher header pill
        function formatVoucherDate(ymd) {
            if (!ymd) return null;
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const d = new Date(ymd);
            if (isNaN(d)) return ymd;
            return String(d.getDate()).padStart(2, '0') + '-' + months[d.getMonth()] + '-' + d.getFullYear();
        }

        function buildVoucherPanelHtml(v) {
            const fmtTgl = v.tgl_keluar ? formatVoucherDate(v.tgl_keluar) : null;
            const coaText = v.coa_display || null; // filled by controller JSON response (see note)
            const tglPill = fmtTgl ?
                `<span style="background:#d1fae5;color:#065f46;border-radius:20px;padding:1px 8px;font-weight:600;font-size:0.75rem;"><i class="fas fa-calendar-alt me-1"></i>${fmtTgl}</span>` :
                `<span class="voucher-tgl-text" style="background:#f1f5f9;color:#94a3b8;border-radius:20px;padding:1px 8px;font-size:0.75rem;"><i class="fas fa-calendar-alt me-1"></i><em>No date</em></span>`;
            const coaPill = coaText ?
                `<span class="voucher-coa-text" style="background:#dbeafe;color:#1e40af;border-radius:20px;padding:1px 8px;font-weight:600;font-size:0.75rem;"><i class="fas fa-university me-1"></i>${coaText}</span>` :
                `<span class="voucher-coa-text" style="background:#f1f5f9;color:#94a3b8;border-radius:20px;padding:1px 8px;font-size:0.75rem;"><i class="fas fa-university me-1"></i><em>No bank/cash</em></span>`;

            return `
    <div class="voucher-accordion-item" id="voucher-panel-${v.id}"
         data-voucher-id="${v.id}"
         data-tgl-keluar="${v.tgl_keluar || ''}"
         data-coa-id="${v.id_md_chart_of_account || ''}">
        <div class="voucher-accordion-header" onclick="toggleVoucherPanel(${v.id})">
            <div class="voucher-title" style="flex-direction:column;align-items:flex-start;gap:4px;">
                <div class="d-flex align-items-center gap-2">
                    <span class="voucher-number-badge">#${v.urutan}</span>
                    <strong class="voucher-nomor-text">${v.nomor_voucher}</strong>
                    <span class="text-muted small voucher-ket-text">${v.keterangan ? '— ' + v.keterangan : ''}</span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    ${tglPill}
                    ${coaPill}
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark detail-count-badge-${v.id}">0 detail</span>
                <a href="{{ url('data/mutasi-pembayaran/voucher') }}/${v.id}/pdf"
                   target="_blank"
                   class="btn btn-sm btn-success"
                   onclick="event.stopPropagation()"
                   title="Export PDF">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <button type="button" class="btn btn-sm btn-warning" onclick="event.stopPropagation(); editVoucher(${v.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="event.stopPropagation(); deleteVoucher(${v.id}, '${v.nomor_voucher}')">
                    <i class="fas fa-trash"></i>
                </button>
                <i class="fas fa-chevron-down toggle-icon-${v.id}"></i>
            </div>
        </div>
        <div class="voucher-accordion-body" id="voucher-body-${v.id}">
            ${buildDetailFormHtml(v.id)}
            ${buildDetailTableHtml(v.id, [])}
        </div>
    </div>`;
        }

        // ============================================================
        // DETAIL FORM HTML — JO → Kasbon Summary (auto-display)
        // ============================================================
        function buildDetailFormHtml(voucherId) {
            return `
    <div class="add-form-section mb-3" id="addDetailSection-${voucherId}">
        <h6 id="detailFormTitle-${voucherId}">
            <i class="fas fa-plus-square" style="color:#16a34a;"></i> Add Detail
        </h6>

        {{-- ── ADD MODE ── --}}
        <div id="detailAddMode-${voucherId}">
<div class="row g-3 mb-2">
    <div class="col-12" id="jenisCol-${voucherId}">
        <label class="form-label fw-semibold required-field">Type</label>
        <select id="input_jenis-${voucherId}" class="form-select"
            onchange="onJenisChange(${voucherId})">
            <option value="">-- Select Type --</option>
            <option value="tramper">Tramper</option>
            <option value="other">Other</option>
            <option value="contract">Contract</option>
            <option value="general">General</option>
        </select>
    </div>
    <div class="col-12" id="joCol-${voucherId}" style="display:none;">
                    <label class="form-label fw-semibold required-field">JO</label>
                    <select id="input_jo-${voucherId}" class="form-select select2-jo-${voucherId}"
                        data-placeholder="Select JO..."
                        onchange="onJoChange(${voucherId})">
                        <option value=""></option>
                    </select>
                </div>
            </div>

            {{-- Kasbon Summary Table (auto-tampil saat JO dipilih) --}}
            <div id="joKasbonSummaryWrap-${voucherId}" style="display:none;" class="mb-2">
                <div class="kasbon-summary-wrap">
                    <div class="kasbon-summary-header">
                        <span>
                            <i class="fas fa-list-alt me-2"></i>Cash Advances &amp; LPJ for this JO
                            <span class="badge bg-white text-success ms-2"
                                  id="kasbonSummaryCount-${voucherId}">0 cash adv.</span>
                        </span>
                        <button type="button" id="btnAddAll-${voucherId}"
                            class="btn btn-sm"
                            style="background:#fff;color:#16a34a;border:1px solid #16a34a;font-weight:600;display:none;"
                            onclick="addAllKasbon(${voucherId})">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Semua
                        </button>
                    </div>
                    <div class="table-responsive p-0">
                        <table class="table table-kasbon-summary mb-0">
                            <thead>
                                <tr>
                                    <th class="text-start">CA Number</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">PJ / LPJ</th>
                                    <th class="text-end">Balance</th>
                                    <th>Notes</th>
                                    <th width="90" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="kasbonSummaryBody-${voucherId}">
                                <tr><td colspan="6" class="text-center text-muted py-3">
                                    <i class="fas fa-arrow-up me-1"></i> Select JO to show cash advances
                                </td></tr>
                            </tbody>
                            <tfoot id="kasbonSummaryFoot-${voucherId}"></tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- General Section (hanya jenis=general) --}}
            <div id="generalSection-${voucherId}" style="display:none;">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold required-field">General Cash Advance</label>
                        <select id="input_kasbon_gen-${voucherId}"
                            class="form-select select2-kasbon-gen-${voucherId}"
                            data-placeholder="Select CA..."
                            onchange="onGeneralKasbonChange(${voucherId})">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Amount</label>
                        <input type="text" id="input_nilai_gen-${voucherId}" class="form-control"
                            readonly style="background:#e9ecef;" placeholder="0,00">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">PJ</label>
                        <input type="text" id="input_pj_gen-${voucherId}" class="form-control"
                            readonly style="background:#e9ecef;" placeholder="0,00">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Balance</label>
                        <input type="text" id="input_selisih_gen-${voucherId}" class="form-control"
                            readonly style="background:#e9ecef;" placeholder="0,00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Notes</label>
                        <input type="text" id="input_ket_gen-${voucherId}" class="form-control"
                            placeholder="Notes...">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-add-purple w-100"
                            onclick="saveGeneralDetail(${voucherId})">
                            <i class="fas fa-plus me-1"></i> Tambah ke Tabel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── EDIT MODE (hanya keterangan yang bisa diubah) ── --}}
        <div id="detailEditMode-${voucherId}" style="display:none;" class="detail-edit-mode-box">
            <input type="hidden" id="editing_detail_id-${voucherId}" value="">
            <div class="edit-info-grid mb-3">
                <div class="edit-info-card">
                    <div class="edit-info-lbl">Type</div>
                    <div class="edit-info-val"><span id="edit_jenis_badge-${voucherId}" class="jenis-badge"></span></div>
                </div>
                <div class="edit-info-card">
                    <div class="edit-info-lbl">JO</div>
                    <div class="edit-info-val" id="edit_jo_nomor-${voucherId}">—</div>
                </div>
                <div class="edit-info-card">
                    <div class="edit-info-lbl">CA No.</div>
                    <div class="edit-info-val" id="edit_kasbon_nomor-${voucherId}">—</div>
                </div>
                <div class="edit-info-card">
                    <div class="edit-info-lbl">CA Amount (IDR)</div>
                    <div class="edit-info-val clr-nilai" id="edit_nilai_display-${voucherId}">—</div>
                </div>
                <div class="edit-info-card">
                    <div class="edit-info-lbl">LPJ Amount (IDR)</div>
                    <div class="edit-info-val clr-pj" id="edit_pj_display-${voucherId}">—</div>
                </div>
                <div class="edit-info-card">
                    <div class="edit-info-lbl">Balance (IDR)</div>
                    <div class="edit-info-val" id="edit_selisih_display-${voucherId}">—</div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Notes</label>
                <input type="text" id="edit_keterangan-${voucherId}"
                    class="form-control" placeholder="Write notes...">
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-cancel-edit-style"
                    onclick="cancelDetailEdit(${voucherId})">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-add-purple"
                    onclick="submitDetailEdit(${voucherId})">
                    <i class="fas fa-save me-1"></i> Update
                </button>
            </div>
        </div>
    </div>`;
        }

        // ============================================================
        // DETAIL — JENIS / JO CASCADING
        // ============================================================
        function onJenisChange(voucherId) {
            const jenis = $(`#input_jenis-${voucherId}`).val();
            const $jenisCol = $(`#jenisCol-${voucherId}`);
            const $joCol = $(`#joCol-${voucherId}`);
            const $genKasbonCol = $(`#genKasbonCol-${voucherId}`);

            // Reset semua
            $(`#input_jo-${voucherId}`).val('').trigger('change.select2');
            $(`#input_kasbon_gen_sel-${voucherId}`).val('').trigger('change.select2');
            $(`#joKasbonSummaryWrap-${voucherId}`).hide();
            $(`#kasbonSummaryBody-${voucherId}`).html(
                `<tr><td colspan="6" class="text-center text-muted py-3">
            <i class="fas fa-arrow-up me-1"></i> Select type to show cash advances
        </td></tr>`
            );
            $(`#btnAddAll-${voucherId}`).hide();

            if (!jenis) {
                $jenisCol.removeClass('col-md-3').addClass('col-12');
                $joCol.hide();
                $genKasbonCol.hide();
                return;
            }

            $jenisCol.removeClass('col-12').addClass('col-md-3');

            if (jenis === 'general') {
                $joCol.hide();
                $genKasbonCol.removeClass('col-12').addClass('col-md-9').show();

                // Init select2 jika belum
                if (!$(`#input_kasbon_gen_sel-${voucherId}`).hasClass('select2-hidden-accessible')) {
                    $(`#input_kasbon_gen_sel-${voucherId}`).select2({
                        placeholder: 'Pilih Kasbon General...',
                        allowClear: true,
                        width: '100%',
                    });
                }

                // Load kasbon general options
                const $sel = $(`#input_kasbon_gen_sel-${voucherId}`);
                $sel.html('<option value=""></option>');
                $.ajax({
                    url: ROUTES.kasbonOptions,
                    method: 'GET',
                    data: {
                        jenis: 'general'
                    },
                    success: function(r) {
                        let opts = '<option value=""></option>';
                        if (r.success && r.data) {
                            r.data.forEach(k => {
                                opts +=
                                    `<option value="${k.id}">${k.nomor}${k.label ? ' — ' + k.label : ''}</option>`;
                            });
                        }
                        $sel.html(opts).trigger('change.select2');
                    }
                });
                return;
            }

            // tramper / other / contract
            $genKasbonCol.hide();
            $joCol.removeClass('col-12').addClass('col-md-9').show();

            const $joSelect = $(`#input_jo-${voucherId}`);
            $joSelect.html('<option value=""></option>');
            $.ajax({
                url: ROUTES.joOptions,
                method: 'GET',
                data: {
                    jenis
                },
                success: function(r) {
                    let opts = '<option value=""></option>';
                    if (r.success && r.data) {
                        r.data.forEach(jo => {
                            opts +=
                                `<option value="${jo.id}">${jo.nomor}${jo.label ? ' — ' + jo.label : ''}</option>`;
                        });
                    }
                    $joSelect.html(opts).trigger('change.select2');
                }
            });
        }

        function loadGeneralKasbonSummary(voucherId) {
            $(`#joKasbonSummaryWrap-${voucherId}`).show();
            $(`#kasbonSummaryBody-${voucherId}`).html(
                `<tr><td colspan="6" class="text-center text-muted py-3">
            <i class="fas fa-circle-notch fa-spin text-primary me-1"></i>
            Memuat data kasbon general...
        </td></tr>`
            );

            $.ajax({
                url: ROUTES.joKasbonSummary,
                method: 'GET',
                data: {
                    jenis: 'general'
                },
                success: function(r) {
                    if (!r.success) {
                        $(`#kasbonSummaryBody-${voucherId}`).html(
                            `<tr><td colspan="6" class="text-center text-danger py-3">
                        <i class="fas fa-times-circle me-1"></i> Gagal memuat data
                    </td></tr>`
                        );
                        return;
                    }

                    const kasbons = r.data || [];
                    $(`#kasbonSummaryCount-${voucherId}`).text(kasbons.length + ' kasbon');

                    window._kasbonData = window._kasbonData || {};
                    window._kasbonData[voucherId] = kasbons;

                    if (kasbons.length === 0) {
                        $(`#kasbonSummaryBody-${voucherId}`).html(
                            `<tr><td colspan="6" class="text-center text-muted py-3">
                        <i class="fas fa-inbox me-1"></i> Tidak ada kasbon general
                    </td></tr>`
                        );
                        return;
                    }

                    let rows = '';
                    let totNilai = 0,
                        totPj = 0,
                        totSelisih = 0;

                    kasbons.forEach((k, ki) => {
                        const dipakai = k.sudah_dipakai;
                        const trClass = dipakai ? 'sudah-dipakai' : '';
                        const selClass = k.selisih > 0.001 ? 'clr-pos' : k.selisih < -0.001 ?
                            'clr-neg' : 'clr-zero';
                        const itemCount = (k.items || []).length;

                        totNilai += parseFloat(k.nilai || 0);
                        totPj += parseFloat(k.pj || 0);
                        totSelisih += parseFloat(k.selisih || 0);

                        window._kasbonMap = window._kasbonMap || {};
                        window._kasbonMap[`${voucherId}-gen-${ki}`] = k;

                        rows += `
                <tr class="kasbon-parent-row ${trClass}"
                    data-voucher="${voucherId}" data-ki="gen-${ki}">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button"
                                class="btn-toggle-items"
                                data-voucher="${voucherId}" data-ki="gen-${ki}"
                                onclick="toggleKasbonItems(this)"
                                title="Lihat rincian item"
                                ${itemCount === 0 ? 'disabled style="opacity:.3"' : ''}>
                                <i class="fas fa-chevron-right toggle-chev"></i>
                            </button>
                            <button type="button"
                                class="btn-kasbon-info"
                                onclick="showKasbonInfo(this)"
                                title="Info kasbon"
                                data-nomor="${escAttr(k.nomor)}"
                                data-rel="${escAttr(k.release||'')}"
                                data-dep="${escAttr(k.departemen||'')}"
                                data-cab="${escAttr(k.cabang||'')}"
                                data-tgl="${escAttr(k.tgl_kasbon||'')}"
                                data-tgl-rel="${escAttr(k.tgl_release||'')}"
                                data-items="${escAttr(JSON.stringify(k.items||[]))}">
                                <i class="fas fa-info"></i>
                            </button>
                            <span class="fw-semibold" style="font-size:0.82rem;">
                                ${escHtml(k.nomor)}
                            </span>
                        </div>
                    </td>
                    <td class="num-cell clr-nilai">${formatNumber(k.nilai)}</td>
                    <td class="num-cell clr-pj">${formatNumber(k.pj)}</td>
                    <td class="num-cell ${selClass}">${formatNumber(k.selisih)}</td>
                    <td>
                        ${!dipakai
                            ? `<input type="text"
                                                                                                                                                            class="form-control form-control-sm kasbon-ket-input"
                                                                                                                                                            data-voucher="${voucherId}" data-ki="gen-${ki}"
                                                                                                                                                            placeholder="Notes..." style="font-size:0.78rem;">`
                            : '<span class="clr-muted">—</span>'
                        }
                    </td>
                    <td class="text-center">
                        ${!dipakai
                            ? `<button type="button"
                                                                                                                                                            class="btn-tambah-kasbon kasbon-add-btn"
                                                                                                                                                            data-voucher="${voucherId}"
                                                                                                                                                            data-ki="gen-${ki}"
                                                                                                                                                            data-kasbon-id="${escAttr(String(k.id_kasbon))}"
                                                                                                                                                            data-jenis="general"
                                                                                                                                                            data-jo-id=""
                                                                                                                                                            onclick="addKasbonFromSummary(this)">
                                                                                                                                                            <i class="fas fa-plus me-1"></i>Add
                                                                                                                                                           </button>`
                            : `<button class="btn btn-sm btn-secondary" disabled>
                                                                                                                                                               <i class="fas fa-check"></i>
                                                                                                                                                           </button>`
                        }
                    </td>
                </tr>`;

                        (k.items || []).forEach((item, ii) => {
                            const iSel = item.selisih > 0.001 ? 'clr-pos' : item.selisih < -
                                0.001 ? 'clr-neg' : 'clr-zero';
                            rows += `
                    <tr class="kasbon-item-row ${trClass}"
                        data-parent-voucher="${voucherId}"
                        data-parent-ki="gen-${ki}"
                        style="display:none;">
                        <td style="padding-left:56px; color:#64748b; font-size:0.78rem;">
                            ${escHtml(item.label || ('Item #' + (ii + 1)))}
                        </td>
                        <td class="num-cell clr-nilai" style="font-size:0.78rem;">${formatNumber(item.nilai_kasbon)}</td>
                        <td class="num-cell clr-pj"    style="font-size:0.78rem;">${formatNumber(item.pj)}</td>
                        <td class="num-cell ${iSel}"   style="font-size:0.78rem;">${formatNumber(item.selisih)}</td>
                        <td colspan="2"></td>
                    </tr>`;
                        });
                    });

                    $(`#kasbonSummaryBody-${voucherId}`).html(rows);

                    const canAddCount = kasbons.filter(k => !k.sudah_dipakai).length;
                    $(`#btnAddAll-${voucherId}`)
                        .text(`Add All (${canAddCount})`)
                        .prepend('<i class="fas fa-plus-circle me-1"></i> ')
                        .toggle(canAddCount > 0);

                    const totSel = totNilai - totPj;
                    const totSelClass = totSel > 0.001 ? 'clr-pos' : totSel < -0.001 ? 'clr-neg' : 'clr-zero';
                    $(`#kasbonSummaryFoot-${voucherId}`).html(`
                <tr>
                    <td class="fw-bold">Total</td>
                    <td class="num-cell clr-nilai">${formatNumber(totNilai)}</td>
                    <td class="num-cell clr-pj">${formatNumber(totPj)}</td>
                    <td class="num-cell ${totSelClass}">${formatNumber(totSelisih)}</td>
                    <td colspan="2"></td>
                </tr>
            `);
                },
                error: function() {
                    $(`#kasbonSummaryBody-${voucherId}`).html(
                        `<tr><td colspan="6" class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-triangle me-1"></i> Gagal memuat data
                </td></tr>`
                    );
                }
            });
        }

        function onJoChange(voucherId) {
            const jenis = $(`#input_jenis-${voucherId}`).val();
            const joId = $(`#input_jo-${voucherId}`).val();

            $(`#btnAddAll-${voucherId}`).hide();

            if (!joId) {
                $(`#joKasbonSummaryWrap-${voucherId}`).hide();
                return;
            }

            $(`#joKasbonSummaryWrap-${voucherId}`).show();
            $(`#kasbonSummaryBody-${voucherId}`).html(
                `<tr><td colspan="6" class="text-center text-muted py-3">
            <i class="fas fa-circle-notch fa-spin me-1 text-primary"></i> Memuat data kasbon &amp; LPJ...
        </td></tr>`
            );

            loadJoKasbonSummary(voucherId, jenis, joId);
        }

        function loadJoKasbonSummary(voucherId, jenis, joId) {
            $(`#joKasbonSummaryWrap-${voucherId}`).show();
            $(`#kasbonSummaryBody-${voucherId}`).html(
                `<tr><td colspan="6" class="text-center text-muted py-3">
            <i class="fas fa-circle-notch fa-spin text-primary me-1"></i>
            Memuat data kasbon &amp; LPJ...
         </td></tr>`
            );

            $.ajax({
                url: ROUTES.joKasbonSummary,
                method: 'GET',
                data: {
                    jenis,
                    jo_id: joId,
                    voucher_id: voucherId,
                },
                success: function(r) {
                    if (!r.success) {
                        $(`#kasbonSummaryBody-${voucherId}`).html(
                            `<tr><td colspan="6" class="text-center text-danger py-3">
                        <i class="fas fa-times-circle me-1"></i> Gagal memuat data
                     </td></tr>`
                        );
                        return;
                    }

                    const kasbons = r.data || [];
                    $(`#kasbonSummaryCount-${voucherId}`).text(kasbons.length + ' kasbon');

                    window._kasbonData = window._kasbonData || {};
                    window._kasbonData[voucherId] = kasbons;

                    if (kasbons.length === 0) {
                        $(`#kasbonSummaryBody-${voucherId}`).html(
                            `<tr><td colspan="6" class="text-center text-muted py-3">
                        <i class="fas fa-inbox me-1"></i> Tidak ada kasbon untuk JO ini
                     </td></tr>`
                        );
                        return;
                    }

                    let rows = '';
                    let totNilai = 0,
                        totPj = 0,
                        totSelisih = 0;

                    kasbons.forEach((k, ki) => {
                        const dipakai = k.sudah_dipakai;
                        const trClass = dipakai ? 'sudah-dipakai' : '';
                        const selClass = k.selisih > 0.001 ? 'clr-pos' :
                            k.selisih < -0.001 ? 'clr-neg' :
                            'clr-zero';

                        totNilai += parseFloat(k.nilai || 0);
                        totPj += parseFloat(k.pj || 0);
                        totSelisih += parseFloat(k.selisih || 0);

                        const itemCount = (k.items || []).length;

                        window._kasbonMap = window._kasbonMap || {};
                        window._kasbonMap[`${voucherId}-${ki}`] = k;

                        // ── PARENT ROW ──
                        rows += `
                <tr class="kasbon-parent-row ${trClass}"
                    data-voucher="${voucherId}" data-ki="${ki}">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            {{-- Toggle items --}}
                            <button type="button"
                                class="btn-toggle-items"
                                data-voucher="${voucherId}" data-ki="${ki}"
                                onclick="toggleKasbonItems(this)"
                                title="Lihat rincian item"
                                ${itemCount === 0 ? 'disabled style="opacity:.3"' : ''}>
                                <i class="fas fa-chevron-right toggle-chev"></i>
                            </button>
                            {{-- Info modal --}}
                         <button type="button"
    class="btn-kasbon-info"
    onclick="showKasbonInfo(this)"
    title="Info kasbon"
    data-nomor="${escAttr(k.nomor)}"
    data-rel="${escAttr(k.release||'')}"
    data-dep="${escAttr(k.departemen||'')}"
    data-cab="${escAttr(k.cabang||'')}"
    data-tgl="${escAttr(k.tgl_kasbon||'')}"
    data-tgl-rel="${escAttr(k.tgl_release||'')}"
    data-items="${escAttr(JSON.stringify(k.items||[]))}">
    <i class="fas fa-info"></i>
</button>
                            <span class="fw-semibold" style="font-size:0.82rem;">
                                ${escHtml(k.nomor)}
                            </span>
                            ${dipakai
                                ? `<span class="badge"
                                                                                                                                                                                                                                                                                                                                                                                                                                style="background:#fef3c7;color:#92400e;
                                                                                                                                                                                                                                                                                                                                                                                                                                       font-size:0.65rem;padding:2px 7px;border-radius:20px;">                                                                                                                                                                    </span>`
                                : itemCount > 0
                                    ? `<span class="badge"
                                                                                                                                                                                                                                                                                                                                                                                                                                    style="background:#dbeafe;color:#1e40af;
                                                                                                                                                                                                                                                                                                                                                                                                                                           font-size:0.65rem;padding:2px 7px;border-radius:20px;">                                                                                                                                                                        </span>`
                                    : ''
                            }
                        </div>
                    </td>
                    <td class="num-cell clr-nilai">${formatNumber(k.nilai)}</td>
                    <td class="num-cell clr-pj">${formatNumber(k.pj)}</td>
                    <td class="num-cell ${selClass}">${formatNumber(k.selisih)}</td>
                    <td>
                        ${!dipakai
                            ? `<input type="text"
                                                                                                                                                                                                                                                                                                                                                                                                                            class="form-control form-control-sm kasbon-ket-input"
                                                                                                                                                                                                                                                                                                                                                                                                                            data-voucher="${voucherId}" data-ki="${ki}"
                                                                                                                                                                                                                                                                                                                                                                                                                            placeholder="Notes..."
                                                                                                                                                                                                                                                                                                                                                                                                                            style="font-size:0.78rem;">`
                            : '<span class="clr-muted">—</span>'
                        }
                    </td>
                    <td class="text-center">
                        ${!dipakai
                            ? `<button type="button"
                                                                                                                                                                                                                                                                                                                                                                                                                            class="btn-tambah-kasbon kasbon-add-btn"
                                                                                                                                                                                                                                                                                                                                                                                                                            data-voucher="${voucherId}"
                                                                                                                                                                                                                                                                                                                                                                                                                            data-ki="${ki}"
                                                                                                                                                                                                                                                                                                                                                                                                                            data-kasbon-id="${escAttr(String(k.id_kasbon))}"
                                                                                                                                                                                                                                                                                                                                                                                                                            data-jenis="${jenis}"
                                                                                                                                                                                                                                                                                                                                                                                                                            data-jo-id="${escAttr(String(joId))}"
                                                                                                                                                                                                                                                                                                                                                                                                                            onclick="addKasbonFromSummary(this)">
                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="fas fa-plus me-1"></i>Add
                                                                                                                                                                                                                                                                                                                                                                                                                           </button>`
                            : `<button class="btn btn-sm btn-secondary" disabled>
                                                                                                                                                                                                                                                                                                                                                                                                                               <i class="fas fa-check"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                           </button>`
                        }
                    </td>
                </tr>`;

                        // ── CHILD ROWS (items) — tersembunyi by default ──
                        (k.items || []).forEach((item, ii) => {
                            const iSel = item.selisih > 0.001 ? 'clr-pos' :
                                item.selisih < -0.001 ? 'clr-neg' :
                                'clr-zero';
                            rows += `
                    <tr class="kasbon-item-row ${trClass}"
                        data-parent-voucher="${voucherId}"
                        data-parent-ki="${ki}"
                        style="display:none;">
                        <td style="padding-left:56px; color:#64748b; font-size:0.78rem;">
                            ${escHtml(item.label || ('Item #' + (ii + 1)))}
                        </td>
                        <td class="num-cell clr-nilai" style="font-size:0.78rem;">
                            ${formatNumber(item.nilai_kasbon)}
                        </td>
                        <td class="num-cell clr-pj" style="font-size:0.78rem;">
                            ${formatNumber(item.pj)}
                        </td>
                        <td class="num-cell ${iSel}" style="font-size:0.78rem;">
                            ${formatNumber(item.selisih)}
                        </td>
                        <td colspan="2"></td>
                    </tr>`;
                        });
                    });

                    $(`#kasbonSummaryBody-${voucherId}`).html(rows);

                    const canAddCount = kasbons.filter(k => !k.sudah_dipakai).length;
                    $(`#btnAddAll-${voucherId}`)
                        .text(`Add All (${canAddCount})`)
                        .prepend('<i class="fas fa-plus-circle me-1"></i> ')
                        .toggle(canAddCount > 0);

                    // Footer total
                    const totSel = totNilai - totPj;
                    const totSelClass = totSel > 0.001 ? 'clr-pos' : totSel < -0.001 ? 'clr-neg' : 'clr-zero';
                    $(`#kasbonSummaryFoot-${voucherId}`).html(`
                <tr>
                    <td class="fw-bold">Total</td>
                    <td class="num-cell clr-nilai">${formatNumber(totNilai)}</td>
                    <td class="num-cell clr-pj">${formatNumber(totPj)}</td>
                    <td class="num-cell ${totSelClass}">${formatNumber(totSelisih)}</td>
                    <td colspan="2"></td>
                </tr>
            `);
                },
                error: function() {
                    $(`#kasbonSummaryBody-${voucherId}`).html(
                        `<tr><td colspan="6" class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-triangle me-1"></i> Gagal memuat data
                 </td></tr>`
                    );
                }
            });
        }

        // Toggle item rows — pakai data-parent-ki, TIDAK pakai string id_kasbon
        function toggleKasbonItems(btn) {
            const $btn = $(btn);
            const voucherId = $btn.data('voucher');
            const ki = $btn.data('ki');
            const $chev = $btn.find('.toggle-chev');

            const $items = $(`#kasbonSummaryBody-${voucherId}`)
                .find(`tr.kasbon-item-row[data-parent-voucher="${voucherId}"][data-parent-ki="${ki}"]`);

            if ($items.first().is(':visible')) {
                $items.slideUp(150);
                $chev.css('transform', 'rotate(0deg)');
            } else {
                $items.slideDown(150);
                $chev.css('transform', 'rotate(90deg)');
            }
        }

        // ============================================================
        // ADD KASBON — baca dari data-* attribute, TIDAK interpolasi ID
        // ============================================================
        function addKasbonFromSummary(btn) {
            const $btn = $(btn);
            const voucherId = $btn.data('voucher');
            const ki = $btn.data('ki');
            const kasbonId = $btn.data('kasbon-id'); // nilai asli, bebas karakter apapun
            const jenis = $btn.data('jenis');
            const joId = $btn.data('jo-id');

            // Ambil keterangan dari input di baris yang sama (cari by data-ki, bukan by id)
            const ketVal = $(`#kasbonSummaryBody-${voucherId}`)
                .find(`.kasbon-ket-input[data-voucher="${voucherId}"][data-ki="${ki}"]`)
                .val() || '';

            const payload = {
                id_mutasi_voucher: voucherId,
                jenis,
                keterangan: ketVal,
                _token: CSRF,
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
            if (jenis === 'general') {
                payload.id_kasbon_gen = kasbonId;
            }

            showFloatingAlert('saving', 'Menambahkan kasbon...');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: ROUTES.detailStore,
                method: 'POST',
                data: payload,
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', r.message || 'Gagal menambahkan');
                        $btn.prop('disabled', false).html('<i class="fas fa-plus me-1"></i>Tambah');
                        return;
                    }
                    showFloatingAlert('success', 'Cash advance added successfully!');

                    // Grey-out parent row
                    const $tbody = $(`#kasbonSummaryBody-${voucherId}`);
                    const $parentTr = $tbody.find(
                        `.kasbon-parent-row[data-voucher="${voucherId}"][data-ki="${ki}"]`);
                    $parentTr.addClass('sudah-dipakai');
                    $parentTr.find('.kasbon-ket-input').prop('disabled', true);

                    // Grey-out item rows
                    $tbody.find(`.kasbon-item-row[data-parent-voucher="${voucherId}"][data-parent-ki="${ki}"]`)
                        .addClass('sudah-dipakai');

                    // Ganti tombol
                    $btn.replaceWith(
                        `<button class="btn btn-sm btn-success" disabled>
                     <i class="fas fa-check me-1"></i>Added
                 </button>`
                    );

                    appendDetailRow(voucherId, r.data);
                    updateDetailTotals(voucherId);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.errors ?
                        Object.values(xhr.responseJSON.errors).flat().join(', ') :
                        (xhr.responseJSON?.message || 'Gagal menambahkan kasbon');
                    showFloatingAlert('error', msg);
                    $btn.prop('disabled', false).html('<i class="fas fa-plus me-1"></i>Tambah');
                }
            });
        }

        // ── Helper: escape HTML & attribute ──
        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function escAttr(str) {
            // Untuk data-* attribute value — hanya escape quote
            if (!str) return '';
            return String(str).replace(/"/g, '&quot;');
        }

        // ============================================================
        // GENERAL — kasbon dropdown (tidak ada JO)
        // ============================================================
        function loadGeneralKasbonOptions(voucherId) {
            $.ajax({
                url: ROUTES.kasbonOptions,
                method: 'GET',
                data: {
                    jenis: 'general'
                },
                success: function(r) {
                    let opts = '<option value=""></option>';
                    if (r.success && r.data) {
                        r.data.forEach(k => {
                            opts +=
                                `<option value="${k.id}">${k.nomor}${k.label ? ' — ' + k.label : ''}</option>`;
                        });
                    }
                    $(`#input_kasbon_gen-${voucherId}`).html(opts).trigger('change.select2');
                }
            });
        }

        function onGeneralKasbonChange(voucherId) {
            const kasbonId = $(`#input_kasbon_gen_sel-${voucherId}`).val();
            $(`#btnAddAll-${voucherId}`).hide();

            if (!kasbonId) {
                $(`#joKasbonSummaryWrap-${voucherId}`).hide();
                return;
            }

            $(`#joKasbonSummaryWrap-${voucherId}`).show();
            $(`#kasbonSummaryBody-${voucherId}`).html(
                `<tr><td colspan="6" class="text-center text-muted py-3">
                    <i class="fas fa-circle-notch fa-spin me-1 text-primary"></i> Memuat data kasbon general...
                </td></tr>`
            );

            $.ajax({
                url: ROUTES.joKasbonSummary,
                method: 'GET',
                data: {
                    jenis: 'general',
                    kasbon_id: kasbonId
                },
                success: function(r) {
                    if (!r.success || !r.data || !r.data.length) {
                        $(`#kasbonSummaryBody-${voucherId}`).html(
                            `<tr><td colspan="6" class="text-center text-danger py-3">
                                <i class="fas fa-times-circle me-1"></i> ${r.message || 'Gagal memuat data'}
                            </td></tr>`
                        );
                        return;
                    }

                    const kasbons = r.data;
                    $(`#kasbonSummaryCount-${voucherId}`).text(kasbons.length + ' kasbon');

                    let rows = '';
                    let totNilai = 0,
                        totPj = 0,
                        totSelisih = 0;

                    kasbons.forEach((k, ki) => {
                        const dipakai = k.sudah_dipakai;
                        const trClass = dipakai ? 'sudah-dipakai' : '';
                        const selClass = k.selisih > 0.001 ? 'clr-pos' : k.selisih < -0.001 ?
                            'clr-neg' : 'clr-zero';

                        totNilai += parseFloat(k.nilai || 0);
                        totPj += parseFloat(k.pj || 0);
                        totSelisih += parseFloat(k.selisih || 0);

                        const itemCount = (k.items || []).length;
                        window._kasbonMap = window._kasbonMap || {};
                        window._kasbonMap[`${voucherId}-${ki}`] = k;

                        rows += `
                        <tr class="kasbon-parent-row ${trClass}" data-voucher="${voucherId}" data-ki="${ki}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn-toggle-items"
                                        data-voucher="${voucherId}" data-ki="${ki}"
                                        onclick="toggleKasbonItems(this)" title="Lihat rincian item"
                                        ${itemCount === 0 ? 'disabled style="opacity:.3"' : ''}>
                                        <i class="fas fa-chevron-right toggle-chev"></i>
                                    </button>
                                    <button type="button" class="btn-kasbon-info" onclick="showKasbonInfo(this)"
                                        title="Info kasbon"
                                        data-nomor="${escAttr(k.nomor)}"
                                        data-rel="${escAttr(k.release||'')}"
                                        data-dep="${escAttr(k.departemen||'')}"
                                        data-cab="${escAttr(k.cabang||'')}"
                                        data-tgl="${escAttr(k.tgl_kasbon||'')}"
                                        data-tgl-rel="${escAttr(k.tgl_release||'')}"
                                        data-items="${escAttr(JSON.stringify(k.items||[]))}">
                                        <i class="fas fa-info"></i>
                                    </button>
                                    <span class="fw-semibold" style="font-size:0.82rem;">${escHtml(k.nomor)}</span>
                                </div>
                            </td>
                            <td class="num-cell clr-nilai">${formatNumber(k.nilai)}</td>
                            <td class="num-cell clr-pj">${formatNumber(k.pj)}</td>
                            <td class="num-cell ${selClass}">${formatNumber(k.selisih)}</td>
                            <td>
                                ${!dipakai
                                    ? `<input type="text" class="form-control form-control-sm kasbon-ket-input"
                                                                                                                                                      data-voucher="${voucherId}" data-ki="${ki}"
                                                                                                                                                      placeholder="Notes..." style="font-size:0.78rem;">`
                                    : '<span class="clr-muted">—</span>'}
                            </td>
                            <td class="text-center">
                                ${!dipakai
                                    ? `<button type="button" class="btn-tambah-kasbon kasbon-add-btn"
                                                                                                                                                      data-voucher="${voucherId}" data-ki="${ki}"
                                                                                                                                                      data-kasbon-id="${escAttr(String(k.id_kasbon))}"
                                                                                                                                                      data-jenis="general" data-jo-id=""
                                                                                                                                                      onclick="addKasbonFromSummary(this)">
                                                                                                                                                      <i class="fas fa-plus me-1"></i>Add
                                                                                                                                                   </button>`
                                    : '<button class="btn btn-sm btn-secondary" disabled><i class="fas fa-check"></i></button>'}
                            </td>
                        </tr>`;

                        (k.items || []).forEach((item, ii) => {
                            const iSel = item.selisih > 0.001 ? 'clr-pos' : item.selisih < -
                                0.001 ? 'clr-neg' : 'clr-zero';
                            rows += `
                            <tr class="kasbon-item-row ${trClass}"
                                data-parent-voucher="${voucherId}" data-parent-ki="${ki}" style="display:none;">
                                <td style="padding-left:56px; color:#64748b; font-size:0.78rem;">${escHtml(item.label || ('Item #' + (ii+1)))}</td>
                                <td class="num-cell clr-nilai" style="font-size:0.78rem;">${formatNumber(item.nilai_kasbon)}</td>
                                <td class="num-cell clr-pj" style="font-size:0.78rem;">${formatNumber(item.pj)}</td>
                                <td class="num-cell ${iSel}" style="font-size:0.78rem;">${formatNumber(item.selisih)}</td>
                                <td colspan="2"></td>
                            </tr>`;
                        });
                    });

                    $(`#kasbonSummaryBody-${voucherId}`).html(rows);

                    const totSel = totNilai - totPj;
                    const totSelClass = totSel > 0.001 ? 'clr-pos' : totSel < -0.001 ? 'clr-neg' : 'clr-zero';
                    $(`#kasbonSummaryFoot-${voucherId}`).html(`
                        <tr>
                            <td class="fw-bold">Total</td>
                            <td class="num-cell clr-nilai">${formatNumber(totNilai)}</td>
                            <td class="num-cell clr-pj">${formatNumber(totPj)}</td>
                            <td class="num-cell ${totSelClass}">${formatNumber(totSelisih)}</td>
                            <td colspan="2"></td>
                        </tr>`);
                },
                error: function() {
                    $(`#kasbonSummaryBody-${voucherId}`).html(
                        `<tr><td colspan="6" class="text-center text-danger py-3">
                            <i class="fas fa-times-circle me-1"></i> Terjadi kesalahan server
                        </td></tr>`
                    );
                }
            });
        }

        function saveGeneralDetail(voucherId) {
            const kasbonId = $(`#input_kasbon_gen-${voucherId}`).val();
            const keterangan = $(`#input_ket_gen-${voucherId}`).val();
            if (!kasbonId) {
                showFloatingAlert('error', 'Please select a cash advance first');
                return;
            }

            showFloatingAlert('saving', 'Menyimpan detail...');
            $.ajax({
                url: ROUTES.detailStore,
                method: 'POST',
                data: {
                    id_mutasi_voucher: voucherId,
                    jenis: 'general',
                    id_kasbon_gen: kasbonId,
                    keterangan,
                    _token: CSRF
                },
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', r.message || 'Gagal');
                        return;
                    }
                    showFloatingAlert('success', 'Detail added successfully!');
                    appendDetailRow(voucherId, r.data);
                    updateDetailTotals(voucherId);
                    // Reset form general
                    $(`#input_kasbon_gen-${voucherId}`).val('').trigger('change.select2');
                    $(`#input_nilai_gen-${voucherId}, #input_pj_gen-${voucherId}, #input_selisih_gen-${voucherId}, #input_ket_gen-${voucherId}`)
                        .val('');
                    // Reload options (exclude yang sudah dipakai)
                    loadGeneralKasbonOptions(voucherId);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.errors ?
                        Object.values(xhr.responseJSON.errors).flat().join(', ') :
                        (xhr.responseJSON?.message || 'Gagal');
                    showFloatingAlert('error', msg);
                }
            });
        }

        // ============================================================
        // DETAIL — EDIT (hanya keterangan yang bisa diubah)
        // ============================================================
        function editDetail(detailId, voucherId) {
            showFloatingAlert('saving', 'Loading detail data...');
            $.ajax({
                url: ROUTES.detailShow + detailId,
                method: 'GET',
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', 'Gagal memuat data detail');
                        return;
                    }
                    hideFloatingAlert();
                    const d = r.data;

                    // Switch ke edit mode
                    $(`#detailAddMode-${voucherId}`).hide();
                    $(`#detailEditMode-${voucherId}`).show();
                    $(`#addDetailSection-${voucherId}`).addClass('edit-mode');
                    $(`#detailFormTitle-${voucherId}`).html(
                        '<i class="fas fa-edit" style="color:#3b82f6;"></i> Edit Detail Notes');

                    // Isi field edit mode
                    $(`#editing_detail_id-${voucherId}`).val(detailId);
                    $(`#edit_jenis_badge-${voucherId}`)
                        .attr('class', `jenis-badge jenis-${d.jenis}`)
                        .text(d.jenis);
                    $(`#edit_jo_nomor-${voucherId}`).text(d.nomor_jo || d.jo?.nomor || (d.jenis === 'general' ?
                        '—' : '-'));
                    $(`#edit_kasbon_nomor-${voucherId}`).text(d.nomor_kasbon || d.kasbon?.nomor || '-');
                    $(`#edit_nilai_display-${voucherId}`).text('IDR ' + formatNumber(d.nilai || 0));
                    $(`#edit_pj_display-${voucherId}`).text('IDR ' + formatNumber(d.pj || 0));
                    const selisih = parseFloat(d.selisih || 0);
                    $(`#edit_selisih_display-${voucherId}`)
                        .text('IDR ' + formatNumber(selisih))
                        .removeClass('clr-sel-neg clr-sel-pos')
                        .addClass(selisih < -0.001 ? 'clr-sel-neg' : selisih > 0.001 ? 'clr-sel-pos' : '');
                    $(`#edit_keterangan-${voucherId}`).val(d.keterangan || '');

                    $('html, body').animate({
                        scrollTop: $(`#addDetailSection-${voucherId}`).offset().top - 100
                    }, 400);
                },
                error: () => showFloatingAlert('error', 'Gagal memuat data detail')
            });
        }

        function cancelDetailEdit(voucherId) {
            $(`#editing_detail_id-${voucherId}`).val('');
            $(`#detailFormTitle-${voucherId}`).html(
                '<i class="fas fa-plus-square" style="color:#16a34a;"></i> Add Detail');
            $(`#addDetailSection-${voucherId}`).removeClass('edit-mode');
            $(`#detailEditMode-${voucherId}`).hide();
            $(`#detailAddMode-${voucherId}`).show();
            // Reset form
            $(`#input_jenis-${voucherId}`).val('');
            onJenisChange(voucherId);
        }

        function submitDetailEdit(voucherId) {
            const detailId = $(`#editing_detail_id-${voucherId}`).val();
            const keterangan = $(`#edit_keterangan-${voucherId}`).val();
            showFloatingAlert('saving', 'Memperbarui keterangan...');
            $.ajax({
                url: ROUTES.detailUpdate + detailId,
                method: 'POST',
                data: {
                    keterangan,
                    _method: 'PUT',
                    _token: CSRF
                },
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', r.message || 'Gagal memperbarui');
                        return;
                    }
                    showFloatingAlert('success', 'Detail updated successfully!');
                    cancelDetailEdit(voucherId);
                    loadVoucherDetails(voucherId);
                },
                error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Gagal memperbarui')
            });
        }

        // ============================================================
        // DETAIL — DELETE
        // ============================================================
        let pendingDeleteDetailId = null;
        let pendingDeleteVoucherId = null;

        function deleteDetail(detailId, voucherId) {
            pendingDeleteDetailId = detailId;
            pendingDeleteVoucherId = voucherId;
            $('#detailConfirmBody').html('<p>Yakin ingin menghapus detail ini? Tindakan ini tidak dapat dibatalkan.</p>');
            new bootstrap.Modal(document.getElementById('detailConfirmModal')).show();
        }

        function showKasbonInfo(btn) {
            const nomor = btn.dataset.nomor || '—';
            const release = btn.dataset.rel || '—'; // data-rel (bukan data-release)
            const dept = btn.dataset.dep || '—'; // data-dep (bukan data-departemen)
            const cabang = btn.dataset.cab || '—'; // data-cab
            const tgl = btn.dataset.tgl || '—';
            const tglRel = btn.dataset.tglRel || '—';
            const keterangan = btn.dataset.keterangan || '';

            let items = [];
            try {
                items = JSON.parse(btn.dataset.items || '[]');
            } catch (e) {}

            document.getElementById('ki-nomor-title').textContent = nomor;
            document.getElementById('ki-item-count').textContent = items.length + ' item';
            document.getElementById('ki-release').textContent = release;
            document.getElementById('ki-dept').textContent = dept;
            document.getElementById('ki-cabang').textContent = cabang;
            document.getElementById('ki-tgl').textContent = tgl;
            document.getElementById('ki-tgl-release').textContent = tglRel;

            // Keterangan — hanya tampil kalau ada isinya
            const ketWrap = document.getElementById('ki-keterangan-wrap');
            const ketEl = document.getElementById('ki-keterangan');
            if (keterangan) {
                ketEl.textContent = keterangan;
                ketWrap.style.display = 'block';
            } else {
                ketWrap.style.display = 'none';
            }

            const tbody = document.getElementById('ki-tbody');
            const tfoot = document.getElementById('ki-tfoot');

            if (!items.length) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada item</td></tr>';
                tfoot.innerHTML = '';
            } else {
                let totNilai = 0,
                    totPj = 0,
                    totSelisih = 0,
                    html = '',
                    rowNo = 0;
                const groups = [];
                items.forEach(item => {
                    const last = groups[groups.length - 1];
                    if (last && last.cat === (item.kategori || '—')) last.rows.push(item);
                    else groups.push({
                        cat: item.kategori || '—',
                        rows: [item]
                    });
                });
                groups.forEach(g => {
                    g.rows.forEach((item, ii) => {
                        rowNo++;
                        const n = parseFloat(item.nilai_kasbon ?? 0);
                        const p = parseFloat(item.pj ?? 0);
                        const s = parseFloat(item.selisih ?? 0);
                        totNilai += n;
                        totPj += p;
                        totSelisih += s;
                        const sCls = s > 0.001 ? 'clr-pos' : s < -0.001 ? 'clr-neg' : 'clr-zero';
                        html += '<tr>';
                        if (ii === 0) {
                            html +=
                                `<td rowspan="${g.rows.length}" class="text-center fw-bold" style="vertical-align:middle;font-size:.8rem;">${rowNo}</td>`;
                            html +=
                                `<td rowspan="${g.rows.length}" class="cat-cell" style="vertical-align:middle;background:#f0fdf4;">${escHtml(g.cat)}</td>`;
                        }
                        html += `
    <td style="font-size:.8rem;">${escHtml(item.label||'—')}</td>
    <td class="num">${formatNumber(n)}</td>
    <td class="num">${formatNumber(p)}</td>
    <td class="num ${sCls}">${formatNumber(s)}</td>
</tr>`;
                    });
                });
                tbody.innerHTML = html;
                const tsCls = totSelisih < -0.001 ? 'text-danger' : '';
                tfoot.innerHTML = `<tr>
    <td colspan="3" class="text-end fw-bold">GRAND TOTAL</td>
    <td class="num" style="white-space:nowrap;min-width:160px;"><span class="badge bg-dark rounded-1 me-1" style="font-size:.65rem;vertical-align:middle;">IDR</span>${formatNumber(totNilai)}</td>
    <td class="num" style="white-space:nowrap;min-width:160px;"><span class="badge bg-dark rounded-1 me-1" style="font-size:.65rem;vertical-align:middle;">IDR</span>${formatNumber(totPj)}</td>
    <td class="num" style="white-space:nowrap;min-width:160px;"><span class="badge bg-dark rounded-1 me-1" style="font-size:.65rem;vertical-align:middle;">IDR</span>${formatNumber(totSelisih)}</td>
</tr>`;
            }

            new bootstrap.Modal(document.getElementById('modalKasbonInfo')).show();
        }

        $('#btnConfirmDeleteDetail').on('click', function() {
            if (!pendingDeleteDetailId) return;
            const detailId = pendingDeleteDetailId;
            const voucherId = pendingDeleteVoucherId;
            bootstrap.Modal.getInstance(document.getElementById('detailConfirmModal')).hide();
            showFloatingAlert('saving', 'Menghapus detail...');

            $.ajax({
                url: ROUTES.detailDestroy + detailId,
                method: 'DELETE',
                data: {
                    _token: CSRF
                },
                success: function(r) {
                    if (r.success) {
                        showFloatingAlert('success', 'Detail deleted successfully!');
                        loadVoucherDetails(voucherId);
                    } else {
                        showFloatingAlert('error', r.message || 'Gagal menghapus detail');
                    }
                },
                error: xhr => showFloatingAlert('error', xhr.responseJSON?.message ||
                    'Gagal menghapus detail')
            });

            pendingDeleteDetailId = null;
            pendingDeleteVoucherId = null;
        });

        // ============================================================
        // TABLE HELPERS
        // ============================================================
        // SESUDAH (8 kolom, match dengan blade & renderDetailTable)
        function buildDetailTableHtml(voucherId, details) {
            return `
<div class="table-responsive mt-2">
    <table class="table table-detail table-bordered" id="detailTable-${voucherId}">
        <thead>
            <tr>
                <th width="120" class="text-center">JO</th>
                <th style="min-width:150px;">CA No.</th>
                <th width="130">Category</th>
                <th>Description</th>
                <th style="min-width:160px;" class="text-end">CA Amount (IDR)</th>
                <th style="min-width:160px;" class="text-end">LPJ Amount (IDR)</th>
                <th style="min-width:160px;" class="text-end">Balance (IDR)</th>
                <th width="80" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody id="detailTableBody-${voucherId}">
            <tr><td colspan="8" class="text-center text-muted py-3">
                <i class="fas fa-inbox fa-2x d-block mb-2"></i>Belum ada detail
            </td></tr>
        </tbody>
        <tfoot id="detailTableFoot-${voucherId}"></tfoot>
    </table>
</div>`;
        }

        function buildDetailRowHtml(voucherId, d, no) {
            const jenisBadge = `<span class="jenis-badge jenis-${d.jenis}">${d.jenis}</span>`;
            const joText = d.jo ? (d.jo.nomor || d.nomor_jo || '-') : (d.nomor_jo || '-');
            const kasbonText = d.kasbon ? (d.kasbon.nomor || d.nomor_kasbon || '-') : (d.nomor_kasbon || '-');
            return `<tr class="detail-row" data-detail-id="${d.id}" data-voucher-id="${voucherId}">
        <td class="text-center">${no}</td>
        <td class="text-center">${jenisBadge}</td>
        <td class="small">${joText}</td>
        <td class="small">${kasbonText}</td>
        <td class="num-cell">${formatNumber(d.nilai   || 0)}</td>
        <td class="num-cell">${formatNumber(d.pj      || 0)}</td>
        <td class="num-cell">${formatNumber(d.selisih || 0)}</td>
        <td class="small">${d.keterangan || '-'}</td>
        <td class="text-center">
            <button type="button" class="btn btn-primary btn-sm"
                onclick="editDetail('${d.id}', ${voucherId})" title="Edit Keterangan">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-danger btn-sm"
                onclick="deleteDetail('${d.id}', ${voucherId})" title="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>`;
        }

        function appendDetailRow(voucherId, data) {
            // Reload from server so grouping stays correct for all voucher types
            loadVoucherDetails(voucherId);
        }

        function renumberDetailRows(voucherId) {
            $(`#detailTableBody-${voucherId} tr.detail-row`).each(function(i) {
                $(this).find('td:first').text(i + 1);
            });
        }

        function updateDetailTotals(voucherId) {
            let totalNilai = 0,
                totalPj = 0,
                totalSelisih = 0;
            $(`#detailTableBody-${voucherId} tr.detail-row`).each(function() {
                totalNilai += parseRupiah($(this).find('td').eq(4).text());
                totalPj += parseRupiah($(this).find('td').eq(5).text());
                totalSelisih += parseRupiah($(this).find('td').eq(6).text());
            });
            $(`#totalNilai-${voucherId}`).text(formatNumber(totalNilai));
            $(`#totalPj-${voucherId}`).text(formatNumber(totalPj));
            $(`#totalSelisih-${voucherId}`).text(formatNumber(totalSelisih));
        }

        function updateDetailCountBadge(voucherId) {
            const count = $(`#detailTableBody-${voucherId} tr.detail-row`).length;
            $(`.detail-count-badge-${voucherId}`).text(count + ' detail');
        }

        function toggleVoucherPanel(voucherId) {
            const $body = $(`#voucher-body-${voucherId}`);
            const $icon = $(`.toggle-icon-${voucherId}`);
            const isOpen = $body.is(':visible');

            if (isOpen) {
                $body.slideUp(200);
                $icon.css('transform', 'rotate(0deg)');
            } else {
                $body.slideDown(200);
                $icon.css('transform', 'rotate(180deg)');

                // Load detail hanya sekali — saat pertama kali dibuka
                if (!$(`#detailTableBody-${voucherId}`).data('loaded')) {
                    loadVoucherDetails(voucherId);
                    $(`#detailTableBody-${voucherId}`).data('loaded', true);
                }
            }
        }

        function initVoucherPanelSelects(voucherId) {
            $(`.select2-jo-${voucherId}`).select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih JO...',
                allowClear: true,
                width: '100%',
            });
            $(`.select2-kasbon-gen-${voucherId}`).select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih kasbon general...',
                allowClear: true,
                width: '100%',
            });
        }

        function loadJoOptions(voucherId, jenis) {
            const joSel = $(`#input_jo-${voucherId}`);
            joSel.html('<option value="">Loading...</option>');
            $.ajax({
                url: ROUTES.joOptions,
                method: 'GET',
                data: {
                    jenis
                },
                success: function(r) {
                    let opts = '<option value=""></option>';
                    if (r.success && r.data) {
                        r.data.forEach(jo => {
                            opts +=
                                `<option value="${jo.id}">${jo.nomor}${jo.label ? ' — ' + jo.label : ''}</option>`;
                        });
                    }
                    joSel.html(opts).trigger('change.select2');
                },
                error: () => joSel.html('<option value="">Gagal memuat data</option>')
            });
        }

        // ============================================================
        // ADD ALL KASBON dari satu JO sekaligus
        // ============================================================
        async function addAllKasbon(voucherId) {
            const jenis = $(`#input_jenis-${voucherId}`).val();
            const joId = $(`#input_jo-${voucherId}`).val();
            const all = window._kasbonData?.[voucherId] || [];
            const avail = all.filter(k => !k.sudah_dipakai);

            if (!avail.length) {
                showFloatingAlert('error', 'Semua kasbon di JO ini sudah ditambahkan');
                return;
            }

            const confirm = await Swal.fire({
                title: 'Tambah Semua Kasbon?',
                html: `<p>Akan menambahkan <strong>${avail.length} kasbon</strong> dari JO ini sekaligus ke voucher.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-plus-circle me-1"></i> Ya, Tambah Semua',
                cancelButtonText: 'Cancel',
            });
            if (!confirm.isConfirmed) return;

            const $btn = $(`#btnAddAll-${voucherId}`);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menambahkan...');
            showFloatingAlert('saving', `Menambahkan ${avail.length} kasbon...`);

            let ok = 0,
                fail = 0;

            for (const k of avail) {
                const ki = all.indexOf(k);
                const payload = {
                    id_mutasi_voucher: voucherId,
                    jenis,
                    keterangan: '',
                    _token: CSRF
                };
                if (jenis === 'tramper') {
                    payload.id_kasbon_tram = k.id_kasbon;
                    payload.id_jo_tram = joId;
                }
                if (jenis === 'other') {
                    payload.id_kasbon_other = k.id_kasbon;
                    payload.id_jo_other = joId;
                }
                if (jenis === 'contract') {
                    payload.id_kasbon_cont = k.id_kasbon;
                    payload.id_jo_cont = joId;
                }
                if (jenis === 'general') {
                    payload.id_kasbon_gen = kasbonId;
                }

                try {
                    const r = await $.ajax({
                        url: ROUTES.detailStore,
                        method: 'POST',
                        data: payload
                    });
                    if (r.success) {
                        ok++;
                        // Grey-out baris di summary
                        const $tbody = $(`#kasbonSummaryBody-${voucherId}`);
                        const $pRow = $tbody.find(`.kasbon-parent-row[data-voucher="${voucherId}"][data-ki="${ki}"]`);
                        $pRow.addClass('sudah-dipakai');
                        $pRow.find('.kasbon-ket-input').prop('disabled', true);
                        $pRow.find('.kasbon-add-btn').replaceWith(
                            `<button class="btn btn-sm btn-success" disabled><i class="fas fa-check me-1"></i>Ditambahkan</button>`
                        );
                        $tbody.find(`.kasbon-item-row[data-parent-voucher="${voucherId}"][data-parent-ki="${ki}"]`)
                            .addClass('sudah-dipakai');
                        // Tandai di data store supaya tidak dihitung lagi
                        window._kasbonData[voucherId][ki].sudah_dipakai = true;
                    } else {
                        fail++;
                    }
                } catch (e) {
                    fail++;
                }
            }

            // Refresh tabel detail setelah semua selesai
            loadVoucherDetails(voucherId);
            $btn.prop('disabled', false).html('<i class="fas fa-plus-circle me-1"></i> Success!');

            if (fail === 0) showFloatingAlert('success', `${ok} kasbon berhasil ditambahkan!`);
            else showFloatingAlert('error', `${ok} berhasil ditambahkan, ${fail} gagal`);
        }

        // ============================================================
        // LOAD VOUCHER DETAILS (fetch dari server lalu render)
        // ============================================================
        function loadVoucherDetails(voucherId) {
            $.ajax({
                url: ROUTES.voucherDetails + voucherId + '/details',
                method: 'GET',
                success: function(r) {
                    if (r.success) renderDetailTable(voucherId, r.data);
                    else $(`#detailTableBody-${voucherId}`).html(
                        `<tr><td colspan="7" class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-triangle me-1"></i> Gagal memuat detail
                </td></tr>`
                    );
                },
                error: function() {
                    $(`#detailTableBody-${voucherId}`).html(
                        `<tr><td colspan="7" class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-triangle me-1"></i> Gagal memuat detail
                </td></tr>`
                    );
                }
            });
        }

        // ============================================================
        // RENDER DETAIL TABLE — grouped by JO
        // ===========================================================

        function renderDetailTable(voucherId, details) {
            const tbody = $(`#detailTableBody-${voucherId}`);
            const tfoot = $(`#detailTableFoot-${voucherId}`);

            if (!details || details.length === 0) {
                tbody.html(`<tr><td colspan="8" class="text-center text-muted py-4">
            <i class="fas fa-inbox fa-2x d-block mb-2"></i>Belum ada detail
        </td></tr>`);
                tfoot.html('');
                updateDetailCountBadge(voucherId, 0);
                return;
            }

            // ── Kelompokkan per JO ──
            const groups = {},
                order = [];
            details.forEach(d => {
                const joKey = d.nomor_jo || (d.jo_id ? 'id_' + d.jo_id : null);
                const key = joKey ? 'jo_' + joKey :
                    d.jenis === 'general' ? 'gen_' + d.id : 'null_' + d.id;
                if (!groups[key]) {
                    groups[key] = {
                        nomor_jo: d.nomor_jo,
                        jenis: d.jenis,
                        rows: []
                    };
                    order.push(key);
                }
                groups[key].rows.push(d);
            });

            let html = '';
            let totNilai = 0,
                totPj = 0,
                totSelisih = 0;

            order.forEach(key => {
                const g = groups[key];

                // Hitung total baris untuk rowspan kolom JO
                let joTotalRows = 0;
                g.rows.forEach(d => {
                    joTotalRows += (d.items && d.items.length > 0) ? d.items.length : 1;
                });

                const joLabel = g.nomor_jo ? escHtml(g.nomor_jo) : 'General';
                const joBadge =
                    `<span class="jenis-badge jenis-${g.jenis}" style="font-size:.65rem;display:block;margin-bottom:4px;">${g.jenis}</span>`;

                // Flag: apakah JO cell sudah dirender? Hanya boleh satu kali per group
                let joRendered = false;

                g.rows.forEach(d => {
                    const items = d.items || [];
                    const nomKasbon = escHtml(d.nomor_kasbon || '-');
                    const selCls = d.selisih > 0.001 ? 'clr-pos' : d.selisih < -0.001 ? 'clr-neg' :
                        'clr-zero';
                    const kasbonRows = items.length > 0 ? items.length : 1;

                    const aksiCell = `
<button class="btn btn-info btn-sm d-block mx-auto mb-1"
    onclick="showKasbonInfo(this)" title="Info"
    data-nomor="${escAttr(d.nomor_kasbon ?? '')}"
    data-dep="${escAttr(d.departemen ?? '—')}"
    data-cab="${escAttr(d.cabang     ?? '—')}"
    data-rel="${escAttr(d.release    ?? '—')}"
    data-tgl="${escAttr(d.tgl_kasbon ?? '')}"
    data-tgl-rel="${escAttr(d.tgl_release ?? '')}"
    data-keterangan="${escAttr(d.keterangan ?? '')}"
    data-items="${escAttr(JSON.stringify(d.items ?? []))}">
    <i class="bi bi-journal-text text-white"></i>
</button>
<button class="btn btn-primary btn-sm d-block mx-auto mb-1"
    onclick="editDetail('${d.id}',${voucherId})" title="Edit">
    <i class="fas fa-edit"></i>
</button>
<button class="btn btn-danger btn-sm d-block mx-auto"
    onclick="deleteDetail('${d.id}',${voucherId})" title="Hapus">
    <i class="fas fa-trash"></i>
</button>`;

                    if (items.length === 0) {
                        totNilai += parseFloat(d.nilai || 0);
                        totPj += parseFloat(d.pj || 0);
                        totSelisih += parseFloat(d.selisih || 0);

                        // JO cell hanya sekali per group
                        const joCellHtml = !joRendered ?
                            `<td rowspan="${joTotalRows}" class="text-center align-middle" style="font-size:.78rem;font-weight:700;">${joBadge}${joLabel}</td>` :
                            '';
                        joRendered = true;

                        html += `
<tr>
    ${joCellHtml}
    <td style="padding-left:16px;font-weight:600;font-size:.83rem;vertical-align:middle;">${nomKasbon}</td>
    <td class="text-muted" style="font-size:.8rem;vertical-align:middle;">—</td>
    <td class="text-muted" style="font-size:.8rem;vertical-align:middle;">—</td>
    <td class="num-cell clr-nilai" style="vertical-align:middle;">${formatNumber(d.nilai)}</td>
    <td class="num-cell clr-pj"   style="vertical-align:middle;">${formatNumber(d.pj)}</td>
    <td class="num-cell ${selCls}" style="vertical-align:middle;">${formatNumber(d.selisih)}</td>
    <td class="text-center" style="vertical-align:middle;">${aksiCell}</td>
</tr>`;

                    } else {
                        items.forEach(item => {
                            totNilai += parseFloat(item.nilai_kasbon ?? 0);
                            totPj += parseFloat(item.pj ?? 0);
                            totSelisih += parseFloat(item.selisih ?? 0);
                        });

                        // Kelompokkan by kategori
                        const catGroups = [];
                        items.forEach(item => {
                            const cat = (item.kategori && item.kategori !== '—') ? item.kategori :
                                '—';
                            const last = catGroups[catGroups.length - 1];
                            if (last && last.cat === cat) last.items.push(item);
                            else catGroups.push({
                                cat,
                                items: [item]
                            });
                        });

                        let firstItemInKasbon = true;
                        catGroups.forEach(cg => {
                            cg.items.forEach((item, ii) => {
                                const labelText = item.label ? escHtml(item.label) : '—';
                                const iSel = parseFloat(item.selisih ?? 0);
                                const iSelCls = iSel > 0.001 ? 'clr-pos' : iSel < -0.001 ?
                                    'clr-neg' : 'clr-zero';

                                // JO cell: hanya pada baris pertama seluruh group, SEKALI
                                const joCellHtml = !joRendered ?
                                    `<td rowspan="${joTotalRows}" class="text-center align-middle" style="font-size:.78rem;font-weight:700;">${joBadge}${joLabel}</td>` :
                                    '';
                                if (!joRendered) joRendered = true;

                                // Kasbon cell: rowspan untuk semua item dalam kasbon ini
                                const kasbonCellHtml = firstItemInKasbon ?
                                    `<td rowspan="${kasbonRows}" style="padding-left:16px;vertical-align:middle;font-weight:600;font-size:.83rem;">${nomKasbon}</td>` :
                                    '';

                                // Category cell: rowspan untuk item dalam kategori yang sama
                                const catCellHtml = (ii === 0) ?
                                    `<td rowspan="${cg.items.length}" class="cat-cell" style="vertical-align:middle;font-size:.8rem;">${escHtml(cg.cat)}</td>` :
                                    '';

                                // Aksi cell: rowspan untuk semua item dalam kasbon
                                const aksiTdHtml = firstItemInKasbon ?
                                    `<td class="text-center" rowspan="${kasbonRows}" style="vertical-align:middle;">${aksiCell}</td>` :
                                    '';

                                html += `
<tr>
    ${joCellHtml}
    ${kasbonCellHtml}
    ${catCellHtml}
    <td style="font-size:.78rem;vertical-align:middle;">${labelText}</td>
    <td class="num-cell clr-nilai" style="vertical-align:middle;">${formatNumber(item.nilai_kasbon ?? 0)}</td>
    <td class="num-cell clr-pj"   style="vertical-align:middle;">${formatNumber(item.pj ?? 0)}</td>
    <td class="num-cell ${iSelCls}" style="vertical-align:middle;">${formatNumber(item.selisih ?? 0)}</td>
    ${aksiTdHtml}
</tr>`;
                                firstItemInKasbon = false;
                            });
                        });
                    }
                });
            });

            tbody.html(html);

            const totSelClass = totSelisih > 0.001 ? 'clr-pos' : totSelisih < -0.001 ? 'clr-neg' : 'clr-zero';
            tfoot.html(`
<tr>
    <td colspan="4" class="text-end fw-bold" style="background:#2d3748;color:#fff;padding:9px 12px;">GRAND TOTAL</td>
    <td style="background:#2d3748;color:#fff;padding:9px 12px;text-align:right;white-space:nowrap;min-width:160px;">
        <span class="badge bg-secondary rounded-1 me-1" style="font-size:.62rem;vertical-align:middle;">IDR</span>${formatNumber(totNilai)}
    </td>
    <td style="background:#2d3748;color:#fff;padding:9px 12px;text-align:right;white-space:nowrap;min-width:160px;">
        <span class="badge bg-secondary rounded-1 me-1" style="font-size:.62rem;vertical-align:middle;">IDR</span>${formatNumber(totPj)}
    </td>
    <td class="${totSelClass}" style="background:#2d3748;color:#fff;padding:9px 12px;text-align:right;white-space:nowrap;min-width:160px;">
        <span class="badge bg-secondary rounded-1 me-1" style="font-size:.62rem;vertical-align:middle;">IDR</span>${formatNumber(totSelisih)}
    </td>
    <td style="background:#2d3748;"></td>
</tr>`);

            updateDetailCountBadge(voucherId, details.length);
        }

        function toggleDetailGroup(gId) {
            const $rows = $(`.detail-group-${gId}`);
            const $chev = $(`#chev-${gId}`);
            if ($rows.first().is(':visible')) {
                $rows.hide();
                $chev.css('transform', 'rotate(-90deg)');
            } else {
                $rows.show();
                $chev.css('transform', 'rotate(0deg)');
            }
        }

        function updateDetailCountBadge(voucherId, count) {
            $(`.detail-count-badge-${voucherId}`).text(count + ' detail');
        }

        // ── Setelah add/delete, refresh tabel ──
        function appendDetailRow(voucherId, data) {
            // Langsung reload dari server supaya grouping selalu akurat
            loadVoucherDetails(voucherId);
        }

        $(document).ready(function() {
            @foreach ($mutasiPembayaran->vouchers as $v)
                initVoucherPanelSelects({{ $v->id }});
            @endforeach
        });
    </script>
@endpush
