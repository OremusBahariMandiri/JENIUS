@extends('layouts.app')

@section('title', 'Edit JO Contract')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800">Edit JO Contract</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('jo-contract.index') }}">JO Contract</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Edit JO Contract Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('jo-contract.update', $joContract->id_jo_cont) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">JO ID</label>
                            <input type="text" class="form-control" value="JO-{{ $joContract->id_jo_cont }}" readonly>
                            <small class="text-muted">Auto-generated ID</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contract <span class="text-danger">*</span></label>
                            <select name="id_md_cont" class="form-select @error('id_md_cont') is-invalid @enderror" required>
                                <option value="">Select Contract</option>
                                @foreach($contracts as $contract)
                                <option value="{{ $contract->id_md_cont }}" {{ old('id_md_cont', $joContract->id_md_cont) == $contract->id_md_cont ? 'selected' : '' }}>
                                    {{ $contract->no_contract }} - {{ $contract->contract }}
                                    @if($contract->customer)
                                    ({{ $contract->customer->customer }})
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            @error('id_md_cont')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Area <span class="text-danger">*</span></label>
                            <select name="id_md_area" class="form-select @error('id_md_area') is-invalid @enderror" required>
                                <option value="">Select Area</option>
                                @foreach($areas as $area)
                                <option value="{{ $area->id_md_area }}" {{ old('id_md_area', $joContract->id_md_area) == $area->id_md_area ? 'selected' : '' }}>
                                    {{ $area->area }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_md_area')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $joContract->title) }}" placeholder="JO contract title" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="4" placeholder="Additional notes (optional)">{{ old('note', $joContract->note) }}</textarea>
                            @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Update
                            </button>
                            <a href="{{ route('jo-contract.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <ul class="ps-3">
                            <li>Fields marked with <span class="text-danger">*</span> are required</li>
                            <li>JO ID cannot be changed</li>
                            <li>Select the main contract for this JO</li>
                            <li>Choose the work area for this JO</li>
                        </ul>
                    </small>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Record Info</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <div class="mb-2">
                            <i class="fas fa-calendar-plus me-2"></i>Created: {{ $joContract->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <i class="fas fa-calendar-check me-2"></i>Updated: {{ $joContract->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection