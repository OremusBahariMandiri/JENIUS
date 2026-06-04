@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
    <div class="container-fluid invoicePage">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Invoice</span>
                        <a href="{{ route('invoice.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('invoice.update', $invoice->id_md_invoice) }}" method="POST"
                            id="invoiceForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-lg-12">

                                    {{-- JO Category (Radio) --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            JO Category <span class="text-danger">*</span>
                                        </label>
                                        <div class="border rounded p-3 bg-light @error('jo_ctg') border-danger @enderror">
                                            @foreach ($joCtgOptions as $value => $label)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jo_ctg"
                                                        id="jo_ctg_{{ $value }}" value="{{ $value }}"
                                                        {{ old('jo_ctg', $invoice->jo_ctg) === $value ? 'checked' : '' }}
                                                        required>
                                                    <label class="form-check-label" for="jo_ctg_{{ $value }}">
                                                        @if ($value === 'contract')
                                                            <i class="fas fa-file-contract me-1 text-primary"></i>
                                                        @elseif($value === 'tramper')
                                                            <i class="fas fa-ship me-1 text-warning"></i>
                                                        @else
                                                            <i class="fas fa-ellipsis-h me-1 text-secondary"></i>
                                                        @endif
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('jo_ctg')
                                            <div class="text-danger small mt-1">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- Invoice Category --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Invoice Category <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="invoice_ctg"
                                            class="form-control @error('invoice_ctg') is-invalid @enderror"
                                            value="{{ old('invoice_ctg', $invoice->invoice_ctg) }}" list="categoryList"
                                            required>
                                        @error('invoice_ctg')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        @if (isset($categories) && $categories->count() > 0)
                                            <datalist id="categoryList">
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category }}">
                                                @endforeach
                                            </datalist>
                                        @endif
                                    </div>

                                    {{-- Invoice Item --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Invoice Item <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="invoice_typ"
                                            class="form-control @error('invoice_typ') is-invalid @enderror"
                                            value="{{ old('invoice_typ', $invoice->invoice_typ) }}" list="typeList"
                                            required>
                                        @error('invoice_typ')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        @if (isset($types) && $types->count() > 0)
                                            <datalist id="typeList">
                                                @foreach ($types as $type)
                                                    <option value="{{ $type }}">
                                                @endforeach
                                            </datalist>
                                        @endif
                                    </div>


                                    {{-- Note --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Note</label>
                                        <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="5">{{ old('note', $invoice->note) }}</textarea>
                                        @error('note')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('invoice.index') }}" class="btn btn-secondary">
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
        .invoicePage .card {
            border: none;
            border-radius: 10px;
        }

        .invoicePage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .invoicePage .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
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

        .invoicePage .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .invoicePage .btn-success:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }

        .invoicePage .alert {
            border-radius: 8px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('input, textarea').on('change', function() {
                $(this).addClass('border-warning');
                setTimeout(() => $(this).removeClass('border-warning'), 1000);
            });
        });
    </script>
@endpush
