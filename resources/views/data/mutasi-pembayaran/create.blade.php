@extends('layouts.app')

@section('title', 'Add Payment Mutation')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        :root {
            --primary-green: #10b981;
            --dark-green: #059669;
            --text-dark: #1e293b;
        }

        .mutasiCreatePage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .mutasiCreatePage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .mutasiCreatePage .form-control:focus,
        .mutasiCreatePage .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .mutasiCreatePage .form-label {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .mutasiCreatePage .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .mutasiCreatePage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .mutasiCreatePage .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .mutasiCreatePage .btn-success:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: 600;
        }

        /* STATUS BADGE */
        .badge-not-saved {
            background-color: #e5e7eb;
            color: #374151;
            font-weight: 500;
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-not-saved .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #9ca3af;
            display: inline-block;
        }

        /* FINAL SAVE SECTION */
        .final-save-section {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 20px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px 12px 0 0;
            margin-top: 30px;
            z-index: 100;
        }

        .btn-final-back {
            background: linear-gradient(135deg, #868686 0%, #5e5e5e 100%);
            border: none;
            color: white;
            padding: 13px 36px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 6px 20px rgba(49, 49, 49, 0.4);
            transition: all 0.3s;
        }

        .btn-final-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(49, 49, 49, 0.5);
            color: white;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px) !important;
            padding: 0.375rem 0.75rem !important;
            border: 2px solid #dee2e6 !important;
            border-radius: 8px !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 0 !important;
            color: #212529;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
        }

        .voucher-no-preview {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 0.85rem;
            color: #065f46;
            display: none;
        }

        .voucher-no-preview.show {
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mutasiCreatePage">
        <div class="row">
            <div class="col-lg-12">

                {{-- ══ PAGE HEADER CARD ══ --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold">
                            <i class="fas fa-money-bill-wave me-2"></i>Add Payment Mutation
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-not-saved">
                                <span class="dot"></span> Not Saved
                            </span>
                            <a href="{{ route('mutasi-pembayaran.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ══ PAYMENT MUTATION INFO CARD ══ --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color: #d1fae5">
                        <h6 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>Payment Mutation Information
                        </h6>
                    </div>
                    <div class="card-body p-4">

                        {{-- Error / Success Flash --}}
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle me-2"></i>Validasi gagal:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('mutasi-pembayaran.store') }}" method="POST" id="createForm">
                            @csrf

                            <div class="row">
                                {{-- Voucher Number (auto-generated based on Bank/Cash) --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Voucher No.</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="text" id="voucher_no_preview" class="form-control fw-bold"
                                            value="Auto-generate on save"
                                            disabled
                                            style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                    </div>
                                    <small class="text-muted" id="voucher_no_hint">
                                        <i class="fas fa-info-circle me-1"></i>Select Bank / Cash first to preview format
                                    </small>
                                </div>

                                {{-- Date --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Date</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control"
                                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    @error('tanggal')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Bank / Cash --}}
                                <div class="col-md-12 mb-3">
                                    <label class="form-label required-field">Bank / Cash Account</label>
                                    <select name="id_md_chart_of_account" id="select_coa" class="form-select"
                                        data-placeholder="Select Bank / Cash account...">
                                        <option value=""></option>
                                        @foreach ($chartOfAccounts as $coa)
                                            <option value="{{ $coa->id_md_chart_of_account }}"
                                                data-payment-type="{{ $coa->payment_type }}"
                                                {{ old('id_md_chart_of_account') == $coa->id_md_chart_of_account ? 'selected' : '' }}>
                                                [{{ $coa->payment_type }}] {{ $coa->no_account }} —
                                                {{ $coa->account_name ?? ($coa->nama_account ?? '') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_md_chart_of_account')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted mt-1 d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Kas → voucher number format AK{yy}.{NNNNN} &nbsp;|&nbsp; Bank → BK{yy}.{NNNNN}
                                    </small>
                                </div>

                                {{-- Check No. / BG --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Check No. / BG</label>
                                    <input type="text" name="no_cek" id="no_cek" class="form-control"
                                        value="{{ old('no_cek') }}" placeholder="Check number / giro...">
                                    @error('no_cek')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Exchange Rate --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Exchange Rate (if any)</label>
                                    <input type="text" id="kurs_display" class="form-control"
                                        value="{{ old('kurs') ? number_format(old('kurs'), 2, ',', '.') : '' }}"
                                        placeholder="0,00">
                                    <input type="hidden" name="kurs" id="kurs_value" value="{{ old('kurs', 0) }}">
                                    @error('kurs')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Memo --}}
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Memo / Notes</label>
                                    <textarea name="memo" id="memo" class="form-control" rows="3"
                                        placeholder="Additional notes for this mutation...">{{ old('memo') }}</textarea>
                                    @error('memo')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Save & Continue
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- FINAL SAVE SECTION --}}
    <div class="final-save-section">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                Fill in the form above, then click Save & Continue to add payment details
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('mutasi-pembayaran.index') }}" class="btn btn-final-back">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Init Select2 for COA
            $('#select_coa').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select Bank / Cash account...',
                allowClear: true,
                width: '100%',
            });

            // When COA changes, fetch next voucher number
            $('#select_coa').on('change', function() {
                const val = $(this).val();
                const $preview = $('#voucher_no_preview');
                const $hint = $('#voucher_no_hint');

                if (!val) {
                    $preview.val('Auto-generate on save');
                    $hint.html('<i class="fas fa-info-circle me-1"></i>Select Bank / Cash first to preview format');
                    return;
                }

                // Read payment type directly from the original <option> element
                const paymentType = $('select#select_coa option[value="' + val + '"]').data('payment-type')
                                 || $('select#select_coa option[value="' + val + '"]').attr('data-payment-type');

                if (!paymentType) {
                    $preview.val('Auto-generate on save');
                    $hint.html('<i class="fas fa-info-circle me-1"></i>Cannot determine account type');
                    return;
                }

                $preview.val('Generating...').css('opacity', 0.5);

                fetch('/data/mutasi-pembayaran/next-voucher-no?payment_type=' + encodeURIComponent(paymentType), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        $preview.val(data.success ? data.nomor : 'Auto-generate on save').css('opacity', 1);
                        $hint.html('<i class="fas fa-check-circle text-success me-1"></i>Voucher number will be auto-generated when saved');
                    })
                    .catch(err => {
                        console.error('[VoucherNo]', err);
                        $preview.val('Auto-generate on save').css('opacity', 1);
                        $hint.html('<i class="fas fa-exclamation-circle text-warning me-1"></i>Could not preview – will auto-generate on save');
                    });
            });

            // Kurs formatting
            const kursDisplay = document.getElementById('kurs_display');
            const kursValue   = document.getElementById('kurs_value');

            if (kursDisplay) {
                kursDisplay.addEventListener('input', function() {
                    let val = this.value.replace(/[^\d,]/g, '').replace(/\./g, '');
                    let [int, dec] = val.split(',');
                    if (dec !== undefined && dec.length > 2) dec = dec.substring(0, 2);
                    int = (int || '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    this.value = dec !== undefined ? int + ',' + dec : int;
                    kursValue.value = parseFloat((val || '0').replace(',', '.')) || 0;
                });

                kursDisplay.addEventListener('blur', function() {
                    if (this.value && !this.value.includes(',')) {
                        this.value += ',00';
                    }
                });
            }
        });
    </script>
@endpush
