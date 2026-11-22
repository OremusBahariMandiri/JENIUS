<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Master\ChartOfAccount;

class TrialBalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─────────────────────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->startOfYear()->format('Y-m-d'));
        $dateTo   = $request->input('date_to',   Carbon::now()->format('Y-m-d'));
        $coaId    = $request->input('coa_id');
        $parentId = $request->input('parent_id');

        $entries = $this->buildEntries($dateFrom, $dateTo);

        if ($coaId) {
            $entries = $entries->filter(fn($e) => $e['coa_id'] === $coaId);
        }
        if ($parentId) {
            $entries = $entries->filter(fn($e) => $e['parent_code'] === $parentId);
        }

        $totalIn      = $entries->sum('total_in');
        $totalOut     = $entries->sum('total_out');
        $totalBalance = $entries->sum('balance');

        $coaList    = ChartOfAccount::with('parentAccount')->orderBy('no_account')->get();
        $parentList = \App\Models\Master\ParentChartOfAccount::orderBy('kode_perkiraan')->get();

        return view('finance.trial-balance.index', compact(
            'entries', 'totalIn', 'totalOut', 'totalBalance',
            'dateFrom', 'dateTo', 'coaId', 'parentId',
            'coaList', 'parentList'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    //  EXPORT CSV
    // ─────────────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->startOfYear()->format('Y-m-d'));
        $dateTo   = $request->input('date_to',   Carbon::now()->format('Y-m-d'));
        $entries  = $this->buildEntries($dateFrom, $dateTo);

        $filename = 'trial-balance-' . $dateFrom . '-to-' . $dateTo . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($entries, $dateFrom, $dateTo) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Trial Balance Report', 'Period: ' . $dateFrom . ' — ' . $dateTo]);
            fputcsv($handle, []);
            fputcsv($handle, ['COA Code', 'Account Name', 'Parent', 'Total IN', 'Total OUT', 'Balance']);
            foreach ($entries as $e) {
                fputcsv($handle, [
                    $e['no_account'],
                    $e['account_name'],
                    $e['parent_name'],
                    number_format($e['total_in'],  2, '.', ''),
                    number_format($e['total_out'], 2, '.', ''),
                    number_format($e['balance'],   2, '.', ''),
                ]);
            }
            fputcsv($handle, []);
            fputcsv($handle, [
                '', 'TOTAL', '',
                number_format($entries->sum('total_in'),  2, '.', ''),
                number_format($entries->sum('total_out'), 2, '.', ''),
                number_format($entries->sum('balance'),   2, '.', ''),
            ]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─────────────────────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────
    //  SHOW — detail ledger per COA
    // ─────────────────────────────────────────────────────────────
    public function show(Request $request, string $coaId)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->startOfYear()->format('Y-m-d'));
        $dateTo   = $request->input('date_to',   Carbon::now()->format('Y-m-d'));

        $coa = ChartOfAccount::with('parentAccount')
            ->where('id_md_chart_of_account', $coaId)
            ->firstOrFail();

        $rows = collect();

        // OUT: LPJ Contract
        DB::table('d03_lpj_cont_item as item')
            ->join('d01_lpj_cont as lpj',       'lpj.id_lpj_cont',         '=', 'item.id_lpj_cont')
            ->leftJoin('b02_jo_cont_item as joi','joi.id_jo_cont_item',     '=', 'item.id_jo_cont_item')
            ->leftJoin('b01_jo_cont as jo',      'jo.id_jo_cont',           '=', 'joi.id_jo_cont')
            ->where('item.id_md_chart_of_account', $coaId)
            ->whereBetween('lpj.date', [$dateFrom, $dateTo])
            ->where('item.amount_lpj', '>', 0)
            ->select('lpj.date as tanggal',
                DB::raw("lpj.no_lpj_cont as no_transaksi"),
                DB::raw("COALESCE(jo.no_jo_cont,'\u2014') as no_jo"),
                DB::raw("COALESCE(jo.title,'\u2014') as keterangan"),
                DB::raw("0 as amount_in"), DB::raw("item.amount_lpj as amount_out"),
                DB::raw("'LPJ Contract' as source_type"),
                DB::raw("'Contract' as job_type"),
                DB::raw("'LPJ' as category"))
            ->get()->each(fn($r) => $rows->push((array)$r));

        // OUT: LPJ Tramper
        DB::table('d06_lpj_tram_item as item')
            ->join('d04_lpj_tram as lpj',        'lpj.id_lpj_tram',         '=', 'item.id_lpj_tram')
            ->leftJoin('b04_jo_tram_item as joi', 'joi.id_jo_tram_item',     '=', 'item.id_jo_tram_item')
            ->leftJoin('b03_jo_tram as jo',       'jo.id_jo_tram',           '=', 'joi.id_jo_tram')
            ->where('item.id_md_chart_of_account', $coaId)
            ->whereBetween('lpj.date', [$dateFrom, $dateTo])
            ->where('item.amount_lpj', '>', 0)
            ->select('lpj.date as tanggal',
                DB::raw("lpj.no_lpj_tram as no_transaksi"),
                DB::raw("COALESCE(jo.no_jo_tram,'\u2014') as no_jo"),
                DB::raw("COALESCE(jo.title,'\u2014') as keterangan"),
                DB::raw("0 as amount_in"), DB::raw("item.amount_lpj as amount_out"),
                DB::raw("'LPJ Tramper' as source_type"),
                DB::raw("'Tramper' as job_type"),
                DB::raw("'LPJ' as category"))
            ->get()->each(fn($r) => $rows->push((array)$r));

        // OUT: LPJ Other
        DB::table('d09_lpj_other_item as item')
            ->join('d07_lpj_other as lpj',         'lpj.id_lpj_other',       '=', 'item.id_lpj_other')
            ->leftJoin('b06_jo_other_item as joi',  'joi.id_jo_other_item',   '=', 'item.id_jo_other_item')
            ->leftJoin('b05_jo_other as jo',        'jo.id_jo_other',         '=', 'joi.id_jo_other')
            ->where('item.id_md_chart_of_account', $coaId)
            ->whereBetween('lpj.date', [$dateFrom, $dateTo])
            ->where('item.amount_lpj', '>', 0)
            ->select('lpj.date as tanggal',
                DB::raw("lpj.no_lpj_other as no_transaksi"),
                DB::raw("COALESCE(jo.no_jo_other,'\u2014') as no_jo"),
                DB::raw("COALESCE(jo.title,'\u2014') as keterangan"),
                DB::raw("0 as amount_in"), DB::raw("item.amount_lpj as amount_out"),
                DB::raw("'LPJ Other' as source_type"),
                DB::raw("'Other' as job_type"),
                DB::raw("'LPJ' as category"))
            ->get()->each(fn($r) => $rows->push((array)$r));

        // OUT: LPJ General
        DB::table('d12_lpj_gen_item as item')
            ->join('d10_lpj_gen as lpj', 'lpj.id_lpj_gen', '=', 'item.id_lpj_gen')
            ->where('item.id_md_chart_of_account', $coaId)
            ->whereBetween('lpj.date', [$dateFrom, $dateTo])
            ->where('item.amount_lpj', '>', 0)
            ->select('lpj.date as tanggal',
                DB::raw("lpj.no_lpj_gen as no_transaksi"),
                DB::raw("'\u2014' as no_jo"),
                DB::raw("COALESCE(lpj.note,'General LPJ') as keterangan"),
                DB::raw("0 as amount_in"), DB::raw("item.amount_lpj as amount_out"),
                DB::raw("'LPJ General' as source_type"),
                DB::raw("'General' as job_type"),
                DB::raw("'LPJ' as category"))
            ->get()->each(fn($r) => $rows->push((array)$r));

        // IN: Invoice Contract
        DB::table('f02_invoice_pembayaran_contract_item as item')
            ->join('f01_invoice_pembayaran_contract as inv', 'inv.id_invoice_contract', '=', 'item.id_invoice_contract')
            ->leftJoin('b02_jo_cont_item as joi', 'joi.id_jo_cont_item', '=', 'item.id_jo_cont_item')
            ->leftJoin('b01_jo_cont as jo',       'jo.id_jo_cont',       '=', 'joi.id_jo_cont')
            ->where('item.id_md_chart_of_account', $coaId)
            ->whereBetween('inv.tgl_invoice', [$dateFrom, $dateTo])
            ->where('item.nominal_invoice', '>', 0)
            ->select('inv.tgl_invoice as tanggal',
                DB::raw("inv.no_invoice as no_transaksi"),
                DB::raw("COALESCE(jo.no_jo_cont,'\u2014') as no_jo"),
                DB::raw("COALESCE(inv.title, jo.title,'\u2014') as keterangan"),
                DB::raw("item.nominal_invoice as amount_in"), DB::raw("0 as amount_out"),
                DB::raw("'Invoice Contract' as source_type"),
                DB::raw("'Contract' as job_type"),
                DB::raw("'Invoice' as category"))
            ->get()->each(fn($r) => $rows->push((array)$r));

        // IN: Invoice Tramper
        DB::table('f04_invoice_pembayaran_tramper_item as item')
            ->join('f03_invoice_pembayaran_tramper as inv', 'inv.id_invoice_tramper', '=', 'item.id_invoice_tramper')
            ->leftJoin('b04_jo_tram_item as joi', 'joi.id_jo_tram_item', '=', 'item.id_jo_tram_item')
            ->leftJoin('b03_jo_tram as jo',       'jo.id_jo_tram',       '=', 'joi.id_jo_tram')
            ->leftJoin('b03_jo_tram as jo_inv',   'jo_inv.id_jo_tram',   '=', 'inv.id_jo_tram')
            ->where('item.id_md_chart_of_account', $coaId)
            ->whereBetween('inv.tgl_invoice', [$dateFrom, $dateTo])
            ->where('item.nominal_invoice', '>', 0)
            ->select('inv.tgl_invoice as tanggal',
                DB::raw("inv.no_invoice as no_transaksi"),
                DB::raw("COALESCE(jo.no_jo_tram, jo_inv.no_jo_tram,'\u2014') as no_jo"),
                DB::raw("COALESCE(inv.title, jo.title, jo_inv.title,'\u2014') as keterangan"),
                DB::raw("item.nominal_invoice as amount_in"), DB::raw("0 as amount_out"),
                DB::raw("'Invoice Tramper' as source_type"),
                DB::raw("'Tramper' as job_type"),
                DB::raw("'Invoice' as category"))
            ->get()->each(fn($r) => $rows->push((array)$r));

        // IN: Invoice Other
        DB::table('f06_invoice_pembayaran_other_item as item')
            ->join('f05_invoice_pembayaran_other as inv', 'inv.id_invoice_other', '=', 'item.id_invoice_other')
            ->leftJoin('b06_jo_other_item as joi', 'joi.id_jo_other_item', '=', 'item.id_jo_other_item')
            ->leftJoin('b05_jo_other as jo',       'jo.id_jo_other',       '=', 'joi.id_jo_other')
            ->leftJoin('b05_jo_other as jo_inv',   'jo_inv.id_jo_other',   '=', 'inv.id_jo_other')
            ->where('item.id_md_chart_of_account', $coaId)
            ->whereBetween('inv.tgl_invoice', [$dateFrom, $dateTo])
            ->where('item.nominal_invoice', '>', 0)
            ->select('inv.tgl_invoice as tanggal',
                DB::raw("inv.no_invoice as no_transaksi"),
                DB::raw("COALESCE(jo.no_jo_other, jo_inv.no_jo_other,'\u2014') as no_jo"),
                DB::raw("COALESCE(inv.title, jo.title, jo_inv.title,'\u2014') as keterangan"),
                DB::raw("item.nominal_invoice as amount_in"), DB::raw("0 as amount_out"),
                DB::raw("'Invoice Other' as source_type"),
                DB::raw("'Other' as job_type"),
                DB::raw("'Invoice' as category"))
            ->get()->each(fn($r) => $rows->push((array)$r));

        // OUT: Mutasi Pembayaran detail
        DB::table('e03_mutasi_pembayaran_detail as d')
            ->join('e02_mutasi_pembayaran_voucher as v', 'v.id', '=', 'd.id_mutasi_voucher')
            ->join('e01_mutasi_pembayaran as m',         'm.id', '=', 'v.id_mutasi_pembayaran')
            ->leftJoin('b01_jo_cont as joc',  'joc.id_jo_cont',  '=', 'd.id_jo_cont')
            ->leftJoin('b03_jo_tram as jot',  'jot.id_jo_tram',  '=', 'd.id_jo_tram')
            ->leftJoin('b05_jo_other as joo', 'joo.id_jo_other', '=', 'd.id_jo_other')
            ->where('d.id_md_chart_of_account', $coaId)
            ->whereBetween('m.tanggal', [$dateFrom, $dateTo])
            ->where('d.nilai', '>', 0)
            ->select('m.tanggal as tanggal',
                DB::raw("m.nomor as no_transaksi"),
                DB::raw("COALESCE(joc.no_jo_cont, jot.no_jo_tram, joo.no_jo_other,'\u2014') as no_jo"),
                DB::raw("COALESCE(d.keterangan, joc.title, jot.title, joo.title, m.memo,'\u2014') as keterangan"),
                DB::raw("0 as amount_in"), DB::raw("d.nilai as amount_out"),
                DB::raw("'Mutasi Pembayaran' as source_type"),
                DB::raw("CASE WHEN joc.id_jo_cont IS NOT NULL THEN 'Contract' WHEN jot.id_jo_tram IS NOT NULL THEN 'Tramper' WHEN joo.id_jo_other IS NOT NULL THEN 'Other' ELSE '\u2014' END as job_type"),
                DB::raw("'Payment Mutation' as category"))
            ->get()->each(fn($r) => $rows->push((array)$r));

        // IN: Mutasi Penerimaan detail (nilai positif = IN)
        $penerimaanBase = DB::table('e06_mutasi_penerimaan_detail as d')
            ->join('e05_mutasi_penerimaan_voucher as v', 'v.id', '=', 'd.id_mutasi_voucher')
            ->join('e04_mutasi_penerimaan as m',         'm.id', '=', 'v.id_mutasi_penerimaan')
            ->leftJoin('b01_jo_cont as joc',  'joc.id_jo_cont',  '=', 'd.id_jo_cont')
            ->leftJoin('b03_jo_tram as jot',  'jot.id_jo_tram',  '=', 'd.id_jo_tram')
            ->leftJoin('b05_jo_other as joo', 'joo.id_jo_other', '=', 'd.id_jo_other')
            ->where('d.id_md_chart_of_account', $coaId)
            ->whereBetween('m.tanggal', [$dateFrom, $dateTo])
            ->where('d.nilai', '<>', 0);

        // Nilai positif → amount_in
        (clone $penerimaanBase)
            ->where('d.nilai', '>', 0)
            ->select('m.tanggal as tanggal',
                DB::raw("m.nomor as no_transaksi"),
                DB::raw("COALESCE(joc.no_jo_cont, jot.no_jo_tram, joo.no_jo_other,'\u2014') as no_jo"),
                DB::raw("COALESCE(d.keterangan, joc.title, jot.title, joo.title, m.memo,'\u2014') as keterangan"),
                DB::raw("d.nilai as amount_in"), DB::raw("0 as amount_out"),
                DB::raw("'Mutasi Penerimaan' as source_type"),
                DB::raw("CASE WHEN joc.id_jo_cont IS NOT NULL THEN 'Contract' WHEN jot.id_jo_tram IS NOT NULL THEN 'Tramper' WHEN joo.id_jo_other IS NOT NULL THEN 'Other' ELSE '\u2014' END as job_type"),
                DB::raw("'Receipt Mutation' as category"))
            ->get()->each(fn($r) => $rows->push((array)$r));

        // Nilai negatif (potongan PPh dll) → amount_out
        (clone $penerimaanBase)
            ->where('d.nilai', '<', 0)
            ->select('m.tanggal as tanggal',
                DB::raw("m.nomor as no_transaksi"),
                DB::raw("COALESCE(joc.no_jo_cont, jot.no_jo_tram, joo.no_jo_other,'\u2014') as no_jo"),
                DB::raw("COALESCE(d.keterangan, joc.title, jot.title, joo.title, m.memo,'\u2014') as keterangan"),
                DB::raw("0 as amount_in"), DB::raw("ABS(d.nilai) as amount_out"),
                DB::raw("'Mutasi Penerimaan (Potongan)' as source_type"),
                DB::raw("CASE WHEN joc.id_jo_cont IS NOT NULL THEN 'Contract' WHEN jot.id_jo_tram IS NOT NULL THEN 'Tramper' WHEN joo.id_jo_other IS NOT NULL THEN 'Other' ELSE '\u2014' END as job_type"),
                DB::raw("'Receipt Mutation' as category"))
            ->get()->each(fn($r) => $rows->push((array)$r));

        // Sort by date and compute running saldo
        $rows   = $rows->sortBy('tanggal')->values();
        $saldo  = (float)($coa->opening_balance ?? 0);
        $ledger = $rows->map(function ($r) use (&$saldo) {
            $in    = (float)$r['amount_in'];
            $out   = (float)$r['amount_out'];
            $saldo = $saldo + $in - $out;
            return array_merge($r, ['amount_in' => $in, 'amount_out' => $out, 'saldo' => $saldo]);
        });

        $totalIn  = $rows->sum('amount_in');
        $totalOut = $rows->sum('amount_out');

        return view('finance.trial-balance.show', compact(
            'coa', 'ledger', 'totalIn', 'totalOut', 'dateFrom', 'dateTo', 'coaId'
        ));
    }


    //  SYNC current_balance on master COA (all-time)
    // ─────────────────────────────────────────────────────────────
    public function syncBalance()
    {
        $entries = $this->buildEntries('1900-01-01', now()->format('Y-m-d'));

        DB::beginTransaction();
        try {
            // Reset semua COA yang tidak muncul di transaksi ke 0 dulu
            ChartOfAccount::query()->update(['current_balance' => 0]);

            foreach ($entries as $e) {
                ChartOfAccount::where('id_md_chart_of_account', $e['coa_id'])
                    ->update(['current_balance' => $e['balance']]);
            }
            DB::commit();
            return redirect()->route('trial-balance.index')
                ->with('success', 'COA balances synced successfully (' . $entries->count() . ' accounts updated).');
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->route('trial-balance.index')
                ->with('error', 'Sync failed: ' . $ex->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  BUILDER — aggregate all sources via raw DB queries
    // ─────────────────────────────────────────────────────────────
    private function buildEntries(string $dateFrom, string $dateTo): \Illuminate\Support\Collection
    {
        $inRows  = collect();
        $outRows = collect();

        // ══════════════════════════════════════════════════════════
        //  OUT: LPJ Contract items  (d03 → d01 date)
        // ══════════════════════════════════════════════════════════
        DB::table('d03_lpj_cont_item as item')
            ->join('d01_lpj_cont as lpj', 'lpj.id_lpj_cont', '=', 'item.id_lpj_cont')
            ->whereBetween('lpj.date', [$dateFrom, $dateTo])
            ->whereNotNull('item.id_md_chart_of_account')
            ->where('item.amount_lpj', '>', 0)
            ->groupBy('item.id_md_chart_of_account')
            ->select('item.id_md_chart_of_account as coa_id', DB::raw('SUM(item.amount_lpj) as amount'))
            ->get()
            ->each(fn($r) => $outRows->push(['coa_id' => $r->coa_id, 'amount' => (float)$r->amount]));

        // ── OUT: LPJ Tramper items  (d06 → d04 date) ──────────
        DB::table('d06_lpj_tram_item as item')
            ->join('d04_lpj_tram as lpj', 'lpj.id_lpj_tram', '=', 'item.id_lpj_tram')
            ->whereBetween('lpj.date', [$dateFrom, $dateTo])
            ->whereNotNull('item.id_md_chart_of_account')
            ->where('item.amount_lpj', '>', 0)
            ->groupBy('item.id_md_chart_of_account')
            ->select('item.id_md_chart_of_account as coa_id', DB::raw('SUM(item.amount_lpj) as amount'))
            ->get()
            ->each(fn($r) => $outRows->push(['coa_id' => $r->coa_id, 'amount' => (float)$r->amount]));

        // ── OUT: LPJ Other items  (d09 → d07 date) ────────────
        DB::table('d09_lpj_other_item as item')
            ->join('d07_lpj_other as lpj', 'lpj.id_lpj_other', '=', 'item.id_lpj_other')
            ->whereBetween('lpj.date', [$dateFrom, $dateTo])
            ->whereNotNull('item.id_md_chart_of_account')
            ->where('item.amount_lpj', '>', 0)
            ->groupBy('item.id_md_chart_of_account')
            ->select('item.id_md_chart_of_account as coa_id', DB::raw('SUM(item.amount_lpj) as amount'))
            ->get()
            ->each(fn($r) => $outRows->push(['coa_id' => $r->coa_id, 'amount' => (float)$r->amount]));

        // ── OUT: LPJ General items  (d12 → d10 date) ──────────
        DB::table('d12_lpj_gen_item as item')
            ->join('d10_lpj_gen as lpj', 'lpj.id_lpj_gen', '=', 'item.id_lpj_gen')
            ->whereBetween('lpj.date', [$dateFrom, $dateTo])
            ->whereNotNull('item.id_md_chart_of_account')
            ->where('item.amount_lpj', '>', 0)
            ->groupBy('item.id_md_chart_of_account')
            ->select('item.id_md_chart_of_account as coa_id', DB::raw('SUM(item.amount_lpj) as amount'))
            ->get()
            ->each(fn($r) => $outRows->push(['coa_id' => $r->coa_id, 'amount' => (float)$r->amount]));

        // ══════════════════════════════════════════════════════════
        //  IN: Invoice Contract items  (f02 → f01.tgl_invoice)
        // ══════════════════════════════════════════════════════════
        DB::table('f02_invoice_pembayaran_contract_item as item')
            ->join('f01_invoice_pembayaran_contract as inv', 'inv.id_invoice_contract', '=', 'item.id_invoice_contract')
            ->whereBetween('inv.tgl_invoice', [$dateFrom, $dateTo])
            ->whereNotNull('item.id_md_chart_of_account')
            ->where('item.nominal_invoice', '>', 0)
            ->groupBy('item.id_md_chart_of_account')
            ->select('item.id_md_chart_of_account as coa_id', DB::raw('SUM(item.nominal_invoice) as amount'))
            ->get()
            ->each(fn($r) => $inRows->push(['coa_id' => $r->coa_id, 'amount' => (float)$r->amount]));

        // ── IN: Invoice Tramper items  (f04 → f03.tgl_invoice) ─
        DB::table('f04_invoice_pembayaran_tramper_item as item')
            ->join('f03_invoice_pembayaran_tramper as inv', 'inv.id_invoice_tramper', '=', 'item.id_invoice_tramper')
            ->whereBetween('inv.tgl_invoice', [$dateFrom, $dateTo])
            ->whereNotNull('item.id_md_chart_of_account')
            ->where('item.nominal_invoice', '>', 0)
            ->groupBy('item.id_md_chart_of_account')
            ->select('item.id_md_chart_of_account as coa_id', DB::raw('SUM(item.nominal_invoice) as amount'))
            ->get()
            ->each(fn($r) => $inRows->push(['coa_id' => $r->coa_id, 'amount' => (float)$r->amount]));

        // ── IN: Invoice Other items  (f06 → f05.tgl_invoice) ───
        DB::table('f06_invoice_pembayaran_other_item as item')
            ->join('f05_invoice_pembayaran_other as inv', 'inv.id_invoice_other', '=', 'item.id_invoice_other')
            ->whereBetween('inv.tgl_invoice', [$dateFrom, $dateTo])
            ->whereNotNull('item.id_md_chart_of_account')
            ->where('item.nominal_invoice', '>', 0)
            ->groupBy('item.id_md_chart_of_account')
            ->select('item.id_md_chart_of_account as coa_id', DB::raw('SUM(item.nominal_invoice) as amount'))
            ->get()
            ->each(fn($r) => $inRows->push(['coa_id' => $r->coa_id, 'amount' => (float)$r->amount]));

        // ══════════════════════════════════════════════════════════
        //  OUT: Mutasi Pembayaran  (e03 detail → e02 voucher → e01 header.tanggal)
        // ══════════════════════════════════════════════════════════
        DB::table('e03_mutasi_pembayaran_detail as d')
            ->join('e02_mutasi_pembayaran_voucher as v', 'v.id', '=', 'd.id_mutasi_voucher')
            ->join('e01_mutasi_pembayaran as m', 'm.id', '=', 'v.id_mutasi_pembayaran')
            ->whereBetween('m.tanggal', [$dateFrom, $dateTo])
            ->whereNotNull('d.id_md_chart_of_account')
            ->where('d.nilai', '>', 0)
            ->groupBy('d.id_md_chart_of_account')
            ->select('d.id_md_chart_of_account as coa_id', DB::raw('SUM(d.nilai) as amount'))
            ->get()
            ->each(fn($r) => $outRows->push(['coa_id' => $r->coa_id, 'amount' => (float)$r->amount]));

        // ══════════════════════════════════════════════════════════
        //  IN: Mutasi Penerimaan  (e06 detail → e05 voucher → e04 header.tanggal)
        //  Nilai positif → IN; nilai negatif (potongan PPh dll) → OUT
        // ══════════════════════════════════════════════════════════
        // Positif → inRows
        DB::table('e06_mutasi_penerimaan_detail as d')
            ->join('e05_mutasi_penerimaan_voucher as v', 'v.id', '=', 'd.id_mutasi_voucher')
            ->join('e04_mutasi_penerimaan as m', 'm.id', '=', 'v.id_mutasi_penerimaan')
            ->whereBetween('m.tanggal', [$dateFrom, $dateTo])
            ->whereNotNull('d.id_md_chart_of_account')
            ->where('d.nilai', '>', 0)
            ->groupBy('d.id_md_chart_of_account')
            ->select('d.id_md_chart_of_account as coa_id', DB::raw('SUM(d.nilai) as amount'))
            ->get()
            ->each(fn($r) => $inRows->push(['coa_id' => $r->coa_id, 'amount' => (float)$r->amount]));

        // Negatif (potongan) → outRows
        DB::table('e06_mutasi_penerimaan_detail as d')
            ->join('e05_mutasi_penerimaan_voucher as v', 'v.id', '=', 'd.id_mutasi_voucher')
            ->join('e04_mutasi_penerimaan as m', 'm.id', '=', 'v.id_mutasi_penerimaan')
            ->whereBetween('m.tanggal', [$dateFrom, $dateTo])
            ->whereNotNull('d.id_md_chart_of_account')
            ->where('d.nilai', '<', 0)
            ->groupBy('d.id_md_chart_of_account')
            ->select('d.id_md_chart_of_account as coa_id', DB::raw('SUM(ABS(d.nilai)) as amount'))
            ->get()
            ->each(fn($r) => $outRows->push(['coa_id' => $r->coa_id, 'amount' => (float)$r->amount]));

        // ══════════════════════════════════════════════════════════
        //  Merge by COA
        // ══════════════════════════════════════════════════════════
        $allCoaIds = $inRows->pluck('coa_id')->merge($outRows->pluck('coa_id'))->unique()->values();

        // Load all relevant COA in one query
        $coaMap = ChartOfAccount::with('parentAccount.costType')
            ->whereIn('id_md_chart_of_account', $allCoaIds)
            ->get()
            ->keyBy('id_md_chart_of_account');

        // Sum per COA
        $inTotals  = $inRows->groupBy('coa_id')->map(fn($g)  => $g->sum('amount'));
        $outTotals = $outRows->groupBy('coa_id')->map(fn($g) => $g->sum('amount'));

        $entries = collect();
        foreach ($allCoaIds as $coaId) {
            $coa      = $coaMap->get($coaId);
            $totalIn  = round((float)($inTotals[$coaId]  ?? 0), 2);
            $totalOut = round((float)($outTotals[$coaId] ?? 0), 2);
            $balance  = round($totalIn - $totalOut, 2);

            $entries->push([
                'coa_id'          => $coaId,
                'no_account'      => $coa?->no_account      ?? '-',
                'account_name'    => $coa?->account_name    ?? $coaId,
                'parent_code'     => $coa?->parrent         ?? '-',
                'parent_name'     => $coa?->parentAccount?->nama ?? '-',
                'cost_type'       => $coa?->parentAccount?->costType?->name ?? '-',
                'opening_balance' => (float)($coa?->opening_balance ?? 0),
                'total_in'        => $totalIn,
                'total_out'       => $totalOut,
                'balance'         => $balance,
            ]);
        }

        return $entries->sortBy('no_account')->values();
    }
}
