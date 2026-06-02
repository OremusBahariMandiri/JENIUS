@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <div class="container-fluid userPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-users me-2"></i>User Management</span>
                        @if (auth()->check() && auth()->user()->is_admin)
                            <a href="{{ route('user.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> Add
                            </a>
                        @endif
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="userTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Employee ID</th>
                                        <th>Full Name</th>
                                        <th>Department</th>
                                        <th>Position</th>
                                        <th>Work Location</th>
                                        <th>Role</th>
                                        <th width="15%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $index => $user)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $user->employee_id_number }}</td>
                                            <td>{{ $user->full_name }}</td>
                                            <td>{{ $user->department }}</td>
                                            <td>{{ $user->position }}</td>
                                            <td>{{ $user->work_location }}</td>
                                            <td>
                                                @if ($user->is_admin)
                                                    Admin
                                                @else
                                                User
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('user.show', $user->id) }}"
                                                        class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('user-access.edit', $user->id) }}"
                                                        class="btn btn-sm btn-success" data-bs-toggle="tooltip"
                                                        title="Manage Access">
                                                        <i class="fas fa-key"></i>
                                                    </a>
                                                    <a href="{{ route('user.edit', $user->id) }}"
                                                        class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                        data-id="{{ $user->id }}" data-name="{{ $user->full_name }}"
                                                        data-url="{{ route('user.destroy', $user->id) }}"
                                                        data-bs-toggle="tooltip" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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


    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

        <style>
            .userPage .card {
                border: none;
                border-radius: 10px;
            }

            .userPage .card-header {
                border-radius: 10px 10px 0 0 !important;
                padding: 1rem 1.5rem;
            }

            .userPage .dataTables_wrapper {
                width: 100%;
                transition: all 0.3s ease;
            }

            .userPage .dataTables_wrapper .dataTables_length,
            .userPage .dataTables_wrapper .dataTables_filter {
                margin-bottom: 1rem !important;
            }

            .userPage .dataTables_wrapper .dataTables_filter {
                text-align: right !important;
            }

            .userPage .dataTables_wrapper .dataTables_filter input {
                margin-left: 5px !important;
                border-radius: 4px !important;
                border: 1px solid #ced4da !important;
                padding: 0.375rem 0.75rem !important;
            }

            .userPage .dataTables_wrapper .dataTables_length select {
                border-radius: 4px !important;
                border: 1px solid #ced4da !important;
                padding: 0.375rem 2rem 0.375rem 0.75rem !important;
            }

            .userPage #userTable {
                width: 100% !important;
                transition: all 0.3s ease;
            }

            .userPage #userTable tbody tr {
                transition: all 0.2s ease;
            }

            .userPage #userTable tbody tr:hover {
                background-color: #f8f9fa;
                cursor: pointer;
            }

            .userPage .btn-sm {
                transition: transform 0.2s;
            }

            .userPage .btn-sm:hover {
                transform: scale(1.1);
            }

            .userPage .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.375rem 0.75rem !important;
                margin: 0 2px !important;
                border-radius: 4px !important;
            }

            .userPage .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: #10b981 !important;
                color: white !important;
                border: 1px solid #10b981 !important;
            }

            .userPage .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: #10b981 !important;
                color: white !important;
                border: 1px solid #10b981 !important;
            }

            .userPage .table-responsive {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .userPage .badge {
                border-radius: 6px;
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

        <script>
            $(document).ready(function() {
                // Destroy existing DataTable if exists
                if ($.fn.DataTable.isDataTable('#userTable')) {
                    $('#userTable').DataTable().destroy();
                }

                // Initialize DataTable
                var table = $('#userTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    order: [
                        [3, 'asc']
                    ], // Sort by Full Name
                    columnDefs: [{
                            orderable: false,
                            targets: 0
                        },
                        {
                            orderable: false,
                            targets: -1
                        } // Disable sorting on Action column
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
                    drawCallback: function(settings) {
                        var api = this.api();
                        var pageInfo = api.page.info();
                        var startIndex = pageInfo.start;

                        // Update nomor urut pada kolom pertama
                        api.column(0, {
                            page: 'current'
                        }).nodes().each(function(cell, i) {
                            cell.innerHTML = startIndex + i + 1;
                        });

                        // Adjust columns
                        api.columns.adjust();
                    }
                });

                // Handle window resize
                let resizeTimer;
                $(window).on('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        if ($.fn.DataTable.isDataTable('#userTable')) {
                            $('#userTable').DataTable().columns.adjust().responsive.recalc();
                        }
                    }, 300);
                });

                // Initial column adjustment
                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#userTable')) {
                        $('#userTable').DataTable().columns.adjust().responsive.recalc();
                    }
                }, 100);

                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Handle delete button click
                $(document).on('click', '.btn-delete', function(e) {
                    e.stopPropagation();

                    const name = $(this).data('name');
                    const url = $(this).data('url');

                    Swal.fire({
                        title: 'Delete User?',
                        html: `User <strong>${name}</strong> will be permanently deleted.`,
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

                // Handle row click to view detail
                $('#userTable tbody').on('click', 'tr', function(e) {
                    // Don't trigger if clicking on a button or link
                    if ($(e.target).is('button') || $(e.target).is('a') || $(e.target).is('i') ||
                        $(e.target).closest('button').length || $(e.target).closest('a').length) {
                        return;
                    }

                    var detailLink = $(this).find('a[title="Detail"]').attr('href');
                    if (detailLink) {
                        window.location.href = detailLink;
                    }
                });

                // Auto-hide alerts
                setTimeout(function() {
                    $(".alert").fadeOut("slow");
                }, 5000);
            });
        </script>
    @endpush
@endsection
