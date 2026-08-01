@extends('layouts.app')

@section('title', 'Edit Parent Chart of Account')

@section('content')
<div class="container-fluid parentCoaPage">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Parent COA</span>
                    <a href="{{ route('parent-coa.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('parent-coa.update', $parentCoa->id) }}" method="POST" id="parentCoaForm">
                        @csrf
                        @method('PUT')

                        <!-- Tipe Akun -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Tipe Akun <span class="text-danger">*</span>
                            </label>
                            <select name="id_md_cost_type"
                                    class="form-select @error('id_md_cost_type') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Tipe Akun --</option>
                                @foreach($costTypes as $costType)
                                <option value="{{ $costType->id_md_cost_type }}"
                                    {{ (old('id_md_cost_type', $parentCoa->id_md_cost_type)) == $costType->id_md_cost_type ? 'selected' : '' }}>
                                    {{ $costType->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_md_cost_type')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Kode Perkiraan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Kode Perkiraan <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="kode_perkiraan"
                                   class="form-control @error('kode_perkiraan') is-invalid @enderror"
                                   value="{{ old('kode_perkiraan', $parentCoa->kode_perkiraan) }}"
                                   placeholder="e.g. 1000"
                                   required>
                            @error('kode_perkiraan')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Nama Akun -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nama Akun <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="nama"
                                   class="form-control @error('nama') is-invalid @enderror"
                                   value="{{ old('nama', $parentCoa->nama) }}"
                                   placeholder="e.g. Kas / Bank"
                                   required>
                            @error('nama')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('parent-coa.index') }}" class="btn btn-secondary">
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
    .parentCoaPage .card { border: none; border-radius: 10px; }
    .parentCoaPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .parentCoaPage .form-control:focus, .parentCoaPage .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }
    .parentCoaPage .btn { padding: 0.5rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
    .parentCoaPage .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .parentCoaPage .btn-success { background-color: var(--primary-green); border-color: var(--primary-green); }
    .parentCoaPage .btn-success:hover { background-color: var(--dark-green); border-color: var(--dark-green); }
</style>
@endpush