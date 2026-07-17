@extends('layouts.app')

@section('title', 'Add Cash Advance General')

@push('styles')
    <style>
        .kasbonGenCreatePage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .kasbonGenCreatePage .card-header {
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

        /* ADD ITEM SECTION */
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
            color: #10b981;
            font-size: 1.2rem;
        }

        .add-item-form-section .form-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.875rem;
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
            text-align: center;
            background-color: #fff;
        }

        .table-items tbody tr:hover {
            background-color: #f8f9fa;
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
    </style>
@endpush

@section('content')

    <div id="floatingBadgeAlert" class="floating-badge-alert">
        <i class="fas fa-circle-notch fa-spin" id="alertIcon"></i>
        <div class="alert-text" id="alertText">Processing...</div>
    </div>

    <div class="container-fluid kasbonGenCreatePage">
        <div class="row">
            <div class="col-lg-12">

                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center"
                        style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add Cash Advance General</span>
                        <div class="d-flex gap-2 align-items-center">
                            <span id="autoSaveStatus" class="badge bg-secondary"><i class="fas fa-circle"></i> Not
                                Saved</span>
                            <a href="{{ route('kasbon-gen.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="current_kasbon_gen_id" value="">
                <input type="hidden" id="current_kasbon_gen_str" value="">
                <input type="hidden" id="is_header_saved" value="false">

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

                                <div class="col-md-4">
                                    <label class="form-label required-field">Department</label>
                                    <select name="id_md_dep" id="id_md_dep" class="form-select select2-field" required>
                                        <option value="">-- Select Department --</option>
                                        @foreach ($departemens as $dep)
                                            <option value="{{ $dep->id_md_dep }}">{{ $dep->nama_dep }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label required-field">Branch</label>
                                    <select name="id_md_cabang" id="id_md_cabang" class="form-select select2-field"
                                        required>
                                        <option value="">-- Select Branch --</option>
                                        @foreach ($cabangs as $cabang)
                                            <option value="{{ $cabang->id_md_branch }}">{{ $cabang->nama_branch }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
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

                                    {{-- Category --}}
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Category</label>
                                        <select id="input_category" class="form-select select2-item"
                                            data-placeholder="Select Category">
                                            <option value=""></option>
                                            @foreach ($invoices->groupBy('invoice_ctg') as $category => $invoiceGroup)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Item --}}
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Item</label>
                                        <select id="input_item" class="form-select select2-item" disabled
                                            data-placeholder="Select category first">
                                            <option value=""></option>
                                        </select>
                                    </div>

                                    {{-- Nominal Kasbon --}}
                                    <div class="col-md-6">
                                        <label class="form-label required-field">CA Amount (IDR)</label>
                                        <div class="currency-group">
                                            <span class="currency-label">IDR</span>
                                            <input type="text" id="input_nilai_kasbon"
                                                class="form-control currency-input" placeholder="0,00">
                                        </div>
                                    </div>

                                    {{-- Add Button --}}
                                    <div class="col-md-6 d-flex align-items-end">
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
                                            <th style="width:25%;">Category</th>
                                            <th>Item</th>
                                            <th style="width:20%;">CA Amount (IDR)</th>
                                            <th style="width:8%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <tr class="no-items-row">
                                            <td colspan="5">
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
                                                <div class="currency-group-footer">
                                                    <span class="currency-label-footer">IDR</span>
                                                    <span class="value-footer" id="footerTotalKasbon">0,00</span>
                                                </div>
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
        // Invoice data by category
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

        let currentKasbonGenStr = '';
        let isHeaderSaved = false;
        let globalItemNumber = 0;

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
            $('.select2-item').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            $('#itemsCard').addClass('items-card-disabled');

            // Category change → load items
            $('#input_category').on('change', function() {
                const category = $(this).val();
                const itemSelect = $('#input_item');
                if (!category) {
                    itemSelect.prop('disabled', true).html('<option value=""></option>').trigger('change');
                    return;
                }
                const invoices = invoicesByCategory[category] || [];
                let opts = '<option value=""></option>';
                invoices.forEach(inv => {
                    opts += `<option value="${inv.id}">${inv.type}</option>`;
                });
                itemSelect.prop('disabled', false).html(opts).trigger('change');
            });
        });

        // Save Header
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

            showFloatingAlert('saving', 'Saving header...');
            $('#btnSaveHeader').prop('disabled', true);

            $.ajax({
                url: '{{ route('kasbon-gen.header.store') }}',
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
                    _token: $('input[name="_token"]').val(),
                },
                success: function(r) {
                    $('#btnSaveHeader').prop('disabled', false);
                    if (r.success) {
                        currentKasbonGenStr = r.data.id_kasbon_gen;
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

        // Add Item
        $('#btnAddToTable').on('click', function() {
            if (!isHeaderSaved) {
                showFloatingAlert('error', 'Please save the header first');
                return;
            }
            const category = $('#input_category').val(),
                itemId = $('#input_item').val(),
                itemText = $('#input_item option:selected').text(),
                nilaiKasbon = parseRupiah($('#input_nilai_kasbon').val());
            if (!category) {
                showFloatingAlert('error', 'Please select a category');
                return;
            }
            if (!itemId) {
                showFloatingAlert('error', 'Please select an item');
                return;
            }
            if (!nilaiKasbon || nilaiKasbon <= 0) {
                showFloatingAlert('error', 'Please enter CA Amount');
                return;
            }
            showFloatingAlert('saving', 'Adding item...');
            $.ajax({
                url: '{{ route('kasbon-gen.item.store') }}',
                method: 'POST',
                data: {
                    id_kasbon_gen: currentKasbonGenStr,
                    id_md_invoice: itemId,
                    nilai_kasbon: nilaiKasbon,
                    _token: $('input[name="_token"]').val()
                },
                success: function(r) {
                    if (r.success) {
                        showFloatingAlert('success', 'Item added!');
                        appendItemRow(r.data, category, itemText);
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

        function appendItemRow(d, category, itemText) {
            $('.no-items-row').remove();
            globalItemNumber++;
            $('#itemsTableBody').append(`
    <tr class="item-row" data-item-id="${d.id_kasbon_gen_item}" data-category="${category}">
        <td class="fw-bold text-muted">${globalItemNumber}</td>
        <td class="text-start" style="background:#e8f5e9; font-weight:600;">${category}</td>
        <td class="text-start">${itemText}</td>
        <td class="text-end">${formatNumber(d.nilai_kasbon)}</td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                onclick="removeItem(this,'${d.id_kasbon_gen_item}')">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>`);
        }

        function clearItemForm() {
            $('#input_category').val(null).trigger('change');
            $('#input_item').prop('disabled', true).html('<option value=""></option>').trigger('change');
            $('#input_nilai_kasbon').val('');
        }

        function removeItem(button, itemId) {
            if (!confirm('Are you sure you want to delete this item?')) return;
            showFloatingAlert('saving', 'Deleting item...');
            $.ajax({
                url: `/kasbon-gen/item/destroy/${itemId}`,
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
                                `<tr class="no-items-row"><td colspan="5"><i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i><p class="mb-0 fw-bold">No items yet</p></td></tr>`
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
                    url: `/kasbon-gen/item/destroy/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })))
                .then(() => {
                    showFloatingAlert('success', 'All items deleted!');
                    $('#itemsTableBody').html(
                        `<tr class="no-items-row"><td colspan="5"><i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i><p class="mb-0 fw-bold">No items yet</p></td></tr>`
                    );
                    globalItemNumber = 0;
                    updateGrandTotal();
                }).catch(() => {
                    showFloatingAlert('error', 'Some items could not be deleted');
                });
        });

        function renumberAllItems() {
            $('.item-row').each(function(i) {
                $(this).find('td:first').text(i + 1);
            });
            globalItemNumber = $('.item-row').length;
        }

        function updateGrandTotal() {
            let total = 0;
            $('.item-row').each(function() {
                total += parseRupiah($(this).find('td').eq(3).text());
            });
            $('#footerTotalKasbon').text(formatNumber(total));
        }
    </script>
@endpush
