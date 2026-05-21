@extends('layouts.app')

@section('title', 'Add Invoice')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800">Add Invoice</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">Invoice</a></li>
                <li class="breadcrumb-item active">Add</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Invoice Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoice.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Invoice Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}"  required>
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Unique invoice code</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Invoice Category <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_ctg" class="form-control @error('invoice_ctg') is-invalid @enderror" value="{{ old('invoice_ctg') }}" list="categoryList" required>
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
                            <input type="text" name="invoice_typ" class="form-control @error('invoice_typ') is-invalid @enderror" value="{{ old('invoice_typ') }}" list="typeList" required>
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
                            <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="4" >{{ old('note') }}</textarea>
                            @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Save
                            </button>
                            <a href="{{ route('invoice.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection