@extends('layouts.app')

@section('title', 'Contract Data')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">Contract Data</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom mb-0">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item">Master Data</li>
                    <li class="breadcrumb-item active">Contract</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('contract.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Add Contract
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('contract.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search contract..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="customer_id" class="form-select">
                        <option value="">-- All Customers --</option>
                        @foreach($customers as $cust)
                        <option value="{{ $cust->id_md_cust }}" {{ request('customer_id') == $cust->id_md_cust ? 'selected' : '' }}>
                            {{ $cust->customer }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- All Status --</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Contract No.</th>
                            <th>Contract Name</th>
                            <th>Customer</th>
                            <th>Value</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th width="15%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $index => $contract)
                        <tr>
                            <td>{{ $contracts->firstItem() + $index }}</td>
                            <td><span class="badge bg-primary">{{ $contract->no_contract }}</span></td>
                            <td>
                                <strong>{{ $contract->contract }}</strong>
                            </td>
                            <td>{{ $contract->customer->customer ?? '-' }}</td>
                            <td>Rp {{ number_format($contract->expenditure, 0, ',', '.') }}</td>
                            <td>
                                <small>{{ $contract->date_start->format('d/m/Y') }}</small><br>
                                <small>{{ $contract->date_end->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                @if($contract->date_end >= now())
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Expired</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('contract.show', $contract->id_md_cont) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('contract.edit', $contract->id_md_cont) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteContract({{ $contract->id_md_cont }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No contract data available</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $contracts->firstItem() ?? 0 }} to {{ $contracts->lastItem() ?? 0 }} of {{ $contracts->total() }} entries
                </div>
                {{ $contracts->links() }}
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
function deleteContract(id) {
    if (confirm('Are you sure you want to delete this contract?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/master/contract/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection