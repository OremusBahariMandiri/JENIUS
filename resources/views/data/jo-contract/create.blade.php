@extends('layouts.app')

@section('title', 'Add JO Contract')

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
        background-color: #2c3e50;
        color: white;
        border: 1px solid #34495e;
        padding: 14px 10px;
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

    .table-items tbody tr:hover td:not(.category-header-row) {
        background-color: #f8f9fa;
    }

    /* Category Header Row */
    .category-header-row {
        background-color: #e8eaf6 !important;
        border-left: 4px solid #5c6bc0 !important;
    }

    .category-header-content {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 5px 0;
    }

    .category-header-content input,
    .category-header-content select {
        flex: 1;
        max-width: 300px;
    }

    .category-header-content .btn-remove-category {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .category-header-content .btn-remove-category:hover {
        background-color: #c82333;
    }

    .category-header-content .btn-add-item-category {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .category-header-content .btn-add-item-category:hover {
        background-color: #218838;
    }

    .table-items input,
    .table-items select,
    .table-items textarea {
        font-size: 0.875rem;
        padding: 0.5rem;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }

    .table-items textarea {
        min-height: 60px;
        resize: vertical;
    }

    .currency-group {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .currency-label {
        background-color: #34495e;
        color: white;
        padding: 0.5rem;
        font-size: 0.8rem;
        border-radius: 4px 0 0 4px;
        min-width: 50px;
        text-align: center;
        font-weight: 600;
    }

    .currency-input {
        border-radius: 0 4px 4px 0 !important;
    }

    .btn-remove-row {
        padding: 0.4rem 0.6rem;
        font-size: 0.875rem;
        border-radius: 4px;
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
    }

    .action-buttons .btn {
        border-radius: 6px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
    }

    .item-number-cell {
        text-align: center;
        font-weight: 600;
        color: #2c3e50;
        background-color: #ecf0f1 !important;
    }

    .calculation-mode-group {
        display: flex;
        gap: 10px;
    }

    .calculation-mode-group label {
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .calculation-mode-badge {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .mode-idr {
        background-color: #27ae60;
        color: white;
    }

    .mode-usd {
        background-color: #3498db;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800">Add JO Contract</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('jo-contract.index') }}">JO Contract</a></li>
                <li class="breadcrumb-item active">Add</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('jo-contract.store') }}" method="POST" id="joContractForm">
        @csrf

        <div class="row">
            <div class="col-lg-12">
                <!-- JO Contract Info Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>JO Contract Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Contract</label>
                                <select name="id_md_cont" id="id_md_cont" class="form-select @error('id_md_cont') is-invalid @enderror" required>
                                    <option value="">Select Contract</option>
                                    @foreach($contracts as $contract)
                                    <option value="{{ $contract->id_md_cont }}" {{ old('id_md_cont') == $contract->id_md_cont ? 'selected' : '' }}>
                                        {{ $contract->no_contract }} - {{ $contract->contract }}
                                        @if($contract->customer)
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
                                <select name="id_md_area" id="id_md_area" class="form-select @error('id_md_area') is-invalid @enderror" required>
                                    <option value="">Select Area</option>
                                    @foreach($areas as $area)
                                    <option value="{{ $area->id_md_area }}" {{ old('id_md_area') == $area->id_md_area ? 'selected' : '' }}>
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
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="JO contract title" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="3" placeholder="Additional notes (optional)">{{ old('note') }}</textarea>
                            @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- JO Contract Items Card -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>JO Contract Items</h5>
                            <div class="d-flex gap-3 align-items-center">
                                <div>
                                    <label class="form-label mb-1 small">Kurs Date</label>
                                    <input type="date" name="global_tgl_kurs_usd" id="global_tgl_kurs_usd" class="form-control form-control-sm" style="min-width: 150px;" required>
                                </div>
                                <div>
                                    <label class="form-label mb-1 small">Kurs Rate</label>
                                    <input type="number" name="global_kurs_usd" id="global_kurs_usd" class="form-control form-control-sm" step="0.01" min="0" value="17600" placeholder="17,600.00" style="min-width: 130px;" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="action-buttons">
                            <button type="button" class="btn btn-primary" id="addCategoryBtn">
                                <i class="fas fa-plus me-1"></i>Add Category
                            </button>
                            <button type="button" class="btn btn-danger" id="resetAllBtn">
                                <i class="fas fa-redo me-1"></i>Reset Item
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-items table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">Deskripsi</th>
                                        <th style="width: 15%;">Pendapatan IDR</th>
                                        <th style="width: 15%;">Pendapatan USD</th>
                                        <th style="width: 15%;">HPP (Biaya Operasional)</th>
                                        <th style="width: 15%;">Harga Jual (IDR)</th>

                                        <th style="width: 5%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <tr class="no-items-row">
                                        <td colspan="7">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p class="mb-0 fw-bold">No data available in table</p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-footer">
                                    <tr>
                                        <td class="text-end"><strong>Total</strong></td>
                                        <td>
                                            <div class="currency-group">
                                                <span class="currency-label">IDR</span>
                                                <span id="footerTotalIDR" style="color: white; font-weight: 700;">0,00</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group">
                                                <span class="currency-label">USD</span>
                                                <span id="footerTotalUSD" style="color: white; font-weight: 700;">0,00</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group">
                                                <span class="currency-label">IDR</span>
                                                <span id="footerTotalHPP" style="color: white; font-weight: 700;">0,00</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="currency-group">
                                                <span class="currency-label">IDR</span>
                                                <span id="footerTotalSelling" style="color: white; font-weight: 700;">0,00</span>
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
                        <i class="fas fa-save me-2"></i>Save JO Contract
                    </button>
                    <a href="{{ route('jo-contract.index') }}" class="btn btn-secondary btn-lg">
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
    let globalItemNumber = 0;
    let categoryIndex = 0;

    // Group invoices by category
    const invoicesByCategory = {
        @foreach($invoices->groupBy('invoice_ctg') as $category => $invoiceGroup)
        '{{ $category }}': [
            @foreach($invoiceGroup as $invoice)
            {
                id: '{{ $invoice->id_md_invoice }}',
                type: '{{ $invoice->invoice_typ }}'
            },
            @endforeach
        ],
        @endforeach
    };

    const allCategories = Object.keys(invoicesByCategory);

    // Add Category Button
    document.getElementById('addCategoryBtn').addEventListener('click', function() {
        addCategoryHeader();
    });

    // Reset All Button
    document.getElementById('resetAllBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to remove all items?')) {
            document.getElementById('itemsTableBody').innerHTML = `
                <tr class="no-items-row">
                    <td colspan="7">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p class="mb-0 fw-bold">No data available in table</p>
                    </td>
                </tr>
            `;
            globalItemNumber = 0;
            categoryIndex = 0;
            updateGrandTotal();
        }
    });

    // Add category header row
    function addCategoryHeader() {
        // Remove "no items" row if exists
        const noItemsRow = document.querySelector('.no-items-row');
        if (noItemsRow) {
            noItemsRow.remove();
        }

        const tbody = document.getElementById('itemsTableBody');
        const headerRow = document.createElement('tr');
        headerRow.classList.add('category-header-row');
        headerRow.setAttribute('data-category-index', categoryIndex);

        // Build category options
        let categoryOptionsHtml = '<option value="">-- Select Invoice Category --</option>';
        allCategories.forEach(cat => {
            categoryOptionsHtml += `<option value="${cat}">${cat}</option>`;
        });

        headerRow.innerHTML = `
            <td colspan="7" class="category-header-row">
                <div class="category-header-content">
                    <select class="form-select category-select" data-category-index="${categoryIndex}" required>
                        ${categoryOptionsHtml}
                    </select>
                    <button type="button" class="btn-add-item-category" onclick="addItemToCategory(${categoryIndex})" disabled>
                        <i class="fas fa-plus me-1"></i>Add Item
                    </button>
                    <button type="button" class="btn-remove-category" onclick="removeCategory(${categoryIndex})">
                        <i class="fas fa-times me-1"></i>Remove
                    </button>
                </div>
            </td>
        `;

        tbody.appendChild(headerRow);

        // Attach category select event
        const categorySelect = headerRow.querySelector('.category-select');
        categorySelect.addEventListener('change', function() {
            const addButton = headerRow.querySelector('.btn-add-item-category');
            if (this.value) {
                addButton.disabled = false;
            } else {
                addButton.disabled = true;
            }
        });

        categoryIndex++;
    }

    // Add item to category
    function addItemToCategory(catIndex) {
        const headerRow = document.querySelector(`[data-category-index="${catIndex}"]`);
        const categorySelect = headerRow.querySelector('.category-select');
        const selectedCategory = categorySelect.value;

        if (!selectedCategory) {
            alert('Please select a category first!');
            return;
        }

        globalItemNumber++;

        const tbody = document.getElementById('itemsTableBody');
        const newRow = document.createElement('tr');
        newRow.classList.add('item-row');
        newRow.setAttribute('data-item-number', globalItemNumber);
        newRow.setAttribute('data-category-index', catIndex);

        // Get invoices for this category
        const invoiceOptions = invoicesByCategory[selectedCategory] || [];
        let invoiceOptionsHtml = '<option value="">Select Invoice Type</option>';
        invoiceOptions.forEach(invoice => {
            invoiceOptionsHtml += `<option value="${invoice.id}">${invoice.type}</option>`;
        });

        newRow.innerHTML = `
            <td>
                <input type="hidden" name="items[${globalItemNumber}][invoice_ctg]" value="${selectedCategory}">
                <select name="items[${globalItemNumber}][id_md_invoice]" class="form-select mt-2" required>
                    ${invoiceOptionsHtml}
                </select>
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">IDR</span>
                    <input type="number" name="items[${globalItemNumber}][pendapatan_idr]" class="form-control currency-input revenue-idr" step="0.01" min="0" value="0" required>
                </div>
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">USD</span>
                    <input type="number" name="items[${globalItemNumber}][pendapatan_usd]" class="form-control currency-input revenue-usd" step="0.01" min="0" value="0" required>
                </div>
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">IDR</span>
                    <input type="number" name="items[${globalItemNumber}][hpp_ops]" class="form-control currency-input hpp" step="0.01" min="0" value="0" required>
                </div>
            </td>
            <td>
                <div class="currency-group">
                    <span class="currency-label">IDR</span>
                    <input type="number" name="items[${globalItemNumber}][hargajual_idr]" class="form-control currency-input selling-price" step="0.01" min="0" value="0" readonly style="background-color: #e9ecef;">
                </div>
            </td>

            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-remove-row" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        // Find all rows with same category index
        const categoryRows = tbody.querySelectorAll(`tr[data-category-index="${catIndex}"]`);
        const lastRow = categoryRows[categoryRows.length - 1];

        // Insert after last row of this category
        if (lastRow.nextSibling) {
            tbody.insertBefore(newRow, lastRow.nextSibling);
        } else {
            tbody.appendChild(newRow);
        }

        attachItemEventListeners(newRow);
    }

    // Remove category and all its items
    function removeCategory(catIndex) {
        if (confirm('Remove this category and all its items?')) {
            const rows = document.querySelectorAll(`[data-category-index="${catIndex}"]`);
            rows.forEach(row => row.remove());

            updateGrandTotal();

            // Check if table is empty
            const tbody = document.getElementById('itemsTableBody');
            if (tbody.querySelectorAll('tr').length === 0) {
                tbody.innerHTML = `
                    <tr class="no-items-row">
                        <td colspan="7">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p class="mb-0 fw-bold">No data available in table</p>
                        </td>
                    </tr>
                `;
            }
        }
    }

    // Remove item
    function removeItem(button) {
        const row = button.closest('tr');
        row.remove();

        updateGrandTotal();

        // Check if table is empty
        const tbody = document.getElementById('itemsTableBody');
        const itemRows = tbody.querySelectorAll('.item-row');
        if (itemRows.length === 0) {
            tbody.innerHTML = `
                <tr class="no-items-row">
                    <td colspan="7">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p class="mb-0 fw-bold">No data available in table</p>
                    </td>
                </tr>
            `;
        }
    }

    // Attach event listeners to item
    function attachItemEventListeners(row) {
        const revenueIDR = row.querySelector('.revenue-idr');
        const revenueUSD = row.querySelector('.revenue-usd');
        const sellingPrice = row.querySelector('.selling-price');
        const calcModeRadios = row.querySelectorAll('.calc-mode-radio');
        const hpp = row.querySelector('.hpp');

        function calculateSellingPrice() {
            const kursRate = parseFloat(document.getElementById('global_kurs_usd').value) || 0;
            const mode = row.querySelector('.calc-mode-radio:checked').value;

            if (mode === 'idr') {
                const idrValue = parseFloat(revenueIDR.value) || 0;
                sellingPrice.value = idrValue.toFixed(2);
            } else {
                const usdValue = parseFloat(revenueUSD.value) || 0;
                sellingPrice.value = (usdValue * kursRate).toFixed(2);
            }

            updateGrandTotal();
        }

        revenueIDR.addEventListener('input', calculateSellingPrice);
        revenueUSD.addEventListener('input', calculateSellingPrice);
        hpp.addEventListener('input', updateGrandTotal);

        calcModeRadios.forEach(radio => {
            radio.addEventListener('change', calculateSellingPrice);
        });
    }

    // Global kurs change handler
    document.getElementById('global_kurs_usd').addEventListener('input', function() {
        const allRows = document.querySelectorAll('.item-row');
        allRows.forEach(r => {
            const usdInput = r.querySelector('.revenue-usd');
            const mode = r.querySelector('.calc-mode-radio:checked')?.value;
            const selling = r.querySelector('.selling-price');

            if (mode === 'usd' && usdInput && selling) {
                const usdValue = parseFloat(usdInput.value) || 0;
                const kurs = parseFloat(this.value) || 0;
                selling.value = (usdValue * kurs).toFixed(2);
            }
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
            totalRevenueIDR += parseFloat(row.querySelector('.revenue-idr')?.value) || 0;
            totalRevenueUSD += parseFloat(row.querySelector('.revenue-usd')?.value) || 0;
            totalHPP += parseFloat(row.querySelector('.hpp')?.value) || 0;
            totalSelling += parseFloat(row.querySelector('.selling-price')?.value) || 0;
        });

        document.getElementById('footerTotalIDR').textContent = formatNumber(totalRevenueIDR);
        document.getElementById('footerTotalUSD').textContent = formatNumber(totalRevenueUSD);
        document.getElementById('footerTotalHPP').textContent = formatNumber(totalHPP);
        document.getElementById('footerTotalSelling').textContent = formatNumber(totalSelling);
    }

    // Format number
    function formatNumber(amount) {
        return amount.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Form submission validation
    document.getElementById('joContractForm').addEventListener('submit', function(e) {
        const itemRows = document.querySelectorAll('.item-row');

        if (itemRows.length === 0) {
            e.preventDefault();
            alert('Please add at least one item!');
            return false;
        }

        // Check if all categories are selected
        const categorySelects = document.querySelectorAll('.category-select');
        let allSelected = true;
        categorySelects.forEach(select => {
            if (!select.value) {
                allSelected = false;
            }
        });

        if (!allSelected) {
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