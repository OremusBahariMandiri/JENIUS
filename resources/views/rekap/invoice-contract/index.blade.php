@extends('layouts.app')
@section('title', 'Invoice List – Contract')

@section('content')
<div class="container-fluid invoiceContractPage">
    <div class="card shadow">
        <div class="card-header text-black d-flex justify-content-between align-items-center"
             style="background-color:#d1fae5">
            <span class="fw-bold">
                <i class="fas fa-file-invoice me-2"></i>
                Invoice List
                <span class="badge bg-success ms-1">Contract</span>
            </span>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-light btn-sm" id="filterButton">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('invoice-contract.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus me-1"></i>Create Invoice
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table id="invoiceContractTable" class="table table-bordered table-striped table-hover align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="3%">No</th>
                            <th>Inv. No</th>
                            <th>JO No</th>
                            <th>Customer</th>
                            <th class="text-center">Tgl Invoice</th>
                            <th class="text-center">Jatuh Tempo</th>
                            <th class="text-end">DPP</th>
                            <th class="text-end">PPN</th>
                            <th class="text-end">Reimbursement</th>
                            <th class="text-end">Total Invoice</th>
                            <th class="text-center">Diterima</th>
                            <th>Close Note</th>
                            <th>Reopen Note</th>
                            <th class="text-center" width="8%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $i => $row)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>
                                <span class="fw-semibold text-success">{{ $row['no_invoice'] }}</span>
                                @if($row['status'] !== 'draft')
                                    <br><span class="badge bg-{{ $row['status'] === 'closed' ? 'secondary' : 'success' }} mt-1" style="font-size:.68rem">{{ ucfirst($row['status']) }}</span>
                                @else
                                    <br><span class="badge bg-warning text-dark mt-1" style="font-size:.68rem">Draft</span>
                                @endif
                            </td>
                            <td>{{ $row['no_jo'] ?? '-' }}</td>
                            <td>{{ $row['customer'] }}</td>
                            <td class="text-center">
                                {{ $row['tgl_invoice'] ? \Carbon\Carbon::parse($row['tgl_invoice'])->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">
                                @if($row['due_date'])
                                    @php $dd = \Carbon\Carbon::parse($row['due_date']); @endphp
                                    <span class="{{ $dd->isPast() && $row['status'] !== 'closed' ? 'text-danger fw-semibold' : '' }}">
                                        {{ $dd->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($row['dpp'] > 0)
                                    {{ number_format($row['dpp'], 0, ',', '.') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($row['ppn'] > 0)
                                    {{ number_format($row['ppn'], 0, ',', '.') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($row['reimbursement'] > 0)
                                    {{ number_format($row['reimbursement'], 0, ',', '.') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
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
                            <td class="small text-muted" style="max-width:120px;white-space:normal">{{ $row['close_note'] ?? '-' }}</td>
                            <td class="small text-muted" style="max-width:120px;white-space:normal">{{ $row['reopen_note'] ?? '-' }}</td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('invoice-contract.edit', $row['id']) }}"
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $row['id'] }}, '{{ $row['no_invoice'] }}')"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="14" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Belum ada invoice.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Filter Modal --}}
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="{{ route('invoice-contract.index') }}">
                <div class="modal-header" style="background-color:#d1fae5">
                    <h5 class="modal-title fw-bold"><i class="fas fa-filter me-2"></i>Filter Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Invoice</label>
                        <input type="text" name="no_invoice" class="form-control"
                               value="{{ $currentFilters['no_invoice'] ?? '' }}" placeholder="Cari nomor invoice...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Customer</label>
                        <select name="id_md_cust" class="form-select s2-cust">
                            <option value="">-- Semua Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id_md_cust }}"
                                    {{ ($currentFilters['id_md_cust'] ?? '') == $c->id_md_cust ? 'selected' : '' }}>
                                    {{ $c->customer }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="draft"  {{ ($currentFilters['status'] ?? '') === 'draft'  ? 'selected' : '' }}>Draft</option>
                            <option value="sent"   {{ ($currentFilters['status'] ?? '') === 'sent'   ? 'selected' : '' }}>Sent</option>
                            <option value="closed" {{ ($currentFilters['status'] ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('invoice-contract.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Reset
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search me-1"></i>Terapkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Form --}}
<form id="deleteForm" method="POST" style="display:none">
    @csrf @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
document.getElementById('filterButton').addEventListener('click', function(){
    new bootstrap.Modal(document.getElementById('filterModal')).show();
});

$(document).ready(function(){
    $('#invoiceContractTable').DataTable({
        pageLength: 25,
        responsive: true,
        order: [[4, 'desc']],
        columnDefs: [{ orderable: false, targets: [13] }],
        language: { search: 'Cari:', lengthMenu: 'Tampilkan _MENU_', paginate: { next: '›', previous: '‹' } },
    });
    $('.s2-cust').select2({ theme: 'bootstrap-5', placeholder: '-- Semua --', allowClear: true, dropdownParent: $('#filterModal') });
});

function confirmDelete(id, no) {
    if (!confirm('Hapus invoice ' + no + '? Semua item terkait akan ikut terhapus.')) return;
    const form = document.getElementById('deleteForm');
    form.action = '/rekap/invoice-contract/' + id;
    form.submit();
}
</script>
@endpush
