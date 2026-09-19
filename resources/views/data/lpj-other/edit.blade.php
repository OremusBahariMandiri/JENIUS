@extends('layouts.app')

@section('title', 'Edit LPJ Other')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .lpjOtherEditPage .card { border:none; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,.08); }
        .lpjOtherEditPage .card-header { border-radius:10px 10px 0 0 !important; font-weight:600; }

        /* FLOATING BADGE ALERT */
        .floating-badge-alert {
            position:fixed; top:80px; right:30px; z-index:9999;
            min-width:250px; padding:15px 20px; border-radius:12px;
            box-shadow:0 8px 25px rgba(0,0,0,.2); display:none;
            animation:slideInRight .4s ease-out;
        }
        .floating-badge-alert.show { display:flex; align-items:center; gap:12px; }
        .floating-badge-alert.alert-saving  { background:linear-gradient(135deg,#fbbf24,#f59e0b); color:white; }
        .floating-badge-alert.alert-success { background:linear-gradient(135deg,#10b981,#059669); color:white; }
        .floating-badge-alert.alert-error   { background:linear-gradient(135deg,#ef4444,#dc2626); color:white; }
        .floating-badge-alert .alert-text   { flex:1; font-weight:600; font-size:.95rem; }
        @keyframes slideInRight { from{transform:translateX(400px);opacity:0;} to{transform:translateX(0);opacity:1;} }
        @keyframes slideOutRight{ from{transform:translateX(0);opacity:1;} to{transform:translateX(400px);opacity:0;} }
        .floating-badge-alert.hiding { animation:slideOutRight .4s ease-in; }

        /* INFO HEADER */
        .info-header-card {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #86efac;
        }
        .info-header-card .info-label { font-size:.75rem; color:#6b7280; font-weight:600; text-transform:uppercase; letter-spacing:.5px; }
        .info-header-card .info-value { font-weight:700; color:#2c3e50; font-size:.95rem; }

        /* ITEMS TABLE */
        .table-lpj-items thead th {
            background:#2c3e50; color:white;
            padding:12px 10px; font-size:.8rem;
            font-weight:600; vertical-align:middle; text-align:center;
            position:sticky; top:0; z-index:10;
        }
        .table-lpj-items tbody td {
            border:1px solid #dee2e6; padding:10px;
            vertical-align:middle; background:#fff;
            font-size:.85rem;
        }
        .table-lpj-items .category-row td { background:#f0fdf4 !important; font-weight:700; color:#065f46; }
        .table-lpj-items .category-row td:first-child { color:#6b7280; }

        /* From LPJ row */
        .table-lpj-items tbody tr.row-from-lpj td { background:#eff6ff !important; }
        .table-lpj-items tbody tr.row-from-lpj:hover td { background:#dbeafe !important; }

        /* Input LPJ Amount */
        .lpj-amount-input {
            border:2px solid #dee2e6; border-radius:8px;
            padding:.4rem .6rem; width:100%; font-size:.85rem;
            transition:all .2s;
        }
        .lpj-amount-input:focus { border-color:#10b981; box-shadow:0 0 0 .2rem rgba(16,185,129,.25); outline:none; }
        .lpj-amount-input.over-hpp { border-color:#dc3545 !important; }

        /* COA Select */
        .coa-select { font-size:.8rem; border-radius:8px; }

        /* SUMMARY FOOTER */
        .summary-footer {
            background:#2c3e50; color:white; border-radius:10px;
            padding:15px 20px; margin-top:15px;
        }
        .summary-footer .label { font-size:.8rem; opacity:.8; }
        .summary-footer .value { font-size:1rem; font-weight:700; }

        /* LPJ Note */
        .lpj-origin-note {
            display:flex; align-items:center; gap:8px;
            margin-top:10px; padding:8px 14px;
            background:#eff6ff; border-radius:8px;
            border-left:3px solid #3b82f6;
            font-size:.8rem; color:#1e40af;
        }

        /* ADD NEW ITEM FORM */
        .add-item-section {
            background:linear-gradient(135deg,#f8f9fa,#e9ecef);
            border:2px dashed #10b981; border-radius:12px;
            padding:20px; margin-bottom:20px;
        }
        .add-item-section h6 { font-weight:700; color:#2c3e50; margin-bottom:15px; }
        .add-item-section .form-control, .add-item-section .form-select {
            border:2px solid #dee2e6; border-radius:8px; padding:.5rem .75rem;
        }
        .currency-group { display:flex; align-items:center; }
        .currency-label {
            background:#2c3e50; color:white; padding:.5rem .6rem;
            font-size:.7rem; border-radius:6px 0 0 6px; min-width:45px;
            text-align:center; font-weight:600;
        }
        .currency-input { border-radius:0 6px 6px 0 !important; border-left:none !important; }

        /* STICKY BOTTOM */
        .final-save-section {
            position:sticky; bottom:0; background:white;
            padding:15px 20px; box-shadow:0 -4px 20px rgba(0,0,0,.1);
            border-radius:12px 12px 0 0; margin-top:30px; z-index:100;
        }
        .btn-final-back {
            background:linear-gradient(135deg,#868686,#5e5e5e);
            border:none; color:white; padding:12px 30px;
            border-radius:12px; font-weight:700;
        }
        .btn-bulk-save {
            background:linear-gradient(135deg,#10b981,#059669);
            border:none; color:white; padding:12px 30px;
            border-radius:12px; font-weight:700;
        }

        /* Kurs badge */
        .kurs-badge {
            background:linear-gradient(135deg,#10b981,#059669);
            color:white; border-radius:8px; padding:8px 15px;
            font-size:.8rem; font-weight:600;
        }
    </style>
@endpush

@section('content')
@php
    $joOther = $lpj->joOther;
    $hasLpjRows = collect($mergedItems)->contains(fn($i) => !empty($i['origin_lpj_other']));
@endphp

<div id="floatingBadgeAlert" class="floating-badge-alert">
    <i class="fas fa-circle-notch fa-spin" id="alertIcon"></i>
    <div class="alert-text" id="alertText">Processing...</div>
</div>

<div class="container-fluid lpjOtherEditPage">
    <div class="row">
        <div class="col-lg-12">

            {{-- PAGE HEADER --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                     style="background-color:#d1fae5">
                    <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit LPJ Other — {{ $lpj->no_lpj_other }}</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('lpj-other.export-pdf', $lpj->id) }}" target="_blank"
                           class="btn btn-sm" style="background:#fff5f5; color:red; border:1px solid red;">
                            <i class="fas fa-file-pdf me-1"></i>Export PDF
                        </a>
                        <a href="{{ route('lpj-other.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>

            {{-- LPJ HEADER INFO --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black" style="background-color:#d1fae5">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>LPJ Information</h6>
                </div>
                <div class="card-body p-4">
                    <div class="info-header-card mb-4">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <div class="info-label">No. LPJ</div>
                                <div class="info-value text-success">{{ $lpj->no_lpj_other }}</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="info-label">JO Number</div>
                                <div class="info-value">{{ $joOther->no_jo_other ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="info-label">JO Title</div>
                                <div class="info-value">{{ $joOther->title ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="info-label">CA Amount</div>
                                <div class="info-value text-primary">
                                    IDR {{ number_format($lpj->amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold required-field">LPJ Date</label>
                            <input type="date" id="input_date" class="form-control"
                                   value="{{ $lpj->date ? $lpj->date->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Note</label>
                            <input type="text" id="input_note" class="form-control" value="{{ $lpj->note }}">
                        </div>
                    </div>

                    @if($joKurs)
                        <div class="kurs-badge d-inline-block">
                            <i class="fas fa-exchange-alt me-1"></i>
                            Kurs USD: IDR {{ number_format($joKurs->kurs_usd, 0, ',', '.') }}
                            @if($joKurs->tgl_kurs_usd)
                                <span class="opacity-75 ms-1">({{ $joKurs->tgl_kurs_usd->format('d M Y') }})</span>
                            @endif
                        </div>
                    @endif

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-success" id="btnSaveHeader">
                            <i class="fas fa-save me-1"></i>Update Header
                        </button>
                    </div>
                </div>
            </div>

            {{-- ITEMS CARD --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black" style="background-color:#d1fae5">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-list me-2"></i>LPJ Items</h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnRefreshKasbons">
                                <i class="fas fa-sync me-1"></i>Refresh CA
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">

                    {{-- TABLE --}}
                    <div class="table-responsive mb-3">
                        <table class="table table-lpj-items table-bordered" id="lpjItemsTable">
                            <thead>
                                <tr>
                                    <th style="width:4%;">No</th>
                                    <th style="width:15%;">CA No</th>
                                    <th style="width:15%;">Category</th>
                                    <th style="width:20%;">Item</th>
                                    <th style="width:12%;">HPP (IDR)</th>
                                    <th style="width:12%;">CA Amount</th>
                                    <th style="width:12%;">LPJ Amount</th>
                                    <th style="width:10%;">COA</th>
                                </tr>
                            </thead>
                            <tbody id="lpjItemsBody">
                                @php
                                    $groupedByKasbon = collect($mergedItems)->groupBy('id_kasbon_other_no');
                                    $globalNo = 1;
                                @endphp
                                @foreach($groupedByKasbon as $kasbonNo => $kasbonItems)
                                    @php $groupedByCtg = collect($kasbonItems)->groupBy('invoice_ctg'); @endphp
                                    @foreach($groupedByCtg as $ctg => $items)
                                        @foreach($items as $idx => $item)
                                            @php $isFromLpj = !empty($item['origin_lpj_other']); @endphp
                                            <tr class="item-row {{ $isFromLpj ? 'row-from-lpj' : '' }}"
                                                data-kasbon-item-id="{{ $item['id_kasbon_other_item'] }}"
                                                data-jo-item-id="{{ $item['id_jo_other_item'] }}"
                                                data-kasbon-no="{{ $kasbonNo }}"
                                                data-ctg="{{ $ctg }}"
                                                data-hpp="{{ $item['nilai_hpp_other_item'] }}"
                                                data-has-lpj="{{ $item['has_lpj'] ? '1' : '0' }}"
                                                data-lpj-item-id="{{ $item['id_lpj_other_item'] ?? '' }}"
                                                data-origin-lpj="{{ $item['origin_lpj_other'] ?? '' }}">
                                                <td class="text-center">{{ $globalNo }}</td>
                                                <td class="text-center" style="font-size:.75rem; color:#1e3a5f; font-weight:600;">
                                                    {{ $kasbonNo }}
                                                </td>
                                                <td>{{ $ctg }}</td>
                                                <td class="text-start">
                                                    <span class="fw-semibold">{{ $item['invoice_typ'] }}</span>
                                                    @if($isFromLpj)
                                                        <span class="badge ms-1" style="background:#3b82f6; font-size:.65rem;">From LPJ</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ number_format($item['nilai_hpp_other_item'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($item['nilai_kasbon'], 0, ',', '.') }}</td>
                                                <td>
                                                    <div class="currency-group">
                                                        <span class="currency-label">IDR</span>
                                                        <input type="text"
                                                               class="form-control currency-input lpj-amount-input"
                                                               data-kasbon-item-id="{{ $item['id_kasbon_other_item'] }}"
                                                               data-jo-item-id="{{ $item['id_jo_other_item'] }}"
                                                               data-hpp="{{ $item['nilai_hpp_other_item'] }}"
                                                               value="{{ $item['amount_lpj'] > 0 ? number_format($item['amount_lpj'], 0, ',', '.') : '' }}"
                                                               placeholder="0">
                                                    </div>
                                                </td>
                                                <td>
                                                    <select class="form-select coa-select"
                                                            data-kasbon-item-id="{{ $item['id_kasbon_other_item'] }}">
                                                        <option value="">- COA -</option>
                                                        @foreach($coaList as $coa)
                                                            <option value="{{ $coa->id_md_chart_of_account }}"
                                                                {{ ($item['id_md_chart_of_account'] ?? '') == $coa->id_md_chart_of_account ? 'selected' : '' }}>
                                                                {{ $coa->no_account }} - {{ Str::limit($coa->account_name, 25) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            @php $globalNo++; @endphp
                                        @endforeach
                                    @endforeach
                                @endforeach
                                @if(empty($mergedItems))
                                    <tr class="no-items-row">
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                                            No items available. Add Cash Advances first.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot style="background:#2c3e50; color:white;">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold px-3">TOTAL</td>
                                    <td class="text-end fw-bold px-3" id="footerTotalHpp">
                                        {{ number_format(collect($mergedItems)->sum('nilai_hpp_other_item'), 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-bold px-3" id="footerTotalKasbon">
                                        {{ number_format(collect($mergedItems)->sum('nilai_kasbon'), 0, ',', '.') }}
                                    </td>
                                    <td class="fw-bold px-3" id="footerTotalLpj">0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="lpj-origin-note" id="lpjOriginNote" style="{{ $hasLpjRows ? '' : 'display:none;' }}">
                            <i class="fas fa-info-circle" style="color:#3b82f6; flex-shrink:0;"></i>
                            <span>Rows highlighted in <strong>blue</strong> are items added from LPJ page.</span>
                        </div>
                    </div>

                    {{-- ADD NEW ITEM SECTION --}}
                    <div class="add-item-section" id="addItemSection">
                        <h6><i class="fas fa-plus-square me-2 text-success"></i>Add New Item from LPJ</h6>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Cash Advance</label>
                                <select id="new_kasbon_id" class="form-select">
                                    <option value="">Select CA...</option>
                                    @foreach($lpj->kasbons as $lk)
                                        @if($lk->kasbonOther)
                                            <option value="{{ $lk->kasbonOther->id_kasbon_other }}">
                                                {{ $lk->kasbonOther->id_kasbon_other }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Category</label>
                                <select id="new_invoice_ctg" class="form-select">
                                    <option value="">Select Category...</option>
                                    @foreach($invoices->groupBy('invoice_ctg') as $ctg => $invGroup)
                                        <option value="{{ $ctg }}">{{ $ctg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Item</label>
                                <select id="new_invoice_id" class="form-select" disabled>
                                    <option value="">Select category first</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">HPP (IDR)</label>
                                <div class="currency-group">
                                    <span class="currency-label">IDR</span>
                                    <input type="text" id="new_hpp" class="form-control currency-input" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Income IDR</label>
                                <div class="currency-group">
                                    <span class="currency-label">IDR</span>
                                    <input type="text" id="new_pendapatan_idr" class="form-control currency-input" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Income USD</label>
                                <div class="currency-group">
                                    <span class="currency-label">USD</span>
                                    <input type="text" id="new_pendapatan_usd" class="form-control currency-input" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">CA Amount</label>
                                <div class="currency-group">
                                    <span class="currency-label">IDR</span>
                                    <input type="text" id="new_nilai_kasbon" class="form-control currency-input" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">LPJ Amount</label>
                                <div class="currency-group">
                                    <span class="currency-label">IDR</span>
                                    <input type="text" id="new_amount_lpj" class="form-control currency-input" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">COA</label>
                                <select id="new_coa" class="form-select">
                                    <option value="">- Select COA -</option>
                                    @foreach($coaList as $coa)
                                        <option value="{{ $coa->id_md_chart_of_account }}">
                                            {{ $coa->no_account }} - {{ Str::limit($coa->account_name, 35) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end mt-1">
                                <button type="button" class="btn btn-success px-4" id="btnAddNewItem">
                                    <i class="fas fa-plus me-1"></i>Add Item
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- STICKY BOTTOM --}}
<div class="final-save-section">
    <div class="d-flex justify-content-between align-items-center">
        <div id="summaryTotal" class="text-muted" style="font-size:.9rem;"></div>
        <div class="d-flex gap-2">
            <a href="{{ route('lpj-other.index') }}" class="btn btn-final-back">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
            <button type="button" class="btn btn-bulk-save" id="btnBulkSave">
                <i class="fas fa-save me-1"></i>Save All Items
            </button>
        </div>
    </div>
</div>

{{-- Refresh Kasbon Modal --}}
<div class="modal fade" id="refreshKasbonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#d1fae5;">
                <h5 class="modal-title fw-bold"><i class="fas fa-sync me-2"></i>Add New Cash Advance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="refreshKasbonBody">
                <p class="text-muted text-center"><i class="fas fa-spinner fa-spin me-2"></i>Loading...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btnAddKasbons">
                    <i class="fas fa-plus me-1"></i>Add Selected
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// ========================================
// GLOBALS
// ========================================
const lpjId        = {{ $lpj->id }};
const idLpjOther   = '{{ $lpj->id_lpj_other }}';
const idJoOther    = '{{ $lpj->id_jo_other }}';
const csrfToken    = $('meta[name="csrf-token"]').attr('content');

const invoicesByCtg = {
    @foreach($invoices->groupBy('invoice_ctg') as $ctg => $invGroup)
    '{{ $ctg }}': [
        @foreach($invGroup as $inv)
        { id: '{{ $inv->id_md_invoice }}', text: '{{ addslashes($inv->invoice_typ) }}' },
        @endforeach
    ],
    @endforeach
};

// ========================================
// FLOATING ALERT
// ========================================
function showFloatingAlert(type, message) {
    const $a = $('#floatingBadgeAlert');
    $a.removeClass('alert-saving alert-success alert-error hiding');
    const icons = { saving:'fas fa-circle-notch fa-spin', success:'fas fa-check-circle', error:'fas fa-exclamation-circle' };
    const classes = { saving:'alert-saving', success:'alert-success', error:'alert-error' };
    $a.addClass(classes[type] || 'alert-error');
    $('#alertIcon').attr('class', icons[type] || icons.error);
    $('#alertText').text(message);
    $a.addClass('show');
    if (type !== 'saving') setTimeout(hideFloatingAlert, 3000);
}
function hideFloatingAlert() {
    const $a = $('#floatingBadgeAlert');
    $a.addClass('hiding');
    setTimeout(() => $a.removeClass('show hiding'), 400);
}

// ========================================
// FORMAT / PARSE RUPIAH
// ========================================
function formatNumber(n) {
    return Number(n).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}
function parseNum(v) {
    if (!v) return 0;
    return parseFloat(v.toString().replace(/\./g,'').replace(',','.')) || 0;
}
function formatRupiah(v) {
    let n = v.toString().replace(/[^\d]/g,'');
    if (!n) return '';
    return parseInt(n).toLocaleString('id-ID');
}
function setupRupiahInput(el) {
    el.addEventListener('input', function() {
        const raw = this.value.replace(/\./g,'').replace(/[^\d]/g,'');
        if (!raw) { this.value = ''; return; }
        const cursor = this.selectionStart;
        const oldLen = this.value.length;
        this.value = parseInt(raw).toLocaleString('id-ID');
        const newLen = this.value.length;
        this.setSelectionRange(cursor + (newLen - oldLen), cursor + (newLen - oldLen));
    });
}
document.querySelectorAll('.lpj-amount-input, #new_hpp, #new_pendapatan_idr, #new_pendapatan_usd, #new_nilai_kasbon, #new_amount_lpj')
    .forEach(el => setupRupiahInput(el));

// ========================================
// FOOTER TOTAL
// ========================================
function updateFooterTotal() {
    let total = 0;
    $('.lpj-amount-input').each(function() {
        total += parseNum($(this).val());
    });
    $('#footerTotalLpj').text(formatNumber(total));
    $('#summaryTotal').text(`Total LPJ Amount: IDR ${formatNumber(total)}`);
}

// HPP validation on input
$(document).on('input', '.lpj-amount-input', function() {
    const hpp = parseFloat($(this).data('hpp')) || 0;
    const val = parseNum($(this).val());
    if (hpp > 0 && val > hpp) {
        $(this).addClass('over-hpp');
    } else {
        $(this).removeClass('over-hpp');
    }
    updateFooterTotal();
});

// ========================================
// SAVE HEADER
// ========================================
$('#btnSaveHeader').on('click', function() {
    const date = $('#input_date').val();
    if (!date) { showFloatingAlert('error','Please fill LPJ Date'); return; }

    showFloatingAlert('saving', 'Updating header...');
    $.ajax({
        url: '{{ route('lpj-other.header.update', $lpj->id) }}',
        method: 'POST',
        data: { id_jo_other: idJoOther, date, note: $('#input_note').val(), _token: csrfToken },
        success(r) {
            if (r.success) showFloatingAlert('success', 'Header updated!');
            else showFloatingAlert('error', r.message || 'Failed');
        },
        error(xhr) { showFloatingAlert('error', xhr.responseJSON?.message || 'Failed'); }
    });
});

// ========================================
// BULK SAVE ITEMS
// ========================================
$('#btnBulkSave').on('click', function() {
    const items = [];
    let hasOverHpp = false;

    $('.item-row').each(function() {
        const kasbonItemId = $(this).data('kasbon-item-id');
        const joItemId     = $(this).data('jo-item-id');
        const hpp          = parseFloat($(this).data('hpp')) || 0;
        const $input       = $(this).find('.lpj-amount-input');
        const $coa         = $(this).find('.coa-select');
        const amountLpj    = parseNum($input.val());
        const coa          = $coa.val() || null;

        if (hpp > 0 && amountLpj > hpp) { hasOverHpp = true; }

        items.push({
            id_kasbon_other_item:   kasbonItemId,
            id_jo_other_item:       joItemId,
            amount_lpj:             amountLpj,
            id_md_chart_of_account: coa,
        });
    });

    if (hasOverHpp) {
        showFloatingAlert('error', 'Some LPJ amounts exceed HPP. Please check.');
        return;
    }

    if (!items.length) { showFloatingAlert('error', 'No items to save'); return; }

    showFloatingAlert('saving', 'Saving items...');

    $.ajax({
        url: '{{ route('lpj-other.items.bulk-save', $lpj->id) }}',
        method: 'POST',
        data: JSON.stringify({ items, _token: csrfToken }),
        contentType: 'application/json',
        success(r) {
            if (r.success) showFloatingAlert('success', 'Items saved successfully!');
            else showFloatingAlert('error', r.message || 'Failed');
        },
        error(xhr) { showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to save'); }
    });
});

// ========================================
// CATEGORY → ITEM DROPDOWN
// ========================================
$('#new_invoice_ctg').on('change', function() {
    const ctg = $(this).val();
    const $item = $('#new_invoice_id');
    if (!ctg) { $item.prop('disabled', true).html('<option value="">Select category first</option>'); return; }
    const list = invoicesByCtg[ctg] || [];
    let opts = '<option value="">Select item...</option>';
    list.forEach(i => { opts += `<option value="${i.id}">${i.text}</option>`; });
    $item.prop('disabled', false).html(opts);
});

// ========================================
// ADD NEW ITEM FROM LPJ
// ========================================
$('#btnAddNewItem').on('click', function() {
    const kasbonId    = $('#new_kasbon_id').val();
    const invoiceId   = $('#new_invoice_id').val();
    const invoiceCtg  = $('#new_invoice_ctg').val();
    const hpp         = parseNum($('#new_hpp').val());
    const idr         = parseNum($('#new_pendapatan_idr').val());
    const usd         = parseNum($('#new_pendapatan_usd').val());
    const nilaiKasbon = parseNum($('#new_nilai_kasbon').val());
    const amountLpj   = parseNum($('#new_amount_lpj').val());
    const coa         = $('#new_coa').val() || null;

    if (!kasbonId || !invoiceId || !invoiceCtg) {
        showFloatingAlert('error','Please fill CA, Category, and Item');
        return;
    }
    if (amountLpj <= 0) {
        showFloatingAlert('error','LPJ Amount must be greater than 0');
        return;
    }
    if (hpp > 0 && amountLpj > hpp) {
        showFloatingAlert('error','LPJ Amount cannot exceed HPP');
        return;
    }
    if (nilaiKasbon <= 0) {
        showFloatingAlert('error','CA Amount must be greater than 0');
        return;
    }

    showFloatingAlert('saving', 'Adding item...');
    $('#btnAddNewItem').prop('disabled', true);

    $.ajax({
        url: '{{ route('lpj-other.item.store-new', $lpj->id) }}',
        method: 'POST',
        data: {
            id_md_invoice:          invoiceId,
            invoice_ctg:            invoiceCtg,
            id_kasbon_other:        kasbonId,
            pendapatan_idr:         idr,
            pendapatan_usd:         usd,
            hpp_ops:                hpp,
            nilai_kasbon:           nilaiKasbon,
            amount_lpj:             amountLpj,
            id_md_chart_of_account: coa,
            _token:                 csrfToken,
        },
        success(r) {
            if (r.success) {
                showFloatingAlert('success','Item added! Reloading...');
                setTimeout(() => location.reload(), 1000);
            } else {
                showFloatingAlert('error', r.message || 'Failed to add item');
                $('#btnAddNewItem').prop('disabled', false);
            }
        },
        error(xhr) {
            showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to add item');
            $('#btnAddNewItem').prop('disabled', false);
        }
    });
});

// ========================================
// REFRESH KASBONS
// ========================================
$('#btnRefreshKasbons').on('click', function() {
    const modal = new bootstrap.Modal(document.getElementById('refreshKasbonModal'));
    modal.show();

    $('#refreshKasbonBody').html('<p class="text-muted text-center"><i class="fas fa-spinner fa-spin me-2"></i>Loading...</p>');

    $.get('{{ route('lpj-other.refresh-kasbons', $lpj->id) }}', function(r) {
        if (!r.success) {
            $('#refreshKasbonBody').html('<p class="text-danger">Failed to load.</p>');
            return;
        }
        if (!r.new_kasbons.length) {
            $('#refreshKasbonBody').html('<p class="text-muted text-center py-3"><i class="fas fa-check-circle text-success me-1"></i>All cash advances already added.</p>');
            return;
        }
        let html = `<p class="text-muted mb-3">${r.found} new cash advance(s) found:</p>`;
        r.new_kasbons.forEach(k => {
            html += `
                <div class="form-check border rounded p-3 mb-2">
                    <input class="form-check-input new-kasbon-check" type="checkbox"
                           value="${k.id_kasbon_other}" id="ck_${k.id_kasbon_other}">
                    <label class="form-check-label w-100" for="ck_${k.id_kasbon_other}">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">${k.id_kasbon_other}</span>
                            <span class="text-success fw-bold">IDR ${formatNumber(k.total_kasbon)}</span>
                        </div>
                        <small class="text-muted">${k.tgl_kasbon ?? ''} — ${k.items_count} item(s)</small>
                    </label>
                </div>`;
        });
        $('#refreshKasbonBody').html(html);
    });
});

$('#btnAddKasbons').on('click', function() {
    const ids = [];
    $('.new-kasbon-check:checked').each(function() { ids.push($(this).val()); });
    if (!ids.length) { alert('Please select at least one cash advance'); return; }

    showFloatingAlert('saving','Adding cash advances...');
    $.ajax({
        url: '{{ route('lpj-other.add-kasbons', $lpj->id) }}',
        method: 'POST',
        data: { kasbon_ids: ids, _token: csrfToken },
        success(r) {
            if (r.success) {
                showFloatingAlert('success', r.message);
                bootstrap.Modal.getInstance(document.getElementById('refreshKasbonModal'))?.hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showFloatingAlert('error', r.message || 'Failed');
            }
        },
        error(xhr) { showFloatingAlert('error', xhr.responseJSON?.message || 'Failed'); }
    });
});

// ========================================
// INIT
// ========================================
$(document).ready(function() {
    updateFooterTotal();

    // Select2
    $('#new_kasbon_id, #new_invoice_ctg, #new_invoice_id, #new_coa').select2({
        theme: 'bootstrap-5', width: '100%'
    });
});
</script>
@endpush