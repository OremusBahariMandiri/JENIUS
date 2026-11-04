@extends('layouts.app')

@section('title', 'Edit Mutasi Pembayaran')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        .mutasiEditPage .card{border:none;border-radius:10px;box-shadow:0 0 20px rgba(0,0,0,.08);}
        .mutasiEditPage .card-header{border-radius:10px 10px 0 0 !important;padding:1rem 1.5rem;font-weight:600;}
        .mutasiEditPage .form-control:focus,.mutasiEditPage .form-select:focus{border-color:#16a34a;box-shadow:0 0 0 .2rem rgba(22,163,74,.25);}
        .required-field::after{content:" *";color:#dc3545;font-weight:600;}
        .floating-badge-alert{position:fixed;top:80px;right:30px;z-index:9999;min-width:260px;padding:15px 20px;border-radius:12px;box-shadow:0 8px 25px rgba(0,0,0,.2);display:none;animation:slideInRight .4s ease-out;backdrop-filter:blur(10px);}
        .floating-badge-alert.show{display:flex;align-items:center;gap:12px;}
        .floating-badge-alert.alert-saving{background:linear-gradient(135deg,#fbbf24,#f59e0b);color:#fff;}
        .floating-badge-alert.alert-success{background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;}
        .floating-badge-alert.alert-error{background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;}
        .floating-badge-alert i{font-size:1.3rem;}
        .floating-badge-alert .alert-text{flex:1;font-weight:600;font-size:.95rem;}
        @keyframes slideInRight{from{transform:translateX(400px);opacity:0;}to{transform:translateX(0);opacity:1;}}
        @keyframes slideOutRight{from{transform:translateX(0);opacity:1;}to{transform:translateX(400px);opacity:0;}}
        .floating-badge-alert.hiding{animation:slideOutRight .4s ease-in;}
        .add-form-section{background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #86efac;border-radius:12px;padding:1.5rem;}
        .add-form-section.edit-mode{background:linear-gradient(135deg,#eff6ff,#dbeafe);border-color:#93c5fd;}
        .jenis-badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.03em;}
        .jenis-tramper{background:#d1fae5;color:#065f46;}
        .jenis-other{background:#dbeafe;color:#1e40af;}
        .jenis-contract{background:#fce7f3;color:#9d174d;}
        .jenis-general{background:#fef3c7;color:#92400e;}
        .jenis-custom{background:#ede9fe;color:#5b21b6;}
        .detail-edit-mode-box{background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #93c5fd;border-radius:10px;padding:1.25rem;}
        .edit-info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;}
        .edit-info-card{background:#fff;border-radius:8px;padding:10px 14px;border:1px solid #e2e8f0;}
        .edit-info-lbl{font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:600;}
        .edit-info-val{font-size:.88rem;font-weight:700;color:#1e293b;margin-top:4px;}
        .clr-nilai{color:#16a34a!important;}.clr-pj{color:#2563eb!important;}
        .clr-pos{color:#16a34a!important;}.clr-neg{color:#dc2626!important;}.clr-zero{color:#6b7280!important;}.clr-muted{color:#94a3b8;}
        .kasbon-summary-wrap{border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;}
        .kasbon-summary-header{background:linear-gradient(135deg,#f8fafc,#f1f5f9);padding:10px 14px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0;font-weight:600;font-size:.83rem;color:#1e293b;}
        .table-kasbon-summary{font-size:.82rem;}
        .table-kasbon-summary thead th{background:#2d3748;color:#fff;padding:8px 12px;font-weight:700;font-size:.73rem;text-transform:uppercase;letter-spacing:.03em;border:none;white-space:nowrap;}
        .table-kasbon-summary tbody td{padding:8px 12px;vertical-align:middle;border-color:#f1f5f9;}
        .table-kasbon-summary tfoot td{background:#f8fafc;font-weight:700;padding:8px 12px;border-top:2px solid #e2e8f0;}
        .num-cell{text-align:right;font-variant-numeric:tabular-nums;font-weight:600;font-size:.82rem;}
        .kasbon-parent-row.sudah-dipakai td{opacity:.55;}
        .btn-add-purple{background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;border:none;border-radius:8px;padding:8px 20px;font-weight:600;}
        .btn-add-purple:hover{background:linear-gradient(135deg,#6d28d9,#5b21b6);color:#fff;}
        .btn-cancel-edit-style{background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;border-radius:8px;padding:8px 20px;font-weight:600;}
        .btn-cancel-edit-style:hover{background:#e2e8f0;}
        .btn-kasbon-info{background:#e0f2fe;color:#0369a1;border:none;border-radius:50%;width:24px;height:24px;padding:0;font-size:.7rem;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;}
        .btn-kasbon-info:hover{background:#bae6fd;}
        .btn-tambah-kasbon{background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border:none;border-radius:6px;padding:4px 12px;font-size:.78rem;font-weight:600;cursor:pointer;}
        .btn-tambah-kasbon:hover{background:linear-gradient(135deg,#15803d,#166534);}
        .btn-toggle-items{background:none;border:1px solid #e2e8f0;color:#64748b;width:22px;height:22px;border-radius:4px;font-size:.7rem;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .15s;flex-shrink:0;padding:0;}
        .btn-toggle-items:hover:not(:disabled){background:#e0f2fe;border-color:#7dd3fc;color:#0369a1;}
        .btn-toggle-items .toggle-chev{transition:transform .2s;display:inline-block;}
        .kasbon-item-row td{background:#f8f9fa!important;border-bottom:1px solid #e9ecef;font-size:.79rem;color:#374151;}
        .table-detail thead th{background:#2d3748;color:#fff;font-size:.76rem;padding:10px 12px;font-weight:600;white-space:nowrap;}
        .table-detail tbody td{padding:8px 12px;vertical-align:middle;font-size:.82rem;}
        .table-detail tbody tr:hover td{background:#f8fafc;}
        .final-save-section{position:sticky;bottom:0;background:#fff;padding:16px 20px;box-shadow:0 -4px 20px rgba(0,0,0,.1);border-radius:12px 12px 0 0;margin-top:20px;z-index:100;}
        .select2-container{width:100%!important;}
        .select2-container .select2-selection--single{height:calc(1.5em + .75rem + 2px)!important;padding:.375rem .75rem!important;border:2px solid #dee2e6!important;border-radius:8px!important;}
        .select2-container .select2-selection--single .select2-selection__rendered{line-height:1.5!important;padding-left:0!important;color:#212529;}
        .select2-container .select2-selection--single .select2-selection__arrow{height:100%!important;}
        #modalKasbonInfo .modal-header{background:linear-gradient(135deg,#1e3a5f,#1e40af);border-radius:12px 12px 0 0;border:none;padding:16px 20px;}
        #modalKasbonInfo .modal-title{font-size:1rem;font-weight:700;color:#fff;}
        #modalKasbonInfo .modal-subtitle{font-size:.78rem;color:#93c5fd;margin-top:4px;}
        #modalKasbonInfo .btn-close-black{filter:invert(1);}
        #modalKasbonInfo .ki-info-strip{display:flex;flex-wrap:wrap;gap:0;border-bottom:1px solid #e2e8f0;}
        #modalKasbonInfo .ki-info-cell{flex:1;min-width:150px;padding:10px 16px;border-right:1px solid #e2e8f0;}
        #modalKasbonInfo .ki-info-cell:last-child{border-right:none;}
        #modalKasbonInfo .ki-info-lbl{font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:600;}
        #modalKasbonInfo .ki-info-val{font-size:.88rem;font-weight:600;color:#0f172a;margin-top:2px;}
        #modalKasbonInfo .ki-table{width:100%;border-collapse:collapse;font-size:.8rem;}
        #modalKasbonInfo .ki-table thead th{background:#D1FAE5;color:black;padding:9px 12px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap;border:none;}
        #modalKasbonInfo .ki-table thead th.num{text-align:right;}
        #modalKasbonInfo .ki-table tbody tr{border-bottom:1px solid #f1f5f9;}
        #modalKasbonInfo .ki-table tbody td{padding:9px 12px;vertical-align:middle;background:#fff;color:#1e293b;}
        #modalKasbonInfo .ki-table tbody td.num{text-align:right;font-variant-numeric:tabular-nums;font-weight:600;}
        #modalKasbonInfo .ki-table tfoot td{background:#f5f6f7;color:#000;padding:9px 12px;font-weight:700;font-size:.8rem;border:none;white-space:nowrap;}
        #modalKasbonInfo .ki-table tfoot td.num{text-align:right;font-variant-numeric:tabular-nums;}

        /* ── Table footer (JO-Contract style) ── */
        .table-footer-mp { background:#f1f5f9; color:#111827; font-weight:700; border-top:2px solid #2c3e50; }
        .table-footer-mp td { padding:12px 10px; font-size:.9rem; }
        .currency-group-footer-mp { display:flex; align-items:center; gap:5px; }
        .currency-label-footer-mp { background:#2c3e50; color:#fff; border:1px solid #2c3e50; padding:.45rem .7rem; font-size:.78rem; border-radius:6px; min-width:46px; text-align:center; font-weight:700; }
        .value-footer-mp { flex:1; color:#111827; font-weight:700; font-size:.9rem; padding-left:8px; font-variant-numeric:tabular-nums; }
        .value-footer-mp.clr-pos { color:#16a34a; }
        .value-footer-mp.clr-neg { color:#dc2626; }
        .value-footer-mp.clr-zero { color:#6b7280; }
        /* body rows kasbon summary: semua nominal hitam */
        .table-kasbon-summary tbody .value-footer-mp.clr-nilai,
        .table-kasbon-summary tbody .value-footer-mp.clr-pj { color:#212529!important; }
    </style>
@endpush

@section('content')
    <div id="floatingBadgeAlert" class="floating-badge-alert">
        <i class="fas fa-circle-notch fa-spin" id="alertIcon"></i>
        <div class="alert-text" id="alertText">Processing...</div>
    </div>

    <div class="container-fluid mutasiEditPage">
        <div class="row"><div class="col-lg-12">

        <div class="card shadow mb-4">
            <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color:#dcfce7">
                <span class="fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Edit Payment Mutation</span>
            </div>
        </div>

        <input type="hidden" id="current_mutasi_id" value="{{ $mutasiPembayaran->id }}">
        <input type="hidden" id="current_voucher_id" value="{{ $voucher->id ?? '' }}">

        {{-- HEADER CARD --}}
        <div class="card shadow mb-4">
            <div class="card-header text-black" style="background-color:#dcfce7">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Payment Mutation Info</h6>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Voucher No.</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#16a34a;color:white;"><i class="fas fa-hashtag"></i></span>
                            <input type="text" id="edit_voucher_no_display" class="form-control fw-bold"
                                value="{{ $mutasiPembayaran->nomor ?? '-' }}" readonly
                                style="background-color:#f0fdf4;color:#14532d;letter-spacing:1px;">
                        </div>

                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Date</label>
                        <input type="date" id="edit_tanggal" class="form-control" value="{{ $mutasiPembayaran->tanggal ? \Carbon\Carbon::parse($mutasiPembayaran->tanggal)->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Bank / Cash</label>
                        <select id="edit_coa" class="form-select" data-placeholder="Select Bank / Cash account...">
                            <option value=""></option>
                            @foreach($chartOfAccounts as $coa)
                            <option value="{{ $coa->id_md_chart_of_account }}"
                                data-payment-type="{{ $coa->payment_type }}"
                                {{ $mutasiPembayaran->id_md_chart_of_account == $coa->id_md_chart_of_account ? 'selected' : '' }}>
                                [{{ $coa->payment_type }}] {{ $coa->no_account }} - {{ $coa->account_name ?? ($coa->nama_account ?? '') }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. Cek / BG</label>
                        <input type="text" id="edit_no_cek" class="form-control" value="{{ $mutasiPembayaran->no_cek }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kurs</label>
                        <div class="input-group">
                            <input type="text" id="edit_kurs_display" class="form-control" readonly
                                placeholder="1,0000"
                                value="{{ $mutasiPembayaran->kurs && $mutasiPembayaran->kurs != 1 ? number_format($mutasiPembayaran->kurs,2,',','.') : '' }}"
                                style="background:#f8fafc;cursor:pointer;"
                                onclick="$('#modalKursDetail').modal('show')">
                            <button type="button" class="btn btn-outline-secondary" onclick="$('#modalKursDetail').modal('show')"
                                title="Set Kurs Detail">
                                <i class="fas fa-dollar-sign"></i>
                            </button>
                        </div>
                        <small class="text-muted mt-1 d-block" id="kurs_summary_text">
                            @if($mutasiPembayaran->mata_uang && $mutasiPembayaran->mata_uang !== 'IDR')
                                <i class="fas fa-info-circle me-1"></i>
                                {{ $mutasiPembayaran->mata_uang }} @
                                {{ number_format($mutasiPembayaran->kurs,2,',','.') }}
                                @if($mutasiPembayaran->tgl_kurs) · {{ \Carbon\Carbon::parse($mutasiPembayaran->tgl_kurs)->format('d/m/Y') }} @endif
                            @else
                                <i class="fas fa-info-circle me-1"></i>Default: IDR (1:1)
                            @endif
                        </small>
                        <input type="hidden" id="edit_kurs_value" value="{{ $mutasiPembayaran->kurs ?? 1 }}">
                        <input type="hidden" id="edit_mata_uang_value" value="{{ $mutasiPembayaran->mata_uang ?? 'IDR' }}">
                        <input type="hidden" id="edit_tgl_kurs_value" value="{{ $mutasiPembayaran->tgl_kurs ? \Carbon\Carbon::parse($mutasiPembayaran->tgl_kurs)->format('Y-m-d') : '' }}">
                    </div>
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

        {{-- PAYMENT DETAILS CARD --}}
        @if($voucher)
        <div class="card shadow mb-4">
            <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color:#dcfce7">
                <h6 class="mb-0">
                    <i class="fas fa-list-alt me-2"></i>Payment Details
                    <span class="badge bg-secondary ms-2 detail-count-badge-{{ $voucher->id }}">0 detail</span>
                </h6>
            </div>
            <div class="card-body p-4">

                <div class="add-form-section mb-3" id="addDetailSection-{{ $voucher->id }}">
                    <h6 id="detailFormTitle-{{ $voucher->id }}">
                        <i class="fas fa-plus-square" style="color:#16a34a;"></i> Add Detail
                    </h6>
                    {{-- ADD MODE --}}
                    <div id="detailAddMode-{{ $voucher->id }}">
                        <div class="row g-3 mb-2">
                            <div class="col-12" id="jenisCol-{{ $voucher->id }}">
                                <label class="form-label fw-semibold required-field">Type</label>
                                <select id="input_jenis-{{ $voucher->id }}" class="form-select" onchange="onJenisChange({{ $voucher->id }})">
                                    <option value="">-- Select Type --</option>
                                    <option value="tramper">Tramper</option>
                                    <option value="other">Other</option>
                                    <option value="contract">Contract</option>
                                    <option value="general">General</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                            <div class="col-12" id="joCol-{{ $voucher->id }}" style="display:none;">
                                <label class="form-label fw-semibold required-field">JO</label>
                                <select id="input_jo-{{ $voucher->id }}" class="form-select select2-jo-{{ $voucher->id }}" data-placeholder="Select JO..." onchange="onJoChange({{ $voucher->id }})">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-12" id="genKasbonCol-{{ $voucher->id }}" style="display:none;">
                                <label class="form-label fw-semibold required-field">General Cash Advance</label>
                                <select id="input_kasbon_gen_sel-{{ $voucher->id }}" class="form-select select2-gen-kasbon-{{ $voucher->id }}" data-placeholder="Select General CA..." onchange="onGeneralKasbonChange({{ $voucher->id }})">
                                    <option value=""></option>
                                </select>
                            </div>
                            {{-- Custom Type Fields --}}
                            <div class="col-12" id="customCol-{{ $voucher->id }}" style="display:none;">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold required-field">Release</label>
                                        <select id="input_custom_release-{{ $voucher->id }}" class="form-select select2-custom-release-{{ $voucher->id }}" data-placeholder="Pilih Release...">
                                            <option value=""></option>
                                            @foreach($releaseTos as $r)
                                            <option value="{{ $r->id_md_release }}">{{ $r->nama_release }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">COA</label>
                                        <select id="input_custom_coa-{{ $voucher->id }}" class="form-select select2-custom-coa-{{ $voucher->id }}" data-placeholder="Pilih COA...">
                                            <option value=""></option>
                                            @foreach($allChartOfAccounts as $c)
                                            <option value="{{ $c->id_md_chart_of_account }}">{{ $c->no_account }} - {{ $c->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold required-field">Department</label>
                                        <select id="input_custom_dep-{{ $voucher->id }}" class="form-select select2-custom-dep-{{ $voucher->id }}" data-placeholder="Pilih Department...">
                                            <option value=""></option>
                                            @foreach($departemens as $d)
                                            <option value="{{ $d->id_md_dep }}">{{ $d->nama_dep }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold required-field">Cabang</label>
                                        <select id="input_custom_branch-{{ $voucher->id }}" class="form-select select2-custom-branch-{{ $voucher->id }}" data-placeholder="Pilih Cabang...">
                                            <option value=""></option>
                                            @foreach($branchs as $b)
                                            <option value="{{ $b->id_md_branch }}">{{ $b->nama_branch }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold required-field">Nilai</label>
                                        <input type="number" id="input_custom_nilai-{{ $voucher->id }}" class="form-control" placeholder="0" min="0" step="any">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold required-field">PJ</label>
                                        <input type="number" id="input_custom_pj-{{ $voucher->id }}" class="form-control" placeholder="0" min="0" step="any">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Keterangan</label>
                                        <input type="text" id="input_custom_keterangan-{{ $voucher->id }}" class="form-control" placeholder="Keterangan...">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-add-purple" onclick="addCustomDetail({{ $voucher->id }})">
                                        <i class="fas fa-plus me-1"></i> Add Custom
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{-- Kasbon Summary Table --}}
                        <div id="joKasbonSummaryWrap-{{ $voucher->id }}" class="mb-2" style="display:none;">
                            <div class="kasbon-summary-wrap">
                                <div class="kasbon-summary-header">
                                    <span><i class="fas fa-list-alt me-2"></i>Cash Advances &amp; LPJ
                                    <span class="badge bg-white text-success ms-2" id="kasbonSummaryCount-{{ $voucher->id }}">0 items</span></span>
                                    <button type="button" id="btnAddAll-{{ $voucher->id }}" class="btn btn-sm" style="background:#fff;color:#16a34a;border:1px solid #16a34a;font-weight:600;display:none;" onclick="addAllKasbon({{ $voucher->id }})">
                                        <i class="fas fa-plus-circle me-1"></i> Add All
                                    </button>
                                </div>
                                <div class="table-responsive p-0">
                                    <table class="table table-kasbon-summary mb-0">
                                        <thead><tr>
                                            <th>CA Number</th><th class="text-end">Amount</th>
                                            <th class="text-end">PJ / LPJ</th><th class="text-end">Balance</th>
                                            <th style="min-width:180px;">COA</th>
                                            <th style="min-width:220px;">Notes</th><th width="90" class="text-center">Action</th>
                                        </tr></thead>
                                        <tbody id="kasbonSummaryBody-{{ $voucher->id }}">
                                            <tr><td colspan="6" class="text-center text-muted py-3"><i class="fas fa-arrow-up me-1"></i> Select type to show cash advances</td></tr>
                                        </tbody>
                                        <tfoot id="kasbonSummaryFoot-{{ $voucher->id }}"></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /detailAddMode --}}

                    {{-- EDIT MODE --}}
                    <div id="detailEditMode-{{ $voucher->id }}" style="display:none;" class="detail-edit-mode-box">
                        <input type="hidden" id="editing_detail_id-{{ $voucher->id }}" value="">
                        <div class="edit-info-grid mb-3">
                            <div class="edit-info-card"><div class="edit-info-lbl">Type</div>
                                <div class="edit-info-val"><span id="edit_jenis_badge-{{ $voucher->id }}" class="jenis-badge"></span></div></div>
                            <div class="edit-info-card"><div class="edit-info-lbl">JO</div>
                                <div class="edit-info-val" id="edit_jo_nomor-{{ $voucher->id }}">-</div></div>
                            <div class="edit-info-card"><div class="edit-info-lbl">CA No.</div>
                                <div class="edit-info-val" id="edit_kasbon_nomor-{{ $voucher->id }}">-</div></div>
                            <div class="edit-info-card"><div class="edit-info-lbl">CA Amount</div>
                                <div class="edit-info-val clr-nilai" id="edit_nilai_display-{{ $voucher->id }}">-</div></div>
                            <div class="edit-info-card"><div class="edit-info-lbl">LPJ Amount</div>
                                <div class="edit-info-val clr-pj" id="edit_pj_display-{{ $voucher->id }}">-</div></div>
                            <div class="edit-info-card"><div class="edit-info-lbl">Balance</div>
                                <div class="edit-info-val" id="edit_selisih_display-{{ $voucher->id }}">-</div></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">COA</label>
                            <select id="edit_coa_detail-{{ $voucher->id }}" class="form-select select2-coa-detail-{{ $voucher->id }}" data-placeholder="-- Pilih COA --">
                                <option value=""></option>
                                @foreach($allChartOfAccounts as $c)
                                <option value="{{ $c->id_md_chart_of_account }}">{{ $c->no_account }} - {{ $c->account_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Custom type edit fields --}}
                        <div id="editCustomFields-{{ $voucher->id }}" style="display:none;">
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Release</label>
                                    <select id="edit_custom_release-{{ $voucher->id }}" class="form-select select2-edit-custom-release-{{ $voucher->id }}" data-placeholder="Pilih Release...">
                                        <option value=""></option>
                                        @foreach($releaseTos as $r)
                                        <option value="{{ $r->id_md_release }}">{{ $r->nama_release }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Department</label>
                                    <select id="edit_custom_dep-{{ $voucher->id }}" class="form-select select2-edit-custom-dep-{{ $voucher->id }}" data-placeholder="Pilih Department...">
                                        <option value=""></option>
                                        @foreach($departemens as $d)
                                        <option value="{{ $d->id_md_dep }}">{{ $d->nama_dep }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Cabang</label>
                                    <select id="edit_custom_branch-{{ $voucher->id }}" class="form-select select2-edit-custom-branch-{{ $voucher->id }}" data-placeholder="Pilih Cabang...">
                                        <option value=""></option>
                                        @foreach($branchs as $b)
                                        <option value="{{ $b->id_md_branch }}">{{ $b->nama_branch }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Nilai</label>
                                    <input type="number" id="edit_custom_nilai-{{ $voucher->id }}" class="form-control" placeholder="0" min="0" step="any">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">PJ</label>
                                    <input type="number" id="edit_custom_pj-{{ $voucher->id }}" class="form-control" placeholder="0" min="0" step="any">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" id="edit_keterangan-{{ $voucher->id }}" class="form-control" placeholder="Write notes...">
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-cancel-edit-style" onclick="cancelDetailEdit({{ $voucher->id }})">
                                <i class="fas fa-times me-1"></i> Cancel</button>
                            <button type="button" class="btn btn-add-purple" onclick="submitDetailEdit({{ $voucher->id }})">
                                <i class="fas fa-save me-1"></i> Update</button>
                        </div>
                    </div>
                </div>{{-- /add-form-section --}}

                {{-- DETAIL TABLE --}}
                <div class="table-responsive mt-2">
                    <table class="table table-detail table-bordered" id="detailTable-{{ $voucher->id }}">
                        <thead><tr>
                            <th width="40" class="text-center">No.</th>
                            <th width="120" class="text-center">JO</th>
                            <th style="min-width:150px;">CA No.</th>
                            <th style="min-width:160px;">Release / Dep &amp; Branch</th>
                            <th width="130">Category</th>
                            <th style="min-width:160px;">Item</th>
                            <th width="130">COA</th>
                            <th style="min-width:160px;" class="text-end">CA Amount (IDR)</th>
                            <th style="min-width:160px;" class="text-end">LPJ Amount (IDR)</th>
                            <th style="min-width:160px;" class="text-end">Balance (IDR)</th>
                            <th width="80" class="text-center">Action</th>
                        </tr></thead>
                        <tbody id="detailTableBody-{{ $voucher->id }}">
                            <tr><td colspan="11" class="text-center text-muted py-3"><i class="fas fa-circle-notch fa-spin me-1"></i> Loading...</td></tr>
                        </tbody>
                        <tfoot id="detailTableFoot-{{ $voucher->id }}"></tfoot>
                    </table>
                </div>

            </div>
        </div>
        @else
        <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>No payment record found. Please contact administrator.</div>
        @endif

        </div></div>
    </div>

    <div class="final-save-section">
        <div class="d-flex justify-content-between align-items-center">
            <div></div>
            <div class="d-flex gap-2">
                <a href="{{ route('mutasi-pembayaran.pdf', $mutasiPembayaran->id) }}" target="_blank"
                    class="btn"
                    style="padding:15px 40px; border-radius:12px; font-weight:700; font-size:1.1rem;
                           border-color:red; background-color:rgb(255,237,237); color:red;">
                    <i class="fas fa-file-pdf me-2"></i> Generate PDF
                </a>
                <a href="{{ route('mutasi-pembayaran.index') }}"
                    class="btn"
                    style="background:linear-gradient(135deg,#868686 0%,#5e5e5e 100%); border:none; color:white;
                           padding:15px 40px; border-radius:12px; font-weight:700; font-size:1.1rem;
                           box-shadow:0 6px 20px rgba(27,27,27,0.4);">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- MODAL: Kurs Detail --}}
    <div class="modal fade" id="modalKursDetail" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
            <div class="modal-content" style="border-radius:14px;border:none;overflow:hidden;">
                <div class="modal-header" style="background-color:#dcfce7;border:none;border-bottom:1px solid #bbf7d0;padding:16px 20px;">
                    <div>
                        <h6 class="modal-title mb-0" style="font-weight:700;color:#14532d;"><i class="fas fa-dollar-sign me-2"></i>Exchange Rate</h6>
                        <div style="font-size:.75rem;color:#16a34a;margin-top:2px;">Set currency exchange rate detail</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Currency</label>
                        <select id="kurs_mata_uang" class="form-select" style="border-radius:8px;">
                            <option value="IDR">IDR — Indonesian Rupiah</option>
                            <option value="USD">USD — US Dollar</option>
                            <option value="EUR">EUR — Euro</option>
                            <option value="SGD">SGD — Singapore Dollar</option>
                            <option value="MYR">MYR — Malaysian Ringgit</option>
                            <option value="JPY">JPY — Japanese Yen</option>
                            <option value="CNY">CNY — Chinese Yuan</option>
                            <option value="AUD">AUD — Australian Dollar</option>
                            <option value="GBP">GBP — British Pound</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Exchange Rate Value</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#f1f5f9;font-size:.85rem;">IDR</span>
                            <input type="text" id="kurs_nilai_input" class="form-control" placeholder="0,00" style="border-radius:0 8px 8px 0;">
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Rate Date</label>
                        <input type="date" id="kurs_tgl_input" class="form-control" style="border-radius:8px;">
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:12px 20px;gap:8px;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
                    <button type="button" class="btn text-white" onclick="saveKursDetail()" style="background:#16a34a;border-radius:8px;font-weight:600;">
                        <i class="fas fa-check me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: Confirm Delete Detail --}}
    <div class="modal fade" id="detailConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px;border:none;">
                <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:12px 12px 0 0;border:none;">
                    <h5 class="modal-title text-white fw-bold"><i class="fas fa-trash-alt me-2"></i>Hapus Detail</h5>
                </div>
                <div class="modal-body p-4" id="detailConfirmBody"><p>Delete this detail?</p></div>
                <div class="modal-footer border-0 gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger flex-fill fw-bold" id="btnConfirmDeleteDetail"><i class="fas fa-trash me-1"></i> Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: Custom Info --}}
    <div class="modal fade" id="modalCustomInfo" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header" style="background:#DCFCE6;border-radius:12px 12px 0 0;border:none;border-bottom:1px solid #bbf7d0;">
                    <h5 class="modal-title fw-bold" style="color:#14532d;"><i class="fas fa-info-circle me-2"></i>Custom Detail Info</h5>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="fw-semibold text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Release To</div>
                            <div class="fw-bold mt-1" id="ci-release" style="font-size:.9rem;">-</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-semibold text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Department</div>
                            <div class="fw-bold mt-1" id="ci-dept" style="font-size:.9rem;">-</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-semibold text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Branch / Cabang</div>
                            <div class="fw-bold mt-1" id="ci-cabang" style="font-size:.9rem;">-</div>
                        </div>
                        <div class="col-12">
                            <div class="fw-semibold text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">COA</div>
                            <div class="mt-1" id="ci-coa" style="font-size:.9rem;">-</div>
                        </div>
                    </div>
                    <div id="ci-keterangan-wrap" style="display:none;margin-top:14px;">
                        <div style="background:#fefce8;border:1px solid #fde047;border-radius:8px;padding:12px 16px;font-size:.84rem;">
                            <span style="color:#854d0e;font-weight:700;"><i class="fas fa-comment-alt me-2"></i>Notes:</span>
                            <span id="ci-keterangan" style="color:#1e293b;margin-left:4px;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: Kasbon Info --}}
    <div class="modal fade" id="modalKasbonInfo" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="modal-title"><i class="fas fa-book-open me-2"></i>Cash Advance Detail - <span id="ki-nomor-title"></span></div>
                        <div class="modal-subtitle" id="ki-item-count"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-black btn-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="ki-info-strip" id="ki-info-strip">
                    <div class="ki-info-cell"><div class="ki-info-lbl">Release</div><div class="ki-info-val" id="ki-release">-</div></div>
                    <div class="ki-info-cell"><div class="ki-info-lbl">Department</div><div class="ki-info-val" id="ki-dept">-</div></div>
                    <div class="ki-info-cell"><div class="ki-info-lbl">Branch</div><div class="ki-info-val" id="ki-cabang">-</div></div>
                    <div class="ki-info-cell"><div class="ki-info-lbl">CA Date</div><div class="ki-info-val" id="ki-tgl">-</div></div>
                    <div class="ki-info-cell"><div class="ki-info-lbl">Release Date</div><div class="ki-info-val" id="ki-tgl-release">-</div></div>
                </div>
                <div id="ki-keterangan-wrap" style="padding:14px 20px;display:none;">
                    <div style="background:#fefce8;border:1px solid #fde047;border-radius:8px;padding:12px 16px;font-size:.84rem;">
                        <span style="color:#854d0e;font-weight:700;"><i class="fas fa-comment-alt me-2"></i>Notes:</span>
                        <span id="ki-keterangan" style="color:#1e293b;margin-left:4px;"></span>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="ki-table">
                            <thead><tr>
                                <th style="width:44px;text-align:center;">No</th>
                                <th style="width:130px;">Category</th>
                                <th>Description</th>
                                <th class="num" style="width:155px;">CA Amount</th>
                                <th class="num" style="width:165px;">LPJ Amount</th>
                                <th class="num" style="width:145px;">Balance</th>
                            </tr></thead>
                            <tbody id="ki-tbody"><tr><td colspan="6" class="text-center text-muted py-4">-</td></tr></tbody>
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
        const mutasiId  = {{ $mutasiPembayaran->id }};
        const voucherId = {{ $voucher->id ?? 'null' }};
        const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
        const ALL_COAS = @json($allChartOfAccounts->map(fn($c) => ['id' => $c->id_md_chart_of_account, 'no' => $c->no_account, 'nama' => $c->account_name]));
        const ROUTES = {
            headerUpdate   : '/data/mutasi-pembayaran/header/update/' + mutasiId,
            detailStore    : '/data/mutasi-pembayaran/detail/store',
            detailShow     : '/data/mutasi-pembayaran/detail/show/',
            detailUpdate   : '/data/mutasi-pembayaran/detail/update/',
            detailDestroy  : '/data/mutasi-pembayaran/detail/destroy/',
            joOptions      : '/data/mutasi-pembayaran/jo-options',
            kasbonOptions  : '/data/mutasi-pembayaran/kasbon-options',
            joKasbonSummary: '/data/mutasi-pembayaran/jo-kasbon-summary',
            voucherDetails : '/data/mutasi-pembayaran/voucher/',
        };
        function showFloatingAlert(type,message){
            const a=$('#floatingBadgeAlert'),icon=$('#alertIcon'),text=$('#alertText');
            a.removeClass('alert-saving alert-success alert-error hiding');
            if(type==='saving'){a.addClass('alert-saving');icon.attr('class','fas fa-circle-notch fa-spin');}
            if(type==='success'){a.addClass('alert-success');icon.attr('class','fas fa-check-circle');}
            if(type==='error'){a.addClass('alert-error');icon.attr('class','fas fa-exclamation-circle');}
            text.text(message);a.addClass('show');
            if(type!=='saving')setTimeout(hideFloatingAlert,3000);
        }
        function hideFloatingAlert(){const a=$('#floatingBadgeAlert');a.addClass('hiding');setTimeout(()=>a.removeClass('show hiding'),400);}
        function formatNumber(v){return Number(v||0).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2});}
        function parseRupiah(v){return parseFloat(String(v||0).replace(/\./g,'').replace(',','.'))||0;}
        function formatRupiah(v){let s=String(v).replace(/[^\d,]/g,'').replace(/\./g,'');if(!s)return'';let[i,d]=s.split(',');if(d!==undefined&&d.length>2)d=d.substring(0,2);i=(i||'0').replace(/\B(?=(\d{3})+(?!\d))/g,'.');return d!==undefined?i+','+d:i+',00';}
        function escHtml(s){return!s?'':String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
        function escAttr(s){return!s?'':String(s).replace(/"/g,'&quot;');}

        // Track nomor baru yang di-fetch saat COA berubah
        let _newNomor = null;

        $(document).ready(function(){
            $('#edit_coa').select2({theme:'bootstrap-5',placeholder:'Select Bank / Cash account...',allowClear:true,width:'100%'});
            const kursEl=document.getElementById('edit_kurs_display');
            if(kursEl){
                kursEl.addEventListener('input',function(){this.value=formatRupiah(this.value);});
                kursEl.addEventListener('blur',function(){if(this.value&&!this.value.includes(','))this.value+=',00';});
            }
            $('#edit_kurs_display').on('input',function(){$('#edit_kurs_value').val(parseRupiah($(this).val()));});
            if(voucherId){initVoucherPanelSelects(voucherId);loadVoucherDetails(voucherId);}

            // Preview nomor saat COA berubah
            $('#edit_coa').on('change', function(){
                const val = $(this).val();
                const $disp = $('#edit_voucher_no_display');
                const $hint = $('#edit_voucher_no_hint');
                _newNomor = null;

                if(!val){
                    $hint.html('<i class="fas fa-info-circle me-1"></i>Nomor akan diperbarui jika tipe akun berubah');
                    return;
                }

                const paymentType = $('select#edit_coa option[value="'+val+'"]').data('payment-type')
                                 || $('select#edit_coa option[value="'+val+'"]').attr('data-payment-type');

                if(!paymentType){ return; }

                $hint.html('<i class="fas fa-circle-notch fa-spin me-1"></i>Mengambil preview nomor...');

                fetch('/data/mutasi-pembayaran/next-voucher-no?payment_type='+encodeURIComponent(paymentType), {
                    headers:{'X-Requested-With':'XMLHttpRequest'}
                })
                .then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
                .then(data=>{
                    if(data.success){
                        _newNomor = data.nomor;
                        $disp.val(data.nomor).css({'background-color':'#fef9c3','color':'#854d0e'});
                        $hint.html('<i class="fas fa-exclamation-triangle text-warning me-1"></i>Nomor akan berubah ke <strong>'+data.nomor+'</strong> saat disimpan');
                    }
                })
                .catch(()=>{
                    $hint.html('<i class="fas fa-info-circle me-1"></i>Nomor akan diperbarui jika tipe akun berubah');
                });
            });
        });

        $('#btnSaveHeader').on('click',function(){
            const tanggal=$('#edit_tanggal').val(),coaId=$('#edit_coa').val();
            if(!tanggal){showFloatingAlert('error','Date is required');return;}
            if(!coaId){showFloatingAlert('error','Bank / Cash account is required');return;}
            showFloatingAlert('saving','Menyimpan header...');

            const payload = {
                tanggal, id_md_chart_of_account:coaId,
                no_cek:$('#edit_no_cek').val(),
                kurs:$('#edit_kurs_value').val()||1,
                mata_uang:$('#edit_mata_uang_value').val()||'IDR',
                tgl_kurs:$('#edit_tgl_kurs_value').val()||'',
                memo:$('#edit_memo').val(),
                _token:CSRF
            };
            // Kirim nomor baru jika COA berubah
            if(_newNomor){ payload.new_nomor = _newNomor; }

            $.ajax({url:ROUTES.headerUpdate,method:'PUT', data:payload,
                success:function(r){
                    if(r.success){
                        showFloatingAlert('success','Header updated!');
                        if(r.nomor){
                            $('#edit_voucher_no_display').val(r.nomor).css({'background-color':'#f0fdf4','color':'#14532d'});
                            $('#edit_voucher_no_hint').html('<i class="fas fa-check-circle text-success me-1"></i>Nomor berhasil diperbarui');
                            _newNomor = null;
                        }
                    } else {
                        showFloatingAlert('error',r.message||'Gagal');
                    }
                },
                error:xhr=>{const msg=xhr.responseJSON?.errors?Object.values(xhr.responseJSON.errors).flat().join(', '):(xhr.responseJSON?.message||'Gagal');showFloatingAlert('error',msg);}
            });
        });

        function onJenisChange(vid){
            const jenis=$(`#input_jenis-${vid}`).val();
            $(`#input_jo-${vid}`).val('').trigger('change.select2');
            $(`#input_kasbon_gen_sel-${vid}`).val('').trigger('change.select2');
            $(`#joKasbonSummaryWrap-${vid}`).hide();
            $(`#kasbonSummaryBody-${vid}`).html('<tr><td colspan="6" class="text-center text-muted py-3">Select type to show cash advances</td></tr>');
            $(`#btnAddAll-${vid}`).hide();
            const $jc=$(`#jenisCol-${vid}`),$joc=$(`#joCol-${vid}`),$gc=$(`#genKasbonCol-${vid}`),$cc=$(`#customCol-${vid}`);
            $cc.hide();
            if(!jenis){$jc.removeClass('col-md-3').addClass('col-12');$joc.hide();$gc.hide();return;}
            $jc.removeClass('col-12').addClass('col-md-3');
            if(jenis==='custom'){
                $joc.hide();$gc.hide();
                $cc.removeClass('col-12').addClass('col-12').show();
                // init select2 for custom fields
                initCustomSelects(vid);
                return;
            }
            if(jenis==='general'){
                $joc.hide();$gc.removeClass('col-12').addClass('col-md-9').show();
                const $sel=$(`#input_kasbon_gen_sel-${vid}`);
                $sel.html('<option value=""></option>');
                $.ajax({url:ROUTES.kasbonOptions,method:'GET',data:{jenis:'general'},
                    success:function(r){let o='<option value=""></option>';if(r.success&&r.data)r.data.forEach(k=>{o+=`<option value="${k.id}">${k.nomor}${k.label?' - '+k.label:''}</option>`;});$sel.html(o).trigger('change.select2');}
                });
                return;
            }
            $gc.hide();$joc.removeClass('col-12').addClass('col-md-9').show();
            const $js=$(`#input_jo-${vid}`);
            $js.html('<option value=""></option>');
            $.ajax({url:ROUTES.joOptions,method:'GET',data:{jenis},
                success:function(r){let o='<option value=""></option>';if(r.success&&r.data)r.data.forEach(jo=>{o+=`<option value="${jo.id}">${jo.nomor}${jo.label?' - '+jo.label:''}</option>`;});$js.html(o).trigger('change.select2');}
            });
        }

        function initCustomSelects(vid){
            if(!$(`.select2-custom-release-${vid}`).hasClass('select2-hidden-accessible')){
                $(`.select2-custom-release-${vid}`).select2({theme:'bootstrap-5',placeholder:'Pilih Release...',allowClear:true,width:'100%'});
            }
            if(!$(`.select2-custom-coa-${vid}`).hasClass('select2-hidden-accessible')){
                $(`.select2-custom-coa-${vid}`).select2({theme:'bootstrap-5',placeholder:'Pilih COA...',allowClear:true,width:'100%'});
            }
            if(!$(`.select2-custom-dep-${vid}`).hasClass('select2-hidden-accessible')){
                $(`.select2-custom-dep-${vid}`).select2({theme:'bootstrap-5',placeholder:'Pilih Department...',allowClear:true,width:'100%'});
            }
            if(!$(`.select2-custom-branch-${vid}`).hasClass('select2-hidden-accessible')){
                $(`.select2-custom-branch-${vid}`).select2({theme:'bootstrap-5',placeholder:'Pilih Cabang...',allowClear:true,width:'100%'});
            }
        }

        function addCustomDetail(vid){
            const releaseId=$(`#input_custom_release-${vid}`).val();
            const coaId=$(`#input_custom_coa-${vid}`).val();
            const depId=$(`#input_custom_dep-${vid}`).val();
            const branchId=$(`#input_custom_branch-${vid}`).val();
            const nilai=$(`#input_custom_nilai-${vid}`).val();
            const pj=$(`#input_custom_pj-${vid}`).val();
            const ket=$(`#input_custom_keterangan-${vid}`).val();
            if(!releaseId){showFloatingAlert('error','Release wajib diisi');return;}
            if(!depId){showFloatingAlert('error','Department wajib diisi');return;}
            if(!branchId){showFloatingAlert('error','Cabang wajib diisi');return;}
            if(!nilai){showFloatingAlert('error','Nilai wajib diisi');return;}
            if(pj===''){showFloatingAlert('error','PJ wajib diisi');return;}
            const payload={
                id_mutasi_voucher:vid,
                jenis:'custom',
                custom_id_md_release:releaseId,
                id_md_chart_of_account_detail:coaId||null,
                custom_id_md_dep:depId,
                custom_id_md_branch:branchId,
                custom_nilai:nilai,
                custom_pj:pj,
                keterangan:ket,
                _token:CSRF
            };
            showFloatingAlert('saving','Menambahkan custom detail...');
            $.ajax({url:ROUTES.detailStore,method:'POST',data:payload,
                success:function(r){
                    if(r.success){
                        showFloatingAlert('success','Custom detail berhasil ditambahkan');
                        // reset fields
                        $(`#input_custom_release-${vid}`).val('').trigger('change.select2');
                        $(`#input_custom_coa-${vid}`).val('').trigger('change.select2');
                        $(`#input_custom_dep-${vid}`).val('').trigger('change.select2');
                        $(`#input_custom_branch-${vid}`).val('').trigger('change.select2');
                        $(`#input_custom_nilai-${vid}`).val('');
                        $(`#input_custom_pj-${vid}`).val('');
                        $(`#input_custom_keterangan-${vid}`).val('');
                        loadVoucherDetails(vid);
                    } else {
                        showFloatingAlert('error',r.message||'Gagal menambahkan');
                    }
                },
                error:function(xhr){
                    const errs=xhr.responseJSON?.errors;
                    if(errs){const msg=Object.values(errs).flat().join('; ');showFloatingAlert('error',msg);}
                    else showFloatingAlert('error','Gagal menambahkan custom detail');
                }
            });
        }

        function onJoChange(vid){
            const joId=$(`#input_jo-${vid}`).val(),jenis=$(`#input_jenis-${vid}`).val();
            $(`#btnAddAll-${vid}`).hide();
            if(!joId){$(`#joKasbonSummaryWrap-${vid}`).hide();return;}
            $(`#joKasbonSummaryWrap-${vid}`).show();
            $(`#kasbonSummaryBody-${vid}`).html('<tr><td colspan="6" class="text-center py-3"><i class="fas fa-circle-notch fa-spin me-1"></i> Loading...</td></tr>');
            $.ajax({url:ROUTES.joKasbonSummary,method:'GET',data:{jenis,jo_id:joId,voucher_id:vid},
                success:r=>r.success?renderKasbonSummaryRows(vid,r.data||[],jenis,joId):$(`#kasbonSummaryBody-${vid}`).html('<tr><td colspan="6" class="text-center text-danger py-3">Gagal memuat data</td></tr>'),
                error:()=>$(`#kasbonSummaryBody-${vid}`).html('<tr><td colspan="6" class="text-center text-danger py-3">Terjadi kesalahan</td></tr>')
            });
        }

        function onGeneralKasbonChange(vid){
            const kasbonId=$(`#input_kasbon_gen_sel-${vid}`).val();
            $(`#btnAddAll-${vid}`).hide();
            if(!kasbonId){$(`#joKasbonSummaryWrap-${vid}`).hide();return;}
            $(`#joKasbonSummaryWrap-${vid}`).show();
            $(`#kasbonSummaryBody-${vid}`).html('<tr><td colspan="6" class="text-center py-3"><i class="fas fa-circle-notch fa-spin me-1"></i> Loading...</td></tr>');
            $.ajax({url:ROUTES.joKasbonSummary,method:'GET',data:{jenis:'general',kasbon_id:kasbonId},
                success:r=>r.success&&r.data?.length?renderKasbonSummaryRows(vid,r.data,'general',''):$(`#kasbonSummaryBody-${vid}`).html(`<tr><td colspan="6" class="text-center text-danger py-3">${r.message||'Gagal memuat'}</td></tr>`),
                error:()=>$(`#kasbonSummaryBody-${vid}`).html('<tr><td colspan="6" class="text-center text-danger py-3">Terjadi kesalahan</td></tr>')
            });
        }

        function renderKasbonSummaryRows(vid,kasbons,jenis,joId){
            $(`#kasbonSummaryCount-${vid}`).text(kasbons.length+' items');
            window._kasbonData=window._kasbonData||{};window._kasbonData[vid]=kasbons;
            if(!kasbons.length){$(`#kasbonSummaryBody-${vid}`).html('<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada kasbon</td></tr>');return;}
            let rows='',totN=0,totP=0,totS=0;
            kasbons.forEach((k,ki)=>{
                const dipakai=k.sudah_dipakai,sCls=k.selisih>0.001?'clr-pos':k.selisih<-0.001?'clr-neg':'clr-zero';
                totN+=parseFloat(k.nilai||0);totP+=parseFloat(k.pj||0);totS+=parseFloat(k.selisih||0);
                const ic=(k.items||[]).length;
                rows+=`<tr class="kasbon-parent-row ${dipakai?'sudah-dipakai':''}" data-voucher="${vid}" data-ki="${ki}">
<td><div class="d-flex align-items-center gap-2">
<button type="button" class="btn-toggle-items" data-voucher="${vid}" data-ki="${ki}" onclick="toggleKasbonItems(this)" ${ic===0?'disabled style="opacity:.3"':''}><i class="fas fa-chevron-right toggle-chev"></i></button>
<button type="button" class="btn-kasbon-info" onclick="showKasbonInfo(this)" data-nomor="${escAttr(k.nomor)}" data-rel="${escAttr(k.release||'')}" data-dep="${escAttr(k.departemen||'')}" data-cab="${escAttr(k.cabang||'')}" data-tgl="${escAttr(k.tgl_kasbon||'')}" data-tgl-rel="${escAttr(k.tgl_release||'')}" data-items="${escAttr(JSON.stringify(k.items||[]))}"><i class="fas fa-info"></i></button>
<span class="fw-semibold" style="font-size:.82rem;">${escHtml(k.nomor)}</span></div></td>
<td><div class="currency-group-footer-mp"><span class="currency-label-footer-mp">IDR</span><span class="value-footer-mp" style="color:#212529;">${formatNumber(k.nilai)}</span></div></td>
<td><div class="currency-group-footer-mp"><span class="currency-label-footer-mp">IDR</span><span class="value-footer-mp" style="color:#212529;">${formatNumber(k.pj)}</span></div></td>
<td><div class="currency-group-footer-mp"><span class="currency-label-footer-mp">IDR</span><span class="value-footer-mp ${sCls}">${formatNumber(k.selisih)}</span></div></td>
<td>${!dipakai?`<select class="form-select form-select-sm kasbon-coa-select" data-voucher="${vid}" data-ki="${ki}" style="min-width:170px;"><option value="">-- COA --</option>${ALL_COAS.map(c=>`<option value="${c.id}">${c.no} - ${c.nama}</option>`).join('')}</select>`:'<span class="clr-muted">-</span>'}</td>
<td>${!dipakai?`<input type="text" class="form-control form-control-sm kasbon-ket-input" style="min-width:200px;" data-voucher="${vid}" data-ki="${ki}" placeholder="Notes...">`:' <span class="clr-muted">-</span>'}</td>
<td class="text-center">${!dipakai?`<button type="button" class="btn-tambah-kasbon kasbon-add-btn" data-voucher="${vid}" data-ki="${ki}" data-kasbon-id="${escAttr(String(k.id_kasbon))}" data-jenis="${jenis}" data-jo-id="${escAttr(String(joId))}" onclick="addKasbonFromSummary(this)"><i class="fas fa-plus me-1"></i>Add</button>`:'<button class="btn btn-sm btn-secondary" disabled><i class="fas fa-check"></i></button>'}</td>
</tr>`;
                (k.items||[]).forEach((item,ii)=>{
                    const iS=parseFloat(item.selisih||0),iC=iS>0.001?'clr-pos':iS<-0.001?'clr-neg':'clr-zero';
                    rows+=`<tr class="kasbon-item-row ${dipakai?'sudah-dipakai':''}" data-parent-voucher="${vid}" data-parent-ki="${ki}" style="display:none;">
<td style="padding-left:56px;font-size:.78rem;color:#64748b;">${escHtml(item.label||'Item #'+(ii+1))}</td>
<td><div class="currency-group-footer-mp" style="font-size:.78rem;"><span class="currency-label-footer-mp" style="font-size:.72rem;padding:.35rem .5rem;">IDR</span><span class="value-footer-mp" style="color:#212529;">${formatNumber(item.nilai_kasbon)}</span></div></td>
<td><div class="currency-group-footer-mp" style="font-size:.78rem;"><span class="currency-label-footer-mp" style="font-size:.72rem;padding:.35rem .5rem;">IDR</span><span class="value-footer-mp" style="color:#212529;">${formatNumber(item.pj)}</span></div></td>
<td><div class="currency-group-footer-mp" style="font-size:.78rem;"><span class="currency-label-footer-mp" style="font-size:.72rem;padding:.35rem .5rem;">IDR</span><span class="value-footer-mp ${iC}">${formatNumber(item.selisih)}</span></div></td>
<td colspan="3"></td></tr>`;
                });
            });
            $(`#kasbonSummaryBody-${vid}`).html(rows);
            const canAdd=kasbons.filter(k=>!k.sudah_dipakai).length;
            $(`#btnAddAll-${vid}`).html(`<i class="fas fa-plus-circle me-1"></i> Add All (${canAdd})`).toggle(canAdd>0);
            const tSC=totS>0.001?'clr-pos':totS<-0.001?'clr-neg':'clr-zero';
            $(`#kasbonSummaryFoot-${vid}`).html(`<tr class="table-footer-mp">
<td class="text-end fw-bold" style="color:#111827;">TOTAL</td>
<td><div class="currency-group-footer-mp"><span class="currency-label-footer-mp">IDR</span><span class="value-footer-mp" style="color:#212529;">${formatNumber(totN)}</span></div></td>
<td><div class="currency-group-footer-mp"><span class="currency-label-footer-mp">IDR</span><span class="value-footer-mp" style="color:#212529;">${formatNumber(totP)}</span></div></td>
<td><div class="currency-group-footer-mp"><span class="currency-label-footer-mp">IDR</span><span class="value-footer-mp">${formatNumber(totS)}</span></div></td>
<td colspan="3"></td></tr>`);
        }

        function toggleKasbonItems(btn){
            const $b=$(btn),vid=$b.data('voucher'),ki=$b.data('ki');
            const $items=$(`#kasbonSummaryBody-${vid}`).find(`tr.kasbon-item-row[data-parent-voucher="${vid}"][data-parent-ki="${ki}"]`);
            const $chev=$b.find('.toggle-chev');
            if($items.first().is(':visible')){$items.slideUp(150);$chev.css('transform','rotate(0deg)');}
            else{$items.slideDown(150);$chev.css('transform','rotate(90deg)');}
        }

        function addKasbonFromSummary(btn){
            const $b=$(btn),vid=$b.data('voucher'),ki=$b.data('ki');
            const kasbonId=$b.data('kasbon-id'),jenis=$b.data('jenis'),joId=$b.data('jo-id');
            const ket=$(`#kasbonSummaryBody-${vid}`).find(`.kasbon-ket-input[data-voucher="${vid}"][data-ki="${ki}"]`).val()||'';
            const coa=$(`#kasbonSummaryBody-${vid}`).find(`.kasbon-coa-select[data-voucher="${vid}"][data-ki="${ki}"]`).val()||'';
            const payload={id_mutasi_voucher:vid,jenis,keterangan:ket,id_md_chart_of_account_detail:coa,_token:CSRF};
            if(jenis==='tramper'){payload.id_kasbon_tram=kasbonId;payload.id_jo_tram=joId;}
            if(jenis==='other'){payload.id_kasbon_other=kasbonId;payload.id_jo_other=joId;}
            if(jenis==='contract'){payload.id_kasbon_cont=kasbonId;payload.id_jo_cont=joId;}
            if(jenis==='general'){payload.id_kasbon_gen=kasbonId;}
            showFloatingAlert('saving','Menambahkan kasbon...');
            $b.prop('disabled',true).html('<i class="fas fa-spinner fa-spin"></i>');
            $.ajax({url:ROUTES.detailStore,method:'POST',data:payload,
                success:function(r){
                    if(!r.success){showFloatingAlert('error',r.message||'Gagal');$b.prop('disabled',false).html('<i class="fas fa-plus me-1"></i>Add');return;}
                    showFloatingAlert('success','Cash advance added!');
                    const $body=$(`#kasbonSummaryBody-${vid}`);
                    $body.find(`.kasbon-parent-row[data-voucher="${vid}"][data-ki="${ki}"]`).addClass('sudah-dipakai').find('.kasbon-ket-input').prop('disabled',true);
                    $body.find(`.kasbon-item-row[data-parent-voucher="${vid}"][data-parent-ki="${ki}"]`).addClass('sudah-dipakai');
                    $b.replaceWith('<button class="btn btn-sm btn-success" disabled><i class="fas fa-check me-1"></i>Added</button>');
                    loadVoucherDetails(vid);
                },
                error:xhr=>{const msg=xhr.responseJSON?.errors?Object.values(xhr.responseJSON.errors).flat().join(', '):(xhr.responseJSON?.message||'Gagal');showFloatingAlert('error',msg);$b.prop('disabled',false).html('<i class="fas fa-plus me-1"></i>Add');}
            });
        }

        async function addAllKasbon(vid){
            const jenis=$(`#input_jenis-${vid}`).val(),joId=$(`#input_jo-${vid}`).val();
            const all=window._kasbonData?.[vid]||[],avail=all.filter(k=>!k.sudah_dipakai);
            if(!avail.length){showFloatingAlert('error','Semua kasbon sudah ditambahkan');return;}
            const conf=await Swal.fire({title:'Tambah Semua?',html:`<p>Akan menambahkan <strong>${avail.length} kasbon</strong>.</p>`,icon:'question',showCancelButton:true,confirmButtonColor:'#16a34a',confirmButtonText:'<i class="fas fa-plus-circle me-1"></i> Ya, Tambah Semua',cancelButtonText:'Batal'});
            if(!conf.isConfirmed)return;
            const $btn=$(`#btnAddAll-${vid}`);
            $btn.prop('disabled',true).html('<i class="fas fa-spinner fa-spin me-1"></i>...');
            showFloatingAlert('saving',`Menambahkan ${avail.length} kasbon...`);
            let ok=0,fail=0;
            for(const k of avail){
                const ki=all.indexOf(k);
                const coaSel=$(`#kasbonSummaryBody-${vid}`).find(`.kasbon-coa-select[data-voucher="${vid}"][data-ki="${ki}"]`).val()||'';
                const payload={id_mutasi_voucher:vid,jenis,keterangan:'',id_md_chart_of_account_detail:coaSel,_token:CSRF};
                if(jenis==='tramper'){payload.id_kasbon_tram=k.id_kasbon;payload.id_jo_tram=joId;}
                if(jenis==='other'){payload.id_kasbon_other=k.id_kasbon;payload.id_jo_other=joId;}
                if(jenis==='contract'){payload.id_kasbon_cont=k.id_kasbon;payload.id_jo_cont=joId;}
                if(jenis==='general'){payload.id_kasbon_gen=k.id_kasbon;}
                try{
                    const r=await $.ajax({url:ROUTES.detailStore,method:'POST',data:payload});
                    if(r.success){ok++;const $body=$(`#kasbonSummaryBody-${vid}`);
                        $body.find(`.kasbon-parent-row[data-voucher="${vid}"][data-ki="${ki}"]`).addClass('sudah-dipakai').find('.kasbon-add-btn').replaceWith('<button class="btn btn-sm btn-success" disabled><i class="fas fa-check"></i></button>');
                        $body.find(`.kasbon-item-row[data-parent-voucher="${vid}"][data-parent-ki="${ki}"]`).addClass('sudah-dipakai');
                        all[ki].sudah_dipakai=true;
                    }else fail++;
                }catch(e){fail++;}
            }
            loadVoucherDetails(vid);
            $btn.prop('disabled',false).html('<i class="fas fa-check-circle me-1"></i>Done');
            fail===0?showFloatingAlert('success',`${ok} kasbon berhasil ditambahkan!`):showFloatingAlert('error',`${ok} berhasil, ${fail} gagal`);
        }

        function editDetail(detailId,vid){
            showFloatingAlert('saving','Loading...');
            $.ajax({url:ROUTES.detailShow+detailId,method:'GET',
                success:function(r){
                    if(!r.success){showFloatingAlert('error','Gagal memuat');return;}
                    hideFloatingAlert();
                    const d=r.data;
                    $(`#detailAddMode-${vid}`).hide();
                    $(`#detailEditMode-${vid}`).show();
                    $(`#addDetailSection-${vid}`).addClass('edit-mode');
                    $(`#detailFormTitle-${vid}`).html('<i class="fas fa-edit" style="color:#3b82f6;"></i> Edit Detail Notes');
                    $(`#editing_detail_id-${vid}`).val(detailId);
                    $(`#edit_jenis_badge-${vid}`).attr('class',`jenis-badge jenis-${d.jenis}`).text(d.jenis);
                    $(`#edit_jo_nomor-${vid}`).text(d.nomor_jo||d.jo?.nomor||'-');
                    $(`#edit_kasbon_nomor-${vid}`).text(d.nomor_kasbon||d.kasbon?.nomor||'-');
                    $(`#edit_nilai_display-${vid}`).text('IDR '+formatNumber(d.nilai||0));
                    $(`#edit_pj_display-${vid}`).text('IDR '+formatNumber(d.pj||0));
                    const sel=parseFloat(d.selisih||0);
                    $(`#edit_selisih_display-${vid}`).text('IDR '+formatNumber(sel)).removeClass('clr-pos clr-neg clr-zero').addClass(sel>0.001?'clr-pos':sel<-0.001?'clr-neg':'clr-zero');
                    $(`#edit_keterangan-${vid}`).val(d.keterangan||'');
                    $(`#edit_coa_detail-${vid}`).val(d.id_md_chart_of_account_detail||'').trigger('change.select2');
                    // Custom type fields
                    if(d.jenis==='custom'){
                        $(`#editCustomFields-${vid}`).show();
                        // init select2 if needed
                        [['select2-edit-custom-release-','edit_custom_release','Pilih Release...'],
                         ['select2-edit-custom-dep-','edit_custom_dep','Pilih Department...'],
                         ['select2-edit-custom-branch-','edit_custom_branch','Pilih Cabang...']
                        ].forEach(([cls,id,ph])=>{
                            if(!$(`.${cls}${vid}`).hasClass('select2-hidden-accessible'))
                                $(`.${cls}${vid}`).select2({theme:'bootstrap-5',placeholder:ph,allowClear:true,width:'100%'});
                        });
                        $(`#edit_custom_release-${vid}`).val(d.custom_id_md_release||'').trigger('change.select2');
                        $(`#edit_custom_dep-${vid}`).val(d.custom_id_md_dep||'').trigger('change.select2');
                        $(`#edit_custom_branch-${vid}`).val(d.custom_id_md_branch||'').trigger('change.select2');
                        $(`#edit_custom_nilai-${vid}`).val(d.custom_nilai!==null?d.custom_nilai:'');
                        $(`#edit_custom_pj-${vid}`).val(d.custom_pj!==null?d.custom_pj:'');
                    } else {
                        $(`#editCustomFields-${vid}`).hide();
                    }
                    $('html,body').animate({scrollTop:$(`#addDetailSection-${vid}`).offset().top-100},400);
                },
                error:()=>showFloatingAlert('error','Gagal memuat detail')
            });
        }

        function cancelDetailEdit(vid){
            $(`#editing_detail_id-${vid}`).val('');
            $(`#detailFormTitle-${vid}`).html('<i class="fas fa-plus-square" style="color:#16a34a;"></i> Add Detail');
            $(`#addDetailSection-${vid}`).removeClass('edit-mode');
            $(`#detailEditMode-${vid}`).hide();
            $(`#detailAddMode-${vid}`).show();
            $(`#input_jenis-${vid}`).val('');
            onJenisChange(vid);
        }

        function submitDetailEdit(vid){
            const detailId=$(`#editing_detail_id-${vid}`).val();
            showFloatingAlert('saving','Updating...');
            const jenisEditing=$(`#edit_jenis_badge-${vid}`).text().trim();
            const payload={
                keterangan:$(`#edit_keterangan-${vid}`).val(),
                id_md_chart_of_account_detail:$(`#edit_coa_detail-${vid}`).val(),
                _method:'PUT',_token:CSRF
            };
            if(jenisEditing==='custom'){
                payload.custom_id_md_release=$(`#edit_custom_release-${vid}`).val();
                payload.custom_id_md_dep=$(`#edit_custom_dep-${vid}`).val();
                payload.custom_id_md_branch=$(`#edit_custom_branch-${vid}`).val();
                payload.custom_nilai=$(`#edit_custom_nilai-${vid}`).val();
                payload.custom_pj=$(`#edit_custom_pj-${vid}`).val();
            }
            $.ajax({url:ROUTES.detailUpdate+detailId,method:'POST',
                data:payload,
                success:function(r){
                    if(!r.success){showFloatingAlert('error',r.message||'Gagal');return;}
                    showFloatingAlert('success','Detail updated!');
                    cancelDetailEdit(vid);loadVoucherDetails(vid);
                },
                error:xhr=>showFloatingAlert('error',xhr.responseJSON?.message||'Gagal')
            });
        }

        let _delDetailId=null,_delVoucherId=null;
        function deleteDetail(detailId,vid){
            _delDetailId=detailId;_delVoucherId=vid;
            $('#detailConfirmBody').html('<p>Yakin ingin menghapus detail ini?</p>');
            new bootstrap.Modal(document.getElementById('detailConfirmModal')).show();
        }
        $('#btnConfirmDeleteDetail').on('click',function(){
            if(!_delDetailId)return;
            const did=_delDetailId,vid=_delVoucherId;
            bootstrap.Modal.getInstance(document.getElementById('detailConfirmModal')).hide();
            showFloatingAlert('saving','Menghapus...');
            $.ajax({url:ROUTES.detailDestroy+did,method:'DELETE',data:{_token:CSRF},
                success:r=>r.success?(showFloatingAlert('success','Deleted!'),loadVoucherDetails(vid)):showFloatingAlert('error',r.message||'Gagal'),
                error:xhr=>showFloatingAlert('error',xhr.responseJSON?.message||'Gagal menghapus')
            });
            _delDetailId=_delVoucherId=null;
        });

        function loadVoucherDetails(vid){
            $.ajax({url:ROUTES.voucherDetails+vid+'/details',method:'GET',
                success:r=>r.success?renderDetailTable(vid,r.data):$(`#detailTableBody-${vid}`).html('<tr><td colspan="11" class="text-center text-danger py-3">Gagal memuat detail</td></tr>'),
                error:()=>$(`#detailTableBody-${vid}`).html('<tr><td colspan="11" class="text-center text-danger py-3">Gagal memuat detail</td></tr>')
            });
        }

        function renderDetailTable(vid,details){
            const $tbody=$(`#detailTableBody-${vid}`),$tfoot=$(`#detailTableFoot-${vid}`);
            if(!details||!details.length){
                $tbody.html('<tr><td colspan="11" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x d-block mb-2"></i>Belum ada detail</td></tr>');
                $tfoot.html('');updateDetailCountBadge(vid,0);return;
            }
            const groups={},order=[];
            details.forEach(d=>{
                let key;
                if(d.jenis==='custom') key='custom_'+d.id;
                else key=d.jo_id?'jo_'+d.jo_id:'gen_'+d.id;
                if(!groups[key]){groups[key]={nomor_jo:d.nomor_jo,jenis:d.jenis,rows:[]};order.push(key);}
                groups[key].rows.push(d);
            });
            let html='',totN=0,totP=0,totS=0,rowNo=0;
            order.forEach(key=>{
                const g=groups[key];
                let totalRows=0;
                g.rows.forEach(d=>{totalRows+=(d.items&&d.items.length>0)?d.items.length:1;});
                const joLabel=g.nomor_jo?escHtml(g.nomor_jo):'General';
                const joBadge=`<span class="jenis-badge jenis-${g.jenis}" style="font-size:.65rem;display:block;margin-bottom:4px;">${g.jenis}</span>`;
                let joRendered=false;
                g.rows.forEach(d=>{
                    const items=d.items||[],nom=escHtml(d.nomor_kasbon||'-');
                    const sCls=d.selisih>0.001?'clr-pos':d.selisih<-0.001?'clr-neg':'clr-zero';
                    const kr=items.length>0?items.length:1;
                    const aksi=`
<button class="btn btn-info btn-sm d-block mx-auto mb-1" onclick="showKasbonInfo(this)"
    data-nomor="${escAttr(d.nomor_kasbon||'')}" data-dep="${escAttr(d.departemen||d.custom_dep_nama||'-')}"
    data-cab="${escAttr(d.cabang||d.custom_branch_nama||'-')}" data-rel="${escAttr(d.release||d.custom_release_nama||'-')}"
    data-tgl="${escAttr(d.tgl_kasbon||'')}" data-tgl-rel="${escAttr(d.tgl_release||'')}"
    data-keterangan="${escAttr(d.keterangan||'')}" data-items="${escAttr(JSON.stringify(d.items||[]))}">
    <i class="bi bi-journal-text text-white"></i></button>
<button class="btn btn-primary btn-sm d-block mx-auto mb-1" onclick="editDetail('${d.id}',${vid})"><i class="fas fa-edit"></i></button>
<button class="btn btn-danger btn-sm d-block mx-auto" onclick="deleteDetail('${d.id}',${vid})"><i class="fas fa-trash"></i></button>`;
                    // ── Custom type: render single row ──
                    if(d.jenis==='custom'){
                        totN+=parseFloat(d.nilai||0);totP+=parseFloat(d.pj||0);totS+=parseFloat(d.selisih||0);
                        const coaBadge=d.coa_detail?`<span class="badge bg-light text-dark border" style="font-size:.75rem;">${escHtml(d.coa_detail.no)}</span><br><small class="text-muted">${escHtml(d.coa_detail.nama)}</small>`:'<span class="text-muted">-</span>';
                        const relNama=d.custom_release_nama?escHtml(d.custom_release_nama):'-';
                        const depNama=d.custom_dep_nama?escHtml(d.custom_dep_nama):'-';
                        const braNama=d.custom_branch_nama?escHtml(d.custom_branch_nama):'-';
                        const joBadgeCustom=`<span class="jenis-badge jenis-custom" style="font-size:.65rem;display:block;margin-bottom:4px;">custom</span>`;
                        const customAksi=`
<button class="btn btn-info btn-sm d-block mx-auto mb-1" onclick="showCustomInfo(this)"
    data-rel="${escAttr(d.custom_release_nama||'')}"
    data-dep="${escAttr(d.custom_dep_nama||'')}"
    data-cab="${escAttr(d.custom_branch_nama||'')}"
    data-coa-no="${escAttr(d.coa_detail?d.coa_detail.no:'')}"
    data-coa-nama="${escAttr(d.coa_detail?d.coa_detail.nama:'')}"
    data-keterangan="${escAttr(d.keterangan||'')}">
    <i class="bi bi-journal-text text-white"></i></button>
<button class="btn btn-primary btn-sm d-block mx-auto mb-1" onclick="editDetail('${d.id}',${vid})"><i class="fas fa-edit"></i></button>
<button class="btn btn-danger btn-sm d-block mx-auto" onclick="deleteDetail('${d.id}',${vid})"><i class="fas fa-trash"></i></button>`;
                        rowNo++;
                        html+=`<tr>
<td class="text-center align-middle fw-bold" style="font-size:.8rem;color:#64748b;">${rowNo}</td>
<td class="text-center align-middle" style="font-size:.78rem;font-weight:700;">${joBadgeCustom}</td>
<td style="padding-left:10px;font-weight:600;font-size:.83rem;vertical-align:middle;">-</td>
<td style="font-size:.82rem;vertical-align:middle;">${relNama}<br><small class="text-muted">${depNama} / ${braNama}</small></td>
<td class="text-muted" style="font-size:.8rem;vertical-align:middle;">-</td>
<td class="text-muted" style="font-size:.8rem;vertical-align:middle;">${escHtml(d.keterangan||'-')}</td>
<td style="vertical-align:middle;font-size:.8rem;">${coaBadge}</td>
<td class="num-cell" style="color:#212529;vertical-align:middle;">${formatNumber(d.nilai)}</td>
<td class="num-cell" style="color:#212529;vertical-align:middle;">${formatNumber(d.pj)}</td>
<td class="num-cell ${sCls}" style="vertical-align:middle;">${formatNumber(d.selisih)}</td>
<td class="text-center" style="vertical-align:middle;">${customAksi}</td></tr>`;
                        return;
                    }
                    if(!items.length){
                        totN+=parseFloat(d.nilai||0);totP+=parseFloat(d.pj||0);totS+=parseFloat(d.selisih||0);
                        const joc=!joRendered?`<td rowspan="${totalRows}" class="text-center align-middle" style="font-size:.78rem;font-weight:700;">${joBadge}${joLabel}</td>`:'';
                        joRendered=true;
                        rowNo++;
                        const relDepBra=`<span class="text-muted" style="font-size:.78rem;">${escHtml(d.release||d.departemen||'-')}</span>`;
                        const coaNoItems=d.coa_detail?`<span class="badge bg-light text-dark border" style="font-size:.75rem;">${escHtml(d.coa_detail.no)}</span><br><small class="text-muted">${escHtml(d.coa_detail.nama)}</small>`:'<span class="text-muted">-</span>';
                        const noc=`<td class="text-center align-middle fw-bold" style="font-size:.8rem;color:#64748b;">${rowNo}</td>`;
                        html+=`<tr>${noc}${joc}<td style="padding-left:16px;font-weight:600;font-size:.83rem;vertical-align:middle;">${nom}</td>
<td style="vertical-align:middle;font-size:.8rem;">${relDepBra}</td>
<td class="text-muted" style="font-size:.8rem;vertical-align:middle;">-</td>
<td class="text-muted" style="font-size:.8rem;vertical-align:middle;">-</td>
<td style="vertical-align:middle;font-size:.8rem;">${coaNoItems}</td>
<td class="num-cell" style="color:#212529;vertical-align:middle;">${formatNumber(d.nilai)}</td>
<td class="num-cell" style="color:#212529;vertical-align:middle;">${formatNumber(d.pj)}</td>
<td class="num-cell ${sCls}" style="vertical-align:middle;">${formatNumber(d.selisih)}</td>
<td class="text-center" style="vertical-align:middle;">${aksi}</td></tr>`;
                    } else {
                        items.forEach(item=>{totN+=parseFloat(item.nilai_kasbon||0);totP+=parseFloat(item.pj||0);totS+=parseFloat(item.selisih||0);});
                        const catG=[];
                        items.forEach(item=>{
                            const cat=item.kategori||'-',last=catG[catG.length-1];
                            if(last&&last.cat===cat)last.items.push(item);
                            else catG.push({cat,items:[item]});
                        });
                        let firstKasbon=true;
                        catG.forEach(cg=>{
                            cg.items.forEach((item,ii)=>{
                                const iS=parseFloat(item.selisih||0),iC=iS>0.001?'clr-pos':iS<-0.001?'clr-neg':'clr-zero';
                                const joc=!joRendered?`<td rowspan="${totalRows}" class="text-center align-middle" style="font-size:.78rem;font-weight:700;">${joBadge}${joLabel}</td>`:'';
                                if(!joRendered)joRendered=true;
                                if(firstKasbon)rowNo++;
                                const noc=firstKasbon?`<td rowspan="${kr}" class="text-center align-middle fw-bold" style="font-size:.8rem;color:#64748b;">${rowNo}</td>`:'';
                                const kasc=firstKasbon?`<td rowspan="${kr}" style="padding-left:16px;vertical-align:middle;font-weight:600;">${nom}</td>`:'';
                                const relDepBraCell=firstKasbon?`<td rowspan="${kr}" style="vertical-align:middle;font-size:.78rem;"><span class="text-muted">${escHtml(d.release||'-')}</span><br><small class="text-muted">${escHtml(d.departemen||'-')} / ${escHtml(d.cabang||'-')}</small></td>`:'';
                                const catc=ii===0?`<td rowspan="${cg.items.length}" class="text-muted" style="vertical-align:middle;font-size:.8rem;">${escHtml(cg.cat)}</td>`:'';
                                const coaKasbon=d.coa_detail?`<span class="badge bg-light text-dark border" style="font-size:.75rem;">${escHtml(d.coa_detail.no)}</span><br><small class="text-muted">${escHtml(d.coa_detail.nama)}</small>`:'<span class="text-muted">-</span>';
                                const coac=firstKasbon?`<td rowspan="${kr}" style="vertical-align:middle;font-size:.8rem;">${coaKasbon}</td>`:'';
                                const aksc=firstKasbon?`<td class="text-center" rowspan="${kr}" style="vertical-align:middle;">${aksi}</td>`:'';
                                html+=`<tr>${noc}${joc}${kasc}${relDepBraCell}${catc}<td style="font-size:.78rem;vertical-align:middle;">${escHtml(item.label||'-')}</td>
${coac}<td class="num-cell" style="color:#212529;vertical-align:middle;">${formatNumber(item.nilai_kasbon||0)}</td>
<td class="num-cell" style="color:#212529;vertical-align:middle;">${formatNumber(item.pj||0)}</td>
<td class="num-cell ${iC}" style="vertical-align:middle;">${formatNumber(item.selisih||0)}</td>
${aksc}</tr>`;
                                firstKasbon=false;
                            });
                        });
                    }
                });
            });
            $tbody.html(html);
            const tSC=totS>0.001?'clr-pos':totS<-0.001?'clr-neg':'clr-zero';
            $tfoot.html(`<tr class="table-footer-mp">
<td colspan="7" class="text-end" style="color:#fff;">GRAND TOTAL</td>
<td><div class="currency-group-footer-mp"><span class="currency-label-footer-mp">IDR</span><span class="value-footer-mp" style="color:#212529;">${formatNumber(totN)}</span></div></td>
<td><div class="currency-group-footer-mp"><span class="currency-label-footer-mp">IDR</span><span class="value-footer-mp" style="color:#212529;">${formatNumber(totP)}</span></div></td>
<td><div class="currency-group-footer-mp"><span class="currency-label-footer-mp">IDR</span><span class="value-footer-mp ${tSC}">${formatNumber(totS)}</span></div></td>
<td></td></tr>`);
            updateDetailCountBadge(vid,details.length);
        }

        function updateDetailCountBadge(vid,count){$(`.detail-count-badge-${vid}`).text(count+' detail');}

        function initVoucherPanelSelects(vid){
            $(`.select2-jo-${vid}`).select2({theme:'bootstrap-5',placeholder:'Pilih JO...',allowClear:true,width:'100%'});
            $(`.select2-gen-kasbon-${vid}`).select2({theme:'bootstrap-5',placeholder:'Pilih kasbon general...',allowClear:true,width:'100%'});
            $(`.select2-coa-detail-${vid}`).select2({theme:'bootstrap-5',placeholder:'-- Pilih COA --',allowClear:true,width:'100%'});
        }

        function showCustomInfo(btn){
            const d=btn.dataset;
            document.getElementById('ci-release').textContent=d.rel||'-';
            document.getElementById('ci-dept').textContent=d.dep||'-';
            document.getElementById('ci-cabang').textContent=d.cab||'-';
            const coaNo=d.coaNo||'',coaNama=d.coaNama||'';
            document.getElementById('ci-coa').innerHTML=coaNo?`<span class="badge bg-light text-dark border" style="font-size:.85rem;">${escHtml(coaNo)}</span> <span class="text-muted">${escHtml(coaNama)}</span>`:'-';
            const kw=document.getElementById('ci-keterangan-wrap'),ket=d.keterangan||'';
            if(ket){document.getElementById('ci-keterangan').textContent=ket;kw.style.display='block';}
            else kw.style.display='none';
            new bootstrap.Modal(document.getElementById('modalCustomInfo')).show();
        }

        function showKasbonInfo(btn){
            const d=btn.dataset;
            document.getElementById('ki-nomor-title').textContent=d.nomor||'-';
            document.getElementById('ki-release').textContent=d.rel||'-';
            document.getElementById('ki-dept').textContent=d.dep||'-';
            document.getElementById('ki-cabang').textContent=d.cab||'-';
            document.getElementById('ki-tgl').textContent=d.tgl||'-';
            document.getElementById('ki-tgl-release').textContent=d.tglRel||'-';
            const kw=document.getElementById('ki-keterangan-wrap'),ket=d.keterangan||'';
            if(ket){document.getElementById('ki-keterangan').textContent=ket;kw.style.display='block';}
            else kw.style.display='none';
            let items=[];
            try{items=JSON.parse(d.items||'[]');}catch(e){}
            document.getElementById('ki-item-count').textContent=items.length+' item';
            const tbody=document.getElementById('ki-tbody'),tfoot=document.getElementById('ki-tfoot');
            if(!items.length){tbody.innerHTML='<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada item</td></tr>';tfoot.innerHTML='';}
            else{
                let totN=0,totP=0,totS=0,html='',rowNo=0;
                const groups=[];
                items.forEach(item=>{
                    const last=groups[groups.length-1];
                    if(last&&last.cat===(item.kategori||'-'))last.rows.push(item);
                    else groups.push({cat:item.kategori||'-',rows:[item]});
                });
                groups.forEach(g=>{
                    g.rows.forEach((item,ii)=>{
                        rowNo++;
                        const n=parseFloat(item.nilai_kasbon||0),pv=parseFloat(item.pj||0),s=parseFloat(item.selisih||0);
                        totN+=n;totP+=pv;totS+=s;
                        const sC=s>0.001?'clr-pos':s<-0.001?'clr-neg':'clr-zero';
                        html+='<tr>';
                        if(ii===0){html+=`<td rowspan="${g.rows.length}" class="text-center fw-bold" style="vertical-align:middle;">${rowNo}</td><td rowspan="${g.rows.length}" class="text-muted" style="vertical-align:middle;background:#f0fdf4;">${escHtml(g.cat)}</td>`;}
                        html+=`<td style="font-size:.8rem;">${escHtml(item.label||'-')}</td><td class="num">${formatNumber(n)}</td><td class="num">${formatNumber(pv)}</td><td class="num ${sC}">${formatNumber(s)}</td></tr>`;
                    });
                });
                tbody.innerHTML=html;
                tfoot.innerHTML=`<tr><td colspan="3" class="text-end fw-bold">GRAND TOTAL</td><td class="num">${formatNumber(totN)}</td><td class="num">${formatNumber(totP)}</td><td class="num">${formatNumber(totS)}</td></tr>`;
            }
            new bootstrap.Modal(document.getElementById('modalKasbonInfo')).show();
        }

        /* ══ MODAL KURS ══ */
        $('#modalKursDetail').on('show.bs.modal', function(){
            const mu  = $('#edit_mata_uang_value').val() || 'IDR';
            const val = $('#edit_kurs_value').val() || '';
            const tgl = $('#edit_tgl_kurs_value').val() || '';
            $('#kurs_mata_uang').val(mu);
            const numVal = parseFloat(val);
            if(numVal && numVal !== 1 && mu !== 'IDR'){
                // Tampilkan tanpa trailing zeros — format 2 desimal, strip jika .00
                const fmt = numVal % 1 === 0
                    ? numVal.toLocaleString('id-ID')
                    : numVal.toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2});
                $('#kurs_nilai_input').val(fmt);
                $('#kurs_nilai_input').prop('readonly', false);
            } else {
                $('#kurs_nilai_input').val(mu === 'IDR' ? '1' : '').prop('readonly', mu === 'IDR');
            }
            $('#kurs_tgl_input').val(tgl);
        });

        $('#kurs_mata_uang').on('change', function(){
            const mu = $(this).val();
            if(mu === 'IDR'){
                $('#kurs_nilai_input').val('1').prop('readonly', true).removeClass('is-invalid');
            } else {
                $('#kurs_nilai_input').val('').prop('readonly', false).focus();
            }
        });

        $('#kurs_nilai_input').on('input', function(){
            $(this).val($(this).val().replace(/[^0-9,]/g, ''));
        });

        window.saveKursDetail = function(){
            const mu  = $('#kurs_mata_uang').val();
            const raw = $('#kurs_nilai_input').val().replace(',', '.');
            const val = parseFloat(raw);
            const tgl = $('#kurs_tgl_input').val();

            if(!val || val <= 0){
                $('#kurs_nilai_input').addClass('is-invalid').focus();
                return;
            }
            $('#kurs_nilai_input').removeClass('is-invalid');

            $('#edit_kurs_value').val(val);
            $('#edit_mata_uang_value').val(mu);
            $('#edit_tgl_kurs_value').val(tgl);

            const fmt = val.toLocaleString('id-ID', {minimumFractionDigits:2, maximumFractionDigits:2});
            $('#edit_kurs_display').val(mu === 'IDR' ? '' : fmt);

            let summary = mu !== 'IDR'
                ? '<i class="fas fa-info-circle me-1"></i>' + mu + ' @ ' + fmt + (tgl ? ' · ' + tgl.split('-').reverse().join('/') : '')
                : '<i class="fas fa-info-circle me-1"></i>Default: IDR (1:1)';
            $('#kurs_summary_text').html(summary);

            $('#modalKursDetail').modal('hide');
        };
    </script>
@endpush
