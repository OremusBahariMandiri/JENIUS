@extends('layouts.app')

@section('title', 'JO Other Data')

@section('content')
    <div class="container-fluid joOtherPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-file-invoice me-2"></i>JO Other Data</span>
                        @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-other', 'tambah')))
                            <a href="{{ route('jo-other.create') }}" class="btn btn-light">
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
                            <table id="joOtherTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>JO Date</th>
                                        <th>JO Number</th>
                                        <th>Customer</th>
                                        <th>Vessel</th>
                                        <th>Port</th>
                                        <th>Other Type</th>
                                        <th>Period</th>
                                        <th>Title</th>
                                        <th class="text-center">Items</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Invoice</th>
                                        <th class="text-center" width="12%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($joOthers as $joOther)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $joOther->tgl_jo_other ? $joOther->tgl_jo_other->format('d/m/y') : '-' }}
                                            </td>
                                            <td>{{ $joOther->no_jo_other ?? '-' }}</td>
                                            <td>{{ $joOther->customer ? $joOther->customer->customer : '-' }}</td>
                                            <td>{{ $joOther->vessel ? $joOther->vessel->vessel_name : '-' }}</td>
                                            <td>{{ $joOther->port ? $joOther->port->name_port : '-' }}</td>
                                            <td>{{ $joOther->other ? $joOther->other->other : '-' }}</td>
                                            <td>
                                                <small>
                                                    {{ $joOther->date_start ? $joOther->date_start->format('d/m/y') : '-' }}
                                                    –
                                                    {{ $joOther->date_end ? $joOther->date_end->format('d/m/y') : '-' }}
                                                </small>
                                            </td>
                                            <td>{{ $joOther->title }}</td>
                                            <td class="text-center">{{ $joOther->items->count() }}</td>
                                            <td class="text-end"> {{-- tambah ini --}}
                                                {{ number_format($joOther->items->sum('hargajual_idr'), 2, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-other', 'detail')))
                                                    <a href="{{ route('jo-other.export-pdf', $joOther->id_jo_other) }}"
                                                        class="btn btn-sm btn-danger" target="_blank" title="Export PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-other', 'detail')))
                                                        <a href="{{ route('jo-other.show', $joOther->id_jo_other) }}"
                                                            class="btn btn-sm btn-info" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-other', 'ubah')))
                                                        <a href="{{ route('jo-other.edit', $joOther->id_jo_other) }}"
                                                            class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-other', 'hapus')))
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $joOther->id_jo_other }}"
                                                            data-name="{{ $joOther->title }}"
                                                            data-url="{{ route('jo-other.destroy', $joOther->id_jo_other) }}"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center py-5">
                                                <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                                <h5 class="text-muted">No JO Other Data</h5>
                                                <p class="text-muted mb-0">Start by adding a new JO Other</p>
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
        .joOtherPage .card {
            border: none;
            border-radius: 10px;
        }

        .joOtherPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .joOtherPage .dataTables_wrapper {
            width: 100%;
            transition: all 0.3s ease;
        }

        .joOtherPage .dataTables_wrapper .dataTables_length,
        .joOtherPage .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem !important;
        }

        .joOtherPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
        }

        .joOtherPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
        }

        .joOtherPage .dataTables_wrapper .dataTables_length select {
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        }

        .joOtherPage #joOtherTable {
            width: 100% !important;
            transition: all 0.3s ease;
        }

        .joOtherPage #joOtherTable tbody tr {
            transition: all 0.2s ease;
        }

        .joOtherPage #joOtherTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        .joOtherPage .btn-sm {
            transition: transform 0.2s;
        }

        .joOtherPage .btn-sm:hover {
            transform: scale(1.1);
        }

        .joOtherPage .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem !important;
            margin: 0 2px !important;
            border-radius: 4px !important;
        }

        .joOtherPage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-green) !important;
            color: white !important;
            border: 1px solid var(--primary-green) !important;
        }

        .joOtherPage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-green) !important;
            color: white !important;
            border: 1px solid var(--primary-green) !important;
        }

        .joOtherPage .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .joOtherPage .badge {
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
            var hasData = $('#joOtherTable tbody tr').length > 0 &&
                !$('#joOtherTable tbody tr td[colspan]').length;

            if (hasData) {
                if ($.fn.DataTable.isDataTable('#joOtherTable')) {
                    $('#joOtherTable').DataTable().destroy();
                }

                var table = $('#joOtherTable').DataTable({
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
                        }, // No
                        {
                            orderable: false,
                            targets: 9
                        }, // Items
                        {
                            orderable: false,
                            targets: 10
                        }, // Total
                        {
                            orderable: false,
                            targets: 11
                        }, // Invoice
                        {
                            orderable: false,
                            targets: 12
                        },
                        {
                            responsivePriority: 1,
                            targets: 12
                        },
                        {
                            responsivePriority: 2,
                            targets: 0
                        },
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
                        emptyTable: "No JO Other data available"
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
                        if ($.fn.DataTable.isDataTable('#joOtherTable')) {
                            $('#joOtherTable').DataTable().columns.adjust().responsive.recalc();
                        }
                    }, 300);
                });

                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#joOtherTable')) {
                        $('#joOtherTable').DataTable().columns.adjust().responsive.recalc();
                    }
                }, 100);
            }

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Delete confirmation with SweetAlert2
            $(document).on('click', '.btn-delete', function(e) {
                e.stopPropagation();

                const name = $(this).data('name');
                const url = $(this).data('url');

                Swal.fire({
                    title: 'Delete JO Other?',
                    html: `
            <div class="text-start">
                <p>JO Other <strong>${name}</strong> will be permanently deleted.</p>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> All items related to this JO Other will also be deleted.
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
                            html: 'Please wait while we delete the JO Other and all related items.',
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

            // Auto hide alerts
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 5000);
        });
    </script>
@endpush
