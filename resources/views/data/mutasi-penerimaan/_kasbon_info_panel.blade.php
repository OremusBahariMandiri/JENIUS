{{-- Reusable kasbon/refund info panel --}}
@php $pid = isset($prefix) ? "kasbon_{$vid}" : "gen_{$vid}"; @endphp
<div class="row g-2 mb-2">

    {{-- JO No. & Customer: hanya untuk refund (kasbon_ prefix) --}}
    @if(isset($prefix))
    <div class="col-md-6">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
            <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">JO No.</div>
            <div id="ki_nomor_jo-{{ $pid }}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
        </div>
    </div>
    <div class="col-md-6">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
            <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Customer</div>
            <div id="ki_customer-{{ $pid }}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
            <input type="hidden" id="input_id_md_cust_ki-{{ $pid }}" value="">
        </div>
    </div>
    @else
    {{-- Hidden elements tetap ada agar JS tidak error --}}
    <span id="ki_nomor_jo-{{ $pid }}" style="display:none;"></span>
    <span id="ki_customer-{{ $pid }}" style="display:none;"></span>
    <input type="hidden" id="input_id_md_cust_ki-{{ $pid }}" value="">
    @endif

    {{-- Dept & Branch hidden (nilai tetap diisi JS) --}}
    <span id="ki_dep-{{ $pid }}" style="display:none;"></span>
    <input type="hidden" id="input_id_md_dep_ki-{{ $pid }}" value="">
    <span id="ki_branch-{{ $pid }}" style="display:none;"></span>
    <input type="hidden" id="input_id_md_branch_ki-{{ $pid }}" value="">

    {{-- Department / Branch / Release To / Vessel: tampil untuk semua tipe, di atas card kuning --}}
    @if(isset($prefix))
    {{-- Refund: hidden inputs pakai id tanpa suffix pid --}}
    <input type="hidden" id="input_dep-{{ $vid }}" value="">
    <input type="hidden" id="input_branch-{{ $vid }}" value="">
    <input type="hidden" id="input_release_to_ref-{{ $vid }}" value="">
    <div class="col-md-4">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
            <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Department</div>
            <div id="display_dep_ref-{{ $vid }}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
            <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Branch</div>
            <div id="display_branch_ref-{{ $vid }}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
            <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Release To</div>
            <div id="display_release_to_ref-{{ $vid }}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
        </div>
    </div>
    <div class="col-md-6">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
            <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Vessel</div>
            <div id="display_vessel_ref-{{ $vid }}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
        </div>
    </div>
    <div class="col-md-6">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
            <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Port</div>
            <div id="display_port_ref-{{ $vid }}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
        </div>
    </div>
    @else
    {{-- General: hidden inputs pakai id tanpa suffix pid --}}
    <input type="hidden" id="input_dep_gen-{{ $vid }}" value="">
    <input type="hidden" id="input_branch_gen-{{ $vid }}" value="">
    <input type="hidden" id="input_release_to_gen-{{ $vid }}" value="">
    <div class="col-md-4">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
            <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Department</div>
            <div id="display_dep_gen-{{ $vid }}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
            <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Branch</div>
            <div id="display_branch_gen-{{ $vid }}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
            <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Release To</div>
            <div id="display_release_to_gen-{{ $vid }}" style="font-size:.88rem;font-weight:700;color:#1e293b;">—</div>
        </div>
    </div>
    @endif

    {{-- Refund Amount (Selisih LPJ) — selalu di bawah --}}
    <div class="col-12">
        <div style="background:#fefce8;border:1px solid #fde047;border-radius:8px;padding:10px 14px;">
            <div style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Refund Amount (Selisih LPJ)</div>
            <div id="ki_selisih-{{ $pid }}" style="font-size:.95rem;font-weight:700;color:#d97706;">—</div>
            <input type="hidden" id="input_nilai_ki-{{ $pid }}" value="0">
        </div>
    </div>
</div>