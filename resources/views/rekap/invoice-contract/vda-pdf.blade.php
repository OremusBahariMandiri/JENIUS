<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>VDA - {{ $invoice->no_invoice ?? '-' }}</title>
    <style>
        @php echo '@page { size: A4 portrait; margin: 0; }'; @endphp

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            background: #fff;
            padding: 14mm 14mm 14mm 14mm;
        }

        /* ── Kop Surat ── */
        .kop-surat { width: 100%; margin-bottom: 2px; }
        .kop-surat img { width: 100%; height: auto; display: block; }

        hr.kop-line { display: none; margin: 0; }

        /* ── Address Block ── */
        .addr-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            margin-bottom: 6px;
        }
        .addr-table td {
            font-size: 9pt;
            padding: 1.2px 0;
            vertical-align: top;
        }
        .addr-label { width: 130px; white-space: nowrap; }
        .addr-sep   { width: 16px; text-align: center; padding: 0 6px; }

        /* ── Title ── */
        .doc-title {
            text-align: center;
            font-size: 12.5pt;
            font-weight: bold;
            letter-spacing: 3px;
            text-decoration: underline;
            margin: 10px 0 10px 0;
        }

        /* ── Info Box ── */
        .info-outer {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 10px;
        }
        .info-outer td {
            padding: 0;
            border: none;
        }
        .info-inner {
            width: 100%;
            border-collapse: collapse;
        }
        .info-inner tr td {
            padding: 3px 7px;
            font-size: 9pt;
            border: none;
        }
        .info-lbl { width: 90px; }
        .info-sep { width: 10px; text-align: center; }
        .info-val { font-weight: normal; }

        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 0;
        }

        /* header */
        .items-table thead th {
            background-color: #d9d9d9;
            border: 1px solid #000;
            padding: 5px 7px;
            font-weight: bold;
            vertical-align: middle;
        }
        .th-desc  { text-align: left;   width: 50%; }
        .th-usd   { text-align: center; width: 20%; }
        .th-idr   { text-align: right;  width: 30%; }

        /* body rows */
        .items-table tbody td {
            border: 1px solid #000;
            padding: 4px 7px;
            vertical-align: middle;
        }
        .td-desc  { text-align: left; }

        /* Amount cell: IDR/USD label left, number right — using nested table */
        .amt-wrap {
            width: 100%;
            border-collapse: collapse;
        }
        .amt-wrap td { border: none; padding: 0; }
        .amt-lbl {
            font-size: 8.5pt;
            font-weight: 700;
            white-space: nowrap;
            padding-right: 5px;
            color: #000;
            width: 1%;
        }
        .amt-num {
            text-align: right;
            white-space: nowrap;
        }

        /* subtotal / total rows */
        .row-sub td, .row-tot td {
            border: 1px solid #000;
            padding: 4px 7px;
            vertical-align: middle;
        }
        .row-sub .td-label,
        .row-tot .td-label {
            text-align: right;
            font-weight: bold;
            font-size: 9pt;
        }
        .row-tot td { font-weight: bold; }

        /* ── Terbilang ── */
        .terbilang {
            padding: 6px 0;
            font-size: 8.5pt;
            font-style: italic;
            font-weight: bold;
            margin-bottom: 12px;
        }

        /* ── Bottom ── */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .bottom-table td { vertical-align: top; padding: 0; border: none; }
        .remit-block { font-size: 8.5pt; line-height: 1.65; }
        .remit-block p { margin: 0; }
        .sign-block { text-align: center; font-size: 9pt; }
        .sign-gap { height: 130px; }
        .sign-name { font-weight: bold; }
        .sign-line { border-top: 1px solid #000; padding-top: 2px; display: inline-block; min-width: 130px; }
        .sign-role { font-size: 8.5pt; }
    </style>
</head>
<body>

    {{-- ── Kop Surat ── --}}
    @php
        $kopPath   = public_path('images/kop-surat-orindo.png');
        $kopBase64 = base64_encode(file_get_contents($kopPath));
        $kopSrc    = 'data:image/png;base64,' . $kopBase64;
    @endphp
    <div class="kop-surat">
        <img src="{{ $kopSrc }}" alt="Kop Surat">
    </div>
    <hr class="kop-line">

    {{-- ── Address Block ── --}}
    <table class="addr-table">
        <tr>
            <td class="addr-label">To</td>
            <td class="addr-sep">:</td>
            <td>{{ $invoice->to_customer ?? '' }}</td>
        </tr>
        <tr>
            <td class="addr-label">Att</td>
            <td class="addr-sep">:</td>
            <td>{{ $invoice->att ?? '' }}</td>
        </tr>
        <tr>
            <td class="addr-label">Office Number</td>
            <td class="addr-sep">:</td>
            <td>{{ $invoice->office_phone ?? '' }}</td>
        </tr>
        <tr>
            <td class="addr-label">Mobile Number</td>
            <td class="addr-sep">:</td>
            <td>{{ $invoice->mobile_phone ?? '' }}</td>
        </tr>
    </table>

    {{-- ── Title ── --}}
    <div class="doc-title">VESSEL'S DISBURSEMENT ACCOUNT</div>

    {{-- ── Info Box ── --}}
    @php
        $eta      = $invoice->eta ? $invoice->eta->format('d/m/Y') : '';
        $etd      = $invoice->etd ? $invoice->etd->format('d/m/Y') : '';
        $taPeriod = ($eta || $etd) ? trim($eta . ' - ' . $etd, ' -') : '';
        $currency = $invoice->currency ?? 'IDR';
    @endphp
    <table class="info-outer">
        <tr>
            <td style="width:50%; border-right:1px solid #000;">
                <table class="info-inner">
                    <tr>
                        <td class="info-lbl">Vessel</td>
                        <td class="info-sep">:</td>
                        <td class="info-val">{{ $invoice->kapal ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="info-lbl">Voyage</td>
                        <td class="info-sep">:</td>
                        <td class="info-val">{{ $invoice->no_voyage ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="info-lbl">Port/Country</td>
                        <td class="info-sep">:</td>
                        <td class="info-val">{{ $invoice->jetty ?? '' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table class="info-inner">
                    <tr>
                        <td class="info-lbl">GRT</td>
                        <td class="info-sep">:</td>
                        <td class="info-val">{{ $invoice->bar_code ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="info-lbl">Currency</td>
                        <td class="info-sep">:</td>
                        <td class="info-val">{{ $currency }}</td>
                    </tr>
                    <tr>
                        <td class="info-lbl">TA - TD</td>
                        <td class="info-sep">:</td>
                        <td class="info-val">{{ $taPeriod }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ── Items Table ── --}}
    @php
        $items         = $invoice->items;
        $grandTotalIdr = (float) $items->sum('nominal_invoice');
        $grandTotalUsd = (float) $items->sum('nominal_invoice_usd');
        // Use USD mode if any item has nominal_invoice_usd filled
        $isUsdMode     = $grandTotalUsd > 0;
    @endphp
    <table class="items-table">
        <thead>
            <tr>
                <th class="th-desc">DESCRIPTION</th>
                <th class="th-usd">AMOUNT USD</th>
                <th class="th-idr">AMOUNT IDR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            @php
                $isTax  = $item->item_type === 'tax';
                $pct    = $isTax ? ($item->joContractPajak?->persentase ?? null) : null;
                $desc   = $isTax
                    ? (($item->deskripsi ?? $item->joContractPajak?->nama_pajak ?? 'Tax') . ($pct ? ' ' . $pct . '%' : ''))
                    : ($item->joContractItem?->invoice?->invoice_typ ?? $item->deskripsi ?? '-');
                $nomIdr = (float)$item->nominal_invoice;
                $nomUsd = (float)$item->nominal_invoice_usd;
                $useUsd = $nomUsd > 0;
            @endphp
            <tr>
                <td class="td-desc">{{ $desc }}</td>
                <td style="white-space:nowrap;">USD&nbsp;&nbsp;<span style="float:right;">{{ $nomUsd > 0 ? number_format($nomUsd, 2, ',', '.') : '' }}</span></td>
                <td style="white-space:nowrap;">IDR&nbsp;&nbsp;<span style="float:right;">{{ ($nomUsd <= 0 && $nomIdr > 0) ? number_format($nomIdr, 2, ',', '.') : '' }}</span></td>
            </tr>
            @endforeach
        </tbody>
        {{-- SUBTOTAL --}}
        <tr class="row-sub">
            <td style="text-align:left; font-weight:bold; border:1px solid #000; padding:4px 7px; background:#e0e0e0;">SUBTOTAL</td>
            <td style="border:1px solid #000; padding:4px 7px; white-space:nowrap; background:#e0e0e0;">
                <strong>USD</strong>&nbsp;&nbsp;<strong style="float:right;">{{ $isUsdMode ? number_format($grandTotalUsd, 2, ',', '.') : '' }}</strong>
            </td>
            <td style="border:1px solid #000; padding:4px 7px; white-space:nowrap; background:#e0e0e0;">
                <strong>IDR</strong>&nbsp;&nbsp;<strong style="float:right;">{{ !$isUsdMode ? number_format($grandTotalIdr, 2, ',', '.') : '' }}</strong>
            </td>
        </tr>
        {{-- TOTAL --}}
        <tr class="row-tot">
            <td style="text-align:left; font-weight:bold; border:1px solid #000; padding:4px 7px; background:#e0e0e0;">TOTAL</td>
            <td style="border:1px solid #000; padding:4px 7px; white-space:nowrap; background:#e0e0e0;">
                <strong>USD</strong>&nbsp;&nbsp;<strong style="float:right;">{{ $isUsdMode ? number_format($grandTotalUsd, 2, ',', '.') : '' }}</strong>
            </td>
            <td style="border:1px solid #000; padding:4px 7px; white-space:nowrap; background:#e0e0e0;">
                <strong>IDR</strong>&nbsp;&nbsp;<strong style="float:right;">{{ !$isUsdMode ? number_format($grandTotalIdr, 2, ',', '.') : '' }}</strong>
            </td>
        </tr>
    </table>

    {{-- ── Terbilang ── --}}
    <div class="terbilang">
        TERBILANG : {{ strtoupper($terbilang) }}
    </div>

    {{-- ── Bottom: Remittance + Signature ── --}}
    @php
        $tglStr     = $invoice->tgl_invoice
            ? $invoice->tgl_invoice->format('d F Y')
            : now()->format('d F Y');
        $signerName = $companyProfile?->nama_direktur ?: 'Niko';
        $signerRole = $companyProfile?->jabatan ?: 'Direktur Utama';
    @endphp
    <table class="bottom-table">
        <tr>
            <td style="width:55%;">
                <div class="remit-block">
                    <p><em>Pls remit the funds to our company account :</em></p>
                    <p><strong>{{ $companyProfile?->nama_perusahaan ?? 'PT. ORINDO BANGUN SAMUDERA' }}</strong></p>
                    @if($invoice->coa)
                        <p>{{ $invoice->coa->invoice_display }}</p>
                        <p>A/C : {{ $invoice->coa->coa_code }} {{ $invoice->coa->number }}</p>
                        @if($invoice->coa->swift_code)
                            <p>SWIFT : {{ $invoice->coa->swift_code }}</p>
                        @endif
                    @endif
                </div>
            </td>
            <td style="width:45%; text-align:center; vertical-align:top;">
                <div class="sign-block">
                    <p style="margin-bottom:0;">Surabaya, {{ $tglStr }}</p>
                    <div class="sign-gap"></div>
                    <p><span class="sign-line"><strong class="sign-name">{{ $signerName }}</strong></span></p>
                    <p class="sign-role">{{ $signerRole }}</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
