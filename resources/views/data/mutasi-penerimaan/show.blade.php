@extends('layouts.app')

@section('title', 'Detail Receive Mutation')

@push('styles')
<style>
/* ── Page wrapper ── */
.mutasiShowPage .card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,.08);
}
.mutasiShowPage .card-header {
    border-radius: 10px 10px 0 0 !important;
    padding: 1rem 1.5rem;
    font-weight: 600;
}

/* Info section uses disabled form inputs */

/* ── Detail table ── */
.table-detail thead th {
    background: #2d3748;
    color: #fff;
    font-size: .76rem;
    padding: 10px 12px;
    font-weight: 600;
    white-space: nowrap;
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
.table-detail tbody td.num-cell { text-align: right; }
.table-detail tbody tr:hover td { background-color: #f8f9fa; }
.table-detail tfoot td {
    background: #fff;
    color: #1e3a5f;
    font-weight: 700;
    padding: 10px 12px;
    font-size: .875rem;
    border-top: 2px solid #e2e8f0;
}
.table-detail tfoot td.num-cell { text-align: right; }

.currency-group-cell { display:flex; align-items:center; gap:5px; }
.currency-label-cell { background-color:#e9ecef; color:#495057; border:1px solid #ced4da; padding:0.2rem 0.5rem; font-size:0.78rem; border-radius:4px; min-width:42px; text-align:center; font-weight:600; white-space:nowrap; }
.value-cell { flex:1; text-align:right; font-weight:600; font-size:0.88rem; color:#1e293b; }
.currency-label-footer-dark { background-color:#2c3e50; color:#fff; border:1px solid rgba(255,255,255,0.3); padding:0.2rem 0.5rem; font-size:0.78rem; border-radius:4px; min-width:42px; text-align:center; font-weight:600; white-space:nowrap; }
.value-footer-cell { flex:1; text-align:right; font-weight:700; font-size:0.88rem; color:#1e293b; }



/* ── Back bar ── */
.final-bar {
    position: sticky;
    bottom: 0;
    background: #fff;
    padding: 14px 20px;
    box-shadow: 0 -4px 20px rgba(0,0,0,.1);
    border-radius: 12px 12px 0 0;
    margin-top: 20px;
    z-index: 100;
}
.btn-final-back {
    background: linear-gradient(135deg,#868686,#5e5e5e);
    border: none;
    color: #fff;
    padding: 11px 32px;
    border-radius: 10px;
    font-weight: 700;
    font-size: .95rem;
    transition: all .3s;
}
.btn-final-back:hover { transform: translateY(-2px); color: #fff; }
.btn-to-edit {
    background: linear-gradient(135deg,#16a34a,#15803d);
    border: none;
    color: #fff;
    padding: 11px 32px;
    border-radius: 10px;
    font-weight: 700;
    font-size: .95rem;
    transition: all .3s;
}
.btn-to-edit:hover { transform: translateY(-2px); color: #fff; }

/* ── Modal kasbon ── */
#modalKasbonInfo .modal-content { border-radius:16px; border:none; box-shadow:0 24px 64px rgba(0,0,0,.18); overflow:hidden; }
#modalKasbonInfo .modal-header { background:#D1FAE5; padding:16px 22px; border:none; }
#modalKasbonInfo .modal-header .modal-title { color:black; font-size:.9rem; font-weight:700; letter-spacing:.01em; }
#modalKasbonInfo .modal-header .modal-subtitle { color:#374151; font-size:.73rem; margin-top:2px; }
#modalKasbonInfo .ki-info-strip { display:flex; gap:0; background:#f8fafc; border-bottom:1px solid #e2e8f0; }
#modalKasbonInfo .ki-info-cell { flex:1; padding:10px 18px; border-right:1px solid #e2e8f0; }
#modalKasbonInfo .ki-info-cell:last-child { border-right:none; }
#modalKasbonInfo .ki-info-lbl { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:3px; }
#modalKasbonInfo .ki-info-val { font-size:.82rem; font-weight:600; color:#0f172a; }
#modalKasbonInfo .ki-table { width:100%; border-collapse:collapse; font-size:.8rem; }
#modalKasbonInfo .ki-table thead th { background:#D1FAE5; color:black; padding:9px 12px; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; white-space:nowrap; border:none; }
#modalKasbonInfo .ki-table thead th.num { text-align:right; }
#modalKasbonInfo .ki-table tbody tr { border-bottom:1px solid #f1f5f9; }
#modalKasbonInfo .ki-table tbody tr:hover td { background:#f8fafc; }
#modalKasbonInfo .ki-table tbody td { padding:9px 12px; vertical-align:middle; background:#fff; color:#1e293b; }
#modalKasbonInfo .ki-table tbody td.num { text-align:right; font-variant-numeric:tabular-nums; font-weight:600; }
#modalKasbonInfo .ki-table tfoot td { background:#f5f6f7; color:#000; padding:9px 12px; font-weight:700; font-size:.8rem; border:none; white-space:nowrap; }
#modalKasbonInfo .ki-table tfoot td.num { text-align:right; font-variant-numeric:tabular-nums; }
</style>
@endpush

@section('content')

<div class="container-fluid mutasiShowPage">
    <div class="row">
        <div class="col-lg-12">

            {{-- Page Header --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                    style="background-color:#dcfce7;">
                    <span class="fw-bold"><i class="fas fa-hand-holding-usd me-2"></i>Receive Mutation — Detail</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('mutasi-penerimaan.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                        <a href="{{ route('mutasi-penerimaan.edit', $mp->id) }}" class="btn btn-sm text-white"
                            style="background:#16a34a;">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    </div>
                </div>
            </div>

            {{-- Header Info Card --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black" style="background-color:#dcfce7;">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Receive Mutation Info</h6>
                </div>
                <div class="card-body p-4">
                    @php
                        $totalDetail = $mp->vouchers->flatMap->details->count();
                    @endphp
                    <div class="row">
                        {{-- Voucher No. --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Voucher No.</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#16a34a;color:white;"><i class="fas fa-hashtag"></i></span>
                                <input type="text" class="form-control fw-bold" disabled
                                    value="{{ $mp->nomor ?? '—' }}"
                                    style="background-color:#f0fdf4;color:#14532d;letter-spacing:1px;">
                            </div>
                        </div>
                        {{-- Date --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="text" class="form-control" disabled
                                value="{{ $mp->tanggal ? \Carbon\Carbon::parse($mp->tanggal)->format('d M Y') : '—' }}">
                        </div>
                        {{-- Bank / Cash --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Bank / Cash</label>
                            <input type="text" class="form-control" disabled
                                value="{{ $singleVoucher && $singleVoucher->coa ? '[' . $singleVoucher->coa->payment_type . '] ' . $singleVoucher->coa->no_account . ' — ' . $singleVoucher->coa->account_name : '—' }}">
                        </div>
                        {{-- No. Cek --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">No. Cek / BG / Ref</label>
                            <input type="text" class="form-control" disabled
                                value="{{ $mp->no_cek ?: '—' }}">
                        </div>
                        {{-- Kurs --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Exchange Rate</label>
                            <input type="text" class="form-control" disabled
                                value="@if($mp->mata_uang && $mp->mata_uang !== 'IDR'){{ $mp->mata_uang }} @ {{ number_format($mp->kurs, 2, ',', '.') }}{{ $mp->tgl_kurs ? ' · ' . \Carbon\Carbon::parse($mp->tgl_kurs)->format('d/m/Y') : '' }}@else IDR (1:1) @endif">
                        </div>
                        {{-- Created At --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Created At</label>
                            <input type="text" class="form-control" disabled
                                value="{{ $mp->created_at ? $mp->created_at->format('d M Y, H:i') : '—' }}">
                        </div>
                        {{-- Memo --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Memo</label>
                            <textarea class="form-control" rows="3" disabled>{{ $mp->memo ?: '—' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Details Table Card --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                    style="background-color:#dcfce7;">
                    <h6 class="mb-0"><i class="fas fa-list-ul me-2"></i>Payment Details</h6>
                    <span class="badge" style="background:#16a34a;color:#fff;font-size:.78rem;" id="detailCountBadge">
                        {{ $totalDetail }} detail
                    </span>
                </div>
                <div class="card-body p-4">
                    @if($singleVoucher)
                    <div class="table-responsive">
                        <table class="table table-detail table-bordered" id="detailTable-{{ $singleVoucher->id }}">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th style="width:90px;">Type</th>
                                    <th style="width:130px;">Payment Type</th>
                                    <th>Customer / Release To</th>
                                    <th>JO / CA</th>
                                    <th>Dept / Branch</th>
                                    <th style="width:130px;">COA</th>
                                    <th style="width:160px;" class="num-cell">Amount</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody id="detailTableBody-{{ $singleVoucher->id }}">
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <i class="fas fa-circle-notch fa-spin fa-2x d-block mb-2"></i>
                                        Loading...
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot id="detailTableFoot-{{ $singleVoucher->id }}"></tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        <p class="fw-bold">No details found</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Sticky back bar --}}
<div class="final-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted" style="font-size:.82rem;">
            <i class="fas fa-eye me-1"></i> Read-only view
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('mutasi-penerimaan.index') }}" class="btn btn-final-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('mutasi-penerimaan.edit', $mp->id) }}" class="btn btn-to-edit">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        </div>
    </div>
</div>

{{-- ── Modal: Cash Advance Info ── --}}
<div class="modal fade" id="modalKasbonInfo" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <div class="modal-title"><i class="fas fa-book-open me-2"></i>Cash Advance Detail — <span id="ki-nomor-title"></span></div>
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
                            <tr><td colspan="5" class="text-center text-muted py-4">—</td></tr>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ── helpers ──
function fmt(n) {
    return Number(n).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function escH(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Kasbon info modal ──
function showKasbonInfo(btn) {
    const nomor   = btn.dataset.nomor   || '—';
    const jo      = btn.dataset.jo      || '—';
    const cust    = btn.dataset.cust    || '—';
    const dept    = btn.dataset.dep     || '—';
    const cabang  = btn.dataset.cab     || '—';
    const tgl     = btn.dataset.tgl     || '—';
    const nilai   = parseFloat(btn.dataset.nilai   || 0);
    const pj      = parseFloat(btn.dataset.pj      || 0);
    const selisih = parseFloat(btn.dataset.selisih || 0);

    document.getElementById('ki-nomor-title').textContent = nomor;
    document.getElementById('ki-item-count').textContent  = 'CA Summary';
    document.getElementById('ki-customer').textContent    = cust;
    document.getElementById('ki-jo').textContent          = jo;
    document.getElementById('ki-dept').textContent        = dept;
    document.getElementById('ki-cabang').textContent      = cabang;
    document.getElementById('ki-tgl').textContent         = tgl;

    const items = JSON.parse(btn.dataset.items || '[]');
    const tbody = document.getElementById('ki-tbody');
    const tfoot = document.getElementById('ki-tfoot');
    const sCls  = selisih > 0.001 ? 'color:#16a34a;' : selisih < -0.001 ? 'color:#dc2626;' : '';

    if (items.length > 0) {
        tbody.innerHTML = items.map((it, idx) => {
            const bCls = it.selisih > 0.001 ? 'color:#16a34a;' : it.selisih < -0.001 ? 'color:#dc2626;' : '';
            return `<tr>
<td class="text-center fw-bold" style="font-size:.82rem;">${idx+1}</td>
<td style="font-size:.82rem;">${escH(it.label||'—')}</td>
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

// ── Load & render details (read-only — no action buttons) ──
@if($singleVoucher)
const VOUCHER_DETAILS_URL = '/data/mutasi-penerimaan/voucher/{{ $singleVoucher->id }}/details';
const VOUCHER_ID = {{ $singleVoucher->id }};

$(document).ready(function() {
    $.get(VOUCHER_DETAILS_URL, function(r) {
        if (r.success) renderDetailTable(r.data);
        else showError();
    }).fail(showError);
});

function showError() {
    $(`#detailTableBody-${VOUCHER_ID}`).html(
        `<tr><td colspan="9" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle me-1"></i> Failed to load details</td></tr>`
    );
}

function renderDetailTable(details) {
    const tbody = $(`#detailTableBody-${VOUCHER_ID}`);
    const tfoot = $(`#detailTableFoot-${VOUCHER_ID}`);

    if (!details || details.length === 0) {
        tbody.html(`<tr><td colspan="9" class="text-center text-muted py-5"><i class="fas fa-inbox fa-2x d-block mb-2"></i>No details found</td></tr>`);
        tfoot.html('');
        $('#detailCountBadge').text('0 detail');
        return;
    }

    const tipeLabelMap = {
        'uang_muka_pembayaran'  : 'Down Payment',
        'penerimaan_pembayaran' : 'Payment Receipt',
        'pengembalian_lebih_bayar': 'Refund',
    };

    let html = '', totNilai = 0, rowNo = 0;
    details.forEach(d => {
        rowNo++;
        totNilai += parseFloat(d.nilai || 0);
        const ref        = d.nomor_kasbon
            ? (d.nomor_jo ? `${escH(d.nomor_jo)} / ${escH(d.nomor_kasbon)}` : escH(d.nomor_kasbon))
            : (escH(d.nomor_jo) || '—');
        const coaTxt     = d.coa_display || '—';
        const depBr      = [d.departemen, d.branch].filter(Boolean).join(' / ') || '—';
        const tpLabel    = tipeLabelMap[d.tipe_pembayaran] || d.tipe_pembayaran;
        const custDisplay = d.jenis === 'general' ? (d.release_to || '—') : (d.customer || '—');
        html += `
<tr>
    <td class="text-center align-middle fw-bold" style="font-size:.8rem;color:#64748b;">${rowNo}</td>
    <td class="text-center" style="font-size:.8rem;font-weight:600;color:#374151;text-transform:capitalize;">${escH(d.jenis)}</td>
    <td class="text-center" style="font-size:.8rem;font-weight:600;color:#374151;">${tpLabel}</td>
    <td style="font-size:.82rem;">${escH(custDisplay)}</td>
    <td style="font-size:.82rem;">${ref}</td>
    <td style="font-size:.78rem;">${escH(depBr)}</td>
    <td style="vertical-align:middle;font-size:.8rem;">${coaTxt !== '—' ? `<span style="font-size:.8rem;color:#374151;">${escH(coaTxt)}</span>` : '<span class="text-muted">-</span>'}</td>
    <td class="num-cell"><div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell">${fmt(d.nilai || 0)}</span></div></td>
    <td style="font-size:.78rem;color:#6c757d;">${escH(d.keterangan || '—')}</td>
</tr>`;
    });

    tbody.html(html);
    tfoot.html(`<tr>
    <td colspan="7" class="text-end fw-bold">GRAND TOTAL</td>
    <td class="num-cell"><div class="currency-group-cell"><span class="currency-label-footer-dark">IDR</span><span class="value-footer-cell">${fmt(totNilai)}</span></div></td>
    <td></td>
</tr>`);
    $('#detailCountBadge').text(details.length + ' detail');
}
@endif
</script>
@endpush
