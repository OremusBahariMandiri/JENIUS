@extends('layouts.app')

@section('title', 'Edit Port')

@section('content')
<div class="container-fluid portPage">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Port</span>
                    <a href="{{ route('port.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('port.update', $port->id_md_port) }}" method="POST" id="portForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Port Code -->
                            {{-- <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Port Code</label>
                                    <input type="text"
                                           name="no_port"
                                           class="form-control @error('no_port') is-invalid @enderror"
                                           value="{{ old('no_port', $port->no_port) }}">
                                    @error('no_port')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div> --}}

                            <!-- Port Name -->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Port Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="name_port"
                                           class="form-control @error('name_port') is-invalid @enderror"
                                           value="{{ old('name_port', $port->name_port) }}"
                                           required>
                                    @error('name_port')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Address</label>
                                    <textarea name="alamat"
                                              class="form-control @error('alamat') is-invalid @enderror"
                                              rows="2">{{ old('alamat', $port->alamat) }}</textarea>
                                    @error('alamat')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- City -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">City</label>
                                    <input type="text"
                                           name="kota"
                                           class="form-control @error('kota') is-invalid @enderror"
                                           value="{{ old('kota', $port->kota) }}">
                                    @error('kota')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Province/State -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Province/State</label>
                                    <input type="text"
                                           name="provinsi"
                                           class="form-control @error('provinsi') is-invalid @enderror"
                                           value="{{ old('provinsi', $port->provinsi) }}"
                                           list="provinceList">
                                    @error('provinsi')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                    @if(isset($provinces) && $provinces->count() > 0)
                                    <datalist id="provinceList">
                                        @foreach($provinces as $province)
                                        <option value="{{ $province }}">
                                        @endforeach
                                    </datalist>
                                    @endif
                                </div>
                            </div>

                            <!-- Country -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Country</label>
                                    <input type="text"
                                           name="negara"
                                           class="form-control @error('negara') is-invalid @enderror"
                                           value="{{ old('negara', $port->negara) }}"
                                           list="countryList">
                                    @error('negara')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                    @if(isset($countries) && $countries->count() > 0)
                                    <datalist id="countryList">
                                        @foreach($countries as $country)
                                        <option value="{{ $country }}">
                                        @endforeach
                                    </datalist>
                                    @endif
                                </div>
                            </div>

                            <!-- Port Type -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Port Type</label>
                                    <input type="text"
                                           name="port_type"
                                           class="form-control @error('port_type') is-invalid @enderror"
                                           value="{{ old('port_type', $port->port_type) }}"
                                           list="portTypeList">
                                    @error('port_type')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                    @if(isset($portTypes) && $portTypes->count() > 0)
                                    <datalist id="portTypeList">
                                        @foreach($portTypes as $type)
                                        <option value="{{ $type }}">
                                        @endforeach
                                    </datalist>
                                    @endif
                                </div>
                            </div>

                            <!-- Operator Port -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Operator Port</label>
                                    <input type="text"
                                           name="operator_port"
                                           class="form-control @error('operator_port') is-invalid @enderror"
                                           value="{{ old('operator_port', $port->operator_port) }}">
                                    @error('operator_port')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Max Draft -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Max Draft</label>
                                    <input type="text"
                                           name="draft"
                                           class="form-control @error('draft') is-invalid @enderror"
                                           value="{{ old('draft', $port->draft) }}">
                                    @error('draft')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Max LOA (Length Overall) -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Max LOA (Length Overall)</label>
                                    <input type="text"
                                           name="panjang_kapal"
                                           class="form-control @error('panjang_kapal') is-invalid @enderror"
                                           value="{{ old('panjang_kapal', $port->panjang_kapal) }}">
                                    @error('panjang_kapal')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Max Beam -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Max Beam</label>
                                    <input type="text"
                                           name="lebar_kapal"
                                           class="form-control @error('lebar_kapal') is-invalid @enderror"
                                           value="{{ old('lebar_kapal', $port->lebar_kapal) }}">
                                    @error('lebar_kapal')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Max DWT -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Max DWT</label>
                                    <input type="text"
                                           name="dwt"
                                           class="form-control @error('dwt') is-invalid @enderror"
                                           value="{{ old('dwt', $port->dwt) }}">
                                    @error('dwt')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Berth Length -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Berth Length</label>
                                    <input type="text"
                                           name="panjang_dermaga"
                                           class="form-control @error('panjang_dermaga') is-invalid @enderror"
                                           value="{{ old('panjang_dermaga', $port->panjang_dermaga) }}">
                                    @error('panjang_dermaga')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tidal Restriction -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tidal Restriction</label>
                                    <input type="text"
                                           name="pasang_surut"
                                           class="form-control @error('pasang_surut') is-invalid @enderror"
                                           value="{{ old('pasang_surut', $port->pasang_surut) }}">
                                    @error('pasang_surut')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Working Hours -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Working Hours</label>
                                    <input type="text"
                                           name="jam_ops"
                                           class="form-control @error('jam_ops') is-invalid @enderror"
                                           value="{{ old('jam_ops', $port->jam_ops) }}">
                                    @error('jam_ops')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('port.index') }}" class="btn btn-secondary">
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
    .portPage .card {
        border: none;
        border-radius: 10px;
    }

    .portPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .portPage .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }

    .portPage .form-label {
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .portPage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .portPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .portPage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .portPage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }

    .portPage .alert {
        border-radius: 8px;
    }

    .portPage .text-sm small {
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