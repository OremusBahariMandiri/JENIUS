@extends('layouts.app')

@section('title', 'Department Detail')

@section('content')
<div class="container-fluid departemenPage">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-sitemap me-2"></i>Department Detail</span>
                    <div class="d-flex gap-2">
                        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('departemen', 'ubah')))
                        <a href="{{ route('departemen.edit', $departemen->id_md_dep) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        @endif
                        <a href="{{ route('departemen.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Abbr.</label>
                                <div class="detail-value">{{ $departemen->skt_dep ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Department Name</label>
                                <div class="detail-value fw-semibold">{{ $departemen->nama_dep }}</div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="detail-item">
                                <label class="detail-label">Description</label>
                                <div class="detail-value">{{ $departemen->keterangan ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Created At</label>
                                <div class="detail-value">{{ $departemen->created_at ? $departemen->created_at->format('d M Y H:i') : '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Updated At</label>
                                <div class="detail-value">{{ $departemen->updated_at ? $departemen->updated_at->format('d M Y H:i') : '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .departemenPage .card { border: none; border-radius: 10px; }
    .departemenPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .detail-item { padding: 0.75rem; background: #f8f9fa; border-radius: 8px; border-left: 3px solid var(--primary-green); }
    .detail-label { display: block; font-size: 0.75rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; }
    .detail-value { font-size: 0.95rem; color: #212529; }
</style>
@endpush