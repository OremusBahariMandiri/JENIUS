@extends('layouts.app')

@section('title', 'Edit Cash Advance General')

@push('styles')
    <style>
        .kasbonGenEditPage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .kasbonGenEditPage .card-header {
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

        .confirm-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px);
        }

        .confirm-modal-overlay.show {
            display: flex;
        }

        .confirm-modal-box {
            background: white;
            border-radius: 16px;
            padding: 32px;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .confirm-modal-box .modal-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 16px;
        }

        .confirm-modal-box .modal-icon.danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .confirm-modal-box h5 {
            text-align: center;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .confirm-modal-box p {
            text-align: center;
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 24px;
        }

        .confirm-modal-box .modal-actions {
            display: flex;
            gap: 10px;
        }

        .confirm-modal-box .modal-actions .btn {
            flex: 1;
            padding: 10px;
            font-weight: 600;
        }

        /* ADD ITEM FORM - default (green dashed) */
        .add-item-form-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px dashed #10b981;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            transition: all 0.3s;
        }

        .add-item-form-section.edit-mode {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border: 2px solid #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
        }

        .add-item-form-section h6 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .add-item-form-section.edit-mode h6 {
            color: #1e40af;
        }

        .add-item-form-section h6 i {
            color: #10b981;
            font-size: 1.2rem;
        }

        .add-item-form-section.edit-mode h6 i {
            color: #3b82f6;
        }

        .add-item-form-section .form-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.875rem;
        }

        .currency-group {
            display: flex;
            align-items: stretch;
        }

        .currency-group .currency-label {
            background-color: #2c3e50;
            color: white;
            padding: 0 14px;
            font-size: 0.7rem;
            border-radius: 8px 0 0 8px;
            min-width: 52px;
            text-align: center;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .currency-group .currency-input {
            border-radius: 0 8px 8px 0 !important;
            border-left: none !important;
            font-size: 1.05rem;
            font-weight: 600;
        }

        .btn-add-to-table {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            transition: all 0.3s;
        }

        .btn-add-to-table:hover {
            transform: translateY(-3px);
            color: white;
        }

        .btn-cancel-edit-style {
            background: linear-gradient(135deg, #bbbbbb, #5c5c5c);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-cancel-edit-style:hover {
            transform: translateY(-3px);
            color: white;
        }

        /* TABLE */
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
            background-color: #fff;
        }

        .table-items tbody tr:hover td {
            background-color: #f0fdf4;
        }

        .table-items tbody tr.tr-active td {
            background-color: #dbeafe !important;
        }

        .table-items .category-cell {
            background: #e8f5e9 !important;
            color: #2c3e50;
            font-weight: 600;
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
            gap: 6px;
        }

        .currency-label-footer {
            background-color: #2C3E50;
            color: white;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: .78rem;
            font-weight: 700;
            min-width: 44px;
            text-align: center;
        }

        .footer-currency-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* ✅ IDR kiri, nominal kanan */
            width: 100%;
        }

        .footer-currency-label {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            padding: 4px 10px;
            border-radius: 5px;
            font-size: .78rem;
            font-weight: 700;
            min-width: 44px;
            text-align: center;
            flex-shrink: 0;
            /* ✅ label tidak menyusut */
        }

        .footer-value {
            color: #2C3E50;
            font-weight: 700;
            font-size: .95rem;
            text-align: right;
            flex: 1;
            /* ✅ ambil sisa ruang */
        }

        .btn-edit-row {
            background-color: #3b82f6;
            border-color: #3b82f6;
            padding: .3rem .6rem;
            font-size: .8rem;
            border-radius: 6px;
            transition: all .2s;
        }

        .btn-edit-row:hover {
            background-color: #2563eb;
            transform: scale(1.08);
        }

        .btn-remove-row {
            padding: .3rem .6rem;
            font-size: .8rem;
            border-radius: 6px;
            transition: all .2s;
        }

        .btn-remove-row:hover {
            transform: scale(1.08);
        }

        .no-items-row td {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
            background: #f8f9fa !important;
        }

        .items-count-badge {
            background: #10b981;
            color: white;
            font-size: .75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 8px;
        }

        .final-save-section {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 18px 20px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, .1);
            border-radius: 12px 12px 0 0;
            margin-top: 30px;
            z-index: 100;
        }

        .btn-final-back {
            background: linear-gradient(135deg, #868686, #5e5e5e);
            border: none;
            color: white;
            padding: 13px 35px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            transition: all .3s;
        }

        .btn-final-back:hover {
            transform: translateY(-2px);
            color: white;
        }
    </style>
@endpush

@section('content')

    <div id="floatingBadgeAlert" class="floating-badge-alert">
        <i class="fas fa-circle-notch fa-spin" id="alertIcon"></i>
        <div class="alert-text" id="alertText">Processing...</div>
    </div>

    <div class="confirm-modal-overlay" id="confirmModal">
        <div class="confirm-modal-box">
            <div class="modal-icon danger"><i class="fas fa-exclamation-triangle"></i></div>
            <h5 id="confirmModalTitle">Are you sure?</h5>
            <p id="confirmModalDesc">This action cannot be undone.</p>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline-secondary" id="confirmModalCancel">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmModalOk">Yes, proceed</button>
            </div>
        </div>
    </div>

    <div class="container-fluid kasbonGenEditPage">
        <div class="row">
            <div class="col-lg-12">

                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Cash Advance General</span>
                        <a href="{{ route('kasbon-gen.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <input type="hidden" id="current_kasbon_gen_id" value="{{ $kasbonGen->id }}">
                <input type="hidden" id="current_kasbon_gen_str" value="{{ $kasbonGen->id_kasbon_gen }}">

                <form id="kasbonGenForm">
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
                                        <input type="text" class="form-control fw-bold"
                                            value="{{ $kasbonGen->id_kasbon_gen }}" readonly
                                            style="background-color:#e9ecef; color:#2c3e50; letter-spacing:1px;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Cash Advance Date</label>
                                    <input type="date" name="tgl_kasbon" id="tgl_kasbon" class="form-control"
                                        value="{{ $kasbonGen->tgl_kasbon ? $kasbonGen->tgl_kasbon->format('Y-m-d') : '' }}"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Department</label>
                                    <select name="id_md_dep" id="id_md_dep" class="form-select select2-field" required>
                                        <option value="">-- Select Department --</option>
                                        @foreach ($departemens as $dep)
                                            <option value="{{ $dep->id_md_dep }}"
                                                {{ $kasbonGen->id_md_dep == $dep->id_md_dep ? 'selected' : '' }}>
                                                {{ $dep->nama_dep }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Branch</label>
                                    <select name="id_md_cabang" id="id_md_cabang" class="form-select select2-field"
                                        required>
                                        <option value="">-- Select Branch --</option>
                                        @foreach ($cabangs as $cabang)
                                            <option value="{{ $cabang->id_md_branch }}"
                                                {{ $kasbonGen->id_md_cabang == $cabang->id_md_branch ? 'selected' : '' }}>
                                                {{ $cabang->nama_branch }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required-field">Release To</label>
                                    <select name="id_md_release" id="id_md_release" class="form-select select2-field"
                                        required>
                                        <option value="">-- Select Release To --</option>
                                        @foreach ($releases as $rel)
                                            <option value="{{ $rel->id_md_release }}"
                                                {{ $kasbonGen->id_md_release == $rel->id_md_release ? 'selected' : '' }}>
                                                {{ $rel->nama_release ?? $rel->id_md_release }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Release Date</label>
                                    <input type="date" name="tgl_release" id="tgl_release" class="form-control"
                                        value="{{ $kasbonGen->tgl_release ? $kasbonGen->tgl_release->format('Y-m-d') : '' }}">
                                </div>

                                {{-- Priority --}}
                                <div class="col-md-6">
                                    <label class="form-label required-field">Priority</label>
                                    <select name="priority" id="priority" class="form-select" required>
                                        <option value="normal" {{ $kasbonGen->priority == 'normal' ? 'selected' : '' }}>
                                            Normal (Low)</option>
                                        <option value="high" {{ $kasbonGen->priority == 'high' ? 'selected' : '' }}>
                                            High</option>
                                        <option value="urgent" {{ $kasbonGen->priority == 'urgent' ? 'selected' : '' }}>
                                            Urgent</option>
                                    </select>
                                </div>

                                {{-- Due Date --}}
                                <div class="col-md-6">
                                    <label class="form-label">Due Date & Time</label>
                                    <input type="datetime-local" name="due_date" id="due_date" class="form-control"
                                        value="{{ $kasbonGen->due_date ? $kasbonGen->due_date->format('Y-m-d\TH:i') : '' }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Note</label>
                                    <textarea name="note" id="note" class="form-control" rows="3">{{ $kasbonGen->note }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-success px-4" id="btnSaveHeader">
                                    <i class="fas fa-save me-1"></i> Update Header
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ITEMS CARD --}}
                    <div class="card shadow mb-4" id="itemsCard">
                        <div class="card-header text-black d-flex justify-content-between align-items-center"
                            style="background-color: #d1fae5">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>Cash Advance Items
                                <span class="items-count-badge"
                                    id="itemsCountBadge">{{ $kasbonGen->items->count() }}</span>
                            </h6>

                        </div>
                        <div class="card-body p-4">

                            {{-- ADD / EDIT ITEM FORM --}}
                            <div class="add-item-form-section" id="addItemFormSection">
                                <h6 id="formSectionTitle"><i class="fas fa-plus-square"></i> Add New Item</h6>
                                <input type="hidden" id="editing_item_id" value="">

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label required-field">Category</label>
                                        <select id="input_category" class="form-select select2-item"
                                            data-placeholder="Select Category">
                                            <option value=""></option>
                                            @foreach ($invoices->groupBy('invoice_ctg') as $category => $invoiceGroup)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label required-field">Item</label>
                                        <select id="input_item" class="form-select select2-item" disabled
                                            data-placeholder="Select category first">
                                            <option value=""></option>
                                        </select>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label required-field">CA Amount (IDR)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_nilai_kasbon"
                                                class="form-control currency-input" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-add-to-table" id="btnAddToTable">
                                            <i class="fas fa-arrow-down me-1"></i> Add to Table
                                        </button>
                                        <button type="button" class="btn btn-cancel-edit-style" id="btnCancelEdit"
                                            style="display:none;">
                                            <i class="fas fa-times me-1"></i> Cancel Edit
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- TABLE --}}
                            <div class="table-responsive">
                                <table class="table table-items table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:5%;">No</th>
                                            <th style="width:25%;">Category</th>
                                            <th>Item</th>
                                            <th style="width:20%;">CA Amount (IDR)</th>
                                            <th style="width:10%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        @if ($kasbonGen->items->count() > 0)
                                            @php
                                                $groupedItems = $kasbonGen->items->groupBy(
                                                    fn($i) => $i->invoice->invoice_ctg ?? '-',
                                                );
                                                $globalIndex = 1;
                                            @endphp
                                            @foreach ($groupedItems as $category => $items)
                                                @foreach ($items as $index => $item)
                                                    <tr class="item-row" id="row_item_{{ $item->id_kasbon_gen_item }}"
                                                        data-item-id="{{ $item->id_kasbon_gen_item }}"
                                                        data-invoice-id="{{ $item->id_md_invoice }}"
                                                        data-category="{{ $category }}">
                                                        <td class="text-center fw-bold text-muted">{{ $globalIndex++ }}
                                                        </td>
                                                        @if ($index === 0)
                                                            <td class="category-cell text-start"
                                                                rowspan="{{ $items->count() }}">
                                                                {{ $category }}
                                                            </td>
                                                        @endif
                                                        <td class="text-start item-type-cell">
                                                            {{ $item->invoice->invoice_typ ?? '-' }}</td>
                                                        <td class="text-end nilai-kasbon-cell">
                                                            {{ number_format($item->nilai_kasbon, 2, ',', '.') }}</td>
                                                        <td class="text-center">
                                                            <div class="d-flex gap-1 justify-content-center">
                                                                <button type="button"
                                                                    class="btn btn-primary btn-sm btn-edit-row"
                                                                    onclick="editItem('{{ $item->id_kasbon_gen_item }}')"
                                                                    title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm btn-remove-row"
                                                                    onclick="removeItem(this,'{{ $item->id_kasbon_gen_item }}')"
                                                                    title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @else
                                            <tr class="no-items-row">
                                                <td colspan="5">
                                                    <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                                                    <p class="mb-0 fw-bold">No items yet</p>
                                                    <small class="text-muted">Add items using the form above</small>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot class="table-footer">
                                        <tr>
                                            <td colspan="3" class="text-center pe-3"><strong>GRAND TOTAL</strong></td>
                                            <td>
                                                <div class="currency-group-footer">
                                                    <span class="currency-label-footer">IDR</span>
                                                    <span class="footer-value" id="footerTotalKasbon">
                                                        {{ number_format($kasbonGen->items->sum('nilai_kasbon'), 2, ',', '.') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="button" class="btn btn-sm btn-danger" id="btnResetAllItems"
                                        style="border-radius:8px;">
                                        <i class="fas fa-trash-alt me-1"></i> Reset All Items
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="final-save-section">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('kasbon-gen.index') }}" class="btn btn-final-back">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
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
        const currentKasbonGenId = {{ $kasbonGen->id }};
        const currentKasbonGenStr = '{{ $kasbonGen->id_kasbon_gen }}';
        const updateHeaderUrl = '/kasbon-gen/header/update/{{ $kasbonGen->id }}';
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        let confirmCallback = null;
        let globalItemNumber = {{ $kasbonGen->items->count() }};

        const invoicesByCategory = {
            @foreach ($invoices->groupBy('invoice_ctg') as $category => $invoiceGroup)
                '{{ $category }}': [
                    @foreach ($invoiceGroup as $invoice)
                        {
                            id: '{{ $invoice->id_md_invoice }}',
                            type: '{{ $invoice->invoice_typ }}'
                        },
                    @endforeach
                ],
            @endforeach
        };

        // CONFIRM MODAL
        function showConfirm({
            title,
            desc,
            okLabel = 'Yes, proceed',
            okClass = 'btn-danger'
        }, callback) {
            $('#confirmModalTitle').text(title);
            $('#confirmModalDesc').text(desc);
            $('#confirmModalOk').text(okLabel).removeClass().addClass(`btn ${okClass}`);
            confirmCallback = callback;
            $('#confirmModal').addClass('show');
        }
        $('#confirmModalCancel, #confirmModal').on('click', function(e) {
            if (e.target === this) {
                $('#confirmModal').removeClass('show');
                confirmCallback = null;
            }
        });
        $('#confirmModalOk').on('click', function() {
            $('#confirmModal').removeClass('show');
            if (typeof confirmCallback === 'function') confirmCallback();
            confirmCallback = null;
        });

        // FLOATING ALERT
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

        // UTILS
        function formatNumber(amount) {
            return parseFloat(amount || 0).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function parseRupiah(value) {
            if (!value && value !== 0) return 0;
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
            $('.select2-item').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            updateGrandTotal();

            // Category change
            $('#input_category').on('change', function() {
                const category = $(this).val();
                if (!category) {
                    $('#input_item').prop('disabled', true).html('<option value=""></option>').trigger(
                        'change');
                    return;
                }
                const invoices = invoicesByCategory[category] || [];
                let opts = '<option value=""></option>';
                invoices.forEach(inv => {
                    opts += `<option value="${inv.id}">${inv.type}</option>`;
                });
                $('#input_item').prop('disabled', false).html(opts).trigger('change');
            });
        });

        // UPDATE HEADER
        $('#btnSaveHeader').on('click', function() {
            const idDep = $('#id_md_dep').val();
            const idCabang = $('#id_md_cabang').val();
            const idRelease = $('#id_md_release').val();
            const tglKasbon = $('#tgl_kasbon').val();
            const priority = $('#priority').val();
            const dueDate = $('#due_date').val();

            if (!idDep || !idCabang || !idRelease || !tglKasbon) {
                showFloatingAlert('error', 'Please fill in all required fields');
                return;
            }

            showFloatingAlert('saving', 'Updating header...');
            $('#btnSaveHeader').prop('disabled', true);

            $.ajax({
                url: updateHeaderUrl,
                method: 'POST',
                data: {
                    id_md_dep: idDep,
                    id_md_cabang: idCabang,
                    id_md_release: idRelease,
                    tgl_kasbon: tglKasbon,
                    tgl_release: $('#tgl_release').val(),
                    note: $('#note').val(),
                    priority: priority,
                    due_date: dueDate,
                    _token: csrfToken,
                },
                success: function(r) {
                    $('#btnSaveHeader').prop('disabled', false);
                    if (r.success) {
                        showFloatingAlert('success', 'Header updated successfully!');
                    } else {
                        showFloatingAlert('error', r.message || 'Failed to update header');
                    }
                },
                error: function(xhr) {
                    $('#btnSaveHeader').prop('disabled', false);
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to update header');
                }
            });
        });

        // ADD / UPDATE ITEM
        $('#btnAddToTable').on('click', function() {
            const editingId = $('#editing_item_id').val();
            const category = $('#input_category').val();
            const itemId = $('#input_item').val();
            const itemText = $('#input_item option:selected').text();
            const nilaiKasbon = parseRupiah($('#input_nilai_kasbon').val());

            if (!category) {
                showFloatingAlert('error', 'Please select a category');
                return;
            }
            if (!itemId) {
                showFloatingAlert('error', 'Please select an item');
                return;
            }
            if (nilaiKasbon <= 0) {
                showFloatingAlert('error', 'Please enter CA Amount');
                return;
            }

            if (editingId) {
                updateItemToDatabase(editingId, itemId, category, itemText, nilaiKasbon);
            } else {
                storeItemToDatabase(itemId, category, itemText, nilaiKasbon);
            }
        });

        function storeItemToDatabase(invoiceId, category, itemText, nilaiKasbon) {
            showFloatingAlert('saving', 'Adding item...');
            $.ajax({
                url: '{{ route('kasbon-gen.item.store') }}',
                method: 'POST',
                data: {
                    id_kasbon_gen: currentKasbonGenStr,
                    id_md_invoice: invoiceId,
                    nilai_kasbon: nilaiKasbon,
                    _token: csrfToken
                },
                success: function(r) {
                    if (r.success) {
                        showFloatingAlert('success', 'Item added!');
                        $('.no-items-row').remove();
                        globalItemNumber++;
                        $('#itemsTableBody').append(`
                <tr class="item-row" id="row_item_${r.data.id_kasbon_gen_item}"
                    data-item-id="${r.data.id_kasbon_gen_item}" data-invoice-id="${invoiceId}" data-category="${category}">
                    <td class="text-center fw-bold text-muted">${globalItemNumber}</td>
                    <td class="category-cell text-start">${category}</td>
                    <td class="text-start item-type-cell">${itemText}</td>
                    <td class="text-end nilai-kasbon-cell">${formatNumber(r.data.nilai_kasbon)}</td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <button type="button" class="btn btn-primary btn-sm btn-edit-row" onclick="editItem('${r.data.id_kasbon_gen_item}')"><i class="fas fa-edit"></i></button>
                            <button type="button" class="btn btn-danger btn-sm btn-remove-row" onclick="removeItem(this,'${r.data.id_kasbon_gen_item}')"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>`);
                        $('#itemsCountBadge').text($('.item-row').length);
                        updateGrandTotal();
                        clearItemForm();
                    } else {
                        showFloatingAlert('error', r.message || 'Failed to add item');
                    }
                },
                error: function(xhr) {
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to add item');
                }
            });
        }

        function updateItemToDatabase(itemId, invoiceId, category, itemText, nilaiKasbon) {
            showFloatingAlert('saving', 'Updating item...');
            $.ajax({
                url: `/kasbon-gen/item/update/${itemId}`,
                method: 'PUT',
                data: {
                    id_md_invoice: invoiceId,
                    nilai_kasbon: nilaiKasbon,
                    _token: csrfToken
                },
                success: function(r) {
                    if (r.success) {
                        showFloatingAlert('success', 'Item updated!');
                        const $row = $(`#row_item_${itemId}`);
                        $row.find('.category-cell').text(category);
                        $row.find('.item-type-cell').text(itemText);
                        $row.find('.nilai-kasbon-cell').text(formatNumber(r.data.nilai_kasbon));
                        $row.attr('data-invoice-id', invoiceId).attr('data-category', category);
                        $row.removeClass('tr-active');
                        updateGrandTotal();
                        clearItemForm();
                    } else {
                        showFloatingAlert('error', r.message || 'Failed to update item');
                    }
                },
                error: function(xhr) {
                    showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to update item');
                }
            });
        }

        // EDIT ITEM — load to form
        function editItem(itemId) {
            showFloatingAlert('saving', 'Loading item data...');
            $.ajax({
                url: `/kasbon-gen/item/show/${itemId}`,
                method: 'GET',
                success: function(r) {
                    if (!r.success) {
                        showFloatingAlert('error', 'Failed to load item data');
                        return;
                    }
                    hideFloatingAlert();
                    const item = r.data;
                    $('#editing_item_id').val(itemId);
                    $('#formSectionTitle').html('<i class="fas fa-edit"></i> Edit Item');
                    $('#addItemFormSection').addClass('edit-mode');
                    $('#btnAddToTable').html('<i class="fas fa-save me-1"></i> Update Item');
                    $('#btnCancelEdit').show();

                    $('#input_category').val(item.invoice_ctg).trigger('change');
                    setTimeout(() => {
                        $('#input_item').val(item.id_md_invoice).trigger('change.select2');
                        $('#input_nilai_kasbon').val(formatNumber(item.nilai_kasbon).replace(/\./g, ',')
                            .replace(/,(\d{2})$/, ',$1'));
                        // Re-format properly
                        const n = parseFloat(item.nilai_kasbon) || 0;
                        $('#input_nilai_kasbon').val(n.toLocaleString('id-ID', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).replace(/\./g, '.').replace(',', ','));
                    }, 300);

                    $(`#row_item_${itemId}`).addClass('tr-active');
                    $('html, body').animate({
                        scrollTop: $('#addItemFormSection').offset().top - 120
                    }, 400);
                },
                error: function() {
                    showFloatingAlert('error', 'Failed to load item data');
                }
            });
        }

        $('#btnCancelEdit').on('click', function() {
            clearItemForm();
        });

        // REMOVE ITEM
        function removeItem(button, itemId) {
            showConfirm({
                title: 'Delete Item?',
                desc: 'This item will be permanently deleted.',
                okLabel: 'Yes, delete',
                okClass: 'btn-danger'
            }, function() {
                showFloatingAlert('saving', 'Deleting item...');
                $.ajax({
                    url: `/kasbon-gen/item/destroy/${itemId}`,
                    method: 'DELETE',
                    data: {
                        _token: csrfToken
                    },
                    success: function(r) {
                        if (r.success) {
                            showFloatingAlert('success', 'Item deleted!');
                            $(`#row_item_${itemId}`).remove();
                            globalItemNumber--;
                            renumberAllItems();
                            updateGrandTotal();
                            $('#itemsCountBadge').text($('.item-row').length);
                            if ($('.item-row').length === 0) {
                                $('#itemsTableBody').html(
                                    `<tr class="no-items-row"><td colspan="5"><i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i><p class="mb-0 fw-bold">No items yet</p></td></tr>`
                                );
                            }
                            if ($('#editing_item_id').val() == itemId) clearItemForm();
                        } else {
                            showFloatingAlert('error', r.message || 'Failed to delete item');
                        }
                    },
                    error: function(xhr) {
                        showFloatingAlert('error', xhr.responseJSON?.message ||
                            'Failed to delete item');
                    }
                });
            });
        }

        // RESET ALL
        $('#btnResetAllItems').on('click', function() {
            const count = $('.item-row').length;
            if (count === 0) {
                showFloatingAlert('error', 'No items to delete');
                return;
            }
            showConfirm({
                title: 'Reset All Items?',
                desc: `All ${count} item(s) will be permanently deleted.`,
                okLabel: 'Yes, reset all',
                okClass: 'btn-danger'
            }, function() {
                showFloatingAlert('saving', 'Deleting all items...');
                const ids = [];
                $('.item-row').each(function() {
                    ids.push($(this).data('item-id'));
                });
                Promise.all(ids.map(id => $.ajax({
                        url: `/kasbon-gen/item/destroy/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: csrfToken
                        }
                    })))
                    .then(() => {
                        showFloatingAlert('success', 'All items deleted!');
                        $('#itemsTableBody').html(
                            `<tr class="no-items-row"><td colspan="5"><i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i><p class="mb-0 fw-bold">No items yet</p></td></tr>`
                        );
                        globalItemNumber = 0;
                        $('#itemsCountBadge').text(0);
                        updateGrandTotal();
                        clearItemForm();
                    }).catch(() => {
                        showFloatingAlert('error', 'Some items could not be deleted');
                    });
            });
        });

        // UTILS
        function clearItemForm() {
            $('#editing_item_id').val('');
            $('#formSectionTitle').html('<i class="fas fa-plus-square"></i> Add New Item');
            $('#addItemFormSection').removeClass('edit-mode');
            $('#btnAddToTable').html('<i class="fas fa-arrow-down me-1"></i> Add to Table');
            $('#btnCancelEdit').hide();
            $('#input_category').val(null).trigger('change');
            $('#input_item').prop('disabled', true).html('<option value=""></option>').trigger('change');
            $('#input_nilai_kasbon').val('');
            $('.item-row').removeClass('tr-active');
        }

        function renumberAllItems() {
            $('.item-row').each(function(i) {
                $(this).find('td:first').text(i + 1);
            });
            globalItemNumber = $('.item-row').length;
        }

        function updateGrandTotal() {
            let total = 0;
            $('.item-row').each(function() {
                total += parseRupiah($(this).find('.nilai-kasbon-cell').text());
            });
            $('#footerTotalKasbon').text(formatNumber(total));
        }
    </script>
@endpush
