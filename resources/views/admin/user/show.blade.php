@extends('layouts.app')

@section('title', 'User Detail')

@section('content')
<div class="container-fluid userPage">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-12">
            <!-- User Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-user me-2"></i>User Information</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('user.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- User Name -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Full Name</label>
                        <h3 class="mb-0 text-primary">
                            <i class="fas fa-user-circle me-2"></i>{{ $user->full_name }}
                        </h3>
                    </div>

                    <hr>

                    <!-- Details Grid -->
                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Employee ID Number</label>
                            <div>
                                <span class="fs-6">{{ $user->employee_id_number }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Department</label>
                            <div>
                                <span class="fs-6">{{ $user->department }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Position</label>
                            <div>
                                <span class="badge bg-info text-dark fs-6 px-3 py-2">
                                    {{ $user->position }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Work Location</label>
                        <div>
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                            <span class="fs-6">{{ $user->work_location }}</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Role</label>
                        <div>
                            @if($user->is_admin)
                            <span class="badge bg-danger fs-6 px-3 py-2">
                                <i class="fas fa-crown me-1"></i>Administrator
                            </span>
                            @else
                            <span class="badge bg-secondary fs-6 px-3 py-2">
                                <i class="fas fa-user me-1"></i>User
                            </span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Timestamps -->
                    <div class="row text-muted">
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-plus me-2 text-primary"></i>
                            <strong>Created:</strong><br>
                            <span class="ms-4">{{ $summary['created_at'] }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-check me-2 text-success"></i>
                            <strong>Last Updated:</strong><br>
                            <span class="ms-4">{{ $summary['updated_at'] }}</span>
                        </div>
                    </div>

                    @if($user->created_by || $user->updated_by)
                    <hr>
                    <div class="row text-muted">
                        @if($user->created_by)
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-user-plus me-2 text-info"></i>
                            <strong>Created By:</strong><br>
                            <span class="ms-4">{{ $user->created_by }}</span>
                        </div>
                        @endif
                        @if($user->updated_by)
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-user-edit me-2 text-warning"></i>
                            <strong>Updated By:</strong><br>
                            <span class="ms-4">{{ $user->updated_by }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('user.destroy', $user->id) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<style>
    .userPage .card {
        border: none;
        border-radius: 10px;
    }

    .userPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .userPage .badge {
        border-radius: 6px;
        font-weight: 500;
    }

    .userPage h3 {
        color: #10b981;
    }

    .userPage .text-primary {
        color: #10b981 !important;
    }

    .userPage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .userPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .userPage .btn-success {
        background-color: #10b981;
        border-color: #10b981;
    }

    .userPage .btn-success:hover {
        background-color: #059669;
        border-color: #059669;
    }

    .userPage .border-bottom {
        border-color: #e2e8f0 !important;
    }

    .userPage .avatar-circle {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteUser() {
    Swal.fire({
        title: 'Delete User?',
        html: `User <strong>{{ $user->full_name }}</strong> will be permanently deleted.<br><small class="text-muted">This action cannot be undone.</small>`,
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