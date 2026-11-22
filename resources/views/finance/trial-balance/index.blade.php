@extends('layouts.app')

@section('title', 'Trial Balance')

@section('content')
<div class="container-fluid trialBalancePage">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">

                {{-- Card Header --}}
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                    style="background-color: #d1fae5">
                    <span class="fw-bold">
                        <i class="fas fa-balance-scale me-2"></i>
                        Trial Balance
                        <span class="badge bg-success ms-1">Summary</span>
                    </span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="collapse"
                            data-bs-target="#filterPanel">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('trial-balance.export', request()->all()) }}"
                            class="btn btn-light btn-sm">
                            <i class="fas fa-file-csv me-1"></i> Export CSV
                        </a>
                        <form method="POST" action="{{ route('trial-balance.sync') }}" class="d-inline"
                            onsubmit="return confirm('Sync all COA current_balance with these all-time totals?')">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-sync me-1"></i> Sync COA Balance
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Alerts --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Filter Panel --}}
                    <div class="collapse mb-3 {{ request()->hasAny(['date_from','date_to','coa_id','parent_id']) ? 'show' : '' }}"
                        id="filterPanel">
                        <div class="card border-success">
                            <div class="card-body">
                                <form method="GET" action="{{ route('trial-balance.index') }}">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold">Date From</label>
                                            <input type="date" name="date_from" class="form-control form-control-sm"
                                                value="{{ $dateFrom }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold">Date To</label>
                                            <input type="date" name="date_to" class="form-control form-control-sm"
                                                value="{{ $dateTo }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold">Parent Account</label>
                                            <select name="parent_id" class="form-select form-select-sm">
                                                <option value="">All</option>
                                                @foreach ($parentList as $p)
                                                    <option value="{{ $p->kode_perkiraan }}"
                                                        {{ $parentId === $p->kode_perkiraan ? 'selected' : '' }}>
                                                        {{ $p->kode_perkiraan }} – {{ $p->nama ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold">COA</label>
                                            <select name="coa_id" class="form-select form-select-sm">
                                                <option value="">All</option>
                                                @foreach ($coaList as $c)
                                                    <option value="{{ $c->id_md_chart_of_account }}"
                                                        {{ $coaId === $c->id_md_chart_of_account ? 'selected' : '' }}>
                                                        {{ $c->no_account }} – {{ $c->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-search me-1"></i> Apply
                                            </button>
                                            <a href="{{ route('trial-balance.index') }}"
                                                class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-times me-1"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Active filter badge --}}
                    @if ($dateFrom || $dateTo)
                        <div class="alert alert-info py-2 d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Period:
                                <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</strong>
                                —
                                <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong>
                            </span>
                            @if ($coaId)
                                <span class="badge bg-primary">COA filter active</span>
                            @endif
                            @if ($parentId)
                                <span class="badge bg-primary">Parent: {{ $parentId }}</span>
                            @endif
                        </div>
                    @endif

                    {{-- Summary KPI Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center py-3">
                                    <div class="text-muted small mb-1">
                                        <i class="fas fa-arrow-circle-down text-success me-1"></i> Total IN
                                    </div>
                                    <div class="fs-5 fw-bold text-success">
                                        Rp {{ number_format($totalIn, 0, ',', '.') }}
                                    </div>
                                    <div class="text-muted" style="font-size:0.7rem">Invoice + Mutasi Penerimaan</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center py-3">
                                    <div class="text-muted small mb-1">
                                        <i class="fas fa-arrow-circle-up text-danger me-1"></i> Total OUT
                                    </div>
                                    <div class="fs-5 fw-bold text-danger">
                                        Rp {{ number_format($totalOut, 0, ',', '.') }}
                                    </div>
                                    <div class="text-muted" style="font-size:0.7rem">LPJ + Mutasi Pembayaran</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 h-100 {{ $totalBalance >= 0 ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}">
                                <div class="card-body text-center py-3">
                                    <div class="text-muted small mb-1">
                                        <i class="fas fa-balance-scale me-1"></i> Net Balance
                                    </div>
                                    <div class="fs-5 fw-bold {{ $totalBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($totalBalance, 0, ',', '.') }}
                                    </div>
                                    <div class="text-muted" style="font-size:0.7rem">IN – OUT</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table id="trialBalanceTable"
                            class="table table-bordered table-striped table-hover align-middle small">
                            <thead class="ledger-thead">
                                <tr>
                                    <th class="text-center" width="3%">No</th>
                                    <th>COA Code</th>
                                    <th>Account Name</th>
                                    <th>Parent Account</th>
                                    <th class="text-end">Opening Balance</th>
                                    <th class="text-end text-success">Total IN</th>
                                    <th class="text-end text-danger">Total OUT</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($entries as $i => $e)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td class="font-monospace">{{ $e['no_account'] }}</td>
                                        <td>
                                            <a href="{{ route('trial-balance.show', ['coaId'=>$e['coa_id'], 'date_from'=>$dateFrom, 'date_to'=>$dateTo]) }}"
                                               class="text-decoration-none fw-semibold text-dark">
                                                {{ $e['account_name'] }}
                                                <i class="fas fa-external-link-alt fa-xs text-muted ms-1"></i>
                                            </a>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $e['parent_code'] !== '-' ? $e['parent_code'] . ' – ' : '' }}{{ $e['parent_name'] }}
                                        </td>
                                        <td class="text-end text-muted">
                                            {{ number_format($e['opening_balance'], 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-semibold text-success">
                                            {{ number_format($e['total_in'], 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-semibold text-danger">
                                            {{ number_format($e['total_out'], 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold {{ $e['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($e['balance'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                            No transactions found for this period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($entries->count())
                                <tfoot class="ledger-tfoot">
                                    <tr>
                                        <th colspan="4" class="text-end">TOTAL</th>
                                        <th class="text-end">—</th>
                                        <th class="text-end text-success">
                                            {{ number_format($totalIn, 0, ',', '.') }}
                                        </th>
                                        <th class="text-end text-danger">
                                            {{ number_format($totalOut, 0, ',', '.') }}
                                        </th>
                                        <th class="text-end {{ $totalBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($totalBalance, 0, ',', '.') }}
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
.ledger-thead th {
    background-color: #064e3b !important;
    color: #d1fae5 !important;
    border-color: #065f46 !important;
    font-size: 0.75rem;
    letter-spacing: 0.03em;
    white-space: nowrap;
}
.ledger-thead th.text-success { color: #6ee7b7 !important; }
.ledger-thead th.text-danger  { color: #fca5a5 !important; }

.ledger-tfoot th {
    background-color: #f0fdf4 !important;
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
        if (typeof $.fn.DataTable !== 'undefined' && document.getElementById('trialBalanceTable')) {
            $('#trialBalanceTable').DataTable({
                pageLength: 50,
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: 0 },
                    { className: 'text-end', targets: [4, 5, 6, 7] },
                ],
                language: {
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ accounts',
                    emptyTable: 'No accounts found',
                },
            });
        }
    });
</script>
@endpush
