@extends('layouts.app')

@section('title', 'JO Tramper Data')

@section('content')
    <div class="container-fluid joTramperPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-ship me-2"></i>JO Tramper Data</span>
                        @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-tramper', 'tambah')))
                            <a href="{{ route('jo-tramper.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> Add
                            </a>
                        @endif
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="joTramperTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>JO Date</th>
                                        <th>JO Number</th>
                                        <th>Customer</th>
                                        <th>Vessel</th>
                                        <th>Port</th>
                                        <th>Period</th>
                                        <th>Title</th>
                                        <th class="text-center">Items</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Invoice</th>
                                        <th class="text-center" width="12%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($joTrampers as $joTramper)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $joTramper->tgl_jo_tram->format('d/m/y') }}
                                            </td>
                                            <td>{{ $joTramper->no_jo_tram ?? '-' }}</td>
                                            <td>{{ $joTramper->customer ? $joTramper->customer->customer : '-' }}</td>
                                            <td>{{ $joTramper->vessel ? $joTramper->vessel->vessel_name : '-' }}</td>
                                            <td>{{ $joTramper->port ? $joTramper->port->name_port : '-' }}</td>
                                            <td>
                                                <small>
                                                    {{ $joTramper->date_start->format('d/m/y') }}
                                                    –
                                                    {{ $joTramper->date_end->format('d/m/y') }}
                                                </small>
                                            </td>
                                            <td>{{ $joTramper->title }}</td>
                                            <td class="text-center">{{ $joTramper->items->count() }}</td>
                                            <td class="text-end">
                                                {{ number_format($joTramper->items->sum('hargajual_idr'), 2, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-tramper', 'detail')))
                                                    <a href="{{ route('jo-tramper.export-pdf', $joTramper->id_jo_tram) }}"
                                                        class="btn btn-sm btn-danger" target="_blank" title="Export PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-tramper', 'detail')))
                                                        <a href="{{ route('jo-tramper.show', $joTramper->id_jo_tram) }}"
                                                            class="btn btn-sm btn-info" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-tramper', 'ubah')))
                                                        <a href="{{ route('jo-tramper.edit', $joTramper->id_jo_tram) }}"
                                                            class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-tramper', 'hapus')))
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $joTramper->id_jo_tram }}"
                                                            data-name="{{ $joTramper->title }}"
                                                            data-url="{{ route('jo-tramper.destroy', $joTramper->id_jo_tram) }}"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center py-5">
                                                <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                                <h5 class="text-muted">No JO Tramper Data</h5>
                                                <p class="text-muted mb-0">Start by adding a new JO Tramper</p>
                                            </td>
                                        </tr>
                                    @endforelse
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
        .joTramperPage .card {
            border: none;
            border-radius: 10px;
        }

        .joTramperPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .joTramperPage .dataTables_wrapper {
            width: 100%;
            transition: all 0.3s ease;
        }

        .joTramperPage .dataTables_wrapper .dataTables_length,
        .joTramperPage .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem !important;
        }

        .joTramperPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
        }

        .joTramperPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
        }

        .joTramperPage .dataTables_wrapper .dataTables_length select {
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        }

        .joTramperPage #joTramperTable {
            width: 100% !important;
            transition: all 0.3s ease;
        }

        .joTramperPage #joTramperTable tbody tr {
            transition: all 0.2s ease;
        }

        .joTramperPage #joTramperTable tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .joTramperPage .btn-sm {
            transition: transform 0.2s;
        }

        .joTramperPage .btn-sm:hover {
            transform: scale(1.1);
        }

        .joTramperPage .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem !important;
            margin: 0 2px !important;
            border-radius: 4px !important;
        }

        .joTramperPage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-green) !important;
            color: white !important;
            border: 1px solid var(--primary-green) !important;
        }

        .joTramperPage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-green) !important;
            color: white !important;
            border: 1px solid var(--primary-green) !important;
        }

        .joTramperPage .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .joTramperPage .badge {
            font-weight: 500;
            padding: 0.35rem 0.65rem;
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
            // Cek apakah tabel memiliki data
            var hasData = $('#joTramperTable tbody tr').length > 0 &&
                !$('#joTramperTable tbody tr td[colspan]').length;

            if (hasData) {
                if ($.fn.DataTable.isDataTable('#joTramperTable')) {
                    $('#joTramperTable').DataTable().destroy();
                }

                var table = $('#joTramperTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    order: [
                        [1, 'desc']
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: 0
                        },
                        {
                            orderable: false,
                            targets: -1
                        }
                    ],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        },
                        emptyTable: "No JO Tramper data available"
                    },
                    autoWidth: true,
                    drawCallback: function(settings) {
                        var api = this.api();
                        var pageInfo = api.page.info();
                        var startIndex = pageInfo.start;

                        api.column(0, {
                            page: 'current'
                        }).nodes().each(function(cell, i) {
                            cell.innerHTML = startIndex + i + 1;
                        });

                        api.columns.adjust();
                    }
                });

                let resizeTimer;
                $(window).on('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        if ($.fn.DataTable.isDataTable('#joTramperTable')) {
                            $('#joTramperTable').DataTable().columns.adjust().responsive.recalc();
                        }
                    }, 300);
                });

                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#joTramperTable')) {
                        $('#joTramperTable').DataTable().columns.adjust().responsive.recalc();
                    }
                }, 100);
            } else {
                // Jika data kosong, sembunyikan elemen DataTables
                $('#joTramperTable').wrap('<div class="table-responsive"></div>');
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.stopPropagation();

                const name = $(this).data('name');
                const url = $(this).data('url');

                Swal.fire({
                    title: 'Delete JO Tramper?',
                    html: `
            <div class="text-start">
                <p>JO Tramper <strong>${name}</strong> will be permanently deleted.</p>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> All items related to this JO Tramper will also be deleted.
                </div>
            </div>
        `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete All!',
                    cancelButtonText: 'Cancel',
                    focusCancel: true,
                    customClass: {
                        htmlContainer: 'text-start'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait while we delete the JO Tramper and all related items.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $('#deleteForm').attr('action', url).submit();
                    }
                });
            });

            if (hasData) {
                $('#joTramperTable tbody').on('click', 'tr', function(e) {
                    if ($(e.target).is('button') || $(e.target).is('a') || $(e.target).is('i') ||
                        $(e.target).closest('button').length || $(e.target).closest('a').length) {
                        return;
                    }

                    var detailLink = $(this).find('a[title="Detail"]').attr('href');
                    if (detailLink) {
                        window.location.href = detailLink;
                    }
                });
            }

            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 5000);
        });
    </script>
@endpush
