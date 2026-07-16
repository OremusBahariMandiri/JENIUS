<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kasbon Tramper List</title>
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
    <h2>Kasbon Tramper List</h2>
    <p class="subtitle">
        Printed: {{ now()->format('d/m/Y H:i') }}
        &nbsp;&mdash;&nbsp;
        Total: {{ $kasbonTrampers->count() }} records
    </p>

    @if (!empty($filters) && array_filter($filters))
        <div class="filter-info">
            <strong>Active Filters:</strong>
            @if (!empty($filters['jo_label']))   &nbsp; JO Tramper: <em>{{ $filters['jo_label'] }}</em> @endif
            @if (!empty($filters['dep_label']))  &nbsp; Departemen: <em>{{ $filters['dep_label'] }}</em> @endif
            @if (!empty($filters['cabang_label'])) &nbsp; Branch: <em>{{ $filters['cabang_label'] }}</em> @endif
            @if (!empty($filters['tgl_kasbon_from'])) &nbsp; From: <em>{{ $filters['tgl_kasbon_from'] }}</em> @endif
            @if (!empty($filters['tgl_kasbon_to']))   &nbsp; To: <em>{{ $filters['tgl_kasbon_to'] }}</em> @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="3%"  class="center">No</th>
                <th width="10%">CA No</th>
                <th width="7%"  class="center">CA Date</th>
                <th width="10%">JO Number</th>
                <th width="13%">Departemen</th>
                <th width="11%">Branch</th>
                <th width="12%">Release To</th>
                <th width="7%"  class="center">Release Date</th>
                <th width="4%"  class="center">Items</th>
                <th width="11%" class="right">Total HPP (IDR)</th>
                <th width="12%" class="right">Total CA (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kasbonTrampers as $i => $kasbon)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $kasbon->id_kasbon_tram ?? '-' }}</td>
                    <td class="center">{{ $kasbon->tgl_kasbon ? $kasbon->tgl_kasbon->format('d/m/y') : '-' }}</td>
                    <td>{{ $kasbon->joTramper ? $kasbon->joTramper->no_jo_tram : '-' }}</td>
                    <td>{{ $kasbon->departemen ? $kasbon->departemen->nama_dep : '-' }}</td>
                    <td>{{ $kasbon->cabang ? $kasbon->cabang->nama_branch : '-' }}</td>
                    <td>{{ $kasbon->release ? $kasbon->release->nama_release : '-' }}</td>
                    <td class="center">{{ $kasbon->tgl_release ? $kasbon->tgl_release->format('d/m/y') : '-' }}</td>
                    <td class="center">{{ $kasbon->items ? $kasbon->items->count() : 0 }}</td>
                    <td class="right">{{ number_format($kasbon->items ? $kasbon->items->sum('nilai_hpp_tram_item') : 0, 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($kasbon->items ? $kasbon->items->sum('nilai_kasbon') : 0, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="11" class="center">No data available</td></tr>
            @endforelse
        </tbody>
        @if($kasbonTrampers->count() > 0)
        <tfoot>
            <tr>
                <td colspan="8" style="text-align:right;">Total</td>
                <td class="center">{{ $kasbonTrampers->sum(fn($k) => $k->items ? $k->items->count() : 0) }}</td>
                <td class="right">{{ number_format($kasbonTrampers->sum(fn($k) => $k->items ? $k->items->sum('nilai_hpp_tram_item') : 0), 2, ',', '.') }}</td>
                <td class="right">{{ number_format($kasbonTrampers->sum(fn($k) => $k->items ? $k->items->sum('nilai_kasbon') : 0), 2, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">{{ $kasbonTrampers->count() }} record(s) &mdash; {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>