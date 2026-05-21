@extends('layouts.app')

@section('title', 'Edit JO Contract')

@push('styles')
    <style>
        .required-field::after {
            content: " *";
            color: red;
        }

        .kurs-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .kurs-section h6 {
            color: white;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .kurs-section .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .kurs-section .form-control {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            padding: 0.6rem 0.75rem;
        }

        .table-items {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        .table-items thead th {
            background-color: #ececec;
            color: rgb(0, 0, 0);
            border: 1px solid #34495e;
            padding: 14px 10px;
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
            background: #f8f9fa !important;
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
            color: #495057;
        }

        .category-cell .category-count {
            background: #e9ecef;
            color: #495057;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .category-cell .btn-remove-category {
            background: #dc3545;
            border: none;
            color: white;
            border-radius: 4px;
            width: 100%;
            max-width: 60px;
            padding: 3px 8px;
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
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .currency-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .currency-label {
            background-color: #34495e;
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
            background: linear-gradient(90deg, #2c3e50 0%, #34495e 100%);
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .item-number-cell {
            text-align: center;
            font-weight: 600;
            color: #2c3e50;
            background-color: #ecf0f1 !important;
            font-size: 0.9rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="h3 mb-2 text-gray-800">Edit JO Contract</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom mb-0">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('jo-contract.index') }}">JO Contract</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>

        <form action="{{ route('jo-contract.update', $joContract->id_jo_cont) }}" method="POST" id="joContractForm">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Main Form -->
                <div class="col-lg-12">
                    <!-- JO Contract Info Card -->
                    <div class="card mb-4">
                        <div class="card-header text-black" style="background-color: #d1fae5"">
                            <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>JO Contract Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Contract</label>
                                    <select name="id_md_cont" id="id_md_cont"
                                        class="form-select @error('id_md_cont') is-invalid @enderror" required>
                                        <option value="">Select Contract</option>
                                        @foreach ($contracts as $contract)
                                            <option value="{{ $contract->id_md_cont }}"
                                                {{ old('id_md_cont', $joContract->id_md_cont) == $contract->id_md_cont ? 'selected' : '' }}>
                                                {{ $contract->no_contract }} - {{ $contract->contract }}
                                                @if ($contract->customer)
                                                    ({{ $contract->customer->customer }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_md_cont')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Area</label>
                                    <select name="id_md_area" id="id_md_area"
                                        class="form-select @error('id_md_area') is-invalid @enderror" required>
                                        <option value="">Select Area</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id_md_area }}"
                                                {{ old('id_md_area', $joContract->id_md_area) == $area->id_md_area ? 'selected' : '' }}>
                                                {{ $area->area }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_md_area')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required-field">Title</label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $joContract->title) }}" placeholder="JO contract title"
                                    required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="3"
                                    placeholder="Additional notes (optional)">{{ old('note', $joContract->note) }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- JO Contract Items Card -->
                    <div class="card">
                        <div class="card-header text-black" style="background-color: #d1fae5">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-list me-2"></i>JO Contract Items</h5>
                                <div class="d-flex gap-3 align-items-center">
                                    <div>
                                        <label class="form-label mb-1 small">Kurs Date</label>
                                        @php
                                            $firstItem = $joContract->items->first();
                                            $defaultDate = $firstItem && $firstItem->tgl_kurs_usd
                                                ? $firstItem->tgl_kurs_usd->format('Y-m-d')
                                                : '';
                                        @endphp
                                        <input type="date" name="global_tgl_kurs_usd" id="global_tgl_kurs_usd"
                                            class="form-control form-control-sm"
                                            value="{{ old('global_tgl_kurs_usd', $defaultDate) }}"
                                            style="min-width: 150px;" required>
                                    </div>
                                    <div>
                                        <label class="form-label mb-1 small">Kurs Rate</label>
                                        @php
                                            $defaultKurs = $firstItem && $firstItem->kurs_usd
                                                ? $firstItem->kurs_usd
                                                : 17600;
                                        @endphp
                                        <input type="text" id="global_kurs_usd_display"
                                            class="form-control form-control-sm"
                                            value="{{ number_format($defaultKurs, 2, ',', '.') }}"
                                            placeholder="17.600,00" style="min-width: 130px;" required>
                                        <input type="hidden" name="global_kurs_usd" id="global_kurs_usd" value="{{ $defaultKurs }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
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
                                        @if ($joContract->items->count() > 0)
                                            @foreach ($joContract->items as $index => $item)
                                                <tr class="item-row" data-item-number="{{ $index + 1 }}"
                                                    data-category="{{ $item->invoice->invoice_ctg }}"
                                                    @if ($loop->first || $item->invoice->invoice_ctg != $joContract->items[$index - 1]->invoice->invoice_ctg) data-is-first-in-category="true" @endif>
                                                    <td class="item-number-cell">{{ $index + 1 }}</td>

                                                    @if ($loop->first || $item->invoice->invoice_ctg != $joContract->items[$index - 1]->invoice->invoice_ctg)
                                                        @php
                                                            $categoryCount = $joContract->items
                                                                ->where('invoice.invoice_ctg', $item->invoice->invoice_ctg)
                                                                ->count();
                                                        @endphp
                                                        <td class="category-cell" rowspan="{{ $categoryCount }}"
                                                            data-category="{{ $item->invoice->invoice_ctg }}">
                                                            <div class="category-content">
                                                                <span
                                                                    class="category-name">{{ $item->invoice->invoice_ctg }}</span>
                                                                <span
                                                                    class="category-count">{{ $categoryCount }}
                                                                    item{{ $categoryCount > 1 ? 's' : '' }}</span>
                                                                <button type="button" class="btn-remove-category"
                                                                    onclick="removeCategoryWithItems('{{ $item->invoice->invoice_ctg }}', event)">
                                                                    <i class="fas fa-times"></i> Remove
                                                                </button>
                                                            </div>
                                                        </td>
                                                    @endif

                                                    <td>
                                                        <select name="items[{{ $index + 1 }}][id_md_invoice]"
                                                            class="form-select invoice-type-select" required>
                                                            <option value="">Select Invoice Type</option>
                                                            @foreach ($invoices->where('invoice_ctg', $item->invoice->invoice_ctg) as $invoice)
                                                                <option value="{{ $invoice->id_md_invoice }}"
                                                                    {{ $item->id_md_invoice == $invoice->id_md_invoice ? 'selected' : '' }}>
                                                                    {{ $invoice->invoice_typ }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" name="items[{{ $index + 1 }}][invoice_ctg]"
                                                            class="invoice-ctg-input"
                                                            value="{{ $item->invoice->invoice_ctg }}">
                                                    </td>
                                                    <td>
                                                        <div class="currency-group">
                                                            <span class="currency-label">IDR</span>
                                                            <input type="text" class="form-control currency-input revenue-idr-display"
                                                                value="{{ number_format($item->pendapatan_idr, 2, ',', '.') }}" required>
                                                            <input type="hidden" name="items[{{ $index + 1 }}][pendapatan_idr]"
                                                                class="revenue-idr-value" value="{{ $item->pendapatan_idr }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="currency-group">
                                                            <span class="currency-label">USD</span>
                                                            <input type="text" class="form-control currency-input revenue-usd-display"
                                                                value="{{ number_format($item->pendapatan_usd, 2, ',', '.') }}" required>
                                                            <input type="hidden" name="items[{{ $index + 1 }}][pendapatan_usd]"
                                                                class="revenue-usd-value" value="{{ $item->pendapatan_usd }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="currency-group">
                                                            <span class="currency-label">IDR</span>
                                                            <input type="text" class="form-control currency-input hpp-display"
                                                                value="{{ number_format($item->hpp_ops, 2, ',', '.') }}" required>
                                                            <input type="hidden" name="items[{{ $index + 1 }}][hpp_ops]"
                                                                class="hpp-value" value="{{ $item->hpp_ops }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="currency-group">
                                                            <span class="currency-label">IDR</span>
                                                            <input type="text" class="form-control currency-input selling-price-display"
                                                                value="{{ number_format($item->hargajual_idr, 2, ',', '.') }}" readonly
                                                                style="background-color: #e9ecef;">
                                                            <input type="hidden" name="items[{{ $index + 1 }}][hargajual_idr]"
                                                                class="selling-price-value" value="{{ $item->hargajual_idr }}">
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                                                            onclick="removeItem(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="no-items-row">
                                                <td colspan="8">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <p class="mb-0 fw-bold">No data available</p>
                                                    <small class="text-muted">Click "Add Item" above to get started</small>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot class="table-footer">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>GRAND TOTAL</strong></td>
                                            <td>
                                                <div class="currency-group">
                                                    <span class="currency-label text-black">IDR</span>
                                                    <span id="footerTotalIDR"
                                                        style="color: rgb(0, 0, 0); font-weight: 700;">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="currency-group">
                                                    <span class="currency-label text-black">USD</span>
                                                    <span id="footerTotalUSD"
                                                        style="color: rgb(0, 0, 0); font-weight: 700;">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="currency-group">
                                                    <span class="currency-label text-black">IDR</span>
                                                    <span id="footerTotalHPP"
                                                        style="color: rgb(0, 0, 0); font-weight: 700;">0,00</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="currency-group">
                                                    <span class="currency-label text-black">IDR</span>
                                                    <span id="footerTotalSelling"
                                                        style="color: rgb(0, 0, 0); font-weight: 700;">0,00</span>
                                                </div>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            @error('items')
                                <div class="alert alert-danger mt-3">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mt-4 mb-5">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i>Update JO Contract
                        </button>
                        <a href="{{ route('jo-contract.show', $joContract->id_jo_cont) }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let globalItemNumber = {{ $joContract->items->count() }};
        let categories = {};

        // Format Rupiah Helper Functions
        function formatRupiah(value) {
            let number = value.replace(/[^\d,]/g, '');
            number = number.replace(/\./g, '');
            if (number === '') return '0,00';

            let parts = number.split(',');
            let integerPart = parts[0];
            let decimalPart = parts.length > 1 ? parts[1] : '';

            if (decimalPart.length > 2) {
                decimalPart = decimalPart.substring(0, 2);
            }

            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            if (parts.length > 1) {
                return integerPart + ',' + decimalPart.padEnd(2, '0');
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
                    let decimalDigits = beforeCursor.split(',')[1]?.replace(/\D/g, '').length || 0;
                    let newPos = commaPos + 1 + Math.min(decimalDigits, 2);
                    this.setSelectionRange(newPos, newPos);
                }

                hiddenInput.value = parseRupiah(formatted);
            });

            displayInput.addEventListener('blur', function(e) {
                if (e.target.value && e.target.value !== '0,00') {
                    let formatted = formatRupiah(e.target.value);
                    e.target.value = formatted;
                    hiddenInput.value = parseRupiah(formatted);
                } else {
                    e.target.value = '0,00';
                    hiddenInput.value = '0';
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

        // Initialize categories from existing items
        document.addEventListener('DOMContentLoaded', function() {
            const itemRows = document.querySelectorAll('.item-row');
            itemRows.forEach(row => {
                const categoryName = row.dataset.category;
                if (categoryName) {
                    if (!categories[categoryName]) {
                        categories[categoryName] = {
                            count: 0
                        };
                    }
                    categories[categoryName].count++;
                }

                // Attach event listeners to existing rows
                attachItemEventListeners(row);
            });

            // Calculate initial grand total
            updateGrandTotal();
        });

        // Add Item Button - adds new row with category dropdown
        document.getElementById('addItemBtn').addEventListener('click', function() {
            addNewItemRow();
        });

        // Add new item row with category dropdown (same as create page)
        function addNewItemRow() {
            globalItemNumber++;

            const noItemsRow = document.querySelector('.no-items-row');
            if (noItemsRow) {
                noItemsRow.remove();
            }

            const tbody = document.getElementById('itemsTableBody');
            const newRow = document.createElement('tr');
            newRow.classList.add('item-row');
            newRow.setAttribute('data-item-number', globalItemNumber);
            newRow.setAttribute('data-temp-row', 'true');

            let categoryOptionsHtml = '<option value="">-- Select Category --</option>';
            allCategories.forEach(cat => {
                categoryOptionsHtml += `<option value="${cat}">${cat}</option>`;
            });

            newRow.innerHTML = `
            <td class="item-number-cell">${globalItemNumber}</td>
            <td>
                <select class="form-select category-dropdown-select" required>
                    ${categoryOptionsHtml}
                </select>
            </td>
            <td>
                <select name="items[${globalItemNumber}][id_md_invoice]" class="form-select invoice-type-select" disabled required>
                    <option value="">Select category first</option>
                </select>
                <input type="hidden" name="items[${globalItemNumber}][invoice_ctg]" class="invoice-ctg-input">
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">IDR</span>
                    <input type="text" class="form-control currency-input revenue-idr-display" value="0,00" required>
                    <input type="hidden" name="items[${globalItemNumber}][pendapatan_idr]" class="revenue-idr-value" value="0">
                </div>
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">USD</span>
                    <input type="text" class="form-control currency-input revenue-usd-display" value="0,00" required>
                    <input type="hidden" name="items[${globalItemNumber}][pendapatan_usd]" class="revenue-usd-value" value="0">
                </div>
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">IDR</span>
                    <input type="text" class="form-control currency-input hpp-display" value="0,00" required>
                    <input type="hidden" name="items[${globalItemNumber}][hpp_ops]" class="hpp-value" value="0">
                </div>
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">IDR</span>
                    <input type="text" class="form-control currency-input selling-price-display" value="0,00" readonly style="background-color: #e9ecef;">
                    <input type="hidden" name="items[${globalItemNumber}][hargajual_idr]" class="selling-price-value" value="0">
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-remove-row" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

            tbody.appendChild(newRow);

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
            row.querySelector('.invoice-ctg-input').value = categoryName;

            const invoiceSelect = row.querySelector('.invoice-type-select');
            const invoiceOptions = invoicesByCategory[categoryName] || [];

            let invoiceOptionsHtml = '<option value="">Select Invoice Type</option>';
            invoiceOptions.forEach(invoice => {
                invoiceOptionsHtml += `<option value="${invoice.id}">${invoice.type}</option>`;
            });

            invoiceSelect.innerHTML = invoiceOptionsHtml;
            invoiceSelect.disabled = false;

            const existingCategoryCell = document.querySelector(`.category-cell[data-category="${categoryName}"]`);

            if (existingCategoryCell) {
                row.removeAttribute('data-temp-row');
                row.setAttribute('data-category', categoryName);
                row.querySelector('.category-dropdown-select').closest('td').remove();
                categories[categoryName].count++;
                updateCategoryRowspan(categoryName);
            } else {
                row.removeAttribute('data-temp-row');
                row.setAttribute('data-category', categoryName);
                row.setAttribute('data-is-first-in-category', 'true');

                categories[categoryName] = {
                    count: 1,
                    firstRowIndex: itemNumber
                };

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
                const rows = document.querySelectorAll(`[data-category="${categoryName}"]`);
                rows.forEach(row => row.remove());

                delete categories[categoryName];

                renumberAllItems();
                updateGrandTotal();

                const tbody = document.getElementById('itemsTableBody');
                if (tbody.querySelectorAll('tr').length === 0) {
                    tbody.innerHTML = `
                    <tr class="no-items-row">
                        <td colspan="8">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
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

            if (row.dataset.tempRow === 'true') {
                row.remove();
                renumberAllItems();
                updateGrandTotal();

                const tbody = document.getElementById('itemsTableBody');
                if (tbody.querySelectorAll('tr').length === 0) {
                    tbody.innerHTML = `
                    <tr class="no-items-row">
                        <td colspan="8">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p class="mb-0 fw-bold">No data available</p>
                            <small class="text-muted">Click "Add Item" to get started</small>
                        </td>
                    </tr>
                `;
                }
                return;
            }

            const isFirst = row.dataset.isFirstInCategory === 'true';

            if (categories[categoryName].count === 1) {
                removeCategoryWithItems(categoryName, new Event('click'));
                return;
            }

            if (isFirst) {
                const categoryCell = row.querySelector('.category-cell');
                const nextCategoryRow = row.nextElementSibling;

                if (nextCategoryRow && nextCategoryRow.dataset.category === categoryName) {
                    const newCategoryCell = categoryCell.cloneNode(true);
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
                        <i class="fas fa-inbox fa-3x mb-3"></i>
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

        // Update grand total
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