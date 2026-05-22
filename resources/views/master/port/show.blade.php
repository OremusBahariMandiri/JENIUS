@extends('layouts.app')

@section('title', 'Port Detail')

@section('content')
<div class="container-fluid portPage">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-12">
            <!-- Port Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Port Information</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('port.edit', $port->id_md_port) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('port.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Port Name -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Port Name</label>
                        <h3 class="mb-0 text-primary">
                            <i class="fas fa-anchor me-2"></i>{{ $port->name_port }}
                        </h3>
                    </div>

                    <hr>

                    <!-- Details Grid -->
                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Address</label>
                            <p class="mb-0">{{ $port->alamat ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small text-uppercase mb-2">City</label>
                            <p class="mb-0 fw-bold">{{ $port->kota ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Province/State</label>
                            <p class="mb-0 fw-bold">{{ $port->provinsi ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Country</label>
                            <p class="mb-0 fw-bold">{{ $port->negara ?? '-' }}</p>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3"><i class="fas fa-info-circle me-2 text-info"></i>Port Specifications</h5>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Port Type</label>
                            <p class="mb-0">{{ $port->port_type ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Operator Port</label>
                            <p class="mb-0">{{ $port->operator_port ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Max Draft</label>
                            <p class="mb-0">{{ $port->draft ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Max LOA (Length Overall)</label>
                            <p class="mb-0">{{ $port->panjang_kapal ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Max Beam</label>
                            <p class="mb-0">{{ $port->lebar_kapal ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Max DWT</label>
                            <p class="mb-0">{{ $port->dwt ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Berth Length</label>
                            <p class="mb-0">{{ $port->panjang_dermaga ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Tidal Restriction</label>
                            <p class="mb-0">{{ $port->pasang_surut ?? '-' }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Working Hours</label>
                            <p class="mb-0">{{ $port->jam_ops ?? '-' }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Timestamps -->
                    <div class="row text-muted">
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-plus me-2 text-primary"></i>
                            <strong>Created:</strong><br>
                            <span class="ms-4">{{ $port->created_at->format('d F Y, H:i') }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-check me-2 text-success"></i>
                            <strong>Last Updated:</strong><br>
                            <span class="ms-4">{{ $port->updated_at->format('d F Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('port.destroy', $port->id_md_port) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .portPage .card {
        border: none;
        border-radius: 10px;
    }

    .portPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .portPage .badge {
        border-radius: 6px;
        font-weight: 500;
    }

    .portPage .alert {
        border-radius: 8px;
    }

    .portPage h3 {
        color: var(--primary-green);
    }

    .portPage .text-primary {
        color: var(--primary-green) !important;
    }

    .portPage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .portPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .portPage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .portPage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }

    .portPage .border-bottom {
        border-color: #e2e8f0 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deletePort() {
    Swal.fire({
        title: 'Delete Port?',
        html: `Port <strong>{{ $port->name_port }}</strong> will be permanently deleted.<br><small class="text-muted">This action cannot be undone.</small>`,
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