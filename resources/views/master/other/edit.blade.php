@extends('layouts.app')

@section('title', 'Edit Other')

@section('content')
<div class="container-fluid otherPage">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Other</span>
                    <a href="{{ route('other.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('other.update', $other->id_md_other) }}" method="POST" id="otherForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-lg-12">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Other Type <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="other"
                                           class="form-control @error('other') is-invalid @enderror"
                                           value="{{ old('other', $other->other) }}"
                                           required>
                                    @error('other')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Note</label>
                                    <textarea name="note"
                                              class="form-control @error('note') is-invalid @enderror"
                                              rows="4">{{ old('note', $other->note) }}</textarea>
                                    @error('note')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('other.index') }}" class="btn btn-secondary">
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
    }

    .otherPage .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }

    .otherPage .form-label {
        color: var(--text-dark);
        margin-bottom: 0.5rem;
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

    .otherPage .alert {
        border-radius: 8px;
    }

    .otherPage .text-sm small {
        font-size: 0.875rem;
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
});
</script>
@endpush