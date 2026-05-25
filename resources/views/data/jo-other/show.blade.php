@extends('layouts.app')

@section('title', 'JO Other Detail')

@push('styles')
    <style>
        .joOtherShowPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .joOtherShowPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        .joOtherShowPage .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        .joOtherShowPage .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .joOtherShowPage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .joOtherShowPage .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .joOtherShowPage .btn-success:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }

        .joOtherShowPage .border-bottom {
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

        .kurs-section {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 10px;
            padding: 15px 20px;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .kurs-section .form-label {
            color: rgba(255, 255, 255, 0.95);
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .kurs-section .form-control {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        .table-items {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        .table-items thead th {
            background-color: #f8f9fa;
            color: #2c3e50;
            border: 1px solid #dee2e6;
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
        }

        .table-items tbody tr:hover td:not(.category-cell) {
            background-color: #f8f9fa;
        }

        .category-cell {
            background: #f1f5f9 !important;
            color: #2c3e50 !important;
            font-weight: 600;
            font-size: 0.875rem;
            border-right: 2px solid #dee2e6 !important;
            text-align: center;
            vertical-align: middle;
            position: relative;
            padding: 10px !important;
        }

        .category-cell .category-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }

        .category-cell .category-name {
            font-weight: 700;
            font-size: 0.85rem;
            color: #1e293b;
        }

        .category-cell .category-count {
            background: #e2e8f0;
            color: #475569;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .currency-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .currency-label {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #2c3e50;
            padding: 0.5rem;
            font-size: 0.8rem;
            border-radius: 6px 0 0 6px;
            min-width: 50px;
            text-align: center;
            font-weight: 600;
        }

        .currency-value {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-left: none;
            padding: 0.5rem;
            border-radius: 0 6px 6px 0;
            flex: 1;
            font-weight: 600;
            color: #2c3e50;
        }

        .table-footer {
            background: #2c3e50;
            color: white;
            font-weight: 700;
        }

        .table-footer .currency-label {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
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
    <div class="container-fluid joOtherShowPage">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-12">
                <!-- Page Header Card -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-file-invoice me-2"></i>JO Other Detail</span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('jo-other.edit', $joOther->id_jo_other) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('jo-other.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </div>
                </div>

                <!-- JO Other Info Card -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color: #d1fae5">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>JO Other Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Title -->
                        <div class="mb-4">
                            <label class="text-muted small text-uppercase mb-2">Title</label>
                            <h3 class="mb-0 text-primary">
                                <i class="fas fa-file-alt me-2"></i>{{ $joOther->title }}
                            </h3>
                        </div>

                        <hr>

                        <div class="info-card">
                            <div class="info-row">
                                <span class="info-label">JO Other ID</span>
                                <span class="info-value">
                                    <span class="badge bg-secondary fs-6 px-3 py-2">JOO-{{ str_pad($joOther->id_jo_other, 4, '0', STR_PAD_LEFT) }}</span>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Customer</span>
                                <span class="info-value">
                                    <strong>{{ $joOther->customer->customer }}</strong><br>
                                    @if ($joOther->customer->no_customer)
                                        <small class="text-muted">({{ $joOther->customer->no_customer }})</small>
                                    @endif
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Other Type</span>
                                <span class="info-value">
                                    <span class="badge bg-info text-dark fs-6">{{ $joOther->other->other }}</span>
                                    @if ($joOther->other->code)
                                        <br><small class="text-muted">({{ $joOther->other->code }})</small>
                                    @endif
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Port</span>
                                <span class="info-value">
                                    <strong>{{ $joOther->port->name_port }}</strong>
                                    @if ($joOther->port->no_port)
                                        <br><small class="text-muted">({{ $joOther->port->no_port }})</small>
                                    @endif
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Date Range</span>
                                <span class="info-value">
                                    <i class="fas fa-calendar-alt me-1 text-primary"></i>
                                    {{ $joOther->date_start ? $joOther->date_start->format('d M Y') : '-' }}
                                    <br>
                                    <small class="text-muted">to</small>
                                    <br>
                                    <i class="fas fa-calendar-check me-1 text-success"></i>
                                    {{ $joOther->date_end ? $joOther->date_end->format('d M Y') : '-' }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Status</span>
                                <span class="info-value">
                                    @if ($joOther->sts_proses == 'Draft')
                                        <span class="badge bg-secondary fs-6">{{ $joOther->sts_proses }}</span>
                                    @elseif ($joOther->sts_proses == 'On Progress')
                                        <span class="badge bg-warning text-dark fs-6">{{ $joOther->sts_proses }}</span>
                                    @elseif ($joOther->sts_proses == 'Completed')
                                        <span class="badge bg-success fs-6">{{ $joOther->sts_proses }}</span>
                                    @else
                                        <span class="badge bg-primary fs-6">{{ $joOther->sts_proses }}</span>
                                    @endif
                                </span>
                            </div>
                            @if ($joOther->note)
                                <div class="info-row">
                                    <span class="info-label">Note</span>
                                    <span class="info-value">{{ $joOther->note }}</span>
                                </div>
                            @endif
                            <div class="info-row">
                                <span class="info-label">Created At</span>
                                <span class="info-value">
                                    <i class="fas fa-calendar-plus me-1 text-primary"></i>
                                    {{ $joOther->created_at->format('d M Y H:i') }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Last Updated</span>
                                <span class="info-value">
                                    <i class="fas fa-calendar-check me-1 text-success"></i>
                                    {{ $joOther->updated_at->format('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- JO Other Items -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color: #d1fae5">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>JO Other Items
                                <span class="badge bg-light text-dark ms-2">{{ $joOther->items->count() }} items</span>
                            </h6>
                            <div class="kurs-section d-flex gap-3 align-items-center mb-0" style="padding: 8px 15px;">
                                @php
                                    $firstItem = $joOther->items->first();
                                @endphp
                                @if($firstItem)
                                    <div>
                                        <label class="form-label mb-1">Kurs Date</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $firstItem->tgl_kurs_usd ? $firstItem->tgl_kurs_usd->format('d M Y') : '-' }}"
                                            style="min-width: 130px;" readonly>
                                    </div>
                                    <div>
                                        <label class="form-label mb-1">Kurs Rate</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ number_format($firstItem->kurs_usd, 2, ',', '.') }}"
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
                                        <th style="width: 12%;">Category</th>
                                        <th style="width: 18%;">Item / Invoice Type</th>
                                        <th style="width: 13%;">Pendapatan IDR</th>
                                        <th style="width: 13%;">Pendapatan USD</th>
                                        <th style="width: 13%;">HPP (Biaya Ops)</th>
                                        <th style="width: 13%;">Harga Jual (IDR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currentCategory = null;
                                        $categoryItems = [];

                                        // Group items by category
                                        foreach ($joOther->items as $item) {
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
                                                    <td class="category-cell" rowspan="{{ count($items) }}">
                                                        <div class="category-content">
                                                            <span class="category-name">{{ $category }}</span>
                                                            <span class="category-count">{{ count($items) }} item{{ count($items) > 1 ? 's' : '' }}</span>
                                                        </div>
                                                    </td>
                                                @endif

                                                <td class="item-type-cell">
                                                    {{ $item->invoice->invoice_typ }}
                                                </td>
                                                <td>
                                                    <div class="currency-group">
                                                        <span class="currency-label">IDR</span>
                                                        <span class="currency-value">{{ number_format($item->pendapatan_idr, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="currency-group">
                                                        <span class="currency-label">USD</span>
                                                        <span class="currency-value">{{ number_format($item->pendapatan_usd, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="currency-group">
                                                        <span class="currency-label">IDR</span>
                                                        <span class="currency-value">{{ number_format($item->hpp_ops, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="currency-group">
                                                        <span class="currency-label">IDR</span>
                                                        <span class="currency-value">{{ number_format($item->hargajual_idr, 2, ',', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <tfoot class="table-footer">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>GRAND TOTAL</strong></td>
                                        <td>
                                            <div class="currency-group">
                                                <span class="currency-label text-white">IDR</span>
                                                <span style="color: #fff; font-weight: 700;">
                                                    {{ number_format($summary['total_revenue_idr'], 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group">
                                                <span class="currency-label text-white">USD</span>
                                                <span style="color: #fff; font-weight: 700;">
                                                    {{ number_format($summary['total_revenue_usd'], 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group">
                                                <span class="currency-label text-white">IDR</span>
                                                <span style="color: #fff; font-weight: 700;">
                                                    {{ number_format($summary['total_hpp_ops'], 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group">
                                                <span class="currency-label text-white">IDR</span>
                                                <span style="color: #fff; font-weight: 700;">
                                                    {{ number_format($summary['total_selling_price'], 2, ',', '.') }}
                                                </span>
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
    <form id="deleteForm" action="{{ route('jo-other.destroy', $joOther->id_jo_other) }}" method="POST"
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
            title: 'Delete JO Other?',
            html: `JO Other <strong>{{ $joOther->title }}</strong> will be permanently deleted.<br><small class="text-muted">This action cannot be undone.</small>`,
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