{{-- resources/views/data/mutasi-pembayaran/voucher-pdf.blade.php --}}
{{-- DomPDF — table-based layout only, no flexbox/grid --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Cash Out Receipt - {{ $voucher->nomor_voucher ?? '-' }}</title>
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
            padding: 8mm 10mm 8mm 10mm;
        }

        /* ── KOP ── */
        .kop {
            width: 100%;
            border-collapse: collapse;
        }

        .kop>tbody>tr>td {
            border: 1px solid #000;
            vertical-align: middle;
            padding: 6px 10px;
        }

        .kop-logo {
            width: 22%;
            text-align: center;
        }

        .kop-logo img {
            max-height: 50px;
            max-width: 90px;
        }

        .kop-title {
            text-align: center;
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 8px 10px;
        }

        .kop-docbox {
            width: 32%;
            vertical-align: top;
            padding: 0 !important;
        }

        .kop-docbox table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .kop-docbox table td {
            padding: 3px 6px;
            border-bottom: 1px solid #000;
            vertical-align: middle;
        }

        .kop-docbox table tr:last-child td {
            border-bottom: none;
        }

        .kop-docbox table td:first-child {
            font-weight: bold;
            white-space: nowrap;
            border-right: 1px solid #000;
            width: 52%;
        }

        /* ── THICK DIVIDER ── */
        .divider {
            border-top: 2.5px solid #000;
            margin: 5px 0 5px 0;
        }

        /* ── INFO FIELDS (bordered box) ── */
        .info-tbl {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            border: 1px solid #000;
        }

        .info-tbl td {
            padding: 3px 10px;
            font-size: 8.5pt;
            vertical-align: top;
            border: none;
        }

        .info-lbl {
            font-weight: bold;
            white-space: nowrap;
            width: 1%;
            padding-right: 4px;
        }

        .info-sep {
            width: 20px;
            text-align: center;
        }

        .info-val {}

        /* ── MAIN TABLE ── */
        .main {
            width: 100%;
            border-collapse: collapse;
        }

        .main th {
            border: 1px solid #000;
            padding: 4px 8px;
            font-size: 8.5pt;
            text-align: center;
            background: #d9d9d9;
            font-weight: bold;
        }

        .main td {
            border: 1px solid #000;
            padding: 3px 8px;
            font-size: 8.5pt;
            vertical-align: middle;
        }

        .paid-to-cell {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            font-size: 8pt;
            background: #f2f2f2;
            width: 20%;
        }

        .cat-row td {
            font-weight: bold;
        }

        .desc-cell {
            padding-left: 20px !important;
            font-weight: normal;
        }

        .amt {
            text-align: right;
            white-space: nowrap;
            width: 140px;
        }

        .total-row td {
            font-weight: bold;
            background: #ffff00;
        }

        .words-row td {
            font-size: 8pt;
            font-weight: bold;
            padding: 5px 8px;
        }

        /* ── SIGNATURES ── */
        .sign-tbl {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            table-layout: fixed;
        }

        .sign-tbl td {
            border: 1px solid #000;
            text-align: center;
            font-size: 8pt;
            padding: 5px 6px;
            vertical-align: top;
            width: 33.33%;
        }

        .sign-space {
            height: 60px;
        }
    </style>
</head>

<body>
    <div class="page">

        @php
            /* ── LOGO ── */
            $logoPath = public_path('images/logo.png');
            $logoSrc = file_exists($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : '';

            /* ── VOUCHER INFO ── */
            $nomorVoucher = $voucher->nomor_voucher ?? '-';

            $tglKeluar = $voucher->tgl_keluar ? \Carbon\Carbon::parse($voucher->tgl_keluar)->format('j-M-y') : '-';

            /* COA */
            $voucherCoa = $voucher->coa ?? ($mutasi->coa ?? null);
            $bankAccount = $voucherCoa
                ? trim(
                    ($voucherCoa->no_account ?? '') .
                        '  ' .
                        ($voucherCoa->account_name ?? ($voucherCoa->nama_account ?? '')),
                )
                : '-';

            /* ── PRE-PROCESS pdfRows → $groups ── */
            $groups = [];
            $totalAmt = 0;

            foreach ($voucher->pdfRows ?? [] as $pdfRow) {
                $byCategory = collect($pdfRow['items'] ?? [])->groupBy('category');
                $rowspan = 0;
                $cats = [];

                foreach ($byCategory as $catName => $catItems) {
                    $rowspan += 1 + count($catItems);
                    $cats[] = ['name' => $catName, 'items' => $catItems->toArray()];
                    $totalAmt += $catItems->sum('amount');
                }

                if ($rowspan > 0) {
                    $groups[] = [
                        'paid_to' => $pdfRow['paid_to'] ?? '-',
                        'rowspan' => $rowspan,
                        'cats' => $cats,
                    ];
                }
            }

            /* ── IN WORDS ── */
            if (!function_exists('voucherWords')) {
                function voucherWords(float $n): string
                {
                    $n = (int) round(abs($n));
                    if ($n === 0) {
                        return 'ZERO RUPIAH';
                    }
                    $ones = [
                        '',
                        'ONE',
                        'TWO',
                        'THREE',
                        'FOUR',
                        'FIVE',
                        'SIX',
                        'SEVEN',
                        'EIGHT',
                        'NINE',
                        'TEN',
                        'ELEVEN',
                        'TWELVE',
                        'THIRTEEN',
                        'FOURTEEN',
                        'FIFTEEN',
                        'SIXTEEN',
                        'SEVENTEEN',
                        'EIGHTEEN',
                        'NINETEEN',
                    ];
                    $tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
                    function _vw(int $n, $o, $t): string
                    {
                        if ($n < 20) {
                            return $o[$n];
                        }
                        if ($n < 100) {
                            return $t[(int) ($n / 10)] . ($n % 10 ? ' ' . $o[$n % 10] : '');
                        }
                        return $o[(int) ($n / 100)] . ' HUNDRED' . ($n % 100 ? ' ' . _vw($n % 100, $o, $t) : '');
                    }
                    $parts = [];
                    $b = (int) ($n / 1_000_000_000);
                    $n %= 1_000_000_000;
                    $m = (int) ($n / 1_000_000);
                    $n %= 1_000_000;
                    $k = (int) ($n / 1_000);
                    $n %= 1_000;
                    if ($b) {
                        $parts[] = _vw($b, $ones, $tens) . ' BILLION';
                    }
                    if ($m) {
                        $parts[] = _vw($m, $ones, $tens) . ' MILLION';
                    }
                    if ($k) {
                        $parts[] = _vw($k, $ones, $tens) . ' THOUSAND';
                    }
                    if ($n) {
                        $parts[] = _vw($n, $ones, $tens);
                    }
                    return implode(' ', $parts) . ' RUPIAH';
                }
            }
            $wordsLine = '## ' . voucherWords((float) $totalAmt) . ' ##';

            if (!function_exists('fmtIdr')) {
                function fmtIdr($v): string
                {
                    return number_format((float) $v, 2, '.', ',');
                }
            }
        @endphp

        {{-- ════════════════ KOP SURAT ════════════════ --}}
        <table class="kop">
            <tr>
                {{-- Logo --}}
                <td class="kop-logo">
                    @if ($logoSrc)
                        <img src="{{ $logoSrc }}" alt="Logo" />
                    @else
                        <span style="font-size:7pt;color:#555;">[LOGO]</span>
                    @endif
                </td>

                {{-- Title --}}
                <td class="kop-title">CASH OUT RECEIPT</td>

                {{-- Doc Box --}}
                <td class="kop-docbox">
                    <table>
                        <tr>
                            <td>Nomor Dokumen</td>
                            <td>OBS-FOR-01-0022</td>
                        </tr>
                        <tr>
                            <td>Rev (Issued Date)</td>
                            <td>01 (11 Nov 2024)</td>
                        </tr>
                        <tr>
                            <td>Page</td>
                            <td>1</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        {{-- ════════════════ INFO FIELDS (plain) ════════════════ --}}
        <table class="info-tbl">
            <tr>
                <td class="info-lbl">Cash Out No.</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $nomorVoucher }}</strong></td>
            </tr>
            <tr>
                <td class="info-lbl">Cash Out Date</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $tglKeluar }}</strong></td>
            </tr>
            <tr>
                <td class="info-lbl">Bank / Cash</td>
                <td class="info-sep">:</td>
                <td class="info-val"><strong>{{ $bankAccount }}</strong></td>
            </tr>
        </table>

        {{-- ════════════════ MAIN TABLE ════════════════ --}}
        <table class="main">
            <thead>
                <tr>
                    <th style="width:20%;">PAID TO</th>
                    <th style="text-align:left;">DESCRIPTION</th>
                    <th class="amt">AMOUNT (IDR)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $grp)
                    @foreach ($grp['cats'] as $catIdx => $cat)
                        <tr class="cat-row">
                            @if ($catIdx === 0)
                                <td class="paid-to-cell" rowspan="{{ $grp['rowspan'] }}">
                                    {{ $grp['paid_to'] }}
                                </td>
                            @endif
                            <td style="font-weight:bold;">{{ strtoupper($cat['name']) }} :</td>
                            <td class="amt"></td>
                        </tr>
                        @foreach ($cat['items'] as $it)
                            <tr>
                                <td class="desc-cell">{{ $it['label'] ?? '-' }}</td>
                                <td class="amt">{{ fmtIdr($it['amount'] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center;font-style:italic;padding:10px;">
                            No items found
                        </td>
                    </tr>
                @endforelse

                {{-- TOTAL --}}
                <tr class="total-row">
                    <td colspan="2" style="text-align:right;">TOTAL</td>
                    <td class="amt">{{ fmtIdr($totalAmt) }}</td>
                </tr>

                {{-- IN WORDS --}}
                <tr class="words-row">
                    <td style="font-weight:bold; white-space:nowrap; width:1%;">IN WORDS :</td>
                    <td colspan="2">{{ $wordsLine }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ════════════════ SIGNATURES (3 kolom) ════════════════ --}}
        <table class="sign-tbl">
            {{-- Header label --}}
            <tr>
                <td style="font-weight:bold;">Cashier</td>
                <td style="font-weight:bold;">Head of Finance</td>
                <td style="font-weight:bold;">Accounting</td>
            </tr>
            {{-- Space TTD --}}
            <tr>
                <td class="sign-space">&nbsp;</td>
                <td class="sign-space">&nbsp;</td>
                <td class="sign-space">&nbsp;</td>
            </tr>
            {{-- Nama (kosong) --}}
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            {{-- Tanggal --}}
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>

    </div>{{-- /page --}}
</body>

</html>
