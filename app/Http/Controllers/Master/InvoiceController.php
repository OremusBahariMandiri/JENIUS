<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\IdGenerator;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:invoice')->only('index');
        $this->middleware('check.access:invoice,detail')->only('show');
        $this->middleware('check.access:invoice,tambah')->only('create', 'store');
        $this->middleware('check.access:invoice,ubah')->only('edit', 'update');
        $this->middleware('check.access:invoice,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = Invoice::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('invoice_ctg', 'like', "%{$search}%")
                        ->orWhere('invoice_typ', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->has('invoice_ctg') && !empty($request->invoice_ctg)) {
                $query->where('invoice_ctg', $request->invoice_ctg);
            }

            // Filter by type
            if ($request->has('invoice_typ') && !empty($request->invoice_typ)) {
                $query->where('invoice_typ', $request->invoice_typ);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $invoices = $query->paginate($perPage);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $invoices
                ]);
            }

            // Get unique categories and types for filter
            $categories = Invoice::distinct()->pluck('invoice_ctg')->filter()->sort()->values();
            $types = Invoice::distinct()->pluck('invoice_typ')->filter()->sort()->values();

            return view('master.invoice.index', compact('invoices', 'categories', 'types'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving invoices: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving invoices: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get unique categories and types for reference
        $categories = Invoice::distinct()->pluck('invoice_ctg')->filter()->sort()->values();
        $types = Invoice::distinct()->pluck('invoice_typ')->filter()->sort()->values();

        return view('master.invoice.create', compact('categories', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_ctg' => 'required|string|max:100',
            'invoice_typ' => 'required|string|max:100',
            'note'        => 'nullable|string',
        ], [
            'invoice_ctg.required' => 'Kategori invoice wajib diisi',
            'invoice_typ.required' => 'Tipe invoice wajib diisi',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $newId = IdGenerator::generate('A04', 'a04_md_invoice', 'id_md_invoice');

            $invoice = Invoice::create([
                'id_md_invoice' => $newId,
                'code'          => strtoupper($request->code),
                'invoice_ctg'   => $request->invoice_ctg,
                'invoice_typ'   => $request->invoice_typ,
                'note'          => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice berhasil ditambahkan',
                    'data'    => $invoice
                ], 201);
            }

            return redirect()->route('invoice.index')->with('success', 'Invoice berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $invoice = Invoice::with([
                'joContractItems.joContract.contract.customer',
                'joContractItems.joContract.area',
                'joContractItems.joContract.department'
            ])->findOrFail($id);

            // Calculate summary
            $summary = [
                'total_items' => $invoice->joContractItems->count(),
                'total_contracts' => $invoice->joContractItems->pluck('joContract')->unique('id_jo_cont')->count(),
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $invoice,
                    'summary' => $summary
                ]);
            }

            return view('master.invoice.show', compact('invoice', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            return back()->with('error', 'Invoice not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            // Get unique categories and types for reference
            $categories = Invoice::distinct()->pluck('invoice_ctg')->filter()->sort()->values();
            $types = Invoice::distinct()->pluck('invoice_typ')->filter()->sort()->values();

            return view('master.invoice.edit', compact('invoice', 'categories', 'types'));
        } catch (\Exception $e) {
            return back()->with('error', 'Invoice not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // 'code' => 'required|string|max:50|unique:a04_md_invoice,code,' . $id . ',id_md_invoice',
            'invoice_ctg' => 'required|string|max:100',
            'invoice_typ' => 'required|string|max:100',
            'note' => 'nullable|string',
        ], [
            // 'code.required' => 'Kode invoice wajib diisi',
            // 'code.unique' => 'Kode invoice sudah digunakan',
            'invoice_ctg.required' => 'Kategori invoice wajib diisi',
            'invoice_typ.required' => 'Tipe invoice wajib diisi',
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
            $invoice = Invoice::findOrFail($id);

            $invoice->update([
                'code' => strtoupper($request->code),
                'invoice_ctg' => $request->invoice_ctg,
                'invoice_typ' => $request->invoice_typ,
                'note' => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice berhasil diupdate',
                    'data' => $invoice
                ]);
            }

            return redirect()
                ->route('invoice.index')
                ->with('success', 'Invoice berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating invoice: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::findOrFail($id);

            // Check if invoice has related jo_contract_items
            if ($invoice->joContractItems()->count() > 0) {
                DB::rollBack();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invoice tidak dapat dihapus karena masih memiliki item kontrak terkait'
                    ], 422);
                }

                return back()->with('error', 'Invoice tidak dapat dihapus karena masih memiliki item kontrak terkait');
            }

            $invoice->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice berhasil dihapus'
                ]);
            }

            return redirect()
                ->route('invoice.index')
                ->with('success', 'Invoice berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting invoice: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting invoice: ' . $e->getMessage());
        }
    }

    /**
     * Get invoices for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $query = Invoice::select('id_md_invoice', 'code', 'invoice_ctg', 'invoice_typ');

            // Filter by category if provided
            if ($request->has('category')) {
                $query->where('invoice_ctg', $request->category);
            }

            // Filter by type if provided
            if ($request->has('type')) {
                $query->where('invoice_typ', $request->type);
            }

            $invoices = $query->orderBy('code', 'asc')
                ->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->id_md_invoice,
                        'text' => $invoice->code . ' - ' . $invoice->invoice_ctg . ' (' . $invoice->invoice_typ . ')',
                        'category' => $invoice->invoice_ctg,
                        'type' => $invoice->invoice_typ,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $invoices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice categories
     */
    public function getCategories(Request $request)
    {
        try {
            $categories = Invoice::distinct()
                ->pluck('invoice_ctg')
                ->filter()
                ->sort()
                ->values()
                ->map(function ($category) {
                    return [
                        'id' => $category,
                        'text' => $category
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice types
     */
    public function getTypes(Request $request)
    {
        try {
            $query = Invoice::distinct();

            // Filter by category if provided
            if ($request->has('category')) {
                $query->where('invoice_ctg', $request->category);
            }

            $types = $query->pluck('invoice_typ')
                ->filter()
                ->sort()
                ->values()
                ->map(function ($type) {
                    return [
                        'id' => $type,
                        'text' => $type
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $types
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving types: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice statistics
     */
    public function statistics(Request $request)
    {
        try {
            $stats = [
                'total_invoices' => Invoice::count(),
                'total_categories' => Invoice::distinct('invoice_ctg')->count('invoice_ctg'),
                'total_types' => Invoice::distinct('invoice_typ')->count('invoice_typ'),
                'by_category' => Invoice::select('invoice_ctg', DB::raw('count(*) as total'))
                    ->groupBy('invoice_ctg')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'category' => $item->invoice_ctg,
                            'total' => $item->total
                        ];
                    }),
                'by_type' => Invoice::select('invoice_typ', DB::raw('count(*) as total'))
                    ->groupBy('invoice_typ')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'type' => $item->invoice_typ,
                            'total' => $item->total
                        ];
                    }),
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
     * Bulk delete invoices
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:a04_md_invoice,id_md_invoice'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $invoices = Invoice::whereIn('id_md_invoice', $request->ids)->get();

            // Check if any invoice has related items
            $hasRelations = false;
            foreach ($invoices as $invoice) {
                if ($invoice->joContractItems()->count() > 0) {
                    $hasRelations = true;
                    break;
                }
            }

            if ($hasRelations) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa invoice tidak dapat dihapus karena masih memiliki item kontrak terkait'
                ], 422);
            }

            Invoice::whereIn('id_md_invoice', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoices berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting invoices: ' . $e->getMessage()
            ], 500);
        }
    }
}
