@extends('layouts.app')

@section('title', 'Invoice Detail')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">Invoice Detail</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom mb-0">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">Invoice</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('invoice.edit', $invoice->id_md_invoice) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('invoice.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Invoice Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Invoice Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Invoice Code</label>
                            <p class="mb-0"><span class="badge bg-primary fs-6">{{ $invoice->code }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Invoice Category</label>
                            <p class="mb-0 fw-bold">{{ $invoice->invoice_ctg }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Invoice Type</label>
                            <p class="mb-0"><span class="badge bg-info text-dark fs-6">{{ $invoice->invoice_typ }}</span></p>
                        </div>
                    </div>

                    @if($invoice->note)
                    <div class="mb-3">
                        <label class="text-muted small">Note</label>
                        <div class="alert alert-light mb-0">{{ $invoice->note }}</div>
                    </div>
                    @endif

                    <hr>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <i class="fas fa-calendar-plus me-2"></i>Created: {{ $invoice->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-calendar-check me-2"></i>Updated: {{ $invoice->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Contract Items Section -->
            @if(isset($summary))
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Related Contract Items</h5>
                </div>
                <div class="card-body">
                    @if($invoice->joContractItems->count() > 0)
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
                                    <td>{{ $item->joContract->no_jo_cont ?? '-' }}</td>
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
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No related contract items for this invoice</p>
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

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Contracts</small>
                            <h3 class="mb-0 text-primary">{{ $summary['total_contracts'] }}</h3>
                        </div>
                        <div class="stats-icon" style="background: #bfdbfe; color: #3b82f6;">
                            <i class="fas fa-file-contract"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Invoice Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Category</small>
                        <span class="badge bg-secondary">{{ $invoice->invoice_ctg }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block">Type</small>
                        <span class="badge bg-info text-dark">{{ $invoice->invoice_typ }}</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('invoice.edit', $invoice->id_md_invoice) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Invoice
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteInvoice({{ $invoice->id_md_invoice }})">
                            <i class="fas fa-trash me-2"></i>Delete Invoice
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
function deleteInvoice(id) {
    if (confirm('Are you sure you want to delete this invoice?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/master/invoice/${id}`;
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