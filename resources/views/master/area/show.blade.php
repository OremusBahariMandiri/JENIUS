@extends('layouts.app')

@section('title', 'Area Detail')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">Area Detail</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom mb-0">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('area.index') }}">Area</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('area.edit', $area->id_md_area) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('area.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Area Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Area Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted small">Area ID</label>
                            <p class="mb-0"><span class="badge bg-secondary fs-6">{{ $area->id_md_area }}</span></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Area Code</label>
                            <p class="mb-0"><span class="badge bg-primary fs-6">{{ $area->code }}</span></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Status</label>
                            <p class="mb-0"><span class="badge bg-success fs-6">Active</span></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Area Name</label>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt text-success me-2"></i>
                            <strong class="fs-5">{{ $area->area }}</strong>
                        </p>
                    </div>

                    @if($area->note)
                    <div class="mb-3">
                        <label class="text-muted small">Note</label>
                        <div class="alert alert-light mb-0">
                            <i class="fas fa-sticky-note text-warning me-2"></i>
                            {{ $area->note }}
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <i class="fas fa-calendar-plus me-2"></i>Created: {{ $area->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-calendar-check me-2"></i>Updated: {{ $area->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Contracts Section -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-link me-2"></i>Related Job Orders</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No job orders available for this area</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-map me-2"></i>Location Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Code</label>
                        <h4 class="mb-0 text-primary">{{ $area->code }}</h4>
                    </div>
                    <div>
                        <label class="text-muted small">Area Name</label>
                        <h5 class="mb-0">{{ $area->area }}</h5>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('area.edit', $area->id_md_area) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Area
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteArea({{ $area->id_md_area }})">
                            <i class="fas fa-trash me-2"></i>Delete Area
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
function deleteArea(id) {
    if (confirm('Are you sure you want to delete this area?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/master/area/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection