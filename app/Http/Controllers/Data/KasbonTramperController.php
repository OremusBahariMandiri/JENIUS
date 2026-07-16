<?php

namespace App\Http\Controllers\Data;

use App\Helpers\IdGenerator;
use App\Http\Controllers\Controller;
use App\Models\Data\KasbonTramper;
use App\Models\Data\KasbonTramperItem;
use App\Models\Data\JoTramper;
use App\Models\Data\JoTramperItem;
use App\Models\Master\Departemen;
use App\Models\Master\Branch;
use App\Models\Master\ReleaseTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KasbonTramperController extends Controller
{
    // ========================================
    // INDEX
    // ========================================

    public function index(Request $request)
    {
        try {
            $query = KasbonTramper::with(['joTramper', 'departemen', 'cabang', 'release', 'items']);

            if ($request->filled('id_jo_tram')) {
                $query->where('id_jo_tram', $request->id_jo_tram);
            }
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
                    $q->where('id_kasbon_tram', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('joTramper', function ($q) use ($search) {
                            $q->where('no_jo_tram', 'like', "%{$search}%");
                        })
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

            $kasbonTrampers = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $kasbonTrampers]);
            }

            $joTrampers  = JoTramper::orderBy('no_jo_tram')->get();
            $departemens = Departemen::orderBy('nama_dep')->get();
            $cabangs     = Branch::orderBy('nama_branch')->get();
            $releases    = ReleaseTo::orderBy('id_md_release')->get();

            $currentFilters = [
                'id_jo_tram'      => $request->get('id_jo_tram', ''),
                'id_md_dep'       => $request->get('id_md_dep', ''),
                'id_md_cabang'    => $request->get('id_md_cabang', ''),
                'tgl_kasbon_from' => $request->get('tgl_kasbon_from', ''),
                'tgl_kasbon_to'   => $request->get('tgl_kasbon_to', ''),
            ];

            return view('data.kasbon-tramper.index', compact(
                'kasbonTrampers',
                'joTrampers',
                'departemens',
                'cabangs',
                'releases',
                'currentFilters'
            ));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error retrieving kasbon trampers: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving kasbon trampers: ' . $e->getMessage());
        }
    }

    // ========================================
    // CREATE
    // ========================================

    public function create()
    {
        $joTrampers      = JoTramper::with(['customer', 'port'])->orderBy('no_jo_tram')->get();
        $departemens     = Departemen::orderBy('nama_dep')->get();
        $cabangs         = Branch::orderBy('nama_branch')->get();
        $releases        = ReleaseTo::orderBy('id_md_release')->get();
        $previewNoKasbon = IdGenerator::generateCaNo('c03_kasbon_tram', 'id_kasbon_tram');

        return view('data.kasbon-tramper.create', compact(
            'joTrampers',
            'departemens',
            'cabangs',
            'releases',
            'previewNoKasbon'
        ));
    }

    // ========================================
    // STORE HEADER (Realtime Auto-save AJAX)
    // ========================================

    public function storeHeader(Request $request)
    {
        Log::info('=== KasbonTramper Store Header START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_jo_tram'    => 'required|exists:b03_jo_tram,id_jo_tram',
                'id_md_dep'     => 'required|exists:a08_md_dep,id_md_dep',
                'id_md_cabang'  => 'required|exists:a09_md_branch,id_md_branch',
                'id_md_release' => 'required|exists:a10_md_release_to,id_md_release',
                'tgl_kasbon'    => 'required|date',
                'tgl_release'   => 'nullable|date',
                'note'          => 'nullable|string',
            ], [
                'id_jo_tram.required'    => 'Job Order Tramper is required',
                'id_jo_tram.exists'      => 'Selected Job Order Tramper does not exist',
                'id_md_dep.required'     => 'Departemen is required',
                'id_md_cabang.required'  => 'Branch is required',
                'id_md_release.required' => 'Release To is required',
                'tgl_kasbon.required'    => 'Cash Advance Date is required',
            ]);

            if ($validator->fails()) {
                Log::error('KasbonTramper Header Validation Failed', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $idKasbonTram = IdGenerator::generateCaNo('c03_kasbon_tram', 'id_kasbon_tram');

            $lastKasbon = KasbonTramper::orderBy('id', 'desc')->first();
            $newNomor   = $lastKasbon ? $lastKasbon->nomor + 1 : 1;

            $kasbonTramper = KasbonTramper::create([
                'id_kasbon_tram' => $idKasbonTram,
                'id_jo_tram'     => $request->id_jo_tram,
                'id_md_dep'      => $request->id_md_dep,
                'id_md_cabang'   => $request->id_md_cabang,
                'id_md_release'  => $request->id_md_release,
                'nomor'          => $newNomor,
                'tgl_kasbon'     => $request->tgl_kasbon,
                'tgl_release'    => $request->tgl_release,
                'note'           => $request->note,
            ]);

            DB::commit();

            $kasbonTramper->load(['joTramper', 'departemen', 'cabang', 'release']);

            Log::info('KasbonTramper Header Created', ['id' => $kasbonTramper->id, 'id_kasbon_tram' => $idKasbonTram]);

            return response()->json([
                'success'      => true,
                'message'      => 'Kasbon Tramper header saved successfully',
                'redirect_url' => route('kasbon-tramper.edit', $kasbonTramper->id),
                'data'         => [
                    'id'             => $kasbonTramper->id,
                    'id_kasbon_tram' => $kasbonTramper->id_kasbon_tram,
                    'id_jo_tram'     => $kasbonTramper->id_jo_tram,
                    'id_md_dep'      => $kasbonTramper->id_md_dep,
                    'id_md_cabang'   => $kasbonTramper->id_md_cabang,
                    'id_md_release'  => $kasbonTramper->id_md_release,
                    'nomor'          => $kasbonTramper->nomor,
                    'tgl_kasbon'     => $kasbonTramper->tgl_kasbon,
                    'tgl_release'    => $kasbonTramper->tgl_release,
                    'note'           => $kasbonTramper->note,
                    'joTramper'      => $kasbonTramper->joTramper,
                    'departemen'     => $kasbonTramper->departemen,
                    'cabang'         => $kasbonTramper->cabang,
                    'release'        => $kasbonTramper->release,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonTramper Store Header Failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save Kasbon Tramper header',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // UPDATE HEADER (Realtime Auto-save AJAX)
    // ========================================

    public function updateHeader(Request $request, $id)
    {
        Log::info('=== KasbonTramper Update Header START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_jo_tram'    => 'required|exists:b03_jo_tram,id_jo_tram',
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

            $kasbonTramper = KasbonTramper::findOrFail($id);
            $kasbonTramper->update([
                'id_jo_tram'    => $request->id_jo_tram,
                'id_md_dep'     => $request->id_md_dep,
                'id_md_cabang'  => $request->id_md_cabang,
                'id_md_release' => $request->id_md_release,
                'tgl_kasbon'    => $request->tgl_kasbon,
                'tgl_release'   => $request->tgl_release,
                'note'          => $request->note,
            ]);

            DB::commit();

            $kasbonTramper->load(['joTramper', 'departemen', 'cabang', 'release']);

            Log::info('KasbonTramper Header Updated', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Kasbon Tramper header updated successfully',
                'data'    => $kasbonTramper
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonTramper Update Header Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Kasbon Tramper header',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // STORE ITEM (Realtime Auto-save AJAX)
    // ========================================

    public function storeItem(Request $request)
    {
        Log::info('=== KasbonTramper Store Item START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_kasbon_tram'  => 'required|exists:c03_kasbon_tram,id_kasbon_tram',
                'id_jo_tram_item' => 'required|exists:b04_jo_tram_item,id_jo_tram_item',
                'nilai_kasbon'    => 'required|numeric|min:0',
            ], [
                'id_kasbon_tram.required'  => 'Kasbon Tramper is required',
                'id_jo_tram_item.required' => 'Job Order Tramper Item is required',
                'nilai_kasbon.required'    => 'Advance Number Amount is required',
            ]);

            if ($validator->fails()) {
                Log::error('KasbonTramper Item Validation Failed', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                $maxId = DB::table('c04_kasbon_tram_item')
                    ->lockForUpdate()
                    ->max('id_kasbon_tram_item');

                $newIdKasbonTramItem = $maxId ? (string)((int)$maxId + 1) : '1';

                // Ambil nilai HPP dari JO Tramper Item
                $joTramItem = JoTramperItem::find($request->id_jo_tram_item);
                $nilaiHpp   = $joTramItem ? (float)$joTramItem->hpp_ops : 0;

                $nilaiKasbon = (float)$request->nilai_kasbon;
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                $item = KasbonTramperItem::create([
                    'id_kasbon_tram_item' => $newIdKasbonTramItem,
                    'id_kasbon_tram'      => $request->id_kasbon_tram,
                    'id_jo_tram_item'     => $request->id_jo_tram_item,
                    'nilai_hpp_tram_item' => $nilaiHpp,
                    'nilai_kasbon'        => $nilaiKasbon,
                    'total_kasbon'        => $totalKasbon,
                ]);

                $item->load('joTramperItem');

                DB::commit();

                Log::info('KasbonTramper Item Created', ['id' => $item->id, 'id_kasbon_tram_item' => $item->id_kasbon_tram_item]);

                return response()->json([
                    'success' => true,
                    'message' => 'Item added successfully',
                    'data'    => [
                        'id'                  => $item->id,
                        'id_kasbon_tram_item' => $item->id_kasbon_tram_item,
                        'id_kasbon_tram'      => $item->id_kasbon_tram,
                        'id_jo_tram_item'     => $item->id_jo_tram_item,
                        'nilai_hpp_tram_item' => (float)$item->nilai_hpp_tram_item,
                        'nilai_kasbon'        => (float)$item->nilai_kasbon,
                        'total_kasbon'        => (float)$item->total_kasbon,
                        'jo_tram_item'        => $item->joTramperItem,
                    ]
                ], 201);
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();

                if ($e->getCode() === '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    Log::error('Duplicate Key - Retrying...', ['error' => $e->getMessage()]);
                    sleep(1);
                    if (!$request->has('_retry')) {
                        $request->merge(['_retry' => true]);
                        return $this->storeItem($request);
                    }
                }

                throw $e;
            }
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('KasbonTramper Store Item Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add item',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // UPDATE ITEM (Realtime Auto-save AJAX)
    // ========================================

    public function updateItem(Request $request, $id)
    {
        Log::info('=== KasbonTramper Update Item START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_jo_tram_item' => 'required|exists:b04_jo_tram_item,id_jo_tram_item',
                'nilai_kasbon'    => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $item = KasbonTramperItem::findOrFail($id);

            $joTramItem = JoTramperItem::find($request->id_jo_tram_item);
            $nilaiHpp   = $joTramItem ? (float)$joTramItem->hpp_ops : (float)$item->nilai_hpp_tram_item;

            $nilaiKasbon = (float)$request->nilai_kasbon;
            $totalKasbon = $nilaiHpp - $nilaiKasbon;

            $item->update([
                'id_jo_tram_item'     => $request->id_jo_tram_item,
                'nilai_hpp_tram_item' => $nilaiHpp,
                'nilai_kasbon'        => $nilaiKasbon,
                'total_kasbon'        => $totalKasbon,
            ]);

            $item->refresh();
            $item->load('joTramperItem');

            DB::commit();

            Log::info('KasbonTramper Item Updated', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data'    => [
                    'id'                  => $item->id,
                    'id_kasbon_tram_item' => $item->id_kasbon_tram_item,
                    'id_kasbon_tram'      => $item->id_kasbon_tram,
                    'id_jo_tram_item'     => $item->id_jo_tram_item,
                    'nilai_hpp_tram_item' => (float)$item->nilai_hpp_tram_item,
                    'nilai_kasbon'        => (float)$item->nilai_kasbon,
                    'total_kasbon'        => (float)$item->total_kasbon,
                    'jo_tram_item'        => $item->joTramperItem,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonTramper Update Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // DESTROY ITEM (Realtime AJAX)
    // ========================================

    public function destroyItem($id)
    {
        Log::info('=== KasbonTramper Delete Item START ===', ['id' => $id]);

        try {
            DB::beginTransaction();

            $item = KasbonTramperItem::findOrFail($id);
            $item->delete();

            DB::commit();

            Log::info('KasbonTramper Item Deleted', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonTramper Delete Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // GET ITEMS (Load existing data)
    // ========================================

    public function getItems($kasbonTramperId)
    {
        Log::info('=== KasbonTramper Get Items START ===', ['kasbon_tramper_id' => $kasbonTramperId]);

        try {
            $items = KasbonTramperItem::where('id_kasbon_tram', $kasbonTramperId)
                ->with('joTramperItem')
                ->orderBy('created_at')
                ->get();

            $formattedItems = $items->map(function ($item) {
                return [
                    'id'                  => $item->id,
                    'id_kasbon_tram_item' => $item->id_kasbon_tram_item,
                    'id_kasbon_tram'      => $item->id_kasbon_tram,
                    'id_jo_tram_item'     => $item->id_jo_tram_item,
                    'nilai_hpp_tram_item' => (float)$item->nilai_hpp_tram_item,
                    'nilai_kasbon'        => (float)$item->nilai_kasbon,
                    'total_kasbon'        => (float)$item->total_kasbon,
                    'jo_tram_item'        => $item->joTramperItem,
                ];
            });

            Log::info('KasbonTramper Items Retrieved', ['count' => $items->count()]);

            return response()->json([
                'success' => true,
                'data'    => $formattedItems
            ], 200);
        } catch (\Exception $e) {
            Log::error('KasbonTramper Get Items Failed', ['kasbon_tramper_id' => $kasbonTramperId, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch items',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // SHOW ITEM (for edit modal)
    // ========================================

    public function showItem($id)
    {
        try {
            $item = KasbonTramperItem::with('joTramperItem')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'                  => $item->id,
                    'id_kasbon_tram_item' => $item->id_kasbon_tram_item,
                    'id_jo_tram_item'     => $item->id_jo_tram_item,
                    'nilai_hpp_tram_item' => (float)$item->nilai_hpp_tram_item,
                    'nilai_kasbon'        => (float)$item->nilai_kasbon,
                    'total_kasbon'        => (float)$item->total_kasbon,
                    'jo_tram_item'        => $item->joTramperItem,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('KasbonTramper Show Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Item not found: ' . $e->getMessage()
            ], 404);
        }
    }

    // ========================================
    // STORE (Traditional fallback)
    // ========================================

    public function store(Request $request)
    {
        Log::info('KasbonTramper Store Request', ['all_data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'id_jo_tram'                   => 'required|exists:b03_jo_tram,id_jo_tram',
            'id_md_dep'                    => 'required|exists:a08_md_dep,id_md_dep',
            'id_md_cabang'                 => 'required|exists:a09_md_branch,id_md_branch',
            'id_md_release'                => 'required|exists:a10_md_release_to,id_md_release',
            'tgl_kasbon'                   => 'required|date',
            'tgl_release'                  => 'nullable|date',
            'note'                         => 'nullable|string',
            'items'                        => 'required|array|min:1',
            'items.*.id_jo_tram_item'      => 'required|exists:b04_jo_tram_item,id_jo_tram_item',
            'items.*.nilai_kasbon'         => 'required|numeric|min:0',
        ], [
            'id_jo_tram.required'    => 'Job Order Tramper is required',
            'id_md_dep.required'     => 'Departemen is required',
            'id_md_cabang.required'  => 'Branch is required',
            'id_md_release.required' => 'Release To is required',
            'tgl_kasbon.required'    => 'Cash Advance Date is required',
            'items.required'         => 'At least one item is required',
            'items.min'              => 'At least one item is required',
        ]);

        if ($validator->fails()) {
            Log::error('KasbonTramper Validation Failed', ['errors' => $validator->errors()->toArray()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $idKasbonTram = IdGenerator::generateCaNo('c03_kasbon_tram', 'id_kasbon_tram');

            $lastKasbon = KasbonTramper::orderBy('id', 'desc')->first();
            $newNomor   = $lastKasbon ? $lastKasbon->nomor + 1 : 1;

            $kasbonTramper = KasbonTramper::create([
                'id_kasbon_tram' => $idKasbonTram,
                'id_jo_tram'     => $request->id_jo_tram,
                'id_md_dep'      => $request->id_md_dep,
                'id_md_cabang'   => $request->id_md_cabang,
                'id_md_release'  => $request->id_md_release,
                'nomor'          => $newNomor,
                'tgl_kasbon'     => $request->tgl_kasbon,
                'tgl_release'    => $request->tgl_release,
                'note'           => $request->note,
            ]);

            foreach ($request->items as $itemData) {
                $joTramItem  = JoTramperItem::find($itemData['id_jo_tram_item']);
                $nilaiHpp    = $joTramItem ? (float)$joTramItem->hpp_ops : 0;
                $nilaiKasbon = (float)$itemData['nilai_kasbon'];
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                KasbonTramperItem::create([
                    'id_kasbon_tram'      => $idKasbonTram,
                    'id_jo_tram_item'     => $itemData['id_jo_tram_item'],
                    'nilai_hpp_tram_item' => $nilaiHpp,
                    'nilai_kasbon'        => $nilaiKasbon,
                    'total_kasbon'        => $totalKasbon,
                ]);
            }

            DB::commit();

            Log::info('KasbonTramper Store Success', ['id' => $kasbonTramper->id, 'total_items' => count($request->items)]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kasbon Tramper successfully added',
                    'data'    => $kasbonTramper->load('items')
                ], 201);
            }

            return redirect()
                ->route('kasbon-tramper.index')
                ->with('success', 'Kasbon Tramper successfully added with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonTramper Store Failed', [
                'error_message' => $e->getMessage(),
                'error_line'    => $e->getLine(),
                'error_file'    => $e->getFile(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating Kasbon Tramper: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Error creating Kasbon Tramper: ' . $e->getMessage());
        }
    }

    // ========================================
    // SHOW
    // ========================================

    public function show(Request $request, $id)
    {
        Log::info('=== KasbonTramper Show START ===', ['id' => $id]);

        try {
            $kasbonTramper = KasbonTramper::with([
                'joTramper',
                'departemen',
                'cabang',
                'release',
                'items.joTramperItem',
            ])->findOrFail($id);

            $summary = [
                'total_items'        => $kasbonTramper->items->count(),
                'total_hpp'          => $kasbonTramper->items->sum('nilai_hpp_tram_item'),
                'total_nilai_kasbon' => $kasbonTramper->items->sum('nilai_kasbon'),
                'total_kasbon'       => $kasbonTramper->items->sum('total_kasbon'),
            ];

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $kasbonTramper, 'summary' => $summary]);
            }

            return view('data.kasbon-tramper.show', compact('kasbonTramper', 'summary'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('KasbonTramper Not Found', ['id' => $id]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kasbon Tramper not found'], 404);
            }
            return back()->with('error', 'Kasbon Tramper not found');
        } catch (\Exception $e) {
            Log::error('KasbonTramper Show Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error loading Kasbon Tramper: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error loading Kasbon Tramper: ' . $e->getMessage());
        }
    }

    // ========================================
    // EDIT
    // ========================================

    public function edit($id)
    {
        $kasbonTramper = KasbonTramper::with([
            'items.joTramperItem.invoice'
        ])->findOrFail($id);

        // Ambil semua JO Tramper Items berdasarkan JO yang dipilih
        $joTramperItems = JoTramperItem::where('id_jo_tram', $kasbonTramper->id_jo_tram)
            ->with('invoice')
            ->get();

        // Map: gabungkan jo_tram_item dengan kasbon_item yang sudah ada
        $mergedItems = $joTramperItems->map(function ($joItem) use ($kasbonTramper) {
            $kasbonItem = $kasbonTramper->items
                ->firstWhere('id_jo_tram_item', $joItem->id_jo_tram_item);

            return [
                'id_jo_tram_item'     => $joItem->id_jo_tram_item,
                'invoice_typ'         => $joItem->invoice->invoice_typ ?? $joItem->id_jo_tram_item,
                'invoice_ctg'         => $joItem->invoice->invoice_ctg ?? '-',
                'hargajual_idr'       => (float)$joItem->hargajual_idr,
                'hpp_ops'             => (float)$joItem->hpp_ops,
                // dari kasbon item (jika sudah ada)
                'id_kasbon_tram_item' => $kasbonItem?->id ?? null,
                'nilai_hpp_tram_item' => $kasbonItem ? (float)$kasbonItem->nilai_hpp_tram_item : (float)$joItem->hpp_ops,
                'nilai_kasbon'        => $kasbonItem ? (float)$kasbonItem->nilai_kasbon : 0,
                'total_kasbon'        => $kasbonItem ? (float)$kasbonItem->total_kasbon : (float)$joItem->hpp_ops,
                'has_kasbon'          => $kasbonItem !== null,
            ];
        });

        $joTrampers  = JoTramper::orderBy('no_jo_tram')->get();
        $departemens = Departemen::orderBy('nama_dep')->get();
        $cabangs     = Branch::orderBy('nama_branch')->get();
        $releases    = ReleaseTo::orderBy('id_md_release')->get();

        return view('data.kasbon-tramper.edit', compact(
            'kasbonTramper',
            'joTrampers',
            'departemens',
            'cabangs',
            'releases',
            'mergedItems'
        ));
    }

    // ========================================
    // UPDATE (Traditional fallback)
    // ========================================

    public function update(Request $request, $id)
    {
        Log::info('KasbonTramper Update Request', ['id' => $id, 'all_data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'id_jo_tram'                   => 'required|exists:b03_jo_tram,id_jo_tram',
            'id_md_dep'                    => 'required|exists:a08_md_dep,id_md_dep',
            'id_md_cabang'                 => 'required|exists:a09_md_branch,id_md_branch',
            'id_md_release'                => 'required|exists:a10_md_release_to,id_md_release',
            'tgl_kasbon'                   => 'required|date',
            'tgl_release'                  => 'nullable|date',
            'note'                         => 'nullable|string',
            'items'                        => 'required|array|min:1',
            'items.*.id_jo_tram_item'      => 'required|exists:b04_jo_tram_item,id_jo_tram_item',
            'items.*.nilai_kasbon'         => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $kasbonTramper = KasbonTramper::findOrFail($id);

            $kasbonTramper->update([
                'id_jo_tram'    => $request->id_jo_tram,
                'id_md_dep'     => $request->id_md_dep,
                'id_md_cabang'  => $request->id_md_cabang,
                'id_md_release' => $request->id_md_release,
                'tgl_kasbon'    => $request->tgl_kasbon,
                'tgl_release'   => $request->tgl_release,
                'note'          => $request->note,
            ]);

            // Hapus item lama (soft delete)
            foreach ($kasbonTramper->items as $oldItem) {
                $oldItem->delete();
            }

            // Buat item baru
            foreach ($request->items as $itemData) {
                $joTramItem  = JoTramperItem::find($itemData['id_jo_tram_item']);
                $nilaiHpp    = $joTramItem ? (float)$joTramItem->hpp_ops : 0;
                $nilaiKasbon = (float)$itemData['nilai_kasbon'];
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                KasbonTramperItem::create([
                    'id_kasbon_tram'      => $kasbonTramper->id_kasbon_tram,
                    'id_jo_tram_item'     => $itemData['id_jo_tram_item'],
                    'nilai_hpp_tram_item' => $nilaiHpp,
                    'nilai_kasbon'        => $nilaiKasbon,
                    'total_kasbon'        => $totalKasbon,
                ]);
            }

            DB::commit();

            Log::info('KasbonTramper Update Success', ['id' => $id]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kasbon Tramper successfully updated',
                    'data'    => $kasbonTramper->fresh()->load('items')
                ]);
            }

            return redirect()
                ->route('kasbon-tramper.index')
                ->with('success', 'Kasbon Tramper successfully updated with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonTramper Update Failed', ['id' => $id, 'error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating Kasbon Tramper: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Error updating Kasbon Tramper: ' . $e->getMessage());
        }
    }

    // ========================================
    // DESTROY
    // ========================================

    public function destroy(Request $request, $id)
    {
        Log::info('=== KasbonTramper Delete START ===', ['id' => $id]);

        DB::beginTransaction();
        try {
            $kasbonTramper = KasbonTramper::findOrFail($id);
            $itemsCount    = $kasbonTramper->items()->count();

            foreach ($kasbonTramper->items as $item) {
                $item->delete();
            }

            $kasbonTramper->delete();

            DB::commit();

            Log::info('KasbonTramper Deleted', ['id' => $id, 'deleted_items_count' => $itemsCount]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kasbon Tramper and all related items successfully deleted'
                ]);
            }

            return redirect()
                ->route('kasbon-tramper.index')
                ->with('success', "Kasbon Tramper deleted successfully along with {$itemsCount} item(s)");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kasbon Tramper not found'], 404);
            }
            return back()->with('error', 'Kasbon Tramper not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonTramper Delete Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting Kasbon Tramper: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting Kasbon Tramper: ' . $e->getMessage());
        }
    }

    // ========================================
    // GET FOR SELECT (API dropdown)
    // ========================================

    public function getForSelect(Request $request)
    {
        try {
            $query = KasbonTramper::with(['joTramper', 'departemen']);

            if ($request->filled('id_jo_tram')) {
                $query->where('id_jo_tram', $request->id_jo_tram);
            }
            if ($request->filled('id_md_dep')) {
                $query->where('id_md_dep', $request->id_md_dep);
            }

            $kasbonTrampers = $query->orderBy('id', 'desc')
                ->get()
                ->map(function ($kasbon) {
                    return [
                        'id'             => $kasbon->id,
                        'id_kasbon_tram' => $kasbon->id_kasbon_tram,
                        'text'           => $kasbon->id_kasbon_tram . ' - ' . ($kasbon->joTramper->no_jo_tram ?? '-'),
                        'jo_tramper'     => $kasbon->joTramper ? $kasbon->joTramper->no_jo_tram : null,
                        'departemen'     => $kasbon->departemen ? $kasbon->departemen->nama_dep : null,
                    ];
                });

            return response()->json(['success' => true, 'data' => $kasbonTrampers]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving kasbon trampers: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // GET JO TRAMPER ITEMS BY JO TRAM (API)
    // ========================================

    public function getJoTramperItems(Request $request)
    {
        try {
            $idJoTram = $request->get('id_jo_tram');

            if (!$idJoTram) {
                return response()->json(['success' => false, 'message' => 'id_jo_tram is required'], 422);
            }

            $items = JoTramperItem::where('id_jo_tram', $idJoTram)
                ->with('invoice')
                ->get()
                ->map(function ($item) {
                    return [
                        'id_jo_tram_item' => $item->id_jo_tram_item,
                        'text'            => $item->invoice ? $item->invoice->invoice_typ : $item->id_jo_tram_item,
                        'invoice_typ'     => $item->invoice ? $item->invoice->invoice_typ : null,
                        'invoice_ctg'     => $item->invoice ? $item->invoice->invoice_ctg : null,
                        'hpp_ops'         => (float)$item->hpp_ops,
                        'hargajual_idr'   => (float)$item->hargajual_idr,
                    ];
                });

            return response()->json(['success' => true, 'data' => $items]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving JO tramper items: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK DELETE
    // ========================================

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:c03_kasbon_tram,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $kasbonTrampers = KasbonTramper::whereIn('id', $request->ids)->get();

            foreach ($kasbonTrampers as $kasbon) {
                foreach ($kasbon->items as $item) {
                    $item->delete();
                }
                $kasbon->delete();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Kasbon Trampers successfully deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting kasbon trampers: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK SAVE ITEMS
    // ========================================

    public function bulkSaveItems(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'items'                   => 'required|array',
            'items.*.id_jo_tram_item' => 'required|exists:b04_jo_tram_item,id_jo_tram_item',
            'items.*.nilai_kasbon'    => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $kasbonTramper = KasbonTramper::findOrFail($id);

            DB::beginTransaction();

            foreach ($request->items as $itemData) {
                $joTramItem  = JoTramperItem::find($itemData['id_jo_tram_item']);
                $nilaiHpp    = $joTramItem ? (float)$joTramItem->hpp_ops : 0;
                $nilaiKasbon = (float)$itemData['nilai_kasbon'];
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                $existing = KasbonTramperItem::where('id_kasbon_tram', $kasbonTramper->id_kasbon_tram)
                    ->where('id_jo_tram_item', $itemData['id_jo_tram_item'])
                    ->first();

                if ($existing) {
                    $existing->update([
                        'nilai_hpp_tram_item' => $nilaiHpp,
                        'nilai_kasbon'        => $nilaiKasbon,
                        'total_kasbon'        => $totalKasbon,
                    ]);
                } else {
                    if ($nilaiKasbon > 0) {
                        $maxId = DB::table('c04_kasbon_tram_item')->max('id_kasbon_tram_item');
                        KasbonTramperItem::create([
                            'id_kasbon_tram_item' => $maxId ? (string)((int)$maxId + 1) : '1',
                            'id_kasbon_tram'      => $kasbonTramper->id_kasbon_tram,
                            'id_jo_tram_item'     => $itemData['id_jo_tram_item'],
                            'nilai_hpp_tram_item' => $nilaiHpp,
                            'nilai_kasbon'        => $nilaiKasbon,
                            'total_kasbon'        => $totalKasbon,
                        ]);
                    }
                }
            }

            DB::commit();

            $totals = KasbonTramperItem::where('id_kasbon_tram', $kasbonTramper->id_kasbon_tram)
                ->selectRaw('SUM(nilai_hpp_tram_item) as total_hpp, SUM(nilai_kasbon) as total_kasbon, SUM(total_kasbon) as total_remaining')
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Items saved successfully',
                'totals'  => $totals,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('bulkSaveItems KasbonTramper failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save items: ' . $e->getMessage()
            ], 500);
        }
    }
}