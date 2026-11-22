<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Data\JoContract;
use App\Models\Data\JoTramper;
use App\Models\Data\JoOther;
use App\Models\Data\KasbonContract;
use App\Models\Data\KasbonTramper;
use App\Models\Data\KasbonOther;
use App\Models\Data\KasbonGen;
use App\Models\Data\LpjContract;
use App\Models\Data\LpjTramper;
use App\Models\Data\LpjOther;
use App\Models\Data\LpjGen;
use App\Models\Data\InvoicePembayaranContract;
use App\Models\Data\InvoicePembayaranTramper;
use App\Models\Data\InvoicePembayaranOther;
use App\Models\Data\MutasiPembayaran;
use App\Models\Data\MutasiPenerimaan;
use App\Models\Master\Customer;
use App\Models\ActivityLog;
use App\Models\User;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── Main page ────────────────────────────────────────────────────────────
    public function index()
    {
        $now       = Carbon::now();
        $thisMonth = $now->month;
        $thisYear  = $now->year;

        // KPI counts (always all-time for the summary cards)
        $stats = [
            'jo_contract'      => JoContract::count(),
            'jo_tramper'       => JoTramper::count(),
            'jo_other'         => JoOther::count(),
            'kasbon_total'     => KasbonContract::count() + KasbonTramper::count()
                                + KasbonOther::count() + KasbonGen::count(),
            'lpj_total'        => LpjContract::count() + LpjTramper::count()
                                + LpjOther::count() + LpjGen::count(),
            'invoice_contract' => InvoicePembayaranContract::count(),
            'invoice_tramper'  => InvoicePembayaranTramper::count(),
            'invoice_other'    => InvoicePembayaranOther::count(),
            'mutasi_bayar'     => MutasiPembayaran::count(),
            'mutasi_terima'    => MutasiPenerimaan::count(),
            'customers'        => Customer::count(),
            'users'            => User::count(),
        ];
        $stats['jo_total']      = $stats['jo_contract'] + $stats['jo_tramper'] + $stats['jo_other'];
        $stats['invoice_total'] = $stats['invoice_contract'] + $stats['invoice_tramper'] + $stats['invoice_other'];

        // This-month delta
        $joThisMonth = [
            'contract' => JoContract::whereYear('tgl_jo_cont',  $thisYear)->whereMonth('tgl_jo_cont',  $thisMonth)->count(),
            'tramper'  => JoTramper::whereYear('tgl_jo_tram',   $thisYear)->whereMonth('tgl_jo_tram',  $thisMonth)->count(),
            'other'    => JoOther::whereYear('tgl_jo_other',    $thisYear)->whereMonth('tgl_jo_other', $thisMonth)->count(),
        ];

        // Default chart data: last 12 months
        $defaultFrom = Carbon::now()->subMonths(11)->startOfMonth()->format('Y-m-d');
        $defaultTo   = Carbon::now()->endOfMonth()->format('Y-m-d');

        $joMonthly     = $this->buildJoMonthly($defaultFrom, $defaultTo);
        $kasbonMonthly = $this->buildKasbonMonthly($defaultFrom, $defaultTo);
        $invoiceStatus = $this->buildInvoiceStatus($defaultFrom, $defaultTo);
        $topCustomers  = $this->buildTopCustomers($defaultFrom, $defaultTo);

        $recentLogs  = ActivityLog::latest()->limit(8)->get();
        $activeUsers = ActivityLog::where('action', 'login')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->distinct('user_id')->count('user_id');

        return view('home', compact(
            'stats', 'joMonthly', 'kasbonMonthly', 'invoiceStatus',
            'joThisMonth', 'recentLogs', 'activeUsers', 'topCustomers',
            'now', 'defaultFrom', 'defaultTo'
        ));
    }

    // ── AJAX endpoint ────────────────────────────────────────────────────────
    public function chartData(Request $request)
    {
        $request->validate([
            'chart'     => 'required|in:jo,kasbon,invoice,customers,kpi',
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from',
        ]);

        $from = $request->date_from;
        $to   = $request->date_to;

        switch ($request->chart) {
            case 'jo':
                return response()->json($this->buildJoMonthly($from, $to));

            case 'kasbon':
                return response()->json($this->buildKasbonMonthly($from, $to));

            case 'invoice':
                return response()->json($this->buildInvoiceStatus($from, $to));

            case 'customers':
                return response()->json($this->buildTopCustomers($from, $to));

            case 'kpi':
                return response()->json($this->buildKpiRange($from, $to));
        }
    }

    // ── Builders ─────────────────────────────────────────────────────────────
    private function buildJoMonthly(string $from, string $to): array
    {
        $start  = Carbon::parse($from)->startOfMonth();
        $end    = Carbon::parse($to)->endOfMonth();
        $months = [];
        $cur    = $start->copy();

        while ($cur->lte($end)) {
            $m = $cur->month;
            $y = $cur->year;
            $months[] = [
                'label'    => $cur->format('M Y'),
                'contract' => JoContract::whereYear('tgl_jo_cont',  $y)->whereMonth('tgl_jo_cont',  $m)->count(),
                'tramper'  => JoTramper::whereYear('tgl_jo_tram',   $y)->whereMonth('tgl_jo_tram',  $m)->count(),
                'other'    => JoOther::whereYear('tgl_jo_other',    $y)->whereMonth('tgl_jo_other', $m)->count(),
            ];
            $cur->addMonth();
        }
        return $months;
    }

    private function buildKasbonMonthly(string $from, string $to): array
    {
        $start  = Carbon::parse($from)->startOfMonth();
        $end    = Carbon::parse($to)->endOfMonth();
        $months = [];
        $cur    = $start->copy();

        while ($cur->lte($end)) {
            $m = $cur->month;
            $y = $cur->year;
            $months[] = [
                'label'    => $cur->format('M Y'),
                'contract' => KasbonContract::whereYear('tgl_kasbon', $y)->whereMonth('tgl_kasbon', $m)->count(),
                'tramper'  => KasbonTramper::whereYear('tgl_kasbon',  $y)->whereMonth('tgl_kasbon', $m)->count(),
                'other'    => KasbonOther::whereYear('tgl_kasbon',    $y)->whereMonth('tgl_kasbon', $m)->count(),
                'general'  => KasbonGen::whereYear('tgl_kasbon',      $y)->whereMonth('tgl_kasbon', $m)->count(),
            ];
            $cur->addMonth();
        }
        return $months;
    }

    private function buildInvoiceStatus(string $from, string $to): array
    {
        $filter = fn($q) => $q->whereBetween('tgl_invoice', [$from, $to]);

        return [
            'contract' => InvoicePembayaranContract::whereBetween('tgl_invoice', [$from, $to])
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')->pluck('total', 'status')->toArray(),
            'tramper'  => InvoicePembayaranTramper::whereBetween('tgl_invoice', [$from, $to])
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')->pluck('total', 'status')->toArray(),
            'other'    => InvoicePembayaranOther::whereBetween('tgl_invoice', [$from, $to])
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')->pluck('total', 'status')->toArray(),
        ];
    }

    private function buildTopCustomers(string $from, string $to): array
    {
        return JoTramper::select('id_md_cust', DB::raw('count(*) as total'))
            ->with('customer')
            ->whereBetween('tgl_jo_tram', [$from, $to])
            ->groupBy('id_md_cust')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'name'  => $r->customer->customer ?? 'Unknown',
                'total' => $r->total,
            ])
            ->toArray();
    }

    private function buildKpiRange(string $from, string $to): array
    {
        return [
            'jo_contract'  => JoContract::whereBetween('tgl_jo_cont',   [$from, $to])->count(),
            'jo_tramper'   => JoTramper::whereBetween('tgl_jo_tram',    [$from, $to])->count(),
            'jo_other'     => JoOther::whereBetween('tgl_jo_other',     [$from, $to])->count(),
            'kasbon_total' => KasbonContract::whereBetween('tgl_kasbon',[$from, $to])->count()
                            + KasbonTramper::whereBetween('tgl_kasbon', [$from, $to])->count()
                            + KasbonOther::whereBetween('tgl_kasbon',   [$from, $to])->count()
                            + KasbonGen::whereBetween('tgl_kasbon',     [$from, $to])->count(),
            'lpj_total'    => LpjContract::whereBetween('created_at',   [$from, $to])->count()
                            + LpjTramper::whereBetween('created_at',    [$from, $to])->count()
                            + LpjOther::whereBetween('created_at',      [$from, $to])->count()
                            + LpjGen::whereBetween('created_at',        [$from, $to])->count(),
            'invoice_total'=> InvoicePembayaranContract::whereBetween('tgl_invoice',[$from,$to])->count()
                            + InvoicePembayaranTramper::whereBetween('tgl_invoice', [$from,$to])->count()
                            + InvoicePembayaranOther::whereBetween('tgl_invoice',   [$from,$to])->count(),
            'mutasi_bayar' => MutasiPembayaran::whereBetween('tanggal',  [$from, $to])->count(),
            'mutasi_terima'=> MutasiPenerimaan::whereBetween('tanggal',  [$from, $to])->count(),
        ];
    }
}
