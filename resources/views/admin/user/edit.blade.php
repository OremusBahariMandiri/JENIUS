@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid userPage">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-user-edit me-2"></i>Edit User</span>
                    <a href="{{ route('user.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('user.update', $user->id) }}" method="POST" id="userForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Employee ID Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="employee_id_number"
                                           class="form-control @error('employee_id_number') is-invalid @enderror"
                                           value="{{ old('employee_id_number', $user->employee_id_number) }}"
                                           required>
                                    @error('employee_id_number')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                    <small class="text-muted">Unique employee identification number</small>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="full_name"
                                           class="form-control @error('full_name') is-invalid @enderror"
                                           value="{{ old('full_name', $user->full_name) }}"
                                           required>
                                    @error('full_name')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Department <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="department"
                                           class="form-control @error('department') is-invalid @enderror"
                                           value="{{ old('department', $user->department) }}"
                                           list="departmentList"
                                           required>
                                    @error('department')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                    @if(isset($departments) && $departments->count() > 0)
                                    <datalist id="departmentList">
                                        @foreach($departments as $department)
                                        <option value="{{ $department }}">
                                        @endforeach
                                    </datalist>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Position <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="position"
                                           class="form-control @error('position') is-invalid @enderror"
                                           value="{{ old('position', $user->position) }}"
                                           list="positionList"
                                           required>
                                    @error('position')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                    @if(isset($positions) && $positions->count() > 0)
                                    <datalist id="positionList">
                                        @foreach($positions as $position)
                                        <option value="{{ $position }}">
                                        @endforeach
                                    </datalist>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Work Location <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="work_location"
                                   class="form-control @error('work_location') is-invalid @enderror"
                                   value="{{ old('work_location', $user->work_location) }}"
                                   list="locationList"
                                   required>
                            @error('work_location')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                            @if(isset($workLocations) && $workLocations->count() > 0)
                            <datalist id="locationList">
                                @foreach($workLocations as $location)
                                <option value="{{ $location }}">
                                @endforeach
                            </datalist>
                            @endif
                        </div>

                        <hr class="my-4">

                        <div class="alert alert-light border-start border-4 border-warning mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-lock text-warning me-2 mt-1"></i>
                                <div>
                                    <strong>Password Update (Optional)</strong>
                                    <p class="mb-0 small text-muted">Leave blank to keep current password. Fill both fields to change password.</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">New Password</label>
                                    <input type="password"
                                           name="password"
                                           class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                    <small class="text-muted">Minimum 6 characters</small>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Confirm New Password</label>
                                    <input type="password"
                                           name="password_confirmation"
                                           class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_admin"
                                       id="is_admin"
                                       {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_admin">
                                    Set as Administrator
                                </label>
                            </div>
                            <small class="text-muted">Administrators have full access to all features</small>
                        </div>


                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('user.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .userPage .card {
        border: none;
        border-radius: 10px;
    }

    .userPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .userPage .form-control:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }

    .userPage .form-label {
        color: #1f2937;
        margin-bottom: 0.5rem;
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

    .userPage .alert {
        border-radius: 8px;
    }

    .userPage .text-sm small {
        font-size: 0.875rem;
    }

    .userPage .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }

    .userPage .form-check-input:focus {
        border-color: #86efac;
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Highlight changed fields
    $('input, textarea').on('change', function() {
        $(this).addClass('border-warning');
        setTimeout(() => {
            $(this).removeClass('border-warning');
        }, 1000);
    });

    // Password validation
    $('input[name="password"], input[name="password_confirmation"]').on('input', function() {
        const password = $('input[name="password"]').val();
        const confirmation = $('input[name="password_confirmation"]').val();

        if (password && confirmation && password !== confirmation) {
            $('input[name="password_confirmation"]').addClass('is-invalid');
        } else {
            $('input[name="password_confirmation"]').removeClass('is-invalid');
        }
    });
});
</script>
@endpush
@endsection