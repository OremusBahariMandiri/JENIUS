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
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-tramper', 'tambah')))
                                <a href="{{ route('jo-tramper.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus-circle me-1"></i> Add
                                </a>
                            @endif
                        </div>
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

                        {{-- Active Filter Badge --}}
                        @if (!empty($currentFilters) && array_filter(array_intersect_key($currentFilters, array_flip(['no_jo','id_md_cust','id_md_vessel','id_md_port','title']))))
                            <div class="alert alert-info alert-dismissible fade show" id="filterActiveAlert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Active Filters:</strong>
                                @if (!empty($currentFilters['no_jo']))
                                    &nbsp;<span class="badge bg-primary">JO No: {{ $currentFilters['no_jo'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_cust']))
                                    &nbsp;<span class="badge bg-primary">Customer: {{ $currentFilters['customer_label'] ?? $currentFilters['id_md_cust'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_vessel']))
                                    &nbsp;<span class="badge bg-primary">Vessel: {{ $currentFilters['vessel_label'] ?? $currentFilters['id_md_vessel'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_port']))
                                    &nbsp;<span class="badge bg-primary">Port: {{ $currentFilters['port_label'] ?? $currentFilters['id_md_port'] }}</span>
                                @endif
                                @if (!empty($currentFilters['title']))
                                    &nbsp;<span class="badge bg-primary">Title: {{ $currentFilters['title'] }}</span>
                                @endif
                                <a href="{{ route('jo-tramper.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Reset Filter
                                </a>
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
                                                {{ $joTramper->tgl_jo_tram ? $joTramper->tgl_jo_tram->format('d/m/y') : '-' }}
                                            </td>
                                            <td>{{ $joTramper->no_jo_tram ?? '-' }}</td>
                                            <td>{{ $joTramper->customer ? $joTramper->customer->customer : '-' }}</td>
                                            <td>{{ $joTramper->vessel ? $joTramper->vessel->vessel_name : '-' }}</td>
                                            <td>{{ $joTramper->port ? $joTramper->port->name_port : '-' }}</td>
                                            <td>
                                                <small>
                                                    {{ $joTramper->date_start ? $joTramper->date_start->format('d/m/y') : '-' }}
                                                    –
                                                    {{ $joTramper->date_end ? $joTramper->date_end->format('d/m/y') : '-' }}
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

    {{-- ===================== FILTER MODAL ===================== --}}
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-black" style="background-color: #d1fae5">
                    <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter JO Tramper</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET" action="{{ route('jo-tramper.index') }}">
                        <div class="row g-3">

                            {{-- JO Number --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">JO Number</label>
                                <input type="text" class="form-control" name="no_jo"
                                    value="{{ request('no_jo', '') }}">
                            </div>

                             {{-- Title --}}
                             <div class="col-md-6">
                                <label class="form-label fw-semibold">Title</label>
                                <input type="text" class="form-control" name="title"
                                    value="{{ request('title', '') }}">
                            </div>

                            {{-- Customer --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Customer</label>
                                <select class="form-select select2-filter" name="id_md_cust"
                                    id="filterCustomer" data-placeholder="-- Select Customers --">
                                    <option value=""></option>
                                    @foreach($customers as $cust)
                                        <option value="{{ $cust->id_md_cust }}"
                                            {{ request('id_md_cust') == $cust->id_md_cust ? 'selected' : '' }}>
                                            {{ $cust->customer }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Vessel --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vessel</label>
                                <select class="form-select select2-filter" name="id_md_vessel"
                                    id="filterVessel" data-placeholder="-- Select Vessels --">
                                    <option value=""></option>
                                    @foreach($vessels as $vessel)
                                        <option value="{{ $vessel->id_md_vessel }}"
                                            {{ request('id_md_vessel') == $vessel->id_md_vessel ? 'selected' : '' }}>
                                            {{ $vessel->vessel_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Port --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Port</label>
                                <select class="form-select select2-filter" name="id_md_port"
                                    id="filterPort" data-placeholder="-- Select Ports --">
                                    <option value=""></option>
                                    @foreach($ports as $port)
                                        <option value="{{ $port->id_md_port }}"
                                            {{ request('id_md_port') == $port->id_md_port ? 'selected' : '' }}>
                                            {{ $port->name_port }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" id="resetFilter">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-search me-1"></i> Apply Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== EXPORT MODAL ===================== --}}
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-black" style="background-color: #d1fae5">
                    <h5 class="modal-title"><i class="fas fa-download me-2"></i>Export JO Tramper</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Choose the export format:</p>

                    @php
                        $exportParams    = request()->only(['no_jo', 'id_md_cust', 'id_md_vessel', 'id_md_port', 'title']);
                        $hasActiveFilters = array_filter($exportParams);
                        $excelUrl = route('jo-tramper.export') . '?' . http_build_query(array_merge($exportParams, ['format' => 'excel']));
                        $pdfUrl   = route('jo-tramper.export') . '?' . http_build_query(array_merge($exportParams, ['format' => 'pdf']));
                    @endphp

                    @if ($hasActiveFilters)
                        <div class="alert alert-info py-2">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export will use <strong>active filters</strong> (filtered data only).</small>
                        </div>
                    @else
                        <div class="alert alert-info py-2">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export will include <strong>all JO Tramper data</strong> (no active filters).</small>
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ $excelUrl }}" class="btn btn-outline-success">
                            <i class="fas fa-file-excel me-2"></i> Export to Excel (.xlsx)
                        </a>
                        <a href="{{ $pdfUrl }}" class="btn btn-outline-danger">
                            <i class="fas fa-file-pdf me-2"></i> Export to PDF
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    {{-- Select2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

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
        }

        .joTramperPage #joTramperTable tbody tr:hover {
            background-color: #f8f9fa;
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

        /* Select2 di dalam modal */
        .modal .select2-container {
            width: 100% !important;
        }

        .modal .select2-container .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px) !important;
            padding: 0.375rem 0.75rem !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
        }

        .modal .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 0 !important;
            color: #212529;
        }

        .modal .select2-container .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {

            // ===== DATATABLE =====
            var hasData = $('#joTramperTable tbody tr').length > 0 &&
                !$('#joTramperTable tbody tr td[colspan]').length;

            if (hasData) {
                if ($.fn.DataTable.isDataTable('#joTramperTable')) {
                    $('#joTramperTable').DataTable().destroy();
                }

                var table = $('#joTramperTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                    order: [[1, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: 0 },
                        { orderable: false, targets: -1 },
                        { responsivePriority: 1, targets: 11 },
                        { responsivePriority: 2, targets: 0 },
                    ],
                    language: {
                        search: 'Search:',
                        lengthMenu: 'Show _MENU_ entries per page',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        infoEmpty: 'Showing 0 to 0 of 0 entries',
                        infoFiltered: '(filtered from _MAX_ total entries)',
                        paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' },
                        emptyTable: 'No JO Tramper data available'
                    },
                    autoWidth: true,
                    drawCallback: function (settings) {
                        var api        = this.api();
                        var startIndex = api.page.info().start;
                        api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                            cell.innerHTML = startIndex + i + 1;
                        });
                        api.columns.adjust();
                    }
                });

                let resizeTimer;
                $(window).on('resize', function () {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function () {
                        if ($.fn.DataTable.isDataTable('#joTramperTable')) {
                            $('#joTramperTable').DataTable().columns.adjust().responsive.recalc();
                        }
                    }, 300);
                });

                setTimeout(function () {
                    if ($.fn.DataTable.isDataTable('#joTramperTable')) {
                        $('#joTramperTable').DataTable().columns.adjust().responsive.recalc();
                    }
                }, 100);
            }

            // ===== SELECT2 (inisialisasi setelah modal ditampilkan) =====
            // Harus diinisialisasi di event modal shown agar dropdown muncul dengan benar di dalam modal
            $('#filterModal').on('shown.bs.modal', function () {
                $('.select2-filter').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#filterModal'),
                            placeholder: $(this).data('placeholder') || '-- Select --',
                            allowClear: true,
                            width: '100%',
                        });
                    }
                });
            });

            // ===== TOOLTIPS =====
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

            // ===== FILTER =====
            $('#filterButton').on('click', function () {
                $('#filterModal').modal('show');
            });

            $('#resetFilter').on('click', function () {
                // Reset text inputs
                $('#filterForm input[type="text"]').val('');
                // Reset Select2 selects
                $('.select2-filter').val('').trigger('change');
            });

            // ===== EXPORT =====
            $('#exportButton').on('click', function () {
                $('#exportModal').modal('show');
            });

            // ===== DELETE =====
            $(document).on('click', '.btn-delete', function (e) {
                e.stopPropagation();

                const name = $(this).data('name');
                const url  = $(this).data('url');

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
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait while we delete the JO Tramper and all related items.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        $('#deleteForm').attr('action', url).submit();
                    }
                });
            });

            // ===== AUTO HIDE ALERTS =====
            setTimeout(function () {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush