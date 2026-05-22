@extends('layouts.app')

@section('title', 'Data Invoice')

@section('content')
<div class="container-fluid invoicePage">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-file-invoice me-2"></i>Data Invoice</span>
                    @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('invoice', 'tambah')))
                    <a href="{{ route('invoice.create') }}" class="btn btn-light">
                        <i class="fas fa-plus-circle me-1"></i> Add Invoice
                    </a>
                    @endif
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

                    <div class="table-responsive">
                        <table id="invoiceTable" class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Category</th>
                                    <th>Item</th>
                                    <th>Note</th>
                                    <th class="text-center" width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $index => $invoice)
                                <tr>
                                    <td>{{ $invoices->firstItem() + $index }}</td>
                                    <td><strong>{{ $invoice->invoice_ctg }}</strong></td>
                                    <td><span class="badge bg-info text-dark">{{ $invoice->invoice_typ }}</span></td>
                                    <td>{{ $invoice->note ? Str::limit($invoice->note, 50) : '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('invoice', 'detail')))
                                            <a href="{{ route('invoice.show', $invoice->id_md_invoice) }}"
                                               class="btn btn-sm btn-info"
                                               data-bs-toggle="tooltip"
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endif

                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('invoice', 'ubah')))
                                            <a href="{{ route('invoice.edit', $invoice->id_md_invoice) }}"
                                               class="btn btn-sm btn-warning"
                                               data-bs-toggle="tooltip"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('invoice', 'hapus')))
                                            <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete"
                                                    data-id="{{ $invoice->id_md_invoice }}"
                                                    data-name="{{ $invoice->invoice_ctg }}"
                                                    data-url="{{ route('invoice.destroy', $invoice->id_md_invoice) }}"
                                                    data-bs-toggle="tooltip"
                                                    title="Delete">
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

<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<style>
    .invoicePage .card {
        border: none;
        border-radius: 10px;
    }

    .invoicePage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .invoicePage .dataTables_wrapper {
        width: 100%;
        transition: all 0.3s ease;
    }

    .invoicePage .dataTables_wrapper .dataTables_length,
    .invoicePage .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem !important;
    }

    .invoicePage .dataTables_wrapper .dataTables_filter {
        text-align: right !important;
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

    .invoicePage #invoiceTable {
        width: 100% !important;
        transition: all 0.3s ease;
    }

    .invoicePage #invoiceTable tbody tr {
        transition: all 0.2s ease;
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

    .invoicePage .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.375rem 0.75rem !important;
        margin: 0 2px !important;
        border-radius: 4px !important;
    }

    .invoicePage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-green) !important;
        color: white !important;
        border: 1px solid var(--primary-green) !important;
    }

    .invoicePage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--primary-green) !important;
        color: white !important;
        border: 1px solid var(--primary-green) !important;
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#invoiceTable')) {
        $('#invoiceTable').DataTable().destroy();
    }

    var table = $('#invoiceTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: -1 }
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
            }
        },
        autoWidth: true,
        drawCallback: function() {
            this.api().columns.adjust();
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

    setTimeout(function() {
        if ($.fn.DataTable.isDataTable('#invoiceTable')) {
            $('#invoiceTable').DataTable().columns.adjust().responsive.recalc();
        }
    }, 100);

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    $(document).on('click', '.btn-delete', function(e) {
        e.stopPropagation();

        const name = $(this).data('name');
        const url = $(this).data('url');

        Swal.fire({
            title: 'Delete Invoice?',
            html: `Invoice <strong>${name}</strong> will be permanently deleted.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete!',
            cancelButtonText: 'Cancel',
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteForm').attr('action', url).submit();
            }
        });
    });

    $('#invoiceTable tbody').on('click', 'tr', function(e) {
        if ($(e.target).is('button') || $(e.target).is('a') || $(e.target).is('i') ||
            $(e.target).closest('button').length || $(e.target).closest('a').length) {
            return;
        }

        var detailLink = $(this).find('a[title="Detail"]').attr('href');
        if (detailLink) {
            window.location.href = detailLink;
        }
    });

    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 5000);
});
</script>
@endpush