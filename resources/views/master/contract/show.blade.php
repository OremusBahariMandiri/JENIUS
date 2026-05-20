@extends('layouts.app')

@section('title', 'Contract Detail')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">Contract Detail</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom mb-0">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('contract.index') }}">Contract</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('contract.edit', $contract->id_md_cont) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('contract.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Contract Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>Contract Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Contract No.</label>
                            <p class="mb-0"><span class="badge bg-primary fs-6">{{ $contract->no_contract }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Contract Name</label>
                            <p class="mb-0 fw-bold">{{ $contract->contract }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Customer</label>
                        <p class="mb-0">
                            <i class="fas fa-building text-primary me-2"></i>
                            <strong>{{ $contract->customer->customer ?? '-' }}</strong>
                            @if($contract->customer)
                            <br><small class="text-muted ms-4">{{ $contract->customer->code }}</small>
                            @endif
                        </p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Expenditure Value</label>
                            <p class="mb-0">
                                <strong class="text-success fs-5">Rp {{ number_format($contract->expenditure, 2, ',', '.') }}</strong>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Status</label>
                            <p class="mb-0">
                                @if($contract->date_end >= now())
                                <span class="badge bg-success fs-6">Active</span>
                                @else
                                <span class="badge bg-secondary fs-6">Expired</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Start Date</label>
                            <p class="mb-0">
                                <i class="fas fa-calendar-alt text-success me-2"></i>
                                {{ $contract->date_start->format('d F Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">End Date</label>
                            <p class="mb-0">
                                <i class="fas fa-calendar-check text-danger me-2"></i>
                                {{ $contract->date_end->format('d F Y') }}
                            </p>
                        </div>
                    </div>

                    @php
                    $today = now();
                    $remaining = $contract->date_end >= $today ? $today->diffInDays($contract->date_end) : 0;
                    $total = $contract->date_start->diffInDays($contract->date_end);
                    $progress = $total > 0 ? (($total - $remaining) / $total) * 100 : 0;
                    @endphp

                    <div class="mb-3">
                        <label class="text-muted small">Contract Duration</label>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-hourglass-start me-2"></i>Total: {{ $total }} days</span>
                            <span><i class="fas fa-hourglass-end me-2"></i>Remaining: {{ $remaining }} days</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%">
                                {{ number_format($progress, 1) }}%
                            </div>
                        </div>
                    </div>

                    @if($contract->note)
                    <div class="mb-3">
                        <label class="text-muted small">Note</label>
                        <div class="alert alert-light mb-0">{{ $contract->note }}</div>
                    </div>
                    @endif

                    <hr>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <i class="fas fa-calendar-plus me-2"></i>Created: {{ $contract->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-calendar-check me-2"></i>Updated: {{ $contract->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Items Section -->
            @if(isset($summary))
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Related Items</h5>
                </div>
                <div class="card-body">
                    @if($summary['total_items'] > 0)
                    <p class="text-muted">Total: {{ $summary['total_items'] }} items</p>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No items available for this contract</p>
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
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Summary</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Contract Status</small>
                            <h4 class="mb-0">{{ $summary['status'] }}</h4>
                        </div>
                        <div class="stats-icon bg-{{ $summary['status'] == 'Active' ? 'success' : 'secondary' }} bg-opacity-25 p-3 rounded">
                            <i class="fas {{ $summary['status'] == 'Active' ? 'fa-check-circle' : 'fa-times-circle' }} text-{{ $summary['status'] == 'Active' ? 'success' : 'secondary' }} fs-4"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Days Remaining</small>
                            <h3 class="mb-0 text-primary">{{ $summary['remaining_days'] }}</h3>
                        </div>
                        <div class="stats-icon bg-primary bg-opacity-25 p-3 rounded">
                            <i class="fas fa-calendar-day text-primary fs-4"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Items</small>
                            <h3 class="mb-0 text-dark">{{ $summary['total_items'] }}</h3>
                        </div>
                        <div class="stats-icon bg-danger bg-opacity-25 p-3 rounded">
                            <i class="fas fa-list text-danger fs-4"></i>
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
                        <a href="{{ route('contract.edit', $contract->id_md_cont) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Contract
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteContract({{ $contract->id_md_cont }})">
                            <i class="fas fa-trash me-2"></i>Delete Contract
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
function deleteContract(id) {
    if (confirm('Are you sure you want to delete this contract?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/master/contract/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection