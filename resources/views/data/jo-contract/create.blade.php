@extends('layouts.app')

@section('title', 'Add JO Contract')

@push('styles')
    <style>
        .joContractCreatePage .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .joContractCreatePage .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .joContractCreatePage .form-control:focus,
        .joContractCreatePage .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .joContractCreatePage .form-label {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .joContractCreatePage .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .joContractCreatePage .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .joContractCreatePage .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .joContractCreatePage .btn-success:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: 600;
        }

        .kurs-section {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 10px;
            padding: 20px;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .kurs-section .form-label {
            color: rgba(255, 255, 255, 0.95);
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .kurs-section .form-control {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        .table-items {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        .table-items thead th {
            background-color: #f8f9fa;
            color: #2c3e50;
            border: 1px solid #dee2e6;
            padding: 12px 10px;
            font-weight: 600;
            font-size: 0.875rem;
            vertical-align: middle;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-items tbody td {
            border: 1px solid #dee2e6;
            padding: 10px;
            vertical-align: middle;
            background-color: #fff;
        }

        .table-items tbody tr:hover td:not(.category-cell) {
            background-color: #f8f9fa;
        }

        /* Category Cell Styling - MERGED CELL */
        .category-cell {
            background: #f1f5f9 !important;
            color: #2c3e50 !important;
            font-weight: 600;
            font-size: 0.875rem;
            border-right: 2px solid #dee2e6 !important;
            text-align: center;
            vertical-align: middle;
            position: relative;
            padding: 10px !important;
        }

        .category-cell .category-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }

        .category-cell .category-name {
            font-weight: 700;
            font-size: 0.85rem;
            color: #1e293b;
        }

        .category-cell .category-count {
            background: #e2e8f0;
            color: #475569;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .category-cell .btn-remove-category {
            background: #dc3545;
            border: none;
            color: white;
            border-radius: 6px;
            width: 100%;
            max-width: 70px;
            padding: 4px 8px;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .category-cell .btn-remove-category:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        .table-items input,
        .table-items select {
            font-size: 0.875rem;
            padding: 0.5rem;
            border-radius: 6px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }

        .table-items input:focus,
        .table-items select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .currency-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .currency-label {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #2c3e50;
            padding: 0.5rem;
            font-size: 0.8rem;
            border-radius: 6px 0 0 6px;
            min-width: 50px;
            text-align: center;
            font-weight: 600;
        }

        .currency-input {
            border-radius: 0 6px 6px 0 !important;
            border-left: none !important;
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

        .table-footer {
            background: #2c3e50;
            color: white;
            font-weight: 700;
        }

        .table-footer .currency-label {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .no-items-row td {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
            background: #f8f9fa !important;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .item-number-cell {
            text-align: center;
            font-weight: 600;
            color: #2c3e50;
            background-color: #ecf0f1 !important;
            font-size: 0.9rem;
        }

        .calculation-mode-group {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }

        .calculation-mode-group label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            font-size: 0.85rem;
            color: #555;
        }

        .calculation-mode-group input[type="radio"] {
            cursor: pointer;
        }

        .calculation-mode-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .mode-idr {
            background-color: #10b981;
            color: white;
        }

        .mode-usd {
            background-color: #3b82f6;
            color: white;
        }

        .category-selector-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .add-category-dropdown {
            display: inline-block;
            position: relative;
        }

        .category-dropdown-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transition: all 0.3s;
        }

        .category-dropdown-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }

        .category-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 10px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            min-width: 250px;
            max-height: 300px;
            overflow-y: auto;
        }

        .category-dropdown-menu.show {
            display: block;
        }

        .category-dropdown-item {
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid #f1f3f5;
        }

        .category-dropdown-item:last-child {
            border-bottom: none;
        }

        .category-dropdown-item:hover {
            background: #f8f9fa;
            padding-left: 25px;
        }

        .category-dropdown-item i {
            margin-right: 8px;
            color: var(--primary-green);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid joContractCreatePage">
        <div class="row">
            <div class="col-lg-12">
                <!-- Page Header Card -->
                <div class="card shadow mb-4">
                    <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                        <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add JO Contract</span>
                        <a href="{{ route('jo-contract.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <form action="{{ route('jo-contract.store') }}" method="POST" id="joContractForm">
                    @csrf

                    <!-- JO Contract Info Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <h6 class="mb-0"><i class="fas fa-file-contract me-2"></i>JO Contract Information</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Contract</label>
                                    <select name="id_md_cont" id="id_md_cont"
                                        class="form-select @error('id_md_cont') is-invalid @enderror">
                                        <option value="">Select Contract</option>
                                        @foreach ($contracts as $contract)
                                            <option value="{{ $contract->id_md_cont }}"
                                                {{ old('id_md_cont') == $contract->id_md_cont ? 'selected' : '' }}>
                                                {{ $contract->no_contract }} - {{ $contract->contract }}
                                                @if ($contract->customer)
                                                    ({{ $contract->customer->customer }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_md_cont')
                                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Area</label>
                                    <select name="id_md_area" id="id_md_area"
                                        class="form-select @error('id_md_area') is-invalid @enderror">
                                        <option value="">Select Area</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id_md_area }}"
                                                {{ old('id_md_area') == $area->id_md_area ? 'selected' : '' }}>
                                                {{ $area->area }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_md_area')
                                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required-field">Title</label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                                @error('title')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- JO Contract Items Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>JO Contract Items</h6>
                                <div class="kurs-section d-flex gap-3 align-items-center mb-0" style="padding: 10px 15px;">
                                    <div>
                                        <label class="form-label mb-1">Kurs Date</label>
                                        <input type="date" name="global_tgl_kurs_usd" id="global_tgl_kurs_usd"
                                            class="form-control form-control-sm" style="min-width: 150px;" required>
                                    </div>
                                    <div>
                                        <label class="form-label mb-1">Kurs Rate</label>
                                        <input type="text" id="global_kurs_usd_display"
                                            class="form-control form-control-sm" style="min-width: 130px;" required>
                                        <input type="hidden" name="global_kurs_usd" id="global_kurs_usd" value="17600">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="action-buttons">
                                <button type="button" class="btn btn-primary" id="addItemBtn">
                                    <i class="fas fa-plus me-1"></i>Add Item
                                </button>
                                <button type="button" class="btn btn-danger" id="resetAllBtn">
                                    <i class="fas fa-trash-alt me-1"></i>Reset All
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-items table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 3%;">No</th>
                                            <th style="width: 10%;">Category</th>
                                            <th style="width: 15%;">Item</th>
                                            <th style="width: 13%;">Pendapatan IDR</th>
                                            <th style="width: 13%;">Pendapatan USD</th>
                                            <th style="width: 13%;">HPP (Biaya Ops)</th>
                                            <th style="width: 13%;">Harga Jual (IDR)</th>
                                            <th style="width: 4%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <tr class="no-items-row">
                                            <td colspan="8">
                                                <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                                                <p class="mb-0 fw-bold">No data available</p>
                                                <small class="text-muted">Click "Add Item" to get started</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="table-footer">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>GRAND TOTAL</strong></td>
                                            <td>
                                                <div class="currency-group">
                                                    <span class="currency-label text-white">IDR</span>
                                                    <span id="footerTotalIDR"
                                                        style="color: #fff; font-weight: 700;">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="currency-group">
                                                    <span class="currency-label text-white">USD</span>
                                                    <span id="footerTotalUSD"
                                                        style="color: #fff; font-weight: 700;">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="currency-group">
                                                    <span class="currency-label text-white">IDR</span>
                                                    <span id="footerTotalHPP"
                                                        style="color: #fff; font-weight: 700;">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="currency-group">
                                                    <span class="currency-label text-white">IDR</span>
                                                    <span id="footerTotalSelling"
                                                        style="color: #fff; font-weight: 700;">0,00</span>
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            @error('items')
                                <div class="alert alert-danger mt-3">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mb-5">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i>Save JO Contract
                        </button>
                        <a href="{{ route('jo-contract.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- Script Jo Contract Create --}}
@push('scripts')
    <script>
        let globalItemNumber = 0;
        let categories = {};

        // Format Rupiah Helper Functions
        function formatRupiah(value) {
            let number = value.replace(/[^\d,]/g, '');
            number = number.replace(/\./g, '');
            if (number === '') return '';

            let parts = number.split(',');
            let integerPart = parts[0];
            let decimalPart = parts.length > 1 ? parts[1] : '';

            if (decimalPart.length > 2) {
                decimalPart = decimalPart.substring(0, 2);
            }

            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            if (parts.length > 1) {
                return integerPart + ',' + decimalPart;
            } else {
                return integerPart + ',00';
            }
        }

        function parseRupiah(value) {
            let cleaned = value.replace(/\./g, '').replace(',', '.');
            return parseFloat(cleaned) || 0;
        }

        function setupRupiahInput(displayInput, hiddenInput) {
            displayInput.addEventListener('input', function(e) {
                let cursorPosition = this.selectionStart;
                let beforeCursor = this.value.substring(0, cursorPosition);

                let formatted = formatRupiah(this.value);
                this.value = formatted;

                if (!beforeCursor.includes(',')) {
                    let digitsBeforeCursor = beforeCursor.replace(/\D/g, '').length;
                    let newPos = 0;
                    let digitCount = 0;
                    for (let i = 0; i < this.value.length; i++) {
                        if (/\d/.test(this.value[i])) {
                            digitCount++;
                            if (digitCount === digitsBeforeCursor) {
                                newPos = i + 1;
                                break;
                            }
                        }
                    }
                    this.setSelectionRange(newPos, newPos);
                } else {
                    let commaPos = this.value.indexOf(',');
                    let decimalDigitsInput = beforeCursor.split(',')[1] || '';
                    let decimalDigits = decimalDigitsInput.length;
                    let newPos = commaPos + 1 + Math.min(decimalDigits, 2);
                    this.setSelectionRange(newPos, newPos);
                }

                hiddenInput.value = parseRupiah(formatted);
            });

            displayInput.addEventListener('keydown', function(e) {
                let cursorPosition = this.selectionStart;
                let selectionEnd = this.selectionEnd;
                let commaPos = this.value.indexOf(',');

                if (e.key === 'Backspace') {
                    if (cursorPosition !== selectionEnd) {
                        return;
                    }

                    if (commaPos !== -1 && cursorPosition > commaPos + 1) {
                        e.preventDefault();

                        let posInDecimal = cursorPosition - commaPos - 1;
                        let beforeComma = this.value.substring(0, commaPos);
                        let afterComma = this.value.substring(commaPos + 1);

                        let newDecimal = afterComma.substring(0, posInDecimal - 1) + afterComma.substring(posInDecimal);

                        let newValue = beforeComma.replace(/\./g, '') + ',' + newDecimal;
                        let formatted = formatRupiah(newValue);
                        this.value = formatted;

                        let newCommaPos = this.value.indexOf(',');
                        let newCursorPos = newCommaPos + Math.max(1, posInDecimal);
                        this.setSelectionRange(newCursorPos, newCursorPos);

                        hiddenInput.value = parseRupiah(this.value);
                    }
                    else if (commaPos !== -1 && cursorPosition === commaPos + 1) {
                        e.preventDefault();
                    }
                    else if (cursorPosition === commaPos) {
                        e.preventDefault();
                        let beforeComma = this.value.substring(0, commaPos);
                        this.value = beforeComma;
                        this.setSelectionRange(beforeComma.length, beforeComma.length);
                        hiddenInput.value = parseRupiah(this.value);
                    }
                } else if (e.key === 'Delete') {
                    if (cursorPosition !== selectionEnd) {
                        return;
                    }

                    if (cursorPosition === commaPos) {
                        e.preventDefault();
                        let beforeComma = this.value.substring(0, commaPos);
                        this.value = beforeComma;
                        this.setSelectionRange(beforeComma.length, beforeComma.length);
                        hiddenInput.value = parseRupiah(this.value);
                        return;
                    }

                    if (commaPos !== -1 && cursorPosition > commaPos && cursorPosition < this.value.length) {
                        e.preventDefault();

                        let posInDecimal = cursorPosition - commaPos - 1;
                        let beforeComma = this.value.substring(0, commaPos);
                        let afterComma = this.value.substring(commaPos + 1);

                        let newDecimal = afterComma.substring(0, posInDecimal) + afterComma.substring(posInDecimal + 1);

                        let newValue = beforeComma.replace(/\./g, '') + ',' + newDecimal;
                        let formatted = formatRupiah(newValue);
                        this.value = formatted;

                        let newCommaPos = this.value.indexOf(',');
                        this.setSelectionRange(newCommaPos + posInDecimal + 1, newCommaPos + posInDecimal + 1);

                        hiddenInput.value = parseRupiah(this.value);
                    }
                }
            });

            displayInput.addEventListener('blur', function(e) {
                if (e.target.value) {
                    if (!e.target.value.includes(',')) {
                        e.target.value = e.target.value + ',00';
                    } else {
                        let parts = e.target.value.split(',');
                        if (parts[1] !== undefined) {
                            if (parts[1].length === 0) {
                                e.target.value = parts[0] + ',00';
                            } else if (parts[1].length < 2) {
                                e.target.value = parts[0] + ',' + parts[1].padEnd(2, '0');
                            }
                        }
                    }
                    hiddenInput.value = parseRupiah(e.target.value);
                } else {
                    e.target.value = '';
                    hiddenInput.value = '';
                }
            });

            displayInput.addEventListener('keypress', function(e) {
                if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                    (e.key === ',' && !this.value.includes(',')) ||
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true)) {
                    return;
                }

                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        }

        // Setup Global Kurs Input
        const globalKursDisplay = document.getElementById('global_kurs_usd_display');
        const globalKursValue = document.getElementById('global_kurs_usd');
        setupRupiahInput(globalKursDisplay, globalKursValue);

        // Group invoices by category
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

        // Get all categories
        const allCategories = Object.keys(invoicesByCategory);

        // Add Item Button - adds new row with category dropdown
        document.getElementById('addItemBtn').addEventListener('click', function() {
            addNewItemRow();
        });

        // Add new item row with category dropdown
        function addNewItemRow() {
            globalItemNumber++;

            // Remove "no items" row if exists
            const noItemsRow = document.querySelector('.no-items-row');
            if (noItemsRow) {
                noItemsRow.remove();
            }

            const tbody = document.getElementById('itemsTableBody');
            const newRow = document.createElement('tr');
            newRow.classList.add('item-row');
            newRow.setAttribute('data-item-number', globalItemNumber);
            newRow.setAttribute('data-temp-row', 'true');

            // Build category dropdown options
            let categoryOptionsHtml = '<option value="">-- Select Category --</option>';
            allCategories.forEach(cat => {
                categoryOptionsHtml += `<option value="${cat}">${cat}</option>`;
            });

            newRow.innerHTML = `
            <td class="item-number-cell">${globalItemNumber}</td>
            <td>
                <select class="form-select category-dropdown-select">
                    ${categoryOptionsHtml}
                </select>
            </td>
            <td>
                <select name="items[${globalItemNumber}][id_md_invoice]" class="form-select invoice-type-select" disabled>
                    <option value="">Select category first</option>
                </select>
                <input type="hidden" name="items[${globalItemNumber}][invoice_ctg]" class="invoice-ctg-input">
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">IDR</span>
                    <input type="text" class="form-control currency-input revenue-idr-display">
                    <input type="hidden" name="items[${globalItemNumber}][pendapatan_idr]" class="revenue-idr-value">
                </div>
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">USD</span>
                    <input type="text" class="form-control currency-input revenue-usd-display">
                    <input type="hidden" name="items[${globalItemNumber}][pendapatan_usd]" class="revenue-usd-value">
                </div>
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">IDR</span>
                    <input type="text" class="form-control currency-input hpp-display">
                    <input type="hidden" name="items[${globalItemNumber}][hpp_ops]" class="hpp-value">
                </div>
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">IDR</span>
                    <input type="text" class="form-control currency-input selling-price-display" readonly style="background-color: #e9ecef;">
                    <input type="hidden" name="items[${globalItemNumber}][hargajual_idr]" class="selling-price-value">
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-remove-row" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

            tbody.appendChild(newRow);

            // Attach category change event
            const categorySelect = newRow.querySelector('.category-dropdown-select');
            categorySelect.addEventListener('change', function() {
                onCategorySelected(newRow, this.value);
            });

            attachItemEventListeners(newRow);
            renumberAllItems();
        }

        // When category is selected from dropdown
        function onCategorySelected(row, categoryName) {
            if (!categoryName) return;

            const itemNumber = row.getAttribute('data-item-number');

            // Update hidden input for category
            row.querySelector('.invoice-ctg-input').value = categoryName;

            // Enable and populate invoice type dropdown
            const invoiceSelect = row.querySelector('.invoice-type-select');
            const invoiceOptions = invoicesByCategory[categoryName] || [];

            let invoiceOptionsHtml = '<option value="">Select Invoice Type</option>';
            invoiceOptions.forEach(invoice => {
                invoiceOptionsHtml += `<option value="${invoice.id}">${invoice.type}</option>`;
            });

            invoiceSelect.innerHTML = invoiceOptionsHtml;
            invoiceSelect.disabled = false;

            // Check if this category already exists
            const existingCategoryCell = document.querySelector(`.category-cell[data-category="${categoryName}"]`);

            if (existingCategoryCell) {
                // Category exists - merge with existing
                row.removeAttribute('data-temp-row');
                row.setAttribute('data-category', categoryName);

                // Remove the dropdown cell
                row.querySelector('.category-dropdown-select').closest('td').remove();

                // Update category count
                categories[categoryName].count++;
                updateCategoryRowspan(categoryName);

            } else {
                // New category - convert dropdown to merged cell
                row.removeAttribute('data-temp-row');
                row.setAttribute('data-category', categoryName);
                row.setAttribute('data-is-first-in-category', 'true');

                categories[categoryName] = {
                    count: 1,
                    firstRowIndex: itemNumber
                };

                // Replace dropdown cell with merged category cell
                const dropdownCell = row.querySelector('.category-dropdown-select').closest('td');
                dropdownCell.outerHTML = `
                <td class="category-cell" rowspan="1" data-category="${categoryName}">
                    <div class="category-content">
                        <span class="category-name">${categoryName}</span>
                        <span class="category-count">1 item</span>
                        <button type="button" class="btn-remove-category" onclick="removeCategoryWithItems('${categoryName}', event)">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                </td>
            `;
            }

            renumberAllItems();
        }

        // Update category rowspan
        function updateCategoryRowspan(categoryName) {
            const categoryCell = document.querySelector(`.category-cell[data-category="${categoryName}"]`);
            if (categoryCell) {
                const count = categories[categoryName].count;
                categoryCell.setAttribute('rowspan', count);
                const countText = count === 1 ? '1 item' : count + ' items';
                categoryCell.querySelector('.category-count').textContent = countText;
            }
        }

        // Remove category with all items
        function removeCategoryWithItems(categoryName, event) {
            event.stopPropagation();

            if (confirm(`Remove category "${categoryName}" and all its ${categories[categoryName].count} item(s)?`)) {
                // Remove all rows with this category
                const rows = document.querySelectorAll(`[data-category="${categoryName}"]`);
                rows.forEach(row => row.remove());

                // Remove from categories object
                delete categories[categoryName];

                renumberAllItems();
                updateGrandTotal();

                // Check if table is empty
                const tbody = document.getElementById('itemsTableBody');
                if (tbody.querySelectorAll('tr').length === 0) {
                    tbody.innerHTML = `
                    <tr class="no-items-row">
                        <td colspan="8">
                            <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                            <p class="mb-0 fw-bold">No data available</p>
                            <small class="text-muted">Click "Add Item" to get started</small>
                        </td>
                    </tr>
                `;
                }
            }
        }

        // Remove item
        function removeItem(button) {
            const row = button.closest('tr');
            const categoryName = row.dataset.category;

            // If row has category dropdown (not yet selected), just remove it
            if (row.dataset.tempRow === 'true') {
                row.remove();
                renumberAllItems();
                updateGrandTotal();

                const tbody = document.getElementById('itemsTableBody');
                if (tbody.querySelectorAll('tr').length === 0) {
                    tbody.innerHTML = `
                    <tr class="no-items-row">
                        <td colspan="8">
                            <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                            <p class="mb-0 fw-bold">No data available</p>
                            <small class="text-muted">Click "Add Item" to get started</small>
                        </td>
                    </tr>
                `;
                }
                return;
            }

            const isFirst = row.dataset.isFirstInCategory === 'true';

            // If it's the last item in category, remove entire category
            if (categories[categoryName].count === 1) {
                removeCategoryWithItems(categoryName, new Event('click'));
                return;
            }

            // If it's the first row, transfer category cell to next row
            if (isFirst) {
                const categoryCell = row.querySelector('.category-cell');
                const nextCategoryRow = row.nextElementSibling;

                if (nextCategoryRow && nextCategoryRow.dataset.category === categoryName) {
                    // Clone category cell
                    const newCategoryCell = categoryCell.cloneNode(true);

                    // Insert at position 1 (after item number)
                    nextCategoryRow.insertBefore(newCategoryCell, nextCategoryRow.children[1]);
                    nextCategoryRow.setAttribute('data-is-first-in-category', 'true');
                }
            }

            row.remove();
            categories[categoryName].count--;

            updateCategoryRowspan(categoryName);
            renumberAllItems();
            updateGrandTotal();
        }

        // Reset All Button
        document.getElementById('resetAllBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to remove all items?')) {
                document.getElementById('itemsTableBody').innerHTML = `
                <tr class="no-items-row">
                    <td colspan="8">
                        <i class="fas fa-inbox fa-4x mb-3 d-block text-muted"></i>
                        <p class="mb-0 fw-bold">No data available</p>
                        <small class="text-muted">Click "Add Item" to get started</small>
                    </td>
                </tr>
            `;

                globalItemNumber = 0;
                categories = {};
                updateGrandTotal();
            }
        });

        // Renumber all items
        function renumberAllItems() {
            const itemRows = document.querySelectorAll('.item-row');
            itemRows.forEach((row, index) => {
                const newNumber = index + 1;
                row.querySelector('.item-number-cell').textContent = newNumber;
                row.setAttribute('data-item-number', newNumber);
            });
            globalItemNumber = itemRows.length;
        }

        // Attach event listeners to item - WITH RUPIAH FORMAT
        function attachItemEventListeners(row) {
            const revenueIDRDisplay = row.querySelector('.revenue-idr-display');
            const revenueIDRValue = row.querySelector('.revenue-idr-value');
            const revenueUSDDisplay = row.querySelector('.revenue-usd-display');
            const revenueUSDValue = row.querySelector('.revenue-usd-value');
            const hppDisplay = row.querySelector('.hpp-display');
            const hppValue = row.querySelector('.hpp-value');
            const sellingPriceDisplay = row.querySelector('.selling-price-display');
            const sellingPriceValue = row.querySelector('.selling-price-value');

            // Setup Rupiah formatting for all inputs
            setupRupiahInput(revenueIDRDisplay, revenueIDRValue);
            setupRupiahInput(revenueUSDDisplay, revenueUSDValue);
            setupRupiahInput(hppDisplay, hppValue);

            function calculateSellingPrice() {
                const kursRate = parseRupiah(globalKursDisplay.value);
                const idrValue = parseRupiah(revenueIDRDisplay.value);
                const usdValue = parseRupiah(revenueUSDDisplay.value);

                let sellingPrice = 0;
                if (idrValue > 0) {
                    sellingPrice = idrValue;
                } else if (usdValue > 0) {
                    sellingPrice = usdValue * kursRate;
                }

                sellingPriceValue.value = sellingPrice.toFixed(2);
                sellingPriceDisplay.value = formatRupiah(sellingPrice.toFixed(2).replace('.', ','));
                updateGrandTotal();
            }

            // Event listener untuk Pendapatan IDR
            revenueIDRDisplay.addEventListener('input', function() {
                // Jika IDR diisi, kosongkan USD
                if (parseRupiah(this.value) > 0) {
                    revenueUSDDisplay.value = '0,00';
                    revenueUSDValue.value = '0';
                }
                calculateSellingPrice();
            });

            // Event listener untuk Pendapatan USD
            revenueUSDDisplay.addEventListener('input', function() {
                // Jika USD diisi, kosongkan IDR
                if (parseRupiah(this.value) > 0) {
                    revenueIDRDisplay.value = '0,00';
                    revenueIDRValue.value = '0';
                }
                calculateSellingPrice();
            });

            // Event listener untuk HPP
            hppDisplay.addEventListener('input', updateGrandTotal);
        }

        // Global kurs change handler - UPDATED WITH RUPIAH FORMAT
        globalKursDisplay.addEventListener('input', function() {
            const allRows = document.querySelectorAll('.item-row');
            allRows.forEach(row => {
                const revenueIDRDisplay = row.querySelector('.revenue-idr-display');
                const revenueUSDDisplay = row.querySelector('.revenue-usd-display');
                const sellingPriceDisplay = row.querySelector('.selling-price-display');
                const sellingPriceValue = row.querySelector('.selling-price-value');

                const idrValue = parseRupiah(revenueIDRDisplay.value);
                const usdValue = parseRupiah(revenueUSDDisplay.value);
                const kursRate = parseRupiah(this.value);

                let sellingPrice = 0;
                if (idrValue > 0) {
                    sellingPrice = idrValue;
                } else if (usdValue > 0) {
                    sellingPrice = usdValue * kursRate;
                }

                sellingPriceValue.value = sellingPrice.toFixed(2);
                sellingPriceDisplay.value = formatRupiah(sellingPrice.toFixed(2).replace('.', ','));
            });
            updateGrandTotal();
        });

        // Update grand total - FIXED TO CALCULATE ALL COLUMNS
        function updateGrandTotal() {
            const itemRows = document.querySelectorAll('.item-row');
            let totalRevenueIDR = 0;
            let totalRevenueUSD = 0;
            let totalHPP = 0;
            let totalSelling = 0;

            itemRows.forEach(row => {
                const idr = parseRupiah(row.querySelector('.revenue-idr-display')?.value || '0');
                const usd = parseRupiah(row.querySelector('.revenue-usd-display')?.value || '0');
                const hpp = parseRupiah(row.querySelector('.hpp-display')?.value || '0');
                const selling = parseRupiah(row.querySelector('.selling-price-display')?.value || '0');

                totalRevenueIDR += idr;
                totalRevenueUSD += usd;
                totalHPP += hpp;
                totalSelling += selling;
            });

            // Update footer dengan format angka
            document.getElementById('footerTotalIDR').textContent = formatNumber(totalRevenueIDR);
            document.getElementById('footerTotalUSD').textContent = formatNumber(totalRevenueUSD);
            document.getElementById('footerTotalHPP').textContent = formatNumber(totalHPP);
            document.getElementById('footerTotalSelling').textContent = formatNumber(totalSelling);
        }

        // Format number
        function formatNumber(amount) {
            return amount.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Form submission validation
        document.getElementById('joContractForm').addEventListener('submit', function(e) {
            const itemRows = document.querySelectorAll('.item-row');

            if (itemRows.length === 0) {
                e.preventDefault();
                alert('Please add at least one item!');
                return false;
            }

            // Check for temp rows (category not selected)
            const tempRows = document.querySelectorAll('[data-temp-row="true"]');
            if (tempRows.length > 0) {
                e.preventDefault();
                alert('Please select a category for all items!');
                return false;
            }

            const kursDate = document.getElementById('global_tgl_kurs_usd').value;
            const kursRate = document.getElementById('global_kurs_usd').value;

            if (!kursDate || !kursRate || parseFloat(kursRate) <= 0) {
                e.preventDefault();
                alert('Please fill in the exchange rate information!');
                return false;
            }
        });
    </script>
@endpush