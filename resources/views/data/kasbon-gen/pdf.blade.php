<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Kasbon General - {{ $kasbonGen->id_kasbon_gen ?? '-' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            background: #fff;
        }

        .page {
            padding: 15mm 15mm 15mm 15mm;
        }

        /* ── Title ── */
        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 6px;
        }

        hr.title-line {
            border: none;
            border-top: 1.5px solid #000;
            margin-bottom: 8px;
        }

        /* ── Info Header ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            margin-top: 50px;
        }

        .info-table td {
            padding: 1.5px 0;
            font-size: 9pt;
            vertical-align: top;
        }

        .info-label {
            width: 18%;
            font-weight: normal;
            white-space: nowrap;
        }

        .info-colon {
            width: 2%;
            text-align: center;
        }

        .info-value {
            width: 30%;
            font-weight: normal;
        }

        .info-spacer {
            width: 5%;
        }

        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 8.5pt;
            border: 1px solid #000;
        }

        .items-table thead tr th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 5px 6px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }

        .items-table tbody tr td {
            border: 0.5px solid #aaa;
            padding: 4px 6px;
            vertical-align: top;
        }

        .items-table tfoot tr td {
            border: 1px solid #000;
            padding: 5px 6px;
            font-weight: bold;
            background-color: #f2f2f2;
            vertical-align: top;
        }

        /* Category row */
        .row-category td {
            background-color: #f2f2f2 !important;
            font-weight: bold;
            /* border: 1px solid #000; */
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        /* Item note sub-text */
        .item-note {
            font-size: 7.5pt;
            color: #555;
            font-style: italic;
            margin-top: 1px;
        }

        /* ── Terbilang ── */
        .terbilang {
            font-size: 8.5pt;
            color: #000;
            font-style: italic;
            margin: 6px 0 14px 0;
        }

        .terbilang span.label {
            font-style: normal;
            font-weight: bold;
        }

        /* ── Signature ── */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .signature-table td {
            width: 33.33%;
            text-align: center;
            padding: 4px;
            vertical-align: top;
            font-size: 9pt;
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 60%;
            margin-left: auto;
            margin-right: auto;
        }

        /* ── Kop Surat ── */
        .kop-surat {
            width: 100%;
            margin-bottom: 15px;
            margin-top: -50px;
        }

        .kop-surat img {
            width: 100%;
            height: auto;
            display: block;
        }

        hr.kop-line {
            border: none;
            border-top: 2px solid #000;
            margin-bottom: 10px;
        }

        .col-desc {
            width: 50%;
        }

        .col-ca {
            width: 50%;
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- ── Kop Surat ── --}}
        @php
            $kopPath   = public_path('images/kop-surat-orindo.png');
            $kopBase64 = base64_encode(file_get_contents($kopPath));
            $kopSrc    = 'data:image/png;base64,' . $kopBase64;
        @endphp
        <div class="kop-surat">
            <img src="{{ $kopSrc }}" alt="Kop Surat">
        </div>

        {{-- ── Title ── --}}
        <div class="doc-title">Cash Advance General</div>

        {{-- ── Info Header ── --}}
        <table style="width:100%; border-collapse:collapse; margin-bottom:10px; margin-top:50px;">
            <tbody>
                <tr>
                    {{-- KIRI --}}
                    <td style="width:50%; vertical-align:top; padding-right:10px;">
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">CA Number</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    <strong>{{ $kasbonGen->id_kasbon_gen ?? '-' }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">CA Date</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonGen->tgl_kasbon ? $kasbonGen->tgl_kasbon->format('d M Y') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">Note</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonGen->note ?? '-' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    {{-- KANAN --}}
                    <td style="width:50%; vertical-align:top; padding-left:100px;">
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">Departemen</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonGen->departemen ? $kasbonGen->departemen->nama_dep : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">Branch</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonGen->cabang ? $kasbonGen->cabang->nama_branch : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">Release To</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonGen->release ? $kasbonGen->release->nama_release : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">Release Date</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonGen->tgl_release ? $kasbonGen->tgl_release->format('d M Y') : '-' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- ── Items Table ──
             Sumber: kasbonGen->items (langsung, tanpa JO).
             Group by invoice_ctg — identik dengan tampilan tabel di edit view.
             Kolom: DESCRIPTION | CA Amount (IDR)
        --}}
        @php
            $groupedItems = $kasbonGen->items->groupBy(
                fn($i) => optional($i->invoice)->invoice_ctg ?? 'Uncategorized'
            );
            $totalCA = 0;
        @endphp

        <div style="border-right: 1px solid #000;">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-desc">DESCRIPTION</th>
                        <th class="col-ca">CA Amount (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupedItems as $category => $items)
                        <tr class="row-category">
                            <td colspan="2"><strong>{{ strtoupper($category) }}</strong></td>
                        </tr>

                        @foreach ($items as $item)
                            @php
                                $invoiceTyp   = optional($item->invoice)->invoice_typ ?? $item->id_md_invoice;
                                $nilaiKasbon  = (float) $item->nilai_kasbon;
                                $totalCA     += $nilaiKasbon;
                            @endphp
                            <tr>
                                <td class="text-left">{{ $invoiceTyp }}</td>
                                <td class="text-right">{{ number_format($nilaiKasbon, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-left"><strong>GRAND TOTAL</strong></td>
                        <td class="text-right">{{ number_format($totalCA, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- ── Terbilang ── --}}
        <div class="terbilang">
            <span class="label">Terbilang :</span>
            <em>{{ $terbilang }}</em>
        </div>

        {{-- ── Signature Area ── --}}
        <table class="signature-table">
            <tr>
                <td>Prepared By,</td>
                <td>Reviewed By,</td>
                <td>Approved By,</td>
            </tr>
            <tr>
                <td style="height: 70px;"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <div class="signature-line"></div>
                </td>
                <td>
                    <div class="signature-line"></div>
                </td>
                <td>
                    <div class="signature-line"></div>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>