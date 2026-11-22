<?php

namespace App\Http\Controllers\Rekap;

use App\Http\Controllers\Controller;
use App\Models\Master\Customer;
use App\Models\Data\InvoicePembayaranContract;
use App\Models\Data\InvoicePembayaranTramper;
use App\Models\Data\InvoicePembayaranOther;
use App\Models\Data\JoContract;
use App\Models\Master\Contract;
use App\Models\Data\JoTramper;
use App\Models\Data\JoOther;
use App\Models\Data\MutasiPenerimaanDetail;
use Illuminate\Http\Request;

class StatementOfAccountController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  INDEX – list all customers with job & outstanding invoice count
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::query()
            ->when($search, fn($q) => $q->where('customer', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
            ->orderBy('customer')
            ->get();

        $custIds = $customers->pluck('id_md_cust')->toArray();

        // Count jobs per customer (all 3 types)
        // JoContract has no id_md_cust directly; join via Contract (a02_md_contract)
        $joContCount   = JoContract::join('a02_md_contract', 'b01_jo_cont.id_md_cont', '=', 'a02_md_contract.id_md_cont')
            ->whereIn('a02_md_contract.id_md_cust', $custIds)
            ->selectRaw('a02_md_contract.id_md_cust, COUNT(b01_jo_cont.id_jo_cont) as cnt')
            ->groupBy('a02_md_contract.id_md_cust')
            ->pluck('cnt', 'id_md_cust');

        $joTramCount   = JoTramper::whereIn('id_md_cust', $custIds)
            ->selectRaw('id_md_cust, COUNT(*) as cnt')
            ->groupBy('id_md_cust')
            ->pluck('cnt', 'id_md_cust');

        $joOtherCount  = JoOther::whereIn('id_md_cust', $custIds)
            ->selectRaw('id_md_cust, COUNT(*) as cnt')
            ->groupBy('id_md_cust')
            ->pluck('cnt', 'id_md_cust');

        // Count outstanding (non-closed) invoices per customer
        $outContCount  = InvoicePembayaranContract::whereIn('id_md_cust', $custIds)
            ->where('status', '!=', 'closed')
            ->selectRaw('id_md_cust, COUNT(*) as cnt')
            ->groupBy('id_md_cust')
            ->pluck('cnt', 'id_md_cust');

        $outTramCount  = InvoicePembayaranTramper::whereIn('id_md_cust', $custIds)
            ->where('status', '!=', 'closed')
            ->selectRaw('id_md_cust, COUNT(*) as cnt')
            ->groupBy('id_md_cust')
            ->pluck('cnt', 'id_md_cust');

        $outOtherCount = InvoicePembayaranOther::whereIn('id_md_cust', $custIds)
            ->where('status', '!=', 'closed')
            ->selectRaw('id_md_cust, COUNT(*) as cnt')
            ->groupBy('id_md_cust')
            ->pluck('cnt', 'id_md_cust');

        // Outstanding total per customer
        $outContTotal  = InvoicePembayaranContract::with('items')
            ->whereIn('id_md_cust', $custIds)
            ->where('status', '!=', 'closed')
            ->get()
            ->groupBy('id_md_cust')
            ->map(fn($inv) => $inv->sum(fn($i) => $i->items->sum('nominal_invoice')));

        $outTramTotal  = InvoicePembayaranTramper::with('items')
            ->whereIn('id_md_cust', $custIds)
            ->where('status', '!=', 'closed')
            ->get()
            ->groupBy('id_md_cust')
            ->map(fn($inv) => $inv->sum(fn($i) => $i->items->sum('nominal_invoice')));

        $outOtherTotal = InvoicePembayaranOther::with('items')
            ->whereIn('id_md_cust', $custIds)
            ->where('status', '!=', 'closed')
            ->get()
            ->groupBy('id_md_cust')
            ->map(fn($inv) => $inv->sum(fn($i) => $i->items->sum('nominal_invoice')));

        $rows = $customers->map(function ($cust) use (
            $joContCount, $joTramCount, $joOtherCount,
            $outContCount, $outTramCount, $outOtherCount,
            $outContTotal, $outTramTotal, $outOtherTotal
        ) {
            $id = $cust->id_md_cust;
            $totalJobs        = ($joContCount[$id] ?? 0) + ($joTramCount[$id] ?? 0) + ($joOtherCount[$id] ?? 0);
            $totalOutstanding = ($outContCount[$id] ?? 0) + ($outTramCount[$id] ?? 0) + ($outOtherCount[$id] ?? 0);
            $totalAmount      = ($outContTotal[$id] ?? 0) + ($outTramTotal[$id] ?? 0) + ($outOtherTotal[$id] ?? 0);

            return [
                'id_md_cust'       => $id,
                'customer'         => $cust->customer,
                'email'            => $cust->email,
                'phone'            => $cust->phone,
                'address'          => $cust->address,
                'job_contract'     => $joContCount[$id]  ?? 0,
                'job_tramper'      => $joTramCount[$id]  ?? 0,
                'job_other'        => $joOtherCount[$id] ?? 0,
                'total_jobs'       => $totalJobs,
                'inv_outstanding'  => $totalOutstanding,
                'total_outstanding'=> (float) $totalAmount,
            ];
        });

        return view('rekap.statement-of-account.index', compact('rows', 'search'));
    }

    // ─────────────────────────────────────────────────────────────
    //  SHOW – outstanding invoices for one customer
    // ─────────────────────────────────────────────────────────────
    public function show(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Collect non-closed invoices from all 3 types
        $contractInvoices = InvoicePembayaranContract::with('items')
            ->where('id_md_cust', $id)
            ->where('status', '!=', 'closed')
            ->orderByDesc('tgl_invoice')
            ->get();

        $tramperInvoices  = InvoicePembayaranTramper::with('items')
            ->where('id_md_cust', $id)
            ->where('status', '!=', 'closed')
            ->orderByDesc('tgl_invoice')
            ->get();

        $otherInvoices    = InvoicePembayaranOther::with('items')
            ->where('id_md_cust', $id)
            ->where('status', '!=', 'closed')
            ->orderByDesc('tgl_invoice')
            ->get();

        // Helper to build a row array from an invoice
        $buildRow = function ($inv, string $type) {
            $dpp   = (float) $inv->items->where('item_type', 'item')->where('is_reimbursement', false)->sum('nominal_invoice');
            $ppn   = (float) $inv->items->where('item_type', 'tax')->sum('nominal_invoice');
            $reimb = (float) $inv->items->where('is_reimbursement', true)->sum('nominal_invoice');
            $total = $dpp + $ppn + $reimb;

            // JO id for mutasi lookup
            $joId   = match($type) {
                'contract' => $inv->id_jo_cont ?? null,
                'tramper'  => $inv->id_jo_tram ?? null,
                'other'    => $inv->id_jo_other ?? null,
            };
            $joField = match($type) {
                'contract' => 'id_jo_cont',
                'tramper'  => 'id_jo_tram',
                'other'    => 'id_jo_other',
            };

            // Load JO title
            $joTitle = null;
            if ($joId) {
                $joModel = match($type) {
                    'contract' => \App\Models\Data\JoContract::find($joId),
                    'tramper'  => \App\Models\Data\JoTramper::find($joId),
                    'other'    => \App\Models\Data\JoOther::find($joId),
                };
                $joTitle = $joModel?->title;
            }

            $diterima = 0;
            if ($joId) {
                $diterima = (float) MutasiPenerimaanDetail::where($joField, $joId)
                    ->where('jenis', $type)
                    ->whereIn('tipe_pembayaran', [
                        MutasiPenerimaanDetail::TIPE_UANG_MUKA,
                        MutasiPenerimaanDetail::TIPE_PENERIMAAN,
                    ])
                    ->sum('nilai');
            }

            $invId = match($type) {
                'contract' => $inv->id_invoice_cont   ?? $inv->getKey(),
                'tramper'  => $inv->id_invoice_tramper ?? $inv->getKey(),
                'other'    => $inv->id_invoice_other   ?? $inv->getKey(),
            };
            $routeName = "invoice-{$type}.show";

            return [
                'type'         => $type,
                'inv_id'       => $invId,
                'route'        => $routeName,
                'no_invoice'   => $inv->no_invoice,
                'no_jo'        => $inv->no_jo ?? $joId ?? '-',
                'jo_title'     => $joTitle,
                'tgl_invoice'  => $inv->tgl_invoice,
                'due_date'     => $inv->due_date,
                'dpp'          => $dpp,
                'ppn'          => $ppn,
                'reimbursement'=> $reimb,
                'total_invoice'=> $total,
                'diterima'     => $diterima,
                'sisa'         => max(0, $total - $diterima),
                'status'       => $inv->status,
                'close_note'   => $inv->close_note,
                'reopen_note'  => $inv->reopen_note,
            ];
        };

        $invoices = collect();
        foreach ($contractInvoices as $inv) $invoices->push($buildRow($inv, 'contract'));
        foreach ($tramperInvoices  as $inv) $invoices->push($buildRow($inv, 'tramper'));
        foreach ($otherInvoices    as $inv) $invoices->push($buildRow($inv, 'other'));

        // Sort by invoice date desc
        $invoices = $invoices->sortByDesc('tgl_invoice')->values();

        $grandTotal    = $invoices->sum('total_invoice');
        $grandDiterima = $invoices->sum('diterima');
        $grandSisa     = $invoices->sum('sisa');

        return view('rekap.statement-of-account.show', compact(
            'customer', 'invoices', 'grandTotal', 'grandDiterima', 'grandSisa'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    //  EXPORT PDF – Statement of Account for one customer
    // ─────────────────────────────────────────────────────────────
    public function exportPdf($id)
    {
        $customer = Customer::findOrFail($id);

        // All non-closed invoices
        $contractInvoices = InvoicePembayaranContract::with('items')
            ->where('id_md_cust', $id)
            ->where('status', '!=', 'closed')
            ->orderByDesc('tgl_invoice')
            ->get();

        $tramperInvoices  = InvoicePembayaranTramper::with('items')
            ->where('id_md_cust', $id)
            ->where('status', '!=', 'closed')
            ->orderByDesc('tgl_invoice')
            ->get();

        $otherInvoices    = InvoicePembayaranOther::with('items')
            ->where('id_md_cust', $id)
            ->where('status', '!=', 'closed')
            ->orderByDesc('tgl_invoice')
            ->get();

        $buildRow = function ($inv, string $type) {
            $dpp   = (float) $inv->items->where('item_type', 'item')->where('is_reimbursement', false)->sum('nominal_invoice');
            $ppn   = (float) $inv->items->where('item_type', 'tax')->sum('nominal_invoice');
            $reimb = (float) $inv->items->where('is_reimbursement', true)->sum('nominal_invoice');
            $total = $dpp + $ppn + $reimb;

            $joId   = match($type) {
                'contract' => $inv->id_jo_cont ?? null,
                'tramper'  => $inv->id_jo_tram ?? null,
                'other'    => $inv->id_jo_other ?? null,
            };
            $joField = match($type) {
                'contract' => 'id_jo_cont',
                'tramper'  => 'id_jo_tram',
                'other'    => 'id_jo_other',
            };

            // JO title
            $joTitle = null;
            if ($joId) {
                $joModel = match($type) {
                    'contract' => \App\Models\Data\JoContract::find($joId),
                    'tramper'  => \App\Models\Data\JoTramper::find($joId),
                    'other'    => \App\Models\Data\JoOther::find($joId),
                };
                $joTitle = $joModel?->title;
            }

            // Payment details per JO
            $payment     = 0;
            $paymentBack = 0;
            $paymentDate = null;

            if ($joId) {
                $details = MutasiPenerimaanDetail::with('voucher.mutasiPenerimaan')
                    ->where($joField, $joId)
                    ->where('jenis', $type)
                    ->whereIn('tipe_pembayaran', [
                        MutasiPenerimaanDetail::TIPE_UANG_MUKA,
                        MutasiPenerimaanDetail::TIPE_PENERIMAAN,
                        MutasiPenerimaanDetail::TIPE_PENGEMBALIAN,
                    ])
                    ->orderBy('created_at')
                    ->get();

                foreach ($details as $d) {
                    if ($d->tipe_pembayaran === MutasiPenerimaanDetail::TIPE_PENGEMBALIAN) {
                        $paymentBack += (float) $d->nilai;
                    } else {
                        $payment += (float) $d->nilai;
                    }
                    // Latest payment date
                    $tgl = $d->voucher?->mutasiPenerimaan?->tanggal;
                    if ($tgl && (!$paymentDate || $tgl > $paymentDate)) {
                        $paymentDate = $tgl;
                    }
                }
            }

            $balance = max(0, $total - $payment + $paymentBack);

            return [
                'type'         => $type,
                'no_invoice'   => $inv->no_invoice,
                'jo_title'     => $joTitle,
                'tgl_invoice'  => $inv->tgl_invoice,
                'due_date'     => $inv->due_date,
                'dpp'          => $dpp,
                'ppn'          => $ppn,
                'reimbursement'=> $reimb,
                'total_invoice'=> $total,
                'payment_date' => $paymentDate,
                'payment'      => $payment,
                'payment_back' => $paymentBack,
                'balance'      => $balance,
                'status'       => $inv->status,
            ];
        };

        $invoices = collect();
        foreach ($contractInvoices as $inv) $invoices->push($buildRow($inv, 'contract'));
        foreach ($tramperInvoices  as $inv) $invoices->push($buildRow($inv, 'tramper'));
        foreach ($otherInvoices    as $inv) $invoices->push($buildRow($inv, 'other'));
        $invoices = $invoices->sortByDesc('tgl_invoice')->values();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'rekap.statement-of-account.pdf',
            compact('customer', 'invoices')
        )
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => false,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'Arial',
            'dpi'                  => 150,
        ]);

        $custCode = str_replace(' ', '-', $customer->code ?? $id);
        $filename = 'SOA-' . $custCode . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->stream($filename);
    }

}
