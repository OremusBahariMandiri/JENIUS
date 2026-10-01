<?php

namespace App\Http\Controllers\Data;

use App\Helpers\IdGenerator;
use App\Http\Controllers\Controller;
use App\Models\Data\KasbonGen;
use App\Models\Data\KasbonGenItem;
use App\Models\Data\LpjGen;
use App\Models\Data\LpjGenItem;
use App\Models\Data\LpjGenKasbon;
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
    // HELPER: recompute LpjGen.amount
    // Dipanggil setiap kali nilai_kasbon berubah/bertambah/berkurang
    // agar kolom "Total CA (IDR)" di LPJ index selalu akurat
    // ========================================

    private function recomputeLpjAmounts(string $idKasbonGen): void
    {
        $lpjKasbons = LpjGenKasbon::where('id_kasbon_gen', $idKasbonGen)->get();

        foreach ($lpjKasbons as $lpjKasbon) {
            $lpj = LpjGen::where('id_lpj_gen', $lpjKasbon->id_lpj_gen)->with('kasbons')->first();
            if (!$lpj) continue;

            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_gen');
            $newAmount = KasbonGenItem::whereIn('id_kasbon_gen', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $newAmount]);

            Log::info('LpjGen amount recomputed', [
                'id_lpj_gen' => $lpj->id_lpj_gen,
                'new_amount' => $newAmount,
            ]);
        }
    }

    // ========================================
    // INDEX
    // ========================================

    public function index(Request $request)
    {
        try {
            $query = KasbonGen::with(['departemen', 'cabang', 'release', 'items.invoice']);

            if ($request->filled('id_md_dep'))        $query->where('id_md_dep', $request->id_md_dep);
            if ($request->filled('id_md_cabang'))     $query->where('id_md_cabang', $request->id_md_cabang);
            if ($request->filled('tgl_kasbon_from'))  $query->where('tgl_kasbon', '>=', $request->tgl_kasbon_from);
            if ($request->filled('tgl_kasbon_to'))    $query->where('tgl_kasbon', '<=', $request->tgl_kasbon_to);
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id_kasbon_gen', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('departemen', fn($q) => $q->where('nama_dep', 'like', "%{$search}%"))
                        ->orWhereHas('cabang', fn($q) => $q->where('nama_branch', 'like', "%{$search}%"));
                });
            }

            $query->orderBy($request->get('sort_by', 'created_at'), $request->get('sort_order', 'desc'));
            $kasbonGens = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $kasbonGens]);
            }

            $departemens    = Departemen::orderBy('nama_dep')->get();
            $cabangs        = Branch::orderBy('nama_branch')->get();
            $releases       = ReleaseTo::orderBy('id_md_release')->get();
            $currentFilters = [
                'id_md_dep'       => $request->get('id_md_dep', ''),
                'id_md_cabang'    => $request->get('id_md_cabang', ''),
                'tgl_kasbon_from' => $request->get('tgl_kasbon_from', ''),
                'tgl_kasbon_to'   => $request->get('tgl_kasbon_to', ''),
            ];

            return view('data.kasbon-gen.index', compact('kasbonGens', 'departemens', 'cabangs', 'releases', 'currentFilters'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
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
        $invoices        = Invoice::where('jo_ctg', 'general')->orderBy('invoice_ctg')->orderBy('invoice_typ')->get();

        return view('data.kasbon-gen.create', compact('departemens', 'cabangs', 'releases', 'previewNoKasbon', 'invoices'));
    }

    // ========================================
    // STORE HEADER
    // ========================================

    public function storeHeader(Request $request)
    {
        Log::info('=== KasbonGen Store Header START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_dep'         => 'required|exists:a08_md_dep,id_md_dep',
                'id_md_cabang'      => 'required|exists:a09_md_branch,id_md_branch',
                'id_md_release'     => 'required|exists:a10_md_release_to,id_md_release',
                'tgl_kasbon'        => 'required|date',
                'tgl_release'       => 'nullable|date',
                'note'              => 'nullable|string',
                'priority'          => 'required|in:high,normal',
                'due_date'          => 'nullable|date_format:Y-m-d\TH:i',
                'ca_release_status' => 'nullable|in:pending,release',
                'ca_release_date'   => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $idKasbonGen = IdGenerator::generateCaNo('c07_kasbon_gen', 'id_kasbon_gen');
            $lastKasbon  = KasbonGen::orderBy('id', 'desc')->first();
            $newNomor    = $lastKasbon ? $lastKasbon->nomor + 1 : 1;

            $kasbonGen = KasbonGen::create([
                'id_kasbon_gen'     => $idKasbonGen,
                'id_md_dep'         => $request->id_md_dep,
                'id_md_cabang'      => $request->id_md_cabang,
                'id_md_release'     => $request->id_md_release,
                'nomor'             => $newNomor,
                'tgl_kasbon'        => $request->tgl_kasbon,
                'tgl_release'       => $request->tgl_release,
                'note'              => $request->note,
                'priority'          => $request->priority ?? 'normal',
                'due_date'          => $request->due_date ?: null,
                'ca_release_status' => $request->ca_release_status ?: null,
                'ca_release_date'   => $request->ca_release_date ?: null,
            ]);

            DB::commit();
            $kasbonGen->load(['departemen', 'cabang', 'release']);

            return response()->json([
                'success'      => true,
                'message'      => 'Kasbon General header saved successfully',
                'redirect_url' => route('kasbon-gen.edit', $kasbonGen->id),
                'data'         => ['id' => $kasbonGen->id, 'id_kasbon_gen' => $kasbonGen->id_kasbon_gen],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to save header', 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // UPDATE HEADER
    // ========================================

    public function updateHeader(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_md_dep'         => 'required|exists:a08_md_dep,id_md_dep',
                'id_md_cabang'      => 'required|exists:a09_md_branch,id_md_branch',
                'id_md_release'     => 'required|exists:a10_md_release_to,id_md_release',
                'tgl_kasbon'        => 'required|date',
                'tgl_release'       => 'nullable|date',
                'note'              => 'nullable|string',
                'priority'          => 'required|in:high,normal',
                'due_date'          => 'nullable|date_format:Y-m-d\TH:i',
                'ca_release_status' => 'nullable|in:pending,release',
                'ca_release_date'   => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();
            $kasbonGen = KasbonGen::findOrFail($id);
            $kasbonGen->update([
                'id_md_dep'         => $request->id_md_dep,
                'id_md_cabang'      => $request->id_md_cabang,
                'id_md_release'     => $request->id_md_release,
                'tgl_kasbon'        => $request->tgl_kasbon,
                'tgl_release'       => $request->tgl_release,
                'note'              => $request->note,
                'priority'          => $request->priority,
                'due_date'          => $request->due_date ?: null,
                'ca_release_status' => $request->ca_release_status ?: null,
                'ca_release_date'   => $request->ca_release_date ?: null,
            ]);
            DB::commit();
            $kasbonGen->load(['departemen', 'cabang', 'release']);

            return response()->json(['success' => true, 'message' => 'Header updated successfully', 'data' => $kasbonGen], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update header', 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // STORE ITEM
    // Setelah tambah item, recompute amount di LPJ terkait
    // ========================================

    public function storeItem(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_kasbon_gen' => 'required|exists:c07_kasbon_gen,id_kasbon_gen',
                'id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
                'nilai_kasbon'  => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();
            $item = KasbonGenItem::create([
                'id_kasbon_gen' => $request->id_kasbon_gen,
                'id_md_invoice' => $request->id_md_invoice,
                'nilai_kasbon'  => (float) $request->nilai_kasbon,
            ]);
            $item->load('invoice');

            // Recompute LPJ amounts setelah item baru ditambahkan
            $this->recomputeLpjAmounts($request->id_kasbon_gen);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data'    => [
                    'id'                 => $item->id_kasbon_gen_item,
                    'id_kasbon_gen_item' => $item->id_kasbon_gen_item,
                    'id_kasbon_gen'      => $item->id_kasbon_gen,
                    'id_md_invoice'      => $item->id_md_invoice,
                    'invoice_ctg'        => $item->invoice?->invoice_ctg,
                    'invoice_typ'        => $item->invoice?->invoice_typ,
                    'nilai_kasbon'       => (float) $item->nilai_kasbon,
                    'origin_lpj_gen'     => null,
                ]
            ], 201);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to add item', 'error' => $e->getMessage()], 500);
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
                    'invoice_ctg'        => $item->invoice?->invoice_ctg,
                    'invoice_typ'        => $item->invoice?->invoice_typ,
                    'nilai_kasbon'       => (float) $item->nilai_kasbon,
                    'origin_lpj_gen'     => $item->origin_lpj_gen,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Item not found: ' . $e->getMessage()], 404);
        }
    }

    // ========================================
    // UPDATE ITEM
    // Setelah ubah nilai_kasbon, recompute amount di LPJ terkait
    // ========================================

    public function updateItem(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
                'nilai_kasbon'  => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();
            $item = KasbonGenItem::where('id_kasbon_gen_item', $id)->firstOrFail();
            $item->update([
                'id_md_invoice' => $request->id_md_invoice,
                'nilai_kasbon'  => (float) $request->nilai_kasbon,
            ]);
            $item->refresh()->load('invoice');

            // Recompute LPJ amounts karena nilai_kasbon berubah
            $this->recomputeLpjAmounts($item->id_kasbon_gen);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data'    => [
                    'id'                 => $item->id_kasbon_gen_item,
                    'id_kasbon_gen_item' => $item->id_kasbon_gen_item,
                    'id_kasbon_gen'      => $item->id_kasbon_gen,
                    'id_md_invoice'      => $item->id_md_invoice,
                    'invoice_ctg'        => $item->invoice?->invoice_ctg,
                    'invoice_typ'        => $item->invoice?->invoice_typ,
                    'nilai_kasbon'       => (float) $item->nilai_kasbon,
                    'origin_lpj_gen'     => $item->origin_lpj_gen,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update item', 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // DESTROY ITEM
    // Blok jika: (1) berasal dari LPJ, atau (2) sedang digunakan di LPJ Gen Item
    // ========================================

    public function destroyItem($id)
    {
        Log::info('=== KasbonGen Delete Item START ===', ['id' => $id]);

        try {
            DB::beginTransaction();

            $item = KasbonGenItem::where('id_kasbon_gen_item', $id)->firstOrFail();

            // ── BLOK 1: Item berasal dari LPJ — harus pakai destroyItemFromLpj ──
            if (!empty($item->origin_lpj_gen)) {
                DB::rollBack();
                return response()->json([
                    'success'        => false,
                    'blocked_by_lpj' => true,
                    'message'        => 'This item was added from an LPJ page and cannot be deleted here. Please remove it from the LPJ first.',
                    'lpj_id'         => $item->origin_lpj_gen,
                ], 422);
            }

            // ── BLOK 2: Item sedang digunakan di LPJ Gen (ada FK di d12_lpj_gen_item) ──
            $lpjUsage = LpjGenItem::where('id_kasbon_gen_item', $item->id_kasbon_gen_item)->first();
            if ($lpjUsage) {
                DB::rollBack();
                return response()->json([
                    'success'        => false,
                    'blocked_by_lpj' => true,
                    'message'        => 'Cannot delete: this item is currently being used in an LPJ (' . $lpjUsage->id_lpj_gen . '). Please remove the LPJ entry first.',
                    'lpj_id'         => $lpjUsage->id_lpj_gen,
                ], 422);
            }

            $idKasbonGen = $item->id_kasbon_gen;
            $item->delete();

            // Recompute LPJ amounts setelah item dihapus
            $this->recomputeLpjAmounts($idKasbonGen);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Item deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete item', 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // DESTROY ITEM FROM LPJ (hapus kasbon item + lpj item sekaligus)
    // ========================================

    public function destroyItemFromLpj($id)
    {
        Log::info('=== KasbonGen Delete Item From LPJ START ===', ['id' => $id]);

        try {
            DB::beginTransaction();

            $kasbonItem = KasbonGenItem::where('id_kasbon_gen_item', $id)->firstOrFail();

            if (empty($kasbonItem->origin_lpj_gen)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This item is not from an LPJ. Use the regular delete instead.',
                ], 422);
            }

            $lpjGenId    = $kasbonItem->origin_lpj_gen;
            $idKasbonGen = $kasbonItem->id_kasbon_gen;

            // 1. Hapus LPJ Gen Item yang terkait kasbon item ini
            $lpjItemsDeleted = LpjGenItem::where('id_kasbon_gen_item', $kasbonItem->id_kasbon_gen_item)
                ->where('id_lpj_gen', $lpjGenId)
                ->get();

            foreach ($lpjItemsDeleted as $lpjItem) {
                Log::info('Deleting LpjGenItem', ['id_lpj_gen_item' => $lpjItem->id_lpj_gen_item ?? $lpjItem->id]);
                $lpjItem->delete();
            }

            // 2. Hapus kasbon gen item
            $kasbonItem->delete();

            // 3. Recompute LPJ amounts
            $this->recomputeLpjAmounts($idKasbonGen);

            DB::commit();

            Log::info('KasbonGen Item From LPJ Deleted', [
                'id_kasbon_gen_item' => $id,
                'lpj_items_deleted'  => $lpjItemsDeleted->count(),
            ]);

            return response()->json([
                'success'           => true,
                'message'           => 'Item and its LPJ entry deleted successfully',
                'lpj_items_deleted' => $lpjItemsDeleted->count(),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonGen Delete Item From LPJ Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete item', 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK DESTROY ITEMS
    // ========================================

    public function bulkDestroyItems(Request $request)
    {
        Log::info('=== KasbonGen Bulk Destroy Items START ===', ['data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $deleted          = 0;
            $lpjDeleted       = 0;
            $blocked          = [];
            $affectedKasbons  = [];

            foreach ($request->ids as $itemId) {
                $kasbonItem = KasbonGenItem::where('id_kasbon_gen_item', $itemId)->first();
                if (!$kasbonItem) continue;

                // Cek apakah item sedang digunakan di LPJ
                $lpjUsage = LpjGenItem::where('id_kasbon_gen_item', $kasbonItem->id_kasbon_gen_item)->first();
                if ($lpjUsage) {
                    $blocked[] = $kasbonItem->id_kasbon_gen_item;
                    continue;
                }

                // Track kasbon yang terdampak untuk recompute
                $affectedKasbons[] = $kasbonItem->id_kasbon_gen;

                // Jika dari LPJ, hapus juga lpj gen item-nya
                if (!empty($kasbonItem->origin_lpj_gen)) {
                    $lpjItems = LpjGenItem::where('id_kasbon_gen_item', $kasbonItem->id_kasbon_gen_item)
                        ->where('id_lpj_gen', $kasbonItem->origin_lpj_gen)
                        ->get();

                    foreach ($lpjItems as $lpjItem) {
                        $lpjItem->delete();
                        $lpjDeleted++;
                    }
                }

                $kasbonItem->delete();
                $deleted++;
            }

            // Recompute LPJ amounts untuk semua kasbon yang terdampak
            foreach (array_unique($affectedKasbons) as $idKasbonGen) {
                $this->recomputeLpjAmounts($idKasbonGen);
            }

            DB::commit();

            Log::info('KasbonGen Bulk Destroy Items Done', [
                'kasbon_deleted' => $deleted,
                'lpj_deleted'    => $lpjDeleted,
                'blocked'        => count($blocked),
            ]);

            $message = "{$deleted} item(s) deleted";
            if ($lpjDeleted > 0)     $message .= ", including {$lpjDeleted} LPJ entry(ies)";
            if (count($blocked) > 0) $message .= ". " . count($blocked) . " item(s) skipped (currently used in LPJ)";

            return response()->json([
                'success'     => true,
                'message'     => $message,
                'deleted'     => $deleted,
                'lpj_deleted' => $lpjDeleted,
                'blocked'     => $blocked,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonGen Bulk Destroy Items Failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete items', 'error' => $e->getMessage()], 500);
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

            $formattedItems = $items->map(fn($item) => [
                'id'                 => $item->id_kasbon_gen_item,
                'id_kasbon_gen_item' => $item->id_kasbon_gen_item,
                'id_kasbon_gen'      => $item->id_kasbon_gen,
                'id_md_invoice'      => $item->id_md_invoice,
                'invoice_ctg'        => $item->invoice?->invoice_ctg ?? 'Unknown',
                'invoice_typ'        => $item->invoice?->invoice_typ ?? 'Unknown',
                'nilai_kasbon'       => (float) $item->nilai_kasbon,
                'origin_lpj_gen'     => $item->origin_lpj_gen,
                'in_use_by_lpj'      => LpjGenItem::where('id_kasbon_gen_item', $item->id_kasbon_gen_item)->exists(),
            ]);

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
            $kasbonGen = KasbonGen::with(['departemen', 'cabang', 'release', 'items.invoice'])->findOrFail($id);
            $summary   = [
                'total_items'        => $kasbonGen->items->count(),
                'total_nilai_kasbon' => $kasbonGen->items->sum('nilai_kasbon'),
            ];

            if ($request->expectsJson()) return response()->json(['success' => true, 'data' => $kasbonGen, 'summary' => $summary]);
            return view('data.kasbon-gen.show', compact('kasbonGen', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ========================================
    // EDIT
    // ========================================

    public function edit($id)
    {
        $kasbonGen   = KasbonGen::with(['items.invoice'])->findOrFail($id);
        $departemens = Departemen::orderBy('nama_dep')->get();
        $cabangs     = Branch::orderBy('nama_branch')->get();
        $releases    = ReleaseTo::orderBy('id_md_release')->get();
        $invoices    = Invoice::where('jo_ctg', 'general')->orderBy('invoice_ctg')->orderBy('invoice_typ')->get();

        return view('data.kasbon-gen.edit', compact('kasbonGen', 'departemens', 'cabangs', 'releases', 'invoices'));
    }

    // ========================================
    // STORE / UPDATE (traditional fallback)
    // ========================================

    public function store(Request $request)
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
            'priority'              => 'required|in:urgent,high,normal',
            'due_date'              => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
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
                'priority'       => $request->priority ?? 'normal',
                'due_date'       => $request->due_date ?: null,
            ]);

            foreach ($request->items ?? [] as $itemData) {
                KasbonGenItem::create([
                    'id_kasbon_gen' => $idKasbonGen,
                    'id_md_invoice' => $itemData['id_md_invoice'],
                    'nilai_kasbon'  => (float) $itemData['nilai_kasbon'],
                ]);
            }

            DB::commit();
            if ($request->expectsJson()) return response()->json(['success' => true, 'message' => 'Kasbon General successfully added', 'data' => $kasbonGen->load('items')], 201);
            return redirect()->route('kasbon-gen.index')->with('success', 'Kasbon General successfully added');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

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
            'priority'              => 'required|in:urgent,high,normal',
            'due_date'              => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
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
                'priority'      => $request->priority,
                'due_date'      => $request->due_date ?: null,
            ]);

            foreach ($kasbonGen->items as $oldItem) {
                $oldItem->delete();
            }

            foreach ($request->items ?? [] as $itemData) {
                KasbonGenItem::create([
                    'id_kasbon_gen' => $kasbonGen->id_kasbon_gen,
                    'id_md_invoice' => $itemData['id_md_invoice'],
                    'nilai_kasbon'  => (float) $itemData['nilai_kasbon'],
                ]);
            }

            // Recompute LPJ amounts
            $this->recomputeLpjAmounts($kasbonGen->id_kasbon_gen);

            DB::commit();
            if ($request->expectsJson()) return response()->json(['success' => true, 'message' => 'Kasbon General successfully updated', 'data' => $kasbonGen->fresh()->load('items')]);
            return redirect()->route('kasbon-gen.index')->with('success', 'Kasbon General successfully updated');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ========================================
    // DESTROY (kasbon header)
    // Blok jika ada item yang sedang digunakan di LPJ
    // ========================================

    public function destroy(Request $request, $id)
    {
        Log::info('=== KasbonGen Delete START ===', ['id' => $id]);

        DB::beginTransaction();
        try {
            $kasbonGen = KasbonGen::with('items')->findOrFail($id);

            // ── CEK: apakah ada item yang sedang digunakan di LPJ Gen Item ──
            $kasbonItemIds = $kasbonGen->items->pluck('id_kasbon_gen_item');
            $lpjUsageCount = LpjGenItem::whereIn('id_kasbon_gen_item', $kasbonItemIds)->count();

            if ($lpjUsageCount > 0) {
                DB::rollBack();
                $msg = "Cannot delete: {$lpjUsageCount} item(s) in this Cash Advance are currently being used in an LPJ. Please remove the related LPJ entries first.";
                Log::warning('KasbonGen Delete Blocked by LPJ', ['id' => $id, 'lpj_usage_count' => $lpjUsageCount]);
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->with('error', $msg);
            }

            $idKasbonGen = $kasbonGen->id_kasbon_gen;
            $itemsCount  = $kasbonGen->items()->count();

            foreach ($kasbonGen->items as $item) {
                if (!empty($item->origin_lpj_gen)) {
                    LpjGenItem::where('id_kasbon_gen_item', $item->id_kasbon_gen_item)
                        ->where('id_lpj_gen', $item->origin_lpj_gen)
                        ->delete();
                }
                $item->delete();
            }

            // Recompute LPJ amounts sebelum hapus header
            // (kasbon items sudah terhapus, jadi amount akan 0 untuk LPJ ini)
            $this->recomputeLpjAmounts($idKasbonGen);

            $kasbonGen->delete();
            DB::commit();

            Log::info('KasbonGen Deleted', ['id' => $id, 'deleted_items_count' => $itemsCount]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Kasbon General and all related items successfully deleted']);
            }
            return redirect()->route('kasbon-gen.index')->with('success', "Kasbon General deleted successfully along with {$itemsCount} item(s)");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => 'Kasbon General not found'], 404);
            return back()->with('error', 'Kasbon General not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonGen Delete Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
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
            if ($request->filled('id_md_dep')) $query->where('id_md_dep', $request->id_md_dep);

            $kasbonGens = $query->orderBy('id', 'desc')->get()->map(fn($kasbon) => [
                'id'            => $kasbon->id,
                'id_kasbon_gen' => $kasbon->id_kasbon_gen,
                'text'          => $kasbon->id_kasbon_gen . ' - ' . ($kasbon->departemen?->nama_dep ?? '-'),
                'departemen'    => $kasbon->departemen?->nama_dep,
            ]);

            return response()->json(['success' => true, 'data' => $kasbonGens]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK DELETE (kasbon headers)
    // ========================================

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:c07_kasbon_gen,id',
        ]);

        if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            $blocked         = [];
            $deleted         = 0;
            $affectedKasbons = [];

            $kasbonGens = KasbonGen::with('items')->whereIn('id', $request->ids)->get();

            foreach ($kasbonGens as $kasbon) {
                $kasbonItemIds = $kasbon->items->pluck('id_kasbon_gen_item');
                $lpjUsageCount = LpjGenItem::whereIn('id_kasbon_gen_item', $kasbonItemIds)->count();

                if ($lpjUsageCount > 0) {
                    $blocked[] = $kasbon->id_kasbon_gen;
                    continue;
                }

                $affectedKasbons[] = $kasbon->id_kasbon_gen;

                foreach ($kasbon->items as $item) {
                    if (!empty($item->origin_lpj_gen)) {
                        LpjGenItem::where('id_kasbon_gen_item', $item->id_kasbon_gen_item)
                            ->where('id_lpj_gen', $item->origin_lpj_gen)
                            ->delete();
                    }
                    $item->delete();
                }
                $kasbon->delete();
                $deleted++;
            }

            // Recompute LPJ amounts untuk kasbon yang dihapus
            foreach ($affectedKasbons as $idKasbonGen) {
                $this->recomputeLpjAmounts($idKasbonGen);
            }

            DB::commit();

            $message = "{$deleted} Kasbon General(s) successfully deleted";
            if (count($blocked) > 0) {
                $message .= '. ' . count($blocked) . ' skipped (items in use by LPJ): ' . implode(', ', $blocked);
            }

            return response()->json(['success' => true, 'message' => $message, 'blocked' => $blocked]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // EXPORT PDF
    // ========================================

    public function exportPdf($id)
    {
        try {
            $kasbonGen = KasbonGen::with(['departemen', 'cabang', 'release', 'items.invoice'])->findOrFail($id);
            $totalCA   = $kasbonGen->items->sum('nilai_kasbon');
            $terbilang = $this->toTerbilang((int) round($totalCA)) . ' Rupiah';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('data.kasbon-gen.pdf', compact('kasbonGen', 'terbilang'))
                ->setPaper('a4', 'portrait')
                ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false, 'defaultFont' => 'Arial', 'dpi' => 150]);

            return $pdf->stream('Kasbon-General-' . str_replace(['/', '\\'], '-', $kasbonGen->id_kasbon_gen ?? $id) . '.pdf');
        } catch (\Exception $e) {
            Log::error('Kasbon General Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $format     = $request->get('format', 'excel');
        $kasbonGens = $this->buildExportQuery($request)->get();

        if ($format === 'pdf') {
            $filters = $this->activeFilters($request);
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('data.kasbon-gen.export_pdf', compact('kasbonGens', 'filters'))
                    ->setPaper('a4', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false, 'defaultFont' => 'Arial', 'dpi' => 150]);
                return $pdf->stream('kasbon_general_' . date('Ymd_His') . '.pdf');
            }
            $html = view('data.kasbon-gen.export_pdf', compact('kasbonGens', 'filters'))->render();
            return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
        }

        return (new \App\Exports\Data\KasbonGenExport($kasbonGens))->download();
    }

    private function buildExportQuery(Request $request)
    {
        $query = KasbonGen::with(['departemen', 'cabang', 'release', 'items']);
        if ($request->filled('id_md_dep'))       $query->where('id_md_dep', $request->id_md_dep);
        if ($request->filled('id_md_cabang'))    $query->where('id_md_cabang', $request->id_md_cabang);
        if ($request->filled('tgl_kasbon_from')) $query->where('tgl_kasbon', '>=', $request->tgl_kasbon_from);
        if ($request->filled('tgl_kasbon_to'))   $query->where('tgl_kasbon', '<=', $request->tgl_kasbon_to);
        return $query->orderBy('tgl_kasbon', 'desc')->orderBy('id', 'desc');
    }

    private function activeFilters(Request $request): array
    {
        $filters = [];
        if ($request->filled('id_md_dep')) {
            $dep = \App\Models\Master\Departemen::find($request->id_md_dep);
            $filters['dep_label'] = $dep?->nama_dep ?? $request->id_md_dep;
        }
        if ($request->filled('id_md_cabang')) {
            $c = \App\Models\Master\Branch::find($request->id_md_cabang);
            $filters['cabang_label'] = $c?->nama_branch ?? $request->id_md_cabang;
        }
        if ($request->filled('tgl_kasbon_from')) $filters['tgl_kasbon_from'] = $request->tgl_kasbon_from;
        if ($request->filled('tgl_kasbon_to'))   $filters['tgl_kasbon_to']   = $request->tgl_kasbon_to;
        return $filters;
    }

    private function toTerbilang(int $number): string
    {
        if ($number < 0) return 'minus ' . $this->toTerbilang(abs($number));
        $words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        if ($number === 0)               return 'Nol';
        if ($number < 12)                return $words[$number];
        if ($number < 20)                return $this->toTerbilang($number - 10) . ' Belas';
        if ($number < 100)               return $words[(int)($number / 10)] . ' Puluh' . ($number % 10 ? ' ' . $this->toTerbilang($number % 10) : '');
        if ($number < 200)               return 'Seratus' . ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 1000)              return $words[(int)($number / 100)] . ' Ratus' . ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 2000)              return 'Seribu' . ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000)         return $this->toTerbilang((int)($number / 1000)) . ' Ribu' . ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000_000)     return $this->toTerbilang((int)($number / 1_000_000)) . ' Juta' . ($number % 1_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000) : '');
        if ($number < 1_000_000_000_000) return $this->toTerbilang((int)($number / 1_000_000_000)) . ' Miliar' . ($number % 1_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000) : '');
        return $this->toTerbilang((int)($number / 1_000_000_000_000)) . ' Triliun' . ($number % 1_000_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000_000) : '');
    }
}