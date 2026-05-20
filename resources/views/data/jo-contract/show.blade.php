@extends('layouts.app')

@section('title', 'JO Contract Detail')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">JO Contract Detail</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom mb-0">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('jo-contract.index') }}">JO Contract</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('jo-contract.edit', $joContract->id_jo_cont) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('jo-contract.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- JO Contract Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>JO Contract Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">JO ID</label>
                            <p class="mb-0"><span class="badge bg-primary fs-6">JO-{{ $joContract->id_jo_cont }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Title</label>
                            <p class="mb-0 fw-bold">{{ $joContract->title }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Contract Number</label>
                            <p class="mb-0">
                                @if($joContract->contract)
                                {{ $joContract->contract->no_contract }}
                                @else
                                -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Contract Name</label>
                            <p class="mb-0">
                                @if($joContract->contract)
                                {{ $joContract->contract->contract }}
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
                                @if($joContract->contract && $joContract->contract->customer)
                                <i class="fas fa-building text-primary me-2"></i>{{ $joContract->contract->customer->customer }}
                                @else
                                -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Area</label>
                            <p class="mb-0">
                                @if($joContract->area)
                                <span class="badge bg-info text-dark fs-6">{{ $joContract->area->area }}</span>
                                @else
                                -
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($joContract->note)
                    <div class="mb-3">
                        <label class="text-muted small">Note</label>
                        <div class="alert alert-light mb-0">{{ $joContract->note }}</div>
                    </div>
                    @endif

                    <hr>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <i class="fas fa-calendar-plus me-2"></i>Created: {{ $joContract->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-calendar-check me-2"></i>Updated: {{ $joContract->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Items Section -->
            @if(isset($summary))
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>JO Contract Items</h5>
                </div>
                <div class="card-body">
                    @if($joContract->items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice</th>
                                    <th>Revenue IDR</th>
                                    <th>Revenue USD</th>
                                    <th>HPP Ops</th>
                                    <th>Selling Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($joContract->items as $item)
                                <tr>
                                    <td>
                                        @if($item->invoice)
                                        <span class="badge bg-secondary">{{ $item->invoice->code }}</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($item->pendapatan_idr, 2, ',', '.') }}</td>
                                    <td>$ {{ number_format($item->pendapatan_usd, 2, '.', ',') }}</td>
                                    <td>Rp {{ number_format($item->hpp_ops, 2, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->hargajual_idr, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>Total</th>
                                    <th>Rp {{ number_format($summary['total_revenue_idr'], 2, ',', '.') }}</th>
                                    <th>$ {{ number_format($summary['total_revenue_usd'], 2, '.', ',') }}</th>
                                    <th>Rp {{ number_format($summary['total_hpp_ops'], 2, ',', '.') }}</th>
                                    <th>Rp {{ number_format($summary['total_selling_price'], 2, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No items for this JO contract yet</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-lg-4">
            @if(isset($summary))
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Total Items</small>
                            <h3 class="mb-0 text-success">{{ $summary['total_items'] }}</h3>
                        </div>
                        <div class="stats-icon green">
                            <i class="fas fa-list"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Total Revenue IDR</small>
                            <h5 class="mb-0 text-primary">Rp {{ number_format($summary['total_revenue_idr'], 0, ',', '.') }}</h5>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Total Revenue USD</small>
                            <h5 class="mb-0 text-info">$ {{ number_format($summary['total_revenue_usd'], 2, '.', ',') }}</h5>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Selling Price</small>
                            <h5 class="mb-0 text-dark">Rp {{ number_format($summary['total_selling_price'], 0, ',', '.') }}</h5>
                        </div>
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
                        <a href="{{ route('jo-contract.edit', $joContract->id_jo_cont) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit JO Contract
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteJoContract({{ $joContract->id_jo_cont }})">
                            <i class="fas fa-trash me-2"></i>Delete JO Contract
                        </button>
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
function deleteJoContract(id) {
    if (confirm('Are you sure you want to delete this JO contract?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/data/jo-contract/${id}`;
        form.submit();
    }
}
</script>
@endpush

@push('styles')
<style>
.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stats-icon.green {
    background: #d1fae5;
    color: #10b981;
}
</style>
@endpush
@endsection