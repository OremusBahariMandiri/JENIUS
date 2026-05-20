@extends('layouts.app')

@section('title', 'JO Contract Detail')

@push('styles')
    <style>
        .required-field::after {
            content: " *";
            color: red;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            color: black;
            box-shadow: 0 4px 15px rgba(207, 207, 207, 0.4);
        }

        .info-card h6 {
            color: black;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: rgba(0, 0, 0, 0.9);
        }

        .info-value {
            color: rgb(0, 0, 0);
            text-align: right;
        }

        .kurs-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .kurs-section h6 {
            color: white;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .kurs-section .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .kurs-section .form-control {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            padding: 0.6rem 0.75rem;
        }

        .table-items {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        .table-items thead th {
            background-color: #ececec;
            color: rgb(0, 0, 0);
            border: 1px solid #34495e;
            padding: 14px 10px;
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

        /* Category Cell Styling - MERGED CELL */
        .category-cell {
            background: #f8f9fa !important;
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
            color: #495057;
        }

        .category-cell .category-count {
            background: #e9ecef;
            color: #495057;
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
            background-color: #34495e;
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
            background: linear-gradient(90deg, #2c3e50 0%, #34495e 100%);
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

        .badge-category {
            background-color: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .item-type-cell {
            font-weight: 600;
            color: #2c3e50;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="h3 mb-2 text-gray-800">JO Contract Detail</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom mb-0">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('jo-contract.index') }}">JO Contract</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <!-- JO Contract Info Card -->
                <div class="card mb-4">
                    <div class="card-header text-black" style="background-color: #d1fae5">
                        <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>JO Contract Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-card">
                            <div class="info-row">
                                <span class="info-label">JO Contract ID:</span>
                                <span class="info-value">{{ $joContract->id_jo_cont }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Contract:</span>
                                <span class="info-value">
                                    {{ $joContract->contract->no_contract }} - {{ $joContract->contract->contract }}
                                    @if ($joContract->contract->customer)
                                        <br><small>({{ $joContract->contract->customer->customer }})</small>
                                    @endif
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Area:</span>
                                <span class="info-value">{{ $joContract->area->area }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Title:</span>
                                <span class="info-value">{{ $joContract->title }}</span>
                            </div>
                            @if ($joContract->note)
                                <div class="info-row">
                                    <span class="info-label">Note:</span>
                                    <span class="info-value">{{ $joContract->note }}</span>
                                </div>
                            @endif
                            <div class="info-row">
                                <span class="info-label">Created At:</span>
                                <span class="info-value">{{ $joContract->created_at->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- JO Contract Items -->
                <div class="card">
                    <div class="card-header text-black" style="background-color: #d1fae5">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>JO Contract Items ({{ $joContract->items->count() }} items)</h5>
                            <div class="d-flex gap-3 align-items-center">
                                @php
                                    $firstItem = $joContract->items->first();
                                @endphp
                                @if($firstItem)
                                    <div>
                                        <label class="form-label mb-1 small">Kurs Date</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $firstItem->tgl_kurs_usd ? $firstItem->tgl_kurs_usd->format('d M Y') : '-' }}"
                                            style="min-width: 150px; background-color: #f8f9fa;" readonly>
                                    </div>
                                    <div>
                                        <label class="form-label mb-1 small">Kurs Rate</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ number_format($firstItem->kurs_usd, 2, ',', '.') }}"
                                            style="min-width: 130px; background-color: #f8f9fa;" readonly>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
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
                                                <span class="currency-label text-black">IDR</span>
                                                <span style="color: rgb(0, 0, 0); font-weight: 700;">
                                                    {{ number_format($summary['total_revenue_idr'], 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group">
                                                <span class="currency-label text-black">USD</span>
                                                <span style="color: rgb(0, 0, 0); font-weight: 700;">
                                                    {{ number_format($summary['total_revenue_usd'], 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group">
                                                <span class="currency-label text-black">IDR</span>
                                                <span style="color: rgb(0, 0, 0); font-weight: 700;">
                                                    {{ number_format($summary['total_hpp_ops'], 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group">
                                                <span class="currency-label text-black">IDR</span>
                                                <span style="color: rgb(0, 0, 0); font-weight: 700;">
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

                <!-- Action Buttons -->
                <div class="action-buttons mt-4 mb-5">
                    <a href="{{ route('jo-contract.edit', $joContract->id_jo_cont) }}" class="btn btn-warning btn-lg">
                        <i class="fas fa-edit me-2"></i>Edit JO Contract
                    </a>
                    <a href="{{ route('jo-contract.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <button type="button" class="btn btn-danger btn-lg" onclick="confirmDelete()">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
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
    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this JO Contract? This action cannot be undone!')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endpush