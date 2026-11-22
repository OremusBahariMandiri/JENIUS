@extends('layouts.app')

@section('title', 'Edit Invoice – ' . $invoice->no_invoice)

@php
    $invoiceItemsJsStr = json_encode($invoiceItemsJs);
    $coaListJsStr = json_encode($coaListJs);
@endphp

@push('styles')
<style>
.invoiceEditPage .card { border:none; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,.08); }
.invoiceEditPage .card-header { border-radius:10px 10px 0 0 !important; padding:1rem 1.5rem; font-weight:600; }
.invoiceEditPage .form-control:focus, .invoiceEditPage .form-select:focus { border-color:#10b981; box-shadow:0 0 0 .2rem rgba(16,185,129,.25); }
.required-field::after { content:" *"; color:#dc3545; font-weight:600; }

/* FLOATING ALERT */
.floating-badge-alert { position:fixed; top:80px; right:30px; z-index:9999; min-width:260px; padding:15px 20px; border-radius:12px; box-shadow:0 8px 25px rgba(0,0,0,.2); display:none; animation:slideInRight .4s ease-out; }
.floating-badge-alert.show { display:flex; align-items:center; gap:12px; }
.floating-badge-alert.alert-saving { background:linear-gradient(135deg,#fbbf24,#f59e0b); color:#fff; }
.floating-badge-alert.alert-success { background:linear-gradient(135deg,#10b981,#059669); color:#fff; }
.floating-badge-alert.alert-error { background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; }
.floating-badge-alert i { font-size:1.3rem; }
.floating-badge-alert .alert-text { flex:1; font-weight:600; font-size:.95rem; }
@keyframes slideInRight { from{transform:translateX(400px);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1} to{transform:translateX(400px);opacity:0} }
.floating-badge-alert.hiding { animation:slideOutRight .4s ease-in; }

/* CONFIRM MODAL */
.confirm-modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:10000; display:none; align-items:center; justify-content:center; backdrop-filter:blur(3px); }
.confirm-modal-overlay.show { display:flex; }
.confirm-modal-box { background:#fff; border-radius:16px; padding:32px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,.3); animation:modalIn .25s ease-out; }
@keyframes modalIn { from{transform:scale(.9);opacity:0} to{transform:scale(1);opacity:1} }
.confirm-modal-box .modal-icon { width:56px; height:56px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin:0 auto 16px; }
.confirm-modal-box .modal-icon.danger { background:#fee2e2; color:#dc2626; }
.confirm-modal-box .modal-icon.warning { background:#fef3c7; color:#d97706; }
.confirm-modal-box h5 { text-align:center; font-weight:700; color:#1f2937; margin-bottom:8px; }
.confirm-modal-box p { text-align:center; color:#6b7280; font-size:.9rem; margin-bottom:24px; }
.confirm-modal-box .modal-actions { display:flex; gap:10px; }
.confirm-modal-box .modal-actions .btn { flex:1; padding:10px; font-weight:600; }

/* INPUT CARD */
.input-item-card { border:2px dashed #10b981; border-radius:12px; padding:24px; margin-bottom:20px; display:none; }
.input-item-card.active { display:block; border:2px solid #3b82f6; background:#f0f7ff; box-shadow:0 0 0 4px rgba(59,130,246,0.08); }
.input-item-card .card-title-bar { display:flex; align-items:center; gap:10px; margin-bottom:20px; padding-bottom:14px; border-bottom:1px solid #bfdbfe; }
.input-item-card .card-title-bar i { color:#3b82f6; font-size:1rem; }
.input-item-card .card-title-bar span { font-weight:700; color:#1e40af; font-size:1rem; }
.info-field { background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:10px 14px; }
.info-field .info-label { font-size:.72rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
.info-field .info-value { font-size:.9rem; font-weight:700; color:#1e40af; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

/* CURRENCY GROUP */
.currency-group { display:flex; align-items:stretch; }
.currency-group .currency-label { background:#2c3e50; color:#fff; padding:0 14px; font-size:.7rem; border-radius:8px 0 0 8px; min-width:52px; text-align:center; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.currency-group .currency-input { border-radius:0 8px 8px 0 !important; border-left:none !important; font-size:1.05rem; font-weight:600; }
.currency-group .currency-input:focus { border-color:#3b82f6 !important; box-shadow:0 0 0 .2rem rgba(59,130,246,.25) !important; }
.btn-save-item-input { background:linear-gradient(135deg,#3b82f6,#2563eb); border:none; color:#fff; padding:10px 28px; border-radius:8px; font-weight:600; box-shadow:0 4px 12px rgba(59,130,246,.35); transition:all .25s; }
.btn-save-item-input:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(59,130,246,.45); color:white; }
.reimb-card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; display:flex; align-items:center; }
.reimb-card .form-check-input { width:1.1em; height:1.1em; margin-top:.18em; cursor:pointer; border-color:#93c5fd; }
.reimb-card .form-check-input:checked { background-color:#1e40af; border-color:#1e40af; }
.reimb-card .form-check-label { cursor:pointer; display:flex; flex-direction:column; gap:2px; padding-left:4px; }
.reimb-label-title { font-weight:600; font-size:.875rem; color:#1e40af; }
.reimb-label-desc { font-size:.75rem; color:#94a3b8; font-weight:400; }
.btn-cancel-item-input { background:#fff; border:1px solid #d1d5db; color:#6b7280; padding:10px 22px; border-radius:8px; font-weight:600; transition:all .25s; }
.btn-cancel-item-input:hover { background:#f3f4f6; color:#374151; }

/* TABLE */
.table-invoice thead th { background:#2c3e50; color:#fff; border:1px solid #2c3e50; padding:12px 10px; font-weight:600; font-size:.875rem; vertical-align:middle; text-align:center; white-space:nowrap; }
.table-invoice tbody td { border:1px solid #dee2e6; padding:10px 12px; vertical-align:middle; font-size:.875rem; background:#fff; }
.table-invoice tbody tr:hover td { background:#f0fdf4; }
.table-invoice tbody tr.tr-active td { background:#dcfce7 !important; }
.table-invoice .category-cell { background:#fff !important; font-weight:600; color:#000; text-align:center; vertical-align:middle !important; border-right:2px solid #dee2e6; }
.table-invoice tbody tr.row-tax td { background:#fffbeb !important; }
.table-invoice tbody tr.row-tax:hover td { background:#fef3c7 !important; }
.table-invoice tbody tr.row-tax .category-cell { color:#b45309; font-weight:600; }
.cell-right { text-align:right; }
.cell-center { text-align:center; }
.cell-readonly { background:#f8f9fa !important; color:#495057; font-weight:500; }
.coa-display-cell { min-width:170px; }
.coa-display-badge { display:block; font-size:.75rem; padding:4px 8px; border-radius:6px; background:#f0fdf4; border:1px solid #bbf7d0; color:#065f46; font-weight:600; white-space:normal; word-break:break-word; text-align:center; }
.coa-empty-badge { display:block; font-size:.72rem; padding:3px 8px; border-radius:6px; background:#f3f4f6; border:1px dashed #d1d5db; color:#9ca3af; font-style:italic; text-align:center; }
.table-invoice tfoot td { background:#fff; color:#2c3e50; font-weight:700; padding:14px 12px; font-size:.9rem; border:1px solid #dee2e6; }
.footer-currency-wrap { display:flex; align-items:center; justify-content:space-between; width:100%; }
.footer-currency-label { background:#2c3e50; color:#fff; padding:4px 10px; border-radius:5px; font-size:.78rem; font-weight:700; min-width:44px; text-align:center; flex-shrink:0; }
.footer-value { color:#2c3e50; font-weight:700; font-size:.875rem; text-align:right; flex:1; min-width:0; white-space:nowrap; }
.currency-group-cell { display:flex; align-items:center; gap:5px; }
.currency-label-cell { background-color:#e9ecef; color:#495057; border:1px solid #ced4da; padding:0.2rem 0.5rem; font-size:0.78rem; border-radius:4px; min-width:42px; text-align:center; font-weight:600; white-space:nowrap; }
.value-cell { flex:1; text-align:right; font-weight:600; font-size:0.875rem; color:#2c3e50; }
.btn-edit-row { background:#10b981; border-color:#10b981; padding:.3rem .6rem; font-size:.8rem; border-radius:6px; transition:all .2s; color:#fff; }
.btn-edit-row:hover { background:#059669; border-color:#059669; transform:scale(1.08); color:#fff; }
.btn-clear-row { background:#ef4444; border-color:#ef4444; padding:.3rem .6rem; font-size:.8rem; border-radius:6px; transition:all .2s; color:#fff; }
.btn-clear-row:hover:not(:disabled) { background:#dc2626; border-color:#dc2626; transform:scale(1.08); }
.btn-clear-row:disabled { opacity:.35; cursor:not-allowed; }
.items-count-badge { background:#10b981; color:#fff; font-size:.75rem; font-weight:700; padding:2px 8px; border-radius:10px; margin-left:8px; }
.no-items-row td { text-align:center; padding:50px 20px; color:#6c757d; background:#f8f9fa !important; }
.reimb-badge { display:inline-block; font-size:.7rem; padding:2px 7px; border-radius:6px; font-weight:600; }
.reimb-badge.yes { background:#dbeafe; color:#1d4ed8; border:1px solid #bfdbfe; }
.reimb-badge.no  { background:#f3f4f6; color:#9ca3af; border:1px dashed #d1d5db; }
.validate-hint { font-size:.76rem; font-weight:600; margin-top:5px; min-height:18px; transition:color .2s; }
.validate-hint.hint-danger { color:#dc3545; }
.validate-hint.hint-ok { color:#10b981; }
.validate-hint.hint-info { color:#6b7280; }
.currency-input.input-invalid { border-color:#dc3545 !important; box-shadow:0 0 0 .2rem rgba(220,53,69,.2) !important; }
.currency-input.input-valid { border-color:#10b981 !important; box-shadow:0 0 0 .2rem rgba(16,185,129,.15) !important; }
.table-invoice thead th { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.table-invoice tbody td, .table-invoice tfoot td { overflow:hidden; }
.table-invoice tbody td:nth-child(2), .table-invoice tbody td:nth-child(3), .table-invoice tbody td.coa-display-cell, .table-invoice tbody td.category-cell { white-space:normal; word-break:break-word; }
.final-save-section { position:sticky; bottom:0; background:#fff; padding:18px 20px; box-shadow:0 -4px 20px rgba(0,0,0,.1); border-radius:12px 12px 0 0; margin-top:30px; z-index:100; }
.btn-final-back { background:linear-gradient(135deg,#868686,#5e5e5e); border:none; color:#fff; padding:13px 35px; border-radius:10px; font-weight:700; box-shadow:0 4px 15px rgba(27,27,27,.3); transition:all .3s; }
.btn-final-back:hover { transform:translateY(-2px); color:#fff; }
</style>
@endpush

@section('content')
{{-- Floating Alert --}}
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

{{-- JO Info Modal --}}
<div class="modal fade" id="joInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width:900px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;padding:1rem 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-file-contract me-2"></i>Job Order Info</h5>
                    <small id="joInfoSubtitle" style="opacity:.85;font-size:.8rem;"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="joInfoBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-success" role="status"></div>
                    <div class="mt-2 text-muted">Loading...</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4 d-flex justify-content-between align-items-center">
                <small class="text-muted" id="joInfoNote"></small>
                <button type="button" class="btn btn-danger fw-bold px-4" id="btnConfirmChangeJo" style="display:none;">
                    <i class="fas fa-sync-alt me-1"></i> Change JO & Reset Items
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid invoiceEditPage">

{{-- Page Header --}}
<div class="card shadow mb-4">
    <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color:#d1fae5">
        <span class="fw-bold">
            <i class="fas fa-file-invoice me-2" style="color:#059669;"></i>
            Edit Invoice &ndash; {{ $invoice->no_invoice }}
            <span class="badge bg-{{ $invoice->status === 'draft' ? 'warning text-dark' : ($invoice->status === 'closed' ? 'secondary' : 'success') }} ms-2" style="font-size:.7rem">{{ ucfirst($invoice->status) }}</span>
        </span>
        <a href="{{ route('invoice-contract.index') }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<input type="hidden" id="currentInvoiceId" value="{{ $invoice->id_invoice_contract }}">

{{-- HEADER CARD --}}
<div class="card shadow mb-4">
    <div class="card-header fw-bold" style="background-color:#d1fae5">
        <i class="fas fa-file-alt me-2"></i>Invoice Information
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            {{-- Row 1: Invoice Number | Status --}}
            <div class="col-md-6">
                <label class="form-label fw-semibold">Invoice Number</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:#10b981;border-color:#10b981;"><i class="fas fa-file-invoice text-white"></i></span>
                    <input type="text" id="hdrNoInvoice" class="form-control fw-bold" value="{{ $invoice->no_invoice }}" style="background:#e9ecef;color:#2c3e50;letter-spacing:1px;" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select id="hdrStatus" class="form-select">
                    <option value="draft"  {{ $invoice->status === 'draft'  ? 'selected' : '' }}>Draft</option>
                    <option value="sent"   {{ $invoice->status === 'sent'   ? 'selected' : '' }}>Sent</option>
                    <option value="closed" {{ $invoice->status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            {{-- Row 2: Job Order (col5) | Info Button (col1) | Title (col6) --}}
            <div class="col-md-5">
                <label class="form-label fw-semibold">Job Order</label>
                <select id="hdrJoSelect" class="form-select s2-jo-edit">
                    <option value="">-- Select Job Order --</option>
                    @foreach($joContracts as $jo)
                        <option value="{{ $jo->id_jo_cont }}"
                            data-title="{{ $jo->title }}"
                            data-customer="{{ $jo->contract?->customer?->customer ?? '' }}"
                            data-contract="{{ $jo->contract?->no_contract ?? '' }}"
                            {{ $invoice->id_jo_cont == $jo->id_jo_cont ? 'selected' : '' }}>
                            {{ $jo->no_jo_cont }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" id="btnJoInfo" class="btn btn-outline-info w-100" title="View JO Info" disabled>
                    <i class="fas fa-info-circle"></i>
                </button>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Title</label>
                <input type="text" id="hdrTitle" class="form-control" value="{{ $invoice->title }}" readonly style="background:#e9ecef;">
            </div>
            {{-- Row 3: Customer | Contract No --}}
            <div class="col-md-6">
                <label class="form-label fw-semibold">Customer</label>
                <input type="text" id="hdrToCustomer" class="form-control" value="{{ $invoice->to_customer }}" readonly style="background:#e9ecef;">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Contract No</label>
                <input type="text" id="hdrContractNo" class="form-control" value="{{ $invoice->contract_no }}" readonly style="background:#e9ecef;">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Invoice Date</label>
                <input type="date" id="hdrTglInvoice" class="form-control" value="{{ $invoice->tgl_invoice?->format('Y-m-d') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Due Date</label>
                <input type="date" id="hdrDueDate" class="form-control" value="{{ $invoice->due_date?->format('Y-m-d') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Vessel</label>
                <select id="hdrKapal" class="form-select s2-vessel-edit">
                    <option value="">-- Select Vessel --</option>
                    @foreach($vessels as $v)
                        <option value="{{ $v->vessel_name }}" {{ $invoice->kapal == $v->vessel_name ? 'selected' : '' }}>
                            {{ $v->vessel_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Jetty</label>
                <select id="hdrJetty" class="form-select s2-jetty-edit">
                    <option value="">-- Select Jetty --</option>
                    @foreach($ports as $p)
                        <option value="{{ $p->name_port }}" {{ $invoice->jetty == $p->name_port ? 'selected' : '' }}>
                            {{ $p->name_port }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">PO Number</label>
                <input type="text" id="hdrPoNumber" class="form-control" value="{{ $invoice->po_number }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Currency</label>
                <select id="hdrCurrency" class="form-select">
                    <option value="IDR" {{ $invoice->currency === 'IDR' ? 'selected' : '' }}>IDR</option>
                    <option value="USD" {{ $invoice->currency === 'USD' ? 'selected' : '' }}>USD</option>
                </select>
            </div>
            {{-- Kurs hidden inputs (managed via Kurs modal in Items card) --}}
            <input type="hidden" id="hdrKursInvoiceDisplay" value="{{ $invoice->kurs_invoice ? number_format((float)$invoice->kurs_invoice, 2, ',', '.') : '' }}">
            <input type="hidden" id="hdrKursInvoice" value="{{ $invoice->kurs_invoice ?? '' }}">
            <input type="hidden" id="hdrTglKursInvoice" value="{{ $invoice->tgl_kurs_invoice?->format('Y-m-d') }}">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Vendor Number</label>
                <input type="text" id="hdrVendorNumber" class="form-control" value="{{ $invoice->vendor_number }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">SA/GR Number</label>
                <input type="text" id="hdrSaGrNumber" class="form-control" value="{{ $invoice->sa_gr_number }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">BA Number</label>
                <input type="text" id="hdrBaNumber" class="form-control" value="{{ $invoice->ba_number }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Chart of Account</label>
                <select id="hdrCoa" class="form-select s2-coa-edit">
                    <option value="">— Select COA (optional) —</option>
                    @foreach($coas as $coa)
                        <option value="{{ $coa->id_md_chart_of_account }}"
                            {{ $invoice->id_md_chart_of_account == $coa->id_md_chart_of_account ? 'selected' : '' }}>
                            {{ $coa->no_account }} — {{ $coa->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">No. Voyage</label>
                <input type="text" id="hdrNoVoyage" class="form-control" value="{{ $invoice->no_voyage }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">GRT</label>
                <input type="text" id="hdrGrt" class="form-control" value="{{ $invoice->grt }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">ETA</label>
                <input type="date" id="hdrEta" class="form-control" value="{{ $invoice->eta?->format('Y-m-d') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">ETD</label>
                <input type="date" id="hdrEtd" class="form-control" value="{{ $invoice->etd?->format('Y-m-d') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Cond of Payment</label>
                <input type="text" id="hdrCondOfPayment" class="form-control" value="{{ $invoice->cond_of_payment }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Period</label>
                <input type="text" id="hdrPeriod" class="form-control" value="{{ $invoice->period }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Att</label>
                <input type="text" id="hdrAtt" class="form-control" value="{{ $invoice->att }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Address</label>
                <textarea id="hdrAddress" class="form-control" rows="2">{{ $invoice->address }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">VAT Number</label>
                <input type="text" id="hdrVatNumber" class="form-control" value="{{ $invoice->vat_number }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bar Code</label>
                <input type="text" id="hdrBarCode" class="form-control" value="{{ $invoice->bar_code }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Office Phone</label>
                <input type="text" id="hdrOfficePhone" class="form-control" value="{{ $invoice->office_phone }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Mobile Phone</label>
                <input type="text" id="hdrMobilePhone" class="form-control" value="{{ $invoice->mobile_phone }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Close Note</label>
                <textarea id="hdrCloseNote" class="form-control" rows="2">{{ $invoice->close_note }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Reopen Note</label>
                <textarea id="hdrReopenNote" class="form-control" rows="2">{{ $invoice->reopen_note }}</textarea>
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
    <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color:#d1fae5">
        <h6 class="mb-0">
            <i class="fas fa-list me-2" style="color:#059669;"></i>Invoice Items
            <span class="items-count-badge" id="itemsCountBadge">0</span>
        </h6>
        <div class="d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-sm" id="btnOpenKursModal"
                    style="background:#0369a1;border-color:#0369a1;color:#fff;"
                    title="Set exchange rate for USD conversion">
                <i class="fas fa-dollar-sign me-1"></i> Exchange Rate
                <span id="kursModalBadge" class="badge ms-1" style="background:#fff;color:#0369a1;font-size:.7rem;display:none;"></span>
            </button>
            <button type="button" class="btn btn-sm btn-primary" id="btnEditAllItems" style="background:#1e40af;border-color:#1e40af;">
                <i class="fas fa-pen-to-square me-1"></i> Edit All Items
            </button>
            <button type="button" class="btn btn-sm btn-danger" id="btnResetAllItems">
                <i class="fas fa-trash-alt me-1"></i> Reset All Items
            </button>
        </div>
    </div>
    <div class="card-body p-4">

        {{-- INPUT CARD --}}
        <div class="input-item-card" id="inputItemCard">
            <div class="card-title-bar">
                <i class="fas fa-pen-to-square"></i>
                <span>Input Invoice Amount</span>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="info-field">
                        <div class="info-label">Category</div>
                        <div class="info-value" id="infoCategory">&#8212;</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-field">
                        <div class="info-label">Item</div>
                        <div class="info-value" id="infoItem">&#8212;</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-field">
                        <div class="info-label">Sell Price (IDR)</div>
                        <div class="info-value" id="infoSellIdr">&#8212;</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-field">
                        <div class="info-label">Sell Price (USD)</div>
                        <div class="info-value" id="infoSellUsd">&#8212;</div>
                    </div>
                </div>
            </div>
            {{-- Kurs Info Box --}}
            <div id="kursInfoBox" class="mb-3 p-3 rounded-3 d-none" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                <div class="d-flex align-items-center gap-2">
                    <span style="background:#059669;color:#fff;padding:3px 10px;border-radius:6px;font-size:.75rem;font-weight:700;">
                        <i class="fas fa-dollar-sign me-1"></i>Rate: IDR <span id="kursInfoValue">0</span>
                    </span>
                    <small class="text-muted" id="kursInfoTgl"></small>
                    <small class="text-info ms-2"><i class="fas fa-info-circle me-1"></i>Exchange rate is set. Fill in USD to auto-calculate IDR, or fill IDR directly.</small>
                </div>
            </div>
            <div id="kursWarningBox" class="mb-3 p-3 rounded-3 d-none" style="background:#fef3c7;border:1px solid #fde68a;">
                <small class="fw-semibold" style="color:#b45309;"><i class="fas fa-exclamation-triangle me-1"></i>Exchange rate not set. Click <strong>Exchange Rate</strong> button to configure it before entering a USD amount.</small>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="color:#1e40af;font-size:.875rem;">
                        Invoice Amount (IDR) <span style="color:#dc3545;">*</span>
                    </label>
                    <div class="currency-group">
                        <span class="currency-label">IDR</span>
                        <input type="text" id="inputNominalInvoice" class="form-control currency-input" placeholder="0,00" autocomplete="off">
                    </div>
                    <div class="validate-hint hint-info" id="inputNominalHint"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="color:#1e40af;font-size:.875rem;">
                        Invoice Amount (USD)
                        <small class="fw-normal text-muted ms-1">— or fill IDR on the left</small>
                    </label>
                    <div class="currency-group">
                        <span class="currency-label" style="background:#0369a1;">USD</span>
                        <input type="text" id="inputNominalInvoiceUsd" class="form-control currency-input" placeholder="0.00" autocomplete="off">
                    </div>
                    <div class="validate-hint hint-info" id="inputNominalUsdHint"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="color:#1e40af;font-size:.875rem;">Chart of Account</label>
                    <select id="inputCoa" class="form-select select2-coa" style="font-size:.875rem;">
                        <option value="">&#8212; Select COA (optional) &#8212;</option>
                        @foreach ($coas as $coa)
                            <option value="{{ $coa->id_md_chart_of_account }}">{{ $coa->no_account }} &#8212; {{ $coa->account_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <div class="reimb-card">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="inputIsReimbursement" value="1">
                            <label class="form-check-label" for="inputIsReimbursement">
                                <span class="reimb-label-title"><i class="fas fa-rotate-left me-1"></i> Reimbursement</span>
                                <span class="reimb-label-desc">Check if this item is a reimbursement cost</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn btn-cancel-item-input" id="btnCancelItemInput">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-save-item-input" id="btnSaveItemInput">
                        <i class="fas fa-save me-1"></i> Save Invoice Amount
                    </button>
                </div>
            </div>
            <input type="hidden" id="activeItemId" value="">
            <input type="hidden" id="activeRowIndex" value="">
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-invoice table-bordered mb-0" style="table-layout:fixed;min-width:1000px;width:100%;">
                <colgroup>
                    <col style="width:38px;">
                    <col style="width:72px;">
                    <col style="width:130px;">
                    <col style="width:100px;">
                    <col style="width:155px;">
                    <col style="width:100px;">
                    <col style="width:155px;">
                    <col style="width:110px;">
                    <col style="width:60px;">
                    <col style="width:88px;">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Category</th>
                        <th class="text-start">Item</th>
                        <th class="text-center">Selling Price USD</th>
                        <th class="text-center">Selling Price IDR</th>
                        <th class="text-center">Invoice (USD)</th>
                        <th class="text-center">Invoice (IDR)</th>
                        <th class="text-center">COA</th>
                        <th class="text-center">Reimbursement</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="invoiceItemsBody">
                    <tr id="loadingRow">
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="fas fa-circle-notch fa-spin me-2"></i> Loading data...
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end pe-2"><strong>GRAND TOTAL</strong></td>
                        <td>
                            <div class="footer-currency-wrap">
                                <span class="footer-currency-label">USD</span>
                                <span class="footer-value" id="footerTotalSellUsd">0,00</span>
                            </div>
                        </td>
                        <td>
                            <div class="footer-currency-wrap">
                                <span class="footer-currency-label">IDR</span>
                                <span class="footer-value" id="footerTotalSellIdr">0,00</span>
                            </div>
                        </td>
                        <td>
                            <div class="footer-currency-wrap">
                                <span class="footer-currency-label" style="background:#2c3e50;">USD</span>
                                <span class="footer-value" id="footerTotalInvoiceUsd">0,00</span>
                            </div>
                        </td>
                        <td>
                            <div class="footer-currency-wrap">
                                <span class="footer-currency-label">IDR</span>
                                <span class="footer-value" id="footerTotalInvoice">0,00</span>
                            </div>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Summary --}}
        <div class="mt-3 p-3 rounded-3 border" style="background:#f8fafc;border-color:#e2e8f0!important;">
            <div class="d-flex gap-4 flex-wrap">
                <span class="fw-semibold" style="color:#065f46;">DPP: <strong id="summDpp" style="color:#10b981;">0</strong></span>
                <span class="fw-semibold" style="color:#b45309;">PPN: <strong id="summPpn" style="color:#d97706;">0</strong></span>
                <span class="fw-semibold" style="color:#1d4ed8;">Reimb: <strong id="summReimb" style="color:#3b82f6;">0</strong></span>
                <span class="fw-semibold ms-auto" style="color:#2c3e50;">Total: <strong id="summTotal" style="color:#2c3e50;font-size:1rem;">0</strong></span>
            </div>
        </div>

    </div>
</div>

{{-- MODAL EXCHANGE RATE (KURS) --}}
<div class="modal fade" id="kursModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#0369a1,#0284c7);color:#fff;padding:1rem 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>Exchange Rate (Kurs)
                    </h5>
                    <small style="opacity:.8;">USD ↔ IDR conversion rate for this invoice</small>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Rate <small class="text-muted fw-normal">(optional)</small></label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold" style="background:#2c3e50;color:#fff;min-width:52px;">IDR</span>
                        <input type="text" id="modalKursDisplay" class="form-control" placeholder="e.g. 16.500,00" autocomplete="off">
                    </div>
                    <small class="text-muted">Used to convert Invoice USD → IDR automatically</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Rate Date</label>
                    <input type="date" id="modalKursTgl" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm text-white fw-semibold px-4" id="btnSaveKurs"
                        style="background:#0369a1;border-color:#0369a1;">
                    <i class="fas fa-save me-1"></i> Save Rate
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT ALL ITEMS --}}
<div class="modal fade" id="editAllModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:720px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#1e40af,#2563eb);color:#fff;padding:1rem 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-pen-to-square me-2"></i>Edit All Items</h5>
                    <div style="font-size:.8rem;opacity:.85;margin-top:2px;">Edit invoice nominal for all items at once</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-success btn-sm fw-semibold w-100" id="btnCopyFromSellIdr">
                            <i class="fas fa-copy me-1"></i> Copy All from Selling Price IDR
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-info btn-sm fw-semibold w-100" id="btnCopyFromSellUsd">
                            <i class="fas fa-copy me-1"></i> Copy All from Selling Price USD
                        </button>
                    </div>
                </div>
                <div id="editAllBody"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary fw-bold px-4" id="btnSaveAllItems" style="background:#1e40af;border-color:#1e40af;">
                    <i class="fas fa-save me-1"></i> Save All
                </button>
            </div>
        </div>
    </div>
</div>


</div>{{-- end .container-fluid --}}

<div class="final-save-section">
    <div class="d-flex justify-content-between align-items-center">
        <div></div>
        <div class="d-flex gap-2">

            <a href="{{ route('invoice-contract.export.vda', $invoice->id_invoice_contract) }}"
               target="_blank"
               class="btn"
               style="padding:13px 30px;border-radius:10px;font-weight:700;font-size:1rem;background:#059669;color:#fff;border-color:#059669;">
                <i class="fas fa-ship me-2"></i> Export VDA
            </a>
            <a href="{{ route('invoice-contract.export.invoice', $invoice->id_invoice_contract) }}"
               target="_blank"
               class="btn"
               style="padding:13px 30px;border-radius:10px;font-weight:700;font-size:1rem;background:#059669;color:#fff;border-color:#059669;">
                <i class="fas fa-file-invoice me-2"></i> Export Invoice
            </a>
            <a href="{{ route('invoice-contract.export.reimbursement', $invoice->id_invoice_contract) }}"
               target="_blank"
               class="btn"
               style="padding:13px 30px;border-radius:10px;font-weight:700;font-size:1rem;background:#059669;color:#fff;border-color:#059669;">
                <i class="fas fa-redo me-2"></i> Export Reimbursement
            </a>
            <a href="{{ route('invoice-contract.export.kwitansi', $invoice->id_invoice_contract) }}"
               target="_blank"
               class="btn"
               style="padding:13px 30px;border-radius:10px;font-weight:700;font-size:1rem;background:#059669;color:#fff;border-color:#059669;">
                <i class="fas fa-receipt me-2"></i> Export Kwitansi
            </a>
            <a href="{{ route('invoice-contract.export.all', $invoice->id_invoice_contract) }}"
               target="_blank"
               class="btn"
               style="padding:13px 30px;border-radius:10px;font-weight:700;font-size:1rem;background:#059669;color:#fff;border-color:#059669;">
                <i class="fas fa-layer-group me-2"></i> Export All
            </a>
            <a href="{{ route('invoice-contract.index') }}" class="btn btn-final-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
.select2-coa + .select2-container { width:100% !important; }
.select2-coa + .select2-container .select2-selection--single { height:calc(1.5em + 0.75rem + 2px) !important; padding:0.375rem 0.75rem !important; border:1px solid #ced4da !important; border-radius:0.375rem !important; font-size:.875rem; }
.select2-coa + .select2-container .select2-selection--single .select2-selection__rendered { line-height:1.5 !important; padding-left:0 !important; color:#212529; font-size:.875rem; }
.select2-coa + .select2-container .select2-selection--single .select2-selection__arrow { height:100% !important; }
.select2-coa + .select2-container--bootstrap-5.select2-container--focus .select2-selection { border-color:#10b981 !important; box-shadow:0 0 0 .2rem rgba(16,185,129,.25) !important; }
</style>
<script>
const currentInvoiceId = {{ $invoice->id_invoice_contract }};
const csrfToken        = $('meta[name="csrf-token"]').attr('content');
const bulkSaveUrl      = '{{ route('invoice-contract.items.bulk-save', $invoice->id_invoice_contract) }}';
const updateHeaderUrl  = '{{ route('invoice-contract.header.update', $invoice->id_invoice_contract) }}';
let invoiceItems = {!! $invoiceItemsJsStr !!};
const coaList    = {!! $coaListJsStr !!};
let confirmCallback = null;

/* CONFIRM MODAL */
function showConfirm({title, desc, okLabel='Yes, proceed', okClass='btn-danger', iconClass='danger'}, cb) {
    $('#confirmModalTitle').text(title);
    $('#confirmModalDesc').text(desc);
    $('#confirmModalOk').text(okLabel).removeClass().addClass('btn ' + okClass);
    $('#confirmModalIcon').removeClass('danger warning').addClass(iconClass);
    confirmCallback = cb;
    $('#confirmModal').addClass('show');
}
$('#confirmModalCancel, #confirmModal').on('click', function(e) {
    if (e.target === this) { $('#confirmModal').removeClass('show'); confirmCallback = null; }
});
$('#confirmModalOk').on('click', function() {
    $('#confirmModal').removeClass('show');
    if (typeof confirmCallback === 'function') confirmCallback();
    confirmCallback = null;
});

/* FLOATING ALERT */
function showFloatingAlert(type, message) {
    const $a = $('#floatingBadgeAlert'), $i = $('#alertIcon');
    $a.removeClass('alert-saving alert-success alert-error hiding');
    if (type === 'saving')  { $a.addClass('alert-saving');  $i.attr('class','fas fa-circle-notch fa-spin'); }
    if (type === 'success') { $a.addClass('alert-success'); $i.attr('class','fas fa-check-circle'); }
    if (type === 'error')   { $a.addClass('alert-error');   $i.attr('class','fas fa-exclamation-circle'); }
    $('#alertText').text(message);
    $a.addClass('show');
    if (type !== 'saving') {
        setTimeout(() => { $a.addClass('hiding'); setTimeout(() => $a.removeClass('show hiding'), 400); }, 3000);
    }
}

/* NUMBER UTILS */
function formatRupiah(v) {
    let n = v.toString().replace(/[^\d,]/g,'').replace(/\./g,'');
    if (!n) return '';
    let [int, dec=''] = n.split(',');
    if (dec.length > 2) dec = dec.slice(0,2);
    int = int.replace(/\B(?=(\d{3})+(?!\d))/g,'.');
    return dec !== '' ? int+','+dec : int+',00';
}
function parseRupiah(v) {
    return parseFloat((v||'0').toString().replace(/\./g,'').replace(',','.')) || 0;
}
function fmtNum(v) { return parseFloat(v||0).toLocaleString('id-ID',{minimumFractionDigits:0,maximumFractionDigits:0}); }
function fmtNum2(v) { return parseFloat(v||0).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}); }

function setupRupiahInput(el) {
    if (!el) return;
    el.addEventListener('input', function() {
        const cur = this.selectionStart;
        const before = this.value.substring(0, cur);
        this.value = formatRupiah(this.value);
        let digits = before.replace(/\D/g,'').length, np=0, cnt=0;
        for (let i=0; i<this.value.length; i++) {
            if (/\d/.test(this.value[i])) { cnt++; if (cnt===digits) { np=i+1; break; } }
        }
        this.setSelectionRange(np,np);
    });
    el.addEventListener('blur', function() {
        if (this.value && !this.value.includes(',')) this.value += ',00';
    });
}

/* COA */
function buildCoaDisplay(index, coaId) {
    if (!coaId) return '<span id="coaDisplay_'+index+'" class="coa-empty-badge">— no COA —</span>';
    const coa = coaList.find(c => c.id == coaId);
    const label = coa ? coa.no+' - '+coa.name : coaId;
    return '<span id="coaDisplay_'+index+'" class="coa-display-badge">'+label+'</span>';
}

/* GROUP BY CATEGORY */
function groupByCategory(items) {
    const groups = {}, order = [];
    items.forEach(item => {
        const cat = item.invoice_ctg || 'Uncategorized';
        if (!groups[cat]) { groups[cat]=[]; order.push(cat); }
        groups[cat].push(item);
    });
    const taxIdx = order.indexOf('__TAX__');
    if (taxIdx > 0) { order.splice(taxIdx,1); order.push('__TAX__'); }
    return {groups, order};
}

/* TOTALS */
function recalcTotals() {
    let dpp=0, ppn=0, reimb=0, sellIdr=0, sellUsd=0, totalInvUsd=0;
    invoiceItems.forEach(item => {
        const nom = parseFloat(item.nominal_invoice) || 0;
        if (item.item_type === 'tax')     ppn   += nom;
        else if (item.is_reimbursement)   reimb += nom;
        else                              dpp   += nom;
        sellIdr      += parseFloat(item.sell_idr) || 0;
        sellUsd      += parseFloat(item.sell_usd) || 0;
        totalInvUsd  += parseFloat(item.nominal_invoice_usd) || 0;
    });
    const total = dpp+ppn+reimb;
    $('#summDpp').text(fmtNum(dpp));
    $('#summPpn').text(fmtNum(ppn));
    $('#summReimb').text(fmtNum(reimb));
    $('#summTotal').text(fmtNum(total));
    $('#footerTotalInvoice').text(fmtNum2(total));
    $('#footerTotalSellIdr').text(fmtNum2(sellIdr));
    $('#footerTotalSellUsd').text(sellUsd > 0 ? fmtNum2(sellUsd) : '0,00');
    $('#footerTotalInvoiceUsd').text(totalInvUsd > 0 ? fmtNum2(totalInvUsd) : '0,00');
}

/* KURS INFO BOX */
function getCurrentKurs() {
    return parseRupiah($('#hdrKursInvoiceDisplay').val()) || 0;
}
function updateKursInfoBox() {
    const kurs = getCurrentKurs();
    const tgl  = $('#hdrTglKursInvoice').val();
    if (kurs > 0) {
        $('#kursInfoBox').removeClass('d-none');
        $('#kursWarningBox').addClass('d-none');
        $('#kursInfoValue').text(fmtNum2(kurs));
        $('#kursInfoTgl').text(tgl ? '('+tgl+')' : '');
    } else {
        $('#kursInfoBox').addClass('d-none');
    }
}

/* RENDER TABLE */
function renderTable() {
    const tbody = $('#invoiceItemsBody');
    tbody.empty();
    if (!invoiceItems.length) {
        tbody.append('<tr class="no-items-row"><td colspan="10"><i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i><p class="mb-0 fw-bold">No items found</p></td></tr>');
        $('#itemsCountBadge').text(0); recalcTotals(); return;
    }
    const {groups, order} = groupByCategory(invoiceItems);
    let globalNo = 1;
    order.forEach(cat => {
        const catItems = groups[cat];
        catItems.forEach((item, catIdx) => {
            const index = invoiceItems.indexOf(item);
            const isTax = item.item_type === 'tax';
            const catLabel = isTax ? 'Tax' : cat;
            const nomInv    = parseFloat(item.nominal_invoice) || 0;
            const nomInvUsd = parseFloat(item.nominal_invoice_usd) || 0;
            const sellIdr = parseFloat(item.sell_idr) || 0;
            const sellUsd = parseFloat(item.sell_usd) || 0;
            const rowClass = isTax ? 'row-tax' : '';
            const catCell = catIdx === 0
                ? '<td class="category-cell" rowspan="'+catItems.length+'">'+catLabel+'</td>'
                : '';
            const invoiceCellUsd = nomInvUsd > 0
                ? '<div class="currency-group-cell"><span class="currency-label-cell" >USD</span><span class="value-cell">'+fmtNum2(nomInvUsd)+'</span></div>'
                : '<div class="currency-group-cell"><span class="currency-label-cell" >USD</span><span class="value-cell">0,00</span></div>';
            const invoiceCell = nomInv > 0
                ? '<div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span id="nomDisplay_'+index+'" class="value-cell">'+fmtNum2(nomInv)+'</span></div>'
                : '<div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span id="nomDisplay_'+index+'" class="value-cell">0,00</span></div>';
            const reimbCell = item.is_reimbursement
                ? '<span class="reimb-badge yes">Yes</span>'
                : '<span class="reimb-badge no">No</span>';
            const clrDis = nomInv <= 0 ? 'disabled' : '';
            const noteHtml = item.note ? '<div class="text-muted fst-italic" style="font-size:.72rem;">'+item.note+'</div>' : '';
            const persenHtml = (isTax && item.persen_pajak) ? '<small class="text-muted ms-1">('+item.persen_pajak+'%)</small>' : '';
            tbody.append(
                '<tr class="'+rowClass+'" data-index="'+index+'" id="row_'+index+'">' +
                '<td class="cell-center">'+globalNo+'</td>' +
                catCell +
                '<td class="text-start"><span class="fw-semibold" style="color:#2c3e50;">'+item.invoice_typ+'</span>'+persenHtml+noteHtml+'</td>' +
                '<td class="cell-right cell-readonly"><div class="currency-group-cell"><span class="currency-label-cell">USD</span><span class="value-cell">'+(sellUsd>0?fmtNum2(sellUsd):'0,00')+'</span></div></td>' +
                '<td class="cell-right cell-readonly"><div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">'+(sellIdr>0?fmtNum2(sellIdr):'0,00')+'</span></div></td>' +
                '<td class="cell-right cell-readonly">'+invoiceCellUsd+'</td>' +
                '<td class="cell-center">'+invoiceCell+'</td>' +
                '<td class="coa-display-cell cell-center" id="coaCell_'+index+'">'+buildCoaDisplay(index, item.id_md_chart_of_account)+'</td>' +
                '<td class="cell-center">'+reimbCell+'</td>' +
                '<td class="cell-center">' +
                '<div class="d-flex gap-1 justify-content-center">' +
                '<button type="button" class="btn btn-edit-row btn-sm" onclick="openInputCard('+index+')"><i class="fas fa-pencil-alt"></i></button>' +
                '<button type="button" class="btn btn-clear-row btn-sm '+clrDis+'" '+clrDis+' onclick="clearItem('+index+')"><i class="fas fa-trash"></i></button>' +
                '</div></td>' +
                '</tr>'
            );
            globalNo++;
        });
    });
    $('#itemsCountBadge').text(invoiceItems.length);
    recalcTotals();
}

/* OPEN INPUT CARD */
function openInputCard(index) {
    const item = invoiceItems[index];
    if (!item) return;
    $('#activeItemId').val(item.id_invoice_contract_item);
    $('#activeRowIndex').val(index);
    $('#infoCategory').text(item.item_type === 'tax' ? 'Tax (PPN)' : (item.invoice_ctg || '-'));
    $('#infoItem').text(item.invoice_typ || '-');
    $('#infoSellIdr').text(item.sell_idr > 0 ? fmtNum2(item.sell_idr) : '-');
    $('#infoSellUsd').text(item.sell_usd > 0 ? fmtNum2(item.sell_usd) : '-');
    const nomVal    = parseFloat(item.nominal_invoice) || 0;
    const nomUsdVal = parseFloat(item.nominal_invoice_usd) || 0;
    const kursRate  = getCurrentKurs();

    // Determine mode: if nominal_invoice_usd > 0 → USD mode, else IDR mode
    if (nomUsdVal > 0) {
        // USD mode: USD editable, IDR readonly auto
        $('#inputNominalInvoiceUsd').val(nomUsdVal > 0 ? nomUsdVal.toFixed(2).replace('.',',') : '').prop('readonly', false);
        $('#inputNominalInvoice').val(nomVal > 0 ? formatRupiah(nomVal.toFixed(2).replace('.',',')) : '').prop('readonly', true)
            .css({'background':'#e9ecef','color':'#6c757d'});
        $('#inputNominalInvoice').closest('.currency-group').find('.currency-label').css({'background':'#6c757d'});
    } else {
        // IDR mode: IDR editable, USD readonly
        $('#inputNominalInvoice').val(nomVal > 0 ? formatRupiah(nomVal.toFixed(2).replace('.',',')) : '').prop('readonly', false)
            .css({'background':'','color':''});
        $('#inputNominalInvoice').closest('.currency-group').find('.currency-label').css({'background':'', 'color':''});
        $('#inputNominalInvoiceUsd').val('').prop('readonly', true).css({'background':'#e9ecef','color':'#6c757d'});
    }

    // Show kurs info or warning
    if (kursRate > 0) {
        $('#kursInfoBox').removeClass('d-none');
        $('#kursWarningBox').addClass('d-none');
        $('#kursInfoValue').text(fmtNum2(kursRate));
        const tgl = $('#hdrTglKursInvoice').val();
        $('#kursInfoTgl').text(tgl ? '('+tgl+')' : '');
    } else {
        $('#kursInfoBox').addClass('d-none');
        $('#kursWarningBox').removeClass('d-none');
    }

    $('#inputNominalHint').text('').removeClass();
    $('#inputNominalUsdHint').text('').removeClass();
    $('#inputCoa').val(item.id_md_chart_of_account || '').trigger('change');
    $('#inputIsReimbursement').prop('checked', !!item.is_reimbursement);
    $('.tr-active').removeClass('tr-active');
    $('#row_'+index).addClass('tr-active');
    const card = document.getElementById('inputItemCard');
    card.classList.add('active');
    card.scrollIntoView({behavior:'smooth', block:'nearest'});
    setTimeout(() => $('#inputNominalInvoice').focus(), 200);
}

/* CLOSE INPUT CARD */
function closeInputCard() {
    document.getElementById('inputItemCard').classList.remove('active');
    $('.tr-active').removeClass('tr-active');
    $('#activeItemId').val('');
    $('#activeRowIndex').val('');
    $('#inputNominalInvoice').val('').prop('readonly', false).css({'background':'','color':''});
    $('#inputNominalInvoice').closest('.currency-group').find('.currency-label').css({'background':'','color':''});
    $('#inputNominalInvoiceUsd').val('').prop('readonly', false).css({'background':'','color':''});
    $('#inputCoa').val('').trigger('change');
    $('#inputIsReimbursement').prop('checked', false);
    $('#inputNominalHint').text('').removeClass();
    $('#inputNominalUsdHint').text('').removeClass();
    $('#kursInfoBox').addClass('d-none');
    $('#kursWarningBox').addClass('d-none');
}

/* SAVE ITEM */
function saveItem() {
    const index  = parseInt($('#activeRowIndex').val());
    const itemId = parseInt($('#activeItemId').val());
    if (isNaN(index) || isNaN(itemId)) return;

    const usdRaw = parseRupiah($('#inputNominalInvoiceUsd').val());
    const idrRaw = parseRupiah($('#inputNominalInvoice').val());
    const kursRate = getCurrentKurs();

    let nomVal    = idrRaw;
    let nomUsdVal = usdRaw;

    if (usdRaw > 0) {
        // USD mode: IDR = USD × kurs (auto)
        nomUsdVal = usdRaw;
        nomVal    = kursRate > 0 ? usdRaw * kursRate : idrRaw;
    } else if (idrRaw > 0) {
        // IDR mode: USD = 0 (readonly)
        nomVal    = idrRaw;
        nomUsdVal = 0;
    }

    const coaId   = $('#inputCoa').val() || null;
    const isReimb = $('#inputIsReimbursement').is(':checked');
    showFloatingAlert('saving','Saving...');
    $.ajax({
        url: bulkSaveUrl, method: 'POST', contentType: 'application/json',
        headers: {'X-CSRF-TOKEN': csrfToken},
        data: JSON.stringify({items:[{
            id_invoice_contract_item: itemId,
            nominal_invoice:          nomVal,
            nominal_invoice_usd:      nomUsdVal,
            id_md_chart_of_account:   coaId,
            is_reimbursement:         isReimb,
        }]}),
        success: r => {
            if (r.success) {
                invoiceItems[index].nominal_invoice        = nomVal;
                invoiceItems[index].nominal_invoice_usd   = nomUsdVal;
                invoiceItems[index].id_md_chart_of_account = coaId;
                invoiceItems[index].is_reimbursement       = isReimb;
                showFloatingAlert('success', r.message || 'Saved!');
                closeInputCard(); renderTable();
            } else {
                showFloatingAlert('error', r.message || 'Failed to save.');
            }
        },
        error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'An error occurred.')
    });
}

/* CLEAR ITEM */
function clearItem(index) {
    const item = invoiceItems[index];
    if (!item) return;
    showConfirm({
        title:'Reset Item?',
        desc:'The invoice amount for "'+item.invoice_typ+'" will be reset to 0.',
        okLabel:'Reset', okClass:'btn-danger', iconClass:'danger',
    }, () => {
        showFloatingAlert('saving','Resetting...');
        $.ajax({
            url: bulkSaveUrl, method: 'POST', contentType: 'application/json',
            headers: {'X-CSRF-TOKEN': csrfToken},
            data: JSON.stringify({items:[{
                id_invoice_contract_item: item.id_invoice_contract_item,
                nominal_invoice:          0,
                id_md_chart_of_account:   null,
                is_reimbursement:         false,
            }]}),
            success: r => {
                if (r.success) {
                    invoiceItems[index].nominal_invoice        = 0;
                    invoiceItems[index].nominal_invoice_usd   = 0;
                    invoiceItems[index].id_md_chart_of_account = null;
                    invoiceItems[index].coa_no                 = null;
                    invoiceItems[index].coa_name               = null;
                    invoiceItems[index].is_reimbursement       = false;
                    showFloatingAlert('success','Item reset!'); renderTable();
                } else {
                    showFloatingAlert('error', r.message || 'Failed to reset.');
                }
            },
            error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'An error occurred.')
        });
    });
}

/* RESET ALL */
$('#btnResetAllItems').on('click', function() {
    showConfirm({
        title:'Reset All Items?',
        desc:'All invoice amounts, COA, and reimbursement will be cleared. This action cannot be undone.',
        okLabel:'Reset All', okClass:'btn-danger', iconClass:'danger',
    }, () => {
        showFloatingAlert('saving','Resetting all...');
        const payload = invoiceItems.map(item => ({
            id_invoice_contract_item: item.id_invoice_contract_item,
            nominal_invoice:          0,
            nominal_invoice_usd:      0,
            id_md_chart_of_account:   null,
            is_reimbursement:         false,
        }));
        $.ajax({
            url: bulkSaveUrl, method: 'POST', contentType: 'application/json',
            headers: {'X-CSRF-TOKEN': csrfToken},
            data: JSON.stringify({items: payload}),
            success: r => {
                if (r.success) {
                    invoiceItems.forEach(i => {
                        i.nominal_invoice        = 0;
                        i.nominal_invoice_usd   = 0;
                        i.id_md_chart_of_account = null;
                        i.coa_no                 = null;
                        i.coa_name               = null;
                        i.is_reimbursement       = false;
                    });
                    showFloatingAlert('success','All items reset!');
                    closeInputCard(); renderTable();
                } else {
                    showFloatingAlert('error', r.message || 'Failed to reset.');
                }
            },
            error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'An error occurred.')
        });
    });
});


/* ── EDIT ALL ITEMS ── */
$('#btnEditAllItems').on('click', function() {
    if (!invoiceItems.length) return;
    let rows = '';
    const coaOptions = coaList.map(c => `<option value="${c.id}">${c.no} — ${c.name}</option>`).join('');
    invoiceItems.forEach(function(item, index) {
        const isTax      = item.item_type === 'tax';
        const catLabel   = isTax ? 'Tax' : (item.invoice_ctg || '-');
        const catBg      = isTax ? '#b45309' : '#2c3e50';
        const rowBg      = isTax ? '#fffbeb' : '#f8fafc';
        const rowBorder  = isTax ? '#fde68a' : '#e2e8f0';
        const persenBadge = (isTax && item.persen_pajak) ? `<small class="text-muted ms-1">(${item.persen_pajak}%)</small>` : '';
        const nomVal     = parseFloat(item.nominal_invoice) || 0;
        const nomUsdVal  = parseFloat(item.nominal_invoice_usd) || 0;
        const valStr     = nomVal > 0 ? formatRupiah(nomVal.toFixed(2).replace('.',',')) : '';
        const sellIdr    = parseFloat(item.sell_idr) || 0;
        const sellUsd    = parseFloat(item.sell_usd) || 0;
        const selCoaId   = item.id_md_chart_of_account || '';
        const isReimb    = item.is_reimbursement ? 'checked' : '';

        rows += `
        <div class="edit-all-row mb-3 rounded-3 border overflow-hidden" style="border-color:${rowBorder};">
            <!-- Header item -->
            <div class="d-flex align-items-center gap-2 px-3 py-2" style="background:${catBg}10;border-bottom:1px solid ${rowBorder};">
                <span class="badge" style="background:${catBg};font-size:.7rem;">${catLabel}</span>
                <span class="fw-semibold" style="color:#2c3e50;font-size:.875rem;">${item.invoice_typ}</span>
                ${persenBadge}
            </div>
            <!-- Body -->
            <div class="p-3" style="background:${rowBg};">
                <!-- Info sell price -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="info-field">
                            <div class="info-label">Selling Price (IDR)</div>
                            <div class="info-value">${sellIdr > 0 ? fmtNum2(sellIdr) : '—'}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-field">
                            <div class="info-label">Selling Price (USD)</div>
                            <div class="info-value">${sellUsd > 0 ? fmtNum2(sellUsd) : '—'}</div>
                        </div>
                    </div>
                </div>
                <!-- Nominal IDR + USD + COA -->
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="color:#1e40af;font-size:.8rem;">Invoice Amount (IDR) <span style="color:#dc3545;">*</span></label>
                        <div class="currency-group">
                            <span class="currency-label edit-all-idr-label-${index}">IDR</span>
                            <input type="text" class="form-control currency-input edit-all-input" data-index="${index}" placeholder="0,00" value="${valStr}" autocomplete="off">
                        </div>
                        <div class="validate-hint hint-info edit-all-idr-hint-${index}" style="font-size:.72rem;min-height:16px;"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="color:#0369a1;font-size:.8rem;">
                            Invoice Amount (USD)
                        </label>
                        <div class="currency-group">
                            <span class="currency-label" style="background:#0369a1;">USD</span>
                            <input type="text" class="form-control currency-input edit-all-usd-input" data-index="${index}" placeholder="0.00"
                                   value="${nomUsdVal > 0 ? nomUsdVal.toFixed(2).replace('.',',') : ''}" autocomplete="off">
                        </div>
                        <div class="validate-hint hint-info edit-all-usd-hint-${index}" style="font-size:.72rem;min-height:16px;"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="color:#1e40af;font-size:.8rem;">Chart of Account</label>
                        <select class="form-select edit-all-coa" data-index="${index}" style="font-size:.8rem;">
                            <option value="">— Select COA (optional) —</option>
                            ${coaOptions}
                        </select>
                    </div>
                </div>
                <!-- Reimbursement -->
                <div class="reimb-card mt-2">
                    <div class="form-check mb-0">
                        <input class="form-check-input edit-all-reimb" type="checkbox" id="editAllReimb_${index}" data-index="${index}" value="1" ${isReimb}>
                        <label class="form-check-label" for="editAllReimb_${index}">
                            <span class="reimb-label-title"><i class="fas fa-rotate-left me-1"></i> Reimbursement</span>
                            <span class="reimb-label-desc">Check if this item is a reimbursement cost</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>`;
    });
    $('#editAllBody').html(rows);
    // Set COA value setelah render
    invoiceItems.forEach(function(item, index) {
        const $sel = $('#editAllBody .edit-all-coa[data-index="'+index+'"]');
        if (item.id_md_chart_of_account) $sel.val(item.id_md_chart_of_account);
    });
    $('#editAllBody .edit-all-input').each(function() { setupRupiahInput(this); });
    $('#editAllBody .edit-all-usd-input').each(function() { setupRupiahInput(this); });

    // Bidirectional logic for edit-all modal
    const kursRate = getCurrentKurs();
    $(document).on('input.editAll', '#editAllBody .edit-all-input', function() {
        const index  = $(this).data('index');
        const idrVal = parseRupiah($(this).val());
        const $usd   = $('#editAllBody .edit-all-usd-input[data-index="'+index+'"]');
        if (idrVal > 0) {
            $usd.val('').prop('readonly', true).css({'background':'#e9ecef','color':'#6c757d'});
            $('.edit-all-usd-hint-'+index).text('USD cleared automatically').css('color','#6b7280');
        } else {
            $usd.prop('readonly', false).css({'background':'','color':''});
            $('.edit-all-usd-hint-'+index).text('').css('color','');
        }
    });
    $(document).on('input.editAll', '#editAllBody .edit-all-usd-input', function() {
        const index  = $(this).data('index');
        const usdVal = parseRupiah($(this).val());
        const $idr   = $('#editAllBody .edit-all-input[data-index="'+index+'"]');
        if (usdVal > 0) {
            if (kursRate > 0) {
                const autoIdr = usdVal * kursRate;
                $idr.val(formatRupiah(autoIdr.toFixed(2).replace('.',',')))
                    .prop('readonly', true).css({'background':'#e9ecef','color':'#6c757d'});
                $('.edit-all-idr-hint-'+index).text('IDR = '+fmtNum2(usdVal)+' × '+fmtNum2(kursRate)+' = '+fmtNum2(autoIdr)).css('color','#059669');
                $('.edit-all-usd-hint-'+index).text('').css('color','');
            } else {
                $idr.val('').prop('readonly', true).css({'background':'#fff3cd','color':'#856404'});
                $('.edit-all-idr-hint-'+index).text('⚠ Exchange rate not set!').css('color','#dc3545');
            }
        } else {
            $idr.prop('readonly', false).css({'background':'','color':''});
            $('.edit-all-idr-hint-'+index).text('').css('color','');
        }
    });

    // Apply initial readonly states based on loaded values
    invoiceItems.forEach(function(item, index) {
        const nomUsd = parseFloat(item.nominal_invoice_usd) || 0;
        const nomIdr = parseFloat(item.nominal_invoice) || 0;
        const $idr = $('#editAllBody .edit-all-input[data-index="'+index+'"]');
        const $usd = $('#editAllBody .edit-all-usd-input[data-index="'+index+'"]');
        if (nomUsd > 0) {
            $idr.prop('readonly', true).css({'background':'#e9ecef','color':'#6c757d'});
        } else if (nomIdr > 0) {
            $usd.prop('readonly', true).css({'background':'#e9ecef','color':'#6c757d'});
        }
    });
    $('#editAllModal').modal('show');
});

$('#btnSaveAllItems').on('click', function() {
    const payload = [];
    let valid = true;
    const kursRateAll = getCurrentKurs();
    invoiceItems.forEach(function(item, index) {
        const $idrInput = $('#editAllBody .edit-all-input[data-index="'+index+'"]');
        const $usdInput = $('#editAllBody .edit-all-usd-input[data-index="'+index+'"]');
        const coaId     = $('#editAllBody .edit-all-coa[data-index="'+index+'"]').val() || null;
        const reimb     = $('#editAllBody .edit-all-reimb[data-index="'+index+'"]').is(':checked');
        const usdVal    = parseRupiah($usdInput.val());
        const idrVal    = parseRupiah($idrInput.val());

        let nomVal    = idrVal;
        let nomUsdVal = usdVal;
        if (usdVal > 0) {
            nomUsdVal = usdVal;
            nomVal    = kursRateAll > 0 ? usdVal * kursRateAll : idrVal;
        } else {
            nomVal    = idrVal;
            nomUsdVal = 0;
        }
        if (isNaN(nomVal)) { valid = false; return; }
        payload.push({
            id_invoice_contract_item: item.id_invoice_contract_item,
            nominal_invoice:          nomVal,
            nominal_invoice_usd:      nomUsdVal,
            id_md_chart_of_account:   coaId,
            is_reimbursement:         reimb,
        });
    });
    if (!valid) { showFloatingAlert('error','Invalid amount(s).'); return; }
    showFloatingAlert('saving','Saving all items...');
    $.ajax({
        url: bulkSaveUrl, method: 'POST', contentType: 'application/json',
        headers: {'X-CSRF-TOKEN': csrfToken},
        data: JSON.stringify({items: payload}),
        success: r => {
            if (r.success) {
                payload.forEach(p => {
                    const idx = invoiceItems.findIndex(i => i.id_invoice_contract_item === p.id_invoice_contract_item);
                    if (idx !== -1) {
                        invoiceItems[idx].nominal_invoice        = p.nominal_invoice;
                        invoiceItems[idx].nominal_invoice_usd   = p.nominal_invoice_usd;
                        invoiceItems[idx].id_md_chart_of_account = p.id_md_chart_of_account;
                        invoiceItems[idx].is_reimbursement       = p.is_reimbursement;
                        const coa = coaList.find(c => c.id == p.id_md_chart_of_account);
                        invoiceItems[idx].coa_no   = coa ? coa.no   : null;
                        invoiceItems[idx].coa_name = coa ? coa.name : null;
                    }
                });
                // Remove bidirectional listeners to avoid stale handlers
                $(document).off('input.editAll');
                $('#editAllModal').modal('hide');
                showFloatingAlert('success','All items saved!');
                closeInputCard(); renderTable();
            } else {
                showFloatingAlert('error', r.message || 'Failed to save.');
            }
        },
        error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'An error occurred.')
    });
});


/* ── CLEANUP EDIT ALL LISTENERS ── */
$(document).on('hidden.bs.modal', '#editAllModal', function() {
    $(document).off('input.editAll');
});

/* ── COPY FROM SELL IDR ── */
$(document).on('click', '#btnCopyFromSellIdr', function() {
    $('#editAllBody .edit-all-input').each(function() {
        const index   = parseInt($(this).data('index'));
        const item    = invoiceItems[index];
        const sellIdr = parseFloat(item.sell_idr) || 0;
        if (sellIdr > 0) {
            $(this).val(formatRupiah(sellIdr.toFixed(2).replace('.', ',')));
        }
    });
    showFloatingAlert('success', 'Selling Price IDR copied to all nominals!');
});

$(document).on('click', '#btnCopyFromSellUsd', function() {
    const kurs = getCurrentKurs();
    $('#editAllBody .edit-all-usd-input').each(function() {
        const index   = parseInt($(this).data('index'));
        const item    = invoiceItems[index];
        const sellUsd = parseFloat(item.sell_usd) || 0;
        if (sellUsd > 0) {
            $(this).val(sellUsd.toFixed(2).replace('.', ','));
            // Auto-calculate IDR if kurs available
            const idrInput = $('#editAllBody .edit-all-input[data-index="'+index+'"]');
            if (kurs > 0) {
                const idrVal = sellUsd * kurs;
                idrInput.val(formatRupiah(idrVal.toFixed(2).replace('.',',')));
                idrInput.prop('readonly', true).css({'background':'#e9ecef','color':'#6c757d'});
                $('.edit-all-idr-hint-'+index).text('IDR auto-calculated from USD × Rate').css('color','#6b7280');
            } else {
                idrInput.val('').prop('readonly', false).css({'background':'','color':''});
                $('.edit-all-idr-hint-'+index).text('⚠ Exchange rate not set!').css('color','#dc3545');
            }
            $(this).prop('readonly', false).css({'background':'','color':''});
            $('.edit-all-usd-hint-'+index).text('').css('color','');
        }
    });
    showFloatingAlert('success', 'Selling Price USD copied to all USD nominals!');
});

/* SAVE HEADER */
$('#btnSaveHeader').on('click', function() {
    showFloatingAlert('saving','Saving header...');
    $.ajax({
        url: updateHeaderUrl, method: 'POST', contentType: 'application/json',
        headers: {'X-CSRF-TOKEN': csrfToken},
        data: JSON.stringify({
            tgl_invoice:    $('#hdrTglInvoice').val(),
            due_date:       $('#hdrDueDate').val(),
            status:         $('#hdrStatus').val(),
            id_jo_cont:     $('#hdrJoSelect').val(),
            title:          $('#hdrTitle').val(),
            contract_no:    $('#hdrContractNo').val(),
            po_number:      $('#hdrPoNumber').val(),
            to_customer:    $('#hdrToCustomer').val(),
            currency:       $('#hdrCurrency').val(),
            kurs_invoice:   parseRupiah($('#hdrKursInvoiceDisplay').val()) || null,
            tgl_kurs_invoice: $('#hdrTglKursInvoice').val() || null,
            vendor_number:  $('#hdrVendorNumber').val(),
            sa_gr_number:   $('#hdrSaGrNumber').val(),
            ba_number:      $('#hdrBaNumber').val(),
            eta:            $('#hdrEta').val(),
            etd:            $('#hdrEtd').val(),
            no_voyage:      $('#hdrNoVoyage').val(),
            grt:            $('#hdrGrt').val(),
            kapal:          $('#hdrKapal').val(),
            jetty:          $('#hdrJetty').val(),
            cond_of_payment:$('#hdrCondOfPayment').val(),
            period:         $('#hdrPeriod').val(),
            att:            $('#hdrAtt').val(),
            address:        $('#hdrAddress').val(),
            vat_number:     $('#hdrVatNumber').val(),
            bar_code:       $('#hdrBarCode').val(),
            office_phone:   $('#hdrOfficePhone').val(),
            mobile_phone:   $('#hdrMobilePhone').val(),
            close_note:     $('#hdrCloseNote').val(),
            reopen_note:    $('#hdrReopenNote').val(),
            id_md_chart_of_account: $('#hdrCoa').val() || null,
        }),
        success: r => {
            if (r.success) {
                showFloatingAlert('success', r.message || 'Header saved!');
                if (r.jo_changed) {
                    setTimeout(function() { location.reload(); }, 1500);
                }
            } else {
                showFloatingAlert('error', r.message || 'Failed to save header.');
            }
        },
        error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'An error occurred.')
    });
});

$('#btnCancelItemInput').on('click', closeInputCard);
$('#btnSaveItemInput').on('click', saveItem);

// ── JO Change Logic ──
$(document).ready(function() {
    var currentJoId = {{ $invoice->id_jo_cont ?? 'null' }};
    var pendingJoId = null;

    if ($('#hdrJoSelect').val()) $('#btnJoInfo').prop('disabled', false);

    // Auto-fill Title/Customer/Contract from option data attributes
    $('#hdrJoSelect').on('change', function() {
        var opt = $(this).find('option:selected');
        var joId = $(this).val();
        $('#btnJoInfo').prop('disabled', !joId);
        if (!joId) { $('#hdrTitle').val(''); $('#hdrToCustomer').val(''); $('#hdrContractNo').val(''); return; }
        $('#hdrTitle').val(opt.data('title') || '');
        $('#hdrToCustomer').val(opt.data('customer') || '');
        $('#hdrContractNo').val(opt.data('contract') || '');
    });

    // Info button → open modal
    $('#btnJoInfo').on('click', function() {
        var joId = $('#hdrJoSelect').val();
        if (!joId) return;
        pendingJoId = joId;
        $('#joInfoBody').html('<div class="col-12 text-center py-4"><div class="spinner-border text-success" role="status"></div><div class="mt-2 text-muted">Loading...</div></div>');
        $('#btnConfirmChangeJo').hide().prop('disabled', false).html('<i class="fas fa-sync-alt me-1"></i> Change JO & Reset Items');
        $('#joInfoNote').text('');
        $('#joInfoModal').modal('show');

        $.ajax({
            url: '{{ route("invoice-contract.jo.info", $invoice->id_invoice_contract) }}',
            method: 'GET',
            data: { id_jo_cont: joId },
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            success: function(res) {
                if (!res.success) { $('#joInfoBody').html('<div class="p-4 text-danger"><i class="fas fa-exclamation-circle me-2"></i>' + res.message + '</div>'); return; }

                $('#joInfoSubtitle').text(res.no_jo_cont + ' — ' + res.customer);

                // ── Build tabbed layout ──
                var tabs = `
                <style>
                    #joInfoTabs .nav-link { color:#6b7280; border-radius:6px 6px 0 0; }
                    #joInfoTabs .nav-link:hover { color:#2c3e50; background:#f3f4f6; }
                    #joInfoTabs .nav-link.active { background:#2c3e50 !important; color:#fff !important; border-color:#2c3e50 !important; }
                    #joInfoTabs .nav-link.active .badge { background:#ffffff33 !important; }
                </style>
                <ul class="nav nav-tabs px-3 pt-3" id="joInfoTabs" role="tablist" style="border-bottom:1px solid #e5e7eb;">
                    <li class="nav-item"><button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tabJoOverview" style="font-size:.85rem;"><i class="fas fa-info-circle me-1"></i>Overview</button></li>
                    <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tabJoItems" style="font-size:.85rem;"><i class="fas fa-list me-1"></i>Items <span class="badge bg-secondary ms-1">${res.items.length + res.pajak_items.length}</span></button></li>
                    <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tabJoKasbon" style="font-size:.85rem;"><i class="fas fa-wallet me-1"></i>Kasbon <span class="badge bg-secondary ms-1">${res.kasbon.length}</span></button></li>
                    <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tabJoLpj" style="font-size:.85rem;"><i class="fas fa-receipt me-1"></i>LPJ <span class="badge bg-secondary ms-1">${res.lpj.length}</span></button></li>
                    <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tabJoMutasi" style="font-size:.85rem;"><i class="fas fa-exchange-alt me-1"></i>Mutasi <span class="badge bg-secondary ms-1">${res.mutasi_penerimaan.length + res.mutasi_pembayaran.length}</span></button></li>
                </ul>
                <div class="tab-content p-3" id="joInfoTabContent">`;

                // ── Tab 1: Overview ──
                var kursColor = res.kurs_usd ? '#059669' : '#9ca3af';
                tabs += `<div class="tab-pane fade show active" id="tabJoOverview">
                    <div class="row g-2 mt-1">
                        ${joCard('fas fa-hashtag','JO Number',res.no_jo_cont)}
                        ${joCard('fas fa-heading','Title',res.title)}
                        ${joCard('fas fa-building','Customer',res.customer)}
                        ${joCard('fas fa-file-contract','Contract No',res.contract_no)}
                        ${joCardHighlight('fas fa-dollar-sign','Exchange Rate (USD)', res.kurs_usd ? 'IDR '+res.kurs_usd : 'No USD Rate', kursColor)}
                        ${res.tgl_kurs_usd ? joCard('fas fa-calendar','Rate Date',res.tgl_kurs_usd) : ''}
                    </div>
                </div>`;

                // ── Tab 2: Items ──
                // Kurs info above table
                var kursInfo = res.kurs_usd
                    ? `<div class="d-flex align-items-center gap-2 mb-2 mt-2 px-1">
                           <span style="background:#059669;color:#fff;padding:3px 10px;border-radius:6px;font-size:.75rem;font-weight:700;">
                               <i class="fas fa-dollar-sign me-1"></i>Exchange Rate: IDR ${res.kurs_usd}
                           </span>
                           <span style="font-size:.78rem;color:#6b7280;">${res.tgl_kurs_usd ? '<i class="fas fa-calendar me-1"></i>'+res.tgl_kurs_usd : ''}</span>
                       </div>`
                    : `<div class="mb-2 mt-2 px-1"><span style="background:#e5e7eb;color:#6b7280;padding:3px 10px;border-radius:6px;font-size:.75rem;font-weight:600;"><i class="fas fa-dollar-sign me-1"></i>Exchange Rate: —</span></div>`;

                // Build rows with category rowspan merge
                var allItems = res.items.map(function(it) { return {type:'item', data:it}; });
                var itemRows = '';
                var rowNum = 1;

                // Calculate category rowspans
                var catSpans = {};
                res.items.forEach(function(it) {
                    var cat = it.category || '-';
                    catSpans[cat] = (catSpans[cat] || 0) + 1;
                });
                var catSeen = {};
                res.items.forEach(function(it, i) {
                    var cat = it.category || '-';
                    var showCat = !catSeen[cat];
                    if (showCat) catSeen[cat] = true;
                    var catCell = showCat
                        ? `<td rowspan="${catSpans[cat]}" style="vertical-align:middle;font-weight:600;text-align:center;background:#f0fdf4;color:#065f46;">${cat}</td>`
                        : '';
                    itemRows += `<tr>
                        <td class="text-center">${rowNum++}</td>
                        ${catCell}
                        <td>${it.name}${it.note ? '<br><small class="text-muted fst-italic">'+it.note+'</small>' : ''}</td>
                        <td class="text-end">${it.pend_idr}</td>
                        <td class="text-end">${it.pend_usd}</td>
                        <td class="text-end">${it.sell_idr}</td>
                        <td class="text-end">${it.hpp}</td>
                    </tr>`;
                });
                // Tax rows — no category merge needed, just append
                res.pajak_items.forEach(function(p) {
                    itemRows += `<tr style="background:#fffbeb;">
                        <td class="text-center">${rowNum++}</td>
                        <td style="color:#b45309;font-weight:700;text-align:center;background:#fef3c7;">Tax</td>
                        <td>${p.name}${p.persen ? ' <span class="badge bg-warning text-dark ms-1" style="font-size:.7rem;">'+p.persen+'%</span>' : ''}</td>
                        <td class="text-end">0,00</td>
                        <td class="text-end">0,00</td>
                        <td class="text-end fw-bold">${p.nominal_idr}</td>
                        <td class="text-end fw-bold">${p.nominal_hpp}</td>
                    </tr>`;
                });

                tabs += `<div class="tab-pane fade" id="tabJoItems">
                    <div class="px-3">
                        ${kursInfo}
                        <div class="table-responsive" style="max-height:320px;overflow-y:auto;">
                            <table class="table table-sm table-bordered mb-0" style="font-size:.8rem;">
                                <thead style="background:#2c3e50;color:#fff;position:sticky;top:0;z-index:1;">
                                    <tr>
                                        <th style="width:40px;">No.</th>
                                        <th style="min-width:100px;">Category</th>
                                        <th>Item</th>
                                        <th class="text-end" style="min-width:90px;">Inc. IDR</th>
                                        <th class="text-end" style="min-width:80px;">Inc. USD</th>
                                        <th class="text-end" style="min-width:90px;">Sell</th>
                                        <th class="text-end" style="min-width:90px;">HPP</th>
                                    </tr>
                                </thead>
                                <tbody>${itemRows || '<tr><td colspan="7" class="text-center text-muted py-3">No items</td></tr>'}</tbody>
                            </table>
                        </div>
                    </div>
                </div>`;

                // ── Tab 3: Kasbon ──
                var kasbonRows = '';
                res.kasbon.forEach(function(k) {
                    var badge = k.status === 'released' ? 'success' : (k.status === 'pending' ? 'warning text-dark' : 'secondary');
                    kasbonRows += `<tr>
                        <td style="font-size:.82rem;">${k.nomor}</td>
                        <td style="font-size:.82rem;">${k.tgl}</td>
                        <td><span class="badge bg-${badge}" style="font-size:.72rem;">${k.status}</span></td>
                        <td class="text-end fw-bold" style="font-size:.82rem;">IDR ${k.total}</td>
                    </tr>`;
                });
                tabs += `<div class="tab-pane fade" id="tabJoKasbon">
                    <div class="table-responsive mt-2" style="max-height:280px;overflow-y:auto;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead style="background:#2c3e50;color:#fff;position:sticky;top:0;">
                                <tr><th>No.</th><th>Date</th><th>Status</th><th class="text-end">Total</th></tr>
                            </thead>
                            <tbody>${kasbonRows || '<tr><td colspan="4" class="text-center text-muted py-3">No kasbon</td></tr>'}</tbody>
                            ${res.kasbon.length ? '<tfoot><tr><td colspan="3" class="fw-bold text-end" style="font-size:.82rem;">Grand Total</td><td class="fw-bold text-end text-success" style="font-size:.85rem;">IDR '+res.kasbon_total+'</td></tr></tfoot>' : ''}
                        </table>
                    </div>
                </div>`;

                // ── Tab 4: LPJ ──
                var lpjRows = '';
                res.lpj.forEach(function(l) {
                    var bal = parseFloat((l.balance || '0').replace(/\./g,'').replace(',','.'));
                    var balColor = bal >= 0 ? 'color:#059669;' : 'color:#dc2626;';
                    lpjRows += `<tr>
                        <td style="font-size:.82rem;font-weight:600;">${l.no_lpj}</td>
                        <td style="font-size:.82rem;">${l.tgl}</td>
                        <td class="text-end" style="font-size:.82rem;">IDR ${l.ca_amount}</td>
                        <td class="text-end" style="font-size:.82rem;">IDR ${l.pj_amount}</td>
                        <td class="text-end fw-bold" style="font-size:.82rem;${balColor}">IDR ${l.balance}</td>
                    </tr>`;
                });
                tabs += `<div class="tab-pane fade" id="tabJoLpj">
                    <div class="table-responsive mt-2" style="max-height:280px;overflow-y:auto;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead style="background:#2c3e50;color:#fff;position:sticky;top:0;">
                                <tr>
                                    <th>No. LPJ</th>
                                    <th>Date</th>
                                    <th class="text-end">CA Amount</th>
                                    <th class="text-end">PJ Amount</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>${lpjRows || '<tr><td colspan="5" class="text-center text-muted py-3">No LPJ</td></tr>'}</tbody>
                        </table>
                    </div>
                </div>`;

                // ── Tab 5: Mutasi ──
                var mutasiRows = '';
                res.mutasi_penerimaan.forEach(function(m) {
                    mutasiRows += `<tr>
                        <td><span class="badge bg-success" style="font-size:.7rem;">Receipt</span></td>
                        <td style="font-size:.82rem;">${m.nomor}</td>
                        <td style="font-size:.82rem;">${m.tgl}</td>
                        <td style="font-size:.8rem;">${m.tipe}</td>
                        <td class="text-end fw-bold" style="font-size:.82rem;">IDR ${m.nilai}</td>
                    </tr>`;
                });
                res.mutasi_pembayaran.forEach(function(m) {
                    mutasiRows += `<tr>
                        <td><span class="badge bg-primary" style="font-size:.7rem;">Payment</span></td>
                        <td style="font-size:.82rem;">${m.nomor}</td>
                        <td style="font-size:.82rem;">${m.tgl}</td>
                        <td style="font-size:.8rem;">${m.jenis}</td>
                        <td class="text-end fw-bold" style="font-size:.82rem;">IDR ${m.nilai}</td>
                    </tr>`;
                });
                tabs += `<div class="tab-pane fade" id="tabJoMutasi">
                    <div class="table-responsive mt-2" style="max-height:280px;overflow-y:auto;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead style="background:#2c3e50;color:#fff;position:sticky;top:0;">
                                <tr><th>Type</th><th>No.</th><th>Date</th><th>Description</th><th class="text-end">Amount</th></tr>
                            </thead>
                            <tbody>${mutasiRows || '<tr><td colspan="5" class="text-center text-muted py-3">No mutasi</td></tr>'}</tbody>
                        </table>
                    </div>
                </div>`;

                tabs += '</div>'; // close tab-content

                $('#joInfoBody').html(tabs);

                if (parseInt(joId) !== parseInt(currentJoId)) {
                    $('#btnConfirmChangeJo').show();
                    $('#joInfoNote').html('<i class="fas fa-exclamation-triangle text-warning me-1"></i> Changing JO will reset all invoice items.');
                } else {
                    $('#btnConfirmChangeJo').hide();
                    $('#joInfoNote').text('This is the current JO.');
                }
            },
            error: function() { $('#joInfoBody').html('<div class="col-12 text-danger"><i class="fas fa-exclamation-circle me-2"></i>Failed to load JO info.</div>'); }
        });
    });

    function joCard(icon, label, value) {
        return '<div class="col-6"><div style="background:#f8f9fa;border-radius:8px;padding:10px 14px;border:1px solid #e5e7eb;margin-bottom:4px;">' +
               '<div style="font-size:.68rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;"><i class="' + icon + ' me-1"></i>' + label + '</div>' +
               '<div style="font-size:.85rem;font-weight:700;color:#374151;">' + (value || '—') + '</div></div></div>';
    }
    function joCardHighlight(icon, label, value, color) {
        return '<div class="col-6"><div style="background:#f0fdf4;border-radius:8px;padding:10px 14px;border:1px solid #bbf7d0;margin-bottom:4px;">' +
               '<div style="font-size:.68rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;"><i class="' + icon + ' me-1"></i>' + label + '</div>' +
               '<div style="font-size:.92rem;font-weight:800;color:' + color + ';">' + (value || '—') + '</div></div></div>';
    }

    // Confirm change JO
    $('#btnConfirmChangeJo').on('click', function() {
        var joId = pendingJoId;
        if (!joId) return;
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Changing...');
        $.ajax({
            url: '{{ route("invoice-contract.jo.change", $invoice->id_invoice_contract) }}',
            method: 'POST',
            data: { id_jo_cont: joId, _token: '{{ csrf_token() }}' },
            success: function(res) {
                $('#joInfoModal').modal('hide');
                if (res.success) {
                    currentJoId = parseInt(joId);
                    showFloatingAlert('success', res.message);
                    setTimeout(function() { location.reload(); }, 1800);
                } else {
                    showFloatingAlert('error', res.message || 'Failed to change JO.');
                    $('#btnConfirmChangeJo').prop('disabled', false).html('<i class="fas fa-sync-alt me-1"></i> Change JO & Reset Items');
                }
            },
            error: function() {
                $('#joInfoModal').modal('hide');
                showFloatingAlert('error', 'Failed to change JO.');
                $('#btnConfirmChangeJo').prop('disabled', false).html('<i class="fas fa-sync-alt me-1"></i> Change JO & Reset Items');
            }
        });
    });

    @if(session('success'))
        showFloatingAlert('success', '{{ session('success') }}');
    @endif
    @if(session('error'))
        showFloatingAlert('error', '{{ session('error') }}');
    @endif
});

$(document).ready(function() {
    $('#inputCoa').select2({theme:'bootstrap-5', placeholder:'— Select COA —', allowClear:true, width:'100%'});
    $('.s2-coa-edit').select2({theme:'bootstrap-5', placeholder:'— Select COA (optional) —', allowClear:true, width:'100%'});
    $('.s2-jo-edit').select2({theme:'bootstrap-5', placeholder:'-- Select Job Order --', allowClear:true, width:'100%'});
    $('.s2-vessel-edit').select2({theme:'bootstrap-5', placeholder:'-- Select Vessel --', allowClear:true, width:'100%'});
    $('.s2-jetty-edit').select2({theme:'bootstrap-5', placeholder:'-- Select Jetty --', allowClear:true, width:'100%'});
    setupRupiahInput(document.getElementById('inputNominalInvoice'));
    setupRupiahInput(document.getElementById('inputNominalInvoiceUsd'));
    setupRupiahInput(document.getElementById('modalKursDisplay'));

    // ── Kurs Modal ──
    function updateKursModalBadge() {
        const kurs = parseRupiah($('#hdrKursInvoiceDisplay').val()) || 0;
        const badge = $('#kursModalBadge');
        if (kurs > 0) {
            badge.text('IDR ' + fmtNum2(kurs)).show();
        } else {
            badge.hide();
        }
        updateKursInfoBox();
    }

    $('#btnOpenKursModal').on('click', function() {
        // Populate modal from current hidden values
        const curKurs = $('#hdrKursInvoiceDisplay').val();
        const curTgl  = $('#hdrTglKursInvoice').val();
        $('#modalKursDisplay').val(curKurs);
        $('#modalKursTgl').val(curTgl);
        $('#kursModal').modal('show');
    });

    $('#btnSaveKurs').on('click', function() {
        const kursVal  = parseRupiah($('#modalKursDisplay').val()) || 0;
        const tglVal   = $('#modalKursTgl').val() || null;
        const dispStr  = kursVal > 0 ? formatRupiah(kursVal.toFixed(2).replace('.',',')) : '';

        // Persist to hidden fields
        $('#hdrKursInvoiceDisplay').val(dispStr);
        $('#hdrKursInvoice').val(kursVal || '');
        $('#hdrTglKursInvoice').val(tglVal || '');

        updateKursModalBadge();
        recalcActiveInputCard();

        // Auto-save kurs to header via AJAX
        $.ajax({
            url: updateHeaderUrl,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                kurs_invoice: kursVal || null,
                tgl_kurs_invoice: tglVal || null,
            },
            success: function() {
                showFloatingAlert('success', 'Exchange rate saved!');
            },
            error: function() {
                showFloatingAlert('error', 'Failed to save exchange rate.');
            }
        });

        $('#kursModal').modal('hide');
    });

    // Init badge on load
    updateKursModalBadge();

    // ── Bidirectional input logic ──
    // When IDR filled → USD becomes readonly (cleared)
    $('#inputNominalInvoice').on('input', function() {
        const idrVal = parseRupiah($(this).val());
        if (idrVal > 0) {
            // IDR mode: lock USD
            $('#inputNominalInvoiceUsd').val('').prop('readonly', true)
                .css({'background':'#e9ecef','color':'#6c757d'});
            $('#inputNominalUsdHint').text('USD cleared automatically').removeClass().addClass('validate-hint hint-info');
        } else {
            // IDR cleared: unlock USD
            $('#inputNominalInvoiceUsd').prop('readonly', false).css({'background':'','color':''});
            $('#inputNominalUsdHint').text('').removeClass();
        }
    });

    // When USD filled → IDR becomes auto (readonly = derived)
    $('#inputNominalInvoiceUsd').on('input', function() {
        const usdVal = parseRupiah($(this).val());
        if (usdVal > 0) {
            const kursRate = getCurrentKurs();
            if (kursRate > 0) {
                const autoIdr = usdVal * kursRate;
                $('#inputNominalInvoice').val(formatRupiah(autoIdr.toFixed(2).replace('.',',')))
                    .prop('readonly', true).css({'background':'#e9ecef','color':'#6c757d'});
                $('#inputNominalInvoice').closest('.currency-group').find('.currency-label')
                    .css({'background':'#6c757d'});
                $('#inputNominalHint').text('IDR = '+fmtNum2(usdVal)+' × '+fmtNum2(kursRate)+' = '+fmtNum2(autoIdr)).removeClass().addClass('validate-hint hint-ok');
                $('#kursWarningBox').addClass('d-none');
            } else {
                $('#inputNominalInvoice').val('').prop('readonly', true)
                    .css({'background':'#fff3cd','color':'#856404'});
                $('#inputNominalHint').text('⚠ Exchange rate not set — please set it first!').removeClass().addClass('validate-hint hint-danger');
                $('#kursWarningBox').removeClass('d-none');
            }
        } else {
            // USD cleared: unlock IDR
            $('#inputNominalInvoice').prop('readonly', false).css({'background':'','color':''});
            $('#inputNominalInvoice').closest('.currency-group').find('.currency-label').css({'background':'','color':''});
            $('#inputNominalHint').text('').removeClass();
        }
    });

    function recalcActiveInputCard() {
        const usdVal   = parseRupiah($('#inputNominalInvoiceUsd').val());
        const kursRate = getCurrentKurs();
        if (usdVal > 0 && kursRate > 0) {
            const autoIdr = usdVal * kursRate;
            $('#inputNominalInvoice').val(formatRupiah(autoIdr.toFixed(2).replace('.',',')));
            $('#inputNominalHint').text('IDR = '+fmtNum2(usdVal)+' × '+fmtNum2(kursRate)+' = '+fmtNum2(autoIdr)).removeClass().addClass('validate-hint hint-ok');
        }
    }

    updateKursInfoBox();
    renderTable();
});
</script>
@endpush
