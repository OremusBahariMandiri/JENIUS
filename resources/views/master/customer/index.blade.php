@extends('layouts.app')

@section('title', 'Data Customer')

@section('content')
    <div class="container-fluid customerPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-users me-2"></i>Data Customer</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('customer', 'tambah')))
                                <a href="{{ route('customer.create') }}" class="btn btn-light">
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
                        @if (!empty($currentFilters['search']))
                            <div class="alert alert-info" role="alert" id="filterActiveAlert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Filter Aktif:</strong>
                                @if (!empty($currentFilters['search']))
                                    Pencarian: <span class="badge bg-primary">{{ $currentFilters['search'] }}</span>
                                @endif
                                <a href="{{ route('customer.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Reset Filter
                                </a>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="customerTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>NPWP</th>
                                        <th>Address</th>
                                        <th class="text-center" width="15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $index => $customer)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if (!empty($currentFilters['search']))
                                                    {!! str_ireplace(
                                                        $currentFilters['search'],
                                                        '<mark>' . e($currentFilters['search']) . '</mark>',
                                                        e($customer->customer),
                                                    ) !!}
                                                @else
                                                    {{ $customer->customer }}
                                                @endif
                                            </td>
                                            <td>{{ $customer->email ?? '-' }}</td>
                                            <td>{{ $customer->phone ?? '-' }}</td>
                                            <td>{{ $customer->npwp ?? '-' }}</td>
                                            <td>{{ Str::limit($customer->address, 50) }}</td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('customer', 'detail')))
                                                        <a href="{{ route('customer.show', $customer->id_md_cust) }}"
                                                            class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                                            title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif

                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('customer', 'ubah')))
                                                        <a href="{{ route('customer.edit', $customer->id_md_cust) }}"
                                                            class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif

                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('customer', 'hapus')))
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $customer->id_md_cust }}"
                                                            data-name="{{ $customer->customer }}"
                                                            data-url="{{ route('customer.destroy', $customer->id_md_cust) }}"
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

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small" id="paginationInfo">
                                {{-- DataTables will handle this --}}
                            </div>
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
                    <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter Data Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET" action="{{ route('customer.index') }}">
                        <div class="row mb-3">
                            {{-- Search --}}
                            <div class="col-md-12 mb-3">
                                <label for="filter_search" class="form-label fw-bold">Cari Customer</label>
                                <input type="text" class="form-control" id="filter_search" name="search"
                                    placeholder="Nama, kode, email, telepon, NPWP, alamat..."
                                    value="{{ $currentFilters['search'] ?? '' }}">
                                <small class="text-muted">Cari berdasarkan nama, kode, email, telepon, NPWP, atau
                                    alamat.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" id="resetFilter">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                            <button type="button" class="btn btn-success" id="applyFilter">
                                <i class="fas fa-search me-1"></i> Terapkan Filter
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
                    <h5 class="modal-title"><i class="fas fa-download me-2"></i>Export Data Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Pilih format export yang diinginkan:</p>
                    @if (!empty($currentFilters['search']))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export akan menggunakan filter aktif:
                                <strong>{{ $currentFilters['search'] }}</strong></small>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export akan mengekspor <strong>semua data customer</strong> (tidak ada filter
                                aktif).</small>
                        </div>
                    @endif
                    @php
                        $exportBase = route('customer.export');
                        $exportParams = array_merge(request()->only(['search']), []);
                        $excelUrl =
                            $exportBase . '?' . http_build_query(array_merge($exportParams, ['format' => 'excel']));
                        $pdfUrl = $exportBase . '?' . http_build_query(array_merge($exportParams, ['format' => 'pdf']));
                    @endphp
                    <div class="d-grid gap-2">
                        <a href="{{ $excelUrl }}" class="btn btn-outline-success">
                            <i class="fas fa-file-excel me-2"></i> Export ke Excel (.xlsx)
                        </a>
                        {{-- target="_blank" → PDF terbuka di tab baru, bukan download --}}
                        <a href="{{ $pdfUrl }}" class="btn btn-outline-danger" target="_blank"
                            rel="noopener noreferrer">
                            <i class="fas fa-file-pdf me-2"></i> Export ke PDF
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <style>
        .customerPage .card {
            border: none;
            border-radius: 10px;
        }

        .customerPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .customerPage .dataTables_wrapper .dataTables_filter,
        .customerPage .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem !important;
        }

        .customerPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
        }

        .customerPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
        }

        .customerPage .dataTables_wrapper .dataTables_length select {
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        }

        .customerPage #customerTable tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .customerPage .btn-sm {
            transition: transform 0.2s;
        }

        .customerPage .btn-sm:hover {
            transform: scale(1.1);
        }

        .customerPage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #10b981 !important;
            color: white !important;
            border: 1px solid #10b981 !important;
        }

        .customerPage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #10b981 !important;
            color: white !important;
            border: 1px solid #10b981 !important;
        }

        .customerPage .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        #filterActiveAlert {
            display: block !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // ===== DATATABLE =====
            if ($.fn.DataTable.isDataTable('#customerTable')) {
                $('#customerTable').DataTable().destroy();
            }

            var table = $('#customerTable').DataTable({
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
                    }
                ],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
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
                    if ($.fn.DataTable.isDataTable('#customerTable')) {
                        $('#customerTable').DataTable().columns.adjust().responsive.recalc();
                    }
                }, 300);
            });

            // ===== FILTER BUTTON =====
            $('#filterButton').click(function() {
                $('#filterModal').modal('show');
            });

            // ===== APPLY FILTER =====
            $('#applyFilter').click(function() {
                $('#filterForm').submit();
            });

            // ===== RESET FILTER =====
            $('#resetFilter').click(function() {
                $('#filter_search').val('');
            });

            // ===== EXPORT BUTTON =====
            $('#exportButton').click(function() {
                $('#exportModal').modal('show');
            });

            // ===== TOOLTIPS =====
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(el) {
                return new bootstrap.Tooltip(el);
            });

            // ===== DELETE =====
            $(document).on('click', '.btn-delete', function(e) {
                e.stopPropagation();
                const name = $(this).data('name');
                const url = $(this).data('url');
                Swal.fire({
                    title: 'Delete Customer?',
                    html: `Customer <strong>${name}</strong> akan dihapus.`,
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

            // ===== CLICK ROW TO DETAIL =====
            $('#customerTable tbody').on('click', 'tr', function(e) {
                if ($(e.target).is('button,a,i') || $(e.target).closest('button,a').length) return;
                var detailLink = $(this).find('a[title="Detail"]').attr('href');
                if (detailLink) window.location.href = detailLink;
            });

            // ===== AUTO-HIDE ALERTS =====
            setTimeout(function() {
                $(".alert-success, .alert-danger").fadeOut("slow");
            }, 5000);
        });
    </script>
@endpush
