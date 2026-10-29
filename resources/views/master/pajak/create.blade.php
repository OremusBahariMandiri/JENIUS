@extends('layouts.app')

@section('title', 'Tambah Data Pajak')

@section('content')
<div class="container-fluid pajakPage">
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Data Pajak</span>
                    <a href="{{ route('pajak.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('pajak.store') }}" method="POST" id="pajakForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nama Pajak <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_pajak"
                                   class="form-control @error('nama_pajak') is-invalid @enderror"
                                   value="{{ old('nama_pajak') }}"
                                   placeholder="Contoh: Pajak Pertambahan Nilai"
                                   required>
                            @error('nama_pajak')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Kode Pajak
                            </label>
                            <input type="text" name="kode_pajak"
                                   class="form-control @error('kode_pajak') is-invalid @enderror"
                                   value="{{ old('kode_pajak') }}"
                                   placeholder="Contoh: PPN, PPh21, PPh23">
                            <div class="form-text">Kode singkat untuk identifikasi pajak.</div>
                            @error('kode_pajak')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Persentase (%) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group" style="max-width: 200px;">
                                <input type="number" name="persentase"
                                       class="form-control @error('persentase') is-invalid @enderror"
                                       value="{{ old('persentase') }}"
                                       placeholder="0.00"
                                       min="0" max="100" step="0.01"
                                       required>
                                <span class="input-group-text">%</span>
                                @error('persentase')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Masukkan nilai persentase, misal <strong>11</strong> untuk 11%.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan"
                                      class="form-control @error('keterangan') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                       id="is_active" value="1"
                                       {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Status Aktif
                                </label>
                            </div>
                            <div class="form-text">Pajak yang tidak aktif tidak akan muncul di pilihan JO.</div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('pajak.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Simpan
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
    .pajakPage .card { border: none; border-radius: 10px; }
    .pajakPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .pajakPage .form-control:focus, .pajakPage .form-check-input:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }
    .pajakPage .form-check-input:checked { background-color: var(--primary-green); border-color: var(--primary-green); }
    .pajakPage .btn { padding: .5rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all .2s; }
    .pajakPage .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,.1); }
    .pajakPage .btn-success { background-color: var(--primary-green); border-color: var(--primary-green); }
    .pajakPage .btn-success:hover { background-color: var(--dark-green); border-color: var(--dark-green); }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    $('input[name="nama_pajak"]').focus();
});
</script>
@endpush
