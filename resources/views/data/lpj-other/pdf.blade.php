<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ Other - {{ $lpj->no_lpj_other }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; font-size: 9pt; color: #1a1a1a; }

        /* KOP */
        .kop { text-align:center; margin-bottom:12px; border-bottom:2.5px solid #1e3a5f; padding-bottom:10px; }
        .kop .company-name { font-size:14pt; font-weight:700; color:#1e3a5f; letter-spacing:.5px; }
        .kop .company-sub  { font-size:8pt; color:#4b5563; margin-top:2px; }
        .kop .doc-title    { font-size:12pt; font-weight:700; color:#1e3a5f; margin-top:8px; letter-spacing:.5px; }

        /* META INFO */
        .meta-table { width:100%; margin-bottom:10px; border-collapse:collapse; }
        .meta-table td { padding:3px 6px; font-size:8.5pt; vertical-align:top; }
        .meta-table .label { width:110px; font-weight:700; color:#374151; }
        .meta-table .colon { width:10px; text-align:center; }
        .meta-table .value { color:#1a1a1a; }

        /* ITEMS TABLE */
        .items-table { width:100%; border-collapse:collapse; margin-bottom:10px; }
        .items-table th {
            background:#1e3a5f; color:white;
            padding:6px 8px; font-size:8pt; font-weight:700;
            text-align:center; border:1px solid #1e3a5f;
        }
        .items-table td {
            border:1px solid #d1d5db; padding:5px 7px;
            font-size:8pt; vertical-align:middle;
        }
        .items-table .text-right  { text-align:right; }
        .items-table .text-center { text-align:center; }
        .items-table .ctg-row td  { background:#f0fdf4; font-weight:700; color:#065f46; }
        .items-table .total-row td { background:#1e3a5f; color:white; font-weight:700; }
        .items-table .row-from-lpj td { background:#eff6ff; }

        /* Selisih colors */
        .positive { color:#16a34a; font-weight:700; }
        .negative { color:#dc2626; font-weight:700; }

        /* TERBILANG */
        .terbilang-box {
            border:1px solid #d1d5db; border-radius:4px;
            padding:7px 12px; margin-bottom:12px;
            font-size:8.5pt; background:#f9fafb;
        }
        .terbilang-box .tb-label { font-weight:700; color:#374151; font-size:7.5pt; margin-bottom:2px; }
        .terbilang-box .tb-value { font-style:italic; color:#1e3a5f; }

        /* TTD */
        .ttd-section { width:100%; margin-top:18px; }
        .ttd-section table { width:100%; }
        .ttd-section td { text-align:center; padding:0 10px; vertical-align:top; width:33%; }
        .ttd-section .ttd-title { font-size:8.5pt; font-weight:700; margin-bottom:50px; }
        .ttd-section .ttd-name  { border-top:1px solid #1a1a1a; padding-top:4px; font-size:8.5pt; font-weight:700; }
        .ttd-section .ttd-role  { font-size:7.5pt; color:#6b7280; }

        .page-footer { text-align:right; font-size:7pt; color:#9ca3af; margin-top:10px; border-top:1px solid #e5e7eb; padding-top:4px; }

        .badge-from-lpj {
            display:inline-block; background:#3b82f6; color:white;
            font-size:6.5pt; padding:1px 5px; border-radius:3px; margin-left:4px;
        }
    </style>
</head>
<body>

{{-- KOP --}}
<div class="kop">
    <div class="company-name">PT. JENIUS MITRA JASA</div>
    <div class="company-sub">Jl. Contoh No. 123, Kota, Provinsi | Telp: 021-XXXXXX</div>
    <div class="doc-title">LAPORAN PERTANGGUNGJAWABAN (LPJ) — OTHER</div>
</div>

{{-- META INFO --}}
@php
    $joOther = $lpj->joOther;
@endphp
<table class="meta-table">
    <tr>
        <td class="label">No. LPJ</td>
        <td class="colon">:</td>
        <td class="value fw-bold" style="color:#1e3a5f;">{{ $lpj->no_lpj_other }}</td>
        <td class="label">No. JO</td>
        <td class="colon">:</td>
        <td class="value">{{ $joOther->no_jo_other ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Tanggal</td>
        <td class="colon">:</td>
        <td class="value">{{ $lpj->date ? $lpj->date->format('d M Y') : '-' }}</td>
        <td class="label">Title JO</td>
        <td class="colon">:</td>
        <td class="value">{{ $joOther->title ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Note</td>
        <td class="colon">:</td>
        <td class="value" colspan="4">{{ $lpj->note ?? '-' }}</td>
    </tr>
</table>

{{-- ITEMS TABLE --}}
@php
    // Build grouped structure: kasbonNo → ctg → items
    $kasbonOrder  = [];
    $catGroups    = [];
    $totalKasbon  = 0;
    $totalLpj     = 0;

    foreach ($lpj->kasbons as $lpjKasbon) {
        $kasbon = $lpjKasbon->kasbonOther;
        if (!$kasbon) continue;

        $kNo = $kasbon->id_kasbon_other;
        if (!in_array($kNo, $kasbonOrder)) $kasbonOrder[] = $kNo;

        foreach ($kasbon->items as $kasbonItem) {
            $invoiceTyp  = $kasbonItem->joOtherItem->invoice->invoice_typ  ?? '-';
            $cat         = $kasbonItem->joOtherItem->invoice->invoice_ctg  ?? '-';
            $nilaiKasbon = (float) $kasbonItem->nilai_kasbon;

            // Find matching LPJ item
            $lpjItem   = $lpj->items->firstWhere('id_kasbon_other_item', $kasbonItem->id_kasbon_other_item);
            $amountLpj = $lpjItem ? (float) $lpjItem->amount_lpj : 0;
            $isFromLpj = !empty($kasbonItem->joOtherItem->origin_lpj_other ?? '');

            $totalKasbon += $nilaiKasbon;
            $totalLpj    += $amountLpj;

            $catGroups[$kNo][$cat][] = [
                'typ'       => $invoiceTyp,
                'kasbon'    => $nilaiKasbon,
                'lpj'       => $amountLpj,
                'from_lpj'  => $isFromLpj,
            ];
        }
    }

    $totalSelisih = $totalKasbon - $totalLpj;
    $no = 1;
@endphp

<table class="items-table">
    <thead>
        <tr>
            <th style="width:5%;">No</th>
            <th style="width:18%;">CA No</th>
            <th style="width:30%;">Deskripsi</th>
            <th style="width:16%;">Kasbon (IDR)</th>
            <th style="width:16%;">LPJ (IDR)</th>
            <th style="width:15%;">Selisih (IDR)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($kasbonOrder as $kNo)
            @if(!empty($catGroups[$kNo]))
                @foreach($catGroups[$kNo] as $cat => $items)
                    {{-- Category row --}}
                    <tr class="ctg-row">
                        <td class="text-center" style="color:#6b7280; font-size:7.5pt;">{{ $no }}</td>
                        <td class="text-center" style="font-size:7.5pt; color:#1e3a5f; font-weight:700;">{{ $kNo }}</td>
                        <td colspan="4" style="font-weight:700; color:#065f46;">{{ $cat }}</td>
                    </tr>
                    @foreach($items as $item)
                        @php
                            $selisih = $item['kasbon'] - $item['lpj'];
                            $selisihClass = $selisih >= 0 ? 'positive' : 'negative';
                        @endphp
                        <tr class="{{ $item['from_lpj'] ? 'row-from-lpj' : '' }}">
                            <td class="text-center">{{ $no }}</td>
                            <td class="text-center" style="font-size:7.5pt; color:#6b7280;"></td>
                            <td>
                                {{ $item['typ'] }}
                                @if($item['from_lpj'])
                                    <span class="badge-from-lpj">From LPJ</span>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($item['kasbon'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item['lpj'], 0, ',', '.') }}</td>
                            <td class="text-right {{ $selisihClass }}">
                                {{ number_format($selisih, 0, ',', '.') }}
                            </td>
                        </tr>
                        @php $no++; @endphp
                    @endforeach
                @endforeach
            @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="3" class="text-right fw-bold">TOTAL</td>
            <td class="text-right">{{ number_format($totalKasbon, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($totalLpj, 0, ',', '.') }}</td>
            <td class="text-right {{ $totalSelisih >= 0 ? 'positive' : 'negative' }}"
                style="color:{{ $totalSelisih >= 0 ? '#86efac' : '#fca5a5' }};">
                {{ number_format($totalSelisih, 0, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>

{{-- TERBILANG --}}
<div class="terbilang-box">
    <div class="tb-label">Terbilang (Total LPJ):</div>
    <div class="tb-value">{{ $terbilang }}</div>
</div>

{{-- TTD --}}
<div class="ttd-section">
    <table>
        <tr>
            <td>
                <div class="ttd-title">Dibuat oleh,</div>
                <div class="ttd-name">___________________</div>
                <div class="ttd-role">Pembuat LPJ</div>
            </td>
            <td>
                <div class="ttd-title">Diperiksa oleh,</div>
                <div class="ttd-name">___________________</div>
                <div class="ttd-role">Manager</div>
            </td>
            <td>
                <div class="ttd-title">Disetujui oleh,</div>
                <div class="ttd-name">___________________</div>
                <div class="ttd-role">Direktur</div>
            </td>
        </tr>
    </table>
</div>

<div class="page-footer">
    Dicetak: {{ now()->format('d M Y H:i') }} | {{ $lpj->no_lpj_other }}
</div>

</body>
</html>