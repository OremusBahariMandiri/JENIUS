@extends('layouts.app')

@section('title', 'JO Contract Item Detail')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">JO Contract Item Detail</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom mb-0">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('jo-contract-item.index') }}">JO Contract Item</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('jo-contract-item.edit', $joContractItem->id_jo_cont_item) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('jo-contract-item.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Item Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Item Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Item ID</label>
                            <p class="mb-0"><span class="badge bg-primary fs-6">#{{ $joContractItem->id_jo_cont_item }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Status</label>
                            <p class="mb-0">
                                @if($joContractItem->trashed())
                                <span class="badge bg-danger fs-6">Deleted</span>
                                @else
                                <span class="badge bg-success fs-6">Active</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="fas fa-link me-2"></i>Related Data</h6>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">JO Contract</label>
                            <p class="mb-0">
                                @if($joContractItem->joContract)
                                <span class="badge bg-primary">JO-{{ $joContractItem->joContract->id_jo_cont }}</span><br>
                                <strong>{{ $joContractItem->joContract->title }}</strong>
                                @else
                                -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Invoice</label>
                            <p class="mb-0">
                                @if($joContractItem->invoice)
                                <span class="badge bg-secondary fs-6">{{ $joContractItem->invoice->code }}</span><br>
                                <small>{{ $joContractItem->invoice->invoice_ctg }} - {{ $joContractItem->invoice->invoice_typ }}</small>
                                @else
                                -
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Customer</label>
                            <p class="mb-0">
                                @if($joContractItem->joContract && $joContractItem->joContract->contract && $joContractItem->joContract->contract->customer)
                                <i class="fas fa-building text-primary me-2"></i>{{ $joContractItem->joContract->contract->customer->customer }}
                                @else
                                -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Area</label>
                            <p class="mb-0">
                                @if($joContractItem->joContract && $joContractItem->joContract->area)
                                <span class="badge bg-info text-dark">{{ $joContractItem->joContract->area->area }}</span>
                                @else
                                -
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="fas fa-money-bill me-2"></i>Financial Details</h6>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Revenue IDR</label>
                            <h5 class="mb-0 text-success">Rp {{ number_format($joContractItem->pendapatan_idr, 2, ',', '.') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Revenue USD</label>
                            <h5 class="mb-0 text-info">$ {{ number_format($joContractItem->pendapatan_usd, 2, '.', ',') }}</h5>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Exchange Rate</label>
                            <p class="mb-0">{{ number_format($joContractItem->kurs_usd, 4, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Exchange Rate Date</label>
                            <p class="mb-0">
                                @if($joContractItem->tgl_kurs_usd)
                                {{ $joContractItem->tgl_kurs_usd->format('d/m/Y') }}
                                @else
                                -
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">HPP Ops (Cost)</label>
                            <h5 class="mb-0 text-danger">Rp {{ number_format($joContractItem->hpp_ops, 2, ',', '.') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Selling Price IDR</label>
                            <h5 class="mb-0 text-primary">Rp {{ number_format($joContractItem->hargajual_idr, 2, ',', '.') }}</h5>
                        </div>
                    </div>

                    <hr>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <i class="fas fa-calendar-plus me-2"></i>Created: {{ $joContractItem->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-calendar-check me-2"></i>Updated: {{ $joContractItem->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    @if($joContractItem->trashed())
                    <div class="row mt-2 text-muted small">
                        <div class="col-md-12">
                            <i class="fas fa-trash me-2"></i>Deleted: {{ $joContractItem->deleted_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Calculations Sidebar -->
        <div class="col-lg-4">
            @if(isset($calculations))
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Calculations</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block">Total Revenue</small>
                        <h5 class="mb-0 text-primary">Rp {{ number_format($calculations['total_revenue'], 2, ',', '.') }}</h5>
                        <small class="text-muted">IDR + (USD × Rate)</small>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block">Profit</small>
                        <h5 class="mb-0 {{ $calculations['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($calculations['profit'], 2, ',', '.') }}
                        </h5>
                        <small class="text-muted">Selling Price - HPP Ops</small>
                    </div>

                    <div>
                        <small class="text-muted d-block">Profit Margin</small>
                        <h5 class="mb-0 {{ $calculations['profit_margin'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($calculations['profit_margin'], 2, ',', '.') }}%
                        </h5>
                        <small class="text-muted">(Profit ÷ Selling Price) × 100</small>
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$joContractItem->trashed())
                        <a href="{{ route('jo-contract-item.edit', $joContractItem->id_jo_cont_item) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Item
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteItem({{ $joContractItem->id_jo_cont_item }})">
                            <i class="fas fa-trash me-2"></i>Delete Item
                        </button>
                        @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            This item has been deleted
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteItem(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/data/jo-contract-item/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection