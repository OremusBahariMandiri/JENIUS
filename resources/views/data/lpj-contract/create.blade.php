@extends('layouts.app')

@section('title', 'Add LPJ Contract')

@push('styles')
<style>
    .lpjCreatePage .card { border:none; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,.08); }
    .lpjCreatePage .card-header { border-radius:10px 10px 0 0!important; padding:1rem 1.5rem; font-weight:600; }
    .lpjCreatePage .form-control:focus,
    .lpjCreatePage .form-select:focus {
        border-color:#10b981; box-shadow:0 0 0 0.2rem rgba(16,185,129,.25);
    }
    .required-field::after { content:" *"; color:#dc3545; font-weight:600; }

    /* FLOATING ALERT */
    .floating-badge-alert {
        position:fixed; top:80px; right:30px; z-index:9999;
        min-width:260px; padding:15px 20px; border-radius:12px;
        box-shadow:0 8px 25px rgba(0,0,0,.2); display:none;
        animation:slideInRight .4s ease-out; backdrop-filter:blur(10px);
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

    /* JO SUMMARY */
    .jo-summary-card {
        background:#fff; border:1.5px solid #10b981; border-radius:12px;
        padding:1.25rem 1.5rem; animation:fadeInDown .35s ease-out;
    }
    @keyframes fadeInDown { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
    .jo-meta-label { font-size:.72rem; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:.5px; }
    .jo-meta-value { font-size:.875rem; font-weight:700; color:#2c3e50; margin-top:2px; }

    /* KASBON SELECT TABLE */
    .kasbon-select-table { border-radius:10px; overflow:hidden; border:1px solid #dee2e6; }
    .kasbon-select-table thead th {
        background:#2c3e50; color:#fff; font-weight:600; font-size:.8rem;
        padding:10px 12px; border:none;
    }
    .kasbon-select-table tbody td {
        padding:10px 12px; vertical-align:middle; font-size:.875rem;
        border-bottom:1px solid #f1f5f9;
    }
    .kasbon-select-table tbody tr:hover td { background:#f0fdf4; }
    .kasbon-select-table tbody tr.selected-row td { background:#dcfce7; }
    .kasbon-select-table .form-check-input:checked { background-color:#10b981; border-color:#10b981; }

    .kasbon-amount-badge {
        background:#d1fae5; color:#065f46; border:1px solid #6ee7b7;
        border-radius:6px; padding:3px 8px; font-size:.78rem; font-weight:600;
    }
    .kasbon-empty-state { text-align:center; padding:40px 20px; color:#9ca3af; }

    /* DETAIL MODAL */
    .detail-item-row td { font-size:.85rem; padding:8px 12px; }
    .detail-item-row:hover td { background:#f0fdf4; }
    .detail-thead th { background:#2c3e50; color:#fff; font-size:.8rem; padding:10px 12px; }

    /* FINAL */
    .final-save-section {
        position:sticky; bottom:0; background:#fff;
        padding:18px 20px; box-shadow:0 -4px 20px rgba(0,0,0,.1);
        border-radius:12px 12px 0 0; margin-top:30px; z-index:100;
    }
    .btn-final-back {
        background:linear-gradient(135deg,#868686,#5e5e5e); border:none; color:#fff;
        padding:13px 35px; border-radius:10px; font-weight:700; font-size:1rem;
        box-shadow:0 4px 15px rgba(27,27,27,.3); transition:all .3s;
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
                    <i class="fas fa-info-circle me-2" style="color:#059669;"></i>
                    Cash Advance Detail — <span id="modalKasbonNo">—</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Header info --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div style="background:#f8f9fa; border-radius:8px; padding:12px 14px;">
                            <div class="jo-meta-label">CA Number</div>
                            <div class="jo-meta-value" id="detailKasbonNo">—</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background:#f8f9fa; border-radius:8px; padding:12px 14px;">
                            <div class="jo-meta-label">CA Date</div>
                            <div class="jo-meta-value" id="detailKasbonTgl">—</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background:#d1fae5; border-radius:8px; padding:12px 14px;">
                            <div class="jo-meta-label">Total CA Amount</div>
                            <div class="jo-meta-value text-success" id="detailKasbonTotal">—</div>
                        </div>
                    </div>
                </div>

                {{-- Items table --}}
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
                                <th class="text-end">HPP (IDR)</th>
                                <th class="text-end">CA Amount (IDR)</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsBody">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-circle-notch fa-spin me-2"></i> Loading...
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8f9fa; font-weight:700;">
                                <td colspan="3" class="text-center">TOTAL</td>
                                <td class="text-end" id="detailTotalHpp">—</td>
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

<div class="container-fluid lpjCreatePage">
    <div class="row">
        <div class="col-lg-12">

            {{-- Page Header --}}
            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                    style="background-color:#d1fae5">
                    <span class="fw-bold">
                        <i class="fas fa-plus-circle me-2" style="color:#059669;"></i>Add LPJ Contract
                    </span>
                    <a href="{{ route('lpj-contract.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <form id="lpjHeaderForm" enctype="multipart/form-data">
                @csrf

                {{-- Header Card --}}
                <div class="card shadow mb-4">
                    <div class="card-header text-black" style="background-color:#d1fae5">
                        <h6 class="mb-0">
                            <i class="fas fa-file-invoice me-2" style="color:#059669;"></i>LPJ Information
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
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Auto-generated on save
                                </small>
                            </div>

                            {{-- Date --}}
                            <div class="col-md-6">
                                <label class="form-label required-field">Date</label>
                                <input type="date" name="date" id="date" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            {{-- JO Contract --}}
                            <div class="col-md-12">
                                <label class="form-label required-field">Job Order</label>
                                <select name="id_jo_cont" id="id_jo_cont" class="form-select select2-field" required>
                                    <option value="">-- Select Job Order --</option>
                                    @foreach($joContracts as $jo)
                                        <option value="{{ $jo->id_jo_cont }}"
                                            data-no="{{ $jo->no_jo_cont }}"
                                            data-title="{{ $jo->title }}"
                                            data-tgl="{{ optional($jo->tgl_jo_cont)->format('Y-m-d') ?? '' }}"
                                            data-area="{{ $jo->area->area ?? '—' }}">
                                            {{ $jo->no_jo_cont }} — {{ $jo->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- JO Summary Card --}}
                            <div class="col-md-12" id="joSummaryWrapper" style="display:none;">
                                <div class="jo-summary-card">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:48px;height:48px;border-radius:10px;background:#d1fae5;
                                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="fas fa-file-contract" style="color:#059669;font-size:1.2rem;"></i>
                                        </div>
                                        <div>
                                            <div id="joSummaryNo" style="font-weight:700;font-size:1.05rem;color:#2c3e50;"></div>
                                            <div id="joSummaryTitle" style="font-size:.875rem;color:#6b7280;margin-top:2px;"></div>
                                        </div>
                                    </div>
                                    <div style="border-top:1px solid #d1fae5;margin:12px 0;"></div>
                                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px 24px;">
                                        <div>
                                            <div class="jo-meta-label">JO Date</div>
                                            <div class="jo-meta-value" id="joSummaryDate">—</div>
                                        </div>
                                        <div>
                                            <div class="jo-meta-label">Area</div>
                                            <div class="jo-meta-value" id="joSummaryArea">—</div>
                                        </div>
                                        <div>
                                            <div class="jo-meta-label">Available CA</div>
                                            <div class="jo-meta-value" id="joSummaryKasbonCount">—</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SELECT KASBON SECTION --}}
                            <div class="col-md-12" id="kasbonSelectWrapper" style="display:none;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-semibold mb-0 required-field">
                                        Select Cash Advances to Include
                                    </label>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-success" id="btnSelectAllCa">
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
                                                <th class="text-center">CA Date</th>
                                                <th class="text-center">Items</th>
                                                <th class="text-end">Total CA Amount (IDR)</th>
                                                <th class="text-center" width="8%">Info</th>
                                            </tr>
                                        </thead>
                                        <tbody id="kasbonSelectBody"></tbody>
                                        <tfoot>
                                            <tr style="background:#f0fdf4; font-weight:700;">
                                                <td colspan="4" class="text-center" style="font-size:.85rem;">
                                                    SELECTED TOTAL
                                                </td>
                                                <td class="text-end" style="color:#059669;" id="kasbonSelectedTotal">
                                                    IDR 0,00
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div id="kasbonEmptyState" class="kasbon-empty-state" style="display:none;">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0 small">No cash advances found for this Job Order.</p>
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
                                <input type="file" name="evidence" id="evidence"
                                    class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">PDF / JPG / PNG, max 5MB</small>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-success px-4 fw-bold" id="btnSaveHeader">
                                <i class="fas fa-save me-1"></i> Save LPJ Header
                            </button>
                        </div>
                    </div>
                </div>

            </form>

            <div class="final-save-section">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('lpj-contract.index') }}" class="btn btn-final-back">
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let kasbonData = []; // store loaded kasbon list

function formatNumber(v) {
    return parseFloat(v || 0).toLocaleString('id-ID', { minimumFractionDigits:2, maximumFractionDigits:2 });
}

function showFloatingAlert(type, message) {
    const $a = $('#floatingBadgeAlert'), $i = $('#alertIcon');
    $a.removeClass('alert-saving alert-success alert-error hiding');
    if (type === 'saving')  { $a.addClass('alert-saving');  $i.attr('class', 'fas fa-circle-notch fa-spin'); }
    if (type === 'success') { $a.addClass('alert-success'); $i.attr('class', 'fas fa-check-circle'); }
    if (type === 'error')   { $a.addClass('alert-error');   $i.attr('class', 'fas fa-exclamation-circle'); }
    $('#alertText').text(message);
    $a.addClass('show');
    if (type !== 'saving') setTimeout(() => { $a.addClass('hiding'); setTimeout(() => $a.removeClass('show hiding'), 400); }, 3000);
}

// ── LOAD KASBONS BY JO ──
function loadKasbonsByJo(idJoCont) {
    $('#kasbonSelectWrapper').show();
    $('#kasbonTableWrapper').hide();
    $('#kasbonEmptyState').hide();
    $('#kasbonLoadingState').show();
    kasbonData = [];

    $.ajax({
        url: '{{ route("lpj-contract.kasbons-by-jo") }}',
        method: 'GET',
        data: { id_jo_cont: idJoCont },
        success: function(r) {
            $('#kasbonLoadingState').hide();
            if (!r.success || !r.data.length) {
                $('#kasbonEmptyState').show();
                $('#joSummaryKasbonCount').text('0 CA');
                updateSelectedTotal();
                return;
            }

            kasbonData = r.data;
            $('#joSummaryKasbonCount').text(r.data.length + ' CA');

            const $tbody = $('#kasbonSelectBody');
            $tbody.empty();

            r.data.forEach((k, idx) => {
                $tbody.append(`
                    <tr class="kasbon-row" id="krow_${idx}">
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input kasbon-check"
                                name="kasbon_ids[]" value="${k.id_kasbon_cont}" checked
                                data-idx="${idx}" data-amount="${k.total_kasbon}"
                                onchange="onKasbonCheck(this)">
                        </td>
                        <td class="fw-semibold" style="color:#2c3e50;">${k.id_kasbon_cont}</td>
                        <td class="text-center text-muted">${k.tgl_kasbon ?? '—'}</td>
                        <td class="text-center">
                            <span class="kasbon-amount-badge">${k.items_count} item${k.items_count !== 1 ? 's' : ''}</span>
                        </td>
                        <td class="text-end fw-semibold">IDR ${formatNumber(k.total_kasbon)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm" title="View CA Detail"
                                style="background:#dbeafe; color:#1d4ed8; border:none; border-radius:6px; padding:4px 8px;"
                                onclick="showKasbonDetail('${k.id_kasbon_cont}', ${idx})">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            $('#checkAllCa').prop('checked', true);
            $('#kasbonTableWrapper').show();
            updateSelectedTotal();
        },
        error: () => {
            $('#kasbonLoadingState').hide();
            $('#kasbonEmptyState').show();
        }
    });
}

// ── UPDATE SELECTED TOTAL ──
function updateSelectedTotal() {
    let total = 0;
    $('.kasbon-check:checked').each(function() {
        total += parseFloat($(this).data('amount')) || 0;
    });
    $('#kasbonSelectedTotal').text('IDR ' + formatNumber(total));

    // Highlight selected rows
    $('.kasbon-row').each(function() {
        const checked = $(this).find('.kasbon-check').is(':checked');
        $(this).toggleClass('selected-row', checked);
    });
}

function onKasbonCheck(el) {
    updateSelectedTotal();
    // Sync master checkbox
    const total = $('.kasbon-check').length;
    const checked = $('.kasbon-check:checked').length;
    $('#checkAllCa').prop('indeterminate', checked > 0 && checked < total);
    $('#checkAllCa').prop('checked', checked === total);
}

// ── SELECT ALL / DESELECT ALL ──
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

// ── SHOW KASBON DETAIL MODAL ──
function showKasbonDetail(kasbonId, idx) {
    const k = kasbonData[idx];

    $('#modalKasbonNo').text(kasbonId);
    $('#detailKasbonNo').text(kasbonId);
    $('#detailKasbonTgl').text(k.tgl_kasbon ?? '—');
    $('#detailKasbonTotal').text('IDR ' + formatNumber(k.total_kasbon));
    $('#detailItemsBody').html(`
        <tr><td colspan="5" class="text-center py-4 text-muted">
            <i class="fas fa-circle-notch fa-spin me-2"></i> Loading items...
        </td></tr>
    `);
    $('#detailTotalHpp').text('—');
    $('#detailTotalKasbon').text('—');

    $('#modalKasbonDetail').modal('show');

    // Load items detail
    $.ajax({
        url: '{{ route("lpj-contract.kasbons-by-jo") }}',
        method: 'GET',
        data: { id_jo_cont: $('#id_jo_cont').val(), detail_kasbon: kasbonId },
        success: function(r) {
            if (r.success && r.kasbon_detail) {
                const items = r.kasbon_detail.items || [];
                if (!items.length) {
                    $('#detailItemsBody').html(`<tr><td colspan="5" class="text-center py-3 text-muted">No items found</td></tr>`);
                    return;
                }
                let html = '', totalHpp = 0, totalKasbon = 0;
                items.forEach((item, i) => {
                    totalHpp    += parseFloat(item.nilai_hpp_cont_item) || 0;
                    totalKasbon += parseFloat(item.nilai_kasbon) || 0;
                    html += `<tr class="detail-item-row">
                        <td class="text-center text-muted">${i + 1}</td>
                        <td>${item.invoice_ctg ?? '—'}</td>
                        <td class="fw-semibold">${item.invoice_typ ?? '—'}</td>
                        <td class="text-end">IDR ${formatNumber(item.nilai_hpp_cont_item)}</td>
                        <td class="text-end">IDR ${formatNumber(item.nilai_kasbon)}</td>
                    </tr>`;
                });
                $('#detailItemsBody').html(html);
                $('#detailTotalHpp').text('IDR ' + formatNumber(totalHpp));
                $('#detailTotalKasbon').text('IDR ' + formatNumber(totalKasbon));
            } else {
                $('#detailItemsBody').html(`<tr><td colspan="5" class="text-center py-3 text-muted">Failed to load items</td></tr>`);
            }
        },
        error: () => {
            $('#detailItemsBody').html(`<tr><td colspan="5" class="text-center py-3 text-muted">Error loading items</td></tr>`);
        }
    });
}

// ── JO SUMMARY ──
function populateJoSummary(idJoCont) {
    if (!idJoCont) {
        $('#joSummaryWrapper').hide();
        $('#kasbonSelectWrapper').hide();
        return;
    }
    const $opt = $('#id_jo_cont option:selected');
    $('#joSummaryNo').text($opt.data('no') || idJoCont);
    $('#joSummaryTitle').text($opt.data('title') || '—');
    $('#joSummaryDate').text($opt.data('tgl') || '—');
    $('#joSummaryArea').text($opt.data('area') || '—');
    $('#joSummaryKasbonCount').html('<i class="fas fa-circle-notch fa-spin" style="color:#10b981;font-size:.8rem;"></i>');
    $('#joSummaryWrapper').show();

    loadKasbonsByJo(idJoCont);
}

// ── SAVE HEADER ──
$('#btnSaveHeader').on('click', function() {
    const idJoCont    = $('#id_jo_cont').val();
    const date        = $('#date').val();
    const selectedCAs = $('.kasbon-check:checked').map((_, el) => el.value).get();

    if (!idJoCont || !date) {
        showFloatingAlert('error', 'Job Order and Date are required');
        return;
    }
    if (!selectedCAs.length) {
        showFloatingAlert('error', 'Please select at least 1 Cash Advance');
        return;
    }

    showFloatingAlert('saving', 'Saving LPJ header...');
    $('#btnSaveHeader').prop('disabled', true);

    const formData = new FormData($('#lpjHeaderForm')[0]);
    // kasbon_ids[] sudah otomatis ter-include dari FormData karena checkbox name="kasbon_ids[]"
    // JANGAN append manual lagi — akan menyebabkan duplikat

    $.ajax({
        url: '{{ route("lpj-contract.header.store") }}',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(r) {
            $('#btnSaveHeader').prop('disabled', false);
            if (r.success) {
                showFloatingAlert('success', 'Header saved! Redirecting...');
                setTimeout(() => { window.location.href = r.redirect_url; }, 1000);
            } else {
                showFloatingAlert('error', r.message || 'Failed to save');
            }
        },
        error: function(xhr) {
            $('#btnSaveHeader').prop('disabled', false);
            showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to save header');
        }
    });
});

$(document).ready(function() {
    $('.select2-field').select2({ theme:'bootstrap-5', width:'100%' });
    $('#id_jo_cont').on('change', function() { populateJoSummary($(this).val()); });
});
</script>
@endpush