@extends('layouts.app')
@section('title', 'Company Profile')

@section('content')
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header text-black d-flex justify-content-between align-items-center"
            style="background-color: #d1fae5">
            <span class="fw-bold">
                <i class="fas fa-building me-2"></i>Company Profile
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header fw-bold" style="background-color:#d1fae5">
            <i class="fas fa-id-card me-2"></i>Informasi Perusahaan
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('company-profile.update') }}">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Nama Perusahaan
                        </label>
                        <input type="text"
                               name="nama_perusahaan"
                               class="form-control @error('nama_perusahaan') is-invalid @enderror"
                               value="{{ old('nama_perusahaan', $profile->nama_perusahaan) }}"
                               placeholder="Contoh: PT. ORINDO BANGUN SAMUDERA">
                        @error('nama_perusahaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Nama Direktur
                        </label>
                        <input type="text"
                               name="nama_direktur"
                               class="form-control @error('nama_direktur') is-invalid @enderror"
                               value="{{ old('nama_direktur', $profile->nama_direktur) }}"
                               placeholder="Contoh: John Doe">
                        @error('nama_direktur')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Jabatan
                        </label>
                        <input type="text"
                               name="jabatan"
                               class="form-control @error('jabatan') is-invalid @enderror"
                               value="{{ old('jabatan', $profile->jabatan) }}"
                               placeholder="Contoh: Direktur Utama">
                        @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Alamat Perusahaan
                        </label>
                        <textarea name="alamat"
                                  rows="3"
                                  class="form-control @error('alamat') is-invalid @enderror"
                                  placeholder="Alamat lengkap perusahaan">{{ old('alamat', $profile->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- TTD Kwitansi --}}
                <div class="row mb-3">
                    <div class="col-12 mb-2">
                        <h6 class="fw-bold text-secondary border-bottom pb-1">TTD Kwitansi</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama TTD</label>
                        <input type="text"
                               name="nama_ttd_kwitansi"
                               class="form-control @error('nama_ttd_kwitansi') is-invalid @enderror"
                               value="{{ old('nama_ttd_kwitansi', $profile->nama_ttd_kwitansi) }}"
                               placeholder="Nama yang muncul di TTD kwitansi">
                        @error('nama_ttd_kwitansi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jabatan TTD</label>
                        <input type="text"
                               name="jabatan_ttd_kwitansi"
                               class="form-control @error('jabatan_ttd_kwitansi') is-invalid @enderror"
                               value="{{ old('jabatan_ttd_kwitansi', $profile->jabatan_ttd_kwitansi) }}"
                               placeholder="Jabatan yang muncul di TTD kwitansi">
                        @error('jabatan_ttd_kwitansi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
