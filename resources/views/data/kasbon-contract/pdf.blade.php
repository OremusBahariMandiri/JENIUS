<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kasbon Contract - {{ $kasbonContract->id_kasbon_cont ?? '-' }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            background: #fff;
        }
        .page { padding: 8mm 8mm 8mm 8mm; }

        /* MASTER TABLE */
        .master { width: 100%; border-collapse: collapse; }

        /* Semua td default padding 5px 10px */
        .master td, .master th {
            border: 1px solid #000;
            padding: 5px 10px;
            vertical-align: middle;
            font-size: 8.5pt;
        }

        /* KOP */
        .logo-cell {
            width: 22%;
            text-align: center;
            vertical-align: middle;
            padding: 6px 10px;
        }
        .logo-cell img { max-width: 90px; max-height: 50px; }

        .company-cell {
            width: 22%;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            vertical-align: middle;
        }

        /* TOTAL */
        .total-label {
            font-weight: bold;
            text-align: left;
            background-color: #ffff00;
        }
        .total-value {
            font-weight: bold;
            background-color: #ffff00;
            white-space: nowrap;
        }

        /* TTD */
        .ttd-header { text-align: center; vertical-align: top; font-size: 8pt; }
        .ttd-space  { height: 65px; }
        .ttd-bottom { font-size: 8pt; }

        /* FOOTER NOTE */
        .footer-note { font-size: 7.5pt; font-weight: bold; }

        /* BLANK ROW */
        .blank-row td { height: 16px; padding: 0 10px; }

        /* Rp split inner table */
        .rp-table { width: 100%; border-collapse: collapse; }
        .rp-table td { border: none; padding: 0; font-size: 8.5pt; vertical-align: middle; }
        .rp-left  { text-align: left;  white-space: nowrap; width: 25px; }
        .rp-right { text-align: right; white-space: nowrap; }
        .rp-bold  { font-weight: bold; }
    </style>
</head>
<body>
<div class="page">
@php
    $logoPath   = public_path('images/logo.png');
    $logoBase64 = base64_encode(file_get_contents($logoPath));
    $logoSrc    = 'data:image/png;base64,' . $logoBase64;

    // Lookup kasbon items by jo_cont_item id
    $kasbonLookup = $kasbonContract->items->keyBy('id_jo_cont_item');

    // Sumber baris = seluruh JO Contract items
    $joItems = $kasbonContract->joContract
        ? $kasbonContract->joContract->items
        : collect();

    $groupedItems = $joItems->groupBy(
        fn($ji) => optional($ji->invoice)->invoice_ctg ?? 'Uncategorized'
    );

    $totalCA = 0;

    $flatItems = collect();
    foreach ($groupedItems as $cat => $items) {
        foreach ($items as $joItem) {
            $kasbonItem  = $kasbonLookup->get($joItem->id_jo_cont_item);
            $invoiceTyp  = optional($joItem->invoice)->invoice_typ ?? $joItem->id_jo_cont_item;
            $nilaiKasbon = $kasbonItem ? (float) $kasbonItem->nilai_kasbon : 0;
            $totalCA += $nilaiKasbon;
            $flatItems->push([
                'category' => $cat,
                'typ'      => $invoiceTyp,
                'amount'   => $nilaiKasbon,
            ]);
        }
    }

    $blankNeeded = max(0, 8 - $flatItems->count());
@endphp

<table class="master">
<tbody>

    {{-- ── KOP ROW 1 : Logo + Document Title ── --}}
    <tr>
        <td class="logo-cell" rowspan="2" style="width:22%;">
            <img src="{{ $logoSrc }}" alt="Logo">
        </td>
        <td style="width:18%;">Document Title :</td>
        <td colspan="3">KASBON</td>
    </tr>

    {{-- ── KOP ROW 2 : Document No. ── --}}
    <tr>
        <td>Document No. :</td>
        <td colspan="3"><strong>OBM-FOR-01-002</strong></td>
    </tr>

    {{-- ── KOP ROW 3 : Perusahaan + 0(1) ── --}}
    <tr>
        <td class="company-cell">PT. ORINDO BANGUN SAMUDERA</td>
        <td></td>
        <td colspan="3">0 ( 1 )</td>
    </tr>

    {{-- ── SPACER ── --}}
    <tr>
        <td colspan="5" style="height:20px; border:none; padding:0;"></td>
    </tr>

    {{-- ── AREA | TGL | NAMA | BAGIAN ── --}}
    <tr>
        <td style="width:22%;">AREA : <strong>{{ $kasbonContract->joContract && $kasbonContract->joContract->area ? $kasbonContract->joContract->area->area : '-' }}</strong></td>
        <td style="width:18%;">TGL : <strong>{{ $kasbonContract->tgl_release ? $kasbonContract->tgl_release->format('d/m/Y') : '-' }}</strong></td>
        <td colspan="2" style="width:28%;">NAMA : <strong>{{ $kasbonContract->release->nama_release ?? '-' }}</strong></td>
        <td style="width:32%;">BAGIAN : <strong>{{ $kasbonContract->departemen->nama_dep ?? '-' }}</strong></td>
    </tr>

    {{-- ── PERIHAL ── --}}
    <tr>
        <td colspan="5">Perihal: {{ $kasbonContract->note ?? '-' }}</td>
    </tr>

    {{-- ── SPACER ── --}}
    <tr>
        <td colspan="5" style="height:20px; border:none; padding:0;"></td>
    </tr>

    {{-- ── RINCIAN HEADER ── --}}
    <tr>
        <td colspan="4" style="font-weight:bold;">Rincian:</td>
        <td style="text-align:center; font-weight:bold;">Jumlah</td>
    </tr>

    {{-- ── ITEM ROWS ── --}}
    @foreach ($flatItems as $fi)
    <tr>
        <td colspan="4">{{ $fi['category'] }} - {{ $fi['typ'] }}</td>
        <td>
            <table class="rp-table">
                <tr>
                    <td class="rp-left">Rp</td>
                    <td class="rp-right">{{ number_format($fi['amount'], 0, ',', '.') }}</td>
                </tr>
            </table>
        </td>
    </tr>
    @endforeach

    {{-- ── BLANK ROWS ── --}}
    @for ($b = 0; $b < $blankNeeded; $b++)
    <tr class="blank-row">
        <td colspan="4">&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    @endfor

    {{-- ── TOTAL ── --}}
    <tr>
        <td class="total-label" colspan="4">TOTAL</td>
        <td class="total-value">
            <table class="rp-table">
                <tr>
                    <td class="rp-left rp-bold" style="background:#ffff00;">Rp</td>
                    <td class="rp-right rp-bold" style="background:#ffff00;">{{ number_format($totalCA, 0, ',', '.') }}</td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="5" style="height:20px; border:none; padding:0;"></td>
    </tr>

    {{-- ── TTD HEADER ── --}}
    <tr>
        <td class="ttd-header">TANDA TANGAN<br>PEMOHON :</td>
        <td class="ttd-header">MENYETUJUI :</td>
        <td class="ttd-header">MENYETUJUI</td>
        <td class="ttd-header">DIBAYARKAN OLEH :</td>
        <td class="ttd-header">DITERIMA OLEH:</td>
    </tr>

    {{-- ── TTD SPACE ── --}}
    <tr>
        <td class="ttd-space">&nbsp;</td>
        <td class="ttd-space">&nbsp;</td>
        <td class="ttd-space">&nbsp;</td>
        <td class="ttd-space">&nbsp;</td>
        <td class="ttd-space">&nbsp;</td>
    </tr>

    {{-- ── NAMA TTD (kosong) ── --}}
    <tr>
        <td class="ttd-bottom">&nbsp;</td>
        <td class="ttd-bottom">&nbsp;</td>
        <td class="ttd-bottom">&nbsp;</td>
        <td class="ttd-bottom">&nbsp;</td>
        <td class="ttd-bottom">&nbsp;</td>
    </tr>

    {{-- ── TGL ROW ── --}}
    <tr>
        <td class="ttd-bottom">TGL :</td>
        <td class="ttd-bottom">TGL :</td>
        <td class="ttd-bottom">TGL :</td>
        <td class="ttd-bottom">TGL :</td>
        <td class="ttd-bottom">TGL :</td>
    </tr>

    {{-- ── FOOTER NOTE ── --}}
    <tr>
        <td class="footer-note" colspan="5">
            <strong>PERHATIAN UNTUK PEMOHON</strong> : HARAP DISELESAIKAN DENGAN BAIK SELAMBATNYA 3 HARI<br>
            KERJA SETELAH UANG DITERIMA ATAU 3 HARI SETELAH KEMBALI DARI DINAS LUAR KOTA
        </td>
    </tr>

</tbody>
</table>
</div>
</body>
</html>