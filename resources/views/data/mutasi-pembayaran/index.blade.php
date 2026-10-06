@extends('layouts.app')

@section('title', 'Payment Mutation')

@section('content')
    <div class="container-fluid mutasiPembayaranPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Payment Mutation</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('mutasi-pembayaran', 'tambah')))
                                <a href="{{ route('mutasi-pembayaran.create') }}" class="btn btn-light">
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
                            $hasActiveFilter = !empty($currentFilters) &&
                                array_filter(array_intersect_key(
                                    $currentFilters,
                                    array_flip(['nomor', 'tanggal_from', 'tanggal_to', 'id_md_chart_of_account'])
                                ));
                        @endphp
                        @if ($hasActiveFilter)
                            <div class="alert alert-info alert-dismissible fade show" id="filterActiveAlert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Active Filters:</strong>
                                @if (!empty($currentFilters['nomor']))
                                    &nbsp;<span class="badge bg-primary">Number: {{ $currentFilters['nomor'] }}</span>
                                @endif
                                @if (!empty($currentFilters['tanggal_from']))
                                    &nbsp;<span class="badge bg-primary">From: {{ $currentFilters['tanggal_from'] }}</span>
                                @endif
                                @if (!empty($currentFilters['tanggal_to']))
                                    &nbsp;<span class="badge bg-primary">To: {{ $currentFilters['tanggal_to'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_chart_of_account']))
                                    &nbsp;<span class="badge bg-primary">COA: {{ $currentFilters['coa_label'] ?? $currentFilters['id_md_chart_of_account'] }}</span>
                                @endif
                                <a href="{{ route('mutasi-pembayaran.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Reset Filter
                                </a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif


                        <div class="table-responsive">
                            <table id="mutasiPembayaranTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>Number</th>
                                        <th>Date</th>
                                        <th>Check No.</th>
                                        <th class="text-end">Rate</th>
                                        <th>Memo</th>
                                        <th class="text-center">Vouchers</th>
                                        <th class="text-center" width="12%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mutasiPembayarans as $mutasi)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge" style="background:#059669; font-size:.85rem;">
                                                    {{ $mutasi->nomor ?? '-' }}
                                                </span>
                                            </td>
                                            <td>{{ $mutasi->tanggal ? \Carbon\Carbon::parse($mutasi->tanggal)->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $mutasi->no_cek ?? '-' }}</td>
                                            <td class="text-end">{{ $mutasi->kurs ? number_format($mutasi->kurs, 2, ',', '.') : '-' }}</td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($mutasi->memo, 60) }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $mutasi->vouchers->count() }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('mutasi-pembayaran', 'detail')))
                                                        <a href="{{ route('mutasi-pembayaran.show', $mutasi->id) }}"
                                                            class="btn btn-sm btn-info" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('mutasi-pembayaran', 'ubah')))
                                                        <a href="{{ route('mutasi-pembayaran.edit', $mutasi->id) }}"
                                                            class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('mutasi-pembayaran', 'hapus')))
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $mutasi->id }}"
                                                            data-name="{{ $mutasi->nomor }}"
                                                            data-url="{{ route('mutasi-pembayaran.destroy', $mutasi->id) }}"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                                <h5 class="text-muted">No Payment Mutation Data</h5>
                                                <p class="text-muted mb-0">Start by adding a new Payment Mutation</p>
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
                    <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter Payment Mutation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET" action="{{ route('mutasi-pembayaran.index') }}">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Number</label>
                                <input type="text" class="form-control" name="nomor"
                                    value="{{ request('nomor', '') }}" placeholder="Search document number...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Chart of Account</label>
                                <select class="form-select select2-filter" name="id_md_chart_of_account"
                                    id="filterCoa" data-placeholder="-- Select COA --">
                                    <option value=""></option>
                                    @foreach ($chartOfAccounts as $coa)
                                        <option value="{{ $coa->id }}"
                                            {{ request('id_md_chart_of_account') == $coa->id ? 'selected' : '' }}>
                                            {{ $coa->kode_akun }} - {{ $coa->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date From</label>
                                <input type="date" class="form-control" name="tanggal_from"
                                    value="{{ request('tanggal_from', '') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date To</label>
                                <input type="date" class="form-control" name="tanggal_to"
                                    value="{{ request('tanggal_to', '') }}">
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
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

    <style>
        .mutasiPembayaranPage .card {
            border: none;
            border-radius: 10px;
        }

        .mutasiPembayaranPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .mutasiPembayaranPage #mutasiPembayaranTable tbody tr:hover {
            background-color: #f0fdf4;
        }

        .mutasiPembayaranPage .btn-sm {
            transition: transform 0.2s;
        }

        .mutasiPembayaranPage .btn-sm:hover {
            transform: scale(1.1);
        }

        .mutasiPembayaranPage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #059669 !important;
            color: white !important;
            border: 1px solid #059669 !important;
        }

        .mutasiPembayaranPage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #059669 !important;
            color: white !important;
            border: 1px solid #059669 !important;
        }

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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            // ===== DATATABLE =====
            var hasData = $('#mutasiPembayaranTable tbody tr').length > 0 &&
                !$('#mutasiPembayaranTable tbody tr td[colspan]').length;

            if (hasData) {
                if ($.fn.DataTable.isDataTable('#mutasiPembayaranTable')) {
                    $('#mutasiPembayaranTable').DataTable().destroy();
                }

                var table = $('#mutasiPembayaranTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, 'All']
                    ],
                    order: [
                        [2, 'desc']
                    ],
                    columnDefs: [
                        { orderable: false, targets: 0 },
                        { orderable: false, targets: -1 },
                        { responsivePriority: 1, targets: -1 },
                        { responsivePriority: 2, targets: 1 },  // Number
                        { responsivePriority: 3, targets: 7 },  // Vouchers
                        { responsivePriority: 4, targets: 2 },  // Date
                        { responsivePriority: 5, targets: 3 },  // COA
                        { responsivePriority: 10001, targets: 4 }, // Check No
                        { responsivePriority: 10002, targets: 5 }, // Rate
                        { responsivePriority: 10003, targets: 6 }, // Memo
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
                        emptyTable: 'No Payment Mutation data available'
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
                        if ($.fn.DataTable.isDataTable('#mutasiPembayaranTable')) {
                            $('#mutasiPembayaranTable').DataTable().columns.adjust().responsive.recalc();
                        }
                    }, 300);
                });
            }

            // ===== SELECT2 =====
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
                const url = $(this).data('url');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                Swal.fire({
                    title: 'Delete Payment Mutation?',
                    html: `<div class="text-start">
                        <p>Mutation <strong>${name}</strong> will be permanently deleted.</p>
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> All vouchers and details within this mutation will also be deleted.
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
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Deleting...',
                        html: 'Please wait...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading(),
                    });

                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: data.message || 'Payment Mutation deleted successfully.',
                                icon: 'success',
                                timer: 1800,
                                showConfirmButton: false,
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                title: 'Cannot Delete',
                                html: `<div class="text-start"><p>${data.message}</p></div>`,
                                icon: 'error',
                                confirmButtonColor: '#059669',
                                confirmButtonText: 'Understood',
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    });
                });
            });

            // ===== AUTO HIDE ALERTS =====
            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush