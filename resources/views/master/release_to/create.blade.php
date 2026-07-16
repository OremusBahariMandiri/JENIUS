@extends('layouts.app')

@section('title', 'Add Release To')

@section('content')
<div class="container-fluid releasePage">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add Release To</span>
                    <a href="{{ route('release_to.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('release_to.store') }}" method="POST" id="releaseForm">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Release Name <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_release"
                                           class="form-control @error('nama_release') is-invalid @enderror"
                                           value="{{ old('nama_release') }}" placeholder="Release name" required>
                                    @error('nama_release')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Destination</label>
                                    <input type="text" name="tujuan"
                                           class="form-control @error('tujuan') is-invalid @enderror"
                                           value="{{ old('tujuan') }}" list="tujuanList" placeholder="Destination">
                                    @error('tujuan')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                    @if(isset($tujuans) && $tujuans->count() > 0)
                                    <datalist id="tujuanList">
                                        @foreach($tujuans as $tujuan)<option value="{{ $tujuan }}">@endforeach
                                    </datalist>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Account No.</label>
                                    <input type="text" name="rekening"
                                           class="form-control @error('rekening') is-invalid @enderror"
                                           value="{{ old('rekening') }}" placeholder="Account number">
                                    @error('rekening')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" name="no_telepon"
                                           class="form-control @error('no_telepon') is-invalid @enderror"
                                           value="{{ old('no_telepon') }}" placeholder="+62...">
                                    @error('no_telepon')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Address</label>
                                    <textarea name="alamat" rows="2"
                                              class="form-control @error('alamat') is-invalid @enderror"
                                              placeholder="Full address">{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Note</label>
                                    <textarea name="note" rows="3"
                                              class="form-control @error('note') is-invalid @enderror"
                                              placeholder="Additional notes">{{ old('note') }}</textarea>
                                    @error('note')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('release_to.index') }}" class="btn btn-secondary">
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
    .releasePage .card { border: none; border-radius: 10px; }
    .releasePage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .releasePage .form-control:focus { border-color: var(--primary-green); box-shadow: 0 0 0 0.2rem rgba(16,185,129,0.25); }
    .releasePage .btn { padding: 0.5rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
    .releasePage .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .releasePage .btn-success { background-color: var(--primary-green); border-color: var(--primary-green); }
    .releasePage .btn-success:hover { background-color: var(--dark-green); border-color: var(--dark-green); }
</style>
@endpush

@push('scripts')
<script>$(document).ready(function() { $('input[name="nama_release"]').focus(); });</script>
@endpush