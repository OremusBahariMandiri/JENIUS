@extends('layouts.app')

@section('title', 'Detail Customer')

@section('content')
<div class="container-fluid customerPage">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Customer Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Informasi Customer</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('customer.edit', $customer->id_md_cust) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('customer.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Customer Name -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Nama Customer</label>
                        <h3 class="mb-0 text-primary">
                            <i class="fas fa-user me-2"></i>{{ $customer->customer }}
                        </h3>
                    </div>

                    <hr>

                    <!-- Details Grid -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Customer ID</label>
                            <div>
                                <span class="badge bg-secondary fs-6 px-3 py-2">
                                    #{{ $customer->id_md_cust }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Status</label>
                            <div>
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Alamat</label>
                        @if($customer->address)
                        <div class="alert alert-light mb-0">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                            {{ $customer->address }}
                        </div>
                        @else
                        <p class="text-muted mb-0"><em>Tidak ada alamat</em></p>
                        @endif
                    </div>

                    <!-- Contact Information -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Telepon</label>
                            @if($customer->phone)
                            <p class="mb-0">
                                <i class="fas fa-phone text-success me-2"></i>{{ $customer->phone }}
                            </p>
                            @else
                            <p class="text-muted mb-0"><em>-</em></p>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Email</label>
                            @if($customer->email)
                            <p class="mb-0">
                                <i class="fas fa-envelope text-primary me-2"></i>{{ $customer->email }}
                            </p>
                            @else
                            <p class="text-muted mb-0"><em>-</em></p>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Website</label>
                            @if($customer->website)
                            <p class="mb-0">
                                <a href="{{ $customer->website }}" target="_blank">
                                    <i class="fas fa-globe text-info me-2"></i>{{ $customer->website }}
                                </a>
                            </p>
                            @else
                            <p class="text-muted mb-0"><em>-</em></p>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">NPWP</label>
                            <p class="mb-0">{{ $customer->npwp ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Note Section -->
                    @if($customer->note)
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Catatan</label>
                        <div class="alert alert-light border-start border-4 border-warning mb-0">
                            <i class="fas fa-sticky-note text-warning me-2"></i>
                            {{ $customer->note }}
                        </div>
                    </div>
                    @endif

                    <hr>

                    <!-- Timestamps -->
                    <div class="row text-muted">
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-plus me-2 text-primary"></i>
                            <strong>Dibuat:</strong><br>
                            <span class="ms-4">{{ $customer->created_at->format('d F Y, H:i') }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-check me-2 text-success"></i>
                            <strong>Update Terakhir:</strong><br>
                            <span class="ms-4">{{ $customer->updated_at->format('d F Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contracts Section -->
            @if(isset($customer->contracts))
            <div class="card shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-file-contract me-2 text-primary"></i>Kontrak Terkait</h5>
                    <span class="badge bg-light text-dark">{{ $customer->contracts->count() }} items</span>
                </div>
                <div class="card-body">
                    @if($customer->contracts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Kontrak</th>
                                    <th>Nama Kontrak</th>
                                    <th>Nilai</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->contracts as $contract)
                                <tr>
                                    <td><strong>{{ $contract->no_contract }}</strong></td>
                                    <td>{{ $contract->contract }}</td>
                                    <td>Rp {{ number_format($contract->expenditure, 0, ',', '.') }}</td>
                                    <td>
                                        <small>
                                            {{ $contract->date_start->format('d/m/Y') }} -
                                            {{ $contract->date_end->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($contract->date_end >= now())
                                        <span class="badge bg-success">Aktif</span>
                                        @else
                                        <span class="badge bg-secondary">Expired</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum Ada Kontrak</h5>
                        <p class="text-muted mb-0">Belum ada kontrak yang terkait dengan customer ini.</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2 text-success"></i>Informasi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small text-uppercase mb-2">Customer ID</label>
                        <h4 class="mb-0 text-primary">#{{ $customer->id_md_cust }}</h4>
                    </div>
                    <div>
                        <label class="text-muted small text-uppercase mb-2">Nama Customer</label>
                        <h5 class="mb-0">{{ $customer->customer }}</h5>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            @if(isset($summary))
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-info"></i>Statistik</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Total Kontrak</small>
                            <h4 class="mb-0 text-primary">{{ $summary['total_contracts'] }}</h4>
                        </div>
                        <i class="fas fa-file-contract fa-2x text-muted"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Kontrak Aktif</small>
                            <h4 class="mb-0 text-success">{{ $summary['active_contracts'] }}</h4>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-muted"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Pengeluaran</small>
                            <h5 class="mb-0">Rp {{ number_format($summary['total_expenditure'], 0, ',', '.') }}</h5>
                        </div>
                        <i class="fas fa-money-bill-wave fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions Card -->
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-cog me-2 text-warning"></i>Aksi</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.edit', $customer->id_md_cust) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Customer
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteCustomer()">
                            <i class="fas fa-trash me-2"></i>Hapus Customer
                        </button>
                        <a href="{{ route('customer.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list me-2"></i>Kembali ke List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('customer.destroy', $customer->id_md_cust) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .customerPage .card {
        border: none;
        border-radius: 10px;
    }

    .customerPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .customerPage .badge {
        border-radius: 6px;
        font-weight: 500;
    }

    .customerPage .alert {
        border-radius: 8px;
    }

    .customerPage h3 {
        color: var(--primary-green);
    }

    .customerPage .text-primary {
        color: var(--primary-green) !important;
    }

    .customerPage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .customerPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .customerPage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .customerPage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }

    .customerPage .border-bottom {
        border-color: #e2e8f0 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteCustomer() {
    Swal.fire({
        title: 'Hapus Customer?',
        html: `Customer <strong>{{ $customer->customer }}</strong> akan dihapus secara permanen.<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
        cancelButtonText: 'Batal',
        focusCancel: true,
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
}
</script>
@endpush