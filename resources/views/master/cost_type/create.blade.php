@extends('layouts.app')

@section('title', 'Add Cost Type')

@section('content')
<div class="container-fluid costTypePage">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add New Cost Type</span>
                    <a href="{{ route('cost-type.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('cost-type.store') }}" method="POST" id="costTypeForm">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Enter cost type name"
                                   required>
                            @error('name')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Note -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Note</label>
                            <textarea name="note"
                                      class="form-control @error('note') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Enter note (optional)">{{ old('note') }}</textarea>
                            @error('note')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('cost-type.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
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
    }

    .costTypePage .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
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
<script>
$(document).ready(function() {
    $('input[name="name"]').focus();
});
</script>
@endpush