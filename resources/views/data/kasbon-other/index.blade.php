@extends('layouts.app')

@section('title', 'Kasbon Other Data')

@section('content')
    <div class="container-fluid kasbonOtherPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Kasbon Other Data</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('kasbon-other', 'tambah')))
                                <a href="{{ route('kasbon-other.create') }}" class="btn btn-light">
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

                        @if (!empty($currentFilters) && array_filter($currentFilters))
                            <div class="alert alert-info alert-dismissible fade show">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Active Filters:</strong>
                                @if (!empty($currentFilters['id_jo_other']))
                                    &nbsp;<span class="badge bg-primary">JO Other:
                                        {{ $currentFilters['id_jo_other'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_dep']))
                                    &nbsp;<span class="badge bg-primary">Departemen:
                                        {{ $currentFilters['id_md_dep'] }}</span>
                                @endif
                                @if (!empty($currentFilters['id_md_cabang']))
                                    &nbsp;<span class="badge bg-primary">Branch:
                                        {{ $currentFilters['id_md_cabang'] }}</span>
                                @endif
                                @if (!empty($currentFilters['tgl_kasbon_from']))
                                    &nbsp;<span class="badge bg-primary">From:
                                        {{ $currentFilters['tgl_kasbon_from'] }}</span>
                                @endif
                                @if (!empty($currentFilters['tgl_kasbon_to']))
                                    &nbsp;<span class="badge bg-primary">To: {{ $currentFilters['tgl_kasbon_to'] }}</span>
                                @endif
                                <a href="{{ route('kasbon-other.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Reset Filter
                                </a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="kasbonOtherTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>CA No</th>
                                        <th>CA Date</th>
                                        <th>JO No</th>
                                        <th>Dep.</th>
                                        <th>Branch</th>
                                        <th>Release To</th>
                                        <th class="text-center">Items</th>
                                        <th class="text-end">Total HPP</th>
                                        <th class="text-end">Total CA</th>
                                        <th class="text-center">Invoice</th>
                                        <th class="text-center" width="12%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kasbonOthers as $kasbon)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $kasbon->id_kasbon_other ?? '-' }}</td>
                                            <td>{{ $kasbon->tgl_kasbon ? $kasbon->tgl_kasbon->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $kasbon->joOther ? $kasbon->joOther->no_jo_other : '-' }}</td>
                                            <td>{{ $kasbon->departemen ? $kasbon->departemen->nama_dep : '-' }}</td>
                                            <td>{{ $kasbon->cabang ? $kasbon->cabang->nama_branch : '-' }}</td>
                                            <td>{{ $kasbon->release ? $kasbon->release->nama_release : '-' }}</td>
                                            <td class="text-center">{{ $kasbon->items->count() }}</td>
                                            <td class="text-end">
                                                {{ number_format($kasbon->items->sum('nilai_hpp_other_item'), 2, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($kasbon->items->sum('nilai_kasbon'), 2, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('kasbon-other', 'detail')))
                                                    <a href="{{ route('kasbon-other.export-pdf', $kasbon->id) }}"
                                                        class="btn btn-sm btn-danger" target="_blank" title="Export PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('kasbon-other', 'detail')))
                                                        <a href="{{ route('kasbon-other.show', $kasbon->id) }}"
                                                            class="btn btn-sm btn-info" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('kasbon-other', 'ubah')))
                                                        <a href="{{ route('kasbon-other.edit', $kasbon->id) }}"
                                                            class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('kasbon-other', 'hapus')))
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $kasbon->id }}"
                                                            data-name="{{ $kasbon->id_kasbon_other }}"
                                                            data-url="{{ route('kasbon-other.destroy', $kasbon->id) }}"
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
                                                <h5 class="text-muted">No Kasbon Other Data</h5>
                                                <p class="text-muted mb-0">Start by adding a new Kasbon Other</p>
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
                    <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter Kasbon Other</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET" action="{{ route('kasbon-other.index') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">JO Other</label>
                                <select class="form-select select2-filter" name="id_jo_other"
                                    data-placeholder="-- Select JO Other --">
                                    <option value=""></option>
                                    @foreach ($joOthers as $jo)
                                        <option value="{{ $jo->id_jo_other }}"
                                            {{ request('id_jo_other') == $jo->id_jo_other ? 'selected' : '' }}>
                                            {{ $jo->no_jo_other }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Departemen</label>
                                <select class="form-select select2-filter" name="id_md_dep"
                                    data-placeholder="-- Select Departemen --">
                                    <option value=""></option>
                                    @foreach ($departemens as $dep)
                                        <option value="{{ $dep->id_md_dep }}"
                                            {{ request('id_md_dep') == $dep->id_md_dep ? 'selected' : '' }}>
                                            {{ $dep->nama_dep }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Branch</label>
                                <select class="form-select select2-filter" name="id_md_cabang"
                                    data-placeholder="-- Select Branch --">
                                    <option value=""></option>
                                    @foreach ($cabangs as $cabang)
                                        <option value="{{ $cabang->id_md_branch }}"
                                            {{ request('id_md_cabang') == $cabang->id_md_branch ? 'selected' : '' }}>
                                            {{ $cabang->nama_branch }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Kasbon Date From</label>
                                <input type="date" class="form-control" name="tgl_kasbon_from"
                                    value="{{ request('tgl_kasbon_from', '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Kasbon Date To</label>
                                <input type="date" class="form-control" name="tgl_kasbon_to"
                                    value="{{ request('tgl_kasbon_to', '') }}">
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

    {{-- EXPORT MODAL --}}
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-black" style="background-color: #d1fae5">
                    <h5 class="modal-title"><i class="fas fa-download me-2"></i>Export Kasbon Other</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Choose the export format:</p>
                    @php
                        $exportParams = request()->only([
                            'id_jo_other',
                            'id_md_dep',
                            'id_md_cabang',
                            'tgl_kasbon_from',
                            'tgl_kasbon_to',
                        ]);
                        $hasActiveFilters = array_filter($exportParams);
                        $excelUrl =
                            route('kasbon-other.export') .
                            '?' .
                            http_build_query(array_merge($exportParams, ['format' => 'excel']));
                        $pdfUrl =
                            route('kasbon-other.export') .
                            '?' .
                            http_build_query(array_merge($exportParams, ['format' => 'pdf']));
                    @endphp
                    <div class="alert alert-info py-2">
                        <i class="fas fa-info-circle me-1"></i>
                        <small>Export will include <strong>{{ $hasActiveFilters ? 'filtered' : 'all' }} Kasbon Other
                                data</strong>.</small>
                    </div>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
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

        .kasbonOtherPage .card {
            border: none;
            border-radius: 10px;
        }

        .kasbonOtherPage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .kasbonOtherPage .btn-sm {
            transition: transform 0.2s;
        }

        .kasbonOtherPage .btn-sm:hover {
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            var hasData = $('#kasbonOtherTable tbody tr').length > 0 &&
                !$('#kasbonOtherTable tbody tr td[colspan]').length;

            if (hasData) {
                var table = $('#kasbonOtherTable').DataTable({
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
                        {
                            responsivePriority: 1,
                            targets: -1
                        }, // Action selalu tampil
                        {
                            responsivePriority: 2,
                            targets: 1
                        }, // CA No prioritas kedua
                        {
                            responsivePriority: 10001,
                            targets: 4
                        }, // Dep disembunyikan duluan
                        {
                            responsivePriority: 10002,
                            targets: 5
                        }, // Branch disembunyikan duluan
                        {
                            responsivePriority: 10003,
                            targets: 6
                        }
                    ],
                    language: {
                        search: 'Search:',
                        emptyTable: 'No Kasbon Other data available'
                    },
                    drawCallback: function(settings) {
                        var api = this.api(),
                            startIndex = api.page.info().start;
                        api.column(0, {
                            page: 'current'
                        }).nodes().each(function(cell, i) {
                            cell.innerHTML = startIndex + i + 1;
                        });
                    }
                });
            }

            $('#filterModal').on('shown.bs.modal', function() {
                $('.select2-filter').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#filterModal'),
                            placeholder: $(this).data('placeholder') || '-- Select --',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                });
            });

            $('#filterButton').on('click', function() {
                $('#filterModal').modal('show');
            });
            $('#exportButton').on('click', function() {
                $('#exportModal').modal('show');
            });
            $('#resetFilter').on('click', function() {
                $('#filterForm input[type="date"]').val('');
                $('.select2-filter').val('').trigger('change');
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.stopPropagation();
                const name = $(this).data('name'),
                    url = $(this).data('url');
                Swal.fire({
                    title: 'Delete Kasbon Other?',
                    html: `<div class="text-start"><p>Kasbon Other <strong>${name}</strong> will be permanently deleted.</p>
                        <div class="alert alert-warning mt-3 mb-0"><i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> All related items will also be deleted.</div></div>`,
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
                            didOpen: () => {
                                Swal.showLoading();
                            }
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
