@extends('layouts.app')

@section('title', 'Customer Receivables Recap – Contract')

@section('content')
    <div class="container-fluid rekapPiutangContractPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">

                    {{-- Card Header --}}
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            Customer Receivables Recap
                            <span class="badge bg-success ms-1">Contract</span>
                        </span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light btn-sm" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light btn-sm" onclick="exportExcel()">
                                <i class="fas fa-file-excel me-1"></i> Export
                            </button>
                        </div>
                    </div>

                    <div class="card-body">

                        {{-- Session Alerts --}}
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

                        {{-- Active Filter Badge --}}
                        @if (!empty($currentFilters) && array_filter($currentFilters))
                            <div class="alert alert-info alert-dismissible fade show">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Active Filters:</strong>
                                @if (!empty($currentFilters['no_jo']))
                                    <span class="badge bg-primary ms-1">JO No: {{ $currentFilters['no_jo'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_cust']))
                                    @php
                                        $filterCust = $customers->firstWhere('id_md_cust', $currentFilters['id_md_cust']);
                                    @endphp
                                    <span class="badge bg-primary ms-1">Customer: {{ $filterCust?->customer ?? $currentFilters['id_md_cust'] }}</span>
                                @endif
                                @if (!empty($currentFilters['status']))
                                    <span class="badge bg-primary ms-1">Status: {{ $currentFilters['status'] }}</span>
                                @endif
                                <a href="{{ route('rekap-piutang-contract.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Reset
                                </a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        {{-- Table --}}
                        <div class="table-responsive">
                            <table id="rekapPiutangContractTable" class="table table-bordered table-striped table-hover align-middle small">
                                <thead class="table-light">
                                    <tr>
                                        <th width="3%" class="text-center">No</th>
                                        <th>JO Number</th>
                                        <th>Customer</th>
                                        <th>Description</th>
                                        <th class="text-center">Start Date</th>
                                        <th class="text-center">End Date</th>
                                        <th class="text-end">Total Selling Price</th>
                                        <th class="text-center" data-bs-toggle="tooltip" title="Remaining balance: Total Selling minus DP and Payment Receipt">Total Invoice</th>
                                        <th class="text-center" data-bs-toggle="tooltip" title="Down Payment received">Down Payment (DP)</th>
                                        <th class="text-center" data-bs-toggle="tooltip" title="Payment receipts (excluding DP)">Payment Receipt</th>
                                        <th class="text-center" data-bs-toggle="tooltip" title="Total Receivables = DP + Payment Receipt - Selling Price (negatif = masih ada piutang)">Total Receivables</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" width="8%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rows as $i => $row)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td>
                                                <a href="{{ route('jo-contract.show', $row['id_jo_cont']) }}"
                                                   class="text-decoration-none text-dark">
                                                    {{ $row['no_jo_cont'] ?? '-' }}
                                                </a>
                                            </td>
                                            <td>{{ $row['customer'] }}</td>
                                            <td>{{ $row['keterangan'] }}</td>
                                            <td class="text-center">
                                                {{ $row['date_start'] ? \Carbon\Carbon::parse($row['date_start'])->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $row['date_end'] ? \Carbon\Carbon::parse($row['date_end'])->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="text-end" style="min-width:160px">
                                                <div class="">{{ number_format($row['total_selling'], 0, ',', '.') }}</div>
                                                <div class="mt-1" style="font-size:.75rem;line-height:1.6">
                                                    @if ($row['total_idr'] > 0)
                                                        <span class="text-muted">IDR:</span>
                                                        <span class="text-dark">{{ number_format($row['total_idr'], 0, ',', '.') }}</span><br>
                                                    @endif
                                                    @if ($row['total_usd'] > 0)
                                                        <span class="text-muted">USD:</span>
                                                        <span class="text-success">{{ number_format($row['total_usd'], 2, ',', '.') }}</span><br>
                                                        @if ($row['kurs_usd'])
                                                            <span class="text-muted">Kurs:</span>
                                                            <span class="text-secondary">{{ number_format($row['kurs_usd'], 0, ',', '.') }}
                                                            @if ($row['tgl_kurs'])
                                                                <span class="text-muted fst-italic">({{ \Carbon\Carbon::parse($row['tgl_kurs'])->format('d/m/Y') }})</span>
                                                            @endif
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center text-dark">
                                                {{ number_format($row['total_invoice'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($row['uang_muka'] > 0)
                                                    <span class="text-dark">
                                                        {{ number_format($row['uang_muka'], 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($row['payment_receipt'] > 0)
                                                    <span class="text-dark">
                                                        {{ number_format($row['payment_receipt'], 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="{{ $row['total_piutang'] < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($row['total_piutang'], 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($row['status'] === 'Lunas')
                                                    <span class="badge bg-success">Paid Off</span>
                                                @elseif ($row['status'] === 'Partial')
                                                    <span class="badge bg-warning text-dark">Partial</span>
                                                @else
                                                    <span class="badge bg-danger">Outstanding</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('invoice-contract.create') }}?id_jo_cont={{ $row['id_jo_cont'] }}"
                                                   class="btn btn-sm btn-outline-success" title="Create Invoice">
                                                    <i class="fas fa-file-invoice"></i> Create Invoice
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                No data available.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                    </div>{{-- /card-body --}}
                </div>{{-- /card --}}
            </div>
        </div>
    </div>

    {{-- Filter Modal --}}
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form method="GET" action="{{ route('rekap-piutang-contract.index') }}">
                    <div class="modal-header" style="background-color: #d1fae5">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-filter me-2"></i>Filter Receivables
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">JO Number</label>
                            <input type="text" name="no_jo" class="form-control"
                                   placeholder="Search JO number..."
                                   value="{{ $currentFilters['no_jo'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Customer</label>
                            <select name="id_md_cust" class="form-select select2-customer">
                                <option value="">-- All Customers --</option>
                                @foreach ($customers as $cust)
                                    <option value="{{ $cust->id_md_cust }}"
                                        {{ ($currentFilters['id_md_cust'] ?? '') == $cust->id_md_cust ? 'selected' : '' }}>
                                        {{ $cust->customer }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="">-- All Status --</option>
                                <option value="Outstanding" {{ ($currentFilters['status'] ?? '') === 'Outstanding' ? 'selected' : '' }}>Outstanding</option>
                                <option value="Partial"     {{ ($currentFilters['status'] ?? '') === 'Partial'     ? 'selected' : '' }}>Partial</option>
                                <option value="Lunas"       {{ ($currentFilters['status'] ?? '') === 'Lunas'       ? 'selected' : '' }}>Paid Off</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('rekap-piutang-contract.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-search me-1"></i>Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Open filter modal
    document.getElementById('filterButton').addEventListener('click', function () {
        new bootstrap.Modal(document.getElementById('filterModal')).show();
    });

    // DataTable
    $(document).ready(function () {
        $('#rekapPiutangContractTable').DataTable({
            pageLength: 25,
            responsive: true,
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [12] },
            ],
            language: {
                search: 'Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ records',
                paginate: { first: 'First', last: 'Last', next: '›', previous: '‹' },
            },
        });

        // Select2 for customer filter
        $('.select2-customer').select2({
            theme: 'bootstrap-5',
            placeholder: '-- All Customers --',
            allowClear: true,
            dropdownParent: $('#filterModal'),
        });
    });

    // Export placeholder (extend as needed)
    function exportExcel() {
        const url = new URL(window.location.href);
        url.searchParams.set('export', 'excel');
        alert('Export feature coming soon.');
    }
</script>
@endpush
