<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice - {{ $invoice->no_invoice ?? '-' }}</title>
    <style>
        @php echo '@page { size: A4 portrait; margin: 0; }'; @endphp

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            background: #fff;
            padding: 14mm 14mm 14mm 14mm;
        }

        /* ── Kop Surat ── */
        .kop-surat { width: 100%; margin-bottom: 20px; }
        .kop-surat img { width: 100%; height: auto; display: block; }

        /* ── Title ── */
        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 5px;
            margin: 10px 0 10px 0;
        }

        /* ── Info Tables ── */
        .info-top {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 0;
        }
        .info-top td {
            padding: 0;
            border: none;
            vertical-align: top;
        }
        .info-inner {
            width: 100%;
            border-collapse: collapse;
        }
        .info-inner td {
            padding: 2.5px 7px;
            font-size: 9pt;
            border: none;
            vertical-align: top;
        }
        .il { width: 95px; white-space: nowrap; }
        .is { width: 10px; text-align: center; }
        .iv { }

        /* second info row (vendor, contract, BA, PO, SA) */
        .info-row2 {
            width: 100%;
            border-collapse: collapse;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            margin-bottom: 8px;
        }
        .info-row2 td {
            padding: 2.5px 7px;
            font-size: 9pt;
            vertical-align: middle;
            border: none;
        }
        .ir-lbl { width: 95px; white-space: nowrap; }
        .ir-sep { width: 10px; text-align: center; }
        .ir-val { }

        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 0;
        }
        .items-table th {
            background-color: #d9d9d9;
            border: 1px solid #000;
            padding: 5px 7px;
            font-weight: bold;
            vertical-align: middle;
        }
        .th-desc { text-align: left; width: 65%; }
        .th-idr  { text-align: right; width: 35%; }

        .items-table td {
            border: 1px solid #000;
            padding: 4px 7px;
            vertical-align: middle;
        }
        .td-desc { text-align: left; }
        .td-idr  { white-space: nowrap; }

        /* category separator row */
        .row-cat td {
            font-weight: bold;
            background: #fff;
            border: 1px solid #000;
            padding: 4px 7px;
        }

        /* subtotal / total rows */
        .row-sub td, .row-tot td {
            border: 1px solid #000;
            padding: 4px 7px;
            font-weight: bold;
            background: #e0e0e0;
        }

        /* ── Terbilang ── */
        .terbilang-wrap {
            width: 100%;
            border-collapse: collapse;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
        }
        .terbilang-wrap td { padding: 6px 10px; border: none; }
        .terb-label {
            font-weight: bold;
            font-size: 8.5pt;
            white-space: nowrap;
            padding: 5px 8px;
            vertical-align: middle;
            width: 1%;
            border-right: 1px solid #000;
        }
        .terb-text {
            font-style: italic;
            font-size: 8.5pt;
            padding: 5px 8px;
            vertical-align: middle;
        }

        /* ── Bottom Section ── */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .bottom-table td { vertical-align: top; padding: 0; border: none; }

        /* remit */
        .remit-block { font-size: 8.5pt; line-height: 1.6; }
        .remit-block p { margin: 0; }

        /* tax summary box (right) */
        .tax-box {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        .tax-box td {
            border: 1px solid #000;
            padding: 3px 8px;
            vertical-align: middle;
        }
        .tax-lbl { text-align: left; width: 55%; }
        .tax-val { text-align: right; width: 45%; font-weight: bold; white-space: nowrap; }
        .tax-total-row td { font-weight: bold; background: #e0e0e0; }

        /* signature */
        .sign-block { text-align: center; font-size: 9pt; margin-top: 10px; }
        .sign-gap { height: 90px; }
        .sign-name { font-weight: bold; }
        .sign-line { border-top: 1px solid #000; padding-top: 2px; display: inline-block; min-width: 130px; }
        .sign-role { font-size: 8.5pt; }
    </style>
</head>
<body>

    {{-- ── Kop Surat ── --}}
    @php
        $kopPath   = public_path('images/kop-surat-orindo.png');
        $kopBase64 = base64_encode(file_get_contents($kopPath));
        $kopSrc    = 'data:image/png;base64,' . $kopBase64;
    @endphp
    <div class="kop-surat">
        <img src="{{ $kopSrc }}" alt="Kop Surat">
    </div>

    {{-- ── Title ── --}}
    <div class="doc-title">I N V O I C E</div>

    {{-- ── Info Top (2 columns) ── --}}
    @php
        $jo       = $invoice->joTramper;
        $customer = $invoice->customer ?? $jo?->customer ?? null;
        $custName = $invoice->to_customer ?? $customer?->customer ?? '-';
        $custAddr = $invoice->address ?? $customer?->address ?? '';
        $custNpwp = $customer?->npwp ?? '';

        $tglInvoice = $invoice->tgl_invoice ? $invoice->tgl_invoice->format('d F Y') : '-';
        $tglDue     = $invoice->due_date    ? $invoice->due_date->format('d F Y')    : '-';
    @endphp
    <table class="info-top">
        <tr>
            {{-- LEFT --}}
            <td style="width:50%; border-right:1px solid #000;">
                <table class="info-inner">
                    <tr>
                        <td class="il">Customer</td>
                        <td class="is">:</td>
                        <td class="iv"><strong>{{ $custName }}</strong></td>
                    </tr>
                    <tr>
                        <td class="il">Address</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $custAddr }}</td>
                    </tr>
                    <tr>
                        <td class="il">NPWP</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $custNpwp }}</td>
                    </tr>
                </table>
            </td>
            {{-- RIGHT --}}
            <td style="width:50%;">
                <table class="info-inner">
                    <tr>
                        <td class="il">Invoice No</td>
                        <td class="is">:</td>
                        <td class="iv"><strong>{{ $invoice->no_invoice ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="il">VAT Number</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $invoice->vat_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="il">Invoice Date</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $tglInvoice }}</td>
                    </tr>
                    <tr>
                        <td class="il">Invoice Due Date</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $tglDue }}</td>
                    </tr>
                    <tr>
                        <td class="il">Cond of Payment</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $invoice->cond_of_payment ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="il">Currency</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $invoice->currency ?? 'IDR' }}</td>
                    </tr>
                    <tr>
                        <td class="il">Vessel</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $invoice->kapal ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="il">Period</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $invoice->period ?? '' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ── Info Row 2 (Vendor, Contract, BA | PO, SA) ── --}}
    <table class="info-row2">
        <tr>
            <td style="width:50%; border-right:1px solid #000; padding:0;">
                <table class="info-inner">
                    <tr>
                        <td class="il">Vendor Number</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $invoice->vendor_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="il">Contract Number</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $invoice->contract_no ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="il">BA Number</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $invoice->ba_number ?? '' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:50%; padding:0;">
                <table class="info-inner">
                    <tr>
                        <td class="il">PO Number</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $invoice->po_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="il">SA / GR Number</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $invoice->sa_gr_number ?? '' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ── Items Table ── --}}
    @php
        $items        = $invoice->items->where('is_reimbursement', false);
        $regular      = $items->where('item_type', 'item')->where('is_reimbursement', false);
        $reimb        = $items->where('item_type', 'item')->where('is_reimbursement', true);
        $tax          = $items->where('item_type', 'tax');

        $dpp          = (float) $regular->sum('nominal_invoice');
        $reimbTotal   = (float) $reimb->sum('nominal_invoice');
        $ppnTotal     = (float) $tax->sum('nominal_invoice');
        $subTotal     = $dpp + $reimbTotal;
        $totalTagihan = $subTotal + $ppnTotal;
    @endphp
    <table class="items-table">
        <thead>
            <tr>
                <th class="th-desc">DESCRIPTION</th>
                <th class="th-idr" style="text-align:right;">AMOUNT IDR</th>
            </tr>
        </thead>
        <tbody>
            {{-- All items in table order, no grouping --}}
            @foreach($items as $item)
            @php
                if ($item->item_type === 'tax') {
                    $pct  = $item->joTramperPajak?->persentase ?? null;
                    $desc = ($item->deskripsi ?? $item->joTramperPajak?->nama_pajak ?? 'Tax') . ($pct ? ' ' . $pct . '%' : '');
                } else {
                    $desc = $item->joTramperItem?->invoice?->invoice_typ ?? $item->deskripsi ?? '-';
                }
                $nomIdr = (float)$item->nominal_invoice;
            @endphp
            <tr>
                <td class="td-desc">{{ $desc }}</td>
                <td class="td-idr">IDR&nbsp;&nbsp;<span style="float:right;">{{ number_format($nomIdr, 2, ',', '.') }}</span></td>
            </tr>
            @endforeach
        </tbody>
        {{-- SUBTOTAL --}}
        <tr class="row-sub">
            <td style="text-align:left; font-weight:bold;">SUBTOTAL</td>
            <td style="white-space:nowrap;"><strong>IDR</strong>&nbsp;&nbsp;<strong style="float:right;">{{ number_format($totalTagihan, 2, ',', '.') }}</strong></td>
        </tr>
        {{-- TOTAL --}}
        <tr class="row-tot">
            <td style="text-align:left; font-weight:bold;">TOTAL</td>
            <td style="white-space:nowrap;"><strong>IDR</strong>&nbsp;&nbsp;<strong style="float:right;">{{ number_format($totalTagihan, 2, ',', '.') }}</strong></td>
        </tr>
    </table>

    {{-- ── Terbilang ── --}}
    <table class="terbilang-wrap">
        <tr>
            <td class="terb-label">TERBILANG :</td>
            <td class="terb-text">{{ strtoupper($terbilang) }}</td>
        </tr>
    </table>

    {{-- ── Bottom: Remittance (left) + Tax Summary (right) ── --}}
    @php
        $tglStr = $invoice->tgl_invoice
            ? $invoice->tgl_invoice->format('d F Y')
            : now()->format('d F Y');
        $signerName = $companyProfile?->nama_direktur ?: 'Niko';
        $signerRole = $companyProfile?->jabatan ?: 'Direktur Utama';
    @endphp
    <table class="bottom-table">
        <tr>
            <td style="width:52%; vertical-align:top; padding-right:10px;">
                <div class="remit-block">
                    <p><em>Please make the payment to :</em></p>
                    <p><strong>{{ $companyProfile?->nama_perusahaan ?? 'PT. ORINDO BANGUN SAMUDERA' }}</strong></p>
                    @if($invoice->coa)
                        <p>{{ $invoice->coa->invoice_display }}</p>
                        <p>A/C : {{ $invoice->coa->coa_code }} {{ $invoice->coa->number }}</p>
                        @if($invoice->coa->swift_code)
                            <p>SWIFT : {{ $invoice->coa->swift_code }}</p>
                        @endif
                    @endif
                </div>
            </td>
            <td style="width:48%; vertical-align:top;">
                <table class="tax-box">
                    <tr>
                        <td class="tax-lbl">DPP</td>
                        <td class="tax-val">{{ number_format($dpp, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="tax-lbl">Non PPN</td>
                        <td class="tax-val">{{ number_format($reimbTotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="tax-lbl">Total</td>
                        <td class="tax-val">{{ number_format($subTotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="tax-lbl">PPN {{ $ppnPct ?? '11' }}%</td>
                        <td class="tax-val">{{ number_format($ppnTotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="tax-total-row">
                        <td class="tax-lbl">Total Tagihan</td>
                        <td class="tax-val">{{ number_format($totalTagihan, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="tax-lbl">Utip</td>
                        <td class="tax-val">0.00</td>
                    </tr>
                    <tr class="tax-total-row">
                        <td class="tax-lbl">Sisa Tagihan</td>
                        <td class="tax-val">{{ number_format($totalTagihan, 2, ',', '.') }}</td>
                    </tr>
                </table>

                <div class="sign-block">
                    <p style="margin-bottom:0;">Surabaya, {{ $tglStr }}</p>
                    <div class="sign-gap"></div>
                    <p><span class="sign-line"><strong class="sign-name">{{ $signerName }}</strong></span></p>
                    <p class="sign-role">{{ $signerRole }}</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
