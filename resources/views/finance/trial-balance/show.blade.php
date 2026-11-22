@extends('layouts.app')

@section('title', 'Trial Balance – ' . $coa->account_name)

@section('content')
<div class="container-fluid trialBalanceShowPage">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">

                {{-- Card Header --}}
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                    style="background-color: #d1fae5">
                    <span class="fw-bold">
                        <i class="fas fa-book-open me-2"></i>
                        Account Ledger
                        <span class="badge bg-success ms-1">{{ $coa->no_account }}</span>
                        <span class="badge bg-secondary ms-1">{{ $coa->account_name }}</span>
                    </span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('trial-balance.index', ['date_from'=>$dateFrom,'date_to'=>$dateTo]) }}"
                            class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Trial Balance
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Date Filter --}}
                    <form method="GET" class="mb-3">
                        <input type="hidden" name="coa_id" value="{{ $coaId }}">
                        <div class="d-flex flex-wrap gap-2 align-items-end">
                            <div>
                                <label class="form-label small fw-semibold mb-1">Date From</label>
                                <input type="date" name="date_from" class="form-control form-control-sm"
                                    value="{{ $dateFrom }}">
                            </div>
                            <div>
                                <label class="form-label small fw-semibold mb-1">Date To</label>
                                <input type="date" name="date_to" class="form-control form-control-sm"
                                    value="{{ $dateTo }}">
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-search me-1"></i> Apply
                            </button>
                        </div>
                    </form>

                    {{-- COA Info Bar --}}
                    <div class="alert alert-light border py-2 mb-3 d-flex flex-wrap gap-3">
                        <span><i class="fas fa-tag text-muted me-1"></i>
                            <strong>COA:</strong> {{ $coa->no_account }} – {{ $coa->account_name }}</span>
                        @if ($coa->parentAccount)
                            <span><i class="fas fa-sitemap text-muted me-1"></i>
                                <strong>Parent:</strong> {{ $coa->parentAccount->kode_perkiraan }} – {{ $coa->parentAccount->nama }}</span>
                        @endif
                        <span><i class="fas fa-calendar-alt text-muted me-1"></i>
                            <strong>Period:</strong>
                            {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} —
                            {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                        </span>
                        <span><i class="fas fa-list-ol text-muted me-1"></i>
                            <strong>Transactions:</strong> {{ $ledger->count() }}</span>
                    </div>

                    {{-- KPI Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="text-muted small">Opening Balance</div>
                                    <div class="fw-bold">Rp {{ number_format($coa->opening_balance ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-success bg-opacity-10">
                                <div class="card-body text-center py-2">
                                    <div class="text-muted small"><i class="fas fa-arrow-down text-success me-1"></i>Total IN</div>
                                    <div class="fw-bold text-success">Rp {{ number_format($totalIn, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-danger bg-opacity-10">
                                <div class="card-body text-center py-2">
                                    <div class="text-muted small"><i class="fas fa-arrow-up text-danger me-1"></i>Total OUT</div>
                                    <div class="fw-bold text-danger">Rp {{ number_format($totalOut, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            @php $finalBalance = ($coa->opening_balance ?? 0) + $totalIn - $totalOut; @endphp
                            <div class="card border-0 {{ $finalBalance >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10">
                                <div class="card-body text-center py-2">
                                    <div class="text-muted small">Closing Balance</div>
                                    <div class="fw-bold {{ $finalBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($finalBalance, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ledger Table --}}
                    <div class="table-responsive">
                        <table id="ledgerTable"
                            class="table table-bordered table-striped table-hover align-middle small">
                            <thead class="ledger-thead">
                                <tr>
                                    <th class="text-center" width="3%">No</th>
                                    <th width="9%">Date</th>
                                    <th width="14%">Transaction No</th>
                                    <th width="12%">JO Number</th>
                                    <th>Description</th>
                                    <th width="8%" class="text-center">Type</th>
                                    <th width="9%" class="text-center">Category</th>
                                    <th width="12%" class="text-end text-success">IN</th>
                                    <th width="12%" class="text-end text-danger">OUT</th>
                                    <th width="12%" class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ledger as $i => $row)
                                    <tr>
                                        <td class="text-center text-muted">{{ $i + 1 }}</td>
                                        <td class="text-nowrap">
                                            {{ \Carbon\Carbon::parse($row['tanggal'])->format('d M Y') }}
                                        </td>
                                        <td class="font-monospace small">{{ $row['no_transaksi'] ?? '—' }}</td>
                                        <td class="font-monospace small text-primary">{{ $row['no_jo'] ?? '—' }}</td>
                                        <td>{{ $row['keterangan'] ?? '—' }}</td>
                                        @php
                                            $jobType = $row['job_type'] ?? '—';
                                            $category = $row['category'] ?? '—';
                                            $typeBadge = match($jobType) {
                                                'Contract' => 'bg-primary',
                                                'Tramper'  => 'bg-warning text-dark',
                                                'Other'    => 'bg-info text-dark',
                                                'General'  => 'bg-secondary',
                                                default    => 'bg-light text-dark border',
                                            };
                                            $catBadge = match($category) {
                                                'LPJ'              => 'bg-orange text-white',
                                                'Invoice'          => 'bg-success',
                                                'Payment Mutation' => 'bg-danger',
                                                'Receipt Mutation' => 'bg-teal text-white',
                                                default            => 'bg-light text-dark border',
                                            };
                                            $catIcon = match($category) {
                                                'LPJ'              => 'fa-file-invoice',
                                                'Invoice'          => 'fa-receipt',
                                                'Payment Mutation' => 'fa-arrow-circle-up',
                                                'Receipt Mutation' => 'fa-arrow-circle-down',
                                                default            => 'fa-circle',
                                            };
                                        @endphp
                                        <td class="text-center">
                                            <span class="badge {{ $typeBadge }}" style="font-size:0.65rem">
                                                {{ $jobType }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $catBadge }}" style="font-size:0.65rem">
                                                <i class="fas {{ $catIcon }} me-1"></i>{{ $category }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold text-success">
                                            {{ $row['amount_in'] > 0 ? number_format($row['amount_in'], 0, ',', '.') : '' }}
                                        </td>
                                        <td class="text-end fw-semibold text-danger">
                                            {{ $row['amount_out'] > 0 ? number_format($row['amount_out'], 0, ',', '.') : '' }}
                                        </td>
                                        <td class="text-end fw-bold {{ $row['saldo'] >= 0 ? 'text-dark' : 'text-danger' }}">
                                            {{ number_format($row['saldo'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-5">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                            No transactions found for this COA in the selected period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($ledger->count())
                            <tfoot class="ledger-tfoot">
                                <tr>
                                    <th colspan="7" class="text-end">TOTAL</th>
                                    <th class="text-end text-success">{{ number_format($totalIn, 0, ',', '.') }}</th>
                                    <th class="text-end text-danger">{{ number_format($totalOut, 0, ',', '.') }}</th>
                                    <th class="text-end {{ $finalBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($finalBalance, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                </div>{{-- card-body --}}
            </div>{{-- card --}}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-orange { background-color: #f97316 !important; }
.bg-teal   { background-color: #0d9488 !important; }

/* ── Ledger thead / tfoot ───────────────────────────────────── */
.ledger-thead th {
    background-color: #064e3b !important;   /* emerald-900 */
    color: #d1fae5 !important;              /* emerald-100 */
    border-color: #065f46 !important;
    font-size: 0.75rem;
    letter-spacing: 0.03em;
    white-space: nowrap;
}
.ledger-thead th.text-success { color: #6ee7b7 !important; }
.ledger-thead th.text-danger  { color: #fca5a5 !important; }

.ledger-tfoot th {
    background-color: #f0fdf4 !important;   /* emerald-50 */
    color: #064e3b !important;
    border-color: #a7f3d0 !important;
    border-top: 2px solid #059669 !important;
    font-size: 0.78rem;
}
.ledger-tfoot th.text-success { color: #047857 !important; }
.ledger-tfoot th.text-danger  { color: #dc2626 !important; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $.fn.DataTable !== 'undefined' && document.getElementById('ledgerTable')) {
            $('#ledgerTable').DataTable({
                pageLength: 50,
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: 0 },
                    { className: 'text-end', targets: [7, 8, 9] },
                ],
                language: {
                    search: 'Search:',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                },
            });
        }
    });
</script>
@endpush
