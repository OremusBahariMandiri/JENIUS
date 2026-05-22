@extends('layouts.app')

@section('title', 'Vessel Detail')

@section('content')
<div class="container-fluid vesselPage">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-12">
            <!-- Vessel Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Vessel Information</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('vessel.edit', $vessel->id_md_vessel) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('vessel.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Vessel Name -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Vessel Name</label>
                        <h3 class="mb-0 text-primary">
                            <i class="fas fa-ship me-2"></i>{{ $vessel->vessel_name }}
                        </h3>
                    </div>

                    <hr>

                    <!-- Details Grid -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Vessel Type</label>
                            <div>
                                <span class="badge bg-info text-dark fs-6 px-3 py-2">
                                    {{ $vessel->vessel_type ?? '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">No. IMO</label>
                            <p class="mb-0 fw-bold">{{ $vessel->no_imo ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">No. MMSI</label>
                            <p class="mb-0 fw-bold">{{ $vessel->no_mmsi ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Call Sign</label>
                            <p class="mb-0 fw-bold">{{ $vessel->call_sign ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Flag</label>
                            <p class="mb-0 fw-bold">{{ $vessel->flag ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Gross Tonnage</label>
                            <p class="mb-0">{{ $vessel->gt ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Summer DWT</label>
                            <p class="mb-0">{{ $vessel->dwt ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Year Built</label>
                            <p class="mb-0">{{ $vessel->year_built ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Breadth Extreme</label>
                            <p class="mb-0">{{ $vessel->breadth ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Length Overall</label>
                            <p class="mb-0">{{ $vessel->loa ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Principal / GA</label>
                            <p class="mb-0">{{ $vessel->ga ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Ship Particular Section -->
                    @if($vessel->ship_particular)
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Ship Particular</label>
                        <div class="alert alert-light border-start border-4 border-warning mb-0">
                            <i class="fas fa-file-alt text-warning me-2"></i>
                            {{ $vessel->ship_particular }}
                        </div>
                    </div>
                    @else
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Ship Particular</label>
                        <div class="alert alert-light mb-0">
                            <i class="fas fa-info-circle text-muted me-2"></i>
                            <em class="text-muted">No ship particular available</em>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <!-- Timestamps -->
                    <div class="row text-muted">
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-plus me-2 text-primary"></i>
                            <strong>Created:</strong><br>
                            <span class="ms-4">{{ $vessel->created_at->format('d F Y, H:i') }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-check me-2 text-success"></i>
                            <strong>Last Updated:</strong><br>
                            <span class="ms-4">{{ $vessel->updated_at->format('d F Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('vessel.destroy', $vessel->id_md_vessel) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .vesselPage .card {
        border: none;
        border-radius: 10px;
    }

    .vesselPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .vesselPage .badge {
        border-radius: 6px;
        font-weight: 500;
    }

    .vesselPage .alert {
        border-radius: 8px;
    }

    .vesselPage h3 {
        color: var(--primary-green);
    }

    .vesselPage .text-primary {
        color: var(--primary-green) !important;
    }

    .vesselPage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .vesselPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .vesselPage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .vesselPage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }

    .vesselPage .border-bottom {
        border-color: #e2e8f0 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteVessel() {
    Swal.fire({
        title: 'Delete Vessel?',
        html: `Vessel <strong>{{ $vessel->vessel_name }}</strong> will be permanently deleted.<br><small class="text-muted">This action cannot be undone.</small>`,
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