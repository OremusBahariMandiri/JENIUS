@extends('layouts.app')

@section('title', 'Data Item')

@section('content')
    <div class="container-fluid invoicePage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-file-invoice me-2"></i>Data Item</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('invoice', 'tambah')))
                                <a href="{{ route('invoice.create') }}" class="btn btn-light">
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
                        @php
                            $hasFilter =
                                !empty($currentFilters['search']) ||
                                !empty($currentFilters['invoice_ctg']) ||
                                !empty($currentFilters['invoice_typ']) ||
                                !empty($currentFilters['jo_ctg']);
                        @endphp
                        @if ($hasFilter)
                            <div class="alert alert-info" id="filterActiveAlert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Filter Aktif:</strong>
                                @if (!empty($currentFilters['search']))
                                    Pencarian: <span class="badge bg-primary">{{ $currentFilters['search'] }}</span>
                                @endif
                                @if (!empty($currentFilters['invoice_ctg']))
                                    Category: <span class="badge bg-primary">{{ $currentFilters['invoice_ctg'] }}</span>
                                @endif
                                @if (!empty($currentFilters['invoice_typ']))
                                    Item: <span class="badge bg-primary">{{ $currentFilters['invoice_typ'] }}</span>
                                @endif
                                @if (!empty($currentFilters['jo_ctg']))
                                    JO Ctg: <span class="badge bg-primary">{{ ucfirst($currentFilters['jo_ctg']) }}</span>
                                @endif
                                <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Reset Filter
                                </a>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="invoiceTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="4%">No</th>
                                        <th width="10%">JO Category</th>
                                        <th>Category</th>
                                        <th>Item</th>
                                        <th>Note</th>
                                        <th class="text-center" width="15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $index => $invoice)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($invoice->jo_ctg === 'contract')
                                                    Contract
                                                @elseif($invoice->jo_ctg === 'tramper')
                                                    Tramper
                                                @elseif($invoice->jo_ctg === 'other')
                                                    Other
                                                @elseif($invoice->jo_ctg === 'general')
                                                    General
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($currentFilters['search']))
                                                    {!! str_ireplace(
                                                        $currentFilters['search'],
                                                        '<mark>' . e($currentFilters['search']) . '</mark>',
                                                        e($invoice->invoice_ctg),
                                                    ) !!}
                                                @else
                                                    {{ $invoice->invoice_ctg }}
                                                @endif
                                            </td>
                                            <td>{{ $invoice->invoice_typ }}</td>
                                            <td>{{ $invoice->note ? Str::limit($invoice->note, 50) : '-' }}</td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('invoice', 'detail')))
                                                        <a href="{{ route('invoice.show', $invoice->id_md_invoice) }}"
                                                            class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                                            title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('invoice', 'ubah')))
                                                        <a href="{{ route('invoice.edit', $invoice->id_md_invoice) }}"
                                                            class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('invoice', 'hapus')))
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $invoice->id_md_invoice }}"
                                                            data-name="{{ $invoice->invoice_ctg }}"
                                                            data-url="{{ route('invoice.destroy', $invoice->id_md_invoice) }}"
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
                    <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter Data Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET" action="{{ route('invoice.index') }}">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Cari Invoice</label>
                                <input type="text" class="form-control" name="search"
                                    placeholder="Category, item, atau note..."
                                    value="{{ $currentFilters['search'] ?? '' }}">
                                <small class="text-muted">Cari berdasarkan kategori, item, atau catatan.</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">JO Category</label>
                                <select class="form-select" name="jo_ctg">
                                    <option value="">-- Semua JO Category --</option>
                                    @foreach ($joCtgOptions as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ ($currentFilters['jo_ctg'] ?? '') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Category</label>
                                <select class="form-select" name="invoice_ctg">
                                    <option value="">-- Semua Category --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}"
                                            {{ ($currentFilters['invoice_ctg'] ?? '') === $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Item</label>
                                <select class="form-select" name="invoice_typ">
                                    <option value="">-- Semua Item --</option>
                                    @foreach ($types as $typ)
                                        <option value="{{ $typ }}"
                                            {{ ($currentFilters['invoice_typ'] ?? '') === $typ ? 'selected' : '' }}>
                                            {{ $typ }}
                                        </option>
                                    @endforeach
                                </select>
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
                    <h5 class="modal-title"><i class="fas fa-download me-2"></i>Export Data Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Pilih format export yang diinginkan:</p>
                    @if ($hasFilter)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export akan menggunakan <strong>filter yang sedang aktif</strong>.</small>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export akan mengekspor <strong>semua data item</strong> (tidak ada filter
                                aktif).</small>
                        </div>
                    @endif
                    @php
                        $exportParams = request()->only(['search', 'invoice_ctg', 'invoice_typ', 'jo_ctg']);
                        $excelUrl =
                            route('invoice.export') .
                            '?' .
                            http_build_query(array_merge($exportParams, ['format' => 'excel']));
                        $pdfUrl =
                            route('invoice.export') .
                            '?' .
                            http_build_query(array_merge($exportParams, ['format' => 'pdf']));
                    @endphp
                    <div class="d-grid gap-2">
                        <a href="{{ $excelUrl }}" class="btn btn-outline-success">
                            <i class="fas fa-file-excel me-2"></i> Export ke Excel (.xlsx)
                        </a>
                        <a href="{{ $pdfUrl }}" class="btn btn-outline-danger" target="_blank">
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
        .invoicePage .card {
            border: none;
            border-radius: 10px;
        }

        .invoicePage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        /* Fix DataTable pagination arrows */
        .invoicePage .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem !important;
            border-radius: 4px !important;
            border: 1px solid #dee2e6 !important;
            margin: 0 2px !important;
            line-height: 1.5 !important;
            color: #212529 !important;
        }

        .invoicePage .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .invoicePage .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #10b981 !important;
            color: #fff !important;
            border-color: #10b981 !important;
        }

        .invoicePage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #10b981 !important;
            color: #fff !important;
            border-color: #10b981 !important;
        }

        .invoicePage .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .invoicePage .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            color: #adb5bd !important;
            background: transparent !important;
            border-color: #dee2e6 !important;
            cursor: default !important;
        }

        .invoicePage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
        }

        .invoicePage .dataTables_wrapper .dataTables_length select {
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        }

        .invoicePage #invoiceTable tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .invoicePage .btn-sm {
            transition: transform 0.2s;
        }

        .invoicePage .btn-sm:hover {
            transform: scale(1.1);
        }

        .invoicePage .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
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

            if ($.fn.DataTable.isDataTable('#invoiceTable')) {
                $('#invoiceTable').DataTable().destroy();
            }

            var table = $('#invoiceTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                order: [
                    [2, 'asc']
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
                        next: "›",
                        previous: "‹"
                    }
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
                    if ($.fn.DataTable.isDataTable('#invoiceTable')) {
                        $('#invoiceTable').DataTable().columns.adjust().responsive.recalc();
                    }
                }, 300);
            });

            // ===== FILTER =====
            $('#filterButton').click(function() {
                $('#filterModal').modal('show');
            });
            $('#applyFilter').click(function() {
                $('#filterForm').submit();
            });
            $('#resetFilter').click(function() {
                $('#filterForm')[0].reset();
            });

            // ===== EXPORT =====
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
                    title: 'Delete Invoice?',
                    html: `Invoice <strong>${name}</strong> akan dihapus.`,
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
            $('#invoiceTable tbody').on('click', 'tr', function(e) {
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
