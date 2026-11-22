@extends('layouts.app')

@section('title', 'Cash Advance Contract Detail')

@push('styles')
    <style>
        .kasbonContractShowPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .kasbonContractShowPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        .kasbonContractShowPage .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .kasbonContractShowPage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* ===== TABLE ITEMS ===== */
        .table-kasbon-show thead th {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #2c3e50;
            padding: 12px 10px;
            font-weight: 600;
            font-size: 0.875rem;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
        }

        .table-kasbon-show tbody td {
            border: 1px solid #dee2e6;
            padding: 10px 12px;
            vertical-align: middle;
            font-size: 0.875rem;
            background-color: #fff;
        }

        .table-kasbon-show tbody tr:hover td {
            background-color: #f0fdf4;
        }

        .table-kasbon-show .category-cell {
            background: #e8f5e9 !important;
            font-weight: 600;
            color: #2c3e50;
            text-align: center;
            vertical-align: middle !important;
            border-right: 2px solid #dee2e6;
        }

        .table-kasbon-show .item-number-cell {
            text-align: center;
            font-weight: 600;
            color: #2c3e50;
            background-color: #ecf0f1 !important;
        }

        .table-kasbon-show tbody tr.row-from-lpj td {
            background: #eff6ff !important;
        }

        .table-kasbon-show tbody tr.row-from-lpj:hover td {
            background: #dbeafe !important;
        }

        .table-kasbon-show tbody tr.row-from-lpj .category-cell {
            background: #dbeafe !important;
        }

        .table-kasbon-show tbody tr.tax-row td {
            background: #ffffff !important;
        }

        .table-kasbon-show tbody tr.tax-row .category-cell {
            background: #fef3c7 !important;
            color: #92400e !important;
        }

        .table-kasbon-show tbody tr.tax-row .item-number-cell {
            background: #ecf0f1 !important;
        }

        .table-kasbon-show tfoot td {
            background-color: #ffffff;
            color: #2C3E50;
            font-weight: 700;
            padding: 14px 12px;
            font-size: 0.9rem;
            border: 1px solid #dee2e6;
        }

        .footer-currency-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .footer-currency-label {
            background-color: #2C3E50;
            color: white;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-size: 0.72rem;
            font-weight: 700;
            min-width: 36px;
            text-align: center;
            flex-shrink: 0;
        }

        .footer-value {
            color: #2C3E50;
            font-weight: 700;
            font-size: .95rem;
            text-align: right;
            flex: 1;
        }

        /* ===== JO SUMMARY CARD ===== */
        .jo-summary-card {
            background: #fff;
            border: 1.5px solid #10b981;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
        }

        .jo-summary-card .jo-meta-label {
            font-size: .72rem;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .jo-summary-card .jo-meta-value {
            font-size: .875rem;
            font-weight: 700;
            color: #2c3e50;
            margin-top: 2px;
        }


        .lpj-origin-note {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            padding: 8px 14px;
            background: #eff6ff;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #374151;
        }

        .cell-right { text-align: right; }
        .cell-left { text-align: left; }
        .currency-group-cell { display: flex; align-items: center; gap: 5px; }
        .currency-label-cell { background-color: #e9ecef; color: #495057; border: 1px solid #ced4da; padding: 0.2rem 0.4rem; font-size: 0.72rem; border-radius: 4px; min-width: 36px; text-align: center; font-weight: 600; white-space: nowrap; }
        .value-cell { font-size: 0.88rem; font-weight: 600; color: #2c3e50; text-align: right; flex: 1; }
        .cell-center { text-align: center; }

        /* Remove layout bottom padding so sticky bar is truly last */
        main.content {
            padding-bottom: 0 !important;
        }

        .final-save-section {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 18px 20px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px 12px 0 0;
            margin-top: 30px;
            z-index: 100;
        }

        .btn-final-back {
            background: linear-gradient(135deg, #868686, #5e5e5e);
            border: none;
            color: white;
            padding: 13px 35px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(27, 27, 27, 0.3);
            transition: all 0.3s;
        }

        .btn-final-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(49, 49, 49, 0.45);
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid kasbonContractShowPage">
        <div class="row">
            <div class="col-lg-12">

                {{-- ===== PAGE HEADER ===== --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold">
                            <i class="fas fa-eye me-2"></i>Detail Cash Advance Contract
                        </span>
                        <div class="d-flex gap-2 align-items-center">
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('kasbon-contract', 'ubah')))
                                <a href="{{ route('kasbon-contract.edit', $kasbonContract->id) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('kasbon-contract.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ===== CA INFORMATION ===== --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color: #d1fae5">
                        <h6 class="mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>Cash Advance Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Cash Advance No.</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="fas fa-file-alt"></i>
                                    </span>
                                    <input type="text" class="form-control fw-bold"
                                        value="{{ $kasbonContract->id_kasbon_cont }}" readonly
                                        style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cash Advance Date</label>
                                <input type="text" class="form-control" readonly
                                    style="background-color:#e9ecef;"
                                    value="{{ $kasbonContract->tgl_kasbon ? $kasbonContract->tgl_kasbon->format('d M Y') : '-' }}">
                            </div>

                            {{-- JO Contract Summary --}}
                            <div class="col-md-12">
                                <label class="form-label">JO Contract</label>
                                @if($kasbonContract->joContract)
                                    <div class="jo-summary-card mt-1">
                                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                            <div class="d-flex align-items-center gap-3" style="flex:1; min-width:0;">
                                                <div style="width:48px; height:48px; border-radius:10px; background:#d1fae5;
                                                    display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                    <i class="fas fa-file-contract" style="color:#059669; font-size:1.2rem;"></i>
                                                </div>
                                                <div style="min-width:0;">
                                                    <div style="font-weight:700; font-size:1.05rem; color:#2c3e50; letter-spacing:.4px;">
                                                        {{ $kasbonContract->joContract->no_jo_cont }}
                                                    </div>
                                                    <div style="font-size:.875rem; color:#6b7280; margin-top:3px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                                        {{ $kasbonContract->joContract->title }}
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('jo-contract.show', $kasbonContract->joContract->id_jo_cont) }}"
                                                target="_blank"
                                                style="background:#2c3e50; color:#fff; border:none; border-radius:8px;
                                                    padding:8px 18px; font-size:.82rem; font-weight:600;
                                                    display:flex; align-items:center; gap:6px; cursor:pointer; white-space:nowrap; flex-shrink:0; text-decoration:none;">
                                                <i class="fas fa-external-link-alt"></i> View JO Detail
                                            </a>
                                        </div>
                                        <div style="border-top:1px solid #d1fae5; margin:14px 0;"></div>
                                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px 24px;">
                                            <div>
                                                <div class="jo-meta-label">Contract No.</div>
                                                <div class="jo-meta-value" style="font-family:monospace; font-size:.82rem;">
                                                    {{ $kasbonContract->joContract->no_jo_cont }}</div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Contract Date</div>
                                                <div class="jo-meta-value">
                                                    {{ $kasbonContract->joContract->tgl_jo_cont ? $kasbonContract->joContract->tgl_jo_cont->format('d M Y') : '-' }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Total Items</div>
                                                <div class="jo-meta-value">
                                                    {{ $kasbonContract->joContract->items->count() }} items
                                                </div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Title</div>
                                                <div class="jo-meta-value">{{ $kasbonContract->joContract->title ?? '—' }}</div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Area</div>
                                                <div class="jo-meta-value">{{ $kasbonContract->joContract->area->area ?? '—' }}</div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Note</div>
                                                <div class="jo-meta-value" style="font-size:.82rem; line-height:1.4; white-space:pre-wrap;">
                                                    {{ $kasbonContract->joContract->note ?? '—' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <input type="text" class="form-control mt-1" readonly style="background-color:#e9ecef;"
                                        value="— No JO Contract linked —">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                    value="{{ $kasbonContract->departemen->nama_dep ?? '-' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Branch</label>
                                <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                    value="{{ $kasbonContract->cabang->nama_branch ?? '-' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Release To</label>
                                <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                    value="{{ $kasbonContract->release->nama_release ?? $kasbonContract->id_md_release ?? '-' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Release Date</label>
                                <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                    value="{{ $kasbonContract->tgl_release ? $kasbonContract->tgl_release->format('d M Y') : '-' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Priority</label>
                                <select class="form-select" disabled style="background-color:#e9ecef;">
                                    <option value="normal" {{ $kasbonContract->priority !== 'high' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ $kasbonContract->priority === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Due Date & Time</label>
                                <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                    value="{{ $kasbonContract->due_date ? $kasbonContract->due_date->format('d M Y H:i') : '-' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">CA Release Status</label>
                                <select class="form-select" disabled style="background-color:#e9ecef;">
                                    <option value="" {{ !$kasbonContract->ca_release_status ? 'selected' : '' }}>-- No Status (Pending) --</option>
                                    <option value="pending" {{ $kasbonContract->ca_release_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="release" {{ $kasbonContract->ca_release_status === 'release' ? 'selected' : '' }}>Release</option>
                                </select>
                            </div>

                            @if($kasbonContract->ca_release_status === 'release')
                                <div class="col-md-6">
                                    <label class="form-label">CA Release Date</label>
                                    <input type="text" class="form-control" readonly style="background-color:#e9ecef;"
                                        value="{{ $kasbonContract->ca_release_date ? $kasbonContract->ca_release_date->format('d M Y') : '-' }}">
                                </div>
                            @endif

                            <div class="col-md-12">
                                <label class="form-label">Note</label>
                                <textarea class="form-control" rows="3" readonly
                                    style="background-color:#e9ecef;">{{ $kasbonContract->note }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ===== CA ITEMS ===== --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>Cash Advance Items
                            <span class="badge bg-light text-dark ms-2">{{ $summary['total_items'] }} items</span>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        @php
                            // Hanya tampilkan item yang sudah di-kasbon (nilai_kasbon > 0)
                            $kasbonItems = collect($kasbonContract->items)->filter(fn($item) => ($item->nilai_kasbon ?? 0) > 0);

                            // Group items by category (from joContractItem.invoice.invoice_ctg)
                            $groupedItems = $kasbonItems->groupBy(function($item) {
                                return $item->joContractItem->invoice->invoice_ctg ?? ($item->item_type === 'tax' ? 'Tax' : '—');
                            });
                            $hasLpjRows = $kasbonItems->contains(fn($i) => !empty($i->origin_lpj_cont));
                            $itemNumber = 0;
                        @endphp

                        {{-- Summary Stats --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <div class="p-3 rounded-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                                    <div style="font-size:.75rem; color:#6b7280; font-weight:600; text-transform:uppercase;">Total Items</div>
                                    <div style="font-size:1.3rem; font-weight:700; color:#2c3e50; margin-top:4px;">
                                        {{ $summary['total_items'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 rounded-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                                    <div style="font-size:.75rem; color:#6b7280; font-weight:600; text-transform:uppercase;">Total Selling Price</div>
                                    <div style="font-size:1.1rem; font-weight:700; color:#2c3e50; margin-top:4px; font-family:monospace;">
                                        IDR {{ number_format($summary['total_selling'], 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 rounded-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                                    <div style="font-size:.75rem; color:#6b7280; font-weight:600; text-transform:uppercase;">Total HPP</div>
                                    <div style="font-size:1.1rem; font-weight:700; color:#2c3e50; margin-top:4px; font-family:monospace;">
                                        IDR {{ number_format($summary['total_hpp'], 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 rounded-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                                    <div style="font-size:.75rem; color:#6b7280; font-weight:600; text-transform:uppercase;">Total CA Amount</div>
                                    <div style="font-size:1.1rem; font-weight:700; color:#2c3e50; margin-top:4px; font-family:monospace;">
                                        IDR {{ number_format($summary['total_nilai_kasbon'], 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-kasbon-show table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:4%;">No</th>
                                        <th style="width:13%;">Category</th>
                                        <th style="min-width:200px;">Item</th>
                                        <th style="width:15%;">Selling Price (IDR)</th>
                                        <th style="width:15%;">HPP (IDR)</th>
                                        <th style="width:15%;">CA Amount (IDR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($groupedItems as $category => $items)
                                        @foreach($items as $idx => $item)
                                            @php
                                                $itemNumber++;
                                                $joItem    = $item->joContractItem;
                                                $isFromLpj = !empty($item->origin_lpj_cont);
                                                $isTax     = ($item->item_type ?? null) === 'tax';
                                            @endphp
                                            <tr class="{{ $isFromLpj ? 'row-from-lpj' : '' }} {{ $isTax ? 'tax-row' : '' }}">
                                                <td class="item-number-cell">{{ $itemNumber }}</td>

                                                @if($idx === 0)
                                                    <td class="category-cell" rowspan="{{ count($items) }}">
                                                        {{ $category }}
                                                    </td>
                                                @endif

                                                <td class="cell-left">
                                                    <span class="fw-semibold" style="color:#2c3e50;">
                                                        @if($isTax)
                                                        {{ $item->joContractPajak->nama_pajak ?? '—' }}
                                                    @else
                                                        {{ $joItem->invoice->invoice_typ ?? '—' }}
                                                    @endif
                                                    </span>
                                                    @if($isFromLpj)
                                                        <small class="ms-1" style="color:#3b82f6; font-size:.75rem;">
                                                            <i class="fas fa-link"></i> LPJ
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="currency-group-cell">
                                                        <span class="currency-label-cell">IDR</span>
                                                        <span class="value-cell">{{ number_format($joItem->hargajual_idr ?? 0, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="currency-group-cell">
                                                        <span class="currency-label-cell">IDR</span>
                                                        <span class="value-cell">{{ number_format($item->nilai_hpp_cont_item ?? 0, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($item->nilai_kasbon > 0)
                                                        <div class="currency-group-cell">
                                                            <span class="currency-label-cell">IDR</span>
                                                            <span class="value-cell" style="font-weight:700;">{{ number_format($item->nilai_kasbon, 2, ',', '.') }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                No items found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-center pe-3">
                                            <strong>GRAND TOTAL</strong>
                                        </td>
                                        <td>
                                            <div class="currency-group-cell">
                                                <span class="footer-currency-label">IDR</span>
                                                <span class="value-cell fw-bold">{{ number_format($kasbonItems->sum(fn($i) => $i->joContractItem->hargajual_idr ?? 0), 2, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group-cell">
                                                <span class="footer-currency-label">IDR</span>
                                                <span class="value-cell fw-bold">{{ number_format($summary['total_hpp'], 2, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group-cell">
                                                <span class="footer-currency-label">IDR</span>
                                                <span class="value-cell fw-bold">{{ number_format($summary['total_nilai_kasbon'], 2, ',', '.') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>

                            @if($hasLpjRows)
                                <div class="lpj-origin-note mt-2">
                                    <i class="fas fa-info-circle" style="color:#3b82f6; flex-shrink:0;"></i>
                                    <span>Rows highlighted in <strong>blue</strong> are items that were added from an LPJ page.</span>
                                </div>
                            @endif
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
                <a href="{{ route('kasbon-contract.export-pdf', $kasbonContract->id) }}" target="_blank"
                    class="btn"
                    style="padding:13px 35px; border-radius:10px; font-weight:700; font-size:1rem;
                           border-color:red; background-color:rgb(255,237,237); color:red;">
                    <i class="fas fa-file-pdf me-2"></i> Generate CA
                </a>
                <a href="{{ route('kasbon-contract.index') }}" class="btn btn-final-back">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
@endsection
