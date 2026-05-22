@extends('layouts.app')

@section('title', 'Invoice Detail')

@section('content')
<div class="container-fluid invoicePage">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Invoice Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Invoice Information</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('invoice.edit', $invoice->id_md_invoice) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('invoice.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Category Name -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Invoice Category</label>
                        <h3 class="mb-0 text-primary">
                            <i class="fas fa-file-invoice me-2"></i>{{ $invoice->invoice_ctg }}
                        </h3>
                    </div>

                    <hr>

                    <!-- Details Grid -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Invoice ID</label>
                            <div>
                                <span class="badge bg-secondary fs-6 px-3 py-2">
                                    #{{ $invoice->id_md_invoice }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Invoice Item</label>
                            <div>
                                <span class="badge bg-info text-dark fs-6 px-3 py-2">
                                    {{ $invoice->invoice_typ }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Note Section -->
                    @if($invoice->note)
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Note</label>
                        <div class="alert alert-light border-start border-4 border-warning mb-0">
                            <i class="fas fa-sticky-note text-warning me-2"></i>
                            {{ $invoice->note }}
                        </div>
                    </div>
                    @else
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Note</label>
                        <div class="alert alert-light mb-0">
                            <i class="fas fa-info-circle text-muted me-2"></i>
                            <em class="text-muted">No notes available</em>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <!-- Timestamps -->
                    <div class="row text-muted">
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-plus me-2 text-primary"></i>
                            <strong>Created:</strong><br>
                            <span class="ms-4">{{ $invoice->created_at->format('d F Y, H:i') }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-check me-2 text-success"></i>
                            <strong>Last Updated:</strong><br>
                            <span class="ms-4">{{ $invoice->updated_at->format('d F Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Contract Items Section -->
            @if(isset($summary) || isset($invoice->joContractItems))
            <div class="card shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Related Contract Items</h5>
                    @if(isset($invoice->joContractItems))
                    <span class="badge bg-light text-dark">{{ $invoice->joContractItems->count() }} items</span>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($invoice->joContractItems) && $invoice->joContractItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Contract No.</th>
                                    <th>Customer</th>
                                    <th>Area</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->joContractItems as $item)
                                @if($item->joContract)
                                <tr>
                                    <td><strong>{{ $item->joContract->no_jo_cont ?? '-' }}</strong></td>
                                    <td>
                                        @if($item->joContract->contract && $item->joContract->contract->customer)
                                        {{ $item->joContract->contract->customer->customer }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->joContract->area)
                                        {{ $item->joContract->area->area }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->joContract->department)
                                        {{ $item->joContract->department->department }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Related Items</h5>
                        <p class="text-muted mb-0">There are no contract items associated with this invoice yet.</p>
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
                    <h6 class="mb-0"><i class="fas fa-file-invoice me-2 text-success"></i>Quick Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small text-uppercase mb-2">Invoice ID</label>
                        <h4 class="mb-0 text-primary">#{{ $invoice->id_md_invoice }}</h4>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small text-uppercase mb-2">Category</label>
                        <h5 class="mb-0">{{ $invoice->invoice_ctg }}</h5>
                    </div>
                    <div>
                        <label class="text-muted small text-uppercase mb-2">Item</label>
                        <span class="badge bg-info text-dark fs-6">{{ $invoice->invoice_typ }}</span>
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
                            <small class="text-muted">Total Items</small>
                            <h4 class="mb-0 text-primary">{{ $summary['total_items'] }}</h4>
                        </div>
                        <i class="fas fa-list fa-2x text-muted"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Contracts</small>
                            <h4 class="mb-0 text-success">{{ $summary['total_contracts'] }}</h4>
                        </div>
                        <i class="fas fa-file-contract fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
            @elseif(isset($invoice->joContractItems))
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-info"></i>Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Related Items</small>
                            <h4 class="mb-0 text-primary">{{ $invoice->joContractItems->count() }}</h4>
                        </div>
                        <i class="fas fa-list fa-2x text-muted"></i>
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
                        <a href="{{ route('invoice.edit', $invoice->id_md_invoice) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Invoice
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteInvoice()">
                            <i class="fas fa-trash me-2"></i>Delete Invoice
                        </button>
                        <a href="{{ route('invoice.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list me-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('invoice.destroy', $invoice->id_md_invoice) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .invoicePage .card {
        border: none;
        border-radius: 10px;
    }

    .invoicePage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .invoicePage .badge {
        border-radius: 6px;
        font-weight: 500;
    }

    .invoicePage .alert {
        border-radius: 8px;
    }

    .invoicePage h3 {
        color: var(--primary-green);
    }

    .invoicePage .text-primary {
        color: var(--primary-green) !important;
    }

    .invoicePage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .invoicePage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .invoicePage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .invoicePage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }

    .invoicePage .border-bottom {
        border-color: #e2e8f0 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteInvoice() {
    Swal.fire({
        title: 'Delete Invoice?',
        html: `Invoice <strong>{{ $invoice->invoice_ctg }}</strong> will be permanently deleted.<br><small class="text-muted">This action cannot be undone.</small>`,
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
@endsection