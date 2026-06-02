@extends('layouts.app')

@section('title', 'Edit Area')

@section('content')
<div class="container-fluid areaPage">
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Area</span>
                    <a href="{{ route('area.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('area.update', $area->id_md_area) }}" method="POST" id="areaForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Area Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="area"
                                   class="form-control form-control-lg @error('area') is-invalid @enderror"
                                   value="{{ old('area', $area->area) }}"
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
                                      rows="4">{{ old('note', $area->note) }}</textarea>
                            @error('note')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Metadata -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">
                                    <i class="fas fa-clock me-1"></i>Record Information
                                </h6>
                                <div class="row text-sm">
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted">Created:</small><br>
                                        <small><i class="fas fa-calendar-plus me-1"></i>{{ $area->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted">Last Updated:</small><br>
                                        <small><i class="fas fa-calendar-check me-1"></i>{{ $area->updated_at->format('d M Y, H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('area.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Update Area
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

    .areaPage .alert {
        border-radius: 8px;
    }

    .areaPage .text-sm small {
        font-size: 0.875rem;
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

    // Highlight changed fields
    $('input, textarea').on('change', function() {
        $(this).addClass('border-warning');
        setTimeout(() => {
            $(this).removeClass('border-warning');
        }, 1000);
    });
});
</script>
@endpush