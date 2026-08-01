<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\ChartOfAccount;
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

    public function index(Request $request)
    {
        try {
            // Eager load parentAccount + costType lewat parent
            $query = ChartOfAccount::with('parentAccount.costType');

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('account_name', 'like', "%{$search}%")
                      ->orWhere('no_account', 'like', "%{$search}%")
                      ->orWhere('parrent', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%")
                      ->orWhere('payment_type', 'like', "%{$search}%");
                });
            }

            if ($request->has('type') && !empty($request->type)) {
                $query->where('type', $request->type);
            }

            // Filter by cost type lewat relasi parent
            if ($request->has('id_md_cost_type') && !empty($request->id_md_cost_type)) {
                $query->whereHas('parentAccount', function ($q) use ($request) {
                    $q->where('id_md_cost_type', $request->id_md_cost_type);
                });
            }

            $sortBy    = $request->get('sort_by', 'no_account');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $accounts = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $accounts]);
            }

            $parentAccounts     = ParentChartOfAccount::with('costType')->orderBy('kode_perkiraan')->get();
            $typeOptions        = self::TYPE_OPTIONS;
            $paymentTypeOptions = self::PAYMENT_TYPE_OPTIONS;

            return view('master.chart_of_account.index', compact(
                'accounts', 'parentAccounts', 'typeOptions', 'paymentTypeOptions'
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

            if ($request->has('type') && !empty($request->type)) {
                $query->where('type', $request->type);
            }

            if ($request->has('id_md_cost_type') && !empty($request->id_md_cost_type)) {
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