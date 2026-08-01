@extends('layouts.app')

@section('title', 'Cost Type Detail')

@section('content')
<div class="container-fluid costTypePage">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Cost Type Information</span>
                    <div class="d-flex gap-2">
                        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('cost_type', 'ubah')))
                        <a href="{{ route('cost-type.edit', $costType->id_md_cost_type) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @endif
                        <a href="{{ route('cost-type.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Name -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Name</label>
                        <h3 class="mb-0 text-primary">
                            <i class="fas fa-tags me-2"></i>{{ $costType->name }}
                        </h3>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Note</label>
                            @if($costType->note)
                            <div class="alert alert-light border-start border-4 border-warning mb-0">
                                <i class="fas fa-sticky-note text-warning me-2"></i>
                                {{ $costType->note }}
                            </div>
                            @else
                            <div class="alert alert-light mb-0">
                                <i class="fas fa-info-circle text-muted me-2"></i>
                                <em class="text-muted">No note available</em>
                            </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Timestamps -->
                    <div class="row text-muted">
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-plus me-2 text-primary"></i>
                            <strong>Created:</strong><br>
                            <span class="ms-4">{{ $costType->created_at->format('d F Y, H:i') }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-check me-2 text-success"></i>
                            <strong>Last Updated:</strong><br>
                            <span class="ms-4">{{ $costType->updated_at->format('d F Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('cost-type.destroy', $costType->id_md_cost_type) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .costTypePage .card {
        border: none;
        border-radius: 10px;
    }

    .costTypePage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .costTypePage .alert {
        border-radius: 8px;
    }

    .costTypePage h3 {
        color: var(--primary-green);
    }

    .costTypePage .text-primary {
        color: var(--primary-green) !important;
    }

    .costTypePage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .costTypePage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .costTypePage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .costTypePage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteCostType() {
    Swal.fire({
        title: 'Delete Cost Type?',
        html: `Cost Type <strong>{{ $costType->name }}</strong> will be permanently deleted.<br><small class="text-muted">This action cannot be undone.</small>`,
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