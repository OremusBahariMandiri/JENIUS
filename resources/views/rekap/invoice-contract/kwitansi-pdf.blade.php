<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Kwitansi - {{ $invoice->no_invoice ?? '-' }}</title>
    <style>
        @php echo '@page { size: A4 portrait; margin: 0; }'; @endphp

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            background: #fff;
            padding: 8mm 12mm;
        }

        /* ── Outer: single table, border-collapse = NO double borders ── */
        .kwit-wrap {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #1e3a5f;
            margin-bottom: 4mm;
            page-break-inside: avoid;
        }
        .kwit-wrap > tbody > tr > td {
            border: 1.5px solid #1e3a5f;
        }

        /* Kop cell */
        .kop-cell {
            text-align: center;
            padding: 2px 0;
            border-left: 0 !important;
            border-right: 0 !important;
            border-top: 0 !important;
            border-bottom: 1.5px solid #1e3a5f !important;
        }
        .kop-cell img { width: 70%; height: auto; display: inline-block; }

        /* Left vertical KWITANSI strip */
        .side-label {
            width: 7mm;
            background: #fff;
            color: #1e3a5f;
            text-align: center;
            vertical-align: middle;
            font-size: 7.5pt;
            font-weight: bold;
            border-right: 0 !important;
            border-left: 0 !important;
            border-top: 0 !important;
            border-bottom: 0 !important;
        }
        .side-label div { display: block; text-align: center; line-height: 1.6; }

        /* Center: field rows */
        .center-col {
            width: 55%;
            border-right: 0 !important;
            border-left: 0 !important;
            border-top: 0 !important;
            border-bottom: 0 !important;
            vertical-align: top;
            padding-top: 8px;
        }

        .field-table {
            width: 100%;
            border-collapse: collapse;
        }
        .field-table tr { border-bottom: 1px solid #c8d0da; }
        .field-table tr:last-child { border-bottom: none; }
        .field-table td { padding: 7px 8px; vertical-align: top; font-size: 8pt; }
        .fl { width: 36mm; font-weight: bold; white-space: nowrap; }
        .fs { width: 5mm; text-align: center; }
        .fv { }
        .fv.money { font-weight: bold; font-size: 10.5pt; }

        .terbilang-row {
            font-size: 7.5pt;
            font-style: italic;
            color: #333;
            padding: 2px 8px 2px 8px;
            border-top: 1px dashed #c8d0da;
        }

        /* Right: Alamat + Vessel */
        .right-col {
            width: 38%;
            padding: 8px 8px 4px 20px;
            font-size: 8pt;
            line-height: 1.5;
            vertical-align: top;
            border-left: 0 !important;
            border-top: 0 !important;
            border-bottom: 0 !important;
            border-right: 0 !important;
        }
        .ri-label { font-weight: bold; color: #1e3a5f; }

        /* ── Footer row cells (inside kwit-wrap table) ── */
        .ft-row td {
            padding: 6px 10px;
            vertical-align: middle;
            font-size: 8pt;
            height: 55px;
        }
        .ft-thanks {
            width: 20%;
            border-right: 0 !important;
            border-left: 0 !important;
            border-bottom: 0 !important;
            color: #555;
            vertical-align: middle;
            white-space: nowrap;
        }
        .ft-sign {
            width: 45%;
            border-right: 0 !important;
            border-left: 0 !important;
            border-bottom: 0 !important;
            text-align: center;
            vertical-align: top;
            padding-top: 10px;
            padding-left: 20px;
            padding-right: 20px;
        }
        .sign-date { font-size: 8pt; margin-bottom: 4px; }
        .sign-gap { height: 70px; }
        .sign-line { border-top: 1px solid #000; padding-top: 2px; display: inline-block; min-width: 70px; }
        .sign-name { font-weight: bold; font-size: 8pt; }
        .sign-role { font-size: 7.5pt; }
        .ft-noinv {
            background: #fff;
            color: #1e3a5f;
            font-weight: bold;
            font-size: 8.5pt;
            white-space: nowrap;
            vertical-align: top;
            padding-top: 10px;
            border-left: 0 !important;
            border-right: 0 !important;
            border-bottom: 0 !important;
        }
    </style>
</head>
<body>

@php
    $jo       = $invoice->joContract;
    $contract = $jo?->contract;
    $customer = $invoice->customer ?? $contract?->customer ?? null;
    $custName = $invoice->to_customer ?? $customer?->customer ?? '-';
    $custAtt  = $invoice->att ?? '';
    $custAddr = $companyProfile?->alamat ?? $invoice->address ?? $customer?->address ?? '';
    $vessel   = $invoice->kapal ?? '';
    $noInv    = $invoice->no_invoice ?? '-';

    $tglStr = $invoice->tgl_invoice
        ? $invoice->tgl_invoice->format('d F Y')
        : now()->format('d F Y');

    $signerName  = $companyProfile?->nama_ttd_kwitansi ?: ($companyProfile?->nama_direktur ?: 'Direktur');
    $signerRole  = $companyProfile?->jabatan_ttd_kwitansi ?: ($companyProfile?->jabatan ?: 'Direktur Utama');

    $penerimaStr = $custName . ($custAtt ? ' / ' . $custAtt : '');
    $items       = $invoice->items->whereIn('item_type', ['item', 'tax'])->values();

    $kopPath = public_path('images/logo-pt.png');
    $kopSrc  = 'data:image/png;base64,' . base64_encode(file_get_contents($kopPath));
@endphp

@forelse($items as $item)
@php
    $nominal       = (float) $item->nominal_invoice;
    if ($item->item_type === 'tax') {
        $pct  = $item->joContractPajak?->persentase ?? null;
        $desc = ($item->deskripsi ?? $item->joContractPajak?->nama_pajak ?? 'Tax') . ($pct ? ' ' . $pct . '%' : '');
    } else {
        $desc = $item->joContractItem?->invoice?->invoice_typ ?? $item->deskripsi ?? '-';
    }
    $terbilangItem = strtoupper($terbilangFn((int) round($nominal))) . ' RUPIAH';
@endphp

<table class="kwit-wrap">
    <tbody>
        {{-- ── ROW 1: KOP SURAT ── --}}
        <tr>
            <td colspan="3" class="kop-cell">
                <img src="{{ $kopSrc }}" alt="Kop Surat">
            </td>
        </tr>

        {{-- ── ROW 2: BODY strip | fields | alamat+vessel ── --}}
        <tr>
            {{-- KWITANSI vertical strip --}}
            <td class="side-label">
                <div>K</div><div>W</div><div>I</div><div>T</div>
                <div>A</div><div>N</div><div>S</div><div>I</div>
            </td>

            {{-- Fields --}}
            <td class="center-col">
                <table class="field-table">
                    <tr>
                        <td class="fl">Telah diterima dari</td>
                        <td class="fs">:</td>
                        <td class="fv">{{ $penerimaStr }}</td>
                    </tr>
                    <tr>
                        <td class="fl">Uang sejumlah</td>
                        <td class="fs">:</td>
                        <td class="fv money">Rp{{ number_format($nominal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fl">Untuk</td>
                        <td class="fs">:</td>
                        <td class="fv">{{ $desc }}</td>
                    </tr>
                </table>
                <div class="terbilang-row">{{ $terbilangItem }}</div>
            </td>

            {{-- Alamat + Vessel --}}
            <td class="right-col">
                <div class="ri-label">Alamat</div>
                <div>{{ $custAddr ?: '-' }}</div>
                <div class="ri-label" style="margin-top:8px;">VESSEL :</div>
                <div>{{ $vessel }}</div>
            </td>
        </tr>

        {{-- ── ROW 3: FOOTER ── --}}
        <tr class="ft-row">
            <td class="ft-thanks">Terima kasih</td>
            <td class="ft-sign">
                <div class="sign-date">{{ $tglStr }}</div>
                <div class="sign-gap"></div>
                <span class="sign-line">
                    <span class="sign-name">{{ $signerName }}</span>
                </span>
                <div class="sign-role">{{ $signerRole }}</div>
            </td>
            <td class="ft-noinv">NO INV : {{ $noInv }}</td>
        </tr>
    </tbody>
</table>
@empty
<p style="text-align:center;color:#888;font-style:italic;padding:20px;">Tidak ada item untuk dicetak.</p>
@endforelse

</body>
</html>
