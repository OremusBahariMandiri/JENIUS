@extends('layouts.app')

@section('title', 'Detail Parent Chart of Account')

@section('content')
<div class="container-fluid parentCoaPage">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-eye me-2"></i>Detail Parent COA</span>
                    <a href="{{ route('parent-coa.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Tipe Akun</th>
                            <td>: {{ $parentCoa->costType?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kode Perkiraan</th>
                            <td>: <span class="badge bg-secondary fs-6">{{ $parentCoa->kode_perkiraan }}</span></td>
                        </tr>
                        <tr>
                            <th>Nama Akun</th>
                            <td>: {{ $parentCoa->nama }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>: {{ $parentCoa->created_at?->format('d M Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Diupdate</th>
                            <td>: {{ $parentCoa->updated_at?->format('d M Y, H:i') }}</td>
                        </tr>
                    </table>

                    <hr class="my-4">

                    <div class="d-flex gap-2 justify-content-end">
                        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('parent_coa', 'ubah')))
                        <a href="{{ route('parent-coa.edit', $parentCoa->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @endif
                        <a href="{{ route('parent-coa.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .parentCoaPage .card { border: none; border-radius: 10px; }
    .parentCoaPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .parentCoaPage table th { color: #6c757d; }
    .parentCoaPage .btn { padding: 0.5rem 1.5rem; border-radius: 8px; font-weight: 500; }
</style>
@endpush