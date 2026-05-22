@extends('layouts.app')

@section('title', 'Other Detail')

@section('content')
<div class="container-fluid otherPage">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-12">
            <!-- Other Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Other Information</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('other.edit', $other->id_md_other) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('other.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Other Type -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Other Type</label>
                        <h3 class="mb-0 text-primary">
                            <i class="fas fa-box me-2"></i>{{ $other->other }}
                        </h3>
                    </div>

                    <hr>

                    <!-- Details Grid -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Code</label>
                            <div>
                                <span class="badge bg-info text-dark fs-6 px-3 py-2">
                                    {{ $other->code ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Note Section -->
                    @if($other->note)
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Note</label>
                        <div class="alert alert-light border-start border-4 border-warning mb-0">
                            <i class="fas fa-sticky-note text-warning me-2"></i>
                            {{ $other->note }}
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
                            <span class="ms-4">{{ $other->created_at->format('d F Y, H:i') }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-check me-2 text-success"></i>
                            <strong>Last Updated:</strong><br>
                            <span class="ms-4">{{ $other->updated_at->format('d F Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('other.destroy', $other->id_md_other) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .otherPage .card {
        border: none;
        border-radius: 10px;
    }

    .otherPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .otherPage .badge {
        border-radius: 6px;
        font-weight: 500;
    }

    .otherPage .alert {
        border-radius: 8px;
    }

    .otherPage h3 {
        color: var(--primary-green);
    }

    .otherPage .text-primary {
        color: var(--primary-green) !important;
    }

    .otherPage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .otherPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .otherPage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .otherPage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }

    .otherPage .border-bottom {
        border-color: #e2e8f0 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteOther() {
    Swal.fire({
        title: 'Delete Other?',
        html: `Other <strong>{{ $other->other }}</strong> will be permanently deleted.<br><small class="text-muted">This action cannot be undone.</small>`,
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