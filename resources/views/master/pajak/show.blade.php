@extends('layouts.app')

@section('title', 'Detail Data Pajak')

@section('content')
<div class="container-fluid pajakPage">
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-percent me-2"></i>Detail Data Pajak</span>
                    <a href="{{ route('pajak.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width:35%" class="text-muted">ID</th>
                            <td>{{ $pajak->id_md_pajak }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Nama Pajak</th>
                            <td class="fw-semibold fs-5">{{ $pajak->nama_pajak }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Kode Pajak</th>
                            <td>
                                @if($pajak->kode_pajak)
                                    <span class="badge bg-secondary">{{ $pajak->kode_pajak }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Persentase</th>
                            <td>
                                <span class="badge bg-success" style="font-size:1rem; padding:.4em .8em">
                                    {{ number_format($pajak->persentase, 2) }}%
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status</th>
                            <td>
                                @if($pajak->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Keterangan</th>
                            <td>{{ $pajak->keterangan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Dibuat</th>
                            <td>{{ $pajak->created_at ? $pajak->created_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Diupdate</th>
                            <td>{{ $pajak->updated_at ? $pajak->updated_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    </table>

                    <hr>
                    <div class="d-flex gap-2 justify-content-end">
                        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('pajak', 'ubah')))
                        <a href="{{ route('pajak.edit', $pajak->id_md_pajak) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @endif
                        <a href="{{ route('pajak.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
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
    .pajakPage .card { border: none; border-radius: 10px; }
    .pajakPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .pajakPage .btn { padding: .5rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all .2s; }
    .pajakPage .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,.1); }
</style>
@endpush
