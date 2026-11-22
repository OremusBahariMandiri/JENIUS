<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SOA – {{ $customer->customer }}</title>
    <style>
        @php echo '@page { size: A4 portrait; margin: 0; }'; @endphp

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 7.5pt;
            color: #000;
            background: #fff;
            padding: 8mm 10mm 8mm 10mm;
        }

        /* ── Kop ── */
        .kop-surat { width: 100%; margin-bottom: 14px; }
        .kop-surat img { width: 100%; height: auto; display: block; }

        /* ── Title ── */
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 4px;
            margin: 6px 0 10px 0;
            text-transform: uppercase;
        }

        /* ── Customer Info ── */
        .cust-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 10px;
        }
        .cust-table td {
            padding: 3px 7px;
            font-size: 8.5pt;
            vertical-align: top;
            border: none;
        }
        .cust-lbl { width: 90px; font-weight: bold; white-space: nowrap; }
        .cust-sep { width: 10px; text-align: center; }
        .cust-val { }
        .cust-row { border-bottom: 1px solid #ddd; }
        .cust-row:last-child { border-bottom: none; }

        /* ── Invoice Table ── */
        .inv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 8px;
        }
        .inv-table th {
            background: #d9d9d9;
            border: 1px solid #000;
            padding: 3px 4px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        .inv-table td {
            border: 1px solid #000;
            padding: 2px 4px;
            vertical-align: top;
        }
        .inv-table .num { text-align: right; white-space: nowrap; }
        .inv-table .ctr { text-align: center; }
        .inv-table .total-row td {
            background: #d9d9d9;
            font-weight: bold;
        }
        .inv-table .sub-row td {
            background: #fff;
            font-style: italic;
        }

        /* ── Footer note ── */
        .footer-note {
            font-size: 7pt;
            color: #555;
            margin-top: 6px;
            text-align: right;
        }

        /* print date */
        .print-date {
            font-size: 7pt;
            color: #555;
            margin-bottom: 4px;
            text-align: right;
        }
    </style>
</head>
<body>

    {{-- Kop Surat --}}
    @php
        $kopPath   = public_path('images/kop-surat-orindo.png');
        $kopBase64 = base64_encode(file_get_contents($kopPath));
        $kopSrc    = 'data:image/png;base64,' . $kopBase64;
    @endphp
    <div class="kop-surat">
        <img src="{{ $kopSrc }}" alt="Kop Surat">
    </div>

    {{-- Title --}}
    <div class="doc-title">Statement of Account</div>

    {{-- Customer Info --}}
    <table class="cust-table">
        <tr class="cust-row">
            <td class="cust-lbl">Nama Customer</td>
            <td class="cust-sep">:</td>
            <td class="cust-val"><strong>{{ $customer->customer }}</strong></td>
        </tr>
        <tr class="cust-row">
            <td class="cust-lbl">Alamat Customer</td>
            <td class="cust-sep">:</td>
            <td class="cust-val">{{ $customer->address ?: '-' }}</td>
        </tr>
        <tr>
            <td class="cust-lbl">Kontak Customer</td>
            <td class="cust-sep">:</td>
            <td class="cust-val">
                @if($customer->phone && $customer->email)
                    {{ $customer->phone }} &nbsp;|&nbsp; {{ $customer->email }}
                @elseif($customer->phone)
                    {{ $customer->phone }}
                @elseif($customer->email)
                    {{ $customer->email }}
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    {{-- Invoice Table --}}
    @php
        $grandDpp    = 0;
        $grandTax    = 0;
        $grandAmount = 0;
        $grandPay    = 0;
        $grandBack   = 0;
        $grandBal    = 0;
    @endphp

    <table class="inv-table">
        <thead>
            <tr>
                <th style="width:3%">No</th>
                <th style="width:10%">No. Invoice</th>
                <th style="width:16%">Title</th>
                <th style="width:7%">Tgl Invoice</th>
                <th style="width:7%">Due Date</th>
                <th style="width:8%">DPP</th>
                <th style="width:7%">TAX</th>
                <th style="width:9%">Amount</th>
                <th style="width:7%">Pay Date</th>
                <th style="width:8%">Payment</th>
                <th style="width:8%">Pay Back</th>
                <th style="width:10%">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $i => $row)
            @php
                $grandDpp    += $row['dpp'];
                $grandTax    += $row['ppn'];
                $grandAmount += $row['total_invoice'];
                $grandPay    += $row['payment'];
                $grandBack   += $row['payment_back'];
                $grandBal    += $row['balance'];
            @endphp
            <tr>
                <td class="ctr">{{ $i + 1 }}</td>
                <td><strong>{{ $row['no_invoice'] }}</strong></td>
                <td>{{ $row['jo_title'] ?? '-' }}</td>
                <td class="ctr">
                    {{ $row['tgl_invoice'] ? \Carbon\Carbon::parse($row['tgl_invoice'])->format('d/m/Y') : '-' }}
                </td>
                <td class="ctr">
                    {{ $row['due_date'] ? \Carbon\Carbon::parse($row['due_date'])->format('d/m/Y') : '-' }}
                </td>
                <td class="num">
                    {{ $row['dpp'] > 0 ? number_format($row['dpp'], 0, ',', '.') : '-' }}
                </td>
                <td class="num">
                    {{ $row['ppn'] > 0 ? number_format($row['ppn'], 0, ',', '.') : '-' }}
                </td>
                <td class="num"><strong>{{ number_format($row['total_invoice'], 0, ',', '.') }}</strong></td>
                <td class="ctr">
                    {{ $row['payment_date'] ? \Carbon\Carbon::parse($row['payment_date'])->format('d/m/Y') : '-' }}
                </td>
                <td class="num">
                    {{ $row['payment'] > 0 ? number_format($row['payment'], 0, ',', '.') : '-' }}
                </td>
                <td class="num">
                    {{ $row['payment_back'] > 0 ? number_format($row['payment_back'], 0, ',', '.') : '-' }}
                </td>
                <td class="num" style="font-weight:bold;">
                    {{ number_format($row['balance'], 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" style="text-align:center; padding:10px; color:#888; font-style:italic;">
                    No outstanding invoices.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align:right; font-weight:bold;">T O T A L</td>
                <td class="num">{{ $grandDpp > 0 ? number_format($grandDpp, 0, ',', '.') : '-' }}</td>
                <td class="num">{{ $grandTax > 0 ? number_format($grandTax, 0, ',', '.') : '-' }}</td>
                <td class="num">{{ number_format($grandAmount, 0, ',', '.') }}</td>
                <td></td>
                <td class="num">{{ $grandPay > 0 ? number_format($grandPay, 0, ',', '.') : '-' }}</td>
                <td class="num">{{ $grandBack > 0 ? number_format($grandBack, 0, ',', '.') : '-' }}</td>
                <td class="num" style="font-weight:bold;">
                    {{ number_format($grandBal, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-note">
        Printed: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp;
        {{ $invoices->count() }} invoice(s) outstanding
    </div>

</body>
</html>
