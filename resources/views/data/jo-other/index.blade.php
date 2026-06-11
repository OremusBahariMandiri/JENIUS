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
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-other', 'tambah')))
                                <a href="{{ route('jo-other.create') }}" class="btn btn-light">
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
                        @php
                            $filterKeys = ['no_jo', 'id_md_cust', 'id_md_vessel', 'id_md_port', 'id_md_other', 'title'];
                            $hasFilter =
                                !empty($currentFilters) &&
                                array_filter(array_intersect_key($currentFilters, array_flip($filterKeys)));
                        @endphp
                        @if ($hasFilter)
                            <div class="alert alert-info alert-dismissible fade show" id="filterActiveAlert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Active Filters:</strong>
                                @if (!empty($currentFilters['no_jo']))
                                    &nbsp;<span class="badge bg-primary">JO No: {{ $currentFilters['no_jo'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_cust']))
                                    &nbsp;<span class="badge bg-primary">Customer:
                                        {{ $currentFilters['customer_label'] ?? $currentFilters['id_md_cust'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_vessel']))
                                    &nbsp;<span class="badge bg-primary">Vessel:
                                        {{ $currentFilters['vessel_label'] ?? $currentFilters['id_md_vessel'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_port']))
                                    &nbsp;<span class="badge bg-primary">Port:
                                        {{ $currentFilters['port_label'] ?? $currentFilters['id_md_port'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_other']))
                                    &nbsp;<span class="badge bg-primary">Other Type:
                                        {{ $currentFilters['other_label'] ?? $currentFilters['id_md_other'] }}</span>
                                @endif
                                @if (!empty($currentFilters['title']))
                                    &nbsp;<span class="badge bg-primary">Title: {{ $currentFilters['title'] }}</span>
                                @endif
                                <a href="{{ route('jo-other.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Reset Filter
                                </a>
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
                                            <td>{{ $joOther->tgl_jo_other ? $joOther->tgl_jo_other->format('d/m/y') : '-' }}
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
                                            <td class="text-end">
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

    {{-- ===================== FILTER MODAL ===================== --}}
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-black" style="background-color: #d1fae5">
                    <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter JO Other</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET" action="{{ route('jo-other.index') }}">
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
                                <select class="form-select select2-filter" name="id_md_cust" id="filterCustomer"
                                    data-placeholder="-- Select Customers --">
                                    <option value=""></option>
                                    @foreach ($customers as $cust)
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
                                <select class="form-select select2-filter" name="id_md_vessel" id="filterVessel"
                                    data-placeholder="-- Select Vessels --">
                                    <option value=""></option>
                                    @foreach ($vessels as $vessel)
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
                                <select class="form-select select2-filter" name="id_md_port" id="filterPort"
                                    data-placeholder="-- Select Ports --">
                                    <option value=""></option>
                                    @foreach ($ports as $port)
                                        <option value="{{ $port->id_md_port }}"
                                            {{ request('id_md_port') == $port->id_md_port ? 'selected' : '' }}>
                                            {{ $port->name_port }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Other Type --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Other Type</label>
                                <select class="form-select select2-filter" name="id_md_other" id="filterOther"
                                    data-placeholder="-- Select Other Types --">
                                    <option value=""></option>
                                    @foreach ($others as $other)
                                        <option value="{{ $other->id_md_other }}"
                                            {{ request('id_md_other') == $other->id_md_other ? 'selected' : '' }}>
                                            {{ $other->other }}
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
                    <h5 class="modal-title"><i class="fas fa-download me-2"></i>Export JO Other</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Choose the export format:</p>

                    @php
                        $exportParams = request()->only([
                            'no_jo',
                            'id_md_cust',
                            'id_md_vessel',
                            'id_md_port',
                            'id_md_other',
                            'title',
                        ]);
                        $hasActiveFilters = array_filter($exportParams);
                        $excelUrl =
                            route('jo-other.export') .
                            '?' .
                            http_build_query(array_merge($exportParams, ['format' => 'excel']));
                        $pdfUrl =
                            route('jo-other.export') .
                            '?' .
                            http_build_query(array_merge($exportParams, ['format' => 'pdf']));
                    @endphp

                    @if ($hasActiveFilters)
                        <div class="alert alert-info py-2">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export will use <strong>active filters</strong> (filtered data only).</small>
                        </div>
                    @else
                        <div class="alert alert-info py-2">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export will include <strong>all JO Other data</strong> (no active filters).</small>
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
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

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
        $(document).ready(function() {

            // ===== DATATABLE =====
            var hasData = $('#joOtherTable tbody tr').length > 0 &&
                !$('#joOtherTable tbody tr td[colspan]').length;

            if (hasData) {
                if ($.fn.DataTable.isDataTable('#joOtherTable')) {
                    $('#joOtherTable').DataTable().destroy();
                }

                $('#joOtherTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, 'All']
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
                            targets: 9
                        },
                        {
                            orderable: false,
                            targets: 10
                        },
                        {
                            orderable: false,
                            targets: 11
                        },
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
                        search: 'Search:',
                        lengthMenu: 'Show _MENU_ entries per page',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        infoEmpty: 'Showing 0 to 0 of 0 entries',
                        infoFiltered: '(filtered from _MAX_ total entries)',
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Previous'
                        },
                        emptyTable: 'No JO Other data available'
                    },
                    autoWidth: true,
                    drawCallback: function(settings) {
                        var api = this.api();
                        var startIndex = api.page.info().start;
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

            // ===== SELECT2 — inisialisasi saat modal ditampilkan =====
            $('#filterModal').on('shown.bs.modal', function() {
                $('.select2-filter').each(function() {
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
            tooltipTriggerList.map(function(el) {
                return new bootstrap.Tooltip(el);
            });

            // ===== FILTER =====
            $('#filterButton').on('click', function() {
                $('#filterModal').modal('show');
            });

            $('#resetFilter').on('click', function() {
                $('#filterForm input[type="text"]').val('');
                $('.select2-filter').val('').trigger('change');
            });

            // ===== EXPORT =====
            $('#exportButton').on('click', function() {
                $('#exportModal').modal('show');
            });

            // ===== DELETE =====
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
                }).then((result) => {
                    if (result.isConfirmed) {
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

            // ===== AUTO HIDE ALERTS =====
            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush
