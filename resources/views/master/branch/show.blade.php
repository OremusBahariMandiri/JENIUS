@extends('layouts.app')

@section('title', 'Branch Detail')

@section('content')
<div class="container-fluid branchPage">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-code-branch me-2"></i>Branch Detail</span>
                    <div class="d-flex gap-2">
                        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('branch', 'ubah')))
                        <a href="{{ route('branch.edit', $branch->id_md_branch) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        @endif
                        <a href="{{ route('branch.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Abbr.</label>
                                <div class="detail-value">{{ $branch->skt_branch ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Branch Name</label>
                                <div class="detail-value fw-semibold">{{ $branch->nama_branch }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Area</label>
                                <div class="detail-value">{{ $branch->area ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Phone</label>
                                <div class="detail-value">{{ $branch->no_telepon ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="detail-item">
                                <label class="detail-label">Email</label>
                                <div class="detail-value">
                                    @if($branch->email)<a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a>@else -@endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="detail-item">
                                <label class="detail-label">Address</label>
                                <div class="detail-value">{{ $branch->alamat ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="detail-item">
                                <label class="detail-label">Note</label>
                                <div class="detail-value">{{ $branch->note ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Created At</label>
                                <div class="detail-value">{{ $branch->created_at ? $branch->created_at->format('d M Y H:i') : '-' }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="detail-item">
                                <label class="detail-label">Updated At</label>
                                <div class="detail-value">{{ $branch->updated_at ? $branch->updated_at->format('d M Y H:i') : '-' }}</div>
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
    .branchPage .card { border: none; border-radius: 10px; }
    .branchPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .detail-item { padding: 0.75rem; background: #f8f9fa; border-radius: 8px; border-left: 3px solid var(--primary-green); }
    .detail-label { display: block; font-size: 0.75rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; }
    .detail-value { font-size: 0.95rem; color: #212529; }
</style>
@endpush