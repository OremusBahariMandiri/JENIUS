@extends('layouts.app')
@section('title', 'Buat Invoice Contract')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0"><i class="fas fa-file-invoice me-2 text-success"></i>Buat Invoice Contract</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('invoice-contract.index') }}">Invoice Contract</a></li>
                    <li class="breadcrumb-item active">Buat Baru</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('invoice-contract.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('invoice-contract.store') }}">
        @csrf

        <div class="card shadow mb-4">
            <div class="card-header fw-bold" style="background-color:#d1fae5">
                <i class="fas fa-file-alt me-2"></i>Header Invoice
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nomor Invoice <span class="text-danger">*</span></label>
                        <input type="text" name="no_invoice"
                               class="form-control @error('no_invoice') is-invalid @enderror"
                               value="{{ old('no_invoice', $suggestedNo) }}" required>
                        @error('no_invoice')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Job Order <span class="text-danger">*</span></label>
                        <select name="id_jo_cont" id="joSelect"
                                class="form-select @error('id_jo_cont') is-invalid @enderror s2-jo" required>
                            <option value="">-- Pilih JO --</option>
                            @foreach($joContracts as $jo)
                                <option value="{{ $jo->id_jo_cont }}"
                                    data-customer="{{ $jo->contract?->customer?->customer }}"
                                    data-title="{{ $jo->title }}"
                                    data-contract="{{ $jo->contract?->no_contract }}"
                                    {{ old('id_jo_cont', $joContract?->id_jo_cont) == $jo->id_jo_cont ? 'selected' : '' }}>
                                    {{ $jo->no_jo_cont }}
                                    @if($jo->contract?->customer)– {{ $jo->contract->customer->customer }}@endif
                                </option>
                            @endforeach
                        </select>
                        @error('id_jo_cont')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">To Customer</label>
                        <input type="text" name="to_customer" id="toCustomer" class="form-control"
                               value="{{ old('to_customer', $joContract?->contract?->customer?->customer) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" id="titleField" class="form-control"
                               value="{{ old('title', $joContract?->title) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Invoice</label>
                        <input type="date" name="tgl_invoice" class="form-control"
                               value="{{ old('tgl_invoice', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Jatuh Tempo</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Contract No</label>
                        <input type="text" name="contract_no" id="contractNo" class="form-control"
                               value="{{ old('contract_no', $joContract?->contract?->no_contract) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">PO Number</label>
                        <input type="text" name="po_number" class="form-control" value="{{ old('po_number') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Currency</label>
                        <select name="currency" class="form-select">
                            <option value="IDR" {{ old('currency', 'IDR') === 'IDR' ? 'selected' : '' }}>IDR</option>
                            <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Vendor Number</label>
                        <input type="text" name="vendor_number" class="form-control" value="{{ old('vendor_number') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">SA/GR Number</label>
                        <input type="text" name="sa_gr_number" class="form-control" value="{{ old('sa_gr_number') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">BA Number</label>
                        <input type="text" name="ba_number" class="form-control" value="{{ old('ba_number') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">ETA</label>
                        <input type="date" name="eta" class="form-control" value="{{ old('eta') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">ETD</label>
                        <input type="date" name="etd" class="form-control" value="{{ old('etd') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">No. Voyage</label>
                        <input type="text" name="no_voyage" class="form-control" value="{{ old('no_voyage') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Kapal</label>
                        <input type="text" name="kapal" class="form-control" value="{{ old('kapal') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Jetty</label>
                        <input type="text" name="jetty" class="form-control" value="{{ old('jetty') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Cond of Payment</label>
                        <input type="text" name="cond_of_payment" class="form-control" value="{{ old('cond_of_payment') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Period</label>
                        <input type="text" name="period" class="form-control" value="{{ old('period') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Att</label>
                        <input type="text" name="att" class="form-control" value="{{ old('att') }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('invoice-contract.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i>Batal
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i>Simpan & Edit Item
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('.s2-jo').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih JO --',
        allowClear: true,
    });

    $('#joSelect').on('change', function(){
        const opt = $(this).find(':selected');
        $('#toCustomer').val(opt.data('customer') || '');
        $('#titleField').val(opt.data('title') || '');
        $('#contractNo').val(opt.data('contract') || '');
    });
});
</script>
@endpush
