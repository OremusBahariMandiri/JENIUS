@extends('layouts.app')

@section('title', 'Contract Detail')

@section('content')
<div class="container-fluid contractPage">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Contract Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Contract Information</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('contract.edit', $contract->id_md_cont) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('contract.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Contract Name -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Contract Name</label>
                        <h3 class="mb-0 text-primary">
                            <i class="fas fa-file-contract me-2"></i>{{ $contract->contract }}
                        </h3>
                    </div>

                    <hr>

                    <!-- Details Grid -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Contract No.</label>
                            <div>
                                <span class="badge bg-primary fs-6 px-3 py-2">
                                    {{ $contract->no_contract }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Status</label>
                            <div>
                                @if($contract->date_end >= now())
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </span>
                                @else
                                <span class="badge bg-secondary fs-6 px-3 py-2">
                                    <i class="fas fa-times-circle me-1"></i>Expired
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Customer Section -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Customer</label>
                        <div class="alert alert-light mb-0">
                            <i class="fas fa-building text-primary me-2"></i>
                            <strong>{{ $contract->customer->customer ?? '-' }}</strong>
                        </div>
                    </div>

                    <!-- Expenditure Value -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Expenditure Value</label>
                        <h4 class="mb-0 text-success">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            IDR {{ number_format($contract->expenditure, 2, ',', '.') }}
                        </h4>
                    </div>

                    <!-- Date Information -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Start Date</label>
                            <p class="mb-0">
                                <i class="fas fa-calendar-alt text-success me-2"></i>
                                {{ $contract->date_start->format('d F Y') }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">End Date</label>
                            <p class="mb-0">
                                <i class="fas fa-calendar-check text-danger me-2"></i>
                                {{ $contract->date_end->format('d F Y') }}
                            </p>
                        </div>
                    </div>

                    <!-- Contract Duration Progress -->
                    @php
                    $today = now();
                    $remaining = $contract->date_end >= $today ? $today->diffInDays($contract->date_end) : 0;
                    $total = $contract->date_start->diffInDays($contract->date_end);
                    $progress = $total > 0 ? (($total - $remaining) / $total) * 100 : 0;
                    @endphp

                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Contract Duration</label>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-hourglass-start me-2 text-primary"></i>Total: {{ $total }} days</span>
                            <span><i class="fas fa-hourglass-end me-2 text-warning"></i>Remaining: {{ $remaining }} days</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar"
                                 style="background-color: var(--primary-green); width: {{ $progress }}%"
                                 role="progressbar">
                                <strong>{{ number_format($progress, 1) }}%</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Note Section -->
                    @if($contract->note)
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Note</label>
                        <div class="alert alert-light border-start border-4 border-warning mb-0">
                            <i class="fas fa-sticky-note text-warning me-2"></i>
                            {{ $contract->note }}
                        </div>
                    </div>
                    @endif

                    <hr>

                    <!-- Timestamps -->
                    <div class="row text-muted">
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-plus me-2 text-primary"></i>
                            <strong>Created:</strong><br>
                            <span class="ms-4">{{ $contract->created_at->format('d F Y, H:i') }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-check me-2 text-success"></i>
                            <strong>Last Updated:</strong><br>
                            <span class="ms-4">{{ $contract->updated_at->format('d F Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Items Section -->
            @if(isset($summary))
            <div class="card shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Related Items</h5>
                    <span class="badge bg-light text-dark">{{ $summary['total_items'] }} items</span>
                </div>
                <div class="card-body">
                    @if($summary['total_items'] > 0)
                    <p class="text-muted">Total: {{ $summary['total_items'] }} items</p>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Items Available</h5>
                        <p class="text-muted mb-0">There are no items associated with this contract yet.</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-file-contract me-2 text-success"></i>Quick Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small text-uppercase mb-2">Contract No.</label>
                        <h4 class="mb-0 text-primary">{{ $contract->no_contract }}</h4>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small text-uppercase mb-2">Customer</label>
                        <h6 class="mb-0">{{ $contract->customer->customer ?? '-' }}</h6>
                    </div>
                    <div>
                        <label class="text-muted small text-uppercase mb-2">Value</label>
                        <h5 class="mb-0 text-success">IDR {{ number_format($contract->expenditure, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            @if(isset($summary))
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-info"></i>Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Status</small>
                            <h4 class="mb-0 text-{{ $summary['status'] == 'Active' ? 'success' : 'secondary' }}">
                                {{ $summary['status'] }}
                            </h4>
                        </div>
                        <i class="fas fa-{{ $summary['status'] == 'Active' ? 'check-circle' : 'times-circle' }} fa-2x text-{{ $summary['status'] == 'Active' ? 'success' : 'secondary' }}"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Days Remaining</small>
                            <h4 class="mb-0 text-primary">{{ $summary['remaining_days'] }}</h4>
                        </div>
                        <i class="fas fa-calendar-day fa-2x text-muted"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Items</small>
                            <h4 class="mb-0">{{ $summary['total_items'] }}</h4>
                        </div>
                        <i class="fas fa-list fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-info"></i>Contract Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Status</small>
                            @if($contract->date_end >= now())
                            <h4 class="mb-0 text-success">Active</h4>
                            @else
                            <h4 class="mb-0 text-secondary">Expired</h4>
                            @endif
                        </div>
                        <i class="fas fa-{{ $contract->date_end >= now() ? 'check-circle' : 'times-circle' }} fa-2x text-{{ $contract->date_end >= now() ? 'success' : 'secondary' }}"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Days Remaining</small>
                            <h4 class="mb-0 text-primary">{{ $remaining }}</h4>
                        </div>
                        <i class="fas fa-calendar-day fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions Card -->
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-cog me-2 text-warning"></i>Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('contract.edit', $contract->id_md_cont) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Contract
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteContract()">
                            <i class="fas fa-trash me-2"></i>Delete Contract
                        </button>
                        <a href="{{ route('contract.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list me-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('contract.destroy', $contract->id_md_cont) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .contractPage .card {
        border: none;
        border-radius: 10px;
    }

    .contractPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .contractPage .badge {
        border-radius: 6px;
        font-weight: 500;
    }

    .contractPage .alert {
        border-radius: 8px;
    }

    .contractPage h3 {
        color: var(--primary-green);
    }

    .contractPage .text-primary {
        color: var(--primary-green) !important;
    }

    .contractPage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .contractPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .contractPage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .contractPage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }

    .contractPage .border-bottom {
        border-color: #e2e8f0 !important;
    }

    .contractPage .progress {
        border-radius: 8px;
        overflow: hidden;
    }

    .contractPage .progress-bar {
        border-radius: 8px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteContract() {
    Swal.fire({
        title: 'Delete Contract?',
        html: `Contract <strong>{{ $contract->contract }}</strong> will be permanently deleted.<br><small class="text-muted">This action cannot be undone.</small>`,
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