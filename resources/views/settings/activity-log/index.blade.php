@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold"><i class="fas fa-history me-2 text-success"></i>Log Aktivitas</h4>
            <small class="text-muted">Riwayat seluruh aktivitas pengguna dalam sistem</small>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success fs-6 px-3 py-2">
                {{ number_format($logs->total()) }} total log
            </span>
            @if(auth()->user()->is_admin)
            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#clearAllModal">
                <i class="fas fa-trash me-1"></i>Hapus Semua
            </button>
            @endif
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filter Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-semibold"><i class="fas fa-filter me-2 text-success"></i>Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('activity-log.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Pencarian</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Nama, Employee ID, modul..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Aksi</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">Semua Aksi</option>
                            <option value="login"   {{ request('action') == 'login'   ? 'selected' : '' }}>Login</option>
                            <option value="logout"  {{ request('action') == 'logout'  ? 'selected' : '' }}>Logout</option>
                            <option value="create"  {{ request('action') == 'create'  ? 'selected' : '' }}>Tambah Data</option>
                            <option value="update"  {{ request('action') == 'update'  ? 'selected' : '' }}>Ubah Data</option>
                            <option value="delete"  {{ request('action') == 'delete'  ? 'selected' : '' }}>Hapus Data</option>
                            <option value="restore" {{ request('action') == 'restore' ? 'selected' : '' }}>Restore Data</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Modul</label>
                        <select name="module" class="form-select form-select-sm">
                            <option value="">Semua Modul</option>
                            @foreach($modules as $mod)
                            <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Pengguna</label>
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="">Semua Pengguna</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->full_name }} ({{ $u->employee_id_number }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small fw-semibold">Dari</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small fw-semibold">Sampai</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end gap-1">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('activity-log.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:50px">#</th>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th>Modul</th>
                            <th>Deskripsi</th>
                            <th>IP Address</th>
                            <th class="text-center" style="width:80px">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-3 text-muted small">{{ $logs->firstItem() + $loop->index }}</td>
                            <td class="small">
                                <div class="fw-semibold">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold small">{{ $log->user_name ?? '-' }}</div>
                                <div class="text-muted" style="font-size:11px">{{ $log->employee_id_number ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->action_badge }} text-capitalize">
                                    {{ $log->action_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border small">{{ $log->module ?? '-' }}</span>
                            </td>
                            <td class="small" style="max-width:280px">
                                <span class="text-truncate d-inline-block" style="max-width:270px" title="{{ $log->description }}">
                                    {{ $log->description ?? '-' }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $log->ip_address ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('activity-log.show', $log->id) }}"
                                   class="btn btn-sm btn-outline-success" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Tidak ada log aktivitas ditemukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <small class="text-muted">
                    Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ number_format($logs->total()) }} log
                </small>
                {{ $logs->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Modal Hapus Semua --}}
<div class="modal fade" id="clearAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Hapus Semua Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tindakan ini akan menghapus <strong>semua log aktivitas</strong> secara permanen dan tidak dapat dibatalkan.</p>
                <p>Ketik <code>DELETE</code> untuk konfirmasi:</p>
                <form action="{{ route('activity-log.destroy-all') }}" method="POST" id="clearAllForm">
                    @csrf
                    @method('DELETE')
                    <input type="text" name="confirm" id="confirmInput" class="form-control"
                           placeholder="Ketik DELETE di sini">
                </form>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger" onclick="submitClearAll()">
                    <i class="fas fa-trash me-1"></i>Hapus Semua
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function submitClearAll() {
    if (document.getElementById('confirmInput').value !== 'DELETE') {
        alert('Ketik DELETE untuk melanjutkan.');
        return;
    }
    document.getElementById('clearAllForm').submit();
}
</script>
@endpush
