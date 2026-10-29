<?php

namespace App\Http\Controllers\Rekap;

use App\Http\Controllers\Controller;
use App\Models\Data\JoContract;
use App\Models\Data\MutasiPenerimaanDetail;
use App\Models\Master\Customer;
use Illuminate\Http\Request;

class RekapPiutangContractController extends Controller
{
    public function index(Request $request)
    {
        $query = JoContract::with(['contract.customer', 'items', 'pajakItems']);

        if ($request->filled('id_md_cust')) {
            $query->whereHas('contract', fn($q) => $q->where('id_md_cust', $request->id_md_cust));
        }
        if ($request->filled('no_jo')) {
            $query->where('no_jo_cont', 'like', '%' . $request->no_jo . '%');
        }

        $joContracts = $query->orderBy('no_jo_cont', 'desc')->get();
        $joContIds   = $joContracts->pluck('id_jo_cont')->toArray();

        // ── Down Payment per JO ───────────────────────────────────────────────
        $downPayments = MutasiPenerimaanDetail::whereIn('id_jo_cont', $joContIds)
            ->where('jenis', 'contract')
            ->where('tipe_pembayaran', MutasiPenerimaanDetail::TIPE_UANG_MUKA)
            ->selectRaw('id_jo_cont, SUM(nilai) as total_dp')
            ->groupBy('id_jo_cont')
            ->pluck('total_dp', 'id_jo_cont');

        // ── Payment Receipt per JO ────────────────────────────────────────────
        $paymentReceipts = MutasiPenerimaanDetail::whereIn('id_jo_cont', $joContIds)
            ->where('jenis', 'contract')
            ->where('tipe_pembayaran', MutasiPenerimaanDetail::TIPE_PENERIMAAN)
            ->selectRaw('id_jo_cont, SUM(nilai) as total_receipt')
            ->groupBy('id_jo_cont')
            ->pluck('total_receipt', 'id_jo_cont');

        $rows = $joContracts->map(function ($jo) use ($downPayments, $paymentReceipts) {
            // Total Selling = harga jual items + pajak (include_selling = true)
            $totalSelling = (float) $jo->items->sum('hargajual_idr')
                          + (float) $jo->pajakItems->where('include_selling', true)->sum('nominal_pajak');

            // Pendapatan IDR/USD (untuk info sub-baris) — sertakan pajak di IDR
            $totalIdr = (float) $jo->items->sum('pendapatan_idr')
                      + (float) $jo->pajakItems->where('include_selling', true)->sum('nominal_pajak');
            $totalUsd = (float) $jo->items->sum('pendapatan_usd');

            $itemWithUsd = $jo->items->first(fn($i) => (float)$i->pendapatan_usd > 0);
            $kursUsd     = $itemWithUsd ? (float) $itemWithUsd->kurs_usd : null;
            $tglKurs     = $itemWithUsd ? $itemWithUsd->tgl_kurs_usd : null;

            $uangMuka   = (float) ($downPayments[$jo->id_jo_cont] ?? 0);
            $payReceipt = (float) ($paymentReceipts[$jo->id_jo_cont] ?? 0);

            // Total Invoice = sisa tagihan setelah dikurangi semua penerimaan
            $totalInvoice  = $totalSelling - $uangMuka - $payReceipt;

            // Total Receivables = DP + Payment Receipt - Selling Price (negatif = masih ada piutang)
            $totalPiutang  = $uangMuka + $payReceipt - $totalSelling;

            if ($totalSelling <= 0) {
                $status = 'Outstanding';
            } elseif ($totalPiutang >= 0) {
                $status = 'Lunas';
            } elseif ($uangMuka > 0 || $payReceipt > 0) {
                $status = 'Partial';
            } else {
                $status = 'Outstanding';
            }

            return [
                'id_jo_cont'      => $jo->id_jo_cont,
                'no_jo_cont'      => $jo->no_jo_cont,
                'customer'        => $jo->contract?->customer?->customer ?? '-',
                'keterangan'      => $jo->title ?? '-',
                'date_start'      => $jo->contract?->date_start,
                'date_end'        => $jo->contract?->date_end,
                'total_selling'   => $totalSelling,
                'total_idr'       => $totalIdr,
                'total_usd'       => $totalUsd,
                'kurs_usd'        => $kursUsd,
                'tgl_kurs'        => $tglKurs,
                'total_invoice'   => $totalInvoice,
                'uang_muka'       => $uangMuka,
                'payment_receipt' => $payReceipt,
                'total_piutang'   => $totalPiutang,
                'status'          => $status,
            ];
        });

        if ($request->filled('status')) {
            $rows = $rows->filter(fn($r) => $r['status'] === $request->status)->values();
        }

        $summary = [
            'total_selling'   => $rows->sum('total_selling'),
            'total_invoice'   => $rows->sum('total_invoice'),
            'uang_muka'       => $rows->sum('uang_muka'),
            'payment_receipt' => $rows->sum('payment_receipt'),
            'total_piutang'   => $rows->sum('total_piutang'),
        ];

        $customers      = Customer::orderBy('customer')->get();
        $currentFilters = $request->only(['no_jo', 'id_md_cust', 'status']);

        return view('rekap.piutang-contract.index', compact(
            'rows', 'summary', 'customers', 'currentFilters'
        ));
    }
}
