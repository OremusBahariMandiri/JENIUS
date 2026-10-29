@php
    $tglFmt = $voucher->tanggal ? $voucher->tanggal->format('d-M-Y') : null;
    $tglRaw = $voucher->tanggal ? $voucher->tanggal->format('Y-m-d') : '';
    $coaDisplay = $voucher->coa
        ? trim(($voucher->coa->no_account ?? '') . ' ' . ($voucher->coa->account_name ?? ''))
        : null;
@endphp

<div class="voucher-accordion-item" id="voucher-panel-{{ $voucher->id }}" data-voucher-id="{{ $voucher->id }}"
    data-tgl="{{ $tglRaw }}" data-coa-id="{{ $voucher->id_md_chart_of_account ?? '' }}">

    <div class="voucher-accordion-header" onclick="toggleVoucherPanel({{ $voucher->id }})">
        <div class="voucher-title" style="flex-direction:column; align-items:flex-start; gap:4px;">
            <div class="d-flex align-items-center gap-2">
                <span class="voucher-number-badge">#{{ $voucher->urutan }}</span>
                <strong class="voucher-nomor-text">{{ $voucher->nomor_voucher }}</strong>
                <span
                    class="text-muted small voucher-ket-text">{{ $voucher->keterangan ? '— ' . $voucher->keterangan : '' }}</span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size:.75rem;">
                @if ($tglFmt)
                    <span class="voucher-tgl-text"
                        style="background:#d1fae5;color:#065f46;border-radius:20px;padding:1px 8px;font-weight:600;">
                        <i class="fas fa-calendar-alt me-1"></i>{{ $tglFmt }}
                    </span>
                @else
                    <span class="voucher-tgl-text"
                        style="background:#f1f5f9;color:#94a3b8;border-radius:20px;padding:1px 8px;">
                        <i class="fas fa-calendar-alt me-1"></i><em>No date</em>
                    </span>
                @endif
                @if ($coaDisplay)
                    <span class="voucher-coa-text"
                        style="background:#dbeafe;color:#1e40af;border-radius:20px;padding:1px 8px;font-weight:600;">
                        <i class="fas fa-university me-1"></i>{{ $coaDisplay }}
                    </span>
                @else
                    <span class="voucher-coa-text"
                        style="background:#f1f5f9;color:#94a3b8;border-radius:20px;padding:1px 8px;">
                        <i class="fas fa-university me-1"></i><em>No bank/cash</em>
                    </span>
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span
                class="badge bg-light text-dark detail-count-badge-{{ $voucher->id }}">{{ $voucher->details->count() }}
                detail</span>
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

    <div class="voucher-accordion-body" id="voucher-body-{{ $voucher->id }}" style="display:none;">

        {{-- ══ FORM TAMBAH / EDIT DETAIL ══ --}}
        <div class="add-form-section mb-3" id="addDetailSection-{{ $voucher->id }}">
            <h6 id="detailFormTitle-{{ $voucher->id }}">
                <i class="fas fa-plus-square" style="color:#16a34a;"></i> Add Detail
            </h6>

            {{-- ADD MODE --}}
            <div id="detailAddMode-{{ $voucher->id }}">

                <div class="row g-3">
                    {{-- Step 1: Jenis --}}
                    <div class="col-12" id="jenisCol-{{ $voucher->id }}">
                        <label class="form-label fw-semibold required-field">Type</label>
                        <select id="input_jenis-{{ $voucher->id }}" class="form-select"
                            onchange="onJenisChange({{ $voucher->id }})">
                            <option value="">-- Select --</option>
                            <option value="tramper">Tramper</option>
                            <option value="other">Other</option>
                            <option value="contract">Contract</option>
                            <option value="general">General</option>
                        </select>
                    </div>

                    {{-- Step 2a: JO (tramper/other/contract) --}}
                    <div class="col-md-9" id="joCol-{{ $voucher->id }}" style="display:none;">
                        <label class="form-label fw-semibold required-field">Job Order</label>
                        <select id="input_jo-{{ $voucher->id }}" class="form-select select2-jo-{{ $voucher->id }}"
                            data-placeholder="Select JO..." onchange="onJoChange({{ $voucher->id }})">
                            <option value=""></option>
                        </select>
                    </div>

                    {{-- Step 2b: Kasbon General --}}
                    <div class="col-md-9" id="genKasbonCol-{{ $voucher->id }}" style="display:none;">
                        <label class="form-label fw-semibold required-field">General Cash Advance</label>
                        <select id="input_kasbon_gen-{{ $voucher->id }}"
                            class="form-select select2-kasbon-gen-{{ $voucher->id }}"
                            data-placeholder="Select General CA..."
                            onchange="onGeneralKasbonChange({{ $voucher->id }})">
                            <option value=""></option>
                        </select>
                    </div>
                </div>

                {{-- Customer info panel (muncul setelah JO dipilih) --}}
                {{-- Customer info panel (muncul setelah JO dipilih) --}}
                <div id="custPanel-{{ $voucher->id }}" class="mt-3" style="display:none;">
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <div
                                style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
                                <div
                                    style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">
                                    Customer</div>
                                <div id="joCustomerName-{{ $voucher->id }}"
                                    style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
                                <input type="hidden" id="input_id_md_cust-{{ $voucher->id }}" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div
                                style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
                                <div
                                    style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">
                                    Vessel</div>
                                <div id="joVesselName-{{ $voucher->id }}"
                                    style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div
                                style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
                                <div
                                    style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">
                                    Port</div>
                                <div id="joPortName-{{ $voucher->id }}"
                                    style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
                                <input type="hidden" id="input_id_md_port-{{ $voucher->id }}" value="">
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Tipe Pembayaran --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold required-field">Payment Type</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-outline-primary btn-tipe-pem"
                                    data-tipe="uang_muka_pembayaran"
                                    onclick="selectTipePembayaran({{ $voucher->id }}, 'uang_muka_pembayaran', this)">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> Down Payment
                                </button>
                                <button type="button" class="btn btn-outline-success btn-tipe-pem"
                                    data-tipe="penerimaan_pembayaran"
                                    onclick="selectTipePembayaran({{ $voucher->id }}, 'penerimaan_pembayaran', this)">
                                    <i class="fas fa-hand-holding-usd me-1"></i> Payment Receipt
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-tipe-pem"
                                    data-tipe="pengembalian_lebih_bayar"
                                    onclick="selectTipePembayaran({{ $voucher->id }}, 'pengembalian_lebih_bayar', this)">
                                    <i class="fas fa-undo-alt me-1"></i> Refund
                                </button>
                            </div>
                            <input type="hidden" id="input_tipe_pem-{{ $voucher->id }}" value="">
                        </div>
                    </div>
                </div>

                {{-- Kasbon General info panel (muncul setelah kasbon gen dipilih) --}}
                <div id="genKasbonInfoPanel-{{ $voucher->id }}" class="mt-3" style="display:none;">
                    @include('data.mutasi-penerimaan._kasbon_info_panel', ['vid' => $voucher->id])
                    <div id="genKasbonAlert-{{ $voucher->id }}" class="mt-2" style="display:none;"></div>
                    <div id="genKasbonForm-{{ $voucher->id }}" style="display:none;">
                        <div class="row g-3 mt-1">

                            {{-- Notes: satu-satunya input manual untuk general --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <input type="text" id="input_ket_gen-{{ $voucher->id }}" class="form-control"
                                    placeholder="Notes...">
                            </div>

                            <div class="col-12 d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-cancel-edit-style"
                                    onclick="resetDetailForm({{ $voucher->id }})">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="button" class="btn btn-add-purple"
                                    onclick="saveDetailGeneral({{ $voucher->id }})">
                                    <i class="fas fa-plus me-1"></i> Save Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Pengembalian (muncul setelah tipe = pengembalian) --}}
                <div id="pengembalianSection-{{ $voucher->id }}" class="mt-3" style="display:none;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold required-field">Cash Advance (from JO)</label>
                            <select id="input_kasbon-{{ $voucher->id }}"
                                class="form-select select2-kasbon-{{ $voucher->id }}"
                                data-placeholder="Select CA..." onchange="onKasbonChange({{ $voucher->id }})">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    {{-- Kasbon info panel --}}
                    <div id="kasbonInfoPanel-{{ $voucher->id }}" class="mt-3" style="display:none;">
                        @include('data.mutasi-penerimaan._kasbon_info_panel', [
                            'vid' => $voucher->id,
                            'prefix' => 'kasbon',
                        ])
                        <div id="kasbonAlert-{{ $voucher->id }}" class="mt-2" style="display:none;"></div>
                        <div id="kasbonForm-{{ $voucher->id }}" style="display:none;">
                            <div class="row g-3 mt-1">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Notes</label>
                                    <input type="text" id="input_ket-{{ $voucher->id }}" class="form-control"
                                        placeholder="Notes...">
                                </div>
                                <div class="col-12 d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-cancel-edit-style"
                                        onclick="resetDetailForm({{ $voucher->id }})">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    <button type="button" class="btn btn-add-purple"
                                        onclick="saveDetailPengembalian({{ $voucher->id }})">
                                        <i class="fas fa-plus me-1"></i> Save Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Manual (uang_muka / penerimaan) --}}
                <div id="manualSection-{{ $voucher->id }}" class="mt-3" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold required-field">Department</label>
                            <select id="input_dep_manual-{{ $voucher->id }}"
                                class="form-select select2-dep-manual-{{ $voucher->id }}"
                                data-placeholder="Select Dept...">
                                <option value=""></option>
                                @foreach ($departemenList ?? [] as $dep)
                                    <option value="{{ $dep->id_md_dep }}">{{ $dep->nama_dep }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold required-field">Branch</label>
                            <select id="input_branch_manual-{{ $voucher->id }}"
                                class="form-select select2-branch-manual-{{ $voucher->id }}"
                                data-placeholder="Select Branch...">
                                <option value=""></option>
                                @foreach ($branchList ?? [] as $branch)
                                    <option value="{{ $branch->id_md_branch }}">{{ $branch->nama_branch }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold required-field">Amount (IDR)</label>
                            <input type="text" id="input_nilai-{{ $voucher->id }}" class="form-control"
                                placeholder="0,00">
                            <input type="hidden" id="input_nilai_raw-{{ $voucher->id }}" value="0">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" id="input_ket_manual-{{ $voucher->id }}" class="form-control"
                                placeholder="Notes...">
                        </div>
                        <div class="col-12 d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-cancel-edit-style"
                                onclick="resetDetailForm({{ $voucher->id }})">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-add-purple"
                                onclick="saveDetailManual({{ $voucher->id }})">
                                <i class="fas fa-plus me-1"></i> Save Detail
                            </button>
                        </div>
                    </div>
                </div>

            </div>{{-- /detailAddMode --}}

            {{-- EDIT MODE --}}
            <div id="detailEditMode-{{ $voucher->id }}" style="display:none;" class="detail-edit-mode-box">
                <input type="hidden" id="editing_detail_id-{{ $voucher->id }}" value="">
                <input type="hidden" id="editing_detail_tipe-{{ $voucher->id }}" value="">
                <div class="edit-info-grid mb-3">
                    <div class="edit-info-card">
                        <div class="edit-info-lbl">Type</div>
                        <div class="edit-info-val"><span id="edit_jenis_badge-{{ $voucher->id }}"
                                class="jenis-badge"></span></div>
                    </div>
                    <div class="edit-info-card">
                        <div class="edit-info-lbl">Payment Type</div>
                        <div class="edit-info-val" id="edit_tipe_pem-{{ $voucher->id }}">—</div>
                    </div>
                    <div class="edit-info-card" id="edit_customer_card-{{ $voucher->id }}">
                        <div class="edit-info-lbl" id="edit_customer_lbl-{{ $voucher->id }}">Customer</div>
                        <div class="edit-info-val" id="edit_customer-{{ $voucher->id }}">—</div>
                    </div>
                    <div class="edit-info-card">
                        <div class="edit-info-lbl">JO / Kasbon</div>
                        <div class="edit-info-val" id="edit_ref-{{ $voucher->id }}">—</div>
                    </div>
                    {{-- Amount: hanya ditampilkan saat refund (non-refund pakai input di bawah) --}}
                    <div id="edit_nilai_info_card-{{ $voucher->id }}" class="edit-info-card">
                        <div class="edit-info-lbl">Amount</div>
                        <div class="edit-info-val clr-nilai" id="edit_nilai_display-{{ $voucher->id }}">—</div>
                    </div>
                </div>

                {{-- Field editable: hanya untuk non-refund --}}
                <div id="edit_nonrefund_fields-{{ $voucher->id }}" style="display:none;">
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <select id="edit_dep-{{ $voucher->id }}"
                                class="form-select select2-edit-dep-{{ $voucher->id }}"
                                data-placeholder="Select Department...">
                                <option value=""></option>
                                @foreach ($departemenList ?? [] as $dep)
                                    <option value="{{ $dep->id_md_dep }}">{{ $dep->nama_dep }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Branch</label>
                            <select id="edit_branch-{{ $voucher->id }}"
                                class="form-select select2-edit-branch-{{ $voucher->id }}"
                                data-placeholder="Select Branch...">
                                <option value=""></option>
                                @foreach ($branchList ?? [] as $branch)
                                    <option value="{{ $branch->id_md_branch }}">{{ $branch->nama_branch }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12" id="edit_nilai_col-{{ $voucher->id }}">
                            <label class="form-label fw-semibold required-field">Amount (IDR)</label>
                            <input type="text" id="edit_nilai_input-{{ $voucher->id }}" class="form-control"
                                placeholder="0,00">
                            <input type="hidden" id="edit_nilai_raw-{{ $voucher->id }}" value="0">
                        </div>
                    </div>
                </div>

                {{-- Release To: hanya tampil saat general --}}
                <div class="mb-3" id="edit_release_to_wrap-{{ $voucher->id }}" style="display:none;">
                    <label class="form-label fw-semibold required-field">Release To</label>
                    <input type="text" id="edit_release_to-{{ $voucher->id }}" class="form-control"
                        placeholder="Release to..." readonly style="background:#f0fdf4;">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Notes</label>
                    <input type="text" id="edit_keterangan-{{ $voucher->id }}" class="form-control"
                        placeholder="Notes...">
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

        {{-- ══ TABEL DETAIL ══ --}}
        <div class="table-responsive mt-2">
            <table class="table table-detail table-bordered" id="detailTable-{{ $voucher->id }}">
                <thead>
                    <tr>
                        <th class="text-center" width="80">Type</th>
                        <th class="text-center" width="110">Pmt Type</th>
                        <th>Cust / Release To</th>
                        <th>JO / Kasbon</th>
                        <th width="140">Dept / Branch</th>
                        <th style="min-width:130px;" class="text-end">Amount (IDR)</th>
                        <th>Notes</th>
                        <th width="70" class="text-center">Act</th>
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
