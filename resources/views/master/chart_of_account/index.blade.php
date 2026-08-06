@extends('layouts.app')

@section('title', 'Chart of Account')

@section('content')
<div class="container-fluid coaPage">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-sitemap me-2"></i>Chart of Account</span>
                    <div>
                        <button type="button" class="btn btn-light me-2" id="filterButton">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <button type="button" class="btn btn-light me-2" id="exportButton">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('chart_of_account', 'tambah')))
                        <a href="{{ route('chart-of-account.create') }}" class="btn btn-light">
                            <i class="fas fa-plus-circle me-1"></i> Add
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    {{-- Active Filter Display --}}
                    @php
                        $hasFilter = !empty($currentFilters['search'])
                            || !empty($currentFilters['parent'])
                            || !empty($currentFilters['type'])
                            || !empty($currentFilters['payment_type'])
                            || !empty($currentFilters['id_md_cost_type']);
                    @endphp
                    @if($hasFilter)
                    <div class="alert alert-info" id="filterActiveAlert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Filter Aktif:</strong>
                        @if(!empty($currentFilters['search']))
                            Pencarian: <span class="badge bg-primary">{{ $currentFilters['search'] }}</span>
                        @endif
                        @if(!empty($currentFilters['parent']))
                            @php $selectedParent = $parentAccounts->firstWhere('kode_perkiraan', $currentFilters['parent']); @endphp
                            Parent: <span class="badge bg-primary">
                                {{ $selectedParent ? $selectedParent->kode_perkiraan . ' - ' . $selectedParent->nama : $currentFilters['parent'] }}
                            </span>
                        @endif
                        @if(!empty($currentFilters['type']))
                            Type: <span class="badge bg-primary">{{ $currentFilters['type'] }}</span>
                        @endif
                        @if(!empty($currentFilters['payment_type']))
                            Payment Type: <span class="badge bg-primary">{{ $currentFilters['payment_type'] }}</span>
                        @endif
                        @if(!empty($currentFilters['id_md_cost_type']))
                            @php $selectedCostType = $costTypes->firstWhere('id_md_cost_type', $currentFilters['id_md_cost_type']); @endphp
                            Cost Type: <span class="badge bg-primary">
                                {{ $selectedCostType?->name ?? $currentFilters['id_md_cost_type'] }}
                            </span>
                        @endif
                        <a href="{{ route('chart-of-account.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                            <i class="fas fa-times me-1"></i> Reset Filter
                        </a>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table id="coaTable" class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th width="4%">No</th>
                                    <th width="15%">Parent Account</th>
                                    <th width="10%">No. Account</th>
                                    <th>Account Name</th>
                                    <th width="12%">Tipe Akun</th>
                                    <th width="10%">Type</th>
                                    <th width="10%">Payment Type</th>
                                    <th class="text-end" width="11%">Opening Balance</th>
                                    <th class="text-end" width="11%">Current Balance</th>
                                    <th class="text-center" width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accounts as $account)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($account->parentAccount)
                                            <span>{{ $account->parentAccount->kode_perkiraan }} - </span>
                                            {{ $account->parentAccount->nama }}
                                        @else
                                            <span class="text-muted fst-italic">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($currentFilters['search']))
                                            {!! str_ireplace(
                                                $currentFilters['search'],
                                                '<mark>' . e($currentFilters['search']) . '</mark>',
                                                e($account->no_account ?? '-')
                                            ) !!}
                                        @else
                                            {{ $account->no_account ?? '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($currentFilters['search']))
                                            {!! str_ireplace(
                                                $currentFilters['search'],
                                                '<mark>' . e($currentFilters['search']) . '</mark>',
                                                e($account->account_name)
                                            ) !!}
                                        @else
                                            {{ $account->account_name }}
                                        @endif
                                    </td>
                                    <td>{{ $account->parentAccount?->costType?->name ?? '-' }}</td>
                                    <td>
                                        @if($account->type)
                                        @php
                                            $typeColor = match($account->type) {
                                                'Asset'     => 'primary',
                                                'Revenue'   => 'success',
                                                'Expense'   => 'danger',
                                                'Liability' => 'warning',
                                                'Equity'    => 'secondary',
                                                default     => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $typeColor }}">{{ $account->type }}</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>{{ $account->payment_type ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($account->opening_balance, 2) }}</td>
                                    <td class="text-end">{{ number_format($account->current_balance, 2) }}</td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('chart_of_account', 'detail')))
                                            <a href="{{ route('chart-of-account.show', $account->id_md_chart_of_account) }}"
                                               class="btn btn-sm btn-info"
                                               data-bs-toggle="tooltip" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endif

                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('chart_of_account', 'ubah')))
                                            <a href="{{ route('chart-of-account.edit', $account->id_md_chart_of_account) }}"
                                               class="btn btn-sm btn-warning"
                                               data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('chart_of_account', 'hapus')))
                                            <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete"
                                                    data-id="{{ $account->id_md_chart_of_account }}"
                                                    data-name="{{ $account->account_name }}"
                                                    data-url="{{ route('chart-of-account.destroy', $account->id_md_chart_of_account) }}"
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
                <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter Chart of Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm" method="GET" action="{{ route('chart-of-account.index') }}">
                    <div class="row">
                        {{-- Search --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Cari Account</label>
                            <input type="text" class="form-control" name="search"
                                placeholder="No. account, nama account..."
                                value="{{ $currentFilters['search'] ?? '' }}">
                            <small class="text-muted">Cari berdasarkan nomor akun atau nama akun.</small>
                        </div>

                        {{-- Parent Account --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Parent Account</label>
                            <select class="form-select select2-filter" name="parent" id="filterParent">
                                <option value="">-- All Parent --</option>
                                @foreach($parentAccounts as $parent)
                                    <option value="{{ $parent->kode_perkiraan }}"
                                        {{ ($currentFilters['parent'] ?? '') == $parent->kode_perkiraan ? 'selected' : '' }}>
                                        {{ $parent->kode_perkiraan }} - {{ $parent->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Cost Type --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cost Type</label>
                            <select class="form-select select2-filter" name="id_md_cost_type" id="filterCostType">
                                <option value="">-- All Cost Type --</option>
                                @foreach($costTypes as $costType)
                                    <option value="{{ $costType->id_md_cost_type }}"
                                        {{ ($currentFilters['id_md_cost_type'] ?? '') == $costType->id_md_cost_type ? 'selected' : '' }}>
                                        {{ $costType->name }}
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
                <h5 class="modal-title"><i class="fas fa-download me-2"></i>Export Chart of Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Pilih format export yang diinginkan:</p>
                @if($hasFilter)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        <small>Export akan menggunakan <strong>filter yang sedang aktif</strong>.</small>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        <small>Export akan mengekspor <strong>semua data chart of account</strong> (tidak ada filter aktif).</small>
                    </div>
                @endif
                @php
                    $exportParams = request()->only(['search', 'parent', 'type', 'payment_type', 'id_md_cost_type']);
                    $excelUrl = route('chart-of-account.export') . '?' . http_build_query(array_merge($exportParams, ['format' => 'excel']));
                    $pdfUrl   = route('chart-of-account.export') . '?' . http_build_query(array_merge($exportParams, ['format' => 'pdf']));
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    .coaPage .card { border: none; border-radius: 10px; }
    .coaPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }

    .coaPage .dataTables_wrapper .dataTables_filter,
    .coaPage .dataTables_wrapper .dataTables_length { margin-bottom: 1rem !important; }
    .coaPage .dataTables_wrapper .dataTables_filter { text-align: right !important; }
    .coaPage .dataTables_wrapper .dataTables_filter input {
        margin-left: 5px !important; border-radius: 4px !important;
        border: 1px solid #ced4da !important; padding: 0.375rem 0.75rem !important;
    }
    .coaPage .dataTables_wrapper .dataTables_length select {
        border-radius: 4px !important; border: 1px solid #ced4da !important;
        padding: 0.375rem 2rem 0.375rem 0.75rem !important;
    }

    .coaPage #coaTable tbody tr:hover { background-color: #f8f9fa; cursor: pointer; }
    .coaPage .btn-sm { transition: transform 0.2s; }
    .coaPage .btn-sm:hover { transform: scale(1.1); }
    .coaPage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #10b981 !important; color: white !important;
        border: 1px solid #10b981 !important;
    }
    .coaPage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #10b981 !important; color: white !important;
        border: 1px solid #10b981 !important;
    }
    .coaPage .table-responsive {
        width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // ===== DATATABLE =====
    if ($.fn.DataTable.isDataTable('#coaTable')) {
        $('#coaTable').DataTable().destroy();
    }

    var table = $('#coaTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: 0 },
            { orderable: false, targets: -1 }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries per page",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
        },
        autoWidth: true,
        drawCallback: function(settings) {
            var api = this.api();
            var startIndex = api.page.info().start;
            api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                cell.innerHTML = startIndex + i + 1;
            });
            api.columns.adjust();
        }
    });

    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if ($.fn.DataTable.isDataTable('#coaTable')) {
                $('#coaTable').DataTable().columns.adjust().responsive.recalc();
            }
        }, 300);
    });

    // ===== SELECT2 INIT =====
    function initSelect2() {
        $('.select2-filter').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#filterModal'),
            allowClear: true,
        });
    }

    // Init saat modal dibuka (agar dropdown muncul dalam modal, bukan di body)
    $('#filterModal').on('shown.bs.modal', function() {
        initSelect2();
    });

    // ===== FILTER =====
    $('#filterButton').click(function() {
        $('#filterModal').modal('show');
    });
    $('#applyFilter').click(function() {
        $('#filterForm').submit();
    });
    $('#resetFilter').click(function() {
        // Reset native form values
        $('#filterForm')[0].reset();
        // Reset semua Select2
        $('.select2-filter').val(null).trigger('change');
    });

    // ===== EXPORT =====
    $('#exportButton').click(function() {
        $('#exportModal').modal('show');
    });

    // ===== TOOLTIPS =====
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });

    // ===== DELETE =====
    $(document).on('click', '.btn-delete', function(e) {
        e.stopPropagation();
        const name = $(this).data('name');
        const url  = $(this).data('url');
        Swal.fire({
            title: 'Delete Account?',
            html: `Account <strong>${name}</strong> akan dihapus.`,
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
    $('#coaTable tbody').on('click', 'tr', function(e) {
        if ($(e.target).is('button, a, i') || $(e.target).closest('button, a').length) return;
        var detailLink = $(this).find('a[title="Detail"]').attr('href');
        if (detailLink) window.location.href = detailLink;
    });

    // ===== AUTO-HIDE ALERTS =====
    setTimeout(function() { $(".alert-success, .alert-danger").fadeOut("slow"); }, 5000);
});
</script>
@endpush