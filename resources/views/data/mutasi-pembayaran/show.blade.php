@extends('layouts.app')

@section('title', 'Detail Mutasi Pembayaran')

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

/* IDR label cells */
.currency-group-cell  { display:flex; align-items:center; gap:5px; }
.currency-label-cell  { background-color:#e9ecef; color:#495057; border:1px solid #ced4da; padding:.2rem .5rem; font-size:.78rem; border-radius:4px; min-width:42px; text-align:center; font-weight:600; white-space:nowrap; }
.value-cell           { flex:1; text-align:right; font-weight:600; font-size:.88rem; color:#1e293b; }
.currency-label-footer-dark { background-color:#2c3e50; color:#fff; border:1px solid rgba(255,255,255,.3); padding:.2rem .5rem; font-size:.78rem; border-radius:4px; min-width:42px; text-align:center; font-weight:600; white-space:nowrap; }
.value-footer-cell    { flex:1; text-align:right; font-weight:700; font-size:.88rem; color:#1e293b; }

/* colour states */
.clr-pos { color:#16a34a !important; }
.clr-neg { color:#dc2626 !important; }
.clr-zero { color:#6b7280 !important; }

/* ── Jenis badge ── */
.jenis-badge { padding:3px 8px; border-radius:6px; font-size:.72rem; font-weight:700; white-space:nowrap; }
.jenis-tramper  { background:#d1fae5; color:#065f46; }
.jenis-other    { background:#dbeafe; color:#1e40af; }
.jenis-contract { background:#fef3c7; color:#92400e; }
.jenis-general  { background:#f3e8ff; color:#6b21a8; }
.jenis-custom   { background:#fce7f3; color:#9d174d; }

/* ── Voucher separator ── */
.voucher-sep {
    border-left: 4px solid #16a34a;
    padding: 10px 16px;
    background: linear-gradient(135deg,#f0fdf4,#dcfce7);
    border-radius: 0 8px 8px 0;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.voucher-sep .vno-badge {
    background:#16a34a; color:#fff;
    padding:2px 10px; border-radius:20px;
    font-size:.72rem; font-weight:700; margin-right:8px;
}

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
    border: none; color: #fff;
    padding: 11px 32px; border-radius: 10px;
    font-weight: 700; font-size: .95rem; transition: all .3s;
}
.btn-final-back:hover { transform: translateY(-2px); color: #fff; }
.btn-to-edit {
    background: linear-gradient(135deg,#16a34a,#15803d);
    border: none; color: #fff;
    padding: 11px 32px; border-radius: 10px;
    font-weight: 700; font-size: .95rem; transition: all .3s;
}
.btn-to-edit:hover { transform: translateY(-2px); color: #fff; }
</style>
@endpush

@section('content')

@php
    $totalDetail = $mp->vouchers->flatMap->details->count();
    $grandNilai   = $mp->vouchers->flatMap->details->sum('nilai');
    $grandPj      = $mp->vouchers->flatMap->details->sum('pj');
    $grandSelisih = $mp->vouchers->flatMap->details->sum('selisih');
@endphp

<div class="container-fluid mutasiShowPage">
    <div class="row">
        <div class="col-lg-12">

            {{-- ── Page Header ── --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                    style="background-color:#dcfce7;">
                    <span class="fw-bold">
                        <i class="fas fa-money-bill-wave me-2"></i>Payment Mutation — Detail
                    </span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('mutasi-pembayaran.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                        <a href="{{ route('mutasi-pembayaran.edit', $mp->id) }}" class="btn btn-sm text-white"
                            style="background:#16a34a;">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    </div>
                </div>
            </div>

            {{-- ── Header Info Card ── --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black" style="background-color:#dcfce7;">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Payment Mutation Info</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        {{-- Document No --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Document No.</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#16a34a;color:white;">
                                    <i class="fas fa-hashtag"></i>
                                </span>
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
                        {{-- Chart of Account --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Chart of Account</label>
                            <input type="text" class="form-control" disabled
                                value="{{ $mp->coa ? '[' . $mp->coa->no_account . '] ' . $mp->coa->account_name : '—' }}">
                        </div>
                        {{-- No. Cek --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">No. Cek / BG / Ref</label>
                            <input type="text" class="form-control" disabled
                                value="{{ $mp->no_cek ?: '—' }}">
                        </div>
                        {{-- Exchange Rate --}}
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
                            <textarea class="form-control" rows="2" disabled>{{ $mp->memo ?: '—' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Details Card ── --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                    style="background-color:#dcfce7;">
                    <h6 class="mb-0"><i class="fas fa-list-ul me-2"></i>Payment Details</h6>
                    <span class="badge" style="background:#16a34a;color:#fff;font-size:.78rem;">
                        {{ $mp->vouchers->count() }} voucher &nbsp;·&nbsp; {{ $totalDetail }} detail
                    </span>
                </div>
                <div class="card-body p-4">

                    @forelse($mp->vouchers as $voucher)
                        {{-- Voucher header separator --}}
                        <div class="voucher-sep mb-3">
                            <div>
                                <span class="vno-badge">#{{ $voucher->urutan }}</span>
                                <strong style="font-size:.9rem;">{{ $voucher->nomor_voucher ?? 'Voucher ' . $voucher->urutan }}</strong>
                                @if($voucher->keterangan)
                                    <span class="text-muted small ms-2">— {{ $voucher->keterangan }}</span>
                                @endif
                            </div>
                            <div class="text-muted small">
                                {{ $voucher->details->count() }} detail
                            </div>
                        </div>

                        <div class="table-responsive mb-4" id="tableWrap-{{ $voucher->id }}">
                            <table class="table table-detail table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>JO No.</th>
                                        <th>Cash Advance</th>
                                        <th>Dept / Branch</th>
                                        <th style="width:130px;">COA</th>
                                        <th style="width:170px;" class="num-cell">CA Amount</th>
                                        <th style="width:170px;" class="num-cell">LPJ Amount</th>
                                        <th style="width:160px;" class="num-cell">Balance</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="detailBody-{{ $voucher->id }}">
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5">
                                            <i class="fas fa-circle-notch fa-spin fa-2x d-block mb-2"></i>
                                            Loading...
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot id="detailFoot-{{ $voucher->id }}"></tfoot>
                            </table>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <p class="fw-bold">No vouchers found</p>
                        </div>
                    @endforelse



                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── Sticky bottom bar ── --}}
<div class="final-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted" style="font-size:.82rem;">
            <i class="fas fa-eye me-1"></i> Read-only view
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('mutasi-pembayaran.index') }}" class="btn btn-final-back">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('mutasi-pembayaran.edit', $mp->id) }}" class="btn btn-to-edit">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function fmt(n) {
    return Number(n || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function escH(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function idrCell(val, colorClass) {
    const cls = colorClass ? ` ${colorClass}` : '';
    return `<td class="num-cell"><div class="currency-group-cell"><span class="currency-label-cell">IDR</span><span class="value-cell${cls}">${fmt(val)}</span></div></td>`;
}
function idrFootCell(val, colorClass) {
    const cls = colorClass ? ` ${colorClass}` : '';
    return `<td class="num-cell"><div class="currency-group-cell"><span class="currency-label-footer-dark">IDR</span><span class="value-footer-cell${cls}">${fmt(val)}</span></div></td>`;
}

function loadVoucherDetails(voucherId, url) {
    $.get(url, function(r) {
        if (r.success) renderDetails(voucherId, r.data || []);
        else showErr(voucherId);
    }).fail(function() { showErr(voucherId); });
}

function showErr(vid) {
    $(`#detailBody-${vid}`).html(
        `<tr><td colspan="9" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle me-1"></i> Failed to load details</td></tr>`
    );
}

function renderDetails(vid, details) {
    const $body = $(`#detailBody-${vid}`);
    const $foot = $(`#detailFoot-${vid}`);

    if (!details.length) {
        $body.html(`<tr><td colspan="9" class="text-center text-muted py-5"><i class="fas fa-inbox fa-2x d-block mb-2"></i>No details</td></tr>`);
        $foot.html('');
        return;
    }

    let html = '', totN = 0, totP = 0, totS = 0, rowNo = 0;

    details.forEach(d => {
        rowNo++;
        const nilai   = parseFloat(d.nilai   || 0);
        const pj      = parseFloat(d.pj      || 0);
        const selisih = parseFloat(d.selisih  || 0);
        totN += nilai; totP += pj; totS += selisih;

        const sCls = selisih > 0.001 ? 'clr-pos' : selisih < -0.001 ? 'clr-neg' : 'clr-zero';

        const jenisBadge = `<span class="jenis-badge jenis-${d.jenis}">${escH(d.jenis)}</span>`;
        const joTxt = d.nomor_jo || '—';

        // Kasbon / CA display
        let caTxt = d.nomor_kasbon || '—';
        if (d.jenis === 'custom') {
            const relNama = d.custom_release_nama || '—';
            const depNama = d.custom_dep_nama     || '—';
            const braNama = d.custom_branch_nama  || '—';
            caTxt = `<span class="text-muted" style="font-size:.8rem;">${escH(relNama)}</span><br><small class="text-muted">${escH(depNama)} / ${escH(braNama)}</small>`;
        }

        // Dept / Branch
        const dep = d.departemen || '—';
        const cab = d.cabang     || '—';
        const depBr = `<span style="font-size:.78rem;">${escH(dep)}</span><br><small class="text-muted">${escH(cab)}</small>`;

        // COA
        let coaTxt = '<span class="text-muted">—</span>';
        if (d.coa_detail) {
            coaTxt = `<span class="badge bg-light text-dark border" style="font-size:.72rem;">${escH(d.coa_detail.no)}</span><br><small class="text-muted">${escH(d.coa_detail.nama)}</small>`;
        }

        html += `<tr>
<td class="text-center align-middle fw-bold" style="font-size:.8rem;color:#64748b;">${rowNo}</td>
<td style="font-size:.82rem;vertical-align:middle;">${jenisBadge}<br><span style="font-size:.82rem;">${escH(joTxt)}</span></td>
<td style="font-size:.82rem;vertical-align:middle;">${caTxt}</td>
<td style="vertical-align:middle;">${depBr}</td>
<td style="vertical-align:middle;">${coaTxt}</td>
${idrCell(nilai)}
${idrCell(pj)}
${idrCell(selisih, sCls)}
<td style="font-size:.78rem;color:#6c757d;vertical-align:middle;">${escH(d.keterangan || '—')}</td>
</tr>`;
    });

    $body.html(html);

    const tSC = totS > 0.001 ? 'clr-pos' : totS < -0.001 ? 'clr-neg' : 'clr-zero';
    $foot.html(`<tr>
<td colspan="5" class="text-end fw-bold">SUBTOTAL</td>
${idrFootCell(totN)}
${idrFootCell(totP)}
${idrFootCell(totS, tSC)}
<td></td>
</tr>`);
}

$(document).ready(function() {
    @foreach($mp->vouchers as $voucher)
    loadVoucherDetails({{ $voucher->id }}, '/data/mutasi-pembayaran/voucher/{{ $voucher->id }}/details');
    @endforeach
});
</script>
@endpush
