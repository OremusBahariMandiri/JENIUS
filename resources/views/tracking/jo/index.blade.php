@extends('layouts.app')

@section('title', 'JO Tracking')

@push('styles')
<style>
    /* ── JO Type buttons ─────────────────────────────── */
    .jo-type-btn {
        border: 2px solid var(--border-color);
        background: white;
        color: var(--text-gray);
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .jo-type-btn:hover  { border-color: var(--primary-green); color: var(--primary-green); background: var(--hover-green); }
    .jo-type-btn.active { border-color: var(--primary-green); background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); color: white; box-shadow: 0 4px 12px rgba(16,185,129,.3); }

    /* ── Stat cards ──────────────────────────────────── */
    .stat-card { border-radius:12px; padding:18px 20px; border:1px solid var(--border-color); background:white; display:flex; align-items:center; gap:14px; }
    .stat-card .stat-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
    .stat-card .stat-value { font-size:18px; font-weight:700; color:var(--text-dark); }
    .stat-card .stat-label { font-size:11px; color:var(--text-gray); font-weight:500; text-transform:uppercase; letter-spacing:.5px; }

    /* ── Status badges ───────────────────────────────── */
    .badge-exact  { background:#d1fae5; color:#065f46; }
    .badge-short  { background:#fef3c7; color:#92400e; }
    .badge-over   { background:#fee2e2; color:#991b1b; }
    .badge-none   { background:#e0e7ff; color:#3730a3; }
    .badge-no_hpp { background:#f1f5f9; color:#64748b; }

    /* ── Progress bar ────────────────────────────────── */
    .hpp-progress      { height:8px; border-radius:4px; background:#e2e8f0; overflow:hidden; margin-top:4px; }
    .hpp-progress-bar  { height:100%; border-radius:4px; transition:width .5s ease; }
    .hpp-progress-bar.ok   { background:var(--primary-green); }
    .hpp-progress-bar.warn { background:#f59e0b; }
    .hpp-progress-bar.over { background:#ef4444; }
    .hpp-progress-bar.none { background:#94a3b8; }

    /* ── Table ───────────────────────────────────────── */
    .table-header-green th {
        background:#d1fae5; color:#065f46; font-weight:600;
        font-size:12px; letter-spacing:.3px; border-bottom:2px solid #a7f3d0;
    }

    /* category cell — merged */
    .category-cell {
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #065f46;
        background: #f0fdf4 !important;
        border-right: 3px solid #a7f3d0 !important;
        vertical-align: middle !important;
        text-align: center;
        min-width: 100px;
    }
    .category-cell.tax-ctg {
        color: #92400e;
        background: #fffbea !important;
        border-right-color: #fcd34d !important;
    }

    /* clickable row highlight */
    tr.item-row.has-kasbon:hover td {
        background: rgba(16,185,129,.05) !important;
    }
    tr.item-row.has-kasbon { cursor: pointer; }

    /* Empty state */
    .empty-state { text-align:center; padding:60px 20px; color:var(--text-gray); }
    .empty-state i { font-size:48px; margin-bottom:16px; color:#cbd5e1; }
    .empty-state h5 { font-weight:600; color:var(--text-dark); margin-bottom:8px; }

    /* ── Kasbon Modal ────────────────────────────────── */
    #kasbonModal .modal-header { background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); color: white; }
    #kasbonModal .modal-header .btn-close { filter: invert(1); }
    #kasbonModal .modal-body  { padding: 0; }
    #kasbonModal .kb-info-bar { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px; font-size: 13px; }
    #kasbonModal .kb-info-bar .label { color: var(--text-gray); font-size: 11px; text-transform: uppercase; letter-spacing: .4px; }
    #kasbonModal .kb-info-bar .value { font-weight: 700; color: var(--text-dark); }
    #kasbonModal table thead th { background: #f1f5f9; font-size: 12px; font-weight: 600; }
    #kasbonModal .kasbon-row-num {
        width:22px; height:22px; border-radius:50%;
        background:var(--primary-green); color:white;
        font-size:10px; font-weight:700;
        display:inline-flex; align-items:center; justify-content:center;
        margin-right:6px; flex-shrink:0;
    }
    #kasbonModal .modal-footer-summary { background: #f8fafc; border-top: 2px solid #e2e8f0; padding: 14px 20px; }
    #kasbonModal .modal-footer-summary .fs-row { display: flex; justify-content: space-between; font-size: 13px; padding: 3px 0; }
    #kasbonModal .modal-footer-summary .fs-row.total { font-weight: 700; border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 4px; }

    /* ── Dark mode ───────────────────────────────────── */
    [data-theme="dark"] .jo-type-btn        { background:#1e293b; border-color:#334155; color:#94a3b8; }
    [data-theme="dark"] .jo-type-btn:hover  { border-color:var(--primary-green); color:var(--primary-green); background:#022c22; }
    [data-theme="dark"] .stat-card          { background:#1e293b; border-color:#334155; }
    [data-theme="dark"] .stat-card .stat-value { color:#f1f5f9; }
    [data-theme="dark"] .table-header-green th { background:#064e3b; color:#d1fae5; border-bottom-color:#065f46; }
    [data-theme="dark"] .category-cell      { background:#052e16 !important; color:#6ee7b7; border-right-color:#065f46 !important; }
    [data-theme="dark"] .category-cell.tax-ctg { background:#451a03 !important; color:#fcd34d; }
    [data-theme="dark"] .hpp-progress       { background:#334155; }
    [data-theme="dark"] #kasbonModal .kb-info-bar   { background:#1e293b; border-bottom-color:#334155; }
    [data-theme="dark"] #kasbonModal .kb-info-bar .value { color:#f1f5f9; }
    [data-theme="dark"] #kasbonModal table thead th { background:#1e293b; }
    [data-theme="dark"] #kasbonModal .modal-footer-summary { background:#1e293b; border-top-color:#334155; }
    [data-theme="dark"] #kasbonModal .modal-footer-summary .fs-row { color:#94a3b8; }
    [data-theme="dark"] #kasbonModal .modal-content { background:#0f172a; }
    [data-theme="dark"] .badge-exact  { background:#064e3b; color:#6ee7b7; }
    [data-theme="dark"] .badge-short  { background:#451a03; color:#fcd34d; }
    [data-theme="dark"] .badge-over   { background:#450a0a; color:#fca5a5; }
    [data-theme="dark"] .badge-none   { background:#1e1b4b; color:#a5b4fc; }
    [data-theme="dark"] .badge-no_hpp { background:#1e293b; color:#64748b; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color:var(--text-dark)">
            <i class="fas fa-map-marker-alt me-2 text-success"></i>JO Tracking
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Tracking</li>
                <li class="breadcrumb-item active">JO Tracking</li>
            </ol>
        </nav>
    </div>
</div>

{{-- ============================================================
     FILTER
     ============================================================ --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2" style="background:#d1fae5; color:#065f46;">
        <i class="fas fa-filter"></i><span>Filter &amp; Select JO</span>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('tracking-jo.index') }}" id="trackingForm">

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:13px;color:var(--text-gray);text-transform:uppercase;letter-spacing:.5px">JO Type</label>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach(['contract' => ['icon'=>'fas fa-file-contract','label'=>'JO Contract'],
                               'tramper'  => ['icon'=>'fas fa-truck-moving',  'label'=>'JO Tramper'],
                               'other'    => ['icon'=>'fas fa-ellipsis-h',    'label'=>'JO Other']] as $type => $cfg)
                        <button type="button"
                                class="jo-type-btn {{ $joType === $type ? 'active' : '' }}"
                                onclick="setJoType('{{ $type }}')">
                            <i class="{{ $cfg['icon'] }}"></i>{{ $cfg['label'] }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="jo_type" id="joTypeInput" value="{{ $joType }}">
            </div>

            <div class="row g-3 align-items-end">
                <div class="col-md-7">
                    <label class="form-label fw-semibold" style="font-size:13px;color:var(--text-gray);text-transform:uppercase;letter-spacing:.5px">JO Number</label>
                    <select name="jo_id" id="joSelect" class="form-select" style="font-size:14px;">
                        <option value="">-- Select JO --</option>
                        @foreach($joList as $jo)
                            @php
                                if ($joType === 'contract') {
                                    $id    = $jo->id_jo_cont;
                                    $noJo  = $jo->no_jo_cont;
                                    $tglJo = $jo->tgl_jo_cont;
                                } elseif ($joType === 'tramper') {
                                    $id    = $jo->id_jo_tram;
                                    $noJo  = $jo->no_jo_tram;
                                    $tglJo = $jo->tgl_jo_tram;
                                } else {
                                    $id    = $jo->id_jo_other;
                                    $noJo  = $jo->no_jo_other;
                                    $tglJo = $jo->tgl_jo_other;
                                }
                                $tgl = $tglJo ? \Carbon\Carbon::parse($tglJo)->format('d/m/Y') : '-';
                            @endphp
                            <option value="{{ $id }}" {{ $joId == $id ? 'selected' : '' }}>
                                {{ $noJo }} &mdash; {{ $tgl }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-search me-2"></i>Track
                    </button>
                    @if($joId)
                        <a href="{{ route('tracking-jo.index', ['jo_type' => $joType]) }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================
     TRACKING RESULTS
     ============================================================ --}}
@if($joId && $joData)

    {{-- JO Info bar --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <div style="background:#d1fae5;color:#065f46;width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;">
                        @if($joType==='contract')<i class="fas fa-file-contract"></i>
                        @elseif($joType==='tramper')<i class="fas fa-truck-moving"></i>
                        @else<i class="fas fa-ellipsis-h"></i>@endif
                    </div>
                </div>
                <div class="col">
                    @php
                        if ($joType === 'contract') {
                            $noJoDisplay  = $joData->no_jo_cont ?? '-';
                            $tglJoDisplay = $joData->tgl_jo_cont;
                            $partnerLabel = 'Contract';
                            $partnerName  = optional($joData->contract)->nama_cont ?? '-';
                        } elseif ($joType === 'tramper') {
                            $noJoDisplay  = $joData->no_jo_tram ?? '-';
                            $tglJoDisplay = $joData->tgl_jo_tram;
                            $partnerLabel = 'Customer';
                            $partnerName  = optional($joData->customer)->nama_customer ?? '-';
                        } else {
                            $noJoDisplay  = $joData->no_jo_other ?? '-';
                            $tglJoDisplay = $joData->tgl_jo_other;
                            $partnerLabel = 'Customer';
                            $partnerName  = optional($joData->customer)->nama_customer ?? '-';
                        }
                    @endphp
                    <div class="fw-bold" style="font-size:16px;color:var(--text-dark)">{{ $noJoDisplay }}</div>
                    <div style="font-size:12px;color:var(--text-gray)">
                        Date: {{ $tglJoDisplay ? \Carbon\Carbon::parse($tglJoDisplay)->format('d M Y') : '-' }}
                        &nbsp;|&nbsp; {{ $partnerLabel }}: <span class="fw-semibold">{{ $partnerName }}</span>
                        @if($joData->title ?? false)&nbsp;|&nbsp; {{ $joData->title }}@endif
                    </div>
                </div>
                <div class="col-auto">
                    @php
                        $joRoute = match($joType) {
                            'contract' => route('jo-contract.show', $joId),
                            'tramper'  => route('jo-tramper.show', $joId),
                            default    => route('jo-other.show', $joId),
                        };
                    @endphp
                    <a href="{{ $joRoute }}" class="btn btn-sm btn-outline-success" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>Open JO
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Stats --}}
    @php
        $totalHpp        = $allRows->sum('hpp');
        $totalSellingAll = $allRows->sum('selling');
        $totalKasbonAll  = $allRows->where('is_tax', false)->sum('total_kasbon');
        $totalLpjAll     = $allRows->where('is_tax', false)->sum('total_lpj');
        $countExact      = $allRows->where('status','pas')->count();
        $countShort      = $allRows->where('status','kurang')->count();
        $countOver       = $allRows->where('status','lebih')->count();
        $countPending    = $allRows->whereIn('status',['belum','no_hpp'])->count();
        $totalItems      = $allRows->where('is_tax', false)->count();
    @endphp
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;"><i class="fas fa-list-ul"></i></div>
                <div><div class="stat-value">{{ $totalItems }}</div><div class="stat-label">Total Items</div></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-tag"></i></div>
                <div>
                    <div class="stat-value" style="font-size:14px;">{{ number_format($totalSellingAll,0,',','.') }}</div>
                    <div class="stat-label">Total Selling</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:#d1fae5;color:#065f46;"><i class="fas fa-coins"></i></div>
                <div>
                    <div class="stat-value" style="font-size:14px;">{{ number_format($totalHpp,0,',','.') }}</div>
                    <div class="stat-label">Total HPP</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fef3c7;color:#92400e;"><i class="fas fa-cash-register"></i></div>
                <div>
                    <div class="stat-value" style="font-size:14px;">{{ number_format($totalKasbonAll,0,',','.') }}</div>
                    <div class="stat-label">Total Advance</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status pills --}}
    <div class="d-flex gap-2 flex-wrap mb-3">
        <span class="badge badge-exact px-3 py-2" style="font-size:12px;border-radius:20px;">
            <i class="fas fa-check-circle me-1"></i>Exact: {{ $countExact }}
        </span>
        <span class="badge badge-short px-3 py-2" style="font-size:12px;border-radius:20px;">
            <i class="fas fa-exclamation-triangle me-1"></i>Short: {{ $countShort }}
        </span>
        <span class="badge badge-over px-3 py-2" style="font-size:12px;border-radius:20px;">
            <i class="fas fa-arrow-up me-1"></i>Over: {{ $countOver }}
        </span>
        <span class="badge badge-none px-3 py-2" style="font-size:12px;border-radius:20px;">
            <i class="fas fa-clock me-1"></i>No Advance: {{ $countPending }}
        </span>
    </div>

    {{-- Tracking Table --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between" style="background:#d1fae5;color:#065f46;">
            <span><i class="fas fa-table me-2"></i>Item Tracking Detail</span>
            <small style="font-size:11px;color:#059669;">
                <i class="fas fa-receipt me-1"></i>Click the advance badge to view advance breakdown
            </small>
        </div>
        <div class="card-body p-0">
            @if($groups->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-inbox d-block"></i>
                    <h5>No items found</h5>
                    <p>This JO has no items, or all items have been removed.</p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="trackingTable">
                    <thead class="table-header-green">
                        <tr>
                            <th style="width:36px">#</th>
                            <th style="width:110px">Category</th>
                            <th>Item Name</th>
                            <th class="text-end">Selling (IDR)</th>
                            <th class="text-end">HPP (IDR)</th>
                            <th class="text-end">Total Advance</th>
                            <th class="text-end">Total LPJ</th>
                            <th class="text-end">Difference</th>
                            <th style="width:120px">Progress</th>
                            <th class="text-center" style="width:105px">Status</th>
                            <th class="text-center" style="width:80px">Advance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $globalIdx = 1; @endphp
                        @foreach($groups as $group)
                            @foreach($group['rows'] as $rowIdx => $row)
                            @php
                                $persen    = min($row['persen_kasbon'], 100);
                                $barClass  = match($row['status']) {
                                    'pas'    => 'ok',
                                    'kurang' => 'warn',
                                    'lebih'  => 'over',
                                    default  => 'none',
                                };
                                $statusCfg = match($row['status']) {
                                    'pas'    => ['Exact',      'badge-exact'],
                                    'kurang' => ['Short',      'badge-short'],
                                    'lebih'  => ['Over',       'badge-over'],
                                    'belum'  => ['No Advance', 'badge-none'],
                                    default  => ['No HPP',     'badge-no_hpp'],
                                };
                                $isTax      = $row['is_tax'];
                                $hasKasbon  = !$isTax && $row['jumlah_kasbon'] > 0;
                            @endphp
                            <tr class="item-row {{ $hasKasbon ? 'has-kasbon' : '' }}">

                                <td class="text-center align-middle" style="font-size:12px;color:var(--text-gray);">{{ $globalIdx }}</td>

                                {{-- Category: merged rowspan, only first row of group --}}
                                @if($rowIdx === 0)
                                <td class="category-cell {{ $isTax ? 'tax-ctg' : '' }}"
                                    rowspan="{{ $group['count'] }}">
                                    {{ $group['category'] }}
                                </td>
                                @endif

                                <td class="align-middle">
                                    <div class="fw-semibold" style="font-size:13px;">{{ $row['nama_item'] }}</div>
                                    @if(!$isTax)<div style="font-size:11px;color:var(--text-gray)">ID: {{ $row['id_item'] }}</div>@endif
                                </td>

                                {{-- Selling price --}}
                                <td class="text-end align-middle">
                                    @if($row['selling'] > 0)
                                        <span style="font-size:13px;font-weight:600;color:#1d4ed8;">{{ number_format($row['selling'],0,',','.') }}</span>
                                    @else
                                        <span style="font-size:12px;color:#cbd5e1;">—</span>
                                    @endif
                                </td>

                                <td class="text-end align-middle">
                                    <span style="font-size:13px;font-weight:600;">{{ number_format($row['hpp'],0,',','.') }}</span>
                                </td>

                                <td class="text-end align-middle">
                                    @if($isTax)
                                        <span style="font-size:12px;color:#cbd5e1;">—</span>
                                    @else
                                        <span style="font-size:13px;color:var(--primary-green);font-weight:600;">
                                            {{ number_format($row['total_kasbon'],0,',','.') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end align-middle">
                                    @if($isTax)
                                        <span style="font-size:12px;color:#cbd5e1;">—</span>
                                    @else
                                        <span style="font-size:13px;">{{ number_format($row['total_lpj'],0,',','.') }}</span>
                                    @endif
                                </td>

                                <td class="text-end align-middle">
                                    @if($isTax)
                                        <span style="font-size:12px;color:#cbd5e1;">—</span>
                                    @else
                                        @php $sel = $row['selisih']; @endphp
                                        <span style="font-size:13px;font-weight:600;color:{{ $sel < 0 ? '#f59e0b' : ($sel > 0 ? '#ef4444' : '#10b981') }}">
                                            {{ $sel >= 0 ? '+' : '' }}{{ number_format($sel,0,',','.') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="align-middle" style="min-width:110px;">
                                    @if(!$isTax)
                                        <div style="font-size:11px;color:var(--text-gray);text-align:right;margin-bottom:3px;">{{ $row['persen_kasbon'] }}%</div>
                                        <div class="hpp-progress">
                                            <div class="hpp-progress-bar {{ $barClass }}" style="width:{{ $persen }}%"></div>
                                        </div>
                                    @else
                                        <span style="font-size:12px;color:#cbd5e1;">—</span>
                                    @endif
                                </td>

                                <td class="text-center align-middle">
                                    <span class="badge {{ $statusCfg[1] }} px-2 py-1" style="font-size:11px;border-radius:6px;">
                                        {{ $statusCfg[0] }}
                                    </span>
                                </td>

                                <td class="text-center align-middle">
                                    @if($hasKasbon)
                                        <button type="button"
                                            class="badge border-0"
                                            style="background:#e0e7ff;color:#3730a3;border-radius:20px;font-size:11px;padding:5px 11px;cursor:pointer;"
                                            onclick="openKasbonModal({{ json_encode($row) }})"
                                            title="View advance breakdown">
                                            <i class="fas fa-receipt me-1"></i>{{ $row['jumlah_kasbon'] }}x
                                        </button>
                                    @else
                                        <span style="font-size:12px;color:#cbd5e1;">—</span>
                                    @endif
                                </td>
                            </tr>

                            @php $globalIdx++; @endphp
                            @endforeach {{-- rows --}}
                        @endforeach {{-- groups --}}
                    </tbody>

                    <tfoot>
                        <tr style="background:#f1f5f9;font-weight:700;">
                            <td colspan="3" class="text-end" style="font-size:13px;">TOTAL</td>
                            <td class="text-end" style="font-size:13px;color:#1d4ed8;">Rp {{ number_format($totalSellingAll,0,',','.') }}</td>
                            <td class="text-end" style="font-size:13px;">Rp {{ number_format($totalHpp,0,',','.') }}</td>
                            <td class="text-end" style="font-size:13px;color:var(--primary-green)">Rp {{ number_format($totalKasbonAll,0,',','.') }}</td>
                            <td class="text-end" style="font-size:13px;">Rp {{ number_format($totalLpjAll,0,',','.') }}</td>
                            @php $selTotal = $totalKasbonAll - $totalHpp; @endphp
                            <td class="text-end" style="font-size:13px;color:{{ $selTotal < 0 ? '#f59e0b' : ($selTotal > 0 ? '#ef4444' : '#10b981') }}">
                                {{ $selTotal >= 0 ? '+' : '' }}Rp {{ number_format($selTotal,0,',','.') }}
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>

@elseif($joId && !$joData)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i> JO not found. Please select a valid JO.
    </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="fas fa-search d-block"></i>
                <h5>Select a JO to Start Tracking</h5>
                <p>Use the filter above to choose a JO type and JO number.</p>
                <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
                    <span class="badge badge-exact px-3 py-2" style="font-size:12px;border-radius:20px;">&#9679; Exact = Advance matches HPP</span>
                    <span class="badge badge-short px-3 py-2" style="font-size:12px;border-radius:20px;">&#9679; Short = Advance below HPP</span>
                    <span class="badge badge-over px-3 py-2" style="font-size:12px;border-radius:20px;">&#9679; Over = Advance exceeds HPP</span>
                    <span class="badge badge-none px-3 py-2" style="font-size:12px;border-radius:20px;">&#9679; No Advance = Not yet advanced</span>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- ============================================================
     ADVANCE BREAKDOWN MODAL
     ============================================================ --}}
<div class="modal fade" id="kasbonModal" tabindex="-1" aria-labelledby="kasbonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kasbonModalLabel">
                    <i class="fas fa-receipt me-2"></i>Advance Breakdown
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Item info bar --}}
                <div class="kb-info-bar">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="label">Item</div>
                            <div class="value" id="kbItemName">—</div>
                        </div>
                        <div class="col-md-4">
                            <div class="label">Category</div>
                            <div class="value" id="kbCategory">—</div>
                        </div>
                        <div class="col-md-4">
                            <div class="label">Status</div>
                            <div id="kbStatus">—</div>
                        </div>
                    </div>
                </div>

                {{-- Kasbon list table --}}
                <div class="p-3">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width:36px">#</th>
                                <th>Advance No.</th>
                                <th>Date</th>
                                <th class="text-end">Amount (this item)</th>
                            </tr>
                        </thead>
                        <tbody id="kbTableBody">
                        </tbody>
                    </table>
                </div>

                {{-- Summary footer --}}
                <div class="modal-footer-summary">
                    <div class="fs-row">
                        <span style="color:var(--text-gray)">Selling Price</span>
                        <span class="fw-semibold" style="color:#1d4ed8;" id="kbSelling">Rp 0</span>
                    </div>
                    <div class="fs-row">
                        <span style="color:var(--text-gray)">HPP</span>
                        <span class="fw-semibold" id="kbHpp">Rp 0</span>
                    </div>
                    <div class="fs-row total">
                        <span>Total Advance</span>
                        <span style="color:var(--primary-green);" id="kbTotalKasbon">Rp 0</span>
                    </div>
                    <div class="fs-row">
                        <span style="color:var(--text-gray)">Total LPJ</span>
                        <span id="kbTotalLpj">Rp 0</span>
                    </div>
                    <div class="fs-row total">
                        <span>Difference (Advance − HPP)</span>
                        <span id="kbSelisih">Rp 0</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e2e8f0;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function setJoType(type) {
        document.getElementById('joTypeInput').value = type;
        document.querySelectorAll('.jo-type-btn').forEach(btn => {
            const label = btn.textContent.trim().toLowerCase();
            btn.classList.toggle('active', label.includes(type));
        });
        document.getElementById('joSelect').value = '';
        document.getElementById('trackingForm').submit();
    }

    function fmt(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function statusBadge(status) {
        const map = {
            'pas':    ['Exact',      'badge-exact'],
            'kurang': ['Short',      'badge-short'],
            'lebih':  ['Over',       'badge-over'],
            'belum':  ['No Advance', 'badge-none'],
            'no_hpp': ['No HPP',     'badge-no_hpp'],
        };
        const cfg = map[status] || ['—', 'badge-no_hpp'];
        return `<span class="badge ${cfg[1]} px-2 py-1" style="font-size:12px;border-radius:6px;">${cfg[0]}</span>`;
    }

    function openKasbonModal(row) {
        // Populate info bar
        document.getElementById('kbItemName').textContent  = row.nama_item  || '—';
        document.getElementById('kbCategory').textContent  = row.invoice_ctg || '—';
        document.getElementById('kbStatus').innerHTML      = statusBadge(row.status);
        document.getElementById('kbSelling').textContent   = fmt(row.selling      || 0);
        document.getElementById('kbHpp').textContent       = fmt(row.hpp          || 0);
        document.getElementById('kbTotalKasbon').textContent = fmt(row.total_kasbon || 0);
        document.getElementById('kbTotalLpj').textContent  = fmt(row.total_lpj    || 0);

        const diff    = (row.total_kasbon || 0) - (row.hpp || 0);
        const diffEl  = document.getElementById('kbSelisih');
        diffEl.textContent = (diff >= 0 ? '+' : '') + fmt(diff);
        diffEl.style.color = diff < 0 ? '#f59e0b' : (diff > 0 ? '#ef4444' : '#10b981');

        // Build kasbon rows
        const tbody = document.getElementById('kbTableBody');
        tbody.innerHTML = '';
        const details = row.kasbon_detail || [];

        if (details.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">No advance records found.</td></tr>`;
        } else {
            details.forEach((kb, i) => {
                const tgl = kb.tgl_kasbon
                    ? new Date(kb.tgl_kasbon).toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'})
                    : '—';
                tbody.innerHTML += `
                    <tr>
                        <td><span class="kasbon-row-num">${i + 1}</span></td>
                        <td class="fw-semibold">${kb.nomor_kasbon || '—'}</td>
                        <td>${tgl}</td>
                        <td class="text-end fw-semibold" style="color:var(--primary-green)">${fmt(kb.total_kasbon || 0)}</td>
                    </tr>`;
            });
        }

        const modal = new bootstrap.Modal(document.getElementById('kasbonModal'));
        modal.show();
    }
</script>
@endpush
