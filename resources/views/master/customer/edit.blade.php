@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="container-fluid customerPage">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Customer</span>
                    <a href="{{ route('customer.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('customer.update', $customer->id_md_cust) }}" method="POST" id="customerForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Nama Customer <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="customer"
                                           class="form-control @error('customer') is-invalid @enderror"
                                           value="{{ old('customer', $customer->customer) }}"
                                           required>
                                    @error('customer')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Alamat</label>
                                    <textarea name="address"
                                              class="form-control @error('address') is-invalid @enderror"
                                              rows="3">{{ old('address', $customer->address) }}</textarea>
                                    @error('address')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Telepon</label>
                                            <input type="text"
                                                   name="phone"
                                                   class="form-control @error('phone') is-invalid @enderror"
                                                   value="{{ old('phone', $customer->phone) }}">
                                            @error('phone')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Email</label>
                                            <input type="email"
                                                   name="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   value="{{ old('email', $customer->email) }}">
                                            @error('email')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Website</label>
                                            <input type="text"
                                                   name="website"
                                                   class="form-control @error('website') is-invalid @enderror"
                                                   value="{{ old('website', $customer->website) }}">
                                            @error('website')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">NPWP</label>
                                            <input type="text"
                                                   name="npwp"
                                                   class="form-control @error('npwp') is-invalid @enderror"
                                                   value="{{ old('npwp', $customer->npwp) }}">
                                            @error('npwp')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Catatan</label>
                                    <textarea name="note"
                                              class="form-control @error('note') is-invalid @enderror"
                                              rows="3">{{ old('note', $customer->note) }}</textarea>
                                    @error('note')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Metadata -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">
                                    <i class="fas fa-clock me-1"></i>Informasi Record
                                </h6>
                                <div class="row text-sm">
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted">Dibuat:</small><br>
                                        <small><i class="fas fa-calendar-plus me-1"></i>{{ $customer->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted">Terakhir Diupdate:</small><br>
                                        <small><i class="fas fa-calendar-check me-1"></i>{{ $customer->updated_at->format('d M Y, H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('customer.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Batal
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
    .customerPage .card {
        border: none;
        border-radius: 10px;
    }

    .customerPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .customerPage .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }

    .customerPage .form-label {
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .customerPage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .customerPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .customerPage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .customerPage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }

    .customerPage .alert {
        border-radius: 8px;
    }

    .customerPage .text-sm small {
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