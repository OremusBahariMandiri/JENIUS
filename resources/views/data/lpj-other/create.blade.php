@extends('layouts.app')

@section('title', 'Create LPJ Other')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .create-lpj-other .card { border:none; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,.08); }
        .create-lpj-other .card-header { border-radius:10px 10px 0 0 !important; font-weight:600; }
        .create-lpj-other .form-label { font-weight:500; }

        /* FLOATING BADGE ALERT */
        .floating-badge-alert {
            position:fixed; top:80px; right:30px; z-index:9999;
            min-width:250px; padding:15px 20px; border-radius:12px;
            box-shadow:0 8px 25px rgba(0,0,0,.2); display:none;
            animation:slideInRight .4s ease-out;
        }
        .floating-badge-alert.show { display:flex; align-items:center; gap:12px; }
        .floating-badge-alert.alert-saving  { background:linear-gradient(135deg,#fbbf24,#f59e0b); color:white; }
        .floating-badge-alert.alert-success { background:linear-gradient(135deg,#10b981,#059669); color:white; }
        .floating-badge-alert.alert-error   { background:linear-gradient(135deg,#ef4444,#dc2626); color:white; }
        .floating-badge-alert .alert-text   { flex:1; font-weight:600; font-size:.95rem; }
        @keyframes slideInRight {
            from { transform:translateX(400px); opacity:0; }
            to   { transform:translateX(0);     opacity:1; }
        }
        @keyframes slideOutRight {
            from { transform:translateX(0);     opacity:1; }
            to   { transform:translateX(400px); opacity:0; }
        }
        .floating-badge-alert.hiding { animation:slideOutRight .4s ease-in; }

        /* Kasbon list */
        .kasbon-item {
            border:2px solid #dee2e6; border-radius:10px; padding:15px;
            margin-bottom:12px; cursor:pointer; transition:all .2s;
            background:white;
        }
        .kasbon-item:hover   { border-color:#10b981; background:#f0fdf4; }
        .kasbon-item.selected { border-color:#10b981; background:#d1fae5; }
        .kasbon-item .kasbon-id   { font-weight:700; color:#2c3e50; }
        .kasbon-item .kasbon-date { color:#6c757d; font-size:.85rem; }
        .kasbon-item .kasbon-amt  { font-weight:600; color:#059669; }

        /* Preview No LPJ */
        .preview-no-lpj {
            background:linear-gradient(135deg,#10b981,#059669);
            color:white; border-radius:10px; padding:15px 20px;
        }
    </style>
@endpush

@section('content')
<div id="floatingBadgeAlert" class="floating-badge-alert">
    <i class="fas fa-circle-notch fa-spin" id="alertIcon"></i>
    <div class="alert-text" id="alertText">Processing...</div>
</div>

<div class="container-fluid create-lpj-other">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow mb-4">
                <div class="card-header text-black d-flex justify-content-between align-items-center"
                     style="background-color:#d1fae5">
                    <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Create LPJ Other</span>
                    <a href="{{ route('lpj-other.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
                <div class="card-body p-4">

                    {{-- Preview No LPJ --}}
                    <div class="preview-no-lpj mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-hashtag fa-2x opacity-75"></i>
                            <div>
                                <div style="font-size:.8rem; opacity:.85;">No. LPJ (Auto-generated)</div>
                                <div style="font-size:1.4rem; font-weight:700; letter-spacing:1px;">
                                    {{ $previewNoLpj }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 1: JO Selection --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold required-field">Job Order Other</label>
                        <select id="select_jo_other" class="form-select select2-jo" data-placeholder="Select JO Other...">
                            <option value=""></option>
                            @foreach($joOthers as $jo)
                                <option value="{{ $jo->id_jo_other }}">
                                    {{ $jo->no_jo_other }} - {{ $jo->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Step 2: Date & Note --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold required-field">LPJ Date</label>
                            <input type="date" id="input_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Note</label>
                            <input type="text" id="input_note" class="form-control" placeholder="Optional...">
                        </div>
                    </div>

                    {{-- Step 3: Cash Advance List --}}
                    <div id="kasbonSection" style="display:none;">
                        <hr>
                        <h6 class="fw-bold mb-3"><i class="fas fa-money-bill-wave me-2 text-success"></i>Select Cash Advance(s)</h6>
                        <div id="kasbonList">
                            <p class="text-muted text-center py-3">
                                <i class="fas fa-spinner fa-spin me-2"></i>Loading cash advances...
                            </p>
                        </div>
                        <div id="kasbonEmpty" style="display:none;" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                            No cash advances found for this JO.
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-success px-4 py-2 fw-bold" id="btnCreate" disabled>
                            <i class="fas fa-save me-2"></i>Create LPJ
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// ========================================
// FLOATING BADGE ALERT
// ========================================
function showFloatingAlert(type, message) {
    const alert = $('#floatingBadgeAlert');
    const icon  = $('#alertIcon');
    alert.removeClass('alert-saving alert-success alert-error hiding');
    switch (type) {
        case 'saving':  alert.addClass('alert-saving');  icon.attr('class','fas fa-circle-notch fa-spin'); break;
        case 'success': alert.addClass('alert-success'); icon.attr('class','fas fa-check-circle'); break;
        default:        alert.addClass('alert-error');   icon.attr('class','fas fa-exclamation-circle'); break;
    }
    $('#alertText').text(message);
    alert.addClass('show');
    if (type !== 'saving') setTimeout(hideFloatingAlert, 3000);
}
function hideFloatingAlert() {
    const alert = $('#floatingBadgeAlert');
    alert.addClass('hiding');
    setTimeout(() => alert.removeClass('show hiding'), 400);
}

// ========================================
// INIT
// ========================================
$(document).ready(function () {
    $('.select2-jo').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select JO Other...',
        allowClear: true,
        width: '100%',
    });

    // JO change → load kasbons
    $('#select_jo_other').on('change', function () {
        const joId = $(this).val();
        if (!joId) {
            $('#kasbonSection').hide();
            updateCreateButton();
            return;
        }

        $('#kasbonSection').show();
        $('#kasbonList').html('<p class="text-muted text-center py-3"><i class="fas fa-spinner fa-spin me-2"></i>Loading...</p>');
        $('#kasbonEmpty').hide();

        $.get('{{ route('lpj-other.kasbons-by-jo') }}', { id_jo_other: joId }, function (r) {
            if (!r.success || !r.data.length) {
                $('#kasbonList').html('');
                $('#kasbonEmpty').show();
                updateCreateButton();
                return;
            }

            let html = '';
            r.data.forEach(function (k) {
                html += `
                    <div class="kasbon-item" data-id="${k.id_kasbon_other}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="kasbon-id">${k.id_kasbon_other}</div>
                                <div class="kasbon-date"><i class="fas fa-calendar me-1"></i>${k.tgl_kasbon ?? '-'}</div>
                            </div>
                            <div class="text-end">
                                <div class="kasbon-amt">IDR ${Number(k.total_kasbon).toLocaleString('id-ID',{minimumFractionDigits:0})}</div>
                                <small class="text-muted">${k.items_count} item(s)</small>
                            </div>
                        </div>
                    </div>`;
            });
            $('#kasbonList').html(html);
            updateCreateButton();
        }).fail(function () {
            showFloatingAlert('error', 'Failed to load cash advances');
        });
    });

    // Kasbon selection toggle
    $(document).on('click', '.kasbon-item', function () {
        $(this).toggleClass('selected');
        updateCreateButton();
    });

    function updateCreateButton() {
        const hasJo     = !!$('#select_jo_other').val();
        const hasKasbon = $('.kasbon-item.selected').length > 0;
        const hasDate   = !!$('#input_date').val();
        $('#btnCreate').prop('disabled', !(hasJo && hasKasbon && hasDate));
    }

    $('#input_date').on('change', updateCreateButton);

    // Create LPJ
    $('#btnCreate').on('click', function () {
        const joId    = $('#select_jo_other').val();
        const date    = $('#input_date').val();
        const note    = $('#input_note').val();
        const kasbons = [];

        $('.kasbon-item.selected').each(function () {
            kasbons.push($(this).data('id'));
        });

        if (!joId || !date || !kasbons.length) {
            showFloatingAlert('error', 'Please fill all required fields and select at least one CA');
            return;
        }

        showFloatingAlert('saving', 'Creating LPJ...');
        $('#btnCreate').prop('disabled', true);

        $.ajax({
            url: '{{ route('lpj-other.header.store') }}',
            method: 'POST',
            data: {
                id_jo_other: joId,
                date:        date,
                note:        note,
                kasbon_ids:  kasbons,
                _token:      $('meta[name="csrf-token"]').attr('content'),
            },
            success(r) {
                if (r.success) {
                    showFloatingAlert('success', 'LPJ created successfully!');
                    setTimeout(() => { window.location.href = r.redirect_url; }, 1000);
                } else {
                    showFloatingAlert('error', r.message || 'Failed to create LPJ');
                    $('#btnCreate').prop('disabled', false);
                }
            },
            error(xhr) {
                showFloatingAlert('error', xhr.responseJSON?.message || 'Failed to create LPJ');
                $('#btnCreate').prop('disabled', false);
            }
        });
    });
});
</script>
@endpush