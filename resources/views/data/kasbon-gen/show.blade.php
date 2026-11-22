@extends('layouts.app')

@section('title', 'Cash Advance General Detail')

@push('styles')
    <style>
        .kasbonGenShowPage .card { border:none; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,.08); }
        .kasbonGenShowPage .card-header { border-radius:10px 10px 0 0 !important; padding:1rem 1.5rem; font-weight:600; border-bottom:1px solid #e2e8f0; }
        .kasbonGenShowPage .btn { border-radius:8px; font-weight:500; transition:all .2s; }
        .kasbonGenShowPage .btn:hover { transform:translateY(-1px); box-shadow:0 4px 8px rgba(0,0,0,.1); }
        .table-kasbon-show thead th { background-color:#2c3e50; color:#fff; border:1px solid #2c3e50; padding:12px 10px; font-weight:600; font-size:.875rem; vertical-align:middle; text-align:center; white-space:nowrap; }
        .table-kasbon-show tbody td { border:1px solid #dee2e6; padding:10px 12px; vertical-align:middle; font-size:.875rem; background-color:#fff; }
        .table-kasbon-show tbody tr:hover td { background-color:#f0fdf4; }
        .table-kasbon-show .category-cell { background:#e8f5e9 !important; font-weight:600; color:#2c3e50; text-align:center; vertical-align:middle !important; border-right:2px solid #dee2e6; }
        .table-kasbon-show .item-number-cell { text-align:center; font-weight:600; color:#2c3e50; background-color:#ecf0f1 !important; }
        .table-kasbon-show tbody tr.tax-row td { background:#fffbea !important; }
        .table-kasbon-show tbody tr.tax-row:hover td { background:#fef9c3 !important; }
        .table-kasbon-show tbody tr.tax-row .item-number-cell { background:#ecf0f1 !important; }
        .table-kasbon-show .category-cell-tax { background:#fef3c7 !important; color:#92400e !important; }
        .table-kasbon-show tfoot td { background-color:#fff; color:#2c3e50; font-weight:700; padding:14px 12px; font-size:.9rem; border:1px solid #dee2e6; }
        .currency-group-cell { display:flex; align-items:center; gap:5px; }
        .currency-label-cell { background-color:#e9ecef; color:#495057; border:1px solid #ced4da; padding:.2rem .4rem; font-size:.78rem; border-radius:4px; min-width:36px; text-align:center; font-weight:600; white-space:nowrap; }
        .value-cell { font-size:.88rem; font-weight:600; color:#2c3e50; text-align:right; flex:1; }
        .footer-currency-label { background-color:#2c3e50; color:#fff; padding:.2rem .4rem; border-radius:4px; font-size:.72rem; font-weight:700; min-width:36px; text-align:center; flex-shrink:0; }
        .stat-card { padding:1rem 1.25rem; border-radius:10px; background:#f0fdf4; border:1px solid #bbf7d0; }
        .stat-card .stat-label { font-size:.72rem; color:#6b7280; font-weight:600; text-transform:uppercase; letter-spacing:.4px; }
        .stat-card .stat-value { font-size:1rem; font-weight:700; color:#2c3e50; margin-top:4px; font-family:monospace; }
        .stat-card.stat-total { background:#fffbea; border-color:#fde68a; }
        main.content { padding-bottom:0 !important; }
        .final-save-section { position:sticky; bottom:0; background:#fff; padding:18px 20px; box-shadow:0 -4px 20px rgba(0,0,0,.1); border-radius:12px 12px 0 0; margin-top:0; z-index:100; }
        .btn-final-back { background:linear-gradient(135deg,#868686,#5e5e5e); border:none; color:#fff; padding:13px 35px; border-radius:10px; font-weight:700; font-size:1rem; box-shadow:0 4px 15px rgba(27,27,27,.3); transition:all .3s; }
        .btn-final-back:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(49,49,49,.45); color:#fff; }
    </style>
@endpush

@section('content')
    <div class="container-fluid kasbonGenShowPage">
        <div class="row">
            <div class="col-lg-12">

                {{-- PAGE HEADER --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color:#d1fae5">
                        <span class="fw-bold"><i class="fas fa-eye me-2"></i>Detail Cash Advance General</span>
                        <div class="d-flex gap-2 align-items-center">
                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('kasbon-gen','ubah')))
                                <a href="{{ route('kasbon-gen.edit', $kasbonGen->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('kasbon-gen.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                {{-- CA INFORMATION --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color:#d1fae5">
                        <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Cash Advance Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Cash Advance No.</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white"><i class="fas fa-file-alt"></i></span>
                                    <input type="text" class="form-control fw-bold" value="{{ $kasbonGen->id_kasbon_gen }}" readonly
                                        style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cash Advance Date</label>
                                <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                    value="{{ $kasbonGen->tgl_kasbon ? $kasbonGen->tgl_kasbon->format('d M Y') : '-' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                    value="{{ $kasbonGen->departemen->nama_dep ?? '-' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Branch</label>
                                <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                    value="{{ $kasbonGen->cabang->nama_branch ?? '-' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                    value="{{ $kasbonGen->area->area ?? '-' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Release To</label>
                                <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                    value="{{ $kasbonGen->release->nama_release ?? $kasbonGen->id_md_release ?? '-' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Priority</label>
                                <select class="form-select" disabled style="background-color:#e9ecef;">
                                    <option value="normal" {{ ($kasbonGen->priority ?? 'normal') !== 'high' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ ($kasbonGen->priority ?? '') === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CA Release Status</label>
                                <select class="form-select" disabled style="background-color:#e9ecef;">
                                    <option value="" {{ !$kasbonGen->ca_release_status ? 'selected' : '' }}>-- No Status (Pending) --</option>
                                    <option value="pending" {{ $kasbonGen->ca_release_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="release" {{ $kasbonGen->ca_release_status === 'release' ? 'selected' : '' }}>Release</option>
                                </select>
                            </div>
                            @if($kasbonGen->ca_release_status === 'release')
                                <div class="col-md-6">
                                    <label class="form-label">CA Release Date</label>
                                    <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                        value="{{ $kasbonGen->ca_release_date ? $kasbonGen->ca_release_date->format('d M Y') : '-' }}">
                                </div>
                            @endif
                            <div class="col-md-12">
                                <label class="form-label">Note</label>
                                <textarea class="form-control" rows="3" readonly style="background-color:#e9ecef;">{{ $kasbonGen->note }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CA ITEMS --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color:#d1fae5">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>Cash Advance Items
                            <span class="badge bg-light text-dark ms-2">{{ $summary['total_items'] }} items</span>
                        </h6>
                    </div>
                    <div class="card-body p-4">

                        {{-- Stat cards --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-label">Total Items</div>
                                    <div class="stat-value" style="font-family:inherit; font-size:1.3rem;">{{ $summary['total_items'] }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-label">Total CA Amount</div>
                                    <div class="stat-value">IDR {{ number_format($summary['total_nilai_kasbon'], 2, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-label">Total Tax</div>
                                    <div class="stat-value">IDR {{ number_format($summary['total_pajak'], 2, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card stat-total">
                                    <div class="stat-label">Grand Total</div>
                                    <div class="stat-value">IDR {{ number_format($summary['grand_total'], 2, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>

                        @php
                            $groupedItems = $kasbonGen->items->groupBy(fn($i) => optional($i->invoice)->invoice_ctg ?? 'Uncategorized');
                            $itemNumber   = 0;
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-kasbon-show table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:4%;">No</th>
                                        <th style="width:14%;">Category</th>
                                        <th>Item</th>
                                        <th style="width:20%;">CA Amount (IDR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($groupedItems as $category => $items)
                                        @foreach($items as $idx => $item)
                                            @php $itemNumber++; @endphp
                                            <tr>
                                                <td class="item-number-cell">{{ $itemNumber }}</td>
                                                @if($idx === 0)
                                                    <td class="category-cell" rowspan="{{ count($items) }}">{{ $category }}</td>
                                                @endif
                                                <td><span class="fw-semibold" style="color:#2c3e50;">{{ optional($item->invoice)->invoice_typ ?? $item->id_md_invoice }}</span></td>
                                                <td>
                                                    <div class="currency-group-cell">
                                                        <span class="currency-label-cell">IDR</span>
                                                        <span class="value-cell">{{ number_format($item->nilai_kasbon, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>No items found.
                                            </td>
                                        </tr>
                                    @endforelse

                                    @foreach($kasbonGen->pajakItems as $pjk)
                                        @php $itemNumber++; @endphp
                                        <tr class="tax-row">
                                            <td class="item-number-cell">{{ $itemNumber }}</td>
                                            <td class="category-cell category-cell-tax">TAX</td>
                                            <td>
                                                <span class="fw-semibold" style="color:#2c3e50;">{{ $pjk->nama_pajak }}</span>
                                                <small class="text-muted ms-1">({{ number_format($pjk->persentase, 2) }}%)</small>
                                            </td>
                                            <td>
                                                <div class="currency-group-cell">
                                                    <span class="currency-label-cell">IDR</span>
                                                    <span class="value-cell">{{ number_format($pjk->nominal_pajak, 2, ',', '.') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end pe-3"><strong>GRAND TOTAL</strong></td>
                                        <td>
                                            <div class="currency-group-cell">
                                                <span class="footer-currency-label">IDR</span>
                                                <span class="value-cell fw-bold" style="font-size:.95rem;">{{ number_format($grandTotal, 2, ',', '.') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="final-save-section">
        <div class="d-flex justify-content-between align-items-center">
            <div></div>
            <div class="d-flex gap-2">
                <a href="{{ route('kasbon-gen.export-pdf', $kasbonGen->id) }}" target="_blank"
                    class="btn" style="padding:13px 35px; border-radius:10px; font-weight:700; font-size:1rem; border-color:red; background-color:rgb(255,237,237); color:red;">
                    <i class="fas fa-file-pdf me-2"></i> Generate CA
                </a>
                <a href="{{ route('kasbon-gen.index') }}" class="btn btn-final-back">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
@endsection
