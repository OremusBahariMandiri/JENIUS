<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reimbursement - {{ $invoice->no_invoice ?? '-' }}</title>
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
        .kop-surat { width: 100%; margin-bottom: 2px; }
        .kop-surat img { width: 100%; height: auto; display: block; }

        /* ── Title ── */
        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 5px;
            margin: 25px 0 10px 0;
        }

        /* ── Info Table ── */
        .info-top {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 8px;
        }
        .info-top > tbody > tr > td {
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
        .th-desc { text-align: center; width: 60%; }
        .th-idr  { text-align: center; width: 40%; }

        .items-table td {
            border: 1px solid #000;
            padding: 4px 7px;
            vertical-align: middle;
        }
        .td-desc { text-align: left; }
        .td-idr  { white-space: nowrap; }

        /* Lain-lain separator */
        .row-lainlain td {
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

        /* ── Terbilang (plain, no border) ── */
        .terbilang {
            padding: 6px 0;
            font-size: 8.5pt;
            font-style: italic;
            font-weight: bold;
            margin-bottom: 12px;
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

        /* signature */
        .sign-block { text-align: center; font-size: 9pt; }
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
    <div class="doc-title">R E I M B U R S E M E N T</div>

    {{-- ── Info ── --}}
    @php
        $jo       = $invoice->joContract;
        $contract = $jo?->contract;
        $customer = $invoice->customer ?? $contract?->customer ?? null;
        $custName = $invoice->to_customer ?? $customer?->customer ?? '-';
        $custAddr = $invoice->address ?? $customer?->address ?? '';
        $custNpwp = $customer?->npwp ?? '';

        $tglInvoice = $invoice->tgl_invoice ? $invoice->tgl_invoice->format('d F Y') : '-';
        $tglDue     = $invoice->due_date    ? $invoice->due_date->format('d F Y')    : '-';
        $tglContract = $invoice->tgl_contract ? $invoice->tgl_contract->format('d F Y') : '';
    @endphp
    <table class="info-top">
        <tbody>
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
                    <tr>
                        <td class="il">Ref. No.</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $invoice->ref_no ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="il">Date of Contract</td>
                        <td class="is">:</td>
                        <td class="iv">{{ $tglContract }}</td>
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
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    {{-- ── Items Table ── --}}
    @php
        $reimbItems = $invoice->items->where('item_type', 'item')->where('is_reimbursement', true)->values();
        $grandTotal = (float) $reimbItems->sum('nominal_invoice');
    @endphp
    <table class="items-table">
        <thead>
            <tr>
                <th class="th-desc">DESCRIPTION</th>
                <th class="th-idr">AMOUNT IDR</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reimbItems as $item)
            @php
                $desc   = $item->joContractItem?->invoice?->invoice_typ ?? $item->deskripsi ?? '-';
                $nomIdr = (float)$item->nominal_invoice;
            @endphp
            <tr>
                <td class="td-desc">{{ $desc }}</td>
                <td class="td-idr">IDR&nbsp;&nbsp;<span style="float:right;">{{ number_format($nomIdr, 2, ',', '.') }}</span></td>
            </tr>
            @empty
            <tr>
                <td class="td-desc" style="color:#888; font-style:italic;">No reimbursement items</td>
                <td class="td-idr">IDR&nbsp;&nbsp;<span style="float:right;">0,00</span></td>
            </tr>
            @endforelse
        </tbody>
        {{-- SUBTOTAL --}}
        <tr class="row-sub">
            <td style="text-align:left; font-weight:bold;">SUBTOTAL</td>
            <td style="white-space:nowrap;"><strong>IDR</strong>&nbsp;&nbsp;<strong style="float:right;">{{ number_format($grandTotal, 2, ',', '.') }}</strong></td>
        </tr>
        {{-- TOTAL --}}
        <tr class="row-tot">
            <td style="text-align:left; font-weight:bold;">TOTAL</td>
            <td style="white-space:nowrap;"><strong>IDR</strong>&nbsp;&nbsp;<strong style="float:right;">{{ number_format($grandTotal, 2, ',', '.') }}</strong></td>
        </tr>
    </table>

    {{-- ── Terbilang (plain) ── --}}
    <div class="terbilang">TERBILANG : {{ strtoupper($terbilang) }}</div>

    {{-- ── Bottom: Remittance (left) + Signature (right) ── --}}
    @php
        $tglStr = $invoice->tgl_invoice
            ? $invoice->tgl_invoice->format('d F Y')
            : now()->format('d F Y');
        $signerName = $companyProfile?->nama_direktur ?: 'Niko';
        $signerRole = $companyProfile?->jabatan ?: 'Direktur Utama';
    @endphp
    <table class="bottom-table">
        <tr>
            <td style="width:55%; vertical-align:top;">
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
            <td style="width:45%; text-align:center; vertical-align:top;">
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
