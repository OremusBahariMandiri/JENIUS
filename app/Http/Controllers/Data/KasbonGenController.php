<?php

namespace App\Http\Controllers\Data;

use App\Helpers\IdGenerator;
use App\Http\Controllers\Controller;
use App\Models\Data\KasbonGen;
use App\Models\Data\KasbonGenItem;
use App\Models\Master\Departemen;
use App\Models\Master\Branch;
use App\Models\Master\ReleaseTo;
use App\Models\Master\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KasbonGenController extends Controller
{
    // ========================================
    // INDEX
    // ========================================

    public function index(Request $request)
    {
        try {
            $query = KasbonGen::with(['departemen', 'cabang', 'release', 'items.invoice']);

            if ($request->filled('id_md_dep')) {
                $query->where('id_md_dep', $request->id_md_dep);
            }
            if ($request->filled('id_md_cabang')) {
                $query->where('id_md_cabang', $request->id_md_cabang);
            }
            if ($request->filled('tgl_kasbon_from')) {
                $query->where('tgl_kasbon', '>=', $request->tgl_kasbon_from);
            }
            if ($request->filled('tgl_kasbon_to')) {
                $query->where('tgl_kasbon', '<=', $request->tgl_kasbon_to);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id_kasbon_gen', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('departemen', function ($q) use ($search) {
                            $q->where('nama_dep', 'like', "%{$search}%");
                        })
                        ->orWhereHas('cabang', function ($q) use ($search) {
                            $q->where('nama_branch', 'like', "%{$search}%");
                        });
                });
            }

            $sortBy    = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $kasbonGens = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $kasbonGens]);
            }

            $departemens = Departemen::orderBy('nama_dep')->get();
            $cabangs     = Branch::orderBy('nama_branch')->get();
            $releases    = ReleaseTo::orderBy('id_md_release')->get();

            $currentFilters = [
                'id_md_dep'       => $request->get('id_md_dep', ''),
                'id_md_cabang'    => $request->get('id_md_cabang', ''),
                'tgl_kasbon_from' => $request->get('tgl_kasbon_from', ''),
                'tgl_kasbon_to'   => $request->get('tgl_kasbon_to', ''),
            ];

            return view('data.kasbon-gen.index', compact(
                'kasbonGens',
                'departemens',
                'cabangs',
                'releases',
                'currentFilters'
            ));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ========================================
    // CREATE
    // ========================================

    public function create()
    {
        $departemens     = Departemen::orderBy('nama_dep')->get();
        $cabangs         = Branch::orderBy('nama_branch')->get();
        $releases        = ReleaseTo::orderBy('id_md_release')->get();
        $previewNoKasbon = IdGenerator::generateCaNo('c07_kasbon_gen', 'id_kasbon_gen');

        // Ambil invoice dengan jo_ctg = 'general' saja, dikelompokkan per kategori
        $invoices = Invoice::where('jo_ctg', 'general')
            ->orderBy('invoice_ctg')
            ->orderBy('invoice_typ')
            ->get();

        return view('data.kasbon-gen.create', compact(
            'departemens',
            'cabangs',
            'releases',
            'previewNoKasbon',
            'invoices'
        ));
    }

    // ========================================
    // STORE HEADER
    // ========================================

    public function storeHeader(Request $request)
    {
        Log::info('=== KasbonGen Store Header START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_dep'     => 'required|exists:a08_md_dep,id_md_dep',
                'id_md_cabang'  => 'required|exists:a09_md_branch,id_md_branch',
                'id_md_release' => 'required|exists:a10_md_release_to,id_md_release',
                'tgl_kasbon'    => 'required|date',
                'tgl_release'   => 'nullable|date',
                'note'          => 'nullable|string',
            ], [
                'id_md_dep.required'     => 'Departemen is required',
                'id_md_cabang.required'  => 'Branch is required',
                'id_md_release.required' => 'Release To is required',
                'tgl_kasbon.required'    => 'Cash Advance Date is required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $idKasbonGen = IdGenerator::generateCaNo('c07_kasbon_gen', 'id_kasbon_gen');
            $lastKasbon  = KasbonGen::orderBy('id', 'desc')->first();
            $newNomor    = $lastKasbon ? $lastKasbon->nomor + 1 : 1;

            $kasbonGen = KasbonGen::create([
                'id_kasbon_gen'  => $idKasbonGen,
                'id_md_dep'      => $request->id_md_dep,
                'id_md_cabang'   => $request->id_md_cabang,
                'id_md_release'  => $request->id_md_release,
                'nomor'          => $newNomor,
                'tgl_kasbon'     => $request->tgl_kasbon,
                'tgl_release'    => $request->tgl_release,
                'note'           => $request->note,
            ]);

            DB::commit();

            $kasbonGen->load(['departemen', 'cabang', 'release']);

            Log::info('KasbonGen Header Created', ['id' => $kasbonGen->id, 'id_kasbon_gen' => $idKasbonGen]);

            return response()->json([
                'success'      => true,
                'message'      => 'Kasbon General header saved successfully',
                'redirect_url' => route('kasbon-gen.edit', $kasbonGen->id),
                'data'         => [
                    'id'             => $kasbonGen->id,
                    'id_kasbon_gen'  => $kasbonGen->id_kasbon_gen,
                    'id_md_dep'      => $kasbonGen->id_md_dep,
                    'id_md_cabang'   => $kasbonGen->id_md_cabang,
                    'id_md_release'  => $kasbonGen->id_md_release,
                    'nomor'          => $kasbonGen->nomor,
                    'tgl_kasbon'     => $kasbonGen->tgl_kasbon,
                    'tgl_release'    => $kasbonGen->tgl_release,
                    'note'           => $kasbonGen->note,
                    'departemen'     => $kasbonGen->departemen,
                    'cabang'         => $kasbonGen->cabang,
                    'release'        => $kasbonGen->release,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonGen Store Header Failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save Kasbon General header',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // UPDATE HEADER
    // ========================================

    public function updateHeader(Request $request, $id)
    {
        Log::info('=== KasbonGen Update Header START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_dep'     => 'required|exists:a08_md_dep,id_md_dep',
                'id_md_cabang'  => 'required|exists:a09_md_branch,id_md_branch',
                'id_md_release' => 'required|exists:a10_md_release_to,id_md_release',
                'tgl_kasbon'    => 'required|date',
                'tgl_release'   => 'nullable|date',
                'note'          => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $kasbonGen = KasbonGen::findOrFail($id);
            $kasbonGen->update([
                'id_md_dep'     => $request->id_md_dep,
                'id_md_cabang'  => $request->id_md_cabang,
                'id_md_release' => $request->id_md_release,
                'tgl_kasbon'    => $request->tgl_kasbon,
                'tgl_release'   => $request->tgl_release,
                'note'          => $request->note,
            ]);

            DB::commit();

            $kasbonGen->load(['departemen', 'cabang', 'release']);

            return response()->json([
                'success' => true,
                'message' => 'Kasbon General header updated successfully',
                'data'    => $kasbonGen
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonGen Update Header Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Kasbon General header',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // STORE ITEM (konsep seperti JO)
    // ========================================

    public function storeItem(Request $request)
    {
        Log::info('=== KasbonGen Store Item START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_kasbon_gen' => 'required|exists:c07_kasbon_gen,id_kasbon_gen',
                'id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
                'nilai_kasbon'  => 'required|numeric|min:0',
            ], [
                'id_kasbon_gen.required' => 'Kasbon General is required',
                'id_md_invoice.required' => 'Invoice item is required',
                'nilai_kasbon.required'  => 'Nominal kasbon is required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $item = KasbonGenItem::create([
                'id_kasbon_gen' => $request->id_kasbon_gen,
                'id_md_invoice' => $request->id_md_invoice,
                'nilai_kasbon'  => (float) $request->nilai_kasbon,
            ]);

            $item->load('invoice');

            DB::commit();

            Log::info('KasbonGen Item Created', ['id' => $item->id_kasbon_gen_item]);

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data'    => [
                    'id'                  => $item->id_kasbon_gen_item,
                    'id_kasbon_gen_item'  => $item->id_kasbon_gen_item,
                    'id_kasbon_gen'       => $item->id_kasbon_gen,
                    'id_md_invoice'       => $item->id_md_invoice,
                    'invoice_ctg'         => $item->invoice ? $item->invoice->invoice_ctg : null,
                    'invoice_typ'         => $item->invoice ? $item->invoice->invoice_typ : null,
                    'nilai_kasbon'        => (float) $item->nilai_kasbon,
                ]
            ], 201);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            Log::error('KasbonGen Store Item Failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // SHOW ITEM
    // ========================================

    public function showItem($id)
    {
        try {
            $item = KasbonGenItem::with('invoice')->where('id_kasbon_gen_item', $id)->firstOrFail();

            return response()->json([
                'success' => true,
                'data'    => [
                    'id_kasbon_gen_item' => $item->id_kasbon_gen_item,
                    'id_kasbon_gen'      => $item->id_kasbon_gen,
                    'id_md_invoice'      => $item->id_md_invoice,
                    'invoice_ctg'        => $item->invoice ? $item->invoice->invoice_ctg : null,
                    'invoice_typ'        => $item->invoice ? $item->invoice->invoice_typ : null,
                    'nilai_kasbon'       => (float) $item->nilai_kasbon,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Item not found: ' . $e->getMessage()], 404);
        }
    }

    // ========================================
    // UPDATE ITEM
    // ========================================

    public function updateItem(Request $request, $id)
    {
        Log::info('=== KasbonGen Update Item START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
                'nilai_kasbon'  => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $item = KasbonGenItem::where('id_kasbon_gen_item', $id)->firstOrFail();
            $item->update([
                'id_md_invoice' => $request->id_md_invoice,
                'nilai_kasbon'  => (float) $request->nilai_kasbon,
            ]);

            $item->refresh()->load('invoice');

            DB::commit();

            Log::info('KasbonGen Item Updated', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data'    => [
                    'id'                 => $item->id_kasbon_gen_item,
                    'id_kasbon_gen_item' => $item->id_kasbon_gen_item,
                    'id_kasbon_gen'      => $item->id_kasbon_gen,
                    'id_md_invoice'      => $item->id_md_invoice,
                    'invoice_ctg'        => $item->invoice ? $item->invoice->invoice_ctg : null,
                    'invoice_typ'        => $item->invoice ? $item->invoice->invoice_typ : null,
                    'nilai_kasbon'       => (float) $item->nilai_kasbon,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonGen Update Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // DESTROY ITEM
    // ========================================

    public function destroyItem($id)
    {
        Log::info('=== KasbonGen Delete Item START ===', ['id' => $id]);

        try {
            DB::beginTransaction();
            $item = KasbonGenItem::where('id_kasbon_gen_item', $id)->firstOrFail();
            $item->delete();
            DB::commit();

            Log::info('KasbonGen Item Deleted', ['id' => $id]);

            return response()->json(['success' => true, 'message' => 'Item deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonGen Delete Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete item', 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // GET ITEMS
    // ========================================

    public function getItems($kasbonGenId)
    {
        try {
            $items = KasbonGenItem::where('id_kasbon_gen', $kasbonGenId)
                ->with('invoice')
                ->orderBy('created_at')
                ->get();

            $formattedItems = $items->map(function ($item) {
                return [
                    'id'                 => $item->id_kasbon_gen_item,
                    'id_kasbon_gen_item' => $item->id_kasbon_gen_item,
                    'id_kasbon_gen'      => $item->id_kasbon_gen,
                    'id_md_invoice'      => $item->id_md_invoice,
                    'invoice_ctg'        => $item->invoice ? $item->invoice->invoice_ctg : 'Unknown',
                    'invoice_typ'        => $item->invoice ? $item->invoice->invoice_typ : 'Unknown',
                    'nilai_kasbon'       => (float) $item->nilai_kasbon,
                ];
            });

            return response()->json(['success' => true, 'data' => $formattedItems], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch items', 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // SHOW
    // ========================================

    public function show(Request $request, $id)
    {
        try {
            $kasbonGen = KasbonGen::with([
                'departemen', 'cabang', 'release', 'items.invoice',
            ])->findOrFail($id);

            $summary = [
                'total_items'        => $kasbonGen->items->count(),
                'total_nilai_kasbon' => $kasbonGen->items->sum('nilai_kasbon'),
            ];

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $kasbonGen, 'summary' => $summary]);
            }

            return view('data.kasbon-gen.show', compact('kasbonGen', 'summary'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kasbon General not found'], 404);
            }
            return back()->with('error', 'Kasbon General not found');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ========================================
    // EDIT
    // ========================================

    public function edit($id)
    {
        $kasbonGen = KasbonGen::with(['items.invoice'])->findOrFail($id);

        $departemens = Departemen::orderBy('nama_dep')->get();
        $cabangs     = Branch::orderBy('nama_branch')->get();
        $releases    = ReleaseTo::orderBy('id_md_release')->get();

        // Invoice category general saja
        $invoices = Invoice::where('jo_ctg', 'general')
            ->orderBy('invoice_ctg')
            ->orderBy('invoice_typ')
            ->get();

        return view('data.kasbon-gen.edit', compact(
            'kasbonGen',
            'departemens',
            'cabangs',
            'releases',
            'invoices'
        ));
    }

    // ========================================
    // STORE (Traditional fallback)
    // ========================================

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_md_dep'                 => 'required|exists:a08_md_dep,id_md_dep',
            'id_md_cabang'              => 'required|exists:a09_md_branch,id_md_branch',
            'id_md_release'             => 'required|exists:a10_md_release_to,id_md_release',
            'tgl_kasbon'                => 'required|date',
            'tgl_release'               => 'nullable|date',
            'note'                      => 'nullable|string',
            'items'                     => 'nullable|array',
            'items.*.id_md_invoice'     => 'required|exists:a04_md_invoice,id_md_invoice',
            'items.*.nilai_kasbon'      => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $idKasbonGen = IdGenerator::generateCaNo('c07_kasbon_gen', 'id_kasbon_gen');
            $lastKasbon  = KasbonGen::orderBy('id', 'desc')->first();
            $newNomor    = $lastKasbon ? $lastKasbon->nomor + 1 : 1;

            $kasbonGen = KasbonGen::create([
                'id_kasbon_gen'  => $idKasbonGen,
                'id_md_dep'      => $request->id_md_dep,
                'id_md_cabang'   => $request->id_md_cabang,
                'id_md_release'  => $request->id_md_release,
                'nomor'          => $newNomor,
                'tgl_kasbon'     => $request->tgl_kasbon,
                'tgl_release'    => $request->tgl_release,
                'note'           => $request->note,
            ]);

            foreach ($request->items ?? [] as $itemData) {
                KasbonGenItem::create([
                    'id_kasbon_gen' => $idKasbonGen,
                    'id_md_invoice' => $itemData['id_md_invoice'],
                    'nilai_kasbon'  => (float) $itemData['nilai_kasbon'],
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Kasbon General successfully added', 'data' => $kasbonGen->load('items')], 201);
            }
            return redirect()->route('kasbon-gen.index')->with('success', 'Kasbon General successfully added');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonGen Store Failed', ['error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ========================================
    // UPDATE (Traditional fallback)
    // ========================================

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_md_dep'             => 'required|exists:a08_md_dep,id_md_dep',
            'id_md_cabang'          => 'required|exists:a09_md_branch,id_md_branch',
            'id_md_release'         => 'required|exists:a10_md_release_to,id_md_release',
            'tgl_kasbon'            => 'required|date',
            'tgl_release'           => 'nullable|date',
            'note'                  => 'nullable|string',
            'items'                 => 'nullable|array',
            'items.*.id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
            'items.*.nilai_kasbon'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $kasbonGen = KasbonGen::findOrFail($id);
            $kasbonGen->update([
                'id_md_dep'     => $request->id_md_dep,
                'id_md_cabang'  => $request->id_md_cabang,
                'id_md_release' => $request->id_md_release,
                'tgl_kasbon'    => $request->tgl_kasbon,
                'tgl_release'   => $request->tgl_release,
                'note'          => $request->note,
            ]);

            foreach ($kasbonGen->items as $oldItem) { $oldItem->delete(); }

            foreach ($request->items ?? [] as $itemData) {
                KasbonGenItem::create([
                    'id_kasbon_gen' => $kasbonGen->id_kasbon_gen,
                    'id_md_invoice' => $itemData['id_md_invoice'],
                    'nilai_kasbon'  => (float) $itemData['nilai_kasbon'],
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Kasbon General successfully updated', 'data' => $kasbonGen->fresh()->load('items')]);
            }
            return redirect()->route('kasbon-gen.index')->with('success', 'Kasbon General successfully updated');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonGen Update Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ========================================
    // DESTROY
    // ========================================

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $kasbonGen  = KasbonGen::findOrFail($id);
            $itemsCount = $kasbonGen->items()->count();

            foreach ($kasbonGen->items as $item) { $item->delete(); }
            $kasbonGen->delete();

            DB::commit();
            Log::info('KasbonGen Deleted', ['id' => $id]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Kasbon General and all related items successfully deleted']);
            }
            return redirect()->route('kasbon-gen.index')->with('success', "Kasbon General deleted successfully along with {$itemsCount} item(s)");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kasbon General not found'], 404);
            }
            return back()->with('error', 'Kasbon General not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonGen Delete Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ========================================
    // GET FOR SELECT
    // ========================================

    public function getForSelect(Request $request)
    {
        try {
            $query = KasbonGen::with(['departemen']);

            if ($request->filled('id_md_dep')) {
                $query->where('id_md_dep', $request->id_md_dep);
            }

            $kasbonGens = $query->orderBy('id', 'desc')->get()->map(function ($kasbon) {
                return [
                    'id'            => $kasbon->id,
                    'id_kasbon_gen' => $kasbon->id_kasbon_gen,
                    'text'          => $kasbon->id_kasbon_gen . ' - ' . ($kasbon->departemen->nama_dep ?? '-'),
                    'departemen'    => $kasbon->departemen ? $kasbon->departemen->nama_dep : null,
                ];
            });

            return response()->json(['success' => true, 'data' => $kasbonGens]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK DELETE
    // ========================================

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:c07_kasbon_gen,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $kasbonGens = KasbonGen::whereIn('id', $request->ids)->get();
            foreach ($kasbonGens as $kasbon) {
                foreach ($kasbon->items as $item) { $item->delete(); }
                $kasbon->delete();
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Kasbon Generals successfully deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}