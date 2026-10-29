@extends('layouts.app')

@section('title', 'Receive Mutation')

@section('content')
    <div class="container-fluid mutasiPenerimaanPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-hand-holding-usd me-2"></i>Receive Mutation</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('mutasi-penerimaan', 'tambah')))
                                <a href="{{ route('mutasi-penerimaan.create') }}" class="btn btn-light">
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

                        <div class="table-responsive">
                            <table id="mutasiPenerimaanTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>Number</th>
                                        <th>Date</th>
                                        <th>No. Cek / Ref</th>
                                        <th class="text-end">Rate</th>
                                        <th>Memo</th>
                                        <th class="text-center">Vouchers</th>
                                        <th class="text-center" width="12%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mutasiPenerimaans as $mutasi)
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
                                            <td><small class="text-muted">{{ Str::limit($mutasi->memo, 50) }}</small></td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $mutasi->vouchers->count() }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('mutasi-penerimaan', 'detail')))
                                                        <a href="{{ route('mutasi-penerimaan.show', $mutasi->id) }}"
                                                            class="btn btn-sm btn-info" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('mutasi-penerimaan', 'ubah')))
                                                        <a href="{{ route('mutasi-penerimaan.edit', $mutasi->id) }}"
                                                            class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('mutasi-penerimaan', 'hapus')))
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $mutasi->id }}"
                                                            data-name="{{ $mutasi->nomor }}"
                                                            data-url="{{ route('mutasi-penerimaan.destroy', $mutasi->id) }}"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                                <h5 class="text-muted">No Receive Mutation Data</h5>
                                                <p class="text-muted mb-0">Start by adding a new Receive Mutation</p>
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
                    <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter Receive Mutation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET" action="{{ route('mutasi-penerimaan.index') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Number</label>
                                <input type="text" class="form-control" name="nomor"
                                    value="{{ request('nomor', '') }}" placeholder="Search document number...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No. Cek / Ref</label>
                                <input type="text" class="form-control" name="search"
                                    value="{{ request('search', '') }}" placeholder="Search ref number...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date From</label>
                                <input type="date" class="form-control" name="tanggal_dari"
                                    value="{{ request('tanggal_dari', '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date To</label>
                                <input type="date" class="form-control" name="tanggal_sampai"
                                    value="{{ request('tanggal_sampai', '') }}">
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
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        .mutasiPenerimaanPage .card { border: none; border-radius: 10px; }
        .mutasiPenerimaanPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
        .mutasiPenerimaanPage #mutasiPenerimaanTable tbody tr:hover { background-color: #f0fdf4; }
        .mutasiPenerimaanPage .btn-sm { transition: transform 0.2s; }
        .mutasiPenerimaanPage .btn-sm:hover { transform: scale(1.1); }
        .mutasiPenerimaanPage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #059669 !important; color: white !important; border: 1px solid #059669 !important; }
        .mutasiPenerimaanPage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #059669 !important; color: white !important; border: 1px solid #059669 !important; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            var hasData = $('#mutasiPenerimaanTable tbody tr').length > 0
                && !$('#mutasiPenerimaanTable tbody tr td[colspan]').length;

            if (hasData) {
                $('#mutasiPenerimaanTable').DataTable({
                    responsive: true, pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                    order: [[2, 'desc']],
                    columnDefs: [{ orderable: false, targets: [0, -1] }],
                    drawCallback: function (settings) {
                        var api = this.api();
                        var start = api.page.info().start;
                        api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                            cell.innerHTML = start + i + 1;
                        });
                    }
                });
            }

            $('#filterButton').on('click', function () { $('#filterModal').modal('show'); });
            $('#resetFilter').on('click', function () {
                $('#filterForm input[type="text"], #filterForm input[type="date"]').val('');
            });

            $(document).on('click', '.btn-delete', function (e) {
                e.stopPropagation();
                const name = $(this).data('name');
                const url  = $(this).data('url');
                const csrf = document.querySelector('meta[name="csrf-token"]').content;
                Swal.fire({
                    title: 'Delete Receive Mutation?',
                    html: `<div class="text-start"><p>Mutation <strong>${name}</strong> will be permanently deleted.</p>
                        <div class="alert alert-warning mt-2 mb-0"><i class="fas fa-exclamation-triangle me-2"></i>
                        All vouchers and details will also be deleted.</div></div>`,
                    icon: 'warning', showCancelButton: true,
                    confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete!',
                    focusCancel: true,
                }).then(result => {
                    if (!result.isConfirmed) return;
                    Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } })
                        .then(r => r.json()).then(data => {
                            if (data.success) {
                                Swal.fire({ title: 'Deleted!', text: data.message, icon: 'success', timer: 1800, showConfirmButton: false })
                                    .then(() => location.reload());
                            } else {
                                Swal.fire({ title: 'Error', html: `<p>${data.message}</p>`, icon: 'error', confirmButtonColor: '#059669' });
                            }
                        }).catch(() => Swal.fire('Error', 'Something went wrong.', 'error'));
                });
            });

            setTimeout(() => { $('.alert-success, .alert-danger').fadeOut('slow'); }, 5000);
        });
    </script>
@endpush