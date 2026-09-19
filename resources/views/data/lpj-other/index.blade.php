@extends('layouts.app')

@section('title', 'LPJ Other')

@push('styles')
    <style>
        .lpj-other-page .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,.08);
        }
        .lpj-other-page .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        /* Summary Cards */
        .summary-card {
            border-radius: 12px;
            padding: 20px;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,.15);
            transition: transform .2s;
        }
        .summary-card:hover { transform: translateY(-3px); }
        .summary-card .summary-icon { font-size: 2rem; opacity: .8; }
        .summary-card .summary-value { font-size: 1.6rem; font-weight: 700; }
        .summary-card .summary-label { font-size: .85rem; opacity: .9; }
        /* Table */
        .table-lpj th {
            background-color: #2c3e50;
            color: white;
            padding: 12px 10px;
            font-weight: 600;
            font-size: .875rem;
            vertical-align: middle;
        }
        .table-lpj td {
            vertical-align: middle;
            padding: 10px;
            font-size: .875rem;
        }
        /* Filter Modal */
        .filter-badge {
            background: #10b981;
            color: white;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: .75rem;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid lpj-other-page">

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#10b981,#059669)">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="summary-label">Total LPJ</div>
                        <div class="summary-value">{{ $lpjOthers->count() }}</div>
                    </div>
                    <i class="fas fa-file-invoice summary-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="summary-label">Total Amount</div>
                        <div class="summary-value" style="font-size:1.1rem;">
                            {{ number_format($lpjOthers->sum('amount'),0,',','.') }}
                        </div>
                    </div>
                    <i class="fas fa-money-bill-wave summary-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="summary-label">Total Items</div>
                        <div class="summary-value">{{ $lpjOthers->sum(fn($l)=>$l->items->count()) }}</div>
                    </div>
                    <i class="fas fa-list summary-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="summary-label">Total LPJ Amount</div>
                        <div class="summary-value" style="font-size:1.1rem;">
                            {{ number_format($lpjOthers->sum(fn($l)=>$l->items->sum('amount_lpj')),0,',','.') }}
                        </div>
                    </div>
                    <i class="fas fa-check-circle summary-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header text-black d-flex justify-content-between align-items-center"
             style="background-color:#d1fae5">
            <span class="fw-bold"><i class="fas fa-file-invoice me-2"></i>LPJ Other</span>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fas fa-filter me-1"></i>Filter
                    @if(array_filter($currentFilters))
                        <span class="filter-badge ms-1">Active</span>
                    @endif
                </button>
                <a href="{{ route('lpj-other.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus me-1"></i>New LPJ
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-lpj table-bordered table-hover mb-0" id="lpjOtherTable">
                    <thead>
                        <tr>
                            <th style="width:5%;">No</th>
                            <th style="width:15%;">No. LPJ</th>
                            <th style="width:20%;">JO Other</th>
                            <th style="width:12%;">Date</th>
                            <th style="width:14%;">Amount (IDR)</th>
                            <th style="width:14%;">LPJ Total (IDR)</th>
                            <th style="width:10%;">Items</th>
                            <th style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lpjOthers as $i => $lpj)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>
                                    <span class="fw-bold text-success">{{ $lpj->no_lpj_other ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $lpj->joOther->no_jo_other ?? '-' }}</div>
                                    <small class="text-muted">{{ Str::limit($lpj->joOther->title ?? '', 40) }}</small>
                                </td>
                                <td>{{ $lpj->date ? $lpj->date->format('d M Y') : '-' }}</td>
                                <td class="text-end">{{ number_format($lpj->amount, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($lpj->items->sum('amount_lpj'), 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $lpj->items->count() }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('lpj-other.edit', $lpj->id) }}"
                                       class="btn btn-primary btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('lpj-other.export-pdf', $lpj->id) }}"
                                       target="_blank" class="btn btn-danger btn-sm" title="PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete"
                                            data-id="{{ $lpj->id }}"
                                            data-no="{{ $lpj->no_lpj_other }}"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                                    No LPJ Other data available
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
            <form method="GET" action="{{ route('lpj-other.index') }}">
                <div class="modal-header" style="background:#d1fae5;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-filter me-2"></i>Filter LPJ Other</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">No. LPJ</label>
                        <input type="text" name="no_lpj_other" class="form-control"
                               value="{{ $currentFilters['no_lpj_other'] }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Job Order</label>
                        <select name="id_jo_other" class="form-select">
                            <option value="">All JO</option>
                            @foreach($joOthers as $jo)
                                <option value="{{ $jo->id_jo_other }}"
                                    {{ $currentFilters['id_jo_other'] == $jo->id_jo_other ? 'selected' : '' }}>
                                    {{ $jo->no_jo_other }} - {{ Str::limit($jo->title, 40) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control"
                                   value="{{ $currentFilters['date_from'] }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control"
                                   value="{{ $currentFilters['date_to'] }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('lpj-other.index') }}" class="btn btn-outline-secondary">
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
$(document).ready(function () {
    $('#lpjOtherTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: [7] }],
    });

    // Delete
    $(document).on('click', '.btn-delete', function () {
        const id   = $(this).data('id');
        const no   = $(this).data('no');

        Swal.fire({
            title: 'Delete LPJ?',
            html: `<b>${no}</b> will be permanently deleted along with all its items.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, delete',
        }).then(result => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: `/data/lpj-other/${id}`,
                method: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success(r) {
                    if (r.success) {
                        Swal.fire('Deleted!', 'LPJ Other has been deleted.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', r.message, 'error');
                    }
                },
                error(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed to delete', 'error');
                }
            });
        });
    });
});
</script>
@endpush