<?php

namespace App\Http\Controllers\Master;

use App\Exports\DataMaster\ContractExport;
use App\Http\Controllers\Controller;
use App\Models\Master\Contract;
use App\Models\Master\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\IdGenerator;


class ContractController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:contract')->only('index');
        $this->middleware('check.access:contract,detail')->only('show');
        $this->middleware('check.access:contract,tambah')->only('create', 'store');
        $this->middleware('check.access:contract,ubah')->only('edit', 'update');
        $this->middleware('check.access:contract,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = Contract::with('customer');

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('no_contract', 'like', "%{$search}%")
                        ->orWhere('contract', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q) use ($search) {
                            $q->where('customer', 'like', "%{$search}%");
                        });
                });
            }

            // Filter by customer
            if ($request->has('customer_id') && !empty($request->customer_id)) {
                $query->where('id_md_cust', $request->customer_id);
            }

            // Filter by date range
            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->where('date_start', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->where('date_end', '<=', $request->date_to);
            }

            // Filter by status (active/expired)
            if ($request->has('status')) {
                $today = Carbon::today();
                if ($request->status === 'active') {
                    $query->where('date_end', '>=', $today);
                } elseif ($request->status === 'expired') {
                    $query->where('date_end', '<', $today);
                }
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $contracts = $query->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $contracts
                ]);
            }

            $customers = Customer::orderBy('customer', 'asc')->get();
            return view('master.contract.index', compact('contracts', 'customers'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving contracts: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving contracts: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('customer', 'asc')->get();
        return view('master.contract.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_contract' => 'required|string|max:100|unique:a02_md_contract,no_contract',
            'contract' => 'required|string|max:255',
            'id_md_cust' => 'required|integer|exists:a01_md_customer,id_md_cust',
            'expenditure' => 'required|numeric|min:0',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'note' => 'nullable|string',
        ], [
            'no_contract.required' => 'Nomor kontrak wajib diisi',
            'no_contract.unique' => 'Nomor kontrak sudah digunakan',
            'contract.required' => 'Nama kontrak wajib diisi',
            'id_md_cust.required' => 'Customer wajib dipilih',
            'id_md_cust.exists' => 'Customer tidak valid',
            'expenditure.required' => 'Nilai pengeluaran wajib diisi',
            'expenditure.numeric' => 'Nilai pengeluaran harus berupa angka',
            'date_start.required' => 'Tanggal mulai wajib diisi',
            'date_end.required' => 'Tanggal selesai wajib diisi',
            'date_end.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Generate ID
            $newId = IdGenerator::generate('A02', 'a02_md_contract', 'id_md_cont');

            $contract = Contract::create([
                'id_md_cont' => $newId,
                'no_contract' => strtoupper($request->no_contract),
                'contract' => $request->contract,
                'id_md_cust' => $request->id_md_cust,
                'expenditure' => $request->expenditure,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'note' => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kontrak berhasil ditambahkan',
                    'data' => $contract->load('customer')
                ], 201);
            }

            return redirect()
                ->route('contract.index')
                ->with('success', 'Kontrak berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating contract: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating contract: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $contract = Contract::with([
                'customer',
                'joContracts.area',
                'joContracts.department',
                'joContracts.items.invoice'
            ])->findOrFail($id);

            // Calculate contract summary
            $summary = [
                'total_items' => $contract->joContracts->sum(function ($jo) {
                    return $jo->items->count();
                }),
                'status' => $contract->date_end >= Carbon::today() ? 'Active' : 'Expired',
                'remaining_days' => Carbon::today()->diffInDays($contract->date_end, false),
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $contract,
                    'summary' => $summary
                ]);
            }

            return view('master.contract.show', compact('contract', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contract not found'
                ], 404);
            }

            return back()->with('error', 'Contract not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $contract = Contract::findOrFail($id);
            $customers = Customer::orderBy('customer', 'asc')->get();
            return view('master.contract.edit', compact('contract', 'customers'));
        } catch (\Exception $e) {
            return back()->with('error', 'Contract not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'no_contract' => 'required|string|max:100|unique:a02_md_contract,no_contract,' . $id . ',id_md_cont',
            'contract' => 'required|string|max:255',
            'id_md_cust' => 'required|integer|exists:a01_md_customer,id_md_cust',
            'expenditure' => 'required|numeric|min:0',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'note' => 'nullable|string',
        ], [
            'no_contract.required' => 'Nomor kontrak wajib diisi',
            'no_contract.unique' => 'Nomor kontrak sudah digunakan',
            'contract.required' => 'Nama kontrak wajib diisi',
            'id_md_cust.required' => 'Customer wajib dipilih',
            'id_md_cust.exists' => 'Customer tidak valid',
            'expenditure.required' => 'Nilai pengeluaran wajib diisi',
            'date_start.required' => 'Tanggal mulai wajib diisi',
            'date_end.required' => 'Tanggal selesai wajib diisi',
            'date_end.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $contract = Contract::findOrFail($id);

            $contract->update([
                'no_contract' => strtoupper($request->no_contract),
                'contract' => $request->contract,
                'id_md_cust' => $request->id_md_cust,
                'expenditure' => $request->expenditure,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'note' => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kontrak berhasil diupdate',
                    'data' => $contract->load('customer')
                ]);
            }

            return redirect()
                ->route('contract.index')
                ->with('success', 'Kontrak berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating contract: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating contract: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $contract = Contract::findOrFail($id);

            // Check if contract has related jo_contracts
            if ($contract->joContracts()->count() > 0) {
                DB::rollBack();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kontrak tidak dapat dihapus karena masih memiliki JO terkait'
                    ], 422);
                }

                return back()->with('error', 'Kontrak tidak dapat dihapus karena masih memiliki JO terkait');
            }

            $contract->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kontrak berhasil dihapus'
                ]);
            }

            return redirect()
                ->route('contract.index')
                ->with('success', 'Kontrak berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting contract: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting contract: ' . $e->getMessage());
        }
    }

    /**
     * Get contracts for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $query = Contract::with('customer')
                ->select('id_md_cont', 'no_contract', 'contract', 'id_md_cust');

            // Filter by customer if provided
            if ($request->has('customer_id')) {
                $query->where('id_md_cust', $request->customer_id);
            }

            // Only active contracts
            if ($request->has('active_only') && $request->active_only) {
                $query->where('date_end', '>=', Carbon::today());
            }

            $contracts = $query->orderBy('no_contract', 'asc')
                ->get()
                ->map(function ($contract) {
                    return [
                        'id' => $contract->id_md_cont,
                        'text' => $contract->no_contract . ' - ' . $contract->contract,
                        'customer' => $contract->customer->customer ?? '',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $contracts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving contracts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contract statistics
     */
    public function statistics(Request $request)
    {
        try {
            $today = Carbon::today();

            $stats = [
                'total_contracts' => Contract::count(),
                'active_contracts' => Contract::where('date_end', '>=', $today)->count(),
                'expired_contracts' => Contract::where('date_end', '<', $today)->count(),
                'expiring_soon' => Contract::whereBetween('date_end', [
                    $today,
                    $today->copy()->addDays(30)
                ])->count(),
                'total_expenditure' => Contract::sum('expenditure'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete contracts
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:a02_md_contract,id_md_cont'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $contracts = Contract::whereIn('id_md_cont', $request->ids)->get();

            // Check if any contract has related jo_contracts
            $hasRelations = false;
            foreach ($contracts as $contract) {
                if ($contract->joContracts()->count() > 0) {
                    $hasRelations = true;
                    break;
                }
            }

            if ($hasRelations) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa kontrak tidak dapat dihapus karena masih memiliki JO terkait'
                ], 422);
            }

            Contract::whereIn('id_md_cont', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contracts berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting contracts: ' . $e->getMessage()
            ], 500);
        }
    }


    public function export(Request $request)
    {
        $format    = $request->get('format', 'excel');
        $contracts = $this->buildExportQuery($request)->get();

        if ($format === 'pdf') {
            // Jika DomPDF tersedia — download langsung
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                    'master.contract.export_pdf',
                    compact('contracts')
                )->setPaper('a4', 'portrait');

                $filename = 'contracts_' . date('Ymd_His') . '.pdf';
                return $pdf->download($filename);
            }

            // Fallback — tampil di browser untuk Ctrl+P / Save as PDF
            $filename = 'contracts_' . date('Ymd_His') . '.pdf';
            $html     = view('master.contract.export_pdf', compact('contracts'))->render();

            return response($html, 200, [
                'Content-Type'        => 'text/html; charset=UTF-8',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }

        return (new ContractExport($contracts))->download();
    }

    // Tambahkan helper private method ini di ContractController
    private function buildExportQuery(Request $request)
    {
        $today = Carbon::today();
        $query = Contract::with('customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_contract', 'like', "%{$search}%")
                    ->orWhere('contract', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($q) => $q->where('customer', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('customer_id'))  $query->where('id_md_cust', $request->customer_id);
        if ($request->filled('date_from'))    $query->where('date_start', '>=', $request->date_from);
        if ($request->filled('date_to'))      $query->where('date_end', '<=', $request->date_to);

        if ($request->filled('status')) {
            match ($request->status) {
                'active'  => $query->where('date_end', '>=', $today),
                'expired' => $query->where('date_end', '<',  $today),
                default   => null,
            };
        }

        return $query->orderBy('no_contract', 'asc');
    }
}
