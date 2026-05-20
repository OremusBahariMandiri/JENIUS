@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800">Edit Invoice</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">Invoice</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Edit Invoice Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoice.update', $invoice->id_md_invoice) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Invoice Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $invoice->code) }}" placeholder="e.g. INV001" required>
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Unique invoice code</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Invoice Category <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_ctg" class="form-control @error('invoice_ctg') is-invalid @enderror" value="{{ old('invoice_ctg', $invoice->invoice_ctg) }}" placeholder="e.g. Service, Product" list="categoryList" required>
                                @error('invoice_ctg')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if(isset($categories) && $categories->count() > 0)
                                <datalist id="categoryList">
                                    @foreach($categories as $category)
                                    <option value="{{ $category }}">
                                    @endforeach
                                </datalist>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Invoice Type <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_typ" class="form-control @error('invoice_typ') is-invalid @enderror" value="{{ old('invoice_typ', $invoice->invoice_typ) }}" placeholder="e.g. Monthly, Annual" list="typeList" required>
                            @error('invoice_typ')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(isset($types) && $types->count() > 0)
                            <datalist id="typeList">
                                @foreach($types as $type)
                                <option value="{{ $type }}">
                                @endforeach
                            </datalist>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="4" placeholder="Additional notes (optional)">{{ old('note', $invoice->note) }}</textarea>
                            @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Update
                            </button>
                            <a href="{{ route('invoice.index') }}" class="btn btn-secondary">
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
                            <li>Invoice code must be unique</li>
                            <li>Category and type will be used for filtering</li>
                            <li>You can type new category/type or select from existing ones</li>
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
                            <i class="fas fa-calendar-plus me-2"></i>Created: {{ $invoice->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <i class="fas fa-calendar-check me-2"></i>Updated: {{ $invoice->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </small>
                </div>
            </div>

            @if(isset($categories) && $categories->count() > 0)
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Existing Categories</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($categories as $category)
                        <span class="badge bg-secondary">{{ $category }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if(isset($types) && $types->count() > 0)
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Existing Types</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($types as $type)
                        <span class="badge bg-info text-dark">{{ $type }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection