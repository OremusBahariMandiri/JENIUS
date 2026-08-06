<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chart of Account</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 10mm; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #000;
            margin: 0;
            padding: 0;
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
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #000;
            text-align: left;
        }
        tbody td {
            font-size: 8.5px;
            padding: 4px 6px;
            border: 1px solid #000;
            vertical-align: top;
        }
        tbody tr:nth-child(even) td { background: #fff; }
        tbody tr:nth-child(odd)  td { background: #fff; }
        td.center { text-align: center; }
        td.right  { text-align: right; }
        .footer {
            margin-top: 10px;
            font-size: 8px;
            color: #555;
            text-align: right;
        }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

    <h2>Chart of Account</h2>
    <p class="subtitle">
        Printed: {{ now()->format('d/m/Y H:i') }}
        &nbsp;&mdash;&nbsp;
        Total: {{ $accounts->count() }} records
    </p>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">No. Account</th>
                <th width="20%">Account Name</th>
                <th width="18%">Parent Account</th>
                <th width="10%">Cost Type</th>
                <th width="8%">Type</th>
                <th width="9%">Payment Type</th>
                <th width="12%">Opening Balance</th>
                <th width="12%">Current Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $i => $account)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $account->no_account ?? '-' }}</td>
                <td>{{ $account->account_name }}</td>
                <td>
                    @if($account->parentAccount)
                        {{ $account->parentAccount->kode_perkiraan }} - {{ $account->parentAccount->nama }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $account->parentAccount?->costType?->name ?? '-' }}</td>
                <td>{{ $account->type ?? '-' }}</td>
                <td>{{ $account->payment_type ?? '-' }}</td>
                <td class="right">{{ number_format($account->opening_balance, 2) }}</td>
                <td class="right">{{ number_format($account->current_balance, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ $accounts->count() }} account(s) &mdash; {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>