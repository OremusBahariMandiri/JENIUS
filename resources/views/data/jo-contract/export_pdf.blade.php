<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>JO Contract List</title>
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
            font-size: 8px;
            color: #555;
            margin: 0 0 8px 0;
        }
        .filter-info {
            font-size: 8px;
            color: #333;
            margin-bottom: 8px;
            padding: 4px 6px;
            border: 1px solid #ccc;
            background: #f9f9f9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            background: #ffffff;
            color: #000;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 5px;
            border: 1px solid #000;
            text-align: left;
        }
        thead th.center { text-align: center; }
        tbody td {
            font-size: 8.5px;
            padding: 3px 5px;
            border: 1px solid #000;
            vertical-align: top;
        }
        tbody tr:nth-child(even) td { background: #f4f4f4; }
        tbody tr:nth-child(odd)  td { background: #fff; }
        td.center { text-align: center; }
        td.right  { text-align: right; }
        tfoot td {
            font-size: 8.5px;
            font-weight: bold;
            padding: 4px 5px;
            border: 1px solid #000;
            background: #ffffff;
        }
        .footer {
            margin-top: 8px;
            font-size: 7.5px;
            color: #555;
            text-align: right;
        }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

    <h2>JO Contract List</h2>
    <p class="subtitle">
        Printed: {{ now()->format('d/m/Y H:i') }}
        &nbsp;&mdash;&nbsp;
        Total: {{ $joContracts->count() }} records
    </p>

    {{-- Active filters info --}}
    @if (!empty($filters) && array_filter($filters))
        <div class="filter-info">
            <strong>Active Filters:</strong>
            @if (!empty($filters['no_jo']))
                &nbsp; JO Number: <em>{{ $filters['no_jo'] }}</em>
            @endif
            @if (!empty($filters['no_contract']))
                &nbsp; Contract No: <em>{{ $filters['no_contract'] }}</em>
            @endif
            @if (!empty($filters['contract_name']))
                &nbsp; Contract Name: <em>{{ $filters['contract_name'] }}</em>
            @endif
            @if (!empty($filters['customer_name']))
                &nbsp; Customer: <em>{{ $filters['customer_name'] }}</em>
            @endif
            @if (!empty($filters['area_name']))
                &nbsp; Area: <em>{{ $filters['area_name'] }}</em>
            @endif
            @if (!empty($filters['title']))
                &nbsp; Title: <em>{{ $filters['title'] }}</em>
            @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="3%"  class="center">No</th>
                <th width="6%"  class="center">JO Date</th>
                <th width="8%">JO Number</th>
                <th width="8%">Contract No</th>
                <th width="14%">Contract Name</th>
                <th width="12%">Customer</th>
                <th width="9%"  class="center">Period</th>
                <th width="8%">Area</th>
                <th width="14%">Title</th>
                <th width="4%"  class="center">Items</th>
                <th width="10%" class="center">Total (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($joContracts as $i => $jo)
                @php
                    $contract  = $jo->contract;
                    $customer  = $contract && $contract->customer ? $contract->customer->customer : '-';
                    $dateStart = $contract && $contract->date_start
                        ? \Carbon\Carbon::parse($contract->date_start)->format('d/m/y') : '-';
                    $dateEnd   = $contract && $contract->date_end
                        ? \Carbon\Carbon::parse($contract->date_end)->format('d/m/y') : '-';
                    $totalSell = $jo->items ? $jo->items->sum('hargajual_idr') : 0;
                @endphp
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td class="center">{{ $jo->tgl_jo_cont ? $jo->tgl_jo_cont->format('d/m/y') : '-' }}</td>
                    <td>{{ $jo->no_jo_cont ?? '-' }}</td>
                    <td>{{ $contract ? $contract->no_contract : '-' }}</td>
                    <td>{{ $contract ? $contract->contract : '-' }}</td>
                    <td>{{ $customer }}</td>
                    <td class="center">{{ $dateStart }} – {{ $dateEnd }}</td>
                    <td>{{ $jo->area ? $jo->area->area : '-' }}</td>
                    <td>{{ $jo->title ?? '-' }}</td>
                    <td class="center">{{ $jo->items ? $jo->items->count() : 0 }}</td>
                    <td class="right">{{ number_format($totalSell, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center">No data available</td>
                </tr>
            @endforelse
        </tbody>
        @if($joContracts->count() > 0)
        <tfoot>
            <tr>
                <td colspan="9" class="right">Total</td>
                <td class="center">{{ $joContracts->sum(fn($jo) => $jo->items ? $jo->items->count() : 0) }}</td>
                <td class="right">
                    {{ number_format($joContracts->sum(fn($jo) => $jo->items ? $jo->items->sum('hargajual_idr') : 0), 2, ',', '.') }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        {{ $joContracts->count() }} record(s) &mdash; {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>