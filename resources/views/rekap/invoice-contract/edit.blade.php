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
.footer-currency-label { background:#2c3e50; color:#fff; padding:4px 10px; border-radius:5px; font-size:.60rem; font-weight:700; min-width:44px; text-align:center; flex-shrink:0; }
.footer-value { color:#2c3e50; font-weight:700; font-size:.68rem; text-align:right; flex:1; min-width:0; white-space:nowrap; }
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
    <div class="card-header text-black" style="background-color:#d1fae5">
        <h6 class="mb-0"><i class="fas fa-file-alt me-2" style="color:#059669;"></i>Invoice Information</h6>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Nomor Invoice</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:#10b981;border-color:#10b981;"><i class="fas fa-file-invoice text-white"></i></span>
                    <input type="text" id="hdrNoInvoice" class="form-control fw-bold" value="{{ $invoice->no_invoice }}" style="background:#e9ecef;color:#2c3e50;letter-spacing:1px;" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label required-field">Tgl Invoice</label>
                <input type="date" id="hdrTglInvoice" class="form-control" value="{{ $invoice->tgl_invoice?->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Jatuh Tempo</label>
                <input type="date" id="hdrDueDate" class="form-control" value="{{ $invoice->due_date?->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select id="hdrStatus" class="form-select">
                    <option value="draft"  {{ $invoice->status === 'draft'  ? 'selected' : '' }}>Draft</option>
                    <option value="sent"   {{ $invoice->status === 'sent'   ? 'selected' : '' }}>Sent</option>
                    <option value="closed" {{ $invoice->status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Contract No</label>
                <input type="text" id="hdrContractNo" class="form-control" value="{{ $invoice->contract_no }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">PO Number</label>
                <input type="text" id="hdrPoNumber" class="form-control" value="{{ $invoice->po_number }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">To Customer</label>
                <input type="text" id="hdrToCustomer" class="form-control" value="{{ $invoice->to_customer }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Currency</label>
                <select id="hdrCurrency" class="form-select">
                    <option value="IDR" {{ $invoice->currency === 'IDR' ? 'selected' : '' }}>IDR</option>
                    <option value="USD" {{ $invoice->currency === 'USD' ? 'selected' : '' }}>USD</option>
                </select>
            </div>
            <div class="col-md-12">
                <div class="p-3 rounded-3 border" style="background:#f0fdf4;border-color:#bbf7d0!important;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fas fa-file-contract" style="color:#10b981;"></i>
                        <span class="fw-bold" style="color:#065f46;">
                            Job Order: {{ $invoice->joContract?->no_jo_cont ?? '-' }}
                            @if($invoice->joContract?->title) &mdash; {{ $invoice->joContract->title }} @endif
                        </span>
                        @if($kursUsd)
                        <span class="badge bg-dark ms-auto" style="font-size:.72rem;">Kurs {{ number_format($kursUsd, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <small class="text-muted">Customer: {{ $invoice->joContract?->contract?->customer?->customer ?? $invoice->to_customer ?? '-' }}</small>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Close Note</label>
                <textarea id="hdrCloseNote" class="form-control" rows="2">{{ $invoice->close_note }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Reopen Note</label>
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
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-danger" id="btnResetAllItems">
                <i class="fas fa-trash-alt me-1"></i> Reset All Nominal
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
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-bold" style="color:#1e40af;font-size:.875rem;">
                        Nominal Invoice (IDR) <span style="color:#dc3545;">*</span>
                    </label>
                    <div class="currency-group">
                        <span class="currency-label">IDR</span>
                        <input type="text" id="inputNominalInvoice" class="form-control currency-input" placeholder="0,00" autocomplete="off">
                    </div>
                    <div class="validate-hint hint-info" id="inputNominalHint"></div>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold" style="color:#1e40af;font-size:.875rem;">Chart of Account</label>
                    <select id="inputCoa" class="form-select select2-coa" style="font-size:.875rem;">
                        <option value="">&#8212; Select COA (optional) &#8212;</option>
                        @foreach ($coas as $coa)
                            <option value="{{ $coa->id_md_chart_of_account }}">{{ $coa->no_account }} &#8212; {{ $coa->account_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end pb-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="inputIsReimbursement" value="1">
                        <label class="form-check-label fw-bold" style="color:#1e40af;font-size:.875rem;" for="inputIsReimbursement">Reimburse</label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-1">
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
                    <col style="width:80px;">
                    <col style="width:140px;">
                    <col style="width:150px;">
                    <col style="width:115px;">
                    <col style="width:160px;">
                    <col style="width:120px;">
                    <col style="width:65px;">
                    <col style="width:92px;">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Category</th>
                        <th class="text-start">Item</th>
                        <th class="text-center">Sell IDR</th>
                        <th class="text-center">Sell USD</th>
                        <th class="text-center">Invoice (IDR)</th>
                        <th class="text-center">COA</th>
                        <th class="text-center">Reimb</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="invoiceItemsBody">
                    <tr id="loadingRow">
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="fas fa-circle-notch fa-spin me-2"></i> Loading data...
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end pe-2"><strong>GRAND TOTAL</strong></td>
                        <td>
                            <div class="footer-currency-wrap">
                                <span class="footer-currency-label" style="font-size:.62rem;padding:0 8px;">IDR</span>
                                <span class="footer-value" id="footerTotalSellIdr" style="font-size:.68rem;">0,00</span>
                            </div>
                        </td>
                        <td>
                            <div class="footer-currency-wrap">
                                <span class="footer-currency-label" style="font-size:.62rem;padding:0 8px;">USD</span>
                                <span class="footer-value" id="footerTotalSellUsd" style="font-size:.68rem;">0,00</span>
                            </div>
                        </td>
                        <td>
                            <div class="footer-currency-wrap">
                                <span class="footer-currency-label" style="font-size:.62rem;padding:0 8px;">IDR</span>
                                <span class="footer-value" id="footerTotalInvoice" style="font-size:.68rem;">0,00</span>
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

</div>{{-- end .container-fluid --}}

<div class="final-save-section">
    <div class="d-flex justify-content-between align-items-center">
        <div></div>
        <div class="d-flex gap-2">
            <button type="button" class="btn" disabled
                    style="padding:13px 30px;border-radius:10px;font-weight:700;font-size:1rem;border-color:#ccc;background-color:#f3f4f6;color:#9ca3af;cursor:not-allowed;">
                <i class="fas fa-file-pdf me-2"></i> Generate Invoice
            </button>
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
    let dpp=0, ppn=0, reimb=0, sellIdr=0, sellUsd=0;
    invoiceItems.forEach(item => {
        const nom = parseFloat(item.nominal_invoice) || 0;
        if (item.item_type === 'tax')     ppn   += nom;
        else if (item.is_reimbursement)   reimb += nom;
        else                              dpp   += nom;
        sellIdr += parseFloat(item.sell_idr) || 0;
        sellUsd += parseFloat(item.sell_usd) || 0;
    });
    const total = dpp+ppn+reimb;
    $('#summDpp').text(fmtNum(dpp));
    $('#summPpn').text(fmtNum(ppn));
    $('#summReimb').text(fmtNum(reimb));
    $('#summTotal').text(fmtNum(total));
    $('#footerTotalInvoice').text(fmtNum2(total));
    $('#footerTotalSellIdr').text(fmtNum2(sellIdr));
    $('#footerTotalSellUsd').text(sellUsd > 0 ? fmtNum2(sellUsd) : '0,00');
}

/* RENDER TABLE */
function renderTable() {
    const tbody = $('#invoiceItemsBody');
    tbody.empty();
    if (!invoiceItems.length) {
        tbody.append('<tr class="no-items-row"><td colspan="9"><i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i><p class="mb-0 fw-bold">Tidak ada item</p></td></tr>');
        $('#itemsCountBadge').text(0); recalcTotals(); return;
    }
    const {groups, order} = groupByCategory(invoiceItems);
    let globalNo = 1;
    order.forEach(cat => {
        const catItems = groups[cat];
        catItems.forEach((item, catIdx) => {
            const index = invoiceItems.indexOf(item);
            const isTax = item.item_type === 'tax';
            const catLabel = isTax ? 'Pajak (PPN)' : cat;
            const nomInv  = parseFloat(item.nominal_invoice) || 0;
            const sellIdr = parseFloat(item.sell_idr) || 0;
            const sellUsd = parseFloat(item.sell_usd) || 0;
            const rowClass = isTax ? 'row-tax' : '';
            const catCell = catIdx === 0
                ? '<td class="category-cell" rowspan="'+catItems.length+'">'+catLabel+'</td>'
                : '';
            const invoiceCell = nomInv > 0
                ? '<div class="footer-currency-wrap"><span class="footer-currency-label">IDR</span><span id="nomDisplay_'+index+'" class="footer-value">'+fmtNum2(nomInv)+'</span></div>'
                : '<span id="nomDisplay_'+index+'" class="coa-empty-badge">— belum diisi —</span>';
            const reimbCell = item.is_reimbursement
                ? '<span class="reimb-badge yes">Ya</span>'
                : '<span class="reimb-badge no">-</span>';
            const clrDis = nomInv <= 0 ? 'disabled' : '';
            const noteHtml = item.note ? '<div class="text-muted fst-italic" style="font-size:.72rem;">'+item.note+'</div>' : '';
            tbody.append(
                '<tr class="'+rowClass+'" data-index="'+index+'" id="row_'+index+'">' +
                '<td class="cell-center">'+globalNo+'</td>' +
                catCell +
                '<td class="text-start"><span class="fw-semibold" style="color:#2c3e50;">'+item.invoice_typ+'</span>'+noteHtml+'</td>' +
                '<td class="cell-right cell-readonly">'+(sellIdr>0?fmtNum2(sellIdr):'-')+'</td>' +
                '<td class="cell-right cell-readonly">'+(sellUsd>0?fmtNum2(sellUsd):'-')+'</td>' +
                '<td class="cell-center">'+invoiceCell+'</td>' +
                '<td class="coa-display-cell cell-center" id="coaCell_'+index+'">'+buildCoaDisplay(index, item.id_md_chart_of_account)+'</td>' +
                '<td class="cell-center">'+reimbCell+'</td>' +
                '<td class="cell-center">' +
                '<div class="d-flex gap-1 justify-content-center">' +
                '<button type="button" class="btn btn-edit-row btn-sm" onclick="openInputCard('+index+')"><i class="fas fa-pencil-alt"></i></button>' +
                '<button type="button" class="btn btn-clear-row btn-sm '+clrDis+'" '+clrDis+' onclick="clearItem('+index+')"><i class="fas fa-times"></i></button>' +
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
    $('#infoCategory').text(item.item_type === 'tax' ? 'Pajak (PPN)' : (item.invoice_ctg || '-'));
    $('#infoItem').text(item.invoice_typ || '-');
    $('#infoSellIdr').text(item.sell_idr > 0 ? fmtNum2(item.sell_idr) : '-');
    $('#infoSellUsd').text(item.sell_usd > 0 ? fmtNum2(item.sell_usd) : '-');
    const nomVal = parseFloat(item.nominal_invoice) || 0;
    $('#inputNominalInvoice').val(nomVal > 0 ? formatRupiah(nomVal.toString().replace('.',',')) : '');
    $('#inputNominalHint').text('').removeClass();
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
    $('#inputNominalInvoice').val('');
    $('#inputCoa').val('').trigger('change');
    $('#inputIsReimbursement').prop('checked', false);
    $('#inputNominalHint').text('').removeClass();
}

/* SAVE ITEM */
function saveItem() {
    const index  = parseInt($('#activeRowIndex').val());
    const itemId = parseInt($('#activeItemId').val());
    if (isNaN(index) || isNaN(itemId)) return;
    const nomVal  = parseRupiah($('#inputNominalInvoice').val());
    const coaId   = $('#inputCoa').val() || null;
    const isReimb = $('#inputIsReimbursement').is(':checked');
    showFloatingAlert('saving','Menyimpan...');
    $.ajax({
        url: bulkSaveUrl, method: 'POST', contentType: 'application/json',
        headers: {'X-CSRF-TOKEN': csrfToken},
        data: JSON.stringify({items:[{
            id_invoice_contract_item: itemId,
            nominal_invoice:          nomVal,
            id_md_chart_of_account:   coaId,
            is_reimbursement:         isReimb,
        }]}),
        success: r => {
            if (r.success) {
                invoiceItems[index].nominal_invoice        = nomVal;
                invoiceItems[index].id_md_chart_of_account = coaId;
                invoiceItems[index].is_reimbursement       = isReimb;
                showFloatingAlert('success', r.message || 'Tersimpan!');
                closeInputCard(); renderTable();
            } else {
                showFloatingAlert('error', r.message || 'Gagal menyimpan.');
            }
        },
        error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Terjadi kesalahan.')
    });
}

/* CLEAR ITEM */
function clearItem(index) {
    const item = invoiceItems[index];
    if (!item) return;
    showConfirm({
        title:'Reset Item?',
        desc:'Nominal invoice untuk "'+item.invoice_typ+'" akan direset ke 0.',
        okLabel:'Reset', okClass:'btn-danger', iconClass:'danger',
    }, () => {
        showFloatingAlert('saving','Mereset...');
        $.ajax({
            url: bulkSaveUrl, method: 'POST', contentType: 'application/json',
            headers: {'X-CSRF-TOKEN': csrfToken},
            data: JSON.stringify({items:[{
                id_invoice_contract_item: item.id_invoice_contract_item,
                nominal_invoice:          0,
                id_md_chart_of_account:   item.id_md_chart_of_account,
                is_reimbursement:         item.is_reimbursement,
            }]}),
            success: r => {
                if (r.success) {
                    invoiceItems[index].nominal_invoice = 0;
                    showFloatingAlert('success','Item direset!'); renderTable();
                } else {
                    showFloatingAlert('error', r.message || 'Gagal reset.');
                }
            },
            error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Terjadi kesalahan.')
        });
    });
}

/* RESET ALL */
$('#btnResetAllItems').on('click', function() {
    showConfirm({
        title:'Reset Semua Nominal?',
        desc:'Semua nominal invoice akan direset ke 0. Tindakan ini tidak dapat dibatalkan.',
        okLabel:'Reset Semua', okClass:'btn-danger', iconClass:'danger',
    }, () => {
        showFloatingAlert('saving','Mereset semua...');
        const payload = invoiceItems.map(item => ({
            id_invoice_contract_item: item.id_invoice_contract_item,
            nominal_invoice: 0,
            id_md_chart_of_account: item.id_md_chart_of_account,
            is_reimbursement: item.is_reimbursement,
        }));
        $.ajax({
            url: bulkSaveUrl, method: 'POST', contentType: 'application/json',
            headers: {'X-CSRF-TOKEN': csrfToken},
            data: JSON.stringify({items: payload}),
            success: r => {
                if (r.success) {
                    invoiceItems.forEach(i => i.nominal_invoice = 0);
                    showFloatingAlert('success','Semua nominal direset!');
                    closeInputCard(); renderTable();
                } else {
                    showFloatingAlert('error', r.message || 'Gagal reset.');
                }
            },
            error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Terjadi kesalahan.')
        });
    });
});

/* SAVE HEADER */
$('#btnSaveHeader').on('click', function() {
    showFloatingAlert('saving','Menyimpan header...');
    $.ajax({
        url: updateHeaderUrl, method: 'POST', contentType: 'application/json',
        headers: {'X-CSRF-TOKEN': csrfToken},
        data: JSON.stringify({
            tgl_invoice: $('#hdrTglInvoice').val(),
            due_date:    $('#hdrDueDate').val(),
            status:      $('#hdrStatus').val(),
            contract_no: $('#hdrContractNo').val(),
            po_number:   $('#hdrPoNumber').val(),
            to_customer: $('#hdrToCustomer').val(),
            currency:    $('#hdrCurrency').val(),
            close_note:  $('#hdrCloseNote').val(),
            reopen_note: $('#hdrReopenNote').val(),
        }),
        success: r => {
            if (r.success) showFloatingAlert('success', r.message || 'Header tersimpan!');
            else           showFloatingAlert('error', r.message || 'Gagal menyimpan header.');
        },
        error: xhr => showFloatingAlert('error', xhr.responseJSON?.message || 'Terjadi kesalahan.')
    });
});

$('#btnCancelItemInput').on('click', closeInputCard);
$('#btnSaveItemInput').on('click', saveItem);

$(document).ready(function() {
    $('#inputCoa').select2({theme:'bootstrap-5', placeholder:'— Select COA —', allowClear:true, width:'100%'});
    setupRupiahInput(document.getElementById('inputNominalInvoice'));
    renderTable();
});
</script>
@endpush
