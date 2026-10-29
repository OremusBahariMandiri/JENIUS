@extends('layouts.app')

@section('title', 'Data Master Pajak')

@section('content')
<div class="container-fluid pajakPage">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-percent me-2"></i>Data Master Pajak</span>
                    @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('pajak', 'tambah')))
                    <a href="{{ route('pajak.create') }}" class="btn btn-light">
                        <i class="fas fa-plus-circle me-1"></i> Add
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table id="pajakTable" class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Kode Pajak</th>
                                    <th>Nama Pajak</th>
                                    <th width="12%" class="text-center">Persentase</th>
                                    <th width="10%" class="text-center">Status</th>
                                    <th>Keterangan</th>
                                    <th class="text-center" width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pajakList as $pajak)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($pajak->kode_pajak)
                                            <span class="badge bg-secondary">{{ $pajak->kode_pajak }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $pajak->nama_pajak }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success fs-6">
                                            {{ number_format($pajak->persentase, 2) }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($pajak->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $pajak->keterangan ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('pajak', 'detail')))
                                            <a href="{{ route('pajak.show', $pajak->id_md_pajak) }}"
                                               class="btn btn-sm btn-info"
                                               data-bs-toggle="tooltip" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endif

                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('pajak', 'ubah')))
                                            <a href="{{ route('pajak.edit', $pajak->id_md_pajak) }}"
                                               class="btn btn-sm btn-warning"
                                               data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('pajak', 'hapus')))
                                            <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete"
                                                    data-id="{{ $pajak->id_md_pajak }}"
                                                    data-name="{{ $pajak->nama_pajak }}"
                                                    data-url="{{ route('pajak.destroy', $pajak->id_md_pajak) }}"
                                                    data-bs-toggle="tooltip" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    .pajakPage .card { border: none; border-radius: 10px; }
    .pajakPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .pajakPage .dataTables_wrapper .dataTables_filter input {
        margin-left: 5px !important; border-radius: 4px !important;
        border: 1px solid #ced4da !important; padding: .375rem .75rem !important;
    }
    .pajakPage .dataTables_wrapper .dataTables_length select {
        border-radius: 4px !important; border: 1px solid #ced4da !important;
        padding: .375rem 2rem .375rem .75rem !important;
    }
    .pajakPage #pajakTable tbody tr:hover { background-color: #f8f9fa; cursor: pointer; }
    .pajakPage .btn-sm { transition: transform .2s; }
    .pajakPage .btn-sm:hover { transform: scale(1.1); }
    .pajakPage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-green) !important; color: white !important;
        border: 1px solid var(--primary-green) !important;
    }
    .pajakPage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--primary-green) !important; color: white !important;
        border: 1px solid var(--primary-green) !important;
    }
    .pajakPage .badge.fs-6 { font-size: .8rem !important; padding: .35em .65em; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#pajakTable')) {
        $('#pajakTable').DataTable().destroy();
    }

    var table = $('#pajakTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
        order: [[2, 'asc']],
        columnDefs: [
            { orderable: false, targets: 0 },
            { orderable: false, targets: -1 }
        ],
        language: {
            search: 'Search:',
            lengthMenu: 'Show _MENU_ entries per page',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' }
        },
        drawCallback: function (settings) {
            var api = this.api();
            var startIndex = api.page.info().start;
            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = startIndex + i + 1;
            });
            api.columns.adjust();
        }
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    $(document).on('click', '.btn-delete', function (e) {
        e.stopPropagation();
        const name = $(this).data('name');
        const url  = $(this).data('url');
        Swal.fire({
            title: 'Hapus Data Pajak?',
            html: `Pajak <strong>${name}</strong> akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteForm').attr('action', url).submit();
            }
        });
    });

    $('#pajakTable tbody').on('click', 'tr', function (e) {
        if ($(e.target).is('button, a, i') || $(e.target).closest('button, a').length) return;
        var detailLink = $(this).find('a[title="Detail"]').attr('href');
        if (detailLink) window.location.href = detailLink;
    });

    setTimeout(function () { $('.alert').fadeOut('slow'); }, 5000);
});
</script>
@endpush
