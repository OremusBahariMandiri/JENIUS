@extends('layouts.app')
@section('title', 'Create Invoice Contract')

@section('content')
{{-- JO Info Modal --}}
<div class="modal fade" id="joInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width:900px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;padding:1rem 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-file-contract me-2"></i>Job Order Info</h5>
                    <small id="joInfoSubtitle" style="opacity:.85;font-size:.8rem;"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="joInfoBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-success" role="status"></div>
                    <div class="mt-2 text-muted">Loading...</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4 d-flex justify-content-between align-items-center">
                <small class="text-muted" id="joInfoNote"></small>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header text-black d-flex justify-content-between align-items-center"
            style="background-color: #d1fae5">
            <span class="fw-bold"><i class="fas fa-file-invoice me-2"></i>Create Invoice Contract</span>
            <a href="{{ route('invoice-contract.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('invoice-contract.store') }}">
        @csrf

        <div class="card shadow mb-4">
            <div class="card-header fw-bold" style="background-color:#d1fae5">
                <i class="fas fa-file-alt me-2"></i>Invoice Header
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Invoice Number <span class="text-danger">*</span></label>
                        <input type="text" name="no_invoice"
                               class="form-control bg-light"
                               value="{{ $suggestedNo }}" readonly>
                        <small class="text-muted">Nomor invoice di-generate otomatis oleh sistem.</small>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Job Order <span class="text-danger">*</span></label>
                        <select name="id_jo_cont" id="joSelect"
                                class="form-select @error('id_jo_cont') is-invalid @enderror s2-jo" required>
                            <option value="">-- Select JO --</option>
                            @foreach($joContracts as $jo)
                                <option value="{{ $jo->id_jo_cont }}"
                                    data-customer="{{ $jo->contract?->customer?->customer }}"
                                    data-title="{{ $jo->title }}"
                                    data-contract="{{ $jo->contract?->no_contract }}"
                                    {{ old('id_jo_cont', $joContract?->id_jo_cont) == $jo->id_jo_cont ? 'selected' : '' }}>
                                    {{ $jo->no_jo_cont }}
                                    @if($jo->contract?->customer)– {{ $jo->contract->customer->customer }}@endif
                                </option>
                            @endforeach
                        </select>
                        @error('id_jo_cont')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-1 d-flex align-items-end pb-1">
                        <button type="button" id="btnJoInfo" class="btn btn-outline-info w-100" title="View JO Info" disabled>
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" id="titleField" class="form-control"
                               value="{{ old('title', $joContract?->title) }}" readonly style="background:#e9ecef;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Customer</label>
                        <input type="text" name="to_customer" id="toCustomer" class="form-control"
                               value="{{ old('to_customer', $joContract?->contract?->customer?->customer) }}" readonly style="background:#e9ecef;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contract No</label>
                        <input type="text" name="contract_no" id="contractNo" class="form-control"
                               value="{{ old('contract_no', $joContract?->contract?->no_contract) }}" readonly style="background:#e9ecef;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Invoice Date</label>
                        <input type="date" name="tgl_invoice" class="form-control"
                               value="{{ old('tgl_invoice', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Due Date</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vessel</label>
                        <select name="kapal" class="form-select s2-vessel">
                            <option value="">-- Select Vessel --</option>
                            @foreach($vessels as $v)
                                <option value="{{ $v->vessel_name }}" {{ old('kapal') == $v->vessel_name ? 'selected' : '' }}>
                                    {{ $v->vessel_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jetty</label>
                        <select name="jetty" class="form-select s2-jetty">
                            <option value="">-- Select Jetty --</option>
                            @foreach($ports as $p)
                                <option value="{{ $p->name_port }}" {{ old('jetty') == $p->name_port ? 'selected' : '' }}>
                                    {{ $p->name_port }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">PO Number</label>
                        <input type="text" name="po_number" class="form-control" value="{{ old('po_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Currency</label>
                        <select name="currency" class="form-select">
                            <option value="IDR" {{ old('currency', 'IDR') === 'IDR' ? 'selected' : '' }}>IDR</option>
                            <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vendor Number</label>
                        <input type="text" name="vendor_number" class="form-control" value="{{ old('vendor_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">SA/GR Number</label>
                        <input type="text" name="sa_gr_number" class="form-control" value="{{ old('sa_gr_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">BA Number</label>
                        <input type="text" name="ba_number" class="form-control" value="{{ old('ba_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Chart of Account</label>
                        <select name="id_md_chart_of_account" class="form-select s2-coa">
                            <option value="">— Select COA (optional) —</option>
                            @foreach($coas as $coa)
                                <option value="{{ $coa->id_md_chart_of_account }}"
                                    {{ old('id_md_chart_of_account') == $coa->id_md_chart_of_account ? 'selected' : '' }}>
                                    {{ $coa->no_account }} — {{ $coa->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. Voyage</label>
                        <input type="text" name="no_voyage" class="form-control" value="{{ old('no_voyage') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">GRT</label>
                        <input type="text" name="grt" class="form-control" value="{{ old('grt') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ETA</label>
                        <input type="date" name="eta" class="form-control" value="{{ old('eta') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ETD</label>
                        <input type="date" name="etd" class="form-control" value="{{ old('etd') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cond of Payment</label>
                        <input type="text" name="cond_of_payment" class="form-control" value="{{ old('cond_of_payment') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Period</label>
                        <input type="text" name="period" class="form-control" value="{{ old('period') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Att</label>
                        <input type="text" name="att" class="form-control" value="{{ old('att') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">VAT Number</label>
                        <input type="text" name="vat_number" class="form-control" value="{{ old('vat_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bar Code</label>
                        <input type="text" name="bar_code" class="form-control" value="{{ old('bar_code') }}">
                    </div>
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

        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('invoice-contract.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i>Cancel
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i>Save & Edit Items
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('.s2-coa').select2({ theme:'bootstrap-5', placeholder:'— Select COA (optional) —', allowClear:true });
    $('.s2-vessel').select2({ theme:'bootstrap-5', placeholder:'-- Select Vessel --', allowClear:true });
    $('.s2-jetty').select2({ theme:'bootstrap-5', placeholder:'-- Select Jetty --', allowClear:true });
    $('.s2-jo').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Select JO --',
        allowClear: true,
    });

    // Enable info button if JO pre-selected
    if ($('#joSelect').val()) $('#btnJoInfo').prop('disabled', false);

    $('#joSelect').on('change', function(){
        const opt = $(this).find(':selected');
        const joId = $(this).val();
        $('#btnJoInfo').prop('disabled', !joId);
        $('#toCustomer').val(opt.data('customer') || '');
        $('#titleField').val(opt.data('title') || '');
        $('#contractNo').val(opt.data('contract') || '');
    });

    // ── Info button → open modal ──
    $('#btnJoInfo').on('click', function() {
        var joId = $('#joSelect').val();
        if (!joId) return;
        $('#joInfoBody').html('<div class="col-12 text-center py-4"><div class="spinner-border text-success" role="status"></div><div class="mt-2 text-muted">Loading...</div></div>');
        $('#joInfoNote').text('');
        $('#joInfoModal').modal('show');

        $.ajax({
            url: '{{ route("invoice-contract.jo.info.create") }}',
            method: 'GET',
            data: { id_jo_cont: joId },
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            success: function(res) {
                if (!res.success) { $('#joInfoBody').html('<div class="p-4 text-danger"><i class="fas fa-exclamation-circle me-2"></i>' + res.message + '</div>'); return; }

                $('#joInfoSubtitle').text(res.no_jo_cont + ' — ' + res.customer);

                var tabs = `
                <style>
                    #joInfoTabs .nav-link { color:#6b7280; border-radius:6px 6px 0 0; }
                    #joInfoTabs .nav-link:hover { color:#2c3e50; background:#f3f4f6; }
                    #joInfoTabs .nav-link.active { background:#2c3e50 !important; color:#fff !important; border-color:#2c3e50 !important; }
                    #joInfoTabs .nav-link.active .badge { background:#ffffff33 !important; }
                </style>
                <ul class="nav nav-tabs px-3 pt-3" id="joInfoTabs" role="tablist" style="border-bottom:1px solid #e5e7eb;">
                    <li class="nav-item"><button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tabJoOverview" style="font-size:.85rem;"><i class="fas fa-info-circle me-1"></i>Overview</button></li>
                    <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tabJoItems" style="font-size:.85rem;"><i class="fas fa-list me-1"></i>Items <span class="badge bg-secondary ms-1">${res.items.length + res.pajak_items.length}</span></button></li>
                    <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tabJoKasbon" style="font-size:.85rem;"><i class="fas fa-wallet me-1"></i>Kasbon <span class="badge bg-secondary ms-1">${res.kasbon.length}</span></button></li>
                    <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tabJoLpj" style="font-size:.85rem;"><i class="fas fa-receipt me-1"></i>LPJ <span class="badge bg-secondary ms-1">${res.lpj.length}</span></button></li>
                    <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tabJoMutasi" style="font-size:.85rem;"><i class="fas fa-exchange-alt me-1"></i>Mutasi <span class="badge bg-secondary ms-1">${res.mutasi_penerimaan.length + res.mutasi_pembayaran.length}</span></button></li>
                </ul>
                <div class="tab-content p-3" id="joInfoTabContent">`;

                var kursColor = res.kurs_usd ? '#059669' : '#9ca3af';
                tabs += `<div class="tab-pane fade show active" id="tabJoOverview">
                    <div class="row g-2 mt-1">
                        ${joCard('fas fa-hashtag','JO Number',res.no_jo_cont)}
                        ${joCard('fas fa-heading','Title',res.title)}
                        ${joCard('fas fa-building','Customer',res.customer)}
                        ${joCard('fas fa-file-contract','Contract No',res.contract_no)}
                        ${joCardHighlight('fas fa-dollar-sign','Exchange Rate (USD)', res.kurs_usd ? 'IDR '+res.kurs_usd : 'No USD Rate', kursColor)}
                        ${res.tgl_kurs_usd ? joCard('fas fa-calendar','Rate Date',res.tgl_kurs_usd) : ''}
                    </div>
                </div>`;

                var kursInfo = res.kurs_usd
                    ? `<div class="d-flex align-items-center gap-2 mb-2 mt-2 px-1"><span style="background:#059669;color:#fff;padding:3px 10px;border-radius:6px;font-size:.75rem;font-weight:700;"><i class="fas fa-dollar-sign me-1"></i>Exchange Rate: IDR ${res.kurs_usd}</span><span style="font-size:.78rem;color:#6b7280;">${res.tgl_kurs_usd ? '<i class="fas fa-calendar me-1"></i>'+res.tgl_kurs_usd : ''}</span></div>`
                    : `<div class="mb-2 mt-2 px-1"><span style="background:#e5e7eb;color:#6b7280;padding:3px 10px;border-radius:6px;font-size:.75rem;font-weight:600;"><i class="fas fa-dollar-sign me-1"></i>Exchange Rate: —</span></div>`;

                var itemRows = '', rowNum = 1;
                var catSpans = {};
                res.items.forEach(function(it) { var cat = it.category||'-'; catSpans[cat]=(catSpans[cat]||0)+1; });
                var catSeen = {};
                res.items.forEach(function(it) {
                    var cat = it.category||'-', showCat = !catSeen[cat];
                    if (showCat) catSeen[cat]=true;
                    var catCell = showCat ? `<td rowspan="${catSpans[cat]}" style="vertical-align:middle;font-weight:600;text-align:center;background:#f0fdf4;color:#065f46;">${cat}</td>` : '';
                    itemRows += `<tr><td class="text-center">${rowNum++}</td>${catCell}<td>${it.name}${it.note?'<br><small class="text-muted fst-italic">'+it.note+'</small>':''}</td><td class="text-end">${it.pend_idr}</td><td class="text-end">${it.pend_usd}</td><td class="text-end">${it.sell_idr}</td><td class="text-end">${it.hpp}</td></tr>`;
                });
                res.pajak_items.forEach(function(p) {
                    itemRows += `<tr style="background:#fffbeb;"><td class="text-center">${rowNum++}</td><td style="color:#b45309;font-weight:700;text-align:center;background:#fef3c7;">Tax</td><td>${p.name}${p.persen?' <span class="badge bg-warning text-dark ms-1" style="font-size:.7rem;">'+p.persen+'%</span>':''}</td><td class="text-end">0,00</td><td class="text-end">0,00</td><td class="text-end fw-bold">${p.nominal_idr}</td><td class="text-end fw-bold">${p.nominal_hpp}</td></tr>`;
                });
                tabs += `<div class="tab-pane fade" id="tabJoItems"><div class="px-3">${kursInfo}<div class="table-responsive" style="max-height:320px;overflow-y:auto;"><table class="table table-sm table-bordered mb-0" style="font-size:.8rem;"><thead style="background:#2c3e50;color:#fff;position:sticky;top:0;z-index:1;"><tr><th style="width:40px;">No.</th><th style="min-width:100px;">Category</th><th>Item</th><th class="text-end" style="min-width:90px;">Inc. IDR</th><th class="text-end" style="min-width:80px;">Inc. USD</th><th class="text-end" style="min-width:90px;">Sell</th><th class="text-end" style="min-width:90px;">HPP</th></tr></thead><tbody>${itemRows||'<tr><td colspan="7" class="text-center text-muted py-3">No items</td></tr>'}</tbody></table></div></div></div>`;

                var kasbonRows = '';
                res.kasbon.forEach(function(k) {
                    var badge = k.status==='released'?'success':(k.status==='pending'?'warning text-dark':'secondary');
                    kasbonRows += `<tr><td style="font-size:.82rem;">${k.nomor}</td><td style="font-size:.82rem;">${k.tgl}</td><td><span class="badge bg-${badge}" style="font-size:.72rem;">${k.status}</span></td><td class="text-end fw-bold" style="font-size:.82rem;">IDR ${k.total}</td></tr>`;
                });
                tabs += `<div class="tab-pane fade" id="tabJoKasbon"><div class="table-responsive mt-2" style="max-height:280px;overflow-y:auto;"><table class="table table-sm table-bordered mb-0"><thead style="background:#2c3e50;color:#fff;position:sticky;top:0;"><tr><th>No.</th><th>Date</th><th>Status</th><th class="text-end">Total</th></tr></thead><tbody>${kasbonRows||'<tr><td colspan="4" class="text-center text-muted py-3">No kasbon</td></tr>'}</tbody>${res.kasbon.length?'<tfoot><tr><td colspan="3" class="fw-bold text-end" style="font-size:.82rem;">Grand Total</td><td class="fw-bold text-end text-success" style="font-size:.85rem;">IDR '+res.kasbon_total+'</td></tr></tfoot>':''}</table></div></div>`;

                var lpjRows = '';
                res.lpj.forEach(function(l) {
                    var bal = parseFloat((l.balance||'0').replace(/\./g,'').replace(',','.'));
                    var balColor = bal>=0?'color:#059669;':'color:#dc2626;';
                    lpjRows += `<tr><td style="font-size:.82rem;font-weight:600;">${l.no_lpj}</td><td style="font-size:.82rem;">${l.tgl}</td><td class="text-end" style="font-size:.82rem;">IDR ${l.ca_amount}</td><td class="text-end" style="font-size:.82rem;">IDR ${l.pj_amount}</td><td class="text-end fw-bold" style="font-size:.82rem;${balColor}">IDR ${l.balance}</td></tr>`;
                });
                tabs += `<div class="tab-pane fade" id="tabJoLpj"><div class="table-responsive mt-2" style="max-height:280px;overflow-y:auto;"><table class="table table-sm table-bordered mb-0"><thead style="background:#2c3e50;color:#fff;position:sticky;top:0;"><tr><th>No. LPJ</th><th>Date</th><th class="text-end">CA Amount</th><th class="text-end">PJ Amount</th><th class="text-end">Balance</th></tr></thead><tbody>${lpjRows||'<tr><td colspan="5" class="text-center text-muted py-3">No LPJ</td></tr>'}</tbody></table></div></div>`;

                var mutasiRows = '';
                res.mutasi_penerimaan.forEach(function(m) { mutasiRows += `<tr><td><span class="badge bg-success" style="font-size:.7rem;">Receipt</span></td><td style="font-size:.82rem;">${m.nomor}</td><td style="font-size:.82rem;">${m.tgl}</td><td style="font-size:.8rem;">${m.tipe}</td><td class="text-end fw-bold" style="font-size:.82rem;">IDR ${m.nilai}</td></tr>`; });
                res.mutasi_pembayaran.forEach(function(m) { mutasiRows += `<tr><td><span class="badge bg-primary" style="font-size:.7rem;">Payment</span></td><td style="font-size:.82rem;">${m.nomor}</td><td style="font-size:.82rem;">${m.tgl}</td><td style="font-size:.8rem;">${m.jenis}</td><td class="text-end fw-bold" style="font-size:.82rem;">IDR ${m.nilai}</td></tr>`; });
                tabs += `<div class="tab-pane fade" id="tabJoMutasi"><div class="table-responsive mt-2" style="max-height:280px;overflow-y:auto;"><table class="table table-sm table-bordered mb-0"><thead style="background:#2c3e50;color:#fff;position:sticky;top:0;"><tr><th>Type</th><th>No.</th><th>Date</th><th>Description</th><th class="text-end">Amount</th></tr></thead><tbody>${mutasiRows||'<tr><td colspan="5" class="text-center text-muted py-3">No transactions</td></tr>'}</tbody></table></div></div>`;

                tabs += '</div>';
                $('#joInfoBody').html(tabs);
                $('#joInfoNote').text('Info only — JO selected above.');
            },
            error: function() { $('#joInfoBody').html('<div class="col-12 text-danger p-4"><i class="fas fa-exclamation-circle me-2"></i>Failed to load JO info.</div>'); }
        });
    });

    function joCard(icon, label, value) {
        return '<div class="col-6"><div style="background:#f8f9fa;border-radius:8px;padding:10px 14px;border:1px solid #e5e7eb;margin-bottom:4px;"><div style="font-size:.68rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;"><i class="' + icon + ' me-1"></i>' + label + '</div><div style="font-size:.85rem;font-weight:700;color:#374151;">' + (value || '—') + '</div></div></div>';
    }
    function joCardHighlight(icon, label, value, color) {
        return '<div class="col-6"><div style="background:#f0fdf4;border-radius:8px;padding:10px 14px;border:1px solid #bbf7d0;margin-bottom:4px;"><div style="font-size:.68rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;"><i class="' + icon + ' me-1"></i>' + label + '</div><div style="font-size:.92rem;font-weight:800;color:' + color + ';">' + (value || '—') + '</div></div></div>';
    }
});
</script>
@endpush
