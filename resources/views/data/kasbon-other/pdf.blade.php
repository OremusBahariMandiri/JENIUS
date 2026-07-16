<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Kasbon Other - {{ $kasbonOther->id_kasbon_other ?? '-' }}</title>
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
            width: 40%;
        }

        .col-sell {
            width: 20%;
        }

        .col-hpp {
            width: 20%;
        }

        .col-ca {
            width: 20%;
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
        <div class="doc-title">Cash Advance (Kasbon) Other</div>

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
                                    <strong>{{ $kasbonOther->id_kasbon_other ?? '-' }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">CA Date</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonOther->tgl_kasbon ? $kasbonOther->tgl_kasbon->format('d M Y') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">JO Number</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonOther->joOther ? $kasbonOther->joOther->no_jo_other : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">JO Title</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonOther->joOther ? $kasbonOther->joOther->title : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">Note</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonOther->note ?? '-' }}
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
                                    {{ $kasbonOther->departemen ? $kasbonOther->departemen->nama_dep : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">Branch</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonOther->cabang ? $kasbonOther->cabang->nama_branch : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">Release To</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonOther->release ? $kasbonOther->release->nama_release : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">Release Date</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonOther->tgl_release ? $kasbonOther->tgl_release->format('d M Y') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-size:9pt; padding:1.5px 0;">Customer</td>
                                <td style="width:8px; text-align:center; font-size:9pt; padding:1.5px 4px;">:</td>
                                <td style="font-size:9pt; padding:1.5px 0;">
                                    {{ $kasbonOther->joOther && $kasbonOther->joOther->customer
                                        ? $kasbonOther->joOther->customer->customer
                                        : '-' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- ── Items Table ── --}}
        @php
            // lookup: id_jo_other_item => KasbonOtherItem
            $kasbonLookup = $kasbonOther->items->keyBy('id_jo_other_item');

            // Sumber baris = seluruh JO Other items (sama persis dengan $mergedItems di edit view)
            $joItems = $kasbonOther->joOther
                ? $kasbonOther->joOther->items
                : collect();

            $groupedItems = $joItems->groupBy(
                fn($ji) => optional($ji->invoice)->invoice_ctg ?? 'Uncategorized'
            );

            $totalHargaJual = 0;
            $totalHpp       = 0;
            $totalCA        = 0;
        @endphp

        <div style="border-right: 1px solid #000;">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-desc">DESCRIPTION</th>
                        <th class="col-sell">Selling Price (IDR)</th>
                        <th class="col-hpp">Total CA / HPP (IDR)</th>
                        <th class="col-ca">CA Amount (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupedItems as $category => $items)
                        <tr class="row-category">
                            <td colspan="4"><strong>{{ strtoupper($category) }}</strong></td>
                        </tr>

                        @foreach ($items as $joItem)
                            @php
                                $kasbonItem  = $kasbonLookup->get($joItem->id_jo_other_item);
                                $invoiceTyp  = optional($joItem->invoice)->invoice_typ ?? $joItem->id_jo_other_item;
                                $hargaJual   = (float) $joItem->hargajual_idr;
                                $hppOps      = (float) $joItem->hpp_ops;
                                $nilaiKasbon = $kasbonItem ? (float) $kasbonItem->nilai_kasbon : 0;

                                $totalHargaJual += $hargaJual;
                                $totalHpp       += $hppOps;
                                $totalCA        += $nilaiKasbon;
                            @endphp
                            <tr>
                                <td class="text-left">{{ $invoiceTyp }}</td>
                                <td class="text-right">{{ number_format($hargaJual, 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($hppOps, 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($nilaiKasbon, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-left"><strong>GRAND TOTAL</strong></td>
                        <td class="text-right">{{ number_format($totalHargaJual, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalHpp, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalCA, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- ── Terbilang — berdasarkan total CA Amount ── --}}
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