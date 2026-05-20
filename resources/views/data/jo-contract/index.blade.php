@extends('layouts.app')

@section('title', 'JO Contract Data')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-2 text-gray-800">JO Contract Data</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-custom mb-0">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item">Data Collection</li>
                        <li class="breadcrumb-item active">JO Contract</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('jo-contract.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Add JO Contract
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('jo-contract.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search JO contract..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="id_md_cont" class="form-select">
                            <option value="">All Contracts</option>
                            @foreach ($contracts as $contract)
                                <option value="{{ $contract->id_md_cont }}"
                                    {{ request('id_md_cont') == $contract->id_md_cont ? 'selected' : '' }}>
                                    {{ $contract->no_contract }} -
                                    {{ $contract->customer ? $contract->customer->customer : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="id_md_area" class="form-select">
                            <option value="">All Areas</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id_md_area }}"
                                    {{ request('id_md_area') == $area->id_md_area ? 'selected' : '' }}>
                                    {{ $area->area }}
                                </option>
                            @endforeach
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
                                {{-- <th>JO ID</th> --}}
                                <th>Title</th>
                                <th>Contract</th>
                                <th>Customer</th>
                                <th>Area</th>
                                <th width="15%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($joContracts as $index => $joContract)
                                <tr>
                                    <td>{{ $joContracts->firstItem() + $index }}</td>
                                    {{-- <td><span class="badge bg-primary">JO-{{ $joContract->id_jo_cont }}</span></td> --}}
                                    <td>
                                        <strong>{{ $joContract->title }}</strong>
                                    </td>
                                    <td>
                                        @if ($joContract->contract)
                                            {{ $joContract->contract->no_contract }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($joContract->contract && $joContract->contract->customer)
                                            {{ $joContract->contract->customer->customer }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($joContract->area)
                                            {{ $joContract->area->area }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('jo-contract.show', $joContract->id_jo_cont) }}"
                                                class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('jo-contract.edit', $joContract->id_jo_cont) }}"
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="deleteJoContract({{ $joContract->id_jo_cont }})" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No JO contract data available</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $joContracts->firstItem() ?? 0 }} to {{ $joContracts->lastItem() ?? 0 }} of
                        {{ $joContracts->total() }} entries
                    </div>
                    {{ $joContracts->links() }}
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
            function deleteJoContract(id) {
                if (confirm('Are you sure you want to delete this JO contract?')) {
                    const form = document.getElementById('deleteForm');
                    form.action = `/data/jo-contract/${id}`;
                    form.submit();
                }
            }
        </script>
    @endpush
@endsection
