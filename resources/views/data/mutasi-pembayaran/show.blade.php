@extends('layouts.app')

@section('title', 'Detail Mutasi Pembayaran')

@push('styles')
    <style>
        .mutasiShowPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .mutasiShowPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        /* Info rows */
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            align-items: flex-start;
        }

        .info-row:last-child { border-bottom: none; }

        .info-label {
            font-weight: 600;
            color: #64748b;
            font-size: 0.875rem;
            min-width: 180px;
            flex-shrink: 0;
        }

        .info-value {
            color: #1e293b;
            font-size: 0.9rem;
            flex: 1;
        }

        /* VOUCHER CARD */
        .voucher-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .voucher-card-header {
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
        }

        .voucher-card-header .voucher-title-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .voucher-number-badge {
            background: #7c3aed;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .voucher-card-body { padding: 16px; }

        /* DETAIL TABLE */
        .table-detail thead th {
            background-color: #3b0764;
            color: white;
            border: 1px solid #3b0764;
            padding: 10px 8px;
            font-weight: 600;
            font-size: 0.8rem;
            text-align: center;
            vertical-align: middle;
        }

        .table-detail tbody td {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .table-detail tbody td.num-cell { text-align: right; }
        .table-detail tbody tr:hover td { background-color: #f8f9fa; }

        .table-detail tfoot td {
            background: #3b0764;
            color: white;
            font-weight: 700;
            padding: 10px 8px;
            font-size: 0.875rem;
        }

        .table-detail tfoot td.num-cell { text-align: right; }

        /* JENIS BADGE */
        .jenis-badge {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .jenis-tramper  { background: #d1fae5; color: #065f46; }
        .jenis-other    { background: #dbeafe; color: #1e40af; }
        .jenis-contract { background: #fef3c7; color: #92400e; }
        .jenis-general  { background: #f3e8ff; color: #6b21a8; }

        /* SUMMARY TOTALS */
        .grand-total-section {
            background: linear-gradient(135deg, #3b0764 0%, #7c3aed 100%);
            border-radius: 12px;
            padding: 20px 24px;
            color: white;
            margin-top: 16px;
        }

        .grand-total-section .total-label {
            font-size: 0.9rem;
            opacity: 0.85;
        }

        .grand-total-section .total-value {
            font-size: 1.2rem;
            font-weight: 700;
        }

        /* No detail placeholder */
        .no-detail-placeholder {
            text-align: center;
            padding: 30px 20px;
            color: #94a3b8;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mutasiShowPage">
        <div class="row">
            <div class="col-lg-12">

                <!-- ==================== PAGE HEADER ==================== -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #ede9fe">
                        <span class="fw-bold">
                            <i class="fas fa-money-bill-wave me-2"></i>Detail Mutasi Pembayaran
                        </span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('mutasi-pembayaran.edit', $mutasiPembayaran->id) }}"
                                class="btn btn-sm text-white" style="background:#7c3aed;">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('mutasi-pembayaran.index') }}"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ==================== HEADER INFO ==================== -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color: #ede9fe">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Mutasi Pembayaran</h6>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">Nomor Dokumen</span>
                                    <span class="info-value">
                                        <span class="badge text-white fs-6 px-3 py-2"
                                            style="background:#7c3aed; letter-spacing:1px;">
                                            {{ $mutasiPembayaran->nomor ?? '-' }}
                                        </span>
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Tanggal</span>
                                    <span class="info-value">
                                        {{ $mutasiPembayaran->tanggal
                                            ? \Carbon\Carbon::parse($mutasiPembayaran->tanggal)->translatedFormat('d F Y')
                                            : '-' }}
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">No. Cek / BG</span>
                                    <span class="info-value">{{ $mutasiPembayaran->no_cek ?? '-' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Kurs</span>
                                    <span class="info-value">
                                        @if($mutasiPembayaran->kurs && $mutasiPembayaran->kurs != 0)
                                            Rp {{ number_format($mutasiPembayaran->kurs, 2, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">Chart of Account</span>
                                    <span class="info-value">
                                        @if($mutasiPembayaran->chartOfAccount)
                                            <span class="badge bg-light text-dark border me-1">
                                                {{ $mutasiPembayaran->chartOfAccount->kode_akun }}
                                            </span>
                                            {{ $mutasiPembayaran->chartOfAccount->nama_akun }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Jumlah Voucher</span>
                                    <span class="info-value">
                                        <span class="badge bg-secondary">
                                            {{ $mutasiPembayaran->vouchers->count() }} voucher
                                        </span>
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Dibuat</span>
                                    <span class="info-value">
                                        {{ $mutasiPembayaran->created_at
                                            ? $mutasiPembayaran->created_at->translatedFormat('d F Y H:i')
                                            : '-' }}
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Terakhir Update</span>
                                    <span class="info-value">
                                        {{ $mutasiPembayaran->updated_at
                                            ? $mutasiPembayaran->updated_at->translatedFormat('d F Y H:i')
                                            : '-' }}
                                    </span>
                                </div>
                            </div>
                            @if($mutasiPembayaran->memo)
                                <div class="col-12">
                                    <div class="info-row">
                                        <span class="info-label">Memo</span>
                                        <span class="info-value">{{ $mutasiPembayaran->memo }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ==================== VOUCHERS ==================== -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #ede9fe">
                        <h6 class="mb-0">
                            <i class="fas fa-layer-group me-2"></i>Voucher Pembayaran
                            <span class="badge bg-secondary ms-2">{{ $mutasiPembayaran->vouchers->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-4">

                        @php
                            $grandNilai   = 0;
                            $grandPj      = 0;
                            $grandSelisih = 0;
                        @endphp

                        @forelse($mutasiPembayaran->vouchers->sortBy('urutan') as $voucher)
                            @php
                                $details      = $voucher->details ?? collect([]);
                                $vNilai       = $details->sum('nilai');
                                $vPj          = $details->sum('pj');
                                $vSelisih     = $details->sum('selisih');
                                $grandNilai   += $vNilai;
                                $grandPj      += $vPj;
                                $grandSelisih += $vSelisih;
                            @endphp

                            <div class="voucher-card">
                                <div class="voucher-card-header">
                                    <div class="voucher-title-group">
                                        <span class="voucher-number-badge">#{{ $voucher->urutan }}</span>
                                        <strong>{{ $voucher->nomor_voucher }}</strong>
                                        @if($voucher->keterangan)
                                            <span class="text-muted small">— {{ $voucher->keterangan }}</span>
                                        @endif
                                    </div>
                                    <div class="text-end small text-muted">
                                        {{ $details->count() }} detail &nbsp;|&nbsp;
                                        Total: <strong class="text-dark">
                                            Rp {{ number_format($vNilai, 2, ',', '.') }}
                                        </strong>
                                    </div>
                                </div>

                                <div class="voucher-card-body">
                                    @if($details->isEmpty())
                                        <div class="no-detail-placeholder">
                                            <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                                            <p class="mb-0">Belum ada detail</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-detail table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th width="4%">No</th>
                                                        <th width="8%">Jenis</th>
                                                        <th>JO</th>
                                                        <th>Kasbon</th>
                                                        <th class="text-end">Nilai</th>
                                                        <th class="text-end">PJ</th>
                                                        <th class="text-end">Selisih</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($details->sortBy('id') as $idx => $detail)
                                                        @php
                                                            $joText = match($detail->jenis) {
                                                                'tramper'  => $detail->joTram?->nomor  ?? $detail->joTram?->no_jo  ?? '-',
                                                                'other'    => $detail->joOther?->nomor ?? $detail->joOther?->no_jo ?? '-',
                                                                'contract' => $detail->joCont?->nomor  ?? $detail->joCont?->no_jo  ?? '-',
                                                                default    => '-',
                                                            };
                                                            $kasbonText = match($detail->jenis) {
                                                                'tramper'  => $detail->kasbonTram?->nomor  ?? $detail->kasbonTram?->no_kasbon  ?? '-',
                                                                'other'    => $detail->kasbonOther?->nomor ?? $detail->kasbonOther?->no_kasbon ?? '-',
                                                                'contract' => $detail->kasbonCont?->nomor  ?? $detail->kasbonCont?->no_kasbon  ?? '-',
                                                                'general'  => $detail->kasbonGen?->nomor   ?? $detail->kasbonGen?->no_kasbon   ?? '-',
                                                                default    => '-',
                                                            };
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $idx + 1 }}</td>
                                                            <td class="text-center">
                                                                <span class="jenis-badge jenis-{{ $detail->jenis }}">
                                                                    {{ $detail->jenis }}
                                                                </span>
                                                            </td>
                                                            <td class="small">{{ $joText }}</td>
                                                            <td class="small">{{ $kasbonText }}</td>
                                                            <td class="num-cell">
                                                                {{ number_format($detail->nilai ?? 0, 2, ',', '.') }}
                                                            </td>
                                                            <td class="num-cell">
                                                                {{ number_format($detail->pj ?? 0, 2, ',', '.') }}
                                                            </td>
                                                            <td class="num-cell">
                                                                {{ number_format($detail->selisih ?? 0, 2, ',', '.') }}
                                                            </td>
                                                            <td class="small">{{ $detail->keterangan ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4" class="text-end fw-bold">
                                                            Subtotal Voucher
                                                        </td>
                                                        <td class="num-cell">
                                                            {{ number_format($vNilai, 2, ',', '.') }}
                                                        </td>
                                                        <td class="num-cell">
                                                            {{ number_format($vPj, 2, ',', '.') }}
                                                        </td>
                                                        <td class="num-cell">
                                                            {{ number_format($vSelisih, 2, ',', '.') }}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @endif
                                </div>{{-- end voucher-card-body --}}
                            </div>{{-- end voucher-card --}}
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-4x mb-3 d-block"></i>
                                <p class="fw-bold">Belum ada voucher</p>
                                <small>Klik "Edit" untuk menambahkan voucher</small>
                            </div>
                        @endforelse

                        <!-- GRAND TOTAL -->
                        @if($mutasiPembayaran->vouchers->count() > 0)
                            <div class="grand-total-section">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <div class="total-label mb-1">Grand Total Nilai</div>
                                        <div class="total-value">
                                            Rp {{ number_format($grandNilai, 2, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="total-label mb-1">Grand Total PJ (Realisasi)</div>
                                        <div class="total-value">
                                            Rp {{ number_format($grandPj, 2, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="total-label mb-1">Grand Total Selisih</div>
                                        <div class="total-value">
                                            Rp {{ number_format($grandSelisih, 2, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <!-- BOTTOM ACTIONS -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('mutasi-pembayaran.index') }}"
                        class="btn btn-outline-secondary px-4 py-2 fw-semibold">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                    <a href="{{ route('mutasi-pembayaran.edit', $mutasiPembayaran->id) }}"
                        class="btn text-white px-4 py-2 fw-semibold" style="background:#7c3aed; border-radius:10px;">
                        <i class="fas fa-edit me-1"></i> Edit Mutasi
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection