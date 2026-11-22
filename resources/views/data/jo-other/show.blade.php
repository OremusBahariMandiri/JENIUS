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

        .table-items .category-cell {
            background: #e8f5e9 !important;
            color: #2c3e50;
            font-weight: 600;
        }

        .table-items tbody tr.tax-row td {
            background: #fff !important;
        }

        .table-items tbody tr.tax-row td.item-number-cell {
            background: #ecf0f1 !important;
        }

        .table-items tbody tr.tax-row:hover td {
            background: #f8f9fa !important;
        }

        .currency-group-cell { display: flex; align-items: center; gap: 5px; }
        .currency-label-cell { background-color: #e9ecef; color: #495057; border: 1px solid #ced4da; padding: 0.2rem 0.5rem; font-size: 0.78rem; border-radius: 4px; min-width: 42px; text-align: center; font-weight: 600; white-space: nowrap; }
        .value-cell { flex: 1; text-align: right; font-weight: 600; font-size: 0.88rem; color: #2c3e50; }

        .table-footer {
            background: #2c3e50;
            color: white;
            font-weight: 700;
        }

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

        .period-badge {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
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
                        <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>JO Other Information</h6>
                    </div>
                    <div class="card-body p-4">
                        @php
                            $statusColors = [
                                'Draft'       => '#6c757d',
                                'On Progress' => '#ffc107',
                                'Approved'    => '#198754',
                                'Completed'   => '#198754',
                                'Pending'     => '#ffc107',
                            ];
                            $statusColor = $statusColors[$joOther->sts_proses] ?? '#17a2b8';
                        @endphp

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">No. JO Other</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="fas fa-file-invoice"></i>
                                    </span>
                                    <input type="text" class="form-control fw-bold"
                                        value="{{ $joOther->no_jo_other ?? '-' }}" readonly
                                        style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text text-white" style="background-color:{{ $statusColor }};">
                                        <i class="fas fa-tag"></i>
                                    </span>
                                    <input type="text" class="form-control fw-bold" readonly
                                        value="{{ $joOther->sts_proses ?? 'Draft' }}"
                                        style="background-color:#e9ecef; color:{{ $statusColor }};">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" readonly
                                style="background-color:#e9ecef;"
                                value="{{ $joOther->title }}">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer</label>
                                <input type="text" class="form-control" readonly
                                    style="background-color:#e9ecef;"
                                    value="{{ $joOther->customer->customer ?? '-' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Port</label>
                                <input type="text" class="form-control" readonly
                                    style="background-color:#e9ecef;"
                                    value="{{ $joOther->port->name_port ?? '-' }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Other Type</label>
                                <input type="text" class="form-control" readonly
                                    style="background-color:#e9ecef;"
                                    value="{{ $joOther->other->other ?? '-' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Period</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" readonly
                                        style="background-color:#e9ecef;"
                                        value="{{ $joOther->date_start?->format('d M Y') ?? '-' }}">
                                    <span class="input-group-text"><i class="fas fa-arrow-right"></i></span>
                                    <input type="text" class="form-control" readonly
                                        style="background-color:#e9ecef;"
                                        value="{{ $joOther->date_end?->format('d M Y') ?? '-' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Created At</label>
                                <input type="text" class="form-control" readonly
                                    style="background-color:#e9ecef;"
                                    value="{{ $joOther->created_at->format('d M Y, H:i') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Updated</label>
                                <input type="text" class="form-control" readonly
                                    style="background-color:#e9ecef;"
                                    value="{{ $joOther->updated_at->format('d M Y, H:i') }}">
                            </div>
                        </div>

                        @if ($joOther->note)
                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" rows="3" readonly
                                style="background-color:#e9ecef;">{{ $joOther->note }}</textarea>
                        </div>
                        @endif
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
                                @if ($firstItem)
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
                                        <th style="width: 13%;">Income (IDR)</th>
                                        <th style="width: 13%;">Income (USD)</th>
                                        <th style="width: 13%;">Selling Price (IDR)</th>
                                        <th style="width: 13%;">HPP (Ops Costs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $categoryItems = [];
                                        foreach ($joOther->items as $item) {
                                            $cat = $item->invoice->invoice_ctg;
                                            if (!isset($categoryItems[$cat])) $categoryItems[$cat] = [];
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

                                                <td class="item-type-cell text-start">
                                                    <span class="fw-semibold" style="color:#2c3e50;">{{ $item->invoice->invoice_typ }}</span>
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

                                    {{-- TAX ROWS --}}
                                    @if(isset($pajakItems) && $pajakItems->count() > 0)
                                        @foreach($pajakItems as $pi)
                                            @php $itemNumber++; @endphp
                                            <tr class="tax-row">
                                                <td class="item-number-cell">{{ $itemNumber }}</td>
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
                                                <span class="value-footer">{{ number_format($summary['total_selling_price'], 2, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group-footer">
                                                <span class="currency-label-footer">IDR</span>
                                                <span class="value-footer">{{ number_format($summary['total_hpp_ops'], 2, ',', '.') }}</span>
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
