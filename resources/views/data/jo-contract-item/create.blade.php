@extends('layouts.app')

@section('title', 'Add JO Contract Item')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800">Add JO Contract Item</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('jo-contract-item.index') }}">JO Contract Item</a></li>
                <li class="breadcrumb-item active">Add</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">JO Contract Item Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('jo-contract-item.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">JO Contract <span class="text-danger">*</span></label>
                                <select name="id_jo_cont" class="form-select @error('id_jo_cont') is-invalid @enderror" required>
                                    <option value="">Select JO Contract</option>
                                    @foreach($joContracts as $joContract)
                                    <option value="{{ $joContract->id_jo_cont }}" {{ old('id_jo_cont') == $joContract->id_jo_cont ? 'selected' : '' }}>
                                        JO-{{ $joContract->id_jo_cont }} - {{ $joContract->title }}
                                        @if($joContract->contract && $joContract->contract->customer)
                                        ({{ $joContract->contract->customer->customer }})
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                @error('id_jo_cont')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Invoice <span class="text-danger">*</span></label>
                                <select name="id_md_invoice" class="form-select @error('id_md_invoice') is-invalid @enderror" required>
                                    <option value="">Select Invoice</option>
                                    @foreach($invoices as $invoice)
                                    <option value="{{ $invoice->id_md_invoice }}" {{ old('id_md_invoice') == $invoice->id_md_invoice ? 'selected' : '' }}>
                                        {{ $invoice->code }} - {{ $invoice->invoice_ctg }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('id_md_invoice')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3"><i class="fas fa-money-bill me-2"></i>Revenue Information</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Revenue IDR</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="pendapatan_idr" class="form-control @error('pendapatan_idr') is-invalid @enderror" value="{{ old('pendapatan_idr', 0) }}" step="0.01" min="0">
                                    @error('pendapatan_idr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Revenue USD</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="pendapatan_usd" class="form-control @error('pendapatan_usd') is-invalid @enderror" value="{{ old('pendapatan_usd', 0) }}" step="0.01" min="0">
                                    @error('pendapatan_usd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Exchange Rate (Kurs USD)</label>
                                <input type="number" name="kurs_usd" class="form-control @error('kurs_usd') is-invalid @enderror" value="{{ old('kurs_usd', 0) }}" step="0.0001" min="0">
                                @error('kurs_usd')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Exchange Rate Date</label>
                                <input type="date" name="tgl_kurs_usd" class="form-control @error('tgl_kurs_usd') is-invalid @enderror" value="{{ old('tgl_kurs_usd') }}">
                                @error('tgl_kurs_usd')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3"><i class="fas fa-calculator me-2"></i>Cost & Price Information</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">HPP Ops (Cost of Operations)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="hpp_ops" class="form-control @error('hpp_ops') is-invalid @enderror" value="{{ old('hpp_ops', 0) }}" step="0.01" min="0">
                                    @error('hpp_ops')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Selling Price IDR</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="hargajual_idr" class="form-control @error('hargajual_idr') is-invalid @enderror" value="{{ old('hargajual_idr', 0) }}" step="0.01" min="0">
                                    @error('hargajual_idr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Save
                            </button>
                            <a href="{{ route('jo-contract-item.index') }}" class="btn btn-secondary">
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
                            <li>Select the JO contract this item belongs to</li>
                            <li>Choose the invoice type for this item</li>
                            <li>All monetary values default to 0 if not filled</li>
                            <li>Exchange rate should be filled if revenue USD is used</li>
                        </ul>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection