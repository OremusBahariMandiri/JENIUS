<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\CostType;
use App\Models\Master\ParentChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\IdGenerator;

class ChartOfAccountController extends Controller
{
    const TYPE_OPTIONS = [
        'Asset'     => 'Asset',
        'Liability' => 'Liability',
        'Equity'    => 'Equity',
        'Revenue'   => 'Revenue',
        'Expense'   => 'Expense',
    ];

    const PAYMENT_TYPE_OPTIONS = [
        'Cash'        => 'Cash',
        'Bank'        => 'Bank',
        'Credit Card' => 'Credit Card',
        'Transfer'    => 'Transfer',
        'Other'       => 'Other',
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:chart_of_account')->only('index');
        $this->middleware('check.access:chart_of_account,detail')->only('show');
        $this->middleware('check.access:chart_of_account,tambah')->only('create', 'store');
        $this->middleware('check.access:chart_of_account,ubah')->only('edit', 'update');
        $this->middleware('check.access:chart_of_account,hapus')->only('destroy', 'bulkDelete');
    }

    /**
     * Build base query with all filters applied (reusable for index & export).
     */
    private function buildFilteredQuery(Request $request)
    {
        $query = ChartOfAccount::with('parentAccount.costType');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', "%{$search}%")
                  ->orWhere('no_account', 'like', "%{$search}%")
                  ->orWhere('parrent', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('payment_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('parent')) {
            $query->where('parrent', $request->parent);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('id_md_cost_type')) {
            $query->whereHas('parentAccount', function ($q) use ($request) {
                $q->where('id_md_cost_type', $request->id_md_cost_type);
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        try {
            $query = $this->buildFilteredQuery($request);

            $sortBy    = $request->get('sort_by', 'no_account');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $accounts = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $accounts]);
            }

            $parentAccounts     = ParentChartOfAccount::with('costType')->orderBy('kode_perkiraan')->get();
            $costTypes          = CostType::orderBy('name')->get();
            $typeOptions        = self::TYPE_OPTIONS;
            $paymentTypeOptions = self::PAYMENT_TYPE_OPTIONS;

            // Kumpulkan filter aktif untuk ditampilkan di view
            $currentFilters = $request->only(['search', 'parent', 'type', 'payment_type', 'id_md_cost_type']);

            return view('master.chart_of_account.index', compact(
                'accounts', 'parentAccounts', 'costTypes', 'typeOptions', 'paymentTypeOptions', 'currentFilters'
            ));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving accounts: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving accounts: ' . $e->getMessage());
        }
    }

    /**
     * Export — supports format=excel|pdf, respects active filter.
     */
    public function export(Request $request)
    {
        $format   = $request->get('format', 'excel');
        $accounts = $this->buildFilteredQuery($request)
            ->orderBy('no_account', 'asc')
            ->get();

        if ($format === 'pdf') {
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf      = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                    'master.chart_of_account.export_pdf',
                    compact('accounts')
                )->setPaper('a4', 'landscape');

                $filename = 'chart_of_account_' . date('Ymd_His') . '.pdf';

                return $pdf->stream($filename);
            }

            // Fallback: printable HTML
            $filename = 'chart_of_account_' . date('Ymd_His') . '.pdf';
            $html     = view('master.chart_of_account.export_pdf', compact('accounts'))->render();

            return response($html, 200, [
                'Content-Type'        => 'text/html; charset=UTF-8',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }

        // Excel
        return (new \App\Exports\DataMaster\ChartOfAccountExport($accounts))->download();
    }

    /**
     * Build printable PDF HTML (fallback tanpa DomPDF) — landscape A4.
     */
    private function buildPdfHtml($accounts)
    {
        $total     = $accounts->count();
        $printedAt = now()->format('d/m/Y H:i');

        $rows = '';
        foreach ($accounts as $i => $acc) {
            $bg     = ($i % 2 === 0) ? '#fff' : '#f5f5f5';
            $parent = $acc->parentAccount
                ? e($acc->parentAccount->kode_perkiraan) . ' - ' . e($acc->parentAccount->nama)
                : '-';
            $rows .= '<tr style="background:' . $bg . ';">'
                . '<td style="text-align:center;padding:4px 6px;border:1px solid #ccc;">' . ($i + 1) . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . e($acc->no_account ?? '-') . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . e($acc->account_name) . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . $parent . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . e($acc->parentAccount?->costType?->name ?? '-') . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . e($acc->type ?? '-') . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . e($acc->payment_type ?? '-') . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;text-align:right;">' . number_format($acc->opening_balance, 2) . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;text-align:right;">' . number_format($acc->current_balance, 2) . '</td>'
                . '</tr>';
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chart of Account</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 10mm; }
        @media print { .no-print { display: none; } }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #000; margin: 0; padding: 20px; }
        h2 { font-size: 13px; font-weight: bold; margin: 0 0 2px 0; }
        p.sub { font-size: 8.5px; color: #555; margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #000; color: #fff; font-size: 8px; font-weight: bold; text-transform: uppercase; padding: 5px 6px; border: 1px solid #000; text-align: left; }
        .footer { margin-top: 10px; font-size: 8px; color: #555; text-align: right; }
        .print-btn { position: fixed; top: 12px; right: 12px; padding: 7px 16px; background: #111; color: #fff; border: none; cursor: pointer; font-size: 11px; border-radius: 2px; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Print / Save PDF</button>
    <h2>Chart of Account</h2>
    <p class="sub">Printed: {$printedAt} &mdash; Total: {$total} records</p>
    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">No. Account</th>
                <th width="20%">Account Name</th>
                <th width="18%">Parent Account</th>
                <th width="10%">Cost Type</th>
                <th width="8%">Type</th>
                <th width="9%">Payment Type</th>
                <th width="12%">Opening Balance</th>
                <th width="12%">Current Balance</th>
            </tr>
        </thead>
        <tbody>{$rows}</tbody>
    </table>
    <div class="footer">{$total} account(s) &mdash; {$printedAt}</div>
</body>
</html>
HTML;
    }

    public function create()
    {
        $parentAccounts     = ParentChartOfAccount::with('costType')->orderBy('kode_perkiraan')->get();
        $typeOptions        = self::TYPE_OPTIONS;
        $paymentTypeOptions = self::PAYMENT_TYPE_OPTIONS;

        return view('master.chart_of_account.create', compact(
            'parentAccounts', 'typeOptions', 'paymentTypeOptions'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_name'    => 'required|string|max:255',
            'parrent'         => 'nullable|string|exists:a12_md_parent_chart_of_account,kode_perkiraan',
            'no_account'      => 'nullable|string|max:50|unique:a13_md_chart_of_account,no_account',
            'type'            => 'nullable|string|max:100',
            'payment_type'    => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric',
            'current_balance' => 'nullable|numeric',
        ], [
            'account_name.required' => 'Nama akun wajib diisi',
            'no_account.unique'     => 'Nomor akun sudah digunakan',
            'parrent.exists'        => 'Parent akun tidak valid',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $newId = IdGenerator::generate('A13', 'a13_md_chart_of_account', 'id_md_chart_of_account');

            $account = ChartOfAccount::create([
                'id_md_chart_of_account' => $newId,
                'parrent'                => $request->parrent,
                'no_account'             => $request->no_account,
                'account_name'           => $request->account_name,
                'type'                   => $request->type,
                'payment_type'           => $request->payment_type,
                'opening_balance'        => $request->opening_balance ?? 0,
                'current_balance'        => $request->current_balance ?? 0,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Chart of Account berhasil ditambahkan',
                    'data'    => $account->load('parentAccount.costType')
                ], 201);
            }

            return redirect()->route('chart-of-account.index')
                ->with('success', 'Chart of Account berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating account: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Error creating account: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $account = ChartOfAccount::with('parentAccount.costType')->findOrFail($id);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $account]);
            }

            return view('master.chart_of_account.show', compact('account'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Account not found'], 404);
            }

            return back()->with('error', 'Account not found');
        }
    }

    public function edit($id)
    {
        try {
            $account            = ChartOfAccount::findOrFail($id);
            $parentAccounts     = ParentChartOfAccount::with('costType')->orderBy('kode_perkiraan')->get();
            $typeOptions        = self::TYPE_OPTIONS;
            $paymentTypeOptions = self::PAYMENT_TYPE_OPTIONS;

            return view('master.chart_of_account.edit', compact(
                'account', 'parentAccounts', 'typeOptions', 'paymentTypeOptions'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Account not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'account_name'    => 'required|string|max:255',
            'parrent'         => 'nullable|string|exists:a12_md_parent_chart_of_account,kode_perkiraan',
            'no_account'      => 'nullable|string|max:50|unique:a13_md_chart_of_account,no_account,' . $id . ',id_md_chart_of_account',
            'type'            => 'nullable|string|max:100',
            'payment_type'    => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric',
            'current_balance' => 'nullable|numeric',
        ], [
            'account_name.required' => 'Nama akun wajib diisi',
            'no_account.unique'     => 'Nomor akun sudah digunakan',
            'parrent.exists'        => 'Parent akun tidak valid',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $account = ChartOfAccount::findOrFail($id);

            $account->update([
                'parrent'         => $request->parrent,
                'no_account'      => $request->no_account,
                'account_name'    => $request->account_name,
                'type'            => $request->type,
                'payment_type'    => $request->payment_type,
                'opening_balance' => $request->opening_balance ?? 0,
                'current_balance' => $request->current_balance ?? 0,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Chart of Account berhasil diupdate',
                    'data'    => $account->load('parentAccount.costType')
                ]);
            }

            return redirect()->route('chart-of-account.index')
                ->with('success', 'Chart of Account berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating account: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Error updating account: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $account = ChartOfAccount::findOrFail($id);
            $account->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Chart of Account berhasil dihapus']);
            }

            return redirect()->route('chart-of-account.index')
                ->with('success', 'Chart of Account berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting account: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting account: ' . $e->getMessage());
        }
    }

    public function getForSelect(Request $request)
    {
        try {
            $query = ChartOfAccount::with('parentAccount.costType')
                ->select('id_md_chart_of_account', 'no_account', 'account_name', 'type', 'parrent');

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('id_md_cost_type')) {
                $query->whereHas('parentAccount', fn($q) => $q->where('id_md_cost_type', $request->id_md_cost_type));
            }

            $accounts = $query->orderBy('no_account')->get()->map(fn($acc) => [
                'id'           => $acc->id_md_chart_of_account,
                'text'         => ($acc->no_account ? $acc->no_account . ' - ' : '') . $acc->account_name,
                'no_account'   => $acc->no_account,
                'account_name' => $acc->account_name,
                'type'         => $acc->type,
                'cost_type'    => $acc->parentAccount?->costType?->name,
            ]);

            return response()->json(['success' => true, 'data' => $accounts]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving accounts: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|string|exists:a13_md_chart_of_account,id_md_chart_of_account'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            ChartOfAccount::whereIn('id_md_chart_of_account', $request->ids)->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Accounts berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting accounts: ' . $e->getMessage()
            ], 500);
        }
    }
}