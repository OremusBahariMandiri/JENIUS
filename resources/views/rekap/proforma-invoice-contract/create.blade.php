@extends('layouts.app')

@section('title', 'Create Proforma Invoice – Contract')

@section('content')
<div class="container-fluid proformaInvoiceContractPage">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0"><i class="fas fa-file-invoice me-2 text-success"></i>Create Proforma Invoice</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('rekap-piutang-contract.index') }}">Customer Receivables</a></li>
                    <li class="breadcrumb-item active">Create Invoice</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('rekap-piutang-contract.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('proforma-invoice-contract.store') }}" id="invoiceForm">
        @csrf
        <input type="hidden" name="id_jo_cont" value="{{ $joContract->id_jo_cont }}">

        {{-- ============================================================ --}}
        {{-- HEADER CARD                                                  --}}
        {{-- ============================================================ --}}
        <div class="card shadow mb-4">
            <div class="card-header text-black" style="background-color: #d1fae5">
                <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Invoice Header</h6>
            </div>
            <div class="card-body p-4">

                <div class="row g-3">

                    {{-- Row 1 --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold required-field">Invoice Number</label>
                        <input type="text" name="no_invoice" class="form-control @error('no_invoice') is-invalid @enderror"
                               value="{{ old('no_invoice', $suggestedNo) }}" required>
                        @error('no_invoice')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Customer</label>
                        <input type="text" name="to_customer" class="form-control"
                               value="{{ old('to_customer', $joContract->contract?->customer?->customer) }}">
                        <input type="hidden" name="id_md_cust" value="{{ $joContract->contract?->id_md_cust }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kapal</label>
                        <input type="text" name="kapal" class="form-control" value="{{ old('kapal') }}">
                    </div>

                    {{-- Row 2 --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title', $joContract->title) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">No. Job Order</label>
                        <input type="text" name="no_jo" class="form-control"
                               value="{{ old('no_jo', $joContract->no_jo_cont) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Vendor Number</label>
                        <input type="text" name="vendor_number" class="form-control" value="{{ old('vendor_number') }}">
                    </div>

                    {{-- Row 3 --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Contract No</label>
                        <input type="text" name="contract_no" class="form-control"
                               value="{{ old('contract_no', $joContract->contract?->no_contract) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">PO Number</label>
                        <input type="text" name="po_number" class="form-control" value="{{ old('po_number') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">SA/GR Number</label>
                        <input type="text" name="sa_gr_number" class="form-control" value="{{ old('sa_gr_number') }}">
                    </div>

                    {{-- Row 4 --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">BA Number</label>
                        <input type="text" name="ba_number" class="form-control" value="{{ old('ba_number') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">VAT Number</label>
                        <input type="text" name="vat_number" class="form-control" value="{{ old('vat_number') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Currency</label>
                        <select name="currency" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="IDR" {{ old('currency') == 'IDR' ? 'selected' : '' }}>IDR</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                    </div>

                    {{-- Row 5: Dates --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal Invoice</label>
                        <input type="date" name="tgl_invoice" class="form-control"
                               value="{{ old('tgl_invoice', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Due Date</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">ETA</label>
                        <input type="date" name="eta" class="form-control" value="{{ old('eta') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">ETD</label>
                        <input type="date" name="etd" class="form-control" value="{{ old('etd') }}">
                    </div>

                    {{-- Row 6 --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">No. Voyage</label>
                        <input type="text" name="no_voyage" class="form-control" value="{{ old('no_voyage') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Jetty</label>
                        <input type="text" name="jetty" class="form-control" value="{{ old('jetty') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Cond of Payment</label>
                        <input type="text" name="cond_of_payment" class="form-control" value="{{ old('cond_of_payment') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Period</label>
                        <input type="text" name="period" class="form-control" value="{{ old('period') }}">
                    </div>

                    {{-- Row 7 --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Bar Code</label>
                        <input type="text" name="bar_code" class="form-control" value="{{ old('bar_code') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Att</label>
                        <input type="text" name="att" class="form-control" value="{{ old('att') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $joContract->contract?->customer?->address) }}</textarea>
                    </div>

                    {{-- Row 8 --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Office Phone</label>
                        <input type="text" name="office_phone" class="form-control" value="{{ old('office_phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mobile Phone</label>
                        <input type="text" name="mobile_phone" class="form-control" value="{{ old('mobile_phone') }}">
                    </div>

                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- ITEM INPUT CARD (same pattern as Edit JO)                   --}}
        {{-- ============================================================ --}}
        <div class="card shadow mb-4" id="itemEditCard">
            <div class="card-header text-black" style="background-color: #dbeafe">
                <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Item Invoice</h6>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    Click the <strong>Edit</strong> button on a row below, fill in the invoice amount and COA, then click
                    <strong>Apply</strong>.
                </p>
                <div class="row g-3 align-items-end" id="itemEditForm" style="display:none!important">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Selected Item</label>
                        <input type="text" id="edit_item_label" class="form-control" readonly
                               style="background:#f8fafc; color:#555;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Selling Price IDR</label>
                        <input type="text" id="edit_selling_idr" class="form-control" readonly
                               style="background:#f8fafc;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Selling Price USD</label>
                        <input type="text" id="edit_selling_usd" class="form-control" readonly
                               style="background:#f8fafc;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold required-field">Nominal Invoice</label>
                        <div class="input-group">
                            <span class="input-group-text">IDR</span>
                            <input type="text" id="edit_nominal_invoice" class="form-control currency-input"
                                   placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Chart of Account</label>
                        <select id="edit_coa" class="form-select select2-coa">
                            <option value="">-- Select COA --</option>
                            @foreach($coas as $coa)
                                <option value="{{ $coa->id_md_chart_of_account }}">
                                    {{ $coa->no_account }} - {{ $coa->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="edit_is_reimbursement">
                            <label class="form-check-label fw-semibold" for="edit_is_reimbursement">
                                Is Reimbursement
                            </label>
                        </div>
                    </div>
                    <div class="col-md-9 d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-secondary" id="btnCancelItemEdit">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-success" id="btnApplyItemEdit">
                            <i class="fas fa-check me-1"></i>Apply
                        </button>
                    </div>
                </div>
                <div id="editPlaceholder" class="text-center text-muted py-2">
                    <i class="fas fa-mouse-pointer me-1"></i>Click <strong>Edit</strong> on a row to fill invoice details.
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- JO ITEMS TABLE (read-only display + hidden inputs)          --}}
        {{-- ============================================================ --}}
        <div class="card shadow mb-4">
            <div class="card-header text-black" style="background-color: #d1fae5">
                <h6 class="mb-0"><i class="fas fa-list me-2"></i>JO Contract Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle small mb-0" id="invoiceItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width:4%">#</th>
                                <th style="width:14%">Category</th>
                                <th style="width:20%">Item / Description</th>
                                <th style="width:13%">Selling IDR</th>
                                <th style="width:13%">Selling USD</th>
                                <th style="width:13%">Nominal Invoice</th>
                                <th style="width:13%">COA</th>
                                <th style="width:6%" class="text-center">Reimb.</th>
                                <th style="width:8%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($joContract->items as $idx => $item)
                                @php
                                    $no = $idx + 1;
                                    $itemId = $item->id_jo_cont_item;
                                    $categoryName = $item->invoice->invoice_ctg ?? '-';
                                    $itemName = $item->invoice->invoice_typ ?? '-';
                                    $sellingIdr = (float)($item->hargajual_idr ?? 0);
                                    $sellingUsd = (float)($item->pendapatan_usd ?? 0);
                                    // restore old input if validation failed
                                    $oldNominal = old("items.{$idx}.nominal_invoice", '');
                                    $oldCoa     = old("items.{$idx}.id_md_chart_of_account", '');
                                    $oldReimb   = old("items.{$idx}.is_reimbursement", '');
                                @endphp
                                <tr class="item-table-row"
                                    data-idx="{{ $idx }}"
                                    data-item-id="{{ $itemId }}"
                                    data-category="{{ $categoryName }}"
                                    data-item-name="{{ $itemName }}"
                                    data-selling-idr="{{ $sellingIdr }}"
                                    data-selling-usd="{{ $sellingUsd }}">

                                    <td>{{ $no }}</td>
                                    <td>{{ $categoryName }}</td>
                                    <td>
                                        {{ $itemName }}
                                        @if($item->note)
                                            <div class="text-muted fst-italic small">{{ $item->note }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($sellingIdr > 0)
                                            {{ number_format($sellingIdr, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($sellingUsd > 0)
                                            {{ number_format($sellingUsd, 2, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end td-nominal-{{ $idx }}">
                                        <span class="display-nominal text-muted fst-italic">-</span>
                                        {{-- Hidden inputs for submission --}}
                                        <input type="hidden" name="items[{{ $idx }}][id_jo_cont_item]" value="{{ $itemId }}">
                                        <input type="hidden" name="items[{{ $idx }}][nominal_invoice]" class="hidden-nominal" value="{{ $oldNominal }}">
                                        <input type="hidden" name="items[{{ $idx }}][id_md_chart_of_account]" class="hidden-coa" value="{{ $oldCoa }}">
                                        <input type="hidden" name="items[{{ $idx }}][is_reimbursement]" class="hidden-reimb" value="{{ $oldReimb }}">
                                    </td>
                                    <td class="td-coa-{{ $idx }}">
                                        <span class="display-coa text-muted fst-italic small">-</span>
                                    </td>
                                    <td class="text-center td-reimb-{{ $idx }}">
                                        <span class="display-reimb">-</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-item"
                                                data-idx="{{ $idx }}">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No items found on this JO Contract.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('rekap-piutang-contract.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Save Proforma Invoice
            </button>
        </div>

    </form>
</div>
@endsection

@push('styles')
<style>
    .required-field::after { content: ' *'; color: red; }

    .currency-input { text-align: right; }

    #itemEditForm { display: flex !important; flex-wrap: wrap; }

    .item-table-row.editing-row {
        background: #eff6ff !important;
    }

    .select2-coa { width: 100% !important; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {

    // Init Select2 for COA
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-coa').select2({
            placeholder: '-- Select COA --',
            allowClear: true,
            dropdownParent: $('#itemEditCard'),
        });
    }

    // Currency formatting
    function formatCurrency(val) {
        let num = parseFloat(String(val).replace(/\./g, '').replace(',', '.')) || 0;
        return num.toLocaleString('id-ID');
    }

    $('#edit_nominal_invoice').on('input', function () {
        let raw = $(this).val().replace(/\./g, '').replace(/[^0-9]/g, '');
        let num = parseInt(raw) || 0;
        $(this).val(num.toLocaleString('id-ID'));
    });

    let currentEditIdx = null;

    // Click Edit button
    $(document).on('click', '.btn-edit-item', function () {
        let idx = $(this).data('idx');
        let row = $(`.item-table-row[data-idx="${idx}"]`);

        // Highlight row
        $('.item-table-row').removeClass('editing-row');
        row.addClass('editing-row');

        // Populate edit form
        let category  = row.data('category');
        let itemName  = row.data('item-name');
        let selIdr    = row.data('selling-idr');
        let selUsd    = row.data('selling-usd');

        $('#edit_item_label').val(`[${category}] ${itemName}`);
        $('#edit_selling_idr').val(selIdr > 0 ? parseFloat(selIdr).toLocaleString('id-ID') : '-');
        $('#edit_selling_usd').val(selUsd > 0 ? parseFloat(selUsd).toLocaleString('en-US', {minimumFractionDigits:2}) : '-');

        // Load existing saved values
        let existingNominal = row.find('.hidden-nominal').val();
        let existingCoa     = row.find('.hidden-coa').val();
        let existingReimb   = row.find('.hidden-reimb').val();

        $('#edit_nominal_invoice').val(existingNominal ? parseFloat(existingNominal).toLocaleString('id-ID') : '');
        $('#edit_is_reimbursement').prop('checked', existingReimb == '1');

        if (typeof $.fn.select2 !== 'undefined') {
            $('#edit_coa').val(existingCoa || '').trigger('change');
        } else {
            $('#edit_coa').val(existingCoa || '');
        }

        currentEditIdx = idx;

        // Show edit form, hide placeholder
        $('#itemEditForm').show();
        $('#editPlaceholder').hide();

        // Scroll to edit card
        $('html, body').animate({ scrollTop: $('#itemEditCard').offset().top - 80 }, 300);
    });

    // Apply
    $('#btnApplyItemEdit').on('click', function () {
        if (currentEditIdx === null) return;

        let rawVal = $('#edit_nominal_invoice').val().replace(/\./g, '').replace(',', '.');
        let nominal = parseFloat(rawVal) || 0;
        let coaId   = $('#edit_coa').val() || '';
        let coaTxt  = $('#edit_coa option:selected').text() || '-';
        let reimb   = $('#edit_is_reimbursement').is(':checked') ? '1' : '';

        let row = $(`.item-table-row[data-idx="${currentEditIdx}"]`);

        // Update hidden inputs
        row.find('.hidden-nominal').val(nominal);
        row.find('.hidden-coa').val(coaId);
        row.find('.hidden-reimb').val(reimb);

        // Update display cells
        row.find('.display-nominal').text(nominal > 0 ? nominal.toLocaleString('id-ID') : '-').removeClass('text-muted fst-italic');
        row.find('.display-coa').text(coaId ? coaTxt : '-').removeClass('text-muted fst-italic');
        row.find('.display-reimb').html(reimb == '1' ? '<span class="badge bg-warning text-dark">Yes</span>' : '<span class="text-muted">No</span>');

        // Reset form
        row.removeClass('editing-row');
        $('#itemEditForm').hide();
        $('#editPlaceholder').show();
        currentEditIdx = null;
        $('#edit_nominal_invoice').val('');
        $('#edit_item_label').val('');
        $('#edit_selling_idr').val('');
        $('#edit_selling_usd').val('');
        $('#edit_is_reimbursement').prop('checked', false);
        if (typeof $.fn.select2 !== 'undefined') {
            $('#edit_coa').val('').trigger('change');
        }
    });

    // Cancel
    $('#btnCancelItemEdit').on('click', function () {
        if (currentEditIdx !== null) {
            $(`.item-table-row[data-idx="${currentEditIdx}"]`).removeClass('editing-row');
        }
        $('#itemEditForm').hide();
        $('#editPlaceholder').show();
        currentEditIdx = null;
    });

    // On page load: restore old values from server (validation failed)
    @foreach($joContract->items as $idx => $item)
        @php
            $oldNominal = old("items.{$idx}.nominal_invoice", '');
            $oldCoa     = old("items.{$idx}.id_md_chart_of_account", '');
            $oldCoaTxt  = '';
            $oldReimb   = old("items.{$idx}.is_reimbursement", '');
            if ($oldCoa) {
                $coaObj = $coas->firstWhere('id_md_chart_of_account', $oldCoa);
                $oldCoaTxt = $coaObj ? $coaObj->no_account . ' - ' . $coaObj->account_name : '';
            }
        @endphp
        @if($oldNominal)
            (function(){
                let row = $('.item-table-row[data-idx="{{ $idx }}"]');
                row.find('.display-nominal').text('{{ number_format((float)$oldNominal, 0, ",", ".") }}').removeClass('text-muted fst-italic');
                @if($oldCoaTxt)
                    row.find('.display-coa').text('{{ addslashes($oldCoaTxt) }}').removeClass('text-muted fst-italic');
                @endif
                row.find('.display-reimb').html('{{ $oldReimb == "1" ? "<span class=\"badge bg-warning text-dark\">Yes</span>" : "<span class=\"text-muted\">No</span>" }}');
            })();
        @endif
    @endforeach

});
</script>
@endpush
