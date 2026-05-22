@extends('layouts.app')

@section('title', 'Edit User Access - ' . $user->full_name)

@section('content')
<div class="container-fluid userPage">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- User Info Sidebar -->
        <div class="col-lg-3">
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>User Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle bg-primary text-white mb-2"
                             style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 32px;">
                            {{ strtoupper(substr($user->full_name, 0, 1)) }}
                        </div>
                        <h5 class="mb-1">{{ $user->full_name }}</h5>
                        <p class="text-muted mb-0 small">{{ $user->employee_code }}</p>
                        @if($user->is_admin)
                        <span class="badge bg-danger mt-2">Administrator</span>
                        @else
                        <span class="badge bg-secondary mt-2">User</span>
                        @endif
                    </div>
                    <hr>
                    <div class="small">
                        <div class="mb-2">
                            <strong>Employee ID:</strong><br>
                            <span class="text-muted">{{ $user->employee_id_number }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Department:</strong><br>
                            <span class="text-muted">{{ $user->department }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Position:</strong><br>
                            <span class="text-muted">{{ $user->position }}</span>
                        </div>
                        <div class="mb-0">
                            <strong>Location:</strong><br>
                            <span class="text-muted">{{ $user->work_location }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success btn-sm" onclick="grantFullAccess()">
                            <i class="fas fa-check-double me-2"></i>Grant Full Access
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#copyAccessModal">
                            <i class="fas fa-copy me-2"></i>Copy from User
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="revokeAllAccess()">
                            <i class="fas fa-ban me-2"></i>Revoke All Access
                        </button>
                        <hr class="my-2">
                        <a href="{{ route('user.show', $user->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-user me-2"></i>View Profile
                        </a>
                        <a href="{{ route('user.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Access Control Form -->
        <div class="col-lg-9">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-key me-2"></i>Access Control Settings</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('user-access.update', $user->id) }}" method="POST" id="accessForm">
                        @csrf
                        @method('PUT')

                        <!-- Admin Toggle -->
                        <div class="alert alert-light border-start border-4 border-warning mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="fas fa-crown text-warning me-2"></i>
                                        Administrator Status
                                    </h6>
                                    <p class="mb-0 small text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Administrators have full access to all features and bypass all permission checks
                                    </p>
                                </div>
                                <div class="form-check form-switch" style="font-size: 1.5rem;">
                                    <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin"
                                           value="1" {{ $user->is_admin ? 'checked' : '' }}
                                           onchange="toggleAdminMode(this)">
                                </div>
                            </div>
                        </div>

                        <!-- Permission Table -->
                        <div id="permissionTable" style="{{ $user->is_admin ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                            <div class="alert alert-info" id="adminNotice" style="{{ $user->is_admin ? '' : 'display: none;' }}">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> This user is an administrator. Individual menu permissions are disabled.
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th style="width: 25%">Menu</th>
                                            <th class="text-center" style="width: 11.67%">
                                                <i class="fas fa-chart-line text-secondary"></i><br>
                                                <small>Monitor</small>
                                            </th>
                                            <th class="text-center" style="width: 11.67%">
                                                <i class="fas fa-eye text-info"></i><br>
                                                <small>View Detail</small>
                                            </th>
                                            <th class="text-center" style="width: 11.67%">
                                                <i class="fas fa-plus text-success"></i><br>
                                                <small>Create</small>
                                            </th>
                                            <th class="text-center" style="width: 11.67%">
                                                <i class="fas fa-edit text-warning"></i><br>
                                                <small>Update</small>
                                            </th>
                                            <th class="text-center" style="width: 11.67%">
                                                <i class="fas fa-trash text-danger"></i><br>
                                                <small>Delete</small>
                                            </th>
                                            <th class="text-center" style="width: 11.67%">
                                                <i class="fas fa-download text-primary"></i><br>
                                                <small>Download</small>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($menus as $index => $menu)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox"
                                                       name="access[{{ $index }}][enabled]"
                                                       class="form-check-input menu-checkbox"
                                                       value="1"
                                                       data-menu="{{ $menu['key'] }}"
                                                       {{ isset($userAccess[$menu['key']]) ? 'checked' : '' }}>
                                                <input type="hidden"
                                                       name="access[{{ $index }}][menu_access]"
                                                       value="{{ $menu['key'] }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="{{ $menu['icon'] }} me-2 text-primary" style="font-size: 1.3rem;"></i>
                                                    <div>
                                                        <strong>{{ $menu['name'] }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            @foreach(['monitor', 'view_detail', 'create', 'update', 'delete', 'download'] as $permKey)
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input type="checkbox"
                                                           name="access[{{ $index }}][permissions][]"
                                                           value="{{ $permKey }}"
                                                           class="form-check-input permission-checkbox"
                                                           data-menu="{{ $menu['key'] }}"
                                                           id="perm_{{ $menu['key'] }}_{{ $permKey }}"
                                                           {{ isset($userAccess[$menu['key']]) && $userAccess[$menu['key']]->{'can_' . $permKey} ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('user.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy Access Modal -->
<div class="modal fade" id="copyAccessModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-copy me-2"></i>Copy Access from Another User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('user-access.copy', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Source User</label>
                        <select name="source_user_id" class="form-select" required>
                            <option value="">-- Select User --</option>
                            @foreach(\App\Models\User::where('id', '!=', $user->id)->orderBy('full_name')->get() as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->full_name }} ({{ $u->employee_code }}) - {{ $u->position }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            This will copy all access settings from the selected user
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-copy me-2"></i>Copy Access
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Grant Full Access Form -->
<form id="grantFullAccessForm" action="{{ route('user-access.grant-full', $user->id) }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Revoke All Access Form -->
<form id="revokeAllAccessForm" action="{{ route('user-access.revoke-all', $user->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<style>
    .userPage .card {
        border: none;
        border-radius: 10px;
    }

    .userPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .userPage .avatar-circle {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .userPage .badge {
        border-radius: 6px;
        font-weight: 500;
        padding: 0.35rem 0.65rem;
    }

    .userPage .btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .userPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .userPage .btn-success {
        background-color: #10b981;
        border-color: #10b981;
    }

    .userPage .btn-success:hover {
        background-color: #059669;
        border-color: #059669;
    }

    .userPage .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }

    .userPage .form-switch .form-check-input {
        width: 2.5em;
        height: 1.25em;
        cursor: pointer;
    }

    .userPage .form-check-input:focus {
        border-color: #86efac;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }

    .userPage .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }

    .userPage #selectAll {
        width: 1.25em;
        height: 1.25em;
        cursor: pointer;
    }

    .userPage .menu-checkbox {
        width: 1.25em;
        height: 1.25em;
        cursor: pointer;
    }

    .userPage .alert {
        border-radius: 8px;
    }

    .userPage .border-bottom {
        border-color: #e2e8f0 !important;
    }

    .userPage .table-bordered {
        border-color: #e2e8f0;
    }

    .userPage .table-bordered th,
    .userPage .table-bordered td {
        border-color: #e2e8f0;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Auto-hide alerts
    setTimeout(function() {
        $(".alert-dismissible").fadeOut("slow");
    }, 5000);

    // Initialize
    updateSelectAll();
});

// Toggle admin mode
function toggleAdminMode(checkbox) {
    const permissionTable = document.getElementById('permissionTable');
    const adminNotice = document.getElementById('adminNotice');

    if (checkbox.checked) {
        permissionTable.style.opacity = '0.5';
        permissionTable.style.pointerEvents = 'none';
        adminNotice.style.display = 'block';
    } else {
        permissionTable.style.opacity = '1';
        permissionTable.style.pointerEvents = 'auto';
        adminNotice.style.display = 'none';
    }
}

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const menuCheckboxes = document.querySelectorAll('.menu-checkbox');
    menuCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
        togglePermissions(checkbox);
    });
});

// Handle individual menu checkboxes
document.querySelectorAll('.menu-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        togglePermissions(this);
        updateSelectAll();
    });

    // Initialize state
    togglePermissions(checkbox);
});

// Toggle permissions when menu is checked/unchecked
function togglePermissions(menuCheckbox) {
    const menu = menuCheckbox.dataset.menu;
    const permissionCheckboxes = document.querySelectorAll(`.permission-checkbox[data-menu="${menu}"]`);

    permissionCheckboxes.forEach(permCheckbox => {
        permCheckbox.disabled = !menuCheckbox.checked;
        if (!menuCheckbox.checked) {
            permCheckbox.checked = false;
        }
    });
}

// Update select all checkbox state
function updateSelectAll() {
    const menuCheckboxes = document.querySelectorAll('.menu-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');

    const allChecked = Array.from(menuCheckboxes).every(cb => cb.checked);
    const someChecked = Array.from(menuCheckboxes).some(cb => cb.checked);

    selectAllCheckbox.checked = allChecked;
    selectAllCheckbox.indeterminate = someChecked && !allChecked;
}

// Grant full access with SweetAlert2
function grantFullAccess() {
    Swal.fire({
        title: 'Grant Full Access?',
        html: 'All menus with all permissions will be granted to <strong>{{ $user->full_name }}</strong>.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check-double me-1"></i> Yes, Grant!',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('grantFullAccessForm').submit();
        }
    });
}

// Revoke all access with SweetAlert2
function revokeAllAccess() {
    Swal.fire({
        title: 'Revoke All Access?',
        html: '<strong>{{ $user->full_name }}</strong> will not be able to access any menu.<br><small class="text-muted">This action can be reversed later.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-ban me-1"></i> Yes, Revoke!',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('revokeAllAccessForm').submit();
        }
    });
}

// Form validation
document.getElementById('accessForm').addEventListener('submit', function(e) {
    const isAdmin = document.getElementById('is_admin').checked;
    const checkedMenus = document.querySelectorAll('.menu-checkbox:checked');

    if (!isAdmin && checkedMenus.length === 0) {
        e.preventDefault();
        Swal.fire({
            title: 'No Access Selected',
            html: 'No menu access selected and user is not an administrator.<br><strong>{{ $user->full_name }}</strong> will not be able to access any features.<br><br>Continue?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Continue',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('accessForm').submit();
            }
        });
    }
});
</script>
@endpush
@endsection