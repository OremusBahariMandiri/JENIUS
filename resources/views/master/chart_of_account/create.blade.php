@extends('layouts.app')

@section('title', 'Add Chart of Account')

@section('content')
<div class="container-fluid coaPage">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add New Chart of Account</span>
                    <a href="{{ route('chart-of-account.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('chart-of-account.store') }}" method="POST" id="coaForm">
                        @csrf


                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Parent Account</label>
                                    <select name="parrent"
                                            class="form-select @error('parrent') is-invalid @enderror"
                                            id="parentSelect">
                                        <option value="">-- Select Parent --</option>
                                        @foreach($parentAccounts as $parent)
                                        <option value="{{ $parent->kode_perkiraan }}"
                                            {{ old('parrent') == $parent->kode_perkiraan ? 'selected' : '' }}>
                                            [{{ $parent->kode_perkiraan }}] {{ $parent->nama }}
                                            @if($parent->costType) — {{ $parent->costType->name }} @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('parrent')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tipe Akun (readonly, dari parent) -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tipe Akun</label>
                                    <input type="text"
                                           id="tipeAkunDisplay"
                                           class="form-control"
                                           placeholder=""
                                           readonly>
                                </div>
                            </div>

                            <!-- No. Account -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">No. Account</label>
                                    <input type="text"
                                           name="no_account"
                                           class="form-control @error('no_account') is-invalid @enderror"
                                           value="{{ old('no_account') }}"
                                           placeholder="e.g. 1-1001">
                                    @error('no_account')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Account Name -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Account Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="account_name"
                                           class="form-control @error('account_name') is-invalid @enderror"
                                           value="{{ old('account_name') }}"
                                           placeholder="Enter account name"
                                           required>
                                    @error('account_name')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <!-- Type -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Type</label>
                                    <select name="type"
                                            class="form-select @error('type') is-invalid @enderror">
                                        <option value="">-- Select Type --</option>
                                        @foreach($typeOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Payment Type -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Payment Type</label>
                                    <select name="payment_type"
                                            class="form-select @error('payment_type') is-invalid @enderror">
                                        <option value="">-- Select Payment Type --</option>
                                        @foreach($paymentTypeOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('payment_type') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('payment_type')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Opening Balance -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Opening Balance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number"
                                               name="opening_balance"
                                               class="form-control @error('opening_balance') is-invalid @enderror"
                                               value="{{ old('opening_balance', 0) }}"
                                               step="0.01" min="0">
                                        @error('opening_balance')
                                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Current Balance -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Current Balance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number"
                                               name="current_balance"
                                               class="form-control @error('current_balance') is-invalid @enderror"
                                               value="{{ old('current_balance', 0) }}"
                                               step="0.01" min="0">
                                        @error('current_balance')
                                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('chart-of-account.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .coaPage .card { border: none; border-radius: 10px; }
    .coaPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; }
    .coaPage .form-control:focus, .coaPage .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }
    .coaPage .btn { padding: 0.5rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
    .coaPage .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .coaPage .btn-success { background-color: var(--primary-green); border-color: var(--primary-green); }
    .coaPage .btn-success:hover { background-color: var(--dark-green); border-color: var(--dark-green); }
    .coaPage .input-group-text { background-color: #f8f9fa; font-weight: 500; }
    #tipeAkunDisplay { background-color: #f8f9fa; color: #6c757d; }
</style>
@endpush

@push('scripts')
<script>
// Data tipe akun dari parent (di-embed dari controller)
const parentData = @json($parentAccounts->map(fn($p) => [
    'kode'      => $p->kode_perkiraan,
    'cost_type' => $p->costType?->name ?? '',
]));

$(document).ready(function() {
    $('input[name="account_name"]').focus();

    // Update tipe akun display saat parent dipilih
    $('#parentSelect').on('change', function() {
        const kode     = $(this).val();
        const found    = parentData.find(p => p.kode === kode);
        $('#tipeAkunDisplay').val(found ? found.cost_type : '');
    });

    // Trigger saat load jika ada old value
    $('#parentSelect').trigger('change');
});
</script>
@endpush