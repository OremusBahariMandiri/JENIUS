<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LPJ Other List</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 10mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #000; margin: 0; padding: 0; }
        h2 { font-size: 13px; font-weight: bold; margin: 0 0 2px 0; }
        p.subtitle { font-size: 8px; color: #555; margin: 0 0 8px 0; }
        .filter-info { font-size: 8px; color: #333; margin-bottom: 8px; padding: 4px 6px; border: 1px solid #ccc; background: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #ffffff; color: #000; font-size: 8px; font-weight: bold; text-transform: uppercase; padding: 4px 5px; border: 1px solid #000; text-align: left; }
        thead th.center { text-align: center; }
        thead th.right  { text-align: right; }
        tbody td { font-size: 8.5px; padding: 3px 5px; border: 1px solid #000; vertical-align: top; }
        tbody tr:nth-child(even) td { background: #f4f4f4; }
        tbody tr:nth-child(odd)  td { background: #fff; }
        td.center { text-align: center; }
        td.right  { text-align: right; }
        tfoot td { font-size: 8.5px; font-weight: bold; padding: 4px 5px; border: 1px solid #000; background: #ffffff; }
        .footer { margin-top: 8px; font-size: 7.5px; color: #555; text-align: right; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
    <h2>LPJ Other List</h2>
    <p class="subtitle">
        Printed: {{ now()->format('d/m/Y H:i') }}
        &nbsp;&mdash;&nbsp;
        Total: {{ $lpjOthers->count() }} records
    </p>

    @if (!empty($filters) && array_filter($filters))
        <div class="filter-info">
            <strong>Active Filters:</strong>
            @if (!empty($filters['no_lpj_other'])) &nbsp; No. LPJ: <em>{{ $filters['no_lpj_other'] }}</em> @endif
            @if (!empty($filters['id_jo_other']))  &nbsp; JO Other: <em>{{ $filters['id_jo_other'] }}</em> @endif
            @if (!empty($filters['date_from']))    &nbsp; From: <em>{{ $filters['date_from'] }}</em> @endif
            @if (!empty($filters['date_to']))      &nbsp; To: <em>{{ $filters['date_to'] }}</em> @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="3%"  class="center">No</th>
                <th width="12%">No. LPJ</th>
                <th width="7%"  class="center">Date</th>
                <th width="13%">JO Number</th>
                <th width="5%"  class="center">Kasbon</th>
                <th width="5%"  class="center">Items</th>
                <th width="17%" class="right">Total Kasbon (IDR)</th>
                <th width="17%" class="right">Total LPJ (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lpjOthers as $i => $lpj)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $lpj->no_lpj_other ?? '-' }}</td>
                    <td class="center">{{ $lpj->date ? $lpj->date->format('d/m/y') : '-' }}</td>
                    <td>{{ $lpj->joOther ? $lpj->joOther->no_jo_other : '-' }}</td>
                    <td class="center">{{ $lpj->kasbons ? $lpj->kasbons->count() : 0 }}</td>
                    <td class="center">{{ $lpj->items ? $lpj->items->count() : 0 }}</td>
                    <td class="right">{{ number_format($lpj->amount ?? 0, 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($lpj->items ? $lpj->items->sum('amount_lpj') : 0, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="center">No data available</td></tr>
            @endforelse
        </tbody>
        @if($lpjOthers->count() > 0)
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right;">Total</td>
                <td class="center">{{ $lpjOthers->sum(fn($l) => $l->kasbons ? $l->kasbons->count() : 0) }}</td>
                <td class="center">{{ $lpjOthers->sum(fn($l) => $l->items ? $l->items->count() : 0) }}</td>
                <td class="right">{{ number_format($lpjOthers->sum(fn($l) => $l->amount ?? 0), 2, ',', '.') }}</td>
                <td class="right">{{ number_format($lpjOthers->sum(fn($l) => $l->items ? $l->items->sum('amount_lpj') : 0), 2, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">{{ $lpjOthers->count() }} record(s) &mdash; {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>