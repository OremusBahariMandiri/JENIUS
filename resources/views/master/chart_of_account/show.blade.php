@extends('layouts.app')

@section('title', 'Chart of Account Detail')

@section('content')
<div class="container-fluid coaPage">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Chart of Account Detail</span>
                    <div class="d-flex gap-2">
                        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccess('chart_of_account', 'ubah')))
                        <a href="{{ route('chart-of-account.edit', $account->id_md_chart_of_account) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @endif
                        <a href="{{ route('chart-of-account.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Account Name -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase mb-2">Account Name</label>
                        <h3 class="mb-0 text-primary">
                            <i class="fas fa-sitemap me-2"></i>
                            @if($account->no_account)
                            <span class="text-muted fs-5 me-2">{{ $account->no_account }}</span>
                            @endif
                            {{ $account->account_name }}
                        </h3>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <!-- Parent Account -->
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Parent Account</label>
                            <p class="mb-0 fw-bold">
                                @if($account->parentAccount)
                                    <i class="fas fa-level-up-alt me-1 text-muted"></i>
                                    <span class="badge bg-secondary me-1">{{ $account->parentAccount->kode_perkiraan }}</span>
                                    {{ $account->parentAccount->nama }}
                                @else
                                    <span class="text-muted fst-italic">Root Account</span>
                                @endif
                            </p>
                        </div>

                        <!-- Tipe Akun (dari parent) -->
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Tipe Akun</label>
                            <p class="mb-0 fw-bold">
                                @if($account->parentAccount?->costType)
                                <span class="badge bg-info text-dark fs-6 px-3 py-2">
                                    {{ $account->parentAccount->costType->name }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>

                        <!-- Type -->
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Type</label>
                            <p class="mb-0">
                                @if($account->type)
                                @php
                                    $typeColor = match($account->type) {
                                        'Asset'     => 'primary',
                                        'Revenue'   => 'success',
                                        'Expense'   => 'danger',
                                        'Liability' => 'warning',
                                        'Equity'    => 'secondary',
                                        default     => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $typeColor }} fs-6 px-3 py-2">{{ $account->type }}</span>
                                @else
                                -
                                @endif
                            </p>
                        </div>

                        <!-- Payment Type -->
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Payment Type</label>
                            <p class="mb-0 fw-bold">{{ $account->payment_type ?? '-' }}</p>
                        </div>

                        <!-- Opening Balance -->
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Opening Balance</label>
                            <p class="mb-0 fw-bold fs-5">
                                Rp {{ number_format($account->opening_balance, 2, ',', '.') }}
                            </p>
                        </div>

                        <!-- Current Balance -->
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase mb-2">Current Balance</label>
                            <p class="mb-0 fw-bold fs-5 {{ $account->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($account->current_balance, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- Timestamps -->
                    <div class="row text-muted">
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-plus me-2 text-primary"></i>
                            <strong>Created:</strong><br>
                            <span class="ms-4">{{ $account->created_at->format('d F Y, H:i') }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-calendar-check me-2 text-success"></i>
                            <strong>Last Updated:</strong><br>
                            <span class="ms-4">{{ $account->updated_at->format('d F Y, H:i') }}</span>
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
    .coaPage .card { border: none; border-radius: 10px; }
    .coaPage .card-header { border-radius: 10px 10px 0 0 !important; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; }
    .coaPage h3 { color: var(--primary-green); }
    .coaPage .text-primary { color: var(--primary-green) !important; }
    .coaPage .btn { padding: 0.5rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
    .coaPage .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .coaPage .badge { border-radius: 6px; font-weight: 500; }
</style>
@endpush