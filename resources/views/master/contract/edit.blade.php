@extends('layouts.app')

@section('title', 'Edit Contract')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800">Edit Contract</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('contract.index') }}">Contract</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Edit Contract Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('contract.update', $contract->id_md_cont) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Contract No. <span class="text-danger">*</span></label>
                                <input type="text" name="no_contract" class="form-control @error('no_contract') is-invalid @enderror" value="{{ old('no_contract', $contract->no_contract) }}" placeholder="e.g., CNT-2026-001" required>
                                @error('no_contract')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contract Name <span class="text-danger">*</span></label>
                                <input type="text" name="contract" class="form-control @error('contract') is-invalid @enderror" value="{{ old('contract', $contract->contract) }}" placeholder="Contract name" required>
                                @error('contract')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Customer <span class="text-danger">*</span></label>
                            <select name="id_md_cust" class="form-select @error('id_md_cust') is-invalid @enderror" required>
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id_md_cust }}" {{ old('id_md_cust', $contract->id_md_cust) == $customer->id_md_cust ? 'selected' : '' }}>
                                    {{ $customer->code }} - {{ $customer->customer }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_md_cust')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Expenditure Value <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="expenditure" class="form-control @error('expenditure') is-invalid @enderror" value="{{ old('expenditure', $contract->expenditure) }}" placeholder="0" min="0" step="0.01" required>
                                @error('expenditure')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="date_start" class="form-control @error('date_start') is-invalid @enderror" value="{{ old('date_start', $contract->date_start->format('Y-m-d')) }}" required>
                                @error('date_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="date_end" class="form-control @error('date_end') is-invalid @enderror" value="{{ old('date_end', $contract->date_end->format('Y-m-d')) }}" required>
                                @error('date_end')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3" placeholder="Additional notes">{{ old('note', $contract->note) }}</textarea>
                            @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Update
                            </button>
                            <a href="{{ route('contract.index') }}" class="btn btn-secondary">
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
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Update Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td width="40%">Created</td>
                            <td>: {{ $contract->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td>Last Updated</td>
                            <td>: {{ $contract->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:
                                @if($contract->date_end >= now())
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Expired</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection