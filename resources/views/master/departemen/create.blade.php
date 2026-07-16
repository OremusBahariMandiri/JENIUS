@extends('layouts.app')

@section('title', 'Add Department')

@section('content')
<div class="container-fluid departemenPage">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add Department</span>
                    <a href="{{ route('departemen.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('departemen.store') }}" method="POST" id="departemenForm">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Abbr.</label>
                                    <input type="text" name="skt_dep"
                                           class="form-control @error('skt_dep') is-invalid @enderror"
                                           value="{{ old('skt_dep') }}">
                                    @error('skt_dep')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Department Name <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_dep"
                                           class="form-control @error('nama_dep') is-invalid @enderror"
                                           value="{{ old('nama_dep') }}"  required>
                                    @error('nama_dep')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="keterangan" rows="3"
                                              class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('departemen.index') }}" class="btn btn-secondary">
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
    .departemenPage .card { border: none; border-radius: 10px; }
    .departemenPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .departemenPage .form-control:focus { border-color: var(--primary-green); box-shadow: 0 0 0 0.2rem rgba(16,185,129,0.25); }
    .departemenPage .btn { padding: 0.5rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
    .departemenPage .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .departemenPage .btn-success { background-color: var(--primary-green); border-color: var(--primary-green); }
    .departemenPage .btn-success:hover { background-color: var(--dark-green); border-color: var(--dark-green); }
</style>
@endpush

@push('scripts')
<script>$(document).ready(function() { $('input[name="skt_dep"]').focus(); });</script>
@endpush