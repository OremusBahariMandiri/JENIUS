@extends('layouts.app')
@section('title', 'SOA – ' . $customer->customer)

@section('content')
<div class="container-fluid soaShowPage">

    {{-- Header --}}
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background-color:#d1fae5">
            <span class="fw-bold text-dark">
                <i class="fas fa-address-card me-2"></i>
                Statement of Account
            </span>
            <div class="d-flex gap-2">
                <a href="{{ route('soa.pdf', $customer->id_md_cust) }}"
                   class="btn btn-sm btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
                <a href="{{ route('soa.index') }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        {{-- Customer info --}}
        <div class="card-body" style="background:#f0fdf4">

            {{-- Company name - full width --}}
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px;height:44px;background:#bbf7d0">
                    <i class="fas fa-building text-success"></i>
                </div>
                <div>
                    <div class="fs-5 fw-bold text-dark">{{ $customer->customer }}</div>
                    @if($customer->code)
                        <span class="badge bg-success bg-opacity-25 text-success border border-success" style="font-size:.7rem">
                            Code: {{ $customer->code }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Info cards + Summary side by side --}}
            <div class="row g-2 small">

                {{-- Alamat - spans 8 cols --}}
                <div class="col-lg-8">
                    <div class="rounded p-2 d-flex gap-2 align-items-start h-100" style="background:#bbf7d0">
                        <i class="fas fa-map-marker-alt text-success mt-1" style="width:14px;flex-shrink:0"></i>
                        <div>
                            <div class="text-muted" style="font-size:.68rem;line-height:1.2">Address</div>
                            <div class="text-dark">{{ $customer->address ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Total Invoice --}}
                <div class="col-lg-4">
                    <div class="rounded p-2 d-flex justify-content-between align-items-center h-100" style="background:#bbf7d0">
                        <span class="text-muted">Total Invoice</span>
                        <span class="fw-bold text-dark">{{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Telepon --}}
                <div class="col-lg-4 col-sm-6">
                    <div class="rounded p-2 d-flex gap-2 align-items-start h-100" style="background:#bbf7d0">
                        <i class="fas fa-phone text-success mt-1" style="width:14px;flex-shrink:0"></i>
                        <div>
                            <div class="text-muted" style="font-size:.68rem;line-height:1.2">Phone</div>
                            <div class="text-dark">{{ $customer->phone ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="col-lg-4 col-sm-6">
                    <div class="rounded p-2 d-flex gap-2 align-items-start h-100" style="background:#bbf7d0">
                        <i class="fas fa-envelope text-success mt-1" style="width:14px;flex-shrink:0"></i>
                        <div>
                            <div class="text-muted" style="font-size:.68rem;line-height:1.2">Email</div>
                            @if($customer->email)
                                <a href="mailto:{{ $customer->email }}" class="text-dark text-decoration-none">{{ $customer->email }}</a>
                            @else
                                <span class="text-dark">-</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Total Received --}}
                <div class="col-lg-4">
                    <div class="rounded p-2 d-flex justify-content-between align-items-center h-100" style="background:#bbf7d0">
                        <span class="text-muted">Total Received</span>
                        <span class="fw-bold text-success">{{ number_format($grandDiterima, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- NPWP --}}
                <div class="col-lg-4 col-sm-6">
                    <div class="rounded p-2 d-flex gap-2 align-items-start h-100" style="background:#bbf7d0">
                        <i class="fas fa-id-card text-success mt-1" style="width:14px;flex-shrink:0"></i>
                        <div>
                            <div class="text-muted" style="font-size:.68rem;line-height:1.2">NPWP</div>
                            <div class="text-dark fw-semibold">{{ $customer->npwp ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Website --}}
                <div class="col-lg-4 col-sm-6">
                    <div class="rounded p-2 d-flex gap-2 align-items-start h-100" style="background:#bbf7d0">
                        <i class="fas fa-globe text-success mt-1" style="width:14px;flex-shrink:0"></i>
                        <div>
                            <div class="text-muted" style="font-size:.68rem;line-height:1.2">Website</div>
                            @if($customer->website)
                                <a href="{{ $customer->website }}" target="_blank" class="text-dark text-decoration-none">{{ $customer->website }}</a>
                            @else
                                <span class="text-dark">-</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Balance Due --}}
                <div class="col-lg-4">
                    <div class="rounded p-2 d-flex justify-content-between align-items-center h-100"
                         style="background:{{ $grandSisa > 0 ? '#fee2e2' : '#bbf7d0' }}">
                        <div>
                            <div class="text-muted">Balance Due</div>
                            <div style="font-size:.68rem" class="text-muted">
                                <i class="fas fa-file-invoice me-1"></i>{{ $invoices->count() }} invoice(s)
                            </div>
                        </div>
                        <span class="fw-bold {{ $grandSisa > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($grandSisa, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @if($customer->note)
                <div class="col-12">
                    <div class="rounded p-2 d-flex gap-2 align-items-start" style="background:#bbf7d0">
                        <i class="fas fa-sticky-note text-success mt-1" style="width:14px;flex-shrink:0"></i>
                        <div>
                            <div class="text-muted" style="font-size:.68rem;line-height:1.2">Note</div>
                            <div class="text-dark fst-italic">{{ $customer->note }}</div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Invoice detail table --}}
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background-color:#d1fae5">
            <span class="fw-bold text-dark">
                <i class="fas fa-file-invoice me-2"></i>Outstanding Invoices
            </span>
            <span class="badge bg-danger rounded-pill">{{ $invoices->count() }} invoice(s)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="soaDetailTable" class="table table-bordered table-hover align-middle small mb-0">
                    <thead style="background:#ecfdf5">
                        <tr>
                            <th class="text-center" width="3%">No</th>
                            <th>Invoice No.</th>
                            <th>JO No.</th>
                            <th class="text-center">Type</th>
                            <th>Title</th>
                            <th class="text-center">Invoice Date</th>
                            <th class="text-center">Due Date</th>
                            <th class="text-end">DPP</th>
                            <th class="text-end">VAT</th>
                            <th class="text-end">Reim.</th>
                            <th class="text-end">Total Inv.</th>
                            <th class="text-end">Received</th>
                            <th class="text-end">Balance Due</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="6%">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $i => $row)
                        @php
                            $isOverdue = $row['due_date'] && \Carbon\Carbon::parse($row['due_date'])->isPast() && $row['status'] !== 'closed';
                        @endphp
                        <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td class="fw-semibold text-success">{{ $row['no_invoice'] }}</td>
                            <td>{{ $row['no_jo'] ?? '-' }}</td>
                            <td class="text-center">{{ ucfirst($row['type']) }}</td>
                            <td>{{ $row['jo_title'] ?? '-' }}</td>
                            <td class="text-center">
                                {{ $row['tgl_invoice'] ? \Carbon\Carbon::parse($row['tgl_invoice'])->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">
                                @if($row['due_date'])
                                    <span class="{{ $isOverdue ? 'text-danger fw-semibold' : '' }}">
                                        {{ \Carbon\Carbon::parse($row['due_date'])->format('d/m/Y') }}
                                    </span>
                                    @if($isOverdue)
                                        <br><span class="badge bg-danger" style="font-size:.6rem">Overdue</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($row['dpp'] > 0) {{ number_format($row['dpp'], 0, ',', '.') }}
                                @else <span class="text-muted">-</span> @endif
                            </td>
                            <td class="text-end">
                                @if($row['ppn'] > 0) {{ number_format($row['ppn'], 0, ',', '.') }}
                                @else <span class="text-muted">-</span> @endif
                            </td>
                            <td class="text-end">
                                @if($row['reimbursement'] > 0) {{ number_format($row['reimbursement'], 0, ',', '.') }}
                                @else <span class="text-muted">-</span> @endif
                            </td>
                            <td class="text-end fw-semibold">
                                {{ number_format($row['total_invoice'], 0, ',', '.') }}
                            </td>
                            <td class="text-end">
                                @if($row['diterima'] > 0)
                                    <span class="text-success fw-semibold">{{ number_format($row['diterima'], 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold {{ $row['sisa'] > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($row['sisa'], 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @php
                                    $stBadge = match($row['status']) {
                                        'draft'  => 'bg-warning text-dark',
                                        'sent'   => 'bg-primary',
                                        'closed' => 'bg-secondary',
                                        default  => 'bg-light text-dark border',
                                    };
                                @endphp
                                <span class="badge {{ $stBadge }}" style="font-size:.68rem">
                                    {{ ucfirst($row['status']) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route($row['route'], $row['inv_id']) }}"
                                   class="btn btn-sm btn-outline-success" title="View Invoice" target="_blank">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15" class="text-center text-muted py-5">
                                <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                                No outstanding invoices for this customer.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($invoices->count() > 0)
                    <tfoot style="background:#ecfdf5; font-weight:bold">
                        <tr>
                            <td colspan="9" class="text-end text-dark">Grand Total</td>
                            <td class="text-end">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                            <td class="text-end text-success">{{ number_format($grandDiterima, 0, ',', '.') }}</td>
                            <td class="text-end text-danger">{{ number_format($grandSisa, 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#soaDetailTable').DataTable({
        pageLength: 50,
        responsive: true,
        order: [[4, 'desc']],
        columnDefs: [{ orderable: false, targets: [0, 1, 13] }],
        language: {
            search: 'Search:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'No entries to show',
            infoFiltered: '(filtered from _MAX_ total entries)',
            zeroRecords: 'No matching records found',
            paginate: { next: '›', previous: '‹' },
        },
    });
});
</script>
@endpush
