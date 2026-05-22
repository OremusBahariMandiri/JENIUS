@extends('layouts.app')

@section('title', 'Add Area')

@section('content')
<div class="container-fluid areaPage">
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add New Area</span>
                    <a href="{{ route('area.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('area.store') }}" method="POST" id="areaForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Area Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="area"
                                   class="form-control form-control-lg @error('area') is-invalid @enderror"
                                   value="{{ old('area') }}"
                                   required>
                            @error('area')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Note</label>
                            <textarea name="note"
                                      class="form-control @error('note') is-invalid @enderror"
                                      rows="4">{{ old('note') }}</textarea>
                            @error('note')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('area.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Save Area
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
    .areaPage .card {
        border: none;
        border-radius: 10px;
    }

    .areaPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .areaPage .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }

    .areaPage .form-control-lg {
        font-size: 1.1rem;
        padding: 0.75rem 1rem;
    }

    .areaPage .form-label {
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .areaPage .form-text {
        color: var(--text-gray);
        font-size: 0.875rem;
    }

    .areaPage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .areaPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .areaPage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .areaPage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation enhancement
    $('#areaForm').on('submit', function(e) {
        const areaName = $('input[name="area"]').val().trim();

        if (areaName.length < 2) {
            e.preventDefault();
            alert('Area name must be at least 2 characters long');
            $('input[name="area"]').focus();
            return false;
        }
    });

    // Auto-focus on area name input
    $('input[name="area"]').focus();
});
</script>
@endpush