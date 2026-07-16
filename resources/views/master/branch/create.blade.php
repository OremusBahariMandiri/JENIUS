@extends('layouts.app')

@section('title', 'Add Branch')

@section('content')
<div class="container-fluid branchPage">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add Branch</span>
                    <a href="{{ route('branch.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('branch.store') }}" method="POST" id="branchForm">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Abbr.</label>
                                    <input type="text" name="skt_branch"
                                           class="form-control @error('skt_branch') is-invalid @enderror"
                                           value="{{ old('skt_branch') }}">
                                    @error('skt_branch')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Branch Name <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_branch"
                                           class="form-control @error('nama_branch') is-invalid @enderror"
                                           value="{{ old('nama_branch') }}" required>
                                    @error('nama_branch')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Area</label>
                                    <input type="text" name="area"
                                           class="form-control @error('area') is-invalid @enderror"
                                           value="{{ old('area') }}" list="areaList">
                                    @error('area')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                    @if(isset($areas) && $areas->count() > 0)
                                    <datalist id="areaList">
                                        @foreach($areas as $area)<option value="{{ $area }}">@endforeach
                                    </datalist>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" name="no_telepon"
                                           class="form-control @error('no_telepon') is-invalid @enderror"
                                           value="{{ old('no_telepon') }}">
                                    @error('no_telepon')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}">
                                    @error('email')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Address</label>
                                    <textarea name="alamat" rows="2"
                                              class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Note</label>
                                    <textarea name="note" rows="3"
                                              class="form-control @error('note') is-invalid @enderror">{{ old('note') }}</textarea>
                                    @error('note')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('branch.index') }}" class="btn btn-secondary">
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
    .branchPage .card { border: none; border-radius: 10px; }
    .branchPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .branchPage .form-control:focus { border-color: var(--primary-green); box-shadow: 0 0 0 0.2rem rgba(16,185,129,0.25); }
    .branchPage .btn { padding: 0.5rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
    .branchPage .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .branchPage .btn-success { background-color: var(--primary-green); border-color: var(--primary-green); }
    .branchPage .btn-success:hover { background-color: var(--dark-green); border-color: var(--dark-green); }
</style>
@endpush

@push('scripts')
<script>$(document).ready(function() { $('input[name="skt_branch"]').focus(); });</script>
@endpush