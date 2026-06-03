<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contract List</title>
    <style>
        @page { size: A4 portrait; margin: 15mm 12mm; }
        @media print { .no-print { display: none; } }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        h2 {
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 2px 0;
        }
        p.subtitle {
            font-size: 8.5px;
            color: #555;
            margin: 0 0 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            background: #fff;
            color: #000;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #000;
            text-align: left;
        }
        tbody td {
            font-size: 9px;
            padding: 4px 6px;
            border: 1px solid #000000;
            vertical-align: top;
        }
        tbody tr:nth-child(even) td { background: #ffffff; }
        tbody tr:nth-child(odd)  td { background: #fff; }
        td.center { text-align: center; }
        td.right  { text-align: right; }
        .footer {
            margin-top: 10px;
            font-size: 8px;
            color: #555;
            text-align: right;
        }
        .print-btn {
            position: fixed;
            top: 12px;
            right: 12px;
            padding: 7px 16px;
            background: #111;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 11px;
            border-radius: 2px;
        }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

    <button class="print-btn no-print" onclick="window.print()">Print / Save PDF</button>

    <h2>Contract List</h2>
    <p class="subtitle">
        Printed: {{ now()->format('d/m/Y H:i') }} &nbsp;&mdash;&nbsp; Total: {{ $contracts->count() }} records
    </p>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="13%">No. Contract</th>
                <th width="22%">Contract Name</th>
                <th width="17%">Customer</th>
                <th width="12%">Expenditure</th>
                <th width="10%">Date Start</th>
                <th width="10%">Date End</th>
                <th width="12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contracts as $i => $contract)
            @php $status = $contract->date_end >= \Carbon\Carbon::today() ? 'Active' : 'Expired'; @endphp
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $contract->no_contract ?? '-' }}</td>
                <td>{{ $contract->contract    ?? '-' }}</td>
                <td>{{ $contract->customer->customer ?? '-' }}</td>
                <td class="right">{{ number_format($contract->expenditure ?? 0, 2, ',', '.') }}</td>
                <td class="center">{{ $contract->date_start ? $contract->date_start->format('d/m/Y') : '-' }}</td>
                <td class="center">{{ $contract->date_end   ? $contract->date_end->format('d/m/Y')   : '-' }}</td>
                <td class="center">{{ $status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ $contracts->count() }} contract(s) &mdash; {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>