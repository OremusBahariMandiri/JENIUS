@extends('layouts.app')

@section('title', 'Add Payment Mutation')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        .mutasiCreatePage .card { border:none; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,.08); }
        .mutasiCreatePage .card-header { border-radius:10px 10px 0 0!important; padding:1rem 1.5rem; font-weight:600; }
        .mutasiCreatePage .form-control:focus,
        .mutasiCreatePage .form-select:focus { border-color:#10b981; box-shadow:0 0 0 .2rem rgba(16,185,129,.25); }
        .mutasiCreatePage .form-label { color:#1e293b; margin-bottom:.5rem; font-weight:500; }
        .required-field::after { content:" *"; color:#dc3545; font-weight:600; }

        .final-save-section {
            position:sticky; bottom:0; background:white; padding:20px;
            box-shadow:0 -4px 20px rgba(0,0,0,.1); border-radius:12px 12px 0 0;
            margin-top:30px; z-index:100;
        }
        .btn-final-back {
            background:linear-gradient(135deg,#868686 0%,#5e5e5e 100%); border:none; color:white;
            padding:15px 40px; border-radius:12px; font-weight:700; font-size:1.1rem;
            box-shadow:0 6px 20px rgba(27,27,27,.4); transition:all .3s;
        }
        .btn-final-back:hover { transform:translateY(-3px); box-shadow:0 8px 25px rgba(49,49,49,.5); color:white; }

        .select2-container { width:100%!important; }
        .select2-container .select2-selection--single {
            height:calc(1.5em + .75rem + 2px)!important; padding:.375rem .75rem!important;
            border:1px solid #dee2e6!important; border-radius:.375rem!important;
        }
        .select2-container .select2-selection--single .select2-selection__rendered { line-height:1.5!important; padding-left:0!important; color:#212529; }
        .select2-container .select2-selection--single .select2-selection__arrow { height:100%!important; }
    </style>
@endpush

@section('content')
<div class="container-fluid mutasiCreatePage">
    <div class="row"><div class="col-lg-12">

        {{-- PAGE HEADER --}}
        <div class="card shadow mb-4">
            <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color:#dcfce7;">
                <span class="fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Add Payment Mutation</span>
            </div>
        </div>

        {{-- FORM CARD --}}
        <div class="card shadow mb-4">
            <div class="card-header text-black" style="background-color:#dcfce7;">
                <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Payment Mutation Information</h6>
            </div>
            <div class="card-body p-4">

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Validation failed:</strong>
                        <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('mutasi-pembayaran.store') }}" method="POST" id="createForm">
                    @csrf
                    <div class="row">

                        {{-- Voucher No. --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Voucher No.</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#16a34a;color:white;"><i class="fas fa-hashtag"></i></span>
                                <input type="text" id="voucher_no_preview" class="form-control fw-bold"
                                    value="Auto-generate on save" disabled
                                    style="background-color:#f0fdf4;color:#14532d;letter-spacing:1px;">
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Date</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control"
                                value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Bank / Cash --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Bank / Cash</label>
                            <select name="id_md_chart_of_account" id="select_coa" class="form-select"
                                data-placeholder="Select Bank / Cash account...">
                                <option value=""></option>
                                @foreach($chartOfAccounts as $coa)
                                    <option value="{{ $coa->id_md_chart_of_account }}"
                                        data-payment-type="{{ $coa->payment_type }}"
                                        {{ old('id_md_chart_of_account') == $coa->id_md_chart_of_account ? 'selected' : '' }}>
                                        [{{ $coa->payment_type }}] {{ $coa->no_account }} - {{ $coa->account_name ?? ($coa->nama_account ?? '') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_md_chart_of_account')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- No. Cek / BG --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Cek / BG</label>
                            <input type="text" name="no_cek" id="no_cek" class="form-control"
                                value="{{ old('no_cek') }}" placeholder="Check number / BG...">
                            @error('no_cek')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Kurs --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kurs</label>
                            <div class="input-group">
                                <input type="text" id="kurs_display" class="form-control" readonly
                                    placeholder="1,00"
                                    value="{{ old('kurs') && old('kurs') != 1 ? number_format(old('kurs'),2,',','.') : '' }}"
                                    style="background:#f8fafc;cursor:pointer;"
                                    onclick="$('#modalKursCreate').modal('show')">
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="$('#modalKursCreate').modal('show')" title="Set Exchange Rate">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>
                            </div>
                            <small class="text-muted mt-1 d-block" id="kurs_summary_text">
                                <i class="fas fa-info-circle me-1"></i>Default: IDR (1:1)
                            </small>
                            <input type="hidden" name="kurs"       id="kurs_value"      value="{{ old('kurs', 1) }}">
                            <input type="hidden" name="mata_uang"  id="mata_uang_value" value="{{ old('mata_uang', 'IDR') }}">
                            <input type="hidden" name="tgl_kurs"   id="tgl_kurs_value"  value="{{ old('tgl_kurs', '') }}">
                        </div>

                        {{-- Memo --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Memo</label>
                            <textarea name="memo" id="memo" class="form-control" rows="3"
                                placeholder="Additional notes...">{{ old('memo') }}</textarea>
                            @error('memo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn text-white" style="background:#16a34a;">
                            <i class="fas fa-save me-1"></i> Save & Continue
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div></div>
</div>

{{-- FINAL SAVE SECTION --}}
<div class="final-save-section">
    <div class="d-flex justify-content-between align-items-center">
        <div></div>
        <div class="d-flex gap-2">
            <a href="{{ route('mutasi-pembayaran.index') }}" class="btn btn-final-back">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>
    </div>
</div>

{{-- MODAL: Exchange Rate --}}
<div class="modal fade" id="modalKursCreate" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content" style="border-radius:14px;border:none;overflow:hidden;">
            <div class="modal-header" style="background-color:#dcfce7;border:none;border-bottom:1px solid #bbf7d0;padding:16px 20px;">
                <div>
                    <h6 class="modal-title mb-0" style="font-weight:700;color:#14532d;"><i class="fas fa-dollar-sign me-2"></i>Exchange Rate</h6>
                    <div style="font-size:.75rem;color:#16a34a;margin-top:2px;">Set currency exchange rate detail</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.85rem;">Currency</label>
                    <select id="c_kurs_mata_uang" class="form-select" style="border-radius:8px;">
                        <option value="IDR">IDR — Indonesian Rupiah</option>
                        <option value="USD">USD — US Dollar</option>
                        <option value="EUR">EUR — Euro</option>
                        <option value="SGD">SGD — Singapore Dollar</option>
                        <option value="MYR">MYR — Malaysian Ringgit</option>
                        <option value="JPY">JPY — Japanese Yen</option>
                        <option value="CNY">CNY — Chinese Yuan</option>
                        <option value="AUD">AUD — Australian Dollar</option>
                        <option value="GBP">GBP — British Pound</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.85rem;">Exchange Rate Value</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:#f1f5f9;font-size:.85rem;">IDR</span>
                        <input type="text" id="c_kurs_nilai" class="form-control" placeholder="0,00" style="border-radius:0 8px 8px 0;">
                    </div>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-semibold" style="font-size:.85rem;">Rate Date</label>
                    <input type="date" id="c_kurs_tgl" class="form-control" style="border-radius:8px;">
                </div>
            </div>
            <div class="modal-footer" style="border:none;padding:12px 20px;gap:8px;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
                <button type="button" class="btn text-white" onclick="saveKursCreate()" style="background:#16a34a;border-radius:8px;font-weight:600;">
                    <i class="fas fa-check me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){

    // Select2 COA
    $('#select_coa').select2({
        theme:'bootstrap-5',
        placeholder:'Select Bank / Cash account...',
        allowClear:true,
        width:'100%',
    });

    // Preview voucher no when COA changes
    $('#select_coa').on('change', function(){
        const val = $(this).val();
        if(!val){ $('#voucher_no_preview').val('Auto-generate on save'); return; }
        const paymentType = $('select#select_coa option[value="'+val+'"]').data('payment-type');
        if(!paymentType){ return; }
        $('#voucher_no_preview').val('Generating...').css('opacity',.5);
        fetch('/data/mutasi-pembayaran/next-voucher-no?payment_type='+encodeURIComponent(paymentType),
            {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(r=>r.json())
            .then(d=>{ $('#voucher_no_preview').val(d.success ? d.nomor : 'Auto-generate on save').css('opacity',1); })
            .catch(()=>{ $('#voucher_no_preview').val('Auto-generate on save').css('opacity',1); });
    });

    // Modal kurs — populate on open
    $('#modalKursCreate').on('show.bs.modal', function(){
        const mu  = $('#mata_uang_value').val() || 'IDR';
        const val = parseFloat($('#kurs_value').val()) || 1;
        const tgl = $('#tgl_kurs_value').val() || '';
        $('#c_kurs_mata_uang').val(mu);
        if(mu !== 'IDR' && val !== 1){
            const fmt = val % 1 === 0
                ? val.toLocaleString('id-ID')
                : val.toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2});
            $('#c_kurs_nilai').val(fmt).prop('readonly', false);
        } else {
            $('#c_kurs_nilai').val(mu === 'IDR' ? '1' : '').prop('readonly', mu === 'IDR');
        }
        $('#c_kurs_tgl').val(tgl);
    });

    $('#c_kurs_mata_uang').on('change', function(){
        const mu = $(this).val();
        if(mu === 'IDR'){
            $('#c_kurs_nilai').val('1').prop('readonly', true).removeClass('is-invalid');
        } else {
            $('#c_kurs_nilai').val('').prop('readonly', false).focus();
        }
    });

    $('#c_kurs_nilai').on('input', function(){
        $(this).val($(this).val().replace(/[^0-9,]/g,''));
    });

    window.saveKursCreate = function(){
        const mu  = $('#c_kurs_mata_uang').val();
        const raw = $('#c_kurs_nilai').val().replace(',','.');
        const val = parseFloat(raw);
        const tgl = $('#c_kurs_tgl').val();

        if(!val || val <= 0){
            $('#c_kurs_nilai').addClass('is-invalid').focus();
            return;
        }
        $('#c_kurs_nilai').removeClass('is-invalid');

        $('#kurs_value').val(val);
        $('#mata_uang_value').val(mu);
        $('#tgl_kurs_value').val(tgl);

        const fmt = val % 1 === 0
            ? val.toLocaleString('id-ID')
            : val.toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2});
        $('#kurs_display').val(mu === 'IDR' ? '' : fmt);

        const summary = mu !== 'IDR'
            ? '<i class="fas fa-info-circle me-1"></i>' + mu + ' @ ' + fmt + (tgl ? ' · ' + tgl.split('-').reverse().join('/') : '')
            : '<i class="fas fa-info-circle me-1"></i>Default: IDR (1:1)';
        $('#kurs_summary_text').html(summary);

        $('#modalKursCreate').modal('hide');
    };

});
</script>
@endpush
