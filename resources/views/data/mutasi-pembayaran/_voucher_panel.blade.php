{{-- resources/views/data/mutasi-pembayaran/_voucher_panel.blade.php --}}
@php
    $details    = $voucher->details ?? collect([]);
    $totalNilai = $details->sum('nilai');
    $totalPj    = $details->sum('pj');
    $totalSelisih = $details->sum('selisih');

    // Formatted date for display
    $tglKeluar = $voucher->tgl_keluar
        ? \Carbon\Carbon::parse($voucher->tgl_keluar)->format('d-M-Y')
        : null;

    // COA name for display
    $coaName = $voucher->coa
        ? trim(($voucher->coa->no_account ?? '') . ' ' . ($voucher->coa->account_name ?? $voucher->coa->nama_account ?? ''))
        : null;

    // Raw values for data attributes (used by JS editVoucher)
    $tglKeluar_raw = $voucher->tgl_keluar
        ? \Carbon\Carbon::parse($voucher->tgl_keluar)->format('Y-m-d')
        : '';
    $coaId_raw = $voucher->id_md_chart_of_account ?? '';
@endphp

<div class="voucher-accordion-item"
     id="voucher-panel-{{ $voucher->id }}"
     data-voucher-id="{{ $voucher->id }}"
     data-tgl-keluar="{{ $tglKeluar_raw }}"
     data-coa-id="{{ $coaId_raw }}">

    {{-- ── HEADER ── --}}
    <div class="voucher-accordion-header" onclick="toggleVoucherPanel({{ $voucher->id }})">
        <div class="voucher-title" style="flex-direction:column; align-items:flex-start; gap:4px;">
            {{-- Row 1: number badge + voucher number + keterangan --}}
            <div class="d-flex align-items-center gap-2">
                <span class="voucher-number-badge">#{{ $voucher->urutan }}</span>
                <strong class="voucher-nomor-text">{{ $voucher->nomor_voucher }}</strong>
                <span class="text-muted small voucher-ket-text">
                    {{ $voucher->keterangan ? '— ' . $voucher->keterangan : '' }}
                </span>
            </div>
            {{-- Row 2: tgl_keluar + COA info pills --}}
            <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size:0.75rem;">
                @if($tglKeluar)
                    <span style="background:#d1fae5;color:#065f46;border-radius:20px;padding:1px 8px;font-weight:600;">
                        <i class="fas fa-calendar-alt me-1"></i>{{ $tglKeluar }}
                    </span>
                @else
                    <span class="voucher-tgl-text" style="background:#f1f5f9;color:#94a3b8;border-radius:20px;padding:1px 8px;">
                        <i class="fas fa-calendar-alt me-1"></i><em>No date</em>
                    </span>
                @endif
                @if($coaName)
                    <span class="voucher-coa-text" style="background:#dbeafe;color:#1e40af;border-radius:20px;padding:1px 8px;font-weight:600;">
                        <i class="fas fa-university me-1"></i>{{ $coaName }}
                    </span>
                @else
                    <span class="voucher-coa-text" style="background:#f1f5f9;color:#94a3b8;border-radius:20px;padding:1px 8px;">
                        <i class="fas fa-university me-1"></i><em>No bank/cash</em>
                    </span>
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark detail-count-badge-{{ $voucher->id }}">
                {{ $details->count() }} detail
            </span>
            <a href="{{ route('mutasi-pembayaran.voucher.pdf', $voucher->id) }}"
               target="_blank"
               class="btn btn-sm btn-success"
               onclick="event.stopPropagation()"
               title="Export PDF">
                <i class="fas fa-file-pdf"></i>
            </a>
            <button type="button" class="btn btn-sm btn-warning"
                onclick="event.stopPropagation(); editVoucher({{ $voucher->id }})">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger"
                onclick="event.stopPropagation(); deleteVoucher({{ $voucher->id }}, '{{ addslashes($voucher->nomor_voucher) }}')">
                <i class="fas fa-trash"></i>
            </button>
            <i class="fas fa-chevron-down toggle-icon-{{ $voucher->id }}"></i>
        </div>
    </div>

    {{-- ── BODY ── --}}
    <div class="voucher-accordion-body" id="voucher-body-{{ $voucher->id }}" style="display:none;">

        {{-- ══════════════════════════════════════════
             FORM TAMBAH / EDIT DETAIL
        ══════════════════════════════════════════ --}}
        <div class="add-form-section mb-3" id="addDetailSection-{{ $voucher->id }}">
            <h6 id="detailFormTitle-{{ $voucher->id }}">
                <i class="fas fa-plus-square" style="color:#16a34a;"></i> Add Detail
            </h6>

            {{-- ── ADD MODE ── --}}
            <div id="detailAddMode-{{ $voucher->id }}">

                {{-- Baris 1 : Jenis | JO (tramper/other/contract) | Kasbon (general) --}}
                <div class="row g-3 mb-2">

                    {{-- Jenis --}}
                    <div class="col-12" id="jenisCol-{{ $voucher->id }}">
                        <label class="form-label fw-semibold required-field">Type</label>
                        <select id="input_jenis-{{ $voucher->id }}" class="form-select"
                            onchange="onJenisChange({{ $voucher->id }})">
                            <option value="">-- Select Type --</option>
                            <option value="tramper">Tramper</option>
                            <option value="other">Other</option>
                            <option value="contract">Contract</option>
                            <option value="general">General</option>
                        </select>
                    </div>

                    {{-- JO select --}}
                    <div class="col-12" id="joCol-{{ $voucher->id }}" style="display:none;">
                        <label class="form-label fw-semibold required-field">JO</label>
                        <select id="input_jo-{{ $voucher->id }}"
                            class="form-select select2-jo-{{ $voucher->id }}"
                            data-placeholder="Select JO..."
                            onchange="onJoChange({{ $voucher->id }})">
                            <option value=""></option>
                        </select>
                    </div>

                    {{-- Kasbon General select --}}
                    <div class="col-12" id="genKasbonCol-{{ $voucher->id }}" style="display:none;">
                        <label class="form-label fw-semibold required-field">General Cash Advance</label>
                        <select id="input_kasbon_gen_sel-{{ $voucher->id }}"
                            class="form-select select2-gen-kasbon-{{ $voucher->id }}"
                            data-placeholder="Select General CA..."
                            onchange="onGeneralKasbonChange({{ $voucher->id }})">
                            <option value=""></option>
                        </select>
                    </div>

                </div>{{-- /row --}}

                {{-- Kasbon & LPJ Summary Table --}}
                <div id="joKasbonSummaryWrap-{{ $voucher->id }}" class="mb-2" style="display:none;">
                    <div class="kasbon-summary-wrap">
                        <div class="kasbon-summary-header">
                            <span>
                                <i class="fas fa-list-alt me-2"></i>Cash Advances &amp; LPJ
                                <span class="badge bg-white text-success ms-2"
                                    id="kasbonSummaryCount-{{ $voucher->id }}">0 cash adv.</span>
                            </span>
                            <button type="button" id="btnAddAll-{{ $voucher->id }}" class="btn btn-sm"
                                style="background:#fff;color:#16a34a;border:1px solid #16a34a;font-weight:600;display:none;"
                                onclick="addAllKasbon({{ $voucher->id }})">
                                <i class="fas fa-plus-circle me-1"></i> Add All
                            </button>
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table table-kasbon-summary mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-start">CA Number</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end">PJ / LPJ</th>
                                        <th class="text-end">Balance</th>
                                        <th>Notes</th>
                                        <th width="90" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="kasbonSummaryBody-{{ $voucher->id }}">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            <i class="fas fa-arrow-up me-1"></i>
                                            Select type to show cash advances
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot id="kasbonSummaryFoot-{{ $voucher->id }}"></tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>{{-- /detailAddMode --}}

            {{-- ── EDIT MODE (hanya keterangan yang bisa diubah) ── --}}
            <div id="detailEditMode-{{ $voucher->id }}" style="display:none;" class="detail-edit-mode-box">
                <input type="hidden" id="editing_detail_id-{{ $voucher->id }}" value="">
                <div class="edit-info-grid mb-3">
                    <div class="edit-info-card">
                        <div class="edit-info-lbl">Type</div>
                        <div class="edit-info-val">
                            <span id="edit_jenis_badge-{{ $voucher->id }}" class="jenis-badge"></span>
                        </div>
                    </div>
                    <div class="edit-info-card">
                        <div class="edit-info-lbl">JO</div>
                        <div class="edit-info-val" id="edit_jo_nomor-{{ $voucher->id }}">—</div>
                    </div>
                    <div class="edit-info-card">
                        <div class="edit-info-lbl">CA No.</div>
                        <div class="edit-info-val" id="edit_kasbon_nomor-{{ $voucher->id }}">—</div>
                    </div>
                    <div class="edit-info-card">
                        <div class="edit-info-lbl">CA Amount (IDR)</div>
                        <div class="edit-info-val clr-nilai" id="edit_nilai_display-{{ $voucher->id }}">—</div>
                    </div>
                    <div class="edit-info-card">
                        <div class="edit-info-lbl">LPJ Amount (IDR)</div>
                        <div class="edit-info-val clr-pj" id="edit_pj_display-{{ $voucher->id }}">—</div>
                    </div>
                    <div class="edit-info-card">
                        <div class="edit-info-lbl">Balance (IDR)</div>
                        <div class="edit-info-val" id="edit_selisih_display-{{ $voucher->id }}">—</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Notes</label>
                    <input type="text" id="edit_keterangan-{{ $voucher->id }}"
                        class="form-control" placeholder="Write notes...">
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-cancel-edit-style"
                        onclick="cancelDetailEdit({{ $voucher->id }})">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-add-purple"
                        onclick="submitDetailEdit({{ $voucher->id }})">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </div>

        </div>{{-- /add-form-section --}}


        {{-- ══════════════════════════════════════════
             TABEL DETAIL
        ══════════════════════════════════════════ --}}
        <div class="table-responsive mt-2">
            <table class="table table-detail table-bordered" id="detailTable-{{ $voucher->id }}">
                <thead>
                    <tr>
                        <th width="120" class="text-center">JO</th>
                        <th style="min-width:150px;">CA No.</th>
                        <th width="130">Category</th>
                        <th>Description</th>
                        <th style="min-width:160px;" class="text-end">CA Amount (IDR)</th>
                        <th style="min-width:160px;" class="text-end">LPJ Amount (IDR)</th>
                        <th style="min-width:160px;" class="text-end">Balance (IDR)</th>
                        <th width="80" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="detailTableBody-{{ $voucher->id }}">
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">
                            <i class="fas fa-chevron-up me-1"></i> Open panel to load data
                        </td>
                    </tr>
                </tbody>
                <tfoot id="detailTableFoot-{{ $voucher->id }}"></tfoot>
            </table>
        </div>

    </div>{{-- /voucher-accordion-body --}}
</div>{{-- /voucher-accordion-item --}}