@extends('layouts.app')

@section('title', 'Edit Vessel')

@section('content')
<div class="container-fluid vesselPage">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Vessel</span>
                    <a href="{{ route('vessel.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('vessel.update', $vessel->id_md_vessel) }}" method="POST" id="vesselForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Vessel Name -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Vessel Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="vessel_name"
                                           class="form-control @error('vessel_name') is-invalid @enderror"
                                           value="{{ old('vessel_name', $vessel->vessel_name) }}"
                                           required>
                                    @error('vessel_name')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Vessel Type -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Vessel Type</label>
                                    <input type="text"
                                           name="vessel_type"
                                           class="form-control @error('vessel_type') is-invalid @enderror"
                                           value="{{ old('vessel_type', $vessel->vessel_type) }}"
                                           list="vesselTypeList">
                                    @error('vessel_type')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                    @if(isset($vesselTypes) && $vesselTypes->count() > 0)
                                    <datalist id="vesselTypeList">
                                        @foreach($vesselTypes as $type)
                                        <option value="{{ $type }}">
                                        @endforeach
                                    </datalist>
                                    @endif
                                </div>
                            </div>

                            <!-- IMO Number -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">No. IMO</label>
                                    <input type="text"
                                           name="no_imo"
                                           class="form-control @error('no_imo') is-invalid @enderror"
                                           value="{{ old('no_imo', $vessel->no_imo) }}">
                                    @error('no_imo')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- MMSI Number -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">No. MMSI</label>
                                    <input type="text"
                                           name="no_mmsi"
                                           class="form-control @error('no_mmsi') is-invalid @enderror"
                                           value="{{ old('no_mmsi', $vessel->no_mmsi) }}">
                                    @error('no_mmsi')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Call Sign -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Call Sign</label>
                                    <input type="text"
                                           name="call_sign"
                                           class="form-control @error('call_sign') is-invalid @enderror"
                                           value="{{ old('call_sign', $vessel->call_sign) }}">
                                    @error('call_sign')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Gross Tonnage -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Gross Tonnage</label>
                                    <input type="text"
                                           name="gt"
                                           class="form-control @error('gt') is-invalid @enderror"
                                           value="{{ old('gt', $vessel->gt) }}">
                                    @error('gt')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- DWT -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Summer DWT</label>
                                    <input type="text"
                                           name="dwt"
                                           class="form-control @error('dwt') is-invalid @enderror"
                                           value="{{ old('dwt', $vessel->dwt) }}">
                                    @error('dwt')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Year Built -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Year Built</label>
                                    <input type="text"
                                           name="year_built"
                                           class="form-control @error('year_built') is-invalid @enderror"
                                           value="{{ old('year_built', $vessel->year_built) }}"
                                           maxlength="4">
                                    @error('year_built')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Breadth Extreme -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Breadth Extreme</label>
                                    <input type="text"
                                           name="breadth"
                                           class="form-control @error('breadth') is-invalid @enderror"
                                           value="{{ old('breadth', $vessel->breadth) }}">
                                    @error('breadth')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Length Overall -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Length Overall</label>
                                    <input type="text"
                                           name="loa"
                                           class="form-control @error('loa') is-invalid @enderror"
                                           value="{{ old('loa', $vessel->loa) }}">
                                    @error('loa')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Flag -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Flag</label>
                                    <input type="text"
                                           name="flag"
                                           class="form-control @error('flag') is-invalid @enderror"
                                           value="{{ old('flag', $vessel->flag) }}"
                                           list="flagList">
                                    @error('flag')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                    @if(isset($flags) && $flags->count() > 0)
                                    <datalist id="flagList">
                                        @foreach($flags as $flag)
                                        <option value="{{ $flag }}">
                                        @endforeach
                                    </datalist>
                                    @endif
                                </div>
                            </div>

                            <!-- Principal / GA -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Principal / GA</label>
                                    <input type="text"
                                           name="ga"
                                           class="form-control @error('ga') is-invalid @enderror"
                                           value="{{ old('ga', $vessel->ga) }}">
                                    @error('ga')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Ship Particular -->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ship Particular</label>
                                    <textarea name="ship_particular"
                                              class="form-control @error('ship_particular') is-invalid @enderror"
                                              rows="4">{{ old('ship_particular', $vessel->ship_particular) }}</textarea>
                                    @error('ship_particular')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('vessel.index') }}" class="btn btn-secondary">
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
    .vesselPage .card {
        border: none;
        border-radius: 10px;
    }

    .vesselPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .vesselPage .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }

    .vesselPage .form-label {
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .vesselPage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .vesselPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .vesselPage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .vesselPage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }

    .vesselPage .alert {
        border-radius: 8px;
    }

    .vesselPage .text-sm small {
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