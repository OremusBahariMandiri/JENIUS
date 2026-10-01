@extends('layouts.app')

@section('title', 'Add LPJ General')

@push('styles')
<style>
    .lpjGenCreatePage .card { border:none; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,.08); }
    .lpjGenCreatePage .card-header { border-radius:10px 10px 0 0!important; padding:1rem 1.5rem; font-weight:600; }
    .lpjGenCreatePage .form-control:focus,
    .lpjGenCreatePage .form-select:focus { border-color:#10b981; box-shadow:0 0 0 0.2rem rgba(16,185,129,.25); }
    .required-field::after { content:" *"; color:#dc3545; font-weight:600; }

    .floating-badge-alert {
        position:fixed; top:80px; right:30px; z-index:9999; min-width:260px;
        padding:15px 20px; border-radius:12px; box-shadow:0 8px 25px rgba(0,0,0,.2);
        display:none; animation:slideInRight .4s ease-out;
    }
    .floating-badge-alert.show { display:flex; align-items:center; gap:12px; }
    .floating-badge-alert.alert-saving  { background:linear-gradient(135deg,#fbbf24,#f59e0b); color:#fff; }
    .floating-badge-alert.alert-success { background:linear-gradient(135deg,#10b981,#059669); color:#fff; }
    .floating-badge-alert.alert-error   { background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; }
    .floating-badge-alert i { font-size:1.3rem; }
    .floating-badge-alert .alert-text { flex:1; font-weight:600; font-size:.95rem; }
    @keyframes slideInRight { from{transform:translateX(400px);opacity:0} to{transform:translateX(0);opacity:1} }
    @keyframes slideOutRight { from{transform:translateX(0);opacity:1} to{transform:translateX(400px);opacity:0} }
    .floating-badge-alert.hiding { animation:slideOutRight .4s ease-in; }

    .kasbon-select-table { border-radius:10px; overflow:hidden; border:1px solid #dee2e6; }
    .kasbon-select-table thead th { background:#2c3e50; color:#fff; font-weight:600; font-size:.8rem; padding:10px 12px; border:none; }
    .kasbon-select-table tbody td { padding:10px 12px; vertical-align:middle; font-size:.875rem; border-bottom:1px solid #f1f5f9; }
    .kasbon-select-table tbody tr:hover td { background:#eff6ff; }
    .kasbon-select-table tbody tr.selected-row td { background:#d1fae5; }
    .kasbon-select-table .form-check-input:checked { background-color:#10b981; border-color:#10b981; }
    .kasbon-amount-badge { background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; border-radius:6px; padding:3px 8px; font-size:.78rem; font-weight:600; }
    .kasbon-empty-state { text-align:center; padding:40px 20px; color:#9ca3af; }

    .detail-item-row td { font-size:.85rem; padding:8px 12px; }
    .detail-item-row:hover td { background:#eff6ff; }
    .detail-thead th { background:#2c3e50; color:#fff; font-size:.8rem; padding:10px 12px; }

    .final-save-section {
        position:sticky; bottom:0; background:#fff; padding:18px 20px;
        box-shadow:0 -4px 20px rgba(0,0,0,.1); border-radius:12px 12px 0 0; margin-top:30px; z-index:100;
    }
    .btn-final-back {
        background:linear-gradient(135deg,#868686,#5e5e5e); border:none; color:#fff;
        padding:13px 35px; border-radius:10px; font-weight:700; font-size:1rem; transition:all .3s;
    }
    .btn-final-back:hover { transform:translateY(-2px); color:#fff; }
</style>
@endpush

@section('content')

<div id="floatingBadgeAlert" class="floating-badge-alert">
    <i id="alertIcon" class="fas fa-circle-notch fa-spin"></i>
    <div id="alertText" class="alert-text">Processing...</div>
</div>

{{-- MODAL: Kasbon Detail --}}
<div class="modal fade" id="modalKasbonDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#d1fae5; border-bottom:1px solid #bbf7d0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-info-circle me-2" style="color:#10b981;"></i>
                    CA Detail — <span id="modalKasbonNo">—</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div style="background:#f8f9fa; border-radius:8px; padding:12px 14px;">
                            <div style="font-size:.72rem; color:#9ca3af; font-weight:600; text-transform:uppercase;">CA Number</div>
                            <div class="fw-bold mt-1" id="detailKasbonNo">—</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background:#f8f9fa; border-radius:8px; padding:12px 14px;">
                            <div style="font-size:.72rem; color:#9ca3af; font-weight:600; text-transform:uppercase;">CA Date</div>
                            <div class="fw-bold mt-1" id="detailKasbonTgl">—</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background:#d1fae5; border-radius:8px; padding:12px 14px;">
                            <div style="font-size:.72rem; color:#065f46; font-weight:600; text-transform:uppercase;">Total CA</div>
                            <div class="fw-bold mt-1" style="color:#065f46;" id="detailKasbonTotal">—</div>
                        </div>
                    </div>
                </div>
                <h6 class="fw-bold mb-2" style="color:#065f46; font-size:.85rem; text-transform:uppercase; letter-spacing:.5px;">
                    <i class="fas fa-list me-1"></i> Items
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="detail-thead">
                            <tr>
                                <th>No</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th class="text-end">CA Amount (IDR)</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsBody">
                            <tr><td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-circle-notch fa-spin me-2"></i> Loading...
                            </td></tr>
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8f9fa; font-weight:700;">
                                <td colspan="3" class="text-center">TOTAL</td>
                                <td class="text-end" id="detailTotalKasbon">—</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #bbf7d0;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid lpjGenCreatePage">
    <div class="row">
        <div class="col-lg-12">

            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                    style="background-color:#d1fae5">
                    <span class="fw-bold">
                        <i class="fas fa-plus-circle me-2" style="color:#10b981;"></i>Add LPJ General
                    </span>
                    <a href="{{ route('lpj-gen.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <form id="lpjHeaderForm" enctype="multipart/form-data">
                @csrf

                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color:#d1fae5">
                        <h6 class="mb-0">
                            <i class="fas fa-file-invoice me-2" style="color:#10b981;"></i>LPJ Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            {{-- No LPJ --}}
                            <div class="col-md-6">
                                <label class="form-label">LPJ Number</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#10b981;border-color:#10b981;">
                                        <i class="fas fa-file-alt text-white"></i>
                                    </span>
                                    <input type="text" class="form-control fw-bold" value="{{ $previewNoLpj }}"
                                        readonly style="background:#e9ecef;color:#2c3e50;letter-spacing:1px;">
                                </div>
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Auto-generated on save</small>
                            </div>

                            {{-- Date --}}
                            <div class="col-md-6">
                                <label class="form-label required-field">Date</label>
                                <input type="date" name="date" id="date" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            {{-- SELECT KASBON --}}
                            <div class="col-md-12">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-semibold mb-0 required-field">
                                        Select Cash Advances (General) to Include
                                    </label>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAllCa">
                                            <i class="fas fa-check-double me-1"></i> Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeselectAllCa">
                                            <i class="fas fa-times me-1"></i> Deselect All
                                        </button>
                                    </div>
                                </div>

                                <div id="kasbonLoadingState" class="kasbon-empty-state">
                                    <i class="fas fa-circle-notch fa-spin fa-2x mb-2" style="color:#10b981;"></i>
                                    <p class="mb-0 small">Loading cash advances...</p>
                                </div>

                                <div class="table-responsive kasbon-select-table" id="kasbonTableWrapper" style="display:none;">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">
                                                    <input type="checkbox" id="checkAllCa" class="form-check-input">
                                                </th>
                                                <th>CA Number</th>
                                                <th>Department</th>
                                                <th class="text-center">CA Date</th>
                                                <th class="text-center">Items</th>
                                                <th class="text-end">Total CA Amount (IDR)</th>
                                                <th class="text-center" width="8%">Info</th>
                                            </tr>
                                        </thead>
                                        <tbody id="kasbonSelectBody"></tbody>
                                        <tfoot>
                                            <tr style="background:#d1fae5; font-weight:700;">
                                                <td colspan="5" class="text-center" style="font-size:.85rem;">SELECTED TOTAL</td>
                                                <td class="text-end" style="color:#065f46;" id="kasbonSelectedTotal">IDR 0,00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div id="kasbonEmptyState" class="kasbon-empty-state" style="display:none;">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0 small">No General Cash Advances found. Please create one first.</p>
                                </div>
                            </div>

                            {{-- Note --}}
                            <div class="col-md-12">
                                <label class="form-label">Remark</label>
                                <textarea name="note" id="note" class="form-control" rows="3"
                                    placeholder="Enter remark..."></textarea>
                            </div>

                            {{-- Evidence --}}
                            <div class="col-md-12">
                                <label class="form-label">Evidence File</label>
                                <input type="file" name="evidence" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">PDF / JPG / PNG, max 5MB</small>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn px-4 fw-bold" id="btnSaveHeader"
                                style="background:#10b981; color:#fff; border:none; border-radius:8px; padding:12px 30px;">
                                <i class="fas fa-save me-1"></i> Save LPJ Header
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="final-save-section">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('lpj-gen.index') }}" class="btn btn-final-back">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let kasbonData = [];

function formatNumber(v) {
    return parseFloat(v || 0).toLocaleString('id-ID', { minimumFractionDigits:2, maximumFractionDigits:2 });
}

function showFloatingAlert(type, message) {
    const $a = $('#floatingBadgeAlert'), $i = $('#alertIcon');
    $a.removeClass('alert-saving alert-success alert-error hiding');
    if (type === 'saving')  { $a.addClass('alert-saving');  $i.attr('class','fas fa-circle-notch fa-spin'); }
    if (type === 'success') { $a.addClass('alert-success'); $i.attr('class','fas fa-check-circle'); }
    if (type === 'error')   { $a.addClass('alert-error');   $i.attr('class','fas fa-exclamation-circle'); }
    $('#alertText').text(message);
    $a.addClass('show');
    if (type !== 'saving') setTimeout(() => { $a.addClass('hiding'); setTimeout(() => $a.removeClass('show hiding'), 400); }, 3000);
}

function updateSelectedTotal() {
    let total = 0;
    $('.kasbon-check:checked').each(function() { total += parseFloat($(this).data('amount')) || 0; });
    $('#kasbonSelectedTotal').text('IDR ' + formatNumber(total));
    $('.kasbon-row').each(function() {
        $(this).toggleClass('selected-row', $(this).find('.kasbon-check').is(':checked'));
    });
}

function onKasbonCheck(el) {
    updateSelectedTotal();
    const total = $('.kasbon-check').length, checked = $('.kasbon-check:checked').length;
    $('#checkAllCa').prop('indeterminate', checked > 0 && checked < total).prop('checked', checked === total);
}

function showKasbonDetail(kasbonId, idx) {
    const k = kasbonData[idx];
    $('#modalKasbonNo, #detailKasbonNo').text(kasbonId);
    $('#detailKasbonTgl').text(k.tgl_kasbon ?? '—');
    $('#detailKasbonTotal').text('IDR ' + formatNumber(k.total_kasbon));
    $('#detailItemsBody').html(`<tr><td colspan="4" class="text-center py-3 text-muted"><i class="fas fa-circle-notch fa-spin me-2"></i> Loading...</td></tr>`);
    $('#modalKasbonDetail').modal('show');

    $.ajax({
        url: '{{ route("lpj-gen.kasbons") }}',
        method: 'GET',
        data: { detail_kasbon: kasbonId },
        success: function(r) {
            if (r.success && r.kasbon_detail) {
                const items = r.kasbon_detail.items || [];
                if (!items.length) {
                    $('#detailItemsBody').html(`<tr><td colspan="4" class="text-center py-3 text-muted">No items</td></tr>`);
                    return;
                }
                let html = '', total = 0;
                items.forEach((item, i) => {
                    total += parseFloat(item.nilai_kasbon) || 0;
                    html += `<tr class="detail-item-row">
                        <td class="text-center text-muted">${i+1}</td>
                        <td>${item.invoice_ctg ?? '—'}</td>
                        <td class="fw-semibold">${item.invoice_typ ?? '—'}</td>
                        <td class="text-end">IDR ${formatNumber(item.nilai_kasbon)}</td>
                    </tr>`;
                });
                $('#detailItemsBody').html(html);
                $('#detailTotalKasbon').text('IDR ' + formatNumber(total));
            }
        }
    });
}

$(document).ready(function() {
    // Load all kasbon gen
    $.ajax({
        url: '{{ route("lpj-gen.kasbons") }}',
        method: 'GET',
        success: function(r) {
            $('#kasbonLoadingState').hide();
            if (!r.success || !r.data.length) {
                $('#kasbonEmptyState').show();
                return;
            }

            kasbonData = r.data;
            const $tbody = $('#kasbonSelectBody');
            $tbody.empty();

            r.data.forEach((k, idx) => {
                $tbody.append(`
                    <tr class="kasbon-row" id="krow_${idx}">
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input kasbon-check"
                                name="kasbon_ids[]" value="${k.id_kasbon_gen}" checked
                                data-idx="${idx}" data-amount="${k.total_kasbon}"
                                onchange="onKasbonCheck(this)">
                        </td>
                        <td class="fw-semibold" style="color:#2c3e50;">${k.id_kasbon_gen}</td>
                        <td class="text-muted small">${k.dep}</td>
                        <td class="text-center text-muted">${k.tgl_kasbon ?? '—'}</td>
                        <td class="text-center"><span class="kasbon-amount-badge">${k.items_count} item${k.items_count !== 1 ? 's' : ''}</span></td>
                        <td class="text-end fw-semibold">IDR ${formatNumber(k.total_kasbon)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm" title="View Detail"
                                style="background:#d1fae5;color:#065f46;border:none;border-radius:6px;padding:4px 8px;"
                                onclick="showKasbonDetail('${k.id_kasbon_gen}', ${idx})">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </td>
                    </tr>`);
            });

            $('#checkAllCa').prop('checked', true);
            $('#kasbonTableWrapper').show();
            updateSelectedTotal();
        },
        error: () => { $('#kasbonLoadingState').hide(); $('#kasbonEmptyState').show(); }
    });

    $('#checkAllCa, #btnSelectAllCa').on('click', function() {
        $('.kasbon-check').prop('checked', true);
        $('#checkAllCa').prop('indeterminate', false).prop('checked', true);
        updateSelectedTotal();
    });
    $('#btnDeselectAllCa').on('click', function() {
        $('.kasbon-check').prop('checked', false);
        $('#checkAllCa').prop('indeterminate', false).prop('checked', false);
        updateSelectedTotal();
    });

    $('#btnSaveHeader').on('click', function() {
        const date       = $('#date').val();
        const selectedCAs = $('.kasbon-check:checked').map((_, el) => el.value).get();

        if (!date) { showFloatingAlert('error', 'Date is required'); return; }
        if (!selectedCAs.length) { showFloatingAlert('error', 'Please select at least 1 Cash Advance'); return; }

        showFloatingAlert('saving', 'Saving LPJ header...');
        $(this).prop('disabled', true);

        $.ajax({
            url: '{{ route("lpj-gen.header.store") }}',
            method: 'POST',
            data: new FormData($('#lpjHeaderForm')[0]),
            contentType: false,
            processData: false,
            success: function(r) {
                $('#btnSaveHeader').prop('disabled', false);
                if (r.success) {
                    showFloatingAlert('success', 'Saved! Redirecting...');
                    setTimeout(() => { window.location.href = r.redirect_url; }, 1000);
                } else {
                    showFloatingAlert('error', r.message || 'Failed to save');
                }
            },
            error: xhr => {
                $('#btnSaveHeader').prop('disabled', false);
                showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to save');
            }
        });
    });
});
</script>
@endpush