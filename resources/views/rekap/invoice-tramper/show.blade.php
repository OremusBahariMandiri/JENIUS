@extends('layouts.app')

@section('title', 'Invoice – ' . $invoice->no_invoice)

@php
    $invoiceItemsJsStr = json_encode($invoiceItemsJs);
    $coaListJsStr = json_encode($coaListJs);
@endphp

@push('styles')
<style>
.invoiceShowPage .card { border:none; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,.08); }
.invoiceShowPage .card-header { border-radius:10px 10px 0 0 !important; padding:1rem 1.5rem; font-weight:600; }

/* TABLE */
.table-invoice thead th { background:#2c3e50; color:#fff; border:1px solid #2c3e50; padding:12px 10px; font-weight:600; font-size:.875rem; vertical-align:middle; text-align:center; white-space:nowrap; }
.table-invoice tbody td { border:1px solid #dee2e6; padding:10px 12px; vertical-align:middle; font-size:.875rem; background:#fff; }
.table-invoice tbody tr:hover td { background:#f0fdf4; }
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
.items-count-badge { background:#10b981; color:#fff; font-size:.75rem; font-weight:700; padding:2px 8px; border-radius:10px; margin-left:8px; }
.no-items-row td { text-align:center; padding:50px 20px; color:#6c757d; background:#f8f9fa !important; }
.reimb-badge { display:inline-block; font-size:.7rem; padding:2px 7px; border-radius:6px; font-weight:600; }
.reimb-badge.yes { background:#dbeafe; color:#1d4ed8; border:1px solid #bfdbfe; }
.reimb-badge.no  { background:#f3f4f6; color:#9ca3af; border:1px dashed #d1d5db; }

/* READ-ONLY FIELD STYLE */
.ro-field { background:#f8f9fa; border:1px solid #e5e7eb; border-radius:8px; padding:10px 14px; min-height:40px; }
.ro-field-label { font-size:.72rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
.ro-field-value { font-size:.9rem; font-weight:600; color:#2c3e50; }
.ro-field-value.empty { color:#9ca3af; font-style:italic; font-weight:400; }
.ro-textarea { background:#f8f9fa !important; border:1px solid #e5e7eb; border-radius:8px; padding:10px 14px; resize:none; color:#2c3e50; font-weight:500; }

/* FOOTER */
.final-save-section { position:sticky; bottom:0; background:#fff; padding:18px 20px; box-shadow:0 -4px 20px rgba(0,0,0,.1); border-radius:12px 12px 0 0; margin-top:30px; z-index:100; }
.btn-final-back { background:linear-gradient(135deg,#868686,#5e5e5e); border:none; color:#fff; padding:13px 35px; border-radius:10px; font-weight:700; box-shadow:0 4px 15px rgba(27,27,27,.3); transition:all .3s; }
.btn-final-back:hover { transform:translateY(-2px); color:#fff; }
.btn-final-edit { background:linear-gradient(135deg,#1e40af,#2563eb); border:none; color:#fff; padding:13px 35px; border-radius:10px; font-weight:700; box-shadow:0 4px 15px rgba(30,64,175,.3); transition:all .3s; }
.btn-final-edit:hover { transform:translateY(-2px); color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid invoiceShowPage">

{{-- Page Header --}}
<div class="card shadow mb-4">
    <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color:#d1fae5">
        <span class="fw-bold">
            <i class="fas fa-file-invoice me-2" style="color:#059669;"></i>
            Invoice &ndash; {{ $invoice->no_invoice }}
            <span class="badge bg-{{ $invoice->status === 'draft' ? 'warning text-dark' : ($invoice->status === 'closed' ? 'secondary' : 'success') }} ms-2" style="font-size:.7rem">{{ ucfirst($invoice->status) }}</span>
        </span>
        <a href="{{ route('invoice-tramper.index') }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

{{-- HEADER CARD --}}
<div class="card shadow mb-4">
    <div class="card-header fw-bold" style="background-color:#d1fae5">
        <i class="fas fa-file-alt me-2"></i>Invoice Information
    </div>
    <div class="card-body p-4">
        <div class="row g-3">

            @php
                $fields = [
                    ['label' => 'Invoice Number',   'value' => $invoice->no_invoice,                      'icon' => 'fas fa-file-invoice', 'highlight' => true],
                    ['label' => 'Status',           'value' => ucfirst($invoice->status ?? '-'),           'badge' => $invoice->status],
                    ['label' => 'Job Order',        'value' => $invoice->joTramper?->no_jo_tram ?? '-'],
                    ['label' => 'Title',            'value' => $invoice->title],
                    ['label' => 'Customer',         'value' => $invoice->to_customer],
                    ['label' => 'Contract No',      'value' => $invoice->contract_no],
                    ['label' => 'Invoice Date',     'value' => $invoice->tgl_invoice?->format('d/m/Y')],
                    ['label' => 'Due Date',         'value' => $invoice->due_date?->format('d/m/Y')],
                    ['label' => 'Vessel',           'value' => $invoice->kapal],
                    ['label' => 'Jetty',            'value' => $invoice->jetty],
                    ['label' => 'PO Number',        'value' => $invoice->po_number],
                    ['label' => 'Currency',         'value' => $invoice->currency],
                    ['label' => 'Vendor Number',    'value' => $invoice->vendor_number],
                    ['label' => 'SA/GR Number',     'value' => $invoice->sa_gr_number],
                    ['label' => 'BA Number',        'value' => $invoice->ba_number],
                    ['label' => 'Chart of Account', 'value' => $invoice->coa ? ($invoice->coa->no_account . ' — ' . $invoice->coa->account_name) : null],
                    ['label' => 'No. Voyage',       'value' => $invoice->no_voyage],
                    ['label' => 'GRT',              'value' => $invoice->grt],
                    ['label' => 'ETA',              'value' => $invoice->eta?->format('d/m/Y')],
                    ['label' => 'ETD',              'value' => $invoice->etd?->format('d/m/Y')],
                    ['label' => 'Cond of Payment',  'value' => $invoice->cond_of_payment],
                    ['label' => 'Period',           'value' => $invoice->period],
                    ['label' => 'Att',              'value' => $invoice->att],
                    ['label' => 'VAT Number',       'value' => $invoice->vat_number],
                    ['label' => 'Bar Code',         'value' => $invoice->bar_code],
                    ['label' => 'Office Phone',     'value' => $invoice->office_phone],
                    ['label' => 'Mobile Phone',     'value' => $invoice->mobile_phone],
                ];
                $textareaFields = [
                    ['label' => 'Address',       'value' => $invoice->address],
                    ['label' => 'Close Note',    'value' => $invoice->close_note],
                    ['label' => 'Reopen Note',   'value' => $invoice->reopen_note],
                ];
            @endphp

            @php
                $statusColors = ['draft' => '#d97706', 'sent' => '#059669', 'closed' => '#6b7280'];
                $badgeMap = ['draft' => 'warning text-dark', 'sent' => 'success', 'closed' => 'secondary'];
            @endphp

            @foreach($fields as $f)
            <div class="col-md-6">
                <div class="ro-field">
                    <div class="ro-field-label">{{ $f['label'] }}</div>
                    @if(!empty($f['badge']))
                        <div class="ro-field-value">
                            <span class="badge bg-{{ $badgeMap[$f['badge']] ?? 'secondary' }}">{{ $f['value'] }}</span>
                        </div>
                    @elseif(!empty($f['highlight']))
                        <div class="ro-field-value fw-bold" style="color:#1e3a5f;letter-spacing:1px;">{{ $f['value'] ?? '—' }}</div>
                    @else
                        <div class="ro-field-value {{ empty($f['value']) ? 'empty' : '' }}">{{ $f['value'] ?? '—' }}</div>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Textarea fields (full width) --}}
            @foreach($textareaFields as $t)
            <div class="col-md-6">
                <div class="ro-field-label mb-1 ms-1" style="font-size:.72rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;">{{ $t['label'] }}</div>
                <textarea class="form-control ro-textarea" rows="2" readonly>{{ $t['value'] }}</textarea>
            </div>
            @endforeach

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
        @if($invoice->kurs_invoice)
            <span style="background:#0369a1;color:#fff;padding:4px 12px;border-radius:8px;font-size:.8rem;font-weight:600;">
                <i class="fas fa-dollar-sign me-1"></i>Rate: IDR {{ number_format((float)$invoice->kurs_invoice, 2, ',', '.') }}
                @if($invoice->tgl_kurs_invoice)
                    <span class="ms-2 opacity-75" style="font-size:.75rem;">{{ $invoice->tgl_kurs_invoice->format('d M Y') }}</span>
                @endif
            </span>
        @endif
    </div>
    <div class="card-body p-4">

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-invoice table-bordered mb-0" style="table-layout:fixed;min-width:900px;width:100%;">
                <colgroup>
                    <col style="width:38px;">
                    <col style="width:80px;">
                    <col style="width:150px;">
                    <col style="width:100px;">
                    <col style="width:150px;">
                    <col style="width:100px;">
                    <col style="width:150px;">
                    <col style="width:120px;">
                    <col style="width:70px;">
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
                        <th class="text-center">Reimb.</th>
                    </tr>
                </thead>
                <tbody id="invoiceItemsBody">
                    <tr>
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
                        <td colspan="2"></td>
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
            <a href="{{ route('invoice-tramper.export.vda', $invoice->id_invoice_tramper) }}"
               target="_blank"
               class="btn"
               style="padding:13px 30px;border-radius:10px;font-weight:700;font-size:1rem;background:#059669;color:#fff;border-color:#059669;">
                <i class="fas fa-ship me-2"></i> Export VDA
            </a>
            <a href="{{ route('invoice-tramper.export.invoice', $invoice->id_invoice_tramper) }}"
               target="_blank"
               class="btn"
               style="padding:13px 30px;border-radius:10px;font-weight:700;font-size:1rem;background:#059669;color:#fff;border-color:#059669;">
                <i class="fas fa-file-invoice me-2"></i> Export Invoice
            </a>
            <a href="{{ route('invoice-tramper.export.reimbursement', $invoice->id_invoice_tramper) }}"
               target="_blank"
               class="btn"
               style="padding:13px 30px;border-radius:10px;font-weight:700;font-size:1rem;background:#059669;color:#fff;border-color:#059669;">
                <i class="fas fa-redo me-2"></i> Export Reimbursement
            </a>
            <a href="{{ route('invoice-tramper.export.kwitansi', $invoice->id_invoice_tramper) }}"
               target="_blank"
               class="btn"
               style="padding:13px 30px;border-radius:10px;font-weight:700;font-size:1rem;background:#059669;color:#fff;border-color:#059669;">
                <i class="fas fa-receipt me-2"></i> Export Kwitansi
            </a>
            <a href="{{ route('invoice-tramper.export.all', $invoice->id_invoice_tramper) }}"
               target="_blank"
               class="btn"
               style="padding:13px 30px;border-radius:10px;font-weight:700;font-size:1rem;background:#059669;color:#fff;border-color:#059669;">
                <i class="fas fa-layer-group me-2"></i> Export All
            </a>
            @can('update', $invoice)
            <a href="{{ route('invoice-tramper.edit', $invoice->id_invoice_tramper) }}" class="btn btn-final-edit">
                <i class="fas fa-pen me-1"></i> Edit
            </a>
            @endcan
            <a href="{{ route('invoice-tramper.index') }}" class="btn btn-final-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const invoiceItems = {!! $invoiceItemsJsStr !!};
const coaList      = {!! $coaListJsStr !!};

function fmtNum2(v) { return parseFloat(v||0).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function fmtNum(v)  { return parseFloat(v||0).toLocaleString('id-ID',{minimumFractionDigits:0,maximumFractionDigits:0}); }

function buildCoaDisplay(coaId) {
    if (!coaId) return '<span class="coa-empty-badge">— no COA —</span>';
    const coa = coaList.find(c => c.id == coaId);
    const label = coa ? coa.no+' - '+coa.name : coaId;
    return '<span class="coa-display-badge">'+label+'</span>';
}

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

function recalcTotals() {
    let dpp=0, ppn=0, reimb=0, sellIdr=0, sellUsd=0, invUsd=0;
    invoiceItems.forEach(item => {
        const nom    = parseFloat(item.nominal_invoice) || 0;
        const nomUsd = parseFloat(item.nominal_invoice_usd) || 0;
        if (item.item_type === 'tax')    ppn   += nom;
        else if (item.is_reimbursement)  reimb += nom;
        else                             dpp   += nom;
        sellIdr += parseFloat(item.sell_idr) || 0;
        sellUsd += parseFloat(item.sell_usd) || 0;
        invUsd  += nomUsd;
    });
    const total = dpp+ppn+reimb;
    $('#summDpp').text(fmtNum(dpp));
    $('#summPpn').text(fmtNum(ppn));
    $('#summReimb').text(fmtNum(reimb));
    $('#summTotal').text(fmtNum(total));
    $('#footerTotalInvoice').text(fmtNum2(total));
    $('#footerTotalInvoiceUsd').text(fmtNum2(invUsd));
    $('#footerTotalSellIdr').text(fmtNum2(sellIdr));
    $('#footerTotalSellUsd').text(sellUsd > 0 ? fmtNum2(sellUsd) : '0,00');
}

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
            const isTax    = item.item_type === 'tax';
            const catLabel = isTax ? 'Tax' : cat;
            const nomInv    = parseFloat(item.nominal_invoice) || 0;
            const nomInvUsd = parseFloat(item.nominal_invoice_usd) || 0;
            const sellIdr  = parseFloat(item.sell_idr) || 0;
            const sellUsd  = parseFloat(item.sell_usd) || 0;
            const rowClass = isTax ? 'row-tax' : '';
            const catCell  = catIdx === 0
                ? '<td class="category-cell" rowspan="'+catItems.length+'">'+catLabel+'</td>'
                : '';
            const invoiceCellUsd = '<div class="currency-group-cell"><span class="currency-label-cell">USD</span><span class="value-cell">'+fmtNum2(nomInvUsd)+'</span></div>';
            const invoiceCell    = '<div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">'+fmtNum2(nomInv)+'</span></div>';
            const reimbCell = item.is_reimbursement
                ? '<span class="reimb-badge yes">Yes</span>'
                : '<span class="reimb-badge no">No</span>';
            const noteHtml  = '';
            const persenHtml = (isTax && item.persen_pajak) ? '<small class="text-muted ms-1">('+item.persen_pajak+'%)</small>' : '';
            tbody.append(
                '<tr class="'+rowClass+'">' +
                '<td class="cell-center">'+globalNo+'</td>' +
                catCell +
                '<td class="text-start"><span class="fw-semibold" style="color:#2c3e50;">'+item.invoice_typ+'</span>'+persenHtml+'</td>' +
                '<td class="cell-right cell-readonly"><div class="currency-group-cell"><span class="currency-label-cell">USD</span><span class="value-cell">'+(sellUsd>0?fmtNum2(sellUsd):'0,00')+'</span></div></td>' +
                '<td class="cell-right cell-readonly"><div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">'+(sellIdr>0?fmtNum2(sellIdr):'0,00')+'</span></div></td>' +
                '<td class="cell-center">'+invoiceCellUsd+'</td>' +
                '<td class="cell-center">'+invoiceCell+'</td>' +
                '<td class="coa-display-cell cell-center">'+buildCoaDisplay(item.id_md_chart_of_account)+'</td>' +
                '<td class="cell-center">'+reimbCell+'</td>' +
                '</tr>'
            );
            globalNo++;
        });
    });
    $('#itemsCountBadge').text(invoiceItems.length);
    recalcTotals();
}

$(document).ready(function() { renderTable(); });
</script>
@endpush
