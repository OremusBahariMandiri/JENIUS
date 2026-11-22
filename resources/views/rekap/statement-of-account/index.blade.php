@extends('layouts.app')
@section('title', 'Statement of Account')

@section('content')
<div class="container-fluid soaIndexPage">
    <div class="card shadow">
        <div class="card-header text-black d-flex justify-content-between align-items-center"
             style="background-color:#d1fae5">
            <span class="fw-bold">
                <i class="fas fa-address-card me-2"></i>
                Statement of Account
            </span>
            {{-- Search bar --}}
            <form method="GET" action="{{ route('soa.index') }}" class="d-flex gap-2" style="min-width:280px">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search customer / email…" value="{{ $search ?? '' }}">
                <button class="btn btn-primary btn-sm" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                @if($search)
                <a href="{{ route('soa.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="soaTable" class="table table-bordered table-striped table-hover align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="3%">No</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th class="text-center">Tramper Jobs</th>
                            <th class="text-center">Other Jobs</th>
                            <th class="text-center">Total Jobs</th>
                            <th class="text-center">Outstanding Invoices</th>
                            <th class="text-end">Outstanding Amount</th>
                            <th class="text-center" width="6%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $i => $row)
                        <tr class="{{ $row['inv_outstanding'] > 0 ? '' : 'text-muted' }}">
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>
                                <span class="fw-semibold">{{ $row['customer'] }}</span>
                                @if($row['address'])
                                    <br><small class="text-muted">{{ Str::limit($row['address'], 60) }}</small>
                                @endif
                            </td>
                            <td class="small">
                                @if($row['phone'])
                                    <div><i class="fas fa-phone fa-xs me-1 text-muted"></i>{{ $row['phone'] }}</div>
                                @endif
                                @if($row['email'])
                                    <div><i class="fas fa-envelope fa-xs me-1 text-muted"></i>{{ $row['email'] }}</div>
                                @endif
                                @if(!$row['phone'] && !$row['email'])
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $row['job_tramper'] > 0 ? $row['job_tramper'] : '-' }}
                            </td>
                            <td class="text-center">
                                {{ $row['job_other'] > 0 ? $row['job_other'] : '-' }}
                            </td>
                            <td class="text-center fw-semibold">
                                {{ $row['total_jobs'] > 0 ? $row['total_jobs'] : '-' }}
                            </td>
                            <td class="text-center">
                                {{ $row['inv_outstanding'] }}
                            </td>
                            <td class="text-end">
                                @if($row['total_outstanding'] > 0)
                                    <span class="fw-semibold text-danger">
                                        {{ number_format($row['total_outstanding'], 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('soa.show', $row['id_md_cust']) }}"
                                   class="btn btn-sm btn-outline-primary" title="View SOA">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>No customers found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#soaTable').DataTable({
        pageLength: 25,
        responsive: true,
        order: [[8, 'desc']],
        columnDefs: [{ orderable: false, targets: [2, 9] }],
        language: {
            search: 'Search:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'No entries to show',
            infoFiltered: '(filtered from _MAX_ total entries)',
            zeroRecords: 'No matching records found',
            paginate: { next: '›', previous: '‹' },
        },
    });
});
</script>
@endpush
