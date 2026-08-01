@extends('layouts.app')

@section('title', 'Data Parent Chart of Account')

@section('content')
<div class="container-fluid parentCoaPage">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-sitemap me-2"></i>Data Parent Chart of Account</span>
                    @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('parent_coa', 'tambah')))
                    <a href="{{ route('parent-coa.create') }}" class="btn btn-light">
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
                        <table id="parentCoaTable" class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Kode Perkiraan</th>
                                    <th>Nama Akun</th>
                                    <th width="20%">Tipe Akun</th>
                                    <th class="text-center" width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($parentCoas as $index => $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="badge bg-secondary">{{ $item->kode_perkiraan }}</span></td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->costType?->name ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('parent_coa', 'detail')))
                                            <a href="{{ route('parent-coa.show', $item->id) }}"
                                               class="btn btn-sm btn-info"
                                               data-bs-toggle="tooltip" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endif

                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('parent_coa', 'ubah')))
                                            <a href="{{ route('parent-coa.edit', $item->id) }}"
                                               class="btn btn-sm btn-warning"
                                               data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('parent_coa', 'hapus')))
                                            <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete"
                                                    data-id="{{ $item->id }}"
                                                    data-name="{{ $item->nama }}"
                                                    data-url="{{ route('parent-coa.destroy', $item->id) }}"
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
    .parentCoaPage .card { border: none; border-radius: 10px; }
    .parentCoaPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .parentCoaPage #parentCoaTable tbody tr:hover { background-color: #f8f9fa; cursor: pointer; }
    .parentCoaPage .btn-sm { transition: transform 0.2s; }
    .parentCoaPage .btn-sm:hover { transform: scale(1.1); }
    .parentCoaPage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-green) !important; color: white !important;
        border: 1px solid var(--primary-green) !important;
    }
    .parentCoaPage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--primary-green) !important; color: white !important;
        border: 1px solid var(--primary-green) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    var table = $('#parentCoaTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: 0 },
            { orderable: false, targets: -1 }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries per page",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
        },
        drawCallback: function(settings) {
            var api = this.api();
            var startIndex = api.page.info().start;
            api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                cell.innerHTML = startIndex + i + 1;
            });
            api.columns.adjust();
        }
    });

    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if ($.fn.DataTable.isDataTable('#parentCoaTable')) {
                $('#parentCoaTable').DataTable().columns.adjust().responsive.recalc();
            }
        }, 300);
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });

    $(document).on('click', '.btn-delete', function(e) {
        e.stopPropagation();
        const name = $(this).data('name');
        const url  = $(this).data('url');

        Swal.fire({
            title: 'Hapus Parent COA?',
            html: `Parent COA <strong>${name}</strong> akan dihapus permanen.`,
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

    $('#parentCoaTable tbody').on('click', 'tr', function(e) {
        if ($(e.target).is('button, a, i') || $(e.target).closest('button, a').length) return;
        var detailLink = $(this).find('a[title="Detail"]').attr('href');
        if (detailLink) window.location.href = detailLink;
    });

    setTimeout(function() { $(".alert").fadeOut("slow"); }, 5000);
});
</script>
@endpush