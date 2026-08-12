@extends('layouts.app')

@section('title', 'JO Contract Data')

@section('content')
    <div class="container-fluid joContractPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-file-contract me-2"></i>JO Contract Data</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-contract', 'tambah')))
                                <a href="{{ route('jo-contract.create') }}" class="btn btn-light">
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

                        {{-- Active Filter Display --}}
                        @if (!empty($currentFilters) && array_filter($currentFilters))
                            <div class="alert alert-info alert-dismissible fade show" id="filterActiveAlert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Active Filters:</strong>
                                @if (!empty($currentFilters['no_jo']))
                                    &nbsp;<span class="badge bg-primary">JO No: {{ $currentFilters['no_jo'] }}</span>
                                @endif
                                @if (!empty($currentFilters['no_contract']))
                                    &nbsp;<span class="badge bg-primary">Contract No: {{ $currentFilters['no_contract'] }}</span>
                                @endif
                                @if (!empty($currentFilters['contract_name']))
                                    &nbsp;<span class="badge bg-primary">Contract: {{ $currentFilters['contract_name'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_cust']))
                                    &nbsp;<span class="badge bg-primary">Customer: {{ $currentFilters['customer_label'] ?? $currentFilters['id_md_cust'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_area']))
                                    &nbsp;<span class="badge bg-primary">Area: {{ $currentFilters['area_label'] ?? $currentFilters['id_md_area'] }}</span>
                                @endif
                                @if (!empty($currentFilters['title']))
                                    &nbsp;<span class="badge bg-primary">Title: {{ $currentFilters['title'] }}</span>
                                @endif
                                <a href="{{ route('jo-contract.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Reset Filter
                                </a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="joContractTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>JO Date</th>
                                        <th>JO Number</th>
                                        <th>Contract No</th>
                                        <th>Contract Name</th>
                                        <th>Customer</th>
                                        <th>Period</th>
                                        <th>Area</th>
                                        <th>Title</th>
                                        <th class="text-center">Items</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">JO PDF</th>
                                        <th class="text-center" width="12%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($joContracts as $joContract)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $joContract->tgl_jo_cont ? $joContract->tgl_jo_cont->format('d/m/y') : '-' }}
                                            </td>
                                            <td>{{ $joContract->no_jo_cont ?? '-' }}</td>
                                            <td>{{ $joContract->contract ? $joContract->contract->no_contract : '-' }}</td>
                                            <td>{{ $joContract->contract ? $joContract->contract->contract : '-' }}</td>
                                            <td>
                                                {{ $joContract->contract && $joContract->contract->customer ? $joContract->contract->customer->customer : '-' }}
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $joContract->contract && $joContract->contract->date_start ? \Carbon\Carbon::parse($joContract->contract->date_start)->format('d/m/y') : '-' }}
                                                    –
                                                    {{ $joContract->contract && $joContract->contract->date_end ? \Carbon\Carbon::parse($joContract->contract->date_end)->format('d/m/y') : '-' }}
                                                </small>
                                            </td>
                                            <td>{{ $joContract->area ? $joContract->area->area : '-' }}</td>
                                            <td>{{ $joContract->title }}</td>
                                            <td class="text-center">{{ $joContract->items->count() }}</td>
                                            <td class="text-end">
                                                {{ number_format($joContract->items->sum('hargajual_idr'), 2, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-contract', 'detail')))
                                                <a href="{{ route('jo-contract.export-pdf', $joContract->id_jo_cont) }}"
                                                    class="btn btn-sm btn-danger"
                                                    title="Export PDF"
                                                    target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>

                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-contract', 'detail')))
                                                        <a href="{{ route('jo-contract.show', $joContract->id_jo_cont) }}"
                                                            class="btn btn-sm btn-info" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-contract', 'ubah')))
                                                        <a href="{{ route('jo-contract.edit', $joContract->id_jo_cont) }}"
                                                            class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('jo-contract', 'hapus')))
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $joContract->id_jo_cont }}"
                                                            data-name="{{ $joContract->title }}"
                                                            data-url="{{ route('jo-contract.destroy', $joContract->id_jo_cont) }}"
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
                                                <h5 class="text-muted">No JO Contract Data</h5>
                                                <p class="text-muted mb-0">Start by adding a new JO Contract</p>
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
                    <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter JO Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET" action="{{ route('jo-contract.index') }}">
                        <div class="row g-3">
                            {{-- JO Number --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">JO Number</label>
                                <input type="text" class="form-control" name="no_jo"
                                    value="{{ request('no_jo', '') }}">
                            </div>

                            {{-- Contract No --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contract No</label>
                                <input type="text" class="form-control" name="no_contract"
                                    value="{{ request('no_contract', '') }}">
                            </div>

                            {{-- Contract Name --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contract Name</label>
                                <input type="text" class="form-control" name="contract_name"
                                    value="{{ request('contract_name', '') }}">
                            </div>

                             {{-- Title --}}
                             <div class="col-md-6">
                                <label class="form-label fw-semibold">Title</label>
                                <input type="text" class="form-control" name="title"
                                    value="{{ request('title', '') }}">
                            </div>

                            {{-- Customer (select2) --}}
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

                            {{-- Area (select2) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Area</label>
                                <select class="form-select select2-filter" name="id_md_area"
                                    id="filterArea" data-placeholder="-- Select Areas --">
                                    <option value=""></option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id_md_area }}"
                                            {{ request('id_md_area') == $area->id_md_area ? 'selected' : '' }}>
                                            {{ $area->area }}
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
                    <h5 class="modal-title"><i class="fas fa-download me-2"></i>Export JO Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Choose the export format:</p>

                    @php
                        $exportParams = request()->only([
                            'no_jo', 'no_contract', 'contract_name',
                            'id_md_cust', 'id_md_area', 'title'
                        ]);
                        $hasActiveFilters = array_filter($exportParams);

                        $excelUrl = route('jo-contract.export') . '?' . http_build_query(array_merge($exportParams, ['format' => 'excel']));
                        $pdfUrl   = route('jo-contract.export') . '?' . http_build_query(array_merge($exportParams, ['format' => 'pdf']));
                    @endphp

                    @if ($hasActiveFilters)
                        <div class="alert alert-info py-2">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export will use <strong>active filters</strong> (filtered data only).</small>
                        </div>
                    @else
                        <div class="alert alert-info py-2">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export will include <strong>all JO Contract data</strong> (no active filters).</small>
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
        /* Select2 di dalam modal */
        .modal .select2-container { width: 100% !important; }
        .modal .select2-container .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px) !important;
            padding: 0.375rem 0.75rem !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
        }
        .modal .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important; padding-left: 0 !important; color: #212529;
        }
        .modal .select2-container .select2-selection--single .select2-selection__arrow { height: 100% !important; }

        .joContractPage .card {
            border: none;
            border-radius: 10px;
        }

        .joContractPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .joContractPage .dataTables_wrapper {
            width: 100%;
            transition: all 0.3s ease;
        }

        .joContractPage .dataTables_wrapper .dataTables_length,
        .joContractPage .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem !important;
        }

        .joContractPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
        }

        .joContractPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
        }

        .joContractPage .dataTables_wrapper .dataTables_length select {
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        }

        .joContractPage #joContractTable {
            width: 100% !important;
            transition: all 0.3s ease;
        }

        .joContractPage #joContractTable tbody tr {
            transition: all 0.2s ease;
        }

        .joContractPage #joContractTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        .joContractPage .btn-sm {
            transition: transform 0.2s;
        }

        .joContractPage .btn-sm:hover {
            transform: scale(1.1);
        }

        .joContractPage .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem !important;
            margin: 0 2px !important;
            border-radius: 4px !important;
        }

        .joContractPage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-green) !important;
            color: white !important;
            border: 1px solid var(--primary-green) !important;
        }

        .joContractPage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-green) !important;
            color: white !important;
            border: 1px solid var(--primary-green) !important;
        }

        .joContractPage .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .joContractPage .badge {
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
    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // ===== DATATABLE =====
            var hasData = $('#joContractTable tbody tr').length > 0 &&
                !$('#joContractTable tbody tr td[colspan]').length;

            if (hasData) {
                if ($.fn.DataTable.isDataTable('#joContractTable')) {
                    $('#joContractTable').DataTable().destroy();
                }

                var table = $('#joContractTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    order: [
                        [1, 'asc']
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: 0
                        },
                        {
                            orderable: false,
                            targets: -1
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
                        emptyTable: "No JO Contract data available"
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
                        if ($.fn.DataTable.isDataTable('#joContractTable')) {
                            $('#joContractTable').DataTable().columns.adjust().responsive.recalc();
                        }
                    }, 300);
                });

                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#joContractTable')) {
                        $('#joContractTable').DataTable().columns.adjust().responsive.recalc();
                    }
                }, 100);
            }

            // ===== TOOLTIPS =====
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // ===== SELECT2 — inisialisasi saat modal ditampilkan =====
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
                    title: 'Delete JO Contract?',
                    html: `
                        <div class="text-start">
                            <p>JO Contract <strong>${name}</strong> will be permanently deleted.</p>
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Warning:</strong> All items related to this JO Contract will also be deleted.
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
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait while we delete the JO Contract and all related items.',
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
                $(".alert-success, .alert-danger").fadeOut("slow");
            }, 5000);
        });
    </script>
@endpush