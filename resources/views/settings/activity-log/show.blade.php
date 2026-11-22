@extends('layouts.app')

@section('title', 'Detail Log Aktivitas')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('activity-log.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
        <div>
            <h4 class="mb-0 fw-bold"><i class="fas fa-history me-2 text-success"></i>Detail Log Aktivitas</h4>
        </div>
    </div>

    <div class="row g-4">
        {{-- Info Utama --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-info-circle me-2 text-success"></i>Informasi Log</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" style="width:40%">ID Log</td>
                            <td><strong>#{{ $activityLog->id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu</td>
                            <td>{{ $activityLog->created_at->format('d M Y, H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pengguna</td>
                            <td>{{ $activityLog->user_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Employee ID</td>
                            <td>{{ $activityLog->employee_id_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Aksi</td>
                            <td>
                                <span class="badge bg-{{ $activityLog->action_badge }}">
                                    {{ $activityLog->action_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Modul</td>
                            <td><span class="badge bg-light text-dark border">{{ $activityLog->module ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Model</td>
                            <td>{{ $activityLog->model_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Record ID</td>
                            <td>{{ $activityLog->record_id ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Deskripsi</td>
                            <td>{{ $activityLog->description ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Info Teknis --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-network-wired me-2 text-success"></i>Informasi Teknis</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" style="width:40%">IP Address</td>
                            <td>{{ $activityLog->ip_address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Method</td>
                            <td><span class="badge bg-dark">{{ $activityLog->method ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">URL</td>
                            <td class="text-break small">{{ $activityLog->url ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">User Agent</td>
                            <td class="text-break" style="font-size:11px">{{ $activityLog->user_agent ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Old Values --}}
        @if($activityLog->old_values)
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning bg-opacity-10 py-3">
                    <h6 class="mb-0 fw-semibold text-warning"><i class="fas fa-undo me-2"></i>Nilai Sebelum (Before)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Field</th><th>Nilai Lama</th></tr>
                        </thead>
                        <tbody>
                            @foreach($activityLog->old_values as $key => $val)
                            <tr>
                                <td class="text-muted small" style="width:40%">{{ $key }}</td>
                                <td class="small">
                                    @if(is_array($val))
                                        <code>{{ json_encode($val, JSON_PRETTY_PRINT) }}</code>
                                    @else
                                        {{ $val ?? '<em class="text-muted">null</em>' }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- New Values --}}
        @if($activityLog->new_values)
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success bg-opacity-10 py-3">
                    <h6 class="mb-0 fw-semibold text-success"><i class="fas fa-check me-2"></i>Nilai Sesudah (After)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Field</th><th>Nilai Baru</th></tr>
                        </thead>
                        <tbody>
                            @foreach($activityLog->new_values as $key => $val)
                            <tr>
                                <td class="text-muted small" style="width:40%">{{ $key }}</td>
                                <td class="small">
                                    @if(is_array($val))
                                        <code>{{ json_encode($val, JSON_PRETTY_PRINT) }}</code>
                                    @else
                                        {{ $val ?? '<em class="text-muted">null</em>' }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Hapus log ini --}}
    @if(auth()->user()->is_admin)
    <div class="mt-4 d-flex justify-content-end">
        <form action="{{ route('activity-log.destroy', $activityLog->id) }}" method="POST"
              onsubmit="return confirm('Hapus log ini?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger btn-sm">
                <i class="fas fa-trash me-1"></i>Hapus Log Ini
            </button>
        </form>
    </div>
    @endif

</div>
@endsection
