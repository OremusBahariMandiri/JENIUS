<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\IdGenerator;
use App\Exports\DataMaster\InvoiceExport;

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

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('invoice_ctg', 'like', "%{$search}%")
                        ->orWhere('invoice_typ', 'like', "%{$search}%")
                        ->orWhere('jo_ctg', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%");
                });
            }

            if ($request->filled('invoice_ctg')) {
                $query->where('invoice_ctg', $request->invoice_ctg);
            }

            if ($request->filled('invoice_typ')) {
                $query->where('invoice_typ', $request->invoice_typ);
            }

            if ($request->filled('jo_ctg')) {
                $query->where('jo_ctg', $request->jo_ctg);
            }

            $sortBy    = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage  = $request->get('per_page', 15);
            $invoices = $query->paginate($perPage)->withQueryString();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $invoices]);
            }

            $categories = Invoice::distinct()->pluck('invoice_ctg')->filter()->sort()->values();
            $types      = Invoice::distinct()->pluck('invoice_typ')->filter()->sort()->values();
            $joCtgOptions = Invoice::JO_CTG_OPTIONS;

            $currentFilters = [
                'search'      => $request->get('search', ''),
                'invoice_ctg' => $request->get('invoice_ctg', ''),
                'invoice_typ' => $request->get('invoice_typ', ''),
                'jo_ctg'      => $request->get('jo_ctg', ''),
            ];

            return view('master.invoice.index', compact('invoices', 'categories', 'types', 'joCtgOptions', 'currentFilters'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error retrieving invoices: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving invoices: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories   = Invoice::distinct()->pluck('invoice_ctg')->filter()->sort()->values();
        $types        = Invoice::distinct()->pluck('invoice_typ')->filter()->sort()->values();
        $joCtgOptions = Invoice::JO_CTG_OPTIONS;

        return view('master.invoice.create', compact('categories', 'types', 'joCtgOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_ctg' => 'required|string|max:100',
            'invoice_typ' => 'required|string|max:100',
            'jo_ctg'      => 'required|in:contract,tramper,other',
            'note'        => 'nullable|string',
        ], [
            'invoice_ctg.required' => 'Kategori invoice wajib diisi',
            'invoice_typ.required' => 'Tipe invoice wajib diisi',
            'jo_ctg.required'      => 'JO Category wajib dipilih',
            'jo_ctg.in'            => 'JO Category tidak valid',
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
                'jo_ctg'        => $request->jo_ctg,
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
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
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

            $summary = [
                'total_items'     => $invoice->joContractItems->count(),
                'total_contracts' => $invoice->joContractItems->pluck('joContract')->unique('id_jo_cont')->count(),
            ];

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $invoice, 'summary' => $summary]);
            }

            return view('master.invoice.show', compact('invoice', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
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
            $invoice      = Invoice::findOrFail($id);
            $categories   = Invoice::distinct()->pluck('invoice_ctg')->filter()->sort()->values();
            $types        = Invoice::distinct()->pluck('invoice_typ')->filter()->sort()->values();
            $joCtgOptions = Invoice::JO_CTG_OPTIONS;

            return view('master.invoice.edit', compact('invoice', 'categories', 'types', 'joCtgOptions'));
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
            'invoice_ctg' => 'required|string|max:100',
            'invoice_typ' => 'required|string|max:100',
            'jo_ctg'      => 'required|in:contract,tramper,other',
            'note'        => 'nullable|string',
        ], [
            'invoice_ctg.required' => 'Kategori invoice wajib diisi',
            'invoice_typ.required' => 'Tipe invoice wajib diisi',
            'jo_ctg.required'      => 'JO Category wajib dipilih',
            'jo_ctg.in'            => 'JO Category tidak valid',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::findOrFail($id);

            $invoice->update([
                'code'        => strtoupper($request->code),
                'invoice_ctg' => $request->invoice_ctg,
                'invoice_typ' => $request->invoice_typ,
                'jo_ctg'      => $request->jo_ctg,
                'note'        => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Invoice berhasil diupdate', 'data' => $invoice]);
            }

            return redirect()->route('invoice.index')->with('success', 'Invoice berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating invoice: ' . $e->getMessage()], 500);
            }

            return back()->withInput()->with('error', 'Error updating invoice: ' . $e->getMessage());
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
                return response()->json(['success' => true, 'message' => 'Invoice berhasil dihapus']);
            }

            return redirect()->route('invoice.index')->with('success', 'Invoice berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting invoice: ' . $e->getMessage()], 500);
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
            $query = Invoice::select('id_md_invoice', 'code', 'invoice_ctg', 'invoice_typ', 'jo_ctg');

            if ($request->has('category')) {
                $query->where('invoice_ctg', $request->category);
            }
            if ($request->has('type')) {
                $query->where('invoice_typ', $request->type);
            }
            if ($request->has('jo_ctg')) {
                $query->where('jo_ctg', $request->jo_ctg);
            }

            $invoices = $query->orderBy('code', 'asc')
                ->get()
                ->map(function ($invoice) {
                    return [
                        'id'       => $invoice->id_md_invoice,
                        'text'     => $invoice->code . ' - ' . $invoice->invoice_ctg . ' (' . $invoice->invoice_typ . ')',
                        'category' => $invoice->invoice_ctg,
                        'type'     => $invoice->invoice_typ,
                        'jo_ctg'   => $invoice->jo_ctg,
                    ];
                });

            return response()->json(['success' => true, 'data' => $invoices]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving invoices: ' . $e->getMessage()], 500);
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
                ->filter()->sort()->values()
                ->map(fn($c) => ['id' => $c, 'text' => $c]);

            return response()->json(['success' => true, 'data' => $categories]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving categories: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get invoice types
     */
    public function getTypes(Request $request)
    {
        try {
            $query = Invoice::distinct();

            if ($request->has('category')) {
                $query->where('invoice_ctg', $request->category);
            }

            $types = $query->pluck('invoice_typ')
                ->filter()->sort()->values()
                ->map(fn($t) => ['id' => $t, 'text' => $t]);

            return response()->json(['success' => true, 'data' => $types]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving types: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get invoice statistics
     */
    public function statistics(Request $request)
    {
        try {
            $stats = [
                'total_invoices'   => Invoice::count(),
                'total_categories' => Invoice::distinct('invoice_ctg')->count('invoice_ctg'),
                'total_types'      => Invoice::distinct('invoice_typ')->count('invoice_typ'),
                'by_jo_ctg'        => Invoice::select('jo_ctg', DB::raw('count(*) as total'))
                    ->groupBy('jo_ctg')
                    ->get()
                    ->map(fn($item) => ['jo_ctg' => $item->jo_ctg, 'total' => $item->total]),
                'by_category'      => Invoice::select('invoice_ctg', DB::raw('count(*) as total'))
                    ->groupBy('invoice_ctg')
                    ->get()
                    ->map(fn($item) => ['category' => $item->invoice_ctg, 'total' => $item->total]),
                'by_type'          => Invoice::select('invoice_typ', DB::raw('count(*) as total'))
                    ->groupBy('invoice_typ')
                    ->get()
                    ->map(fn($item) => ['type' => $item->invoice_typ, 'total' => $item->total]),
            ];

            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving statistics: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk delete invoices
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:a04_md_invoice,id_md_invoice'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $invoices = Invoice::whereIn('id_md_invoice', $request->ids)->get();

            foreach ($invoices as $invoice) {
                if ($invoice->joContractItems()->count() > 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Beberapa invoice tidak dapat dihapus karena masih memiliki item kontrak terkait'
                    ], 422);
                }
            }

            Invoice::whereIn('id_md_invoice', $request->ids)->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Invoices berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting invoices: ' . $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $format   = $request->get('format', 'excel');
        $invoices = $this->buildExportQuery($request)->get();

        if ($format === 'pdf') {
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf      = \Barryvdh\DomPDF\Facade\Pdf::loadView('master.invoice.export_pdf', compact('invoices'))
                    ->setPaper('a4', 'portrait');
                $filename = 'invoices_' . date('Ymd_His') . '.pdf';
                return $pdf->download($filename);
            }

            $filename = 'invoices_' . date('Ymd_His') . '.pdf';
            $html     = view('master.invoice.export_pdf', compact('invoices'))->render();
            return response($html, 200, [
                'Content-Type'        => 'text/html; charset=UTF-8',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }

        return (new InvoiceExport($invoices))->download();
    }

    private function buildExportQuery(Request $request)
    {
        $query = Invoice::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('invoice_ctg', 'like', "%{$search}%")
                    ->orWhere('invoice_typ', 'like', "%{$search}%")
                    ->orWhere('jo_ctg', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%");
            });
        }

        if ($request->filled('invoice_ctg')) {
            $query->where('invoice_ctg', $request->invoice_ctg);
        }
        if ($request->filled('invoice_typ')) {
            $query->where('invoice_typ', $request->invoice_typ);
        }
        if ($request->filled('jo_ctg')) {
            $query->where('jo_ctg', $request->jo_ctg);
        }

        return $query->orderBy('invoice_ctg', 'asc')->orderBy('invoice_typ', 'asc');
    }
}