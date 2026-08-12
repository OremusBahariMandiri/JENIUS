@extends('layouts.app')

@section('title', 'LPJ Contract Data')

@section('content')
<div class="container-fluid lpjContractPage">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                    style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-file-invoice me-2"></i>LPJ Contract Data</span>
                    <div>
                        <button type="button" class="btn btn-light me-2" id="filterButton">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('lpj-contract','tambah')))
                            <a href="{{ route('lpj-contract.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> Add
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Active Filters --}}
                    @if(array_filter($currentFilters))
                        <div class="alert alert-info alert-dismissible fade show">
                            <i class="fas fa-info-circle me-2"></i><strong>Active Filters:</strong>
                            @if(!empty($currentFilters['no_lpj_cont']))
                                <span class="badge bg-primary ms-1">No LPJ: {{ $currentFilters['no_lpj_cont'] }}</span>
                            @endif
                            @if(!empty($currentFilters['id_jo_cont']))
                                <span class="badge bg-primary ms-1">JO: {{ $currentFilters['id_jo_cont'] }}</span>
                            @endif
                            @if(!empty($currentFilters['date_from']))
                                <span class="badge bg-primary ms-1">From: {{ $currentFilters['date_from'] }}</span>
                            @endif
                            @if(!empty($currentFilters['date_to']))
                                <span class="badge bg-primary ms-1">To: {{ $currentFilters['date_to'] }}</span>
                            @endif
                            <a href="{{ route('lpj-contract.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                <i class="fas fa-times me-1"></i> Reset
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="lpjContractTable" class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th width="4%">No</th>
                                    <th>No. LPJ</th>
                                    <th>Date</th>
                                    <th>Job Order</th>
                                    <th class="text-center">Kasbon</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end">Total Kasbon (IDR)</th>
                                    <th class="text-end">Total LPJ (IDR)</th>
                                    <th class="text-center">Evidence</th>
                                    <th class="text-center">LPJ PDF</th>
                                    <th class="text-center" width="12%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lpjContracts as $lpj)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $lpj->no_lpj_cont ?? '-' }}</td>
                                        <td>{{ $lpj->date ? $lpj->date->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $lpj->joContract ? $lpj->joContract->no_jo_cont : '-' }}</td>
                                        <td class="text-center">{{ $lpj->kasbons->count() }}</td>
                                        <td class="text-center">{{ $lpj->items->count() }}</td>
                                        <td class="text-end">{{ number_format($lpj->amount, 2, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($lpj->items->sum('amount_lpj'), 2, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if($lpj->evidence)
                                                <a href="{{ Storage::url($lpj->evidence) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-warning" title="View Evidence">
                                                    <i class="fas fa-paperclip"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('lpj-contract', 'detail')))
                                            <a href="{{ route('lpj-contract.export-pdf', $lpj->id) }}" target="_blank"
                                                class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-file-pdf"></i>
                                             </a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('lpj-contract','detail')))
                                                    <a href="{{ route('lpj-contract.show', $lpj->id) }}"
                                                        class="btn btn-sm btn-info" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('lpj-contract','ubah')))
                                                    <a href="{{ route('lpj-contract.edit', $lpj->id) }}"
                                                        class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('lpj-contract','hapus')))
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                        data-id="{{ $lpj->id }}"
                                                        data-name="{{ $lpj->no_lpj_cont }}"
                                                        data-url="{{ route('lpj-contract.destroy', $lpj->id) }}"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-5">
                                            <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                            <h5 class="text-muted">No LPJ Contract Data</h5>
                                            <p class="text-muted mb-0">Start by adding a new LPJ Contract</p>
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

{{-- FILTER MODAL --}}
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-black" style="background-color: #d1fae5">
                <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter LPJ Contract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm" method="GET" action="{{ route('lpj-contract.index') }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. LPJ</label>
                            <input type="text" class="form-control" name="no_lpj_cont"
                                value="{{ request('no_lpj_cont','') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Job Order</label>
                            <select class="form-select select2-filter" name="id_jo_cont"
                                id="filterJoCont" data-placeholder="-- Select JO Contract --">
                                <option value=""></option>
                                @foreach($joContracts as $jo)
                                    <option value="{{ $jo->id_jo_cont }}"
                                        {{ request('id_jo_cont') == $jo->id_jo_cont ? 'selected' : '' }}>
                                        {{ $jo->no_jo_cont }} — {{ $jo->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date From</label>
                            <input type="date" class="form-control" name="date_from"
                                value="{{ request('date_from','') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date To</label>
                            <input type="date" class="form-control" name="date_to"
                                value="{{ request('date_to','') }}">
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

<form id="deleteForm" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
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
            line-height: 1.5 !important;
            padding-left: 0 !important;
            color: #212529;
        }
        .modal .select2-container .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
        }

        .lpjContractPage .card {
            border: none;
            border-radius: 10px;
        }

        .lpjContractPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .lpjContractPage .dataTables_wrapper {
            width: 100%;
            transition: all 0.3s ease;
        }

        .lpjContractPage .dataTables_wrapper .dataTables_length,
        .lpjContractPage .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem !important;
        }

        .lpjContractPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
        }

        .lpjContractPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
        }

        .lpjContractPage .dataTables_wrapper .dataTables_length select {
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        }

        .lpjContractPage #lpjContractTable {
            width: 100% !important;
            transition: all 0.3s ease;
        }

        .lpjContractPage #lpjContractTable tbody tr {
            transition: all 0.2s ease;
        }

        .lpjContractPage #lpjContractTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        .lpjContractPage .btn-sm {
            transition: transform 0.2s;
        }

        .lpjContractPage .btn-sm:hover {
            transform: scale(1.1);
        }

        .lpjContractPage .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem !important;
            margin: 0 2px !important;
            border-radius: 4px !important;
        }

        .lpjContractPage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-green) !important;
            color: white !important;
            border: 1px solid var(--primary-green) !important;
        }

        .lpjContractPage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-green) !important;
            color: white !important;
            border: 1px solid var(--primary-green) !important;
        }

        .lpjContractPage .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .lpjContractPage .badge {
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {

        // ===== DATATABLE =====
        var hasData = $('#lpjContractTable tbody tr').length > 0 &&
            !$('#lpjContractTable tbody tr td[colspan]').length;

        if (hasData) {
            if ($.fn.DataTable.isDataTable('#lpjContractTable')) {
                $('#lpjContractTable').DataTable().destroy();
            }

            var table = $('#lpjContractTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                order: [[2, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 0 },
                    { orderable: false, targets: -1 },
                    { responsivePriority: 1, targets: -1 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 2 },
                    { responsivePriority: 4, targets: 3 },
                    { responsivePriority: 10001, targets: 4 },
                    { responsivePriority: 10002, targets: 5 },
                    { responsivePriority: 10003, targets: 8 },
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
                    emptyTable: 'No LPJ Contract data available',
                },
                autoWidth: true,
                drawCallback: function(settings) {
                    var api = this.api();
                    var startIndex = api.page.info().start;
                    api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                        cell.innerHTML = startIndex + i + 1;
                    });
                    api.columns.adjust();
                }
            });

            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#lpjContractTable')) {
                        $('#lpjContractTable').DataTable().columns.adjust().responsive.recalc();
                    }
                }, 300);
            });

            setTimeout(function() {
                if ($.fn.DataTable.isDataTable('#lpjContractTable')) {
                    $('#lpjContractTable').DataTable().columns.adjust().responsive.recalc();
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

        // ===== FILTER =====
        $('#filterButton').on('click', function() {
            $('#filterModal').modal('show');
        });

        $('#resetFilter').on('click', function() {
            $('#filterForm input[type="text"], #filterForm input[type="date"]').val('');
            $('.select2-filter').val('').trigger('change');
        });

        // ===== DELETE =====
        $(document).on('click', '.btn-delete', function(e) {
            e.stopPropagation();
            const name = $(this).data('name');
            const url  = $(this).data('url');

            Swal.fire({
                title: 'Delete LPJ Contract?',
                html: `<div class="text-start">
                    <p>LPJ Contract <strong>${name}</strong> will be permanently deleted.</p>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> All items related to this LPJ Contract will also be deleted.
                    </div>
                </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete!',
                cancelButtonText: 'Cancel',
                focusCancel: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting...',
                        html: 'Please wait...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => { Swal.showLoading(); },
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