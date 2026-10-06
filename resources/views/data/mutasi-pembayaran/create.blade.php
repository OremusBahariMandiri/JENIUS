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

        /* VOUCHER SILHOUETTE SECTION */
        .voucher-silhouette-card {
            position: relative;
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .voucher-silhouette-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(3px);
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            border-radius: 10px;
        }

        .voucher-silhouette-overlay .lock-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #059669;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .voucher-silhouette-overlay p {
            font-size: 0.95rem;
            font-weight: 600;
            color: #374151;
            margin: 0;
        }

        .voucher-silhouette-overlay small {
            font-size: 0.8rem;
            color: #6b7280;
        }

        /* Silhouette content (blurred behind overlay) */
        .silhouette-header {
            background-color: #d1fae5;
            padding: 1rem 1.5rem;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px 10px 0 0;
        }

        .silhouette-body {
            padding: 1.5rem;
        }

        .silhouette-voucher-form {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px dashed #10b981;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
        }

        .silhouette-bar {
            height: 12px;
            background: #e5e7eb;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .silhouette-bar.w-60 { width: 60%; }
        .silhouette-bar.w-40 { width: 40%; }
        .silhouette-bar.w-80 { width: 80%; }
        .silhouette-bar.w-30 { width: 30%; }

        .silhouette-accordion {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .silhouette-accordion-header {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
                                {{-- Mutation Number (disabled) --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mutation Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="text" class="form-control fw-bold"
                                            value="{{ $nextNomor ?? 'AUTO-GENERATED' }}"
                                            disabled
                                            style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Auto-generate on save
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
                                    <input type="text" name="kurs" id="kurs_display" class="form-control"
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
                                    <i class="fas fa-save me-1"></i> Save
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                {{-- ══ VOUCHERS CARD — SILHOUETTE (locked until header saved) ══ --}}
                <div class="voucher-silhouette-card shadow mb-4">

                    {{-- Overlay --}}
                    <div class="voucher-silhouette-overlay">
                        <div class="lock-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <p>Save Payment Mutation Information first</p>
                        <small>Vouchers can be added after the mutation header is saved</small>
                    </div>

                    {{-- Blurred content behind overlay --}}
                    <div class="silhouette-header">
                        <span class="fw-bold">
                            <i class="fas fa-layer-group me-2"></i>Payment Vouchers
                            <span class="badge bg-secondary ms-1">0</span>
                        </span>
                        <button class="btn btn-light btn-sm" disabled>
                            <i class="fas fa-plus me-1"></i> Add Voucher
                        </button>
                    </div>

                    <div class="silhouette-body">
                        {{-- Fake "Add Voucher" form --}}
                        <div class="silhouette-voucher-form">
                            <div class="silhouette-bar w-30 mb-3" style="height:14px;"></div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="silhouette-bar w-80"></div>
                                    <div class="silhouette-bar w-60" style="height:36px; border-radius:6px;"></div>
                                </div>
                                <div class="col-md-2">
                                    <div class="silhouette-bar w-80"></div>
                                    <div class="silhouette-bar w-60" style="height:36px; border-radius:6px;"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="silhouette-bar w-80"></div>
                                    <div class="silhouette-bar" style="height:36px; border-radius:6px;"></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="silhouette-bar w-60"></div>
                                    <div class="silhouette-bar w-80" style="height:36px; border-radius:6px;"></div>
                                </div>
                                <div class="col-md-9">
                                    <div class="silhouette-bar w-40"></div>
                                    <div class="silhouette-bar" style="height:36px; border-radius:6px;"></div>
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <div class="silhouette-bar w-30" style="height:38px; border-radius:20px;"></div>
                                    <div class="silhouette-bar w-30" style="height:38px; border-radius:20px; background:#d1fae5;"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Fake accordion rows --}}
                        <div class="silhouette-accordion">
                            <div class="silhouette-accordion-header">
                                <div class="d-flex align-items-center gap-2" style="width:60%;">
                                    <div class="silhouette-bar w-30" style="height:22px; border-radius:20px; background:#bbf7d0; margin:0; flex-shrink:0; width:40px;"></div>
                                    <div class="silhouette-bar w-60" style="height:14px; margin:0; flex:1;"></div>
                                </div>
                                <div class="d-flex gap-2">
                                    <div class="silhouette-bar" style="height:28px; width:28px; border-radius:6px; margin:0;"></div>
                                    <div class="silhouette-bar" style="height:28px; width:28px; border-radius:6px; margin:0;"></div>
                                    <div class="silhouette-bar" style="height:28px; width:28px; border-radius:6px; margin:0;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="silhouette-accordion" style="opacity:0.5;">
                            <div class="silhouette-accordion-header" style="background:#f8fafc;">
                                <div class="d-flex align-items-center gap-2" style="width:50%;">
                                    <div class="silhouette-bar" style="height:22px; border-radius:20px; background:#e2e8f0; margin:0; flex-shrink:0; width:40px;"></div>
                                    <div class="silhouette-bar w-40" style="height:14px; margin:0; flex:1;"></div>
                                </div>
                                <div class="d-flex gap-2">
                                    <div class="silhouette-bar" style="height:28px; width:28px; border-radius:6px; margin:0;"></div>
                                    <div class="silhouette-bar" style="height:28px; width:28px; border-radius:6px; margin:0;"></div>
                                    <div class="silhouette-bar" style="height:28px; width:28px; border-radius:6px; margin:0;"></div>
                                </div>
                            </div>
                        </div>
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
                Header changes are saved on submit — vouchers can be added after saving
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