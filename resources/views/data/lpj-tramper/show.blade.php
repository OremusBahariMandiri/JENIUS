@extends('layouts.app')

@section('title', 'LPJ Tramper Detail')

@push('styles')
<style>
    .lpjShowPage .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,.08);
    }
    .lpjShowPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
        font-weight: 600;
    }
    .stat-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 14px 18px;
        text-align: center;
    }
    .stat-box .stat-label {
        font-size: .72rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 4px;
    }
    .stat-box .stat-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #065f46;
    }
    .jo-summary-card {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 14px 18px;
    }
    .jo-summary-card .jo-no   { font-weight: 700; color: #065f46; font-size: 1rem; }
    .jo-summary-card .jo-title { color: #374151; font-size: .9rem; margin-top: 2px; }
    .jo-summary-card .jo-meta  { font-size: .78rem; color: #6b7280; margin-top: 4px; }
    .table-lpj-show thead th {
        background: #2c3e50; color: #fff;
        border: 1px solid #2c3e50;
        padding: 11px 10px;
        font-weight: 600; font-size: .85rem;
        vertical-align: middle; text-align: center; white-space: nowrap;
    }
    .table-lpj-show tbody td {
        border: 1px solid #dee2e6;
        padding: 10px 12px;
        vertical-align: middle; font-size: .875rem; background: #fff;
    }
    .table-lpj-show tbody tr:hover td { background: #f0fdf4; }
    .table-lpj-show tbody tr.row-from-lpj td { background: #eff6ff !important; }
    .table-lpj-show tbody tr.row-from-lpj:hover td { background: #dbeafe !important; }
    .table-lpj-show tbody tr.row-tax td { background: #fffbea !important; }
    .table-lpj-show tbody tr.row-tax:hover td { background: #fef3c7 !important; }
    .category-cell {
        background: #fff !important; font-weight: 600; color: #000;
        text-align: center; vertical-align: middle !important;
        border-right: 2px solid #dee2e6;
    }
    .coa-display-badge {
        display: inline-block; font-size: .75rem; padding: 4px 8px;
        border-radius: 6px; background: #f0fdf4; border: 1px solid #bbf7d0;
        color: #065f46; font-weight: 600; word-break: break-word; white-space: normal;
    }
    .coa-empty-badge {
        display: inline-block; font-size: .72rem; padding: 3px 8px;
        border-radius: 6px; background: #f3f4f6; border: 1px dashed #d1d5db;
        color: #9ca3af; font-style: italic;
    }
    .table-lpj-show tfoot td {
        background: #fff; color: #2c3e50; font-weight: 700;
        padding: 14px 12px; font-size: .9rem; border: 1px solid #dee2e6;
    }
    .footer-currency-wrap { display: flex; align-items: center; justify-content: space-between; width: 100%; }
    .footer-currency-label {
        background: #2c3e50; color: #fff; padding: 0.2rem 0.4rem;
        border-radius: 4px; font-size: 0.72rem; font-weight: 700;
        min-width: 36px; text-align: center; flex-shrink: 0;
    }
    .footer-value { color: #2c3e50; font-weight: 700; font-size: 0.88rem; text-align: right; flex: 1; }
    .currency-group-cell { display: flex; align-items: center; gap: 5px; }
    .currency-label-cell {
        background-color: #e9ecef; color: #495057; border: 1px solid #ced4da;
        padding: 0.2rem 0.4rem; font-size: 0.72rem; border-radius: 4px;
        min-width: 36px; text-align: center; font-weight: 600; white-space: nowrap;
    }
    .value-cell { font-size: 0.88rem; font-weight: 600; color: #2c3e50; text-align: right; flex: 1; }
    .cell-right  { text-align: right; }
    .cell-center { text-align: center; }
    .no-items-row td { text-align: center; padding: 50px 20px; color: #6c757d; background: #f8f9fa !important; }
    .lpj-amount-cell { font-weight: 700; color: #059669; }
    .lpj-empty-cell  { color: #9ca3af; font-style: italic; font-size: .8rem; text-align: center; }
    .final-save-section {
        position: sticky; bottom: 0; background: #fff;
        padding: 18px 20px; box-shadow: 0 -4px 20px rgba(0,0,0,.1);
        border-radius: 12px 12px 0 0; margin-top: 30px; z-index: 100;
    }
    .btn-final-back {
        background: linear-gradient(135deg, #868686, #5e5e5e);
        border: none; color: #fff; padding: 13px 35px;
        border-radius: 10px; font-weight: 700;
        box-shadow: 0 4px 15px rgba(27,27,27,.3); transition: all .3s;
    }
    .btn-final-back:hover { transform: translateY(-2px); color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid lpjShowPage">
    <div class="row">
        <div class="col-lg-12">

            {{-- Page Header --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                    style="background-color:#d1fae5;">
                    <span class="fw-bold">
                        <i class="fas fa-file-alt me-2" style="color:#059669;"></i>LPJ Tramper Detail
                    </span>
                    <div class="d-flex gap-2">
                        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('lpj-tramper', 'edit')))
                        <a href="{{ route('lpj-tramper.edit', $lpj->id) }}" class="btn btn-sm btn-success fw-semibold">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        @endif
                        <a href="{{ route('lpj-tramper.export-pdf', $lpj->id) }}" target="_blank"
                           class="btn btn-sm btn-danger fw-semibold">
                            <i class="fas fa-file-pdf me-1"></i> Generate LPJ
                        </a>
                        <a href="{{ route('lpj-tramper.index') }}" class="btn btn-sm btn-secondary fw-semibold">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            {{-- LPJ Information --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black" style="background-color:#d1fae5;">
                    <h6 class="mb-0">
                        <i class="fas fa-file-invoice me-2" style="color:#059669;"></i>LPJ Information
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold mb-1">LPJ Number</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#10b981;border-color:#10b981;">
                                    <i class="fas fa-file-alt text-white"></i>
                                </span>
                                <input type="text" class="form-control fw-bold"
                                    value="{{ $lpj->no_lpj_tram }}" readonly
                                    style="background:#e9ecef;color:#2c3e50;letter-spacing:1px;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold mb-1">Date</label>
                            <input type="text" class="form-control" readonly style="background:#e9ecef;"
                                value="{{ $lpj->date ? \Carbon\Carbon::parse($lpj->date)->format('d/m/Y') : '-' }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-semibold mb-1">Job Order</label>
                            @if($lpj->joTramper)
                            <div class="jo-summary-card">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <div class="jo-no">{{ $lpj->joTramper->no_jo_tram }}</div>
                                        <div class="jo-title">{{ $lpj->joTramper->title ?? '-' }}</div>
                                        <div class="jo-meta">
                                            @if($lpj->joTramper->port)
                                                <i class="fas fa-anchor me-1"></i>{{ $lpj->joTramper->port->port_name ?? '-' }}
                                            @endif
                                            @if($lpj->joTramper->customer)
                                                &nbsp;·&nbsp;<i class="fas fa-building me-1"></i>{{ $lpj->joTramper->customer->nama_customer ?? '-' }}
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('jo-tramper.show', $lpj->joTramper->id_jo_tram) }}"
                                       target="_blank" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-external-link-alt me-1"></i> View JO
                                    </a>
                                </div>
                            </div>
                            @else
                            <input type="text" class="form-control" readonly style="background:#e9ecef;" value="-">
                            @endif
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-semibold mb-1">Related Cash Advances</label>
                            <div class="p-3 rounded-3 border" style="background:#f0fdf4;border-color:#bbf7d0!important;">
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @forelse($lpj->kasbons as $lpjKasbon)
                                        @if($lpjKasbon->kasbonTramper)
                                        <a href="{{ route('kasbon-tramper.show', $lpjKasbon->kasbonTramper->id) }}"
                                           class="badge text-decoration-none"
                                           style="background:#d1fae5;color:#065f46;font-size:.8rem;padding:6px 10px;">
                                            <i class="fas fa-check me-1"></i>{{ $lpjKasbon->kasbonTramper->id_kasbon_tram }}
                                        </a>
                                        @endif
                                    @empty
                                        <span class="text-muted small">No cash advances linked.</span>
                                    @endforelse
                                </div>
                                <div class="fw-bold" style="color:#065f46; font-size:.9rem;">
                                    Total CA Amount:
                                    <span class="ms-2">IDR {{ number_format($lpj->amount ?? 0, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-semibold mb-1">Remark</label>
                            <textarea class="form-control" rows="3" readonly
                                style="background:#e9ecef;">{{ $lpj->note ?? '' }}</textarea>
                        </div>

                        @if($lpj->evidence)
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-semibold mb-1">Evidence File</label>
                            <div>
                                <a href="{{ Storage::url($lpj->evidence) }}" target="_blank"
                                   class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-paperclip me-1"></i> View Evidence
                                </a>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- LPJ Items --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black" style="background-color:#d1fae5;">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2" style="color:#059669;"></i>LPJ Items
                        <span class="badge ms-2"
                            style="background:#10b981;color:#fff;font-size:.75rem;font-weight:700;padding:3px 9px;border-radius:10px;">
                            {{ count($mergedItems) }}
                        </span>
                    </h6>
                </div>
                <div class="card-body p-4">

                    @php
                        $totalHpp    = collect($mergedItems)->sum('nilai_hpp_tram_item');
                        $totalKasbon = collect($mergedItems)->sum('nilai_kasbon');
                        $totalLpj    = collect($mergedItems)->sum('amount_lpj');
                    @endphp

                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="stat-box">
                                <div class="stat-label">Total Items</div>
                                <div class="stat-value">{{ count($mergedItems) }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-box">
                                <div class="stat-label">Total HPP</div>
                                <div class="stat-value" style="font-size:.9rem;">
                                    IDR {{ number_format($totalHpp, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-box">
                                <div class="stat-label">Total CA Amount</div>
                                <div class="stat-value" style="font-size:.9rem;">
                                    IDR {{ number_format($totalKasbon, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-box" style="background:#eff6ff;border-color:#bfdbfe;">
                                <div class="stat-label" style="color:#1e40af;">Total LPJ Amount</div>
                                <div class="stat-value" style="font-size:.9rem;color:#1e40af;">
                                    IDR {{ number_format($totalLpj, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-lpj-show table-bordered mb-0"
                               style="table-layout:fixed; width:100%;">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:11%;">
                                <col style="width:12%;">
                                <col style="width:22%;">
                                <col style="width:16%;">
                                <col style="width:16%;">
                                <col style="width:16%;">
                                <col style="width:12%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Kasbon No</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-start">Item</th>
                                    <th class="text-center">HPP (IDR)</th>
                                    <th class="text-center">CA Amount (IDR)</th>
                                    <th class="text-center">LPJ Amount (IDR)</th>
                                    <th class="text-center">COA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $kSpans    = [];
                                    foreach ($mergedItems as $fi) {
                                        $kNo = $fi['id_kasbon_tram_no'] ?? '-';
                                        $kSpans[$kNo] = ($kSpans[$kNo] ?? 0) + 1;
                                    }
                                    $kRendered = [];
                                @endphp
                                @forelse($mergedItems as $idx => $fi)
                                @php
                                    $isTax    = ($fi['item_type'] ?? 'item') === 'tax';
                                    $isOrigin = !empty($fi['origin_lpj_tram']);
                                    $rowClass = $isOrigin ? 'row-from-lpj' : ($isTax ? 'row-tax' : '');
                                    $kNo      = $fi['id_kasbon_tram_no'] ?? '-';
                                    $showK    = !isset($kRendered[$kNo]);
                                    if ($showK) $kRendered[$kNo] = true;
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="cell-center">{{ $idx + 1 }}</td>
                                    @if($showK)
                                    <td class="cell-center" rowspan="{{ $kSpans[$kNo] }}"
                                        style="font-size:.8rem;vertical-align:middle;font-weight:600;border-right:2px solid #dee2e6;">
                                        {{ $kNo }}
                                    </td>
                                    @endif
                                    <td class="category-cell"
                                        @if($isTax) style="background:#fffbea !important;color:#92400e;" @endif>
                                        {{ $fi['invoice_ctg'] }}
                                    </td>
                                    <td>
                                        <span class="fw-semibold" style="color:{{ $isTax ? '#92400e' : '#2c3e50' }};">
                                            {{ $fi['invoice_typ'] }}
                                        </span>
                                        @if($isOrigin)
                                        <span class="badge ms-1" style="background:#3b82f6;font-size:.65rem;">From LPJ</span>
                                        @endif
                                    </td>
                                    <td class="cell-right">
                                        <div class="currency-group-cell">
                                            <span class="currency-label-cell">IDR</span>
                                            <span class="value-cell">{{ number_format($fi['nilai_hpp_tram_item'], 2, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="cell-right">
                                        <div class="currency-group-cell">
                                            <span class="currency-label-cell">IDR</span>
                                            <span class="value-cell">{{ number_format($fi['nilai_kasbon'], 2, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="cell-right lpj-amount-cell">
                                        @if($fi['has_lpj'])
                                            <div class="currency-group-cell">
                                                <span class="currency-label-cell">IDR</span>
                                                <span class="value-cell">{{ number_format($fi['amount_lpj'], 2, ',', '.') }}</span>
                                            </div>
                                        @else
                                            <span class="lpj-empty-cell">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fi['coa_no'])
                                        <span class="coa-display-badge">
                                            {{ $fi['coa_no'] }}<br>
                                            <span style="font-weight:400;font-size:.7rem;">{{ $fi['coa_name'] }}</span>
                                        </span>
                                        @else
                                        <span class="coa-empty-badge">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr class="no-items-row">
                                    <td colspan="8">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No items found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-center"><strong>GRAND TOTAL</strong></td>
                                    <td>
                                        <div class="footer-currency-wrap">
                                            <span class="footer-currency-label">IDR</span>
                                            <span class="footer-value">{{ number_format($totalHpp, 2, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="footer-currency-wrap">
                                            <span class="footer-currency-label">IDR</span>
                                            <span class="footer-value">{{ number_format($totalKasbon, 2, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="footer-currency-wrap">
                                            <span class="footer-currency-label" style="background:#2c3e50;">IDR</span>
                                            <span class="footer-value" style="color:#212529;font-weight:700;">{{ number_format($totalLpj, 2, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @php
                        $hasBlue = collect($mergedItems)->contains(fn($fi) => !empty($fi['origin_lpj_tram']));
                        $hasTax  = collect($mergedItems)->contains(fn($fi) => ($fi['item_type'] ?? 'item') === 'tax');
                    @endphp
                    @if($hasBlue || $hasTax)
                    <div class="mt-3 d-flex flex-wrap gap-3">
                        @if($hasBlue)
                        <div class="d-flex align-items-center gap-2" style="font-size:.8rem;color:#1e40af;">
                            <span style="display:inline-block;width:14px;height:14px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:3px;"></span>
                            Items added from LPJ page
                        </div>
                        @endif
                        @if($hasTax)
                        <div class="d-flex align-items-center gap-2" style="font-size:.8rem;color:#92400e;">
                            <span style="display:inline-block;width:14px;height:14px;background:#fffbea;border:1px solid #fde68a;border-radius:3px;"></span>
                            Tax items
                        </div>
                        @endif
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

<div class="final-save-section">
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('lpj-tramper.export-pdf', $lpj->id) }}" target="_blank"
           class="btn"
           style="padding:13px 35px;border-radius:10px;font-weight:700;font-size:1rem;border-color:red;background-color:rgb(255,237,237);color:red;">
            <i class="fas fa-file-pdf me-2"></i> Generate LPJ
        </a>
        <a href="{{ route('lpj-tramper.index') }}" class="btn btn-final-back">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>
@endsection
