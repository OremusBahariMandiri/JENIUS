@extends('layouts.app')

@section('title', 'Release To Data')

@section('content')
<div class="container-fluid releasePage">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-share-square me-2"></i>Release To Data</span>
                    @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('release_to', 'tambah')))
                    <a href="{{ route('release_to.create') }}" class="btn btn-light">
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
                        <table id="releaseTable" class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Release Name</th>
                                    <th>Destination</th>
                                    <th>Account No.</th>
                                    <th>Phone</th>
                                    <th class="text-center" width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($releases as $release)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $release->nama_release }}</td>
                                    <td>{{ $release->tujuan ?? '-' }}</td>
                                    <td>{{ $release->rekening ?? '-' }}</td>
                                    <td>{{ $release->no_telepon ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('release_to', 'detail')))
                                            <a href="{{ route('release_to.show', $release->id_md_release) }}"
                                               class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endif
                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('release_to', 'ubah')))
                                            <a href="{{ route('release_to.edit', $release->id_md_release) }}"
                                               class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('release_to', 'hapus')))
                                            <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-id="{{ $release->id_md_release }}"
                                                    data-name="{{ $release->nama_release }}"
                                                    data-url="{{ route('release_to.destroy', $release->id_md_release) }}"
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
    .releasePage .card { border: none; border-radius: 10px; }
    .releasePage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .releasePage #releaseTable tbody tr:hover { background-color: #f8f9fa; cursor: pointer; }
    .releasePage .btn-sm { transition: transform 0.2s; }
    .releasePage .btn-sm:hover { transform: scale(1.1); }
    .releasePage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-green) !important; color: white !important; border: 1px solid var(--primary-green) !important;
    }
    .releasePage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--primary-green) !important; color: white !important; border: 1px solid var(--primary-green) !important;
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
    var table = $('#releaseTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[1, 'asc']],
        columnDefs: [{ orderable: false, targets: 0 }, { orderable: false, targets: -1 }],
        language: {
            search: "Search:", lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
        },
        drawCallback: function(settings) {
            var api = this.api();
            var startIndex = api.page.info().start;
            api.column(0, {page: 'current'}).nodes().each(function(cell, i) { cell.innerHTML = startIndex + i + 1; });
            api.columns.adjust();
        }
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

    $(document).on('click', '.btn-delete', function(e) {
        e.stopPropagation();
        const name = $(this).data('name'), url = $(this).data('url');
        Swal.fire({
            title: 'Delete Release To?',
            html: `Release <strong>${name}</strong> will be permanently deleted.`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete!',
            cancelButtonText: 'Cancel', focusCancel: true,
        }).then((result) => { if (result.isConfirmed) $('#deleteForm').attr('action', url).submit(); });
    });

    $('#releaseTable tbody').on('click', 'tr', function(e) {
        if ($(e.target).is('button,a,i') || $(e.target).closest('button,a').length) return;
        var link = $(this).find('a[title="Detail"]').attr('href');
        if (link) window.location.href = link;
    });

    setTimeout(() => $(".alert").fadeOut("slow"), 5000);
});
</script>
@endpush