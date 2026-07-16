@extends('layouts.app')

@section('title', 'Invoice Detail')

@section('content')
    <div class="container-fluid invoicePage">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Invoice Detail</span>
                        <div class="d-flex gap-2">
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('invoice', 'ubah')))
                                <a href="{{ route('invoice.edit', $invoice->id_md_invoice) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('invoice.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-4">

                        {{-- JO Category (Radio - Read Only) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">JO Category</label>
                            <div class="border rounded p-3 bg-light">
                                @foreach ($joCtgOptions as $value => $label)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jo_ctg"
                                            id="jo_ctg_{{ $value }}" value="{{ $value }}"
                                            {{ $invoice->jo_ctg === $value ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="jo_ctg_{{ $value }}">
                                            @if ($value === 'contract')
                                                <i class="fas fa-file-contract me-1 text-primary"></i>
                                            @elseif ($value === 'tramper')
                                                <i class="fas fa-ship me-1 text-warning"></i>
                                            @elseif ($value === 'other')
                                                <i class="fas fa-ellipsis-h me-1 text-secondary"></i>
                                            @elseif ($value === 'general')
                                                <i class="fas fa-globe me-1 text-success"></i>
                                            @endif
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Invoice Category --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Invoice Category</label>
                            <input type="text" class="form-control" value="{{ $invoice->invoice_ctg }}" readonly>
                        </div>

                        {{-- Invoice Item --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Invoice Item</label>
                            <input type="text" class="form-control" value="{{ $invoice->invoice_typ }}" readonly>
                        </div>

                        {{-- Note --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Note</label>
                            <textarea class="form-control" rows="5" readonly>{{ $invoice->note }}</textarea>
                        </div>

                        <hr class="my-4">

                        {{-- Timestamps --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Created At</label>
                                <input type="text" class="form-control" value="{{ $invoice->created_at->format('d F Y, H:i') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Last Updated</label>
                                <input type="text" class="form-control" value="{{ $invoice->updated_at->format('d F Y, H:i') }}" readonly>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('invoice.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Close
                            </a>
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('invoice', 'ubah')))
                                <a href="{{ route('invoice.edit', $invoice->id_md_invoice) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .invoicePage .card {
            border: none;
            border-radius: 10px;
        }

        .invoicePage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .invoicePage .form-control:read-only {
            background-color: #f8f9fa;
            color: #495057;
            cursor: default;
        }

        .invoicePage .form-control:read-only:focus {
            border-color: #dee2e6;
            box-shadow: none;
        }

        .invoicePage .form-check-input:disabled {
            opacity: 1;
        }

        .invoicePage .form-check-input:disabled:checked {
            background-color: #10b981;
            border-color: #10b981;
        }

        .invoicePage .form-label {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .invoicePage .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .invoicePage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush