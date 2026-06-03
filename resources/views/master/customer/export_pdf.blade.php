<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customer List</title>
    <style>
        @page { size: A4 portrait; margin: 15mm 12mm; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
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
            border: 1px solid #ccc;
            vertical-align: top;
        }
        tbody tr:nth-child(even) td { background: #f5f5f5; }
        tbody tr:nth-child(odd)  td { background: #fff; }
        td.center { text-align: center; }
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

    <h2>Customer List</h2>
    <p class="subtitle">Printed: {{ now()->format('d/m/Y H:i') }} &nbsp;&mdash;&nbsp; Total: {{ $customers->count() }} records</p>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="22%">Customer Name</th>
                <th width="19%">Email</th>
                <th width="12%">Phone</th>
                <th width="13%">NPWP</th>
                <th width="21%">Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $i => $customer)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $customer->customer ?? '-' }}</td>
                <td>{{ $customer->email    ?? '-' }}</td>
                <td>{{ $customer->phone    ?? '-' }}</td>
                <td>{{ $customer->npwp     ?? '-' }}</td>
                <td>{{ \Str::limit($customer->address ?? '-', 55) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">{{ $customers->count() }} customer(s) &mdash; {{ now()->format('d/m/Y H:i') }}</div>

</body>
</html>