@extends('layouts.app')

@section('title', 'JO Contract Detail')

@push('styles')
    <style>
        .joContractShowPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .joContractShowPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        .joContractShowPage .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        .joContractShowPage .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .joContractShowPage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .joContractShowPage .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .joContractShowPage .btn-success:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }

        .joContractShowPage .border-bottom {
            border-color: #e2e8f0 !important;
        }

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #64748b;
            font-size: 0.875rem;
        }

        .info-value {
            color: #1e293b;
            text-align: right;
            font-weight: 500;
        }

        .table-items {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        .table-items thead th {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #2c3e50;
            padding: 12px 10px;
            font-weight: 600;
            font-size: 0.875rem;
            vertical-align: middle;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-items tbody td {
            border: 1px solid #dee2e6;
            padding: 10px;
            vertical-align: middle;
            background-color: #fff;
            text-align: center;
        }

        .table-items tbody tr:hover td {
            background-color: #f8f9fa;
        }

        .table-items .item-number-cell {
            font-weight: 600;
            color: #2c3e50;
            background-color: #ecf0f1 !important;
        }

        .table-items .category-cell {
            background: #e8f5e9 !important;
            color: #2c3e50;
            font-weight: 600;
        }

        .table-items tbody tr.tax-row td {
            background: #fff !important;
        }

        .table-items tbody tr.tax-row:hover td {
            background: #f8f9fa !important;
        }

        .table-footer {
            background: #2c3e50;
            color: white;
            font-weight: 700;
        }

        .table-footer td {
            padding: 15px 10px;
            font-size: 0.95rem;
        }

        .item-number-cell {
            text-align: center;
            font-weight: 600;
            color: #2c3e50;
            background-color: #ecf0f1 !important;
            font-size: 0.9rem;
        }

        .item-type-cell {
            font-weight: 600;
            color: #2c3e50;
        }

        .currency-group-cell { display: flex; align-items: center; gap: 5px; }
        .currency-label-cell { background-color: #e9ecef; color: #495057; border: 1px solid #ced4da; padding: 0.2rem 0.5rem; font-size: 0.78rem; border-radius: 4px; min-width: 42px; text-align: center; font-weight: 600; white-space: nowrap; }
        .value-cell { flex: 1; text-align: right; font-weight: 600; font-size: 0.88rem; color: #2c3e50; }

        .currency-group-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .currency-label-footer {
            background-color: #2c3e50;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.2rem 0.5rem;
            font-size: 0.78rem;
            border-radius: 4px;
            min-width: 42px;
            text-align: center;
            font-weight: 600;
        }

        .value-footer {
            flex: 1;
            color: #000;
            font-weight: 600;
            font-size: 0.88rem;
            padding-right: 0;
            text-align: right;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid joContractShowPage">
        <div class="row">
            <div class="col-lg-12">
                <!-- Page Header Card -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-eye me-2"></i>Detail JO Contract</span>
                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{ route('jo-contract.edit', $joContract->id_jo_cont) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('jo-contract.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <!-- JO Contract Info Card -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color: #d1fae5">
                        <h6 class="mb-0"><i class="fas fa-file-contract me-2"></i>JO Contract Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">No. JO</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="fas fa-file-alt"></i>
                                        </span>
                                        <input type="text" class="form-control fw-bold"
                                            value="{{ $joContract->no_jo_cont ?? '-' }}" readonly
                                            style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">JO Date</label>
                                    <input type="text" class="form-control" readonly
                                        style="background-color:#e9ecef;"
                                        value="{{ $joContract->tgl_jo_cont ? $joContract->tgl_jo_cont->format('d M Y') : '-' }}">
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Contract</label>
                                <div class="card mt-1">
                                    <div class="card-header bg-light">
                                        <strong><i class="fas fa-file-contract me-2"></i>Selected Contract</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <td width="40%"><strong>No. Contract</strong></td>
                                                        <td width="5%">:</td>
                                                        <td>{{ $joContract->contract->no_contract }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Contract</strong></td>
                                                        <td>:</td>
                                                        <td>{{ $joContract->contract->contract }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Customer</strong></td>
                                                        <td>:</td>
                                                        <td>{{ $joContract->contract->customer->customer ?? '-' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <td width="40%"><strong>Expenditure</strong></td>
                                                        <td width="5%">:</td>
                                                        <td>{{ $joContract->contract->expenditure ? 'Rp ' . number_format($joContract->contract->expenditure, 0, ',', '.') : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Start Date</strong></td>
                                                        <td>:</td>
                                                        <td>{{ $joContract->contract->date_start ? \Carbon\Carbon::parse($joContract->contract->date_start)->format('d M Y') : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>End Date</strong></td>
                                                        <td>:</td>
                                                        <td>{{ $joContract->contract->date_end ? \Carbon\Carbon::parse($joContract->contract->date_end)->format('d M Y') : '-' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        @if ($joContract->contract->note)
                                            <div class="row">
                                                <div class="col-12">
                                                    <hr>
                                                    <strong>Note:</strong>
                                                    <p class="mb-0 text-muted">{{ $joContract->contract->note }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-select" readonly
                                    style="background-color:#e9ecef;"
                                    value="{{ $joContract->area->area }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" readonly
                                style="background-color:#e9ecef;"
                                value="{{ $joContract->title }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" rows="3" readonly
                                style="background-color:#e9ecef;">{{ $joContract->note }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- JO Contract Items -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color: #d1fae5">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>JO Contract Items
                                <span class="badge bg-light text-dark ms-2">{{ $joContract->items->count() }} items</span>
                            </h6>
                            <div class="kurs-section d-flex gap-3 align-items-center mb-0" style="padding: 8px 15px;">
                                @php
                                    $firstItem = $joContract->items->first();
                                @endphp
                                @if($firstItem && $firstItem->tgl_kurs_usd)
                                    <div>
                                        <label class="form-label mb-1">Kurs Date & Time</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $firstItem->tgl_kurs_usd->format('d M Y H:i') }}"
                                            style="min-width: 160px;" readonly>
                                    </div>
                                    <div>
                                        <label class="form-label mb-1">Kurs Rate</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ number_format($firstItem->kurs_usd, 2, ',', '.') }}"
                                            style="min-width: 120px;" readonly>
                                    </div>
                                @else
                                    <div>
                                        <label class="form-label mb-1">Kurs Date & Time</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="-"
                                            style="min-width: 160px;" readonly>
                                    </div>
                                    <div>
                                        <label class="form-label mb-1">Kurs Rate</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="-"
                                            style="min-width: 120px;" readonly>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-items table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 13%;">Category</th>
                                        <th style="width: 13%;">Item</th>
                                        <th style="width: 12%;">Income (IDR)</th>
                                        <th style="width: 12%;">Income (USD)</th>
                                        <th style="width: 12%;">Selling Price (IDR)</th>
                                        <th style="width: 12%;">HPP (Ops Costs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currentCategory = null;
                                        $categoryItems = [];

                                        // Group items by category
                                        foreach ($joContract->items as $item) {
                                            $cat = $item->invoice->invoice_ctg;
                                            if (!isset($categoryItems[$cat])) {
                                                $categoryItems[$cat] = [];
                                            }
                                            $categoryItems[$cat][] = $item;
                                        }

                                        $itemNumber = 0;
                                    @endphp

                                    @foreach ($categoryItems as $category => $items)
                                        @foreach ($items as $index => $item)
                                            @php $itemNumber++; @endphp
                                            <tr>
                                                <td class="item-number-cell">{{ $itemNumber }}</td>

                                                @if ($index === 0)
                                                    <td class="category-cell" rowspan="{{ count($items) }}">{{ $category }}</td>
                                                @endif

                                                <td class="text-start">
                                                    <span class="fw-semibold" style="color:#2c3e50;">
                                                        {{ $item->invoice->invoice_typ }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="currency-group-cell">
                                                        <span class="currency-label-cell">IDR</span>
                                                        <span class="value-cell">{{ number_format($item->pendapatan_idr, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="currency-group-cell">
                                                        <span class="currency-label-cell">USD</span>
                                                        <span class="value-cell">{{ number_format($item->pendapatan_usd, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="currency-group-cell">
                                                        <span class="currency-label-cell">IDR</span>
                                                        <span class="value-cell">{{ number_format($item->hargajual_idr, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="currency-group-cell">
                                                        <span class="currency-label-cell">IDR</span>
                                                        <span class="value-cell">{{ number_format($item->hpp_ops, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    @if($pajakItems->count() > 0)
                                        @foreach($pajakItems as $pi)
                                            @php $itemNumber++; @endphp
                                            <tr class="tax-row">
                                                <td class="item-number-cell" style="background-color:#ecf0f1 !important; color:#2c3e50; font-weight:600; text-align:center;">{{ $itemNumber }}</td>
                                                <td class="category-cell" style="background:#fef9c3 !important; color:#000;">Tax</td>
                                                <td class="text-start">
                                                    <span class="fw-semibold" style="color:#2c3e50;">{{ $pi->nama_pajak }}</span>
                                                    <small class="text-muted ms-1">({{ $pi->persentase }}%)</small>
                                                </td>
                                                <td>
                                                    <div class="currency-group-cell">
                                                        <span class="currency-label-cell">IDR</span>
                                                        <span class="value-cell">0,00</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="currency-group-cell">
                                                        <span class="currency-label-cell">USD</span>
                                                        <span class="value-cell">0,00</span>
                                                    </div>
                                                </td>
                                                <td class="tax-selling">
                                                    <div class="currency-group-cell">
                                                        <span class="currency-label-cell">IDR</span>
                                                        <span class="value-cell">
                                                            @if($pi->include_selling)
                                                                {{ number_format($pi->nominal_pajak, 2, ',', '.') }}
                                                            @else
                                                                0,00
                                                            @endif
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="tax-hpp">
                                                    <div class="currency-group-cell">
                                                        <span class="currency-label-cell">IDR</span>
                                                        <span class="value-cell">
                                                            @if($pi->include_hpp)
                                                                {{ number_format($pi->nominal_pajak_hpp, 2, ',', '.') }}
                                                            @else
                                                                0,00
                                                            @endif
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot class="table-footer">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>GRAND TOTAL</strong></td>
                                        <td>
                                            <div class="currency-group-footer">
                                                <span class="currency-label-footer">IDR</span>
                                                <span class="value-footer">{{ number_format($summary['total_revenue_idr'], 2, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group-footer">
                                                <span class="currency-label-footer">USD</span>
                                                <span class="value-footer">{{ number_format($summary['total_revenue_usd'], 2, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group-footer">
                                                <span class="currency-label-footer">IDR</span>
                                                <span class="value-footer">{{ number_format($summary['total_selling_price'] + $summary['total_tax_selling'], 2, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group-footer">
                                                <span class="currency-label-footer">IDR</span>
                                                <span class="value-footer">{{ number_format($summary['total_hpp_ops'] + $summary['total_tax_hpp'], 2, ',', '.') }}</span>
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

    <!-- Delete Form -->
    <form id="deleteForm" action="{{ route('jo-contract.destroy', $joContract->id_jo_cont) }}" method="POST"
        style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete() {
        Swal.fire({
            title: 'Delete JO Contract?',
            html: `JO Contract <strong>{{ $joContract->title }}</strong> will be permanently deleted.<br><small class="text-muted">This action cannot be undone.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete!',
            cancelButtonText: 'Cancel',
            focusCancel: true,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm').submit();
            }
        });
    }
</script>
@endpush