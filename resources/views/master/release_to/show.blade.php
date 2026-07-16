@extends('layouts.app')

@section('title', 'Release To Detail')

@section('content')
<div class="container-fluid releasePage">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-share-square me-2"></i>Release To Detail</span>
                    <div class="d-flex gap-2">
                        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('release_to', 'ubah')))
                        <a href="{{ route('release_to.edit', $release->id_md_release) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        @endif
                        <a href="{{ route('release_to.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <div class="detail-item">
                                <label class="detail-label">Release Name</label>
                                <div class="detail-value fw-semibold">{{ $release->nama_release }}</div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="detail-item">
                                <label class="detail-label">Destination</label>
                                <div class="detail-value">{{ $release->tujuan ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Account No.</label>
                                <div class="detail-value">{{ $release->rekening ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Phone</label>
                                <div class="detail-value">{{ $release->no_telepon ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="detail-item">
                                <label class="detail-label">Address</label>
                                <div class="detail-value">{{ $release->alamat ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="detail-item">
                                <label class="detail-label">Note</label>
                                <div class="detail-value">{{ $release->note ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Created At</label>
                                <div class="detail-value">{{ $release->created_at ? $release->created_at->format('d M Y H:i') : '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Updated At</label>
                                <div class="detail-value">{{ $release->updated_at ? $release->updated_at->format('d M Y H:i') : '-' }}</div>
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
    .releasePage .card { border: none; border-radius: 10px; }
    .releasePage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .detail-item { padding: 0.75rem; background: #f8f9fa; border-radius: 8px; border-left: 3px solid var(--primary-green); }
    .detail-label { display: block; font-size: 0.75rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; }
    .detail-value { font-size: 0.95rem; color: #212529; }
</style>
@endpush