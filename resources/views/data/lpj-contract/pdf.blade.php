<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>LPJ Contract - {{ $lpj->no_lpj_cont ?? '-' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            background: #fff;
        }
        .page { padding: 8mm 8mm 8mm 8mm; }

        /* ── KOP TABLE ── */
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td, .kop-table th {
            border: 1px solid #000;
            padding: 5px 10px;
            vertical-align: middle;
            font-size: 8.5pt;
        }

        /* Logo cell — no internal grid, merged block */
        .logo-cell {
            width: 30%;
            text-align: center;
            vertical-align: middle;
            padding: 10px 12px;
            border: 1px solid #000;
        }
        .logo-cell img  { max-width: 110px; max-height: 55px; display: block; margin: 0 auto 4px; }
        .logo-cell .company-name {
            font-weight: bold;
            font-size: 7.5pt;
            line-height: 1.3;
            margin-top: 3px;
        }

        /* ── TITLE ── */
        .lpj-title {
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
            padding: 10px 6px;
            border: 1px solid #000;
        }

        /* ── ITEMS TABLE ── */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .items-table th {
            background-color: #2c3e50;
            color: #fff;
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 8pt;
            text-align: center;
            font-weight: bold;
        }
        .items-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 8pt;
            vertical-align: middle;
        }
        .items-table .cat-row td {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8pt;
            color: #2c3e50;
        }
        .items-table .text-right  { text-align: right;  white-space: nowrap; }
        .items-table .text-center { text-align: center; }
        .items-table .blank-row td { height: 16px; }

        /* ── TOTAL ROW ── */
        .total-row td {
            font-weight: bold;
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 8.5pt;
        }

        /* ── TERBILANG ── */
        .terbilang-row td {
            font-style: italic;
            font-size: 7.5pt;
            border: 1px solid #000;
            padding: 4px 8px;
            background: #fffde7;
        }

        /* ── TTD ── */
        .ttd-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .ttd-table td {
            border: 1px solid #000;
            vertical-align: top;
            padding: 5px 8px;
            font-size: 8pt;
        }
        .ttd-header   { text-align: center; font-weight: bold; }
        .ttd-space    { height: 60px; }
        /* Nama field — centered label with underline space */
        .ttd-nama     { text-align: left; font-size: 7.5pt; padding-top: 4px; padding-bottom: 2px; }
        /* TGL field — pojok kiri */
        .ttd-tgl      { text-align: left; font-size: 7.5pt; padding-top: 2px; padding-bottom: 5px; }

        /* ── FOOTER ── */
        .footer-note {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 7.5pt;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="page">

@php
    /* ── Logo ── */
    $logoPath   = public_path('images/logo.png');
    $logoBase64 = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : '';

    /* ── Items: group by CA → by Category ── */
    $kasbonOrder  = [];
    $kasbonGroups = [];

    $totalHpp    = 0;
    $totalKasbon = 0;
    $totalLpj    = 0;

    foreach ($lpj->kasbons as $lpjKasbon) {
        $kasbon = $lpjKasbon->kasbonContract;
        if (!$kasbon) continue;

        $kNo = $kasbon->id_kasbon_cont;

        if (!isset($kasbonGroups[$kNo])) {
            $kasbonGroups[$kNo] = ['catOrder' => [], 'catGroups' => []];
            $kasbonOrder[]      = $kNo;
        }

        foreach ($kasbon->items as $kasbonItem) {
            $lpjItem     = $lpj->items->firstWhere('id_kasbon_cont_item', $kasbonItem->id_kasbon_cont_item);
            $cat         = optional(optional($kasbonItem->joContractItem)->invoice)->invoice_ctg ?? 'Uncategorized';
            $invoiceTyp  = optional(optional($kasbonItem->joContractItem)->invoice)->invoice_typ ?? $kasbonItem->id_kasbon_cont_item;

            $nilaiHpp    = (float) $kasbonItem->nilai_hpp_cont_item;
            $nilaiKasbon = (float) $kasbonItem->nilai_kasbon;
            $amountLpj   = $lpjItem ? (float) $lpjItem->amount_lpj : 0;

            $totalHpp    += $nilaiHpp;
            $totalKasbon += $nilaiKasbon;
            $totalLpj    += $amountLpj;

            if (!isset($kasbonGroups[$kNo]['catGroups'][$cat])) {
                $kasbonGroups[$kNo]['catGroups'][$cat] = [];
                $kasbonGroups[$kNo]['catOrder'][]      = $cat;
            }
            $kasbonGroups[$kNo]['catGroups'][$cat][] = [
                'typ'    => $invoiceTyp,
                'hpp'    => $nilaiHpp,
                'kasbon' => $nilaiKasbon,
                'lpj'    => $amountLpj,
            ];
        }
    }

    /* ── Blank row padding ── */
    $totalRows = 0;
    foreach ($kasbonOrder as $kNo) {
        foreach ($kasbonGroups[$kNo]['catOrder'] as $cat) {
            $totalRows += count($kasbonGroups[$kNo]['catGroups'][$cat]);
        }
    }
    $blankNeeded = max(0, 5 - $totalRows);
@endphp

{{-- ══════════════════════════════════════════════
     KOP
══════════════════════════════════════════════ --}}
<table class="kop-table">
<tbody>
    <tr>
        {{-- Logo cell: merged (rowspan 4), no internal grid --}}
        <td class="logo-cell" rowspan="4">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo">
            @endif
            <div class="company-name">PT. ORINDO BANGUN SAMUDERA</div>
        </td>
        <td style="width:20%;">No Document :</td>
        <td><strong>OBM-FIN-FRM-03</strong></td>
    </tr>
    <tr>
        <td>Tipe Job :</td>
        <td>Contract</td>
    </tr>
    <tr>
        <td>Kasbon :</td>
        <td>
            @foreach($lpj->kasbons as $lpjKasbon)
                @if($lpjKasbon->kasbonContract)
                    {{ $lpjKasbon->kasbonContract->id_kasbon_cont }}@if(!$loop->last), @endif
                @endif
            @endforeach
        </td>
    </tr>
    <tr>
        <td>Tanggal :</td>
        <td>{{ $lpj->date?->format('d/m/Y') ?? '-' }}</td>
    </tr>
</tbody>
</table>

{{-- ══════════════════════════════════════════════
     TITLE
══════════════════════════════════════════════ --}}
<table style="width:100%; border-collapse:collapse; margin-top:4px;">
<tbody>
    <tr>
        <td class="lpj-title">
            {{ $lpj->no_lpj_cont }}
            @if($lpj->joContract)
                - {{ $lpj->joContract->title ?? '' }}
                ({{ $lpj->joContract->no_jo_cont ?? '' }})
            @endif
        </td>
    </tr>
</tbody>
</table>

{{-- ══════════════════════════════════════════════
     ITEMS TABLE
     Kolom: Deskripsi | CA No | HPP | Kasbon | LPJ
══════════════════════════════════════════════ --}}
<table class="items-table" style="margin-top:6px;">
<thead>
    <tr>
        <th style="width:30%;">Deskripsi</th>
        <th style="width:18%;">CA No</th>
        <th style="width:17%;">HPP (IDR)</th>
        <th style="width:17%;">Kasbon (IDR)</th>
        <th style="width:18%;">LPJ (IDR)</th>
    </tr>
</thead>
<tbody>

    @foreach ($kasbonOrder as $kNo)
        @php
            $catOrder  = $kasbonGroups[$kNo]['catOrder'];
            $catGroups = $kasbonGroups[$kNo]['catGroups'];
        @endphp

        @foreach ($catOrder as $cat)
            {{-- Category header --}}
            <tr class="cat-row">
                <td colspan="5">{{ $cat }}</td>
            </tr>

            @foreach ($catGroups[$cat] as $item)
            <tr>
                <td>{{ $item['typ'] }}</td>
                <td class="text-center" style="font-size:7.5pt; color:#1e3a5f;">{{ $kNo }}</td>
                <td class="text-right">{{ number_format($item['hpp'],    2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item['kasbon'], 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item['lpj'],    2, ',', '.') }}</td>
            </tr>
            @endforeach
        @endforeach
    @endforeach

    {{-- Blank rows --}}
    @for ($b = 0; $b < $blankNeeded; $b++)
    <tr class="blank-row">
        <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
    </tr>
    @endfor

    {{-- Total --}}
    <tr class="total-row">
        <td colspan="2"><strong>Total</strong></td>
        <td class="text-right">{{ number_format($totalHpp,    2, ',', '.') }}</td>
        <td class="text-right">{{ number_format($totalKasbon, 2, ',', '.') }}</td>
        <td class="text-right">{{ number_format($totalLpj,    2, ',', '.') }}</td>
    </tr>

</tbody>
</table>

{{-- ══════════════════════════════════════════════
     NOTE
══════════════════════════════════════════════ --}}
@if($lpj->note)
<table style="width:100%; border-collapse:collapse; margin-top:4px;">
<tbody>
    <tr>
        <td style="border:1px solid #000; width:15%; font-weight:bold; padding:5px 8px; font-size:8pt;">Keterangan :</td>
        <td style="border:1px solid #000; padding:5px 8px; font-size:8pt;">{{ $lpj->note }}</td>
    </tr>
</tbody>
</table>
@endif

{{-- ══════════════════════════════════════════════
     TTD — 3 kolom: Kasir | Kepala Finance | Accounting
     Setiap kolom: [label atas] [ruang tanda tangan] [Nama: ___] [TGL: ___ (kiri)]
══════════════════════════════════════════════ --}}
<table class="ttd-table" style="margin-top:8px;">
<tbody>
    {{-- Baris label jabatan --}}
    <tr>
        <td class="ttd-header">Kasir</td>
        <td class="ttd-header">Kepala Finance</td>
        <td class="ttd-header">Accounting</td>
    </tr>
    {{-- Ruang tanda tangan --}}
    <tr>
        <td class="ttd-space">&nbsp;</td>
        <td class="ttd-space">&nbsp;</td>
        <td class="ttd-space">&nbsp;</td>
    </tr>
    {{-- Baris Nama --}}
    <tr>
        <td class="ttd-nama">&nbsp;</td>
        <td class="ttd-nama">&nbsp;</td>
        <td class="ttd-nama">&nbsp;</td>
    </tr>
    {{-- Baris TGL (pojok kiri) --}}
    <tr>
        <td class="ttd-tgl">TGL &nbsp;: </td>
        <td class="ttd-tgl">TGL &nbsp;: </td>
        <td class="ttd-tgl">TGL &nbsp;: </td>
    </tr>
</tbody>
</table>

{{-- ══════════════════════════════════════════════
     FOOTER NOTE
══════════════════════════════════════════════ --}}

</div>
</body>
</html>