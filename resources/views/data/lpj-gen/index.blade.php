@extends('layouts.app')

@section('title', 'LPJ General Data')

@section('content')
    <div class="container-fluid lpjGenPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-file-invoice me-2"></i>LPJ General Data</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>

                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('lpj-gen', 'tambah')))
                                <a href="{{ route('lpj-gen.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus-circle me-1"></i> Add
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Active Filters --}}
                        @if (array_filter($currentFilters))
                            <div class="alert alert-info alert-dismissible fade show">
                                <i class="fas fa-info-circle me-2"></i><strong>Active Filters:</strong>
                                @if (!empty($currentFilters['no_lpj_gen']))
                                    <span class="badge bg-primary ms-1">No LPJ: {{ $currentFilters['no_lpj_gen'] }}</span>
                                @endif
                                @if (!empty($currentFilters['date_from']))
                                    <span class="badge bg-primary ms-1">From: {{ $currentFilters['date_from'] }}</span>
                                @endif
                                @if (!empty($currentFilters['date_to']))
                                    <span class="badge bg-primary ms-1">To: {{ $currentFilters['date_to'] }}</span>
                                @endif
                                <a href="{{ route('lpj-gen.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Reset
                                </a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="lpjGenTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>No. LPJ</th>
                                        <th>Date</th>
                                        <th class="text-center">Kasbon</th>
                                        <th class="text-center">Items</th>
                                        <th class="text-end">Total CA (IDR)</th>
                                        <th class="text-end">Total LPJ (IDR)</th>
                                        <th class="text-center">Evidence</th>
                                        <th class="text-center">PDF</th>
                                        <th class="text-center" width="12%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lpjGens as $lpj)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="fw-semibold">{{ $lpj->no_lpj_gen ?? '-' }}</td>
                                            <td>{{ $lpj->date ? $lpj->date->format('d/m/Y') : '-' }}</td>
                                            <td class="text-center">{{ $lpj->kasbons->count() }}</td>
                                            <td class="text-center">{{ $lpj->items->count() }}</td>
                                            <td class="text-end">{{ number_format($lpj->amount, 2, ',', '.') }}</td>
                                            <td class="text-end">
                                                {{ number_format($lpj->items->sum('amount_lpj'), 2, ',', '.') }}</td>
                                            <td class="text-center">
                                                @if ($lpj->evidence)
                                                    <a href="{{ Storage::url($lpj->evidence) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-warning" title="View Evidence">
                                                        <i class="fas fa-paperclip"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('lpj-gen', 'detail')))
                                                    <a href="{{ route('lpj-gen.export-pdf', $lpj->id) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('lpj-gen', 'detail')))
                                                        <a href="{{ route('lpj-gen.show', $lpj->id) }}"
                                                            class="btn btn-sm btn-info" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('lpj-gen', 'ubah')))
                                                        <a href="{{ route('lpj-gen.edit', $lpj->id) }}"
                                                            class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('lpj-gen', 'hapus')))
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $lpj->id }}"
                                                            data-name="{{ $lpj->no_lpj_gen }}"
                                                            data-url="{{ route('lpj-gen.destroy', $lpj->id) }}"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                                <h5 class="text-muted">No LPJ General Data</h5>
                                                <p class="text-muted mb-0">Start by adding a new LPJ General</p>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-black" style="background-color: #d1fae5">
                    <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter LPJ General</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET" action="{{ route('lpj-gen.index') }}">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">No. LPJ</label>
                                <input type="text" class="form-control" name="no_lpj_gen"
                                    value="{{ request('no_lpj_gen', '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date From</label>
                                <input type="date" class="form-control" name="date_from"
                                    value="{{ request('date_from', '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date To</label>
                                <input type="date" class="form-control" name="date_to"
                                    value="{{ request('date_to', '') }}">
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" id="resetFilter">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Apply Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- EXPORT MODAL --}}
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-black" style="background-color: #d1fae5">
                    <h5 class="modal-title"><i class="fas fa-download me-2"></i>Export LPJ General</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Choose the export format:</p>
                    @php
                        $exportParams = request()->only(['no_lpj_gen', 'date_from', 'date_to']);
                        $hasActiveFilters = array_filter($exportParams);
                        $excelUrl =
                            route('lpj-gen.export') .
                            '?' .
                            http_build_query(array_merge($exportParams, ['format' => 'excel']));
                        $pdfUrl =
                            route('lpj-gen.export') .
                            '?' .
                            http_build_query(array_merge($exportParams, ['format' => 'pdf']));
                    @endphp
                    @if ($hasActiveFilters)
                        <div class="alert alert-info py-2">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export will use <strong>active filters</strong>.</small>
                        </div>
                    @else
                        <div class="alert alert-info py-2">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Export will include <strong>all LPJ General data</strong>.</small>
                        </div>
                    @endif
                    <div class="d-grid gap-2">
                        <a href="{{ $excelUrl }}" class="btn btn-outline-success">
                            <i class="fas fa-file-excel me-2"></i> Export to Excel (.xlsx)
                        </a>
                        <a href="{{ $pdfUrl }}" class="btn btn-outline-danger" target="_blank">
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
        @csrf @method('DELETE')
    </form>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        .lpjGenPage .card {
            border: none;
            border-radius: 10px;
        }

        .lpjGenPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .lpjGenPage #lpjGenTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        .lpjGenPage .btn-sm {
            transition: transform .2s;
        }

        .lpjGenPage .btn-sm:hover {
            transform: scale(1.1);
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
            var hasData = $('#lpjGenTable tbody tr').length > 0 &&
                !$('#lpjGenTable tbody tr td[colspan]').length;

            if (hasData) {
                $('#lpjGenTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, 'All']
                    ],
                    order: [
                        [2, 'desc']
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: 0
                        },
                        {
                            orderable: false,
                            targets: -1
                        },
                    ],
                    drawCallback: function(settings) {
                        var api = this.api();
                        var start = api.page.info().start;
                        api.column(0, {
                            page: 'current'
                        }).nodes().each(function(cell, i) {
                            cell.innerHTML = start + i + 1;
                        });
                    }
                });
            }

            $('#filterButton').on('click', function() {
                $('#filterModal').modal('show');
            });
            $('#exportButton').on('click', function() {
    $('#exportModal').modal('show');
});

            $('#resetFilter').on('click', function() {
                $('#filterForm input').val('');
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.stopPropagation();
                const name = $(this).data('name');
                const url = $(this).data('url');
                Swal.fire({
                    title: 'Delete LPJ General?',
                    html: `<p>LPJ <strong>${name}</strong> will be permanently deleted.</p>
                    <div class="alert alert-warning mt-2 mb-0 text-start">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Items added from this LPJ will also be removed from their Cash Advance.
                    </div>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete!',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        $('#deleteForm').attr('action', url).submit();
                    }
                });
            });

            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush
