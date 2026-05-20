@extends('layouts.app')

@section('title', 'Detail Customer')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">Detail Customer</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Customer</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('customer.edit', $customer->id_md_cust) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('customer.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Customer Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Customer</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Kode Customer</label>
                            <p class="mb-0"><span class="badge bg-primary fs-6">{{ $customer->code }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Nama Customer</label>
                            <p class="mb-0 fw-bold">{{ $customer->customer }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Alamat</label>
                        <p class="mb-0">{{ $customer->address ?? '-' }}</p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Telepon</label>
                            <p class="mb-0">
                                @if($customer->phone)
                                <i class="fas fa-phone text-success me-2"></i>{{ $customer->phone }}
                                @else
                                -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Email</label>
                            <p class="mb-0">
                                @if($customer->email)
                                <i class="fas fa-envelope text-primary me-2"></i>{{ $customer->email }}
                                @else
                                -
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Website</label>
                            <p class="mb-0">
                                @if($customer->website)
                                <a href="{{ $customer->website }}" target="_blank">
                                    <i class="fas fa-globe text-info me-2"></i>{{ $customer->website }}
                                </a>
                                @else
                                -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">NPWP</label>
                            <p class="mb-0">{{ $customer->npwp ?? '-' }}</p>
                        </div>
                    </div>

                    @if($customer->note)
                    <div class="mb-3">
                        <label class="text-muted small">Catatan</label>
                        <div class="alert alert-light mb-0">{{ $customer->note }}</div>
                    </div>
                    @endif

                    <hr>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <i class="fas fa-calendar-plus me-2"></i>Dibuat: {{ $customer->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-calendar-check me-2"></i>Update: {{ $customer->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contracts Section -->
            @if(isset($summary))
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>Kontrak Terkait</h5>
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
                                    <td>{{ $contract->no_contract }}</td>
                                    <td>{{ $contract->contract }}</td>
                                    <td>Rp {{ number_format($contract->expenditure, 0, ',', '.') }}</td>
                                    <td>{{ $contract->date_start->format('d/m/Y') }} - {{ $contract->date_end->format('d/m/Y') }}</td>
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
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada kontrak untuk customer ini</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-lg-4">
            @if(isset($summary))
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Total Kontrak</small>
                            <h3 class="mb-0 text-success">{{ $summary['total_contracts'] }}</h3>
                        </div>
                        <div class="stats-icon green">
                            <i class="fas fa-file-contract"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Kontrak Aktif</small>
                            <h3 class="mb-0 text-primary">{{ $summary['active_contracts'] }}</h3>
                        </div>
                        <div class="stats-icon" style="background: #bfdbfe; color: #3b82f6;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Pengeluaran</small>
                            <h4 class="mb-0 text-dark">Rp {{ number_format($summary['total_expenditure'], 0, ',', '.') }}</h4>
                        </div>
                        <div class="stats-icon" style="background: #fee2e2; color: #ef4444;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Aksi</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.edit', $customer->id_md_cust) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Customer
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteCustomer({{ $customer->id_md_cust }})">
                            <i class="fas fa-trash me-2"></i>Hapus Customer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteCustomer(id) {
    if (confirm('Apakah Anda yakin ingin menghapus customer ini?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/master/customer/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection
