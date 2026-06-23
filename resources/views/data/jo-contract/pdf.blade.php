<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>JO Contract - {{ $joContract->no_jo_cont ?? '-' }}</title>
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
            border: 1px solid #000;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }

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

        .col-no   { width: 5%;  }
        .col-desc { width: 35%; }
        .col-idr  { width: 15%; }
        .col-usd  { width: 15%; }
        .col-hpp  { width: 13%; }
        .col-sell { width: 17%; }
    </style>
</head>

<body>
    <div class="page">

        {{-- ── Kop Surat ── --}}
        @php
            $kopPath = public_path('images/kop-surat-orindo.png');
            $kopBase64 = base64_encode(file_get_contents($kopPath));
            $kopSrc = 'data:image/png;base64,' . $kopBase64;
        @endphp
        <div class="kop-surat">
            <img src="{{ $kopSrc }}" alt="Kop Surat">
        </div>

        {{-- ── Title ── --}}
        <div class="doc-title">JO Contract Information</div>

        {{-- ── Info Header ── --}}
        @php
            $contract = $joContract->contract;
            $customer = $contract ? $contract->customer : null;
        @endphp
        <table class="info-table">
            <tbody>
                <tr>
                    <td class="info-label">JO Number</td>
                    <td class="info-colon">:</td>
                    <td class="info-value"><strong>{{ $joContract->no_jo_cont ?? '-' }}</strong></td>
                    <td class="info-spacer"></td>
                    <td class="info-label">JO Date</td>
                    <td class="info-colon">:</td>
                    <td class="info-value">
                        {{ $joContract->tgl_jo_cont ? $joContract->tgl_jo_cont->format('d M Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Contract No</td>
                    <td class="info-colon">:</td>
                    <td class="info-value">
                        {{ $contract ? $contract->no_contract : '-' }}
                    </td>
                    <td class="info-spacer"></td>
                    <td class="info-label">TA - TD</td>
                    <td class="info-colon">:</td>
                    <td class="info-value">
                        {{ $contract && $contract->date_start ? \Carbon\Carbon::parse($contract->date_start)->format('d M Y') : '-' }} -  {{ $contract && $contract->date_end ? \Carbon\Carbon::parse($contract->date_end)->format('d M Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Contract Name</td>
                    <td class="info-colon">:</td>
                    <td class="info-value">
                        {{ $contract ? $contract->contract : '-' }}
                    </td>
                    <td class="info-spacer"></td>
                </tr>
                <tr>
                    <td class="info-label">Customer</td>
                    <td class="info-colon">:</td>
                    <td class="info-value">
                        {{ $customer ? $customer->customer : '-' }}
                    </td>
                    <td class="info-spacer"></td>
                    <td class="info-label">Expenditure</td>
                    <td class="info-colon">:</td>
                    <td class="info-value">
                        {{ $contract && $contract->expenditure ? number_format($contract->expenditure, 2, ',', '.') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Address</td>
                    <td class="info-colon">:</td>
                    <td class="info-value">
                        {{ $customer ? $customer->address ?? '-' : '-' }}
                    </td>
                    <td class="info-spacer"></td>
                    <td class="info-label">Area</td>
                    <td class="info-colon">:</td>
                    <td class="info-value">
                        {{ $joContract->area ? $joContract->area->area : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="info-label">NPWP</td>
                    <td class="info-colon">:</td>
                    <td class="info-value">
                        {{ $customer ? $customer->npwp ?? '-' : '-' }}
                    </td>
                    <td class="info-spacer"></td>
                    <td class="info-label">Title</td>
                    <td class="info-colon">:</td>
                    <td class="info-value">{{ $joContract->title ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Note</td>
                    <td class="info-colon">:</td>
                    <td class="info-value" colspan="5">{{ $joContract->note ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ── Items Table ── --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-no">NO</th>
                    <th class="col-desc">DESCRIPTION</th>
                    <th class="col-idr">Income (IDR)</th>
                    <th class="col-usd">Income (USD)</th>
                    <th class="col-sell">Selling Price (IDR)</th>
                    <th class="col-sell">HPP (Ops Costs)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedItems = $joContract->items->groupBy(fn($i) => $i->invoice->invoice_ctg);
                    $globalIndex  = 1;
                    $totalIDR     = 0;
                    $totalUSD     = 0;
                    $totalHPP     = 0;
                    $totalSell    = 0;
                @endphp

                @foreach ($groupedItems as $category => $items)
                    <tr class="row-category">
                        <td></td>
                        <td colspan="5"><strong>{{ strtoupper($category) }}</strong></td>
                    </tr>

                    @foreach ($items as $item)
                        @php
                            $totalIDR  += $item->pendapatan_idr;
                            $totalUSD  += $item->pendapatan_usd;
                            $totalHPP  += $item->hpp_ops;
                            $totalSell += $item->hargajual_idr;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $globalIndex++ }}</td>
                            <td class="text-left">
                                {{ $item->invoice->invoice_typ }}
                                @if (!empty($item->note))
                                    <div class="item-note">{!! nl2br(e($item->note)) !!}</div>
                                @endif
                            </td>
                            <td class="text-right">
                                {{ number_format($item->pendapatan_idr, 2, ',', '.') }}
                            </td>
                            <td class="text-right">
                                {{ number_format($item->pendapatan_usd, 2, ',', '.') }}
                            </td>
                            <td class="text-right">
                                {{ number_format($item->hargajual_idr, 2, ',', '.') }}
                            </td>
                            <td class="text-right">
                                {{ number_format($item->hpp_ops, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td class="text-left"><strong>GRAND TOTAL</strong></td>
                    <td class="text-right">{{ number_format($totalIDR,  2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($totalUSD,  2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($totalSell, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalHPP,  2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

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
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
            </tr>
        </table>

    </div>
</body>

</html>