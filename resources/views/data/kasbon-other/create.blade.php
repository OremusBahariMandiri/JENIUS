@extends('layouts.app')

@section('title', 'Add Cash Advance Other')

@push('styles')
    <style>
        .kasbonOtherCreatePage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .kasbonOtherCreatePage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: 600;
        }

        .floating-badge-alert {
            position: fixed;
            top: 80px;
            right: 30px;
            z-index: 9999;
            min-width: 260px;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            display: none;
            animation: slideInRight 0.4s ease-out;
        }

        .floating-badge-alert.show {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .floating-badge-alert.alert-saving {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
        }

        .floating-badge-alert.alert-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .floating-badge-alert.alert-error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .floating-badge-alert i {
            font-size: 1.3rem;
        }

        .floating-badge-alert .alert-text {
            flex: 1;
            font-weight: 600;
            font-size: 0.95rem;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .floating-badge-alert.hiding {
            animation: slideOutRight 0.4s ease-in;
        }

        .add-item-form-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px dashed #10b981;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .add-item-form-section h6 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .add-item-form-section h6 i {
            color: var(--primary-green);
            font-size: 1.2rem;
        }

        .currency-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .currency-label {
            background-color: #2c3e50;
            color: white;
            padding: 0.6rem 0.75rem;
            font-size: 0.7rem;
            border-radius: 6px 0 0 6px;
            min-width: 50px;
            text-align: center;
            font-weight: 600;
        }

        .currency-input {
            border-radius: 0 6px 6px 0 !important;
            border-left: none !important;
        }

        .table-items thead th {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #2c3e50;
            padding: 12px 10px;
            font-weight: 600;
            font-size: 0.875rem;
            vertical-align: middle;
            text-align: center;
        }

        .table-items tbody td {
            border: 1px solid #dee2e6;
            padding: 10px;
            vertical-align: middle;
            text-align: center;
            background-color: #fff;
        }

        .table-items tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table-footer {
            background: #2c3e50;
            color: white;
            font-weight: 700;
        }

        .table-footer td {
            padding: 15px 10px;
            font-size: 0.95rem;
        }

        .currency-group-footer {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .currency-label-footer {
            background-color: #2c3e50;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.6rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 6px;
            min-width: 55px;
            text-align: center;
            font-weight: 600;
        }

        .value-footer {
            flex: 1;
            color: #000;
            font-weight: 700;
            font-size: 0.95rem;
            padding-left: 10px;
        }

        .no-items-row td {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
            background: #f8f9fa !important;
        }

        .btn-remove-row {
            padding: 0.4rem 0.6rem;
            font-size: 0.875rem;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .btn-remove-row:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
        }

        .btn-add-to-table {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            transition: all 0.3s;
        }

        .btn-add-to-table:hover {
            transform: translateY(-3px);
            color: white;
        }

        .final-save-section {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 20px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px 12px 0 0;
            margin-top: 30px;
            z-index: 100;
        }

        .btn-final-back {
            background: linear-gradient(135deg, #868686, #5e5e5e);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s;
        }

        .btn-final-back:hover {
            transform: translateY(-3px);
            color: white;
        }

        .items-card-disabled {
            pointer-events: none;
            opacity: 0.6;
            position: relative;
        }

        .items-card-disabled::after {
            content: "Please save the header first to add items";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px 30px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            z-index: 1000;
        }

        .jo-summary-card {
            background: #fff;
            border: 1.5px solid #10b981;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            animation: fadeInDown 0.35s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .jo-summary-card .jo-meta-label {
            font-size: .72rem;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .jo-summary-card .jo-meta-value {
            font-size: .875rem;
            font-weight: 700;
            color: #2c3e50;
            margin-top: 2px;
        }

        #joItemsModal .modal-content {
            border-radius: 12px;
            border: none;
        }

        #joItemsModal .modal-header {
            background: #d1fae5;
            border-radius: 12px 12px 0 0;
            border-bottom: none;
        }

        #joItemsModal table thead th {
            background: #2c3e50;
            color: white;
            border-color: #2c3e50;
            font-size: .85rem;
            padding: 10px;
        }

        #joItemsModal table tbody td {
            font-size: .875rem;
            padding: 8px 10px;
            vertical-align: middle;
        }

        #joItemsModal table tbody tr:hover {
            background: #f0fdf4;
        }

        #joItemsModal tfoot td {
            background: #f8f9fa;
            font-weight: 700;
            font-size: .875rem;
            padding: 10px;
        }
    </style>
@endpush

@section('content')

    <div id="floatingBadgeAlert" class="floating-badge-alert">
        <i class="fas fa-circle-notch fa-spin" id="alertIcon"></i>
        <div class="alert-text" id="alertText">Processing...</div>
    </div>

    <div class="container-fluid kasbonOtherCreatePage">
        <div class="row">
            <div class="col-lg-12">

                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add Cash Advance Other</span>
                        <div class="d-flex gap-2 align-items-center">
                            <span id="autoSaveStatus" class="badge bg-secondary"><i class="fas fa-circle"></i> Not
                                Saved</span>
                            <a href="{{ route('kasbon-other.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="current_kasbon_other_id" value="">
                <input type="hidden" id="current_kasbon_other_str" value="">
                <input type="hidden" id="is_header_saved" value="false">

                <form id="kasbonOtherForm">
                    @csrf

                    {{-- HEADER CARD --}}
                    <div class="card shadow mb-4">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Cash Advance Information</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Cash Advance No.</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white"><i
                                                class="fas fa-file-alt"></i></span>
                                        <input type="text" class="form-control fw-bold" value="{{ $previewNoKasbon }}"
                                            readonly style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                    </div>
                                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Auto-generated on
                                        save</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Cash Advance Date</label>
                                    <input type="date" name="tgl_kasbon" id="tgl_kasbon" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label required-field">JO Other</label>
                                    <select name="id_jo_other" id="id_jo_other" class="form-select select2-field" required>
                                        <option value="">-- Select JO Other --</option>
                                        @foreach ($joOthers as $jo)
                                            <option value="{{ $jo->id_jo_other }}" data-no="{{ $jo->no_jo_other }}"
                                                data-title="{{ $jo->title ?? '' }}"
                                                data-tgl="{{ optional($jo->tgl_jo_other)->format('Y-m-d') ?? '' }}"
                                                data-customer="{{ $jo->customer->customer ?? '—' }}"
                                                data-port="{{ $jo->port->name_port ?? '—' }}"
                                                data-note="{{ $jo->note ?? '' }}">
                                                {{ $jo->no_jo_other }} — {{ $jo->title ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- JO Other Summary Card --}}
                                <div class="col-md-12" id="joSummaryWrapper" style="display:none;">
                                    <div class="jo-summary-card">
                                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                            <div class="d-flex align-items-center gap-3" style="flex:1; min-width:0;">
                                                <div
                                                    style="width:48px; height:48px; border-radius:10px; background:#d1fae5; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                    <i class="fas fa-file-invoice"
                                                        style="color:#059669; font-size:1.2rem;"></i>
                                                </div>
                                                <div style="min-width:0;">
                                                    <div id="joSummaryNo"
                                                        style="font-weight:700; font-size:1.05rem; color:#2c3e50;"></div>
                                                    <div id="joSummaryTitle"
                                                        style="font-size:.875rem; color:#6b7280; margin-top:3px;"></div>
                                                </div>
                                            </div>
                                            <button type="button" id="btnJoDetail"
                                                style="background:#2c3e50; color:#fff; border:none; border-radius:8px; padding:8px 18px; font-size:.82rem; font-weight:600; display:flex; align-items:center; gap:6px; cursor:pointer; white-space:nowrap; flex-shrink:0;"
                                                onmouseover="this.style.background='#1a252f'"
                                                onmouseout="this.style.background='#2c3e50'">
                                                <i class="fas fa-list-ul"></i> View Items
                                            </button>
                                        </div>
                                        <div style="border-top:1px solid #d1fae5; margin:14px 0;"></div>
                                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px 24px;">
                                            <div>
                                                <div class="jo-meta-label">JO No.</div>
                                                <div class="jo-meta-value" id="joSummaryNo2"
                                                    style="font-family:monospace; font-size:.82rem;"></div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">JO Date</div>
                                                <div class="jo-meta-value" id="joSummaryDate"></div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Total Items</div>
                                                <div class="jo-meta-value" id="joSummaryItemCount"><i
                                                        class="fas fa-circle-notch fa-spin"
                                                        style="color:#10b981; font-size:.8rem;"></i></div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Customer</div>
                                                <div class="jo-meta-value" id="joSummaryCustomer"></div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Port</div>
                                                <div class="jo-meta-value" id="joSummaryPort"></div>
                                            </div>
                                            <div>
                                                <div class="jo-meta-label">Note</div>
                                                <div class="jo-meta-value" id="joSummaryNote"
                                                    style="font-size:.82rem; white-space:pre-wrap;">—</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Department</label>
                                    <select name="id_md_dep" id="id_md_dep" class="form-select select2-field" required>
                                        <option value="">-- Select Department --</option>
                                        @foreach ($departemens as $dep)
                                            <option value="{{ $dep->id_md_dep }}">{{ $dep->nama_dep }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Branch</label>
                                    <select name="id_md_cabang" id="id_md_cabang" class="form-select select2-field"
                                        required>
                                        <option value="">-- Select Branch --</option>
                                        @foreach ($cabangs as $cabang)
                                            <option value="{{ $cabang->id_md_branch }}">{{ $cabang->nama_branch }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Release To</label>
                                    <select name="id_md_release" id="id_md_release" class="form-select select2-field"
                                        required>
                                        <option value="">-- Select Release To --</option>
                                        @foreach ($releases as $rel)
                                            <option value="{{ $rel->id_md_release }}">
                                                {{ $rel->nama_release ?? $rel->id_md_release }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Release Date</label>
                                    <input type="date" name="tgl_release" id="tgl_release" class="form-control">
                                </div>

                                {{-- Priority --}}
                                <div class="col-md-6">
                                    <label class="form-label required-field">Priority</label>
                                    <select name="priority" id="priority" class="form-select" required>
                                        <option value="normal" selected>Normal (Low)</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>

                                {{-- Due Date --}}
                                <div class="col-md-6">
                                    <label class="form-label">Due Date & Time</label>
                                    <input type="datetime-local" name="due_date" id="due_date" class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Note</label>
                                    <textarea name="note" id="note" class="form-control" rows="3"
                                        placeholder="Enter any additional notes..."></textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-success px-4" id="btnSaveHeader">
                                    <i class="fas fa-save me-1"></i> Save Header
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ITEMS CARD --}}
                    <div class="card shadow mb-4 items-card-disabled" id="itemsCard">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <h6 class="mb-0"><i class="fas fa-list me-2"></i>Cash Advance Items</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="add-item-form-section">
                                <h6><i class="fas fa-plus-square"></i> Add New Item</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label required-field">JO Other Item</label>
                                        <select id="input_jo_other_item" class="form-select">
                                            <option value="">-- Save header &amp; select JO Other first --</option>
                                        </select>
                                        <small class="text-muted mt-1 d-block" id="hppHint"></small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">HPP (Ops Costs)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_nilai_hpp"
                                                class="form-control currency-input" readonly
                                                style="background-color:#e9ecef;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required-field">CA Amount</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_nilai_kasbon"
                                                class="form-control currency-input" placeholder="0,00">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-add-to-table w-100" id="btnAddToTable">
                                            <i class="fas fa-arrow-down me-1"></i> Add to Table
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn-danger" id="resetAllBtn">
                                    <i class="fas fa-trash-alt me-1"></i> Reset All Items
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-items table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:5%;">No</th>
                                            <th>JO Item</th>
                                            <th>Category</th>
                                            <th style="width:18%;">HPP (IDR)</th>
                                            <th style="width:18%;">CA Amount (IDR)</th>
                                            <th style="width:18%;">Remaining (IDR)</th>
                                            <th style="width:8%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <tr class="no-items-row">
                                            <td colspan="7">
                                                <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                                                <p class="mb-0 fw-bold">No items yet</p>
                                                <small class="text-muted">Save the header first, then add items
                                                    above</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="table-footer">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>GRAND TOTAL</strong></td>
                                            <td>
                                                <div class="currency-group-footer"><span
                                                        class="currency-label-footer">IDR</span><span class="value-footer"
                                                        id="footerTotalHPP">0,00</span></div>
                                            </td>
                                            <td>
                                                <div class="currency-group-footer"><span
                                                        class="currency-label-footer">IDR</span><span class="value-footer"
                                                        id="footerTotalKasbon">0,00</span></div>
                                            </td>
                                            <td>
                                                <div class="currency-group-footer"><span
                                                        class="currency-label-footer">IDR</span><span class="value-footer"
                                                        id="footerTotalRemaining">0,00</span></div>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="final-save-section">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('kasbon-other.index') }}" class="btn btn-final-back">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JO ITEMS MODAL --}}
    <div class="modal fade" id="joItemsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:12px; border:none;">
                <div class="modal-header"
                    style="background:#d1fae5; border-radius:12px 12px 0 0; border-bottom:none; padding:1.25rem 1.5rem;">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="color:#2c3e50;"><i class="fas fa-list-ul me-2"
                                style="color:#059669;"></i>JO Other Items</h5>
                        <small id="joItemsModalSubtitle"
                            style="color:#6b7280; font-size:.8rem; display:block; margin-top:2px;"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.5rem; min-height:220px;">
                    <div id="joItemsModalLoading" class="text-center py-5"><i class="fas fa-circle-notch fa-spin fa-2x"
                            style="color:#10b981;"></i>
                        <p class="mt-2 text-muted mb-0">Loading items...</p>
                    </div>
                    <div id="joItemsModalContent" style="display:none;">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" style="font-size:.875rem;">
                                <thead>
                                    <tr>
                                        <th style="width:5%; text-align:center;">No</th>
                                        <th>Invoice Type</th>
                                        <th>Category</th>
                                        <th style="text-align:right;">HPP Ops (IDR)</th>
                                        <th style="text-align:right;">Selling Price (IDR)</th>
                                    </tr>
                                </thead>
                                <tbody id="joItemsModalBody"></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" style="text-align:right; font-weight:700;">GRAND TOTAL</td>
                                        <td style="text-align:right; font-weight:700; color:#059669; font-family:monospace;"
                                            id="joItemsTotalHpp"></td>
                                        <td style="text-align:right; font-weight:700; font-family:monospace;"
                                            id="joItemsTotalSell"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div id="joItemsModalEmpty"
                        style="display:none; text-align:center; padding:40px 20px; color:#6c757d;"><i
                            class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        <p class="fw-bold mb-0">No items found for this JO Other</p>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #d1fae5;">
                    <small id="joItemsModalCount" class="text-muted me-auto"></small>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let currentKasbonOtherId = null;
        let currentKasbonOtherStr = '';
        let isHeaderSaved = false;
        let globalItemNumber = 0;
        let joOtherItems = [];

        function showFloatingAlert(type, message) {
            const $alert = $('#floatingBadgeAlert'),
                $icon = $('#alertIcon');
            $alert.removeClass('alert-saving alert-success alert-error hiding');
            if (type === 'saving') {
                $alert.addClass('alert-saving');
                $icon.attr('class', 'fas fa-circle-notch fa-spin');
            } else if (type === 'success') {
                $alert.addClass('alert-success');
                $icon.attr('class', 'fas fa-check-circle');
            } else {
                $alert.addClass('alert-error');
                $icon.attr('class', 'fas fa-exclamation-circle');
            }
            $('#alertText').text(message);
            $alert.addClass('show');
            if (type !== 'saving') setTimeout(hideFloatingAlert, 3000);
        }

        function hideFloatingAlert() {
            $('#floatingBadgeAlert').addClass('hiding');
            setTimeout(() => $('#floatingBadgeAlert').removeClass('show hiding'), 400);
        }

        function formatNumber(amount) {
            return parseFloat(amount || 0).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function parseRupiah(value) {
            if (!value) return 0;
            return parseFloat(value.toString().replace(/\./g, '').replace(',', '.')) || 0;
        }

        function formatRupiah(value) {
            let number = value.toString().replace(/[^\d,]/g, '').replace(/\./g, '');
            if (number === '') return '';
            let parts = number.split(','),
                intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.'),
                dec = parts.length > 1 ? parts[1].substring(0, 2) : '';
            return parts.length > 1 ? intPart + ',' + dec : intPart + ',00';
        }

        function setupRupiahInput(input) {
            input.addEventListener('input', function() {
                let cursor = this.selectionStart,
                    before = this.value.substring(0, cursor);
                this.value = formatRupiah(this.value);
                let digits = before.replace(/\D/g, '').length,
                    newPos = 0,
                    count = 0;
                for (let i = 0; i < this.value.length; i++) {
                    if (/\d/.test(this.value[i])) {
                        count++;
                        if (count === digits) {
                            newPos = i + 1;
                            break;
                        }
                    }
                }
                this.setSelectionRange(newPos, newPos);
            });
            input.addEventListener('blur', function() {
                if (this.value && !this.value.includes(',')) this.value += ',00';
            });
        }

        $(document).ready(function() {
            setupRupiahInput(document.getElementById('input_nilai_kasbon'));
            $('.select2-field').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            $('#itemsCard').addClass('items-card-disabled');

            $('#id_jo_other').on('change', function() {
                const id = $(this).val(),
                    $opt = $(this).find('option:selected');
                if (!id) {
                    joOtherItems = [];
                    $('#input_jo_other_item').html('<option value="">-- Select JO Other first --</option>');
                    $('#joSummaryWrapper').hide();
                    return;
                }
                const no = $opt.data('no') || id;
                $('#joSummaryNo').text(no);
                $('#joSummaryNo2').text(no);
                $('#joSummaryTitle').text($opt.data('title') || '—');
                $('#joSummaryCustomer').text($opt.data('customer') || '—');
                $('#joSummaryPort').text($opt.data('port') || '—');
                $('#joSummaryNote').text($opt.data('note') || '—');
                const rawDate = $opt.data('tgl') || '';
                $('#joSummaryDate').text(rawDate ? new Date(rawDate).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) : '—');
                $('#joSummaryItemCount').html(
                    '<i class="fas fa-circle-notch fa-spin" style="color:#10b981; font-size:.8rem;"></i>'
                );
                $('#joSummaryWrapper').show();
                loadJoOtherItems(id);
            });

            $('#input_jo_other_item').on('change', function() {
                const itemId = $(this).val();
                if (!itemId) {
                    $('#input_nilai_hpp').val('');
                    $('#hppHint').html('');
                    return;
                }
                const item = joOtherItems.find(i => i.id_jo_other_item == itemId);
                if (item) {
                    const hpp = parseFloat(item.hpp_ops) || 0;
                    $('#input_nilai_hpp').val(formatNumber(hpp));
                    $('#hppHint').html(
                        `<i class="fas fa-info-circle me-1"></i>Ops HPP: <strong>IDR ${formatNumber(hpp)}</strong>`
                    );
                }
            });

            $(document).on('click', '#btnJoDetail', function() {
                const id = $('#id_jo_other').val();
                if (!id) return;
                const $opt = $('#id_jo_other').find('option:selected');
                $('#joItemsModalSubtitle').text(($opt.data('no') || id) + ' — ' + ($opt.data('title') ||
                    ''));
                $('#joItemsModalLoading').show();
                $('#joItemsModalContent').hide();
                $('#joItemsModalEmpty').hide();
                $('#joItemsModal').modal('show');
                if (joOtherItems.length > 0) {
                    renderJoItemsModal(joOtherItems);
                } else {
                    $.ajax({
                        url: '{{ route('kasbon-other.jo-items.get') }}',
                        method: 'GET',
                        data: {
                            id_jo_other: id
                        },
                        success: function(r) {
                            renderJoItemsModal(r.success ? r.data : []);
                        },
                        error: function() {
                            renderJoItemsModal([]);
                        }
                    });
                }
            });
        });

        function loadJoOtherItems(idJoOther) {
            $.ajax({
                url: '{{ route('kasbon-other.jo-items.get') }}',
                method: 'GET',
                data: {
                    id_jo_other: idJoOther
                },
                success: function(r) {
                    if (r.success) {
                        joOtherItems = r.data;
                        let opts = '<option value="">-- Select Item --</option>';
                        r.data.forEach(item => {
                            opts +=
                                `<option value="${item.id_jo_other_item}">${item.invoice_typ} (${item.invoice_ctg})</option>`;
                        });
                        $('#input_jo_other_item').html(opts);
                        const n = r.data.length;
                        $('#joSummaryItemCount').text(n + ' item' + (n !== 1 ? 's' : ''));
                    } else {
                        $('#joSummaryItemCount').text('—');
                    }
                },
                error: function() {
                    showFloatingAlert('error', 'Failed to load JO Other items');
                    $('#joSummaryItemCount').text('—');
                }
            });
        }

        function renderJoItemsModal(items) {
            $('#joItemsModalLoading').hide();
            if (!items || !items.length) {
                $('#joItemsModalEmpty').show();
                return;
            }
            let rows = '',
                totalHpp = 0,
                totalSell = 0;
            items.forEach(function(item, idx) {
                const hpp = parseFloat(item.hpp_ops || 0),
                    sell = parseFloat(item.hargajual_idr || 0);
                totalHpp += hpp;
                totalSell += sell;
                rows += `<tr><td style="text-align:center;">${idx+1}</td><td style="font-weight:600;">${item.invoice_typ || item.id_jo_other_item}</td>
            <td><span style="background:#d1fae5; color:#065f46; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600;">${item.invoice_ctg || '—'}</span></td>
            <td style="text-align:right; font-family:monospace;">${formatNumber(hpp)}</td>
            <td style="text-align:right; font-family:monospace;">${formatNumber(sell)}</td></tr>`;
            });
            $('#joItemsModalBody').html(rows);
            $('#joItemsTotalHpp').text(formatNumber(totalHpp));
            $('#joItemsTotalSell').text(formatNumber(totalSell));
            $('#joItemsModalCount').text(items.length + ' item' + (items.length !== 1 ? 's' : '') + ' found');
            $('#joItemsModalContent').show();
        }

        $('#btnSaveHeader').on('click', function() {
            const idJoOther = $('#id_jo_other').val();
            const idDep = $('#id_md_dep').val();
            const idCabang = $('#id_md_cabang').val();
            const idRelease = $('#id_md_release').val();
            const tglKasbon = $('#tgl_kasbon').val();
            const priority = $('#priority').val();
            const dueDate = $('#due_date').val();

            if (!idJoOther || !idDep || !idCabang || !idRelease || !tglKasbon) {
                showFloatingAlert('error', 'Please fill in all required fields');
                return;
            }

            showFloatingAlert('saving', 'Saving header...');
            $('#btnSaveHeader').prop('disabled', true);

            $.ajax({
                url: '{{ route('kasbon-other.header.store') }}',
                method: 'POST',
                data: {
                    id_jo_other: idJoOther,
                    id_md_dep: idDep,
                    id_md_cabang: idCabang,
                    id_md_release: idRelease,
                    tgl_kasbon: tglKasbon,
                    tgl_release: $('#tgl_release').val(),
                    note: $('#note').val(),
                    priority: priority,
                    due_date: dueDate,
                    _token: $('input[name="_token"]').val(),
                },
                success: function(r) {
                    $('#btnSaveHeader').prop('disabled', false);
                    if (r.success) {
                        currentKasbonOtherId = r.data.id;
                        currentKasbonOtherStr = r.data.id_kasbon_other;
                        isHeaderSaved = true;
                        showFloatingAlert('success', 'Header saved! Redirecting...');
                        setTimeout(() => {
                            window.location.href = r.redirect_url;
                        }, 1000);
                    } else {
                        showFloatingAlert('error', r.message || 'Failed to save header');
                    }
                },
                error: function(xhr) {
                    $('#btnSaveHeader').prop('disabled', false);
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to save header');
                }
            });
        });

        $('#btnAddToTable').on('click', function() {
            if (!isHeaderSaved) {
                showFloatingAlert('error', 'Please save the header first');
                return;
            }
            const itemId = $('#input_jo_other_item').val(),
                nilaiHpp = parseRupiah($('#input_nilai_hpp').val()),
                nilaiKasbon = parseRupiah($('#input_nilai_kasbon').val());
            if (!itemId) {
                showFloatingAlert('error', 'Please select a JO Other Item');
                return;
            }
            if (!nilaiKasbon || nilaiKasbon <= 0) {
                showFloatingAlert('error', 'Please enter a CA Amount');
                return;
            }
            if (nilaiKasbon > nilaiHpp) {
                showFloatingAlert('error', 'CA Amount cannot exceed HPP value');
                return;
            }
            showFloatingAlert('saving', 'Adding item...');
            $.ajax({
                url: '{{ route('kasbon-other.item.store') }}',
                method: 'POST',
                data: {
                    id_kasbon_other: currentKasbonOtherStr,
                    id_jo_other_item: itemId,
                    nilai_kasbon: nilaiKasbon,
                    _token: $('input[name="_token"]').val()
                },
                success: function(r) {
                    if (r.success) {
                        showFloatingAlert('success', 'Item added successfully!');
                        appendItemRow(r.data);
                        clearItemForm();
                        updateGrandTotal();
                    } else {
                        showFloatingAlert('error', r.message || 'Failed to add item');
                    }
                },
                error: function(xhr) {
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to add item');
                }
            });
        });

        function appendItemRow(d) {
            $('.no-items-row').remove();
            globalItemNumber++;
            const joItem = joOtherItems.find(i => i.id_jo_other_item == d.id_jo_other_item) || {};
            $('#itemsTableBody').append(`
    <tr class="item-row" data-item-id="${d.id_kasbon_other_item}">
        <td class="item-number-cell">${globalItemNumber}</td>
        <td class="text-start">${joItem.invoice_typ || d.id_jo_other_item}</td>
        <td>${joItem.invoice_ctg || '-'}</td>
        <td>${formatNumber(d.nilai_hpp_other_item)}</td>
        <td>${formatNumber(d.nilai_kasbon)}</td>
        <td>${formatNumber(d.total_kasbon)}</td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-row" onclick="removeItem(this, '${d.id_kasbon_other_item}')"><i class="fas fa-trash"></i></button></td>
    </tr>`);
        }

        function clearItemForm() {
            $('#input_jo_other_item').val('').trigger('change');
            $('#input_nilai_hpp').val('');
            $('#input_nilai_kasbon').val('');
            $('#hppHint').html('');
        }

        function removeItem(button, itemId) {
            if (!confirm('Are you sure you want to delete this item?')) return;
            showFloatingAlert('saving', 'Deleting item...');
            $.ajax({
                url: `/kasbon-other/item/destroy/${itemId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(r) {
                    if (r.success) {
                        showFloatingAlert('success', 'Item deleted!');
                        $(button).closest('tr').remove();
                        renumberAllItems();
                        updateGrandTotal();
                        if ($('#itemsTableBody tr.item-row').length === 0) {
                            $('#itemsTableBody').html(
                                `<tr class="no-items-row"><td colspan="7"><i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i><p class="mb-0 fw-bold">No items yet</p></td></tr>`
                            );
                        }
                    }
                },
                error: function(xhr) {
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to delete item');
                }
            });
        }

        $('#resetAllBtn').on('click', function() {
            if (!confirm('Delete all items? This cannot be undone.')) return;
            const ids = [];
            $('.item-row').each(function() {
                ids.push($(this).data('item-id'));
            });
            if (!ids.length) {
                showFloatingAlert('error', 'No items to delete');
                return;
            }
            showFloatingAlert('saving', 'Deleting all items...');
            Promise.all(ids.map(id => $.ajax({
                    url: `/kasbon-other/item/destroy/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })))
                .then(() => {
                    showFloatingAlert('success', 'All items deleted!');
                    $('#itemsTableBody').html(
                        `<tr class="no-items-row"><td colspan="7"><i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i><p class="mb-0 fw-bold">No items yet</p></td></tr>`
                    );
                    globalItemNumber = 0;
                    updateGrandTotal();
                }).catch(() => {
                    showFloatingAlert('error', 'Some items could not be deleted');
                });
        });

        function renumberAllItems() {
            $('.item-row').each(function(i) {
                $(this).find('.item-number-cell').text(i + 1);
            });
            globalItemNumber = $('.item-row').length;
        }

        function updateGrandTotal() {
            let totalHPP = 0,
                totalKasbon = 0,
                totalRemaining = 0;
            $('.item-row').each(function() {
                const tds = $(this).find('td');
                totalHPP += parseRupiah(tds.eq(3).text());
                totalKasbon += parseRupiah(tds.eq(4).text());
                totalRemaining += parseRupiah(tds.eq(5).text());
            });
            $('#footerTotalHPP').text(formatNumber(totalHPP));
            $('#footerTotalKasbon').text(formatNumber(totalKasbon));
            $('#footerTotalRemaining').text(formatNumber(totalRemaining));
        }
    </script>
@endpush
