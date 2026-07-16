<?php

namespace App\Http\Controllers\Data;

use App\Helpers\IdGenerator;
use App\Http\Controllers\Controller;
use App\Models\Data\KasbonOther;
use App\Models\Data\KasbonOtherItem;
use App\Models\Data\JoOther;
use App\Models\Data\JoOtherItem;
use App\Models\Master\Departemen;
use App\Models\Master\Branch;
use App\Models\Master\ReleaseTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KasbonOtherController extends Controller
{
    // ========================================
    // INDEX
    // ========================================

    public function index(Request $request)
    {
        try {
            $query = KasbonOther::with(['joOther', 'departemen', 'cabang', 'release', 'items']);

            if ($request->filled('id_jo_other')) {
                $query->where('id_jo_other', $request->id_jo_other);
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
                    $q->where('id_kasbon_other', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('joOther', function ($q) use ($search) {
                            $q->where('no_jo_other', 'like', "%{$search}%");
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

            $kasbonOthers = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $kasbonOthers]);
            }

            $joOthers    = JoOther::orderBy('no_jo_other')->get();
            $departemens = Departemen::orderBy('nama_dep')->get();
            $cabangs     = Branch::orderBy('nama_branch')->get();
            $releases    = ReleaseTo::orderBy('id_md_release')->get();

            $currentFilters = [
                'id_jo_other'     => $request->get('id_jo_other', ''),
                'id_md_dep'       => $request->get('id_md_dep', ''),
                'id_md_cabang'    => $request->get('id_md_cabang', ''),
                'tgl_kasbon_from' => $request->get('tgl_kasbon_from', ''),
                'tgl_kasbon_to'   => $request->get('tgl_kasbon_to', ''),
            ];

            return view('data.kasbon-other.index', compact(
                'kasbonOthers',
                'joOthers',
                'departemens',
                'cabangs',
                'releases',
                'currentFilters'
            ));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error retrieving kasbon others: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving kasbon others: ' . $e->getMessage());
        }
    }

    // ========================================
    // CREATE
    // ========================================

    public function create()
    {
        $joOthers        = JoOther::with(['customer', 'port'])->orderBy('no_jo_other')->get();
        $departemens     = Departemen::orderBy('nama_dep')->get();
        $cabangs         = Branch::orderBy('nama_branch')->get();
        $releases        = ReleaseTo::orderBy('id_md_release')->get();
        $previewNoKasbon = IdGenerator::generateCaNo('c05_kasbon_other', 'id_kasbon_other');

        return view('data.kasbon-other.create', compact(
            'joOthers',
            'departemens',
            'cabangs',
            'releases',
            'previewNoKasbon'
        ));
    }

    // ========================================
    // STORE HEADER
    // ========================================

    public function storeHeader(Request $request)
    {
        Log::info('=== KasbonOther Store Header START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_jo_other'   => 'required|exists:b05_jo_other,id_jo_other',
                'id_md_dep'     => 'required|exists:a08_md_dep,id_md_dep',
                'id_md_cabang'  => 'required|exists:a09_md_branch,id_md_branch',
                'id_md_release' => 'required|exists:a10_md_release_to,id_md_release',
                'tgl_kasbon'    => 'required|date',
                'tgl_release'   => 'nullable|date',
                'note'          => 'nullable|string',
            ], [
                'id_jo_other.required'   => 'Job Order Other is required',
                'id_jo_other.exists'     => 'Selected Job Order Other does not exist',
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

            $idKasbonOther = IdGenerator::generateCaNo('c05_kasbon_other', 'id_kasbon_other');

            $lastKasbon = KasbonOther::orderBy('id', 'desc')->first();
            $newNomor   = $lastKasbon ? $lastKasbon->nomor + 1 : 1;

            $kasbonOther = KasbonOther::create([
                'id_kasbon_other' => $idKasbonOther,
                'id_jo_other'     => $request->id_jo_other,
                'id_md_dep'       => $request->id_md_dep,
                'id_md_cabang'    => $request->id_md_cabang,
                'id_md_release'   => $request->id_md_release,
                'nomor'           => $newNomor,
                'tgl_kasbon'      => $request->tgl_kasbon,
                'tgl_release'     => $request->tgl_release,
                'note'            => $request->note,
            ]);

            DB::commit();

            $kasbonOther->load(['joOther', 'departemen', 'cabang', 'release']);

            Log::info('KasbonOther Header Created', ['id' => $kasbonOther->id, 'id_kasbon_other' => $idKasbonOther]);

            return response()->json([
                'success'      => true,
                'message'      => 'Kasbon Other header saved successfully',
                'redirect_url' => route('kasbon-other.edit', $kasbonOther->id),
                'data'         => [
                    'id'              => $kasbonOther->id,
                    'id_kasbon_other' => $kasbonOther->id_kasbon_other,
                    'id_jo_other'     => $kasbonOther->id_jo_other,
                    'id_md_dep'       => $kasbonOther->id_md_dep,
                    'id_md_cabang'    => $kasbonOther->id_md_cabang,
                    'id_md_release'   => $kasbonOther->id_md_release,
                    'nomor'           => $kasbonOther->nomor,
                    'tgl_kasbon'      => $kasbonOther->tgl_kasbon,
                    'tgl_release'     => $kasbonOther->tgl_release,
                    'note'            => $kasbonOther->note,
                    'joOther'         => $kasbonOther->joOther,
                    'departemen'      => $kasbonOther->departemen,
                    'cabang'          => $kasbonOther->cabang,
                    'release'         => $kasbonOther->release,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonOther Store Header Failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save Kasbon Other header',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // UPDATE HEADER
    // ========================================

    public function updateHeader(Request $request, $id)
    {
        Log::info('=== KasbonOther Update Header START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_jo_other'   => 'required|exists:b05_jo_other,id_jo_other',
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

            $kasbonOther = KasbonOther::findOrFail($id);
            $kasbonOther->update([
                'id_jo_other'   => $request->id_jo_other,
                'id_md_dep'     => $request->id_md_dep,
                'id_md_cabang'  => $request->id_md_cabang,
                'id_md_release' => $request->id_md_release,
                'tgl_kasbon'    => $request->tgl_kasbon,
                'tgl_release'   => $request->tgl_release,
                'note'          => $request->note,
            ]);

            DB::commit();

            $kasbonOther->load(['joOther', 'departemen', 'cabang', 'release']);

            Log::info('KasbonOther Header Updated', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Kasbon Other header updated successfully',
                'data'    => $kasbonOther
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonOther Update Header Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Kasbon Other header',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // STORE ITEM
    // ========================================

    public function storeItem(Request $request)
    {
        Log::info('=== KasbonOther Store Item START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_kasbon_other'  => 'required|exists:c05_kasbon_other,id_kasbon_other',
                'id_jo_other_item' => 'required|exists:b06_jo_other_item,id_jo_other_item',
                'nilai_kasbon'     => 'required|numeric|min:0',
            ], [
                'id_kasbon_other.required'  => 'Kasbon Other is required',
                'id_jo_other_item.required' => 'Job Order Other Item is required',
                'nilai_kasbon.required'     => 'Advance Number Amount is required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                $maxId = DB::table('c06_kasbon_other_item')
                    ->lockForUpdate()
                    ->max('id_kasbon_other_item');

                $newIdKasbonOtherItem = $maxId ? (string)((int)$maxId + 1) : '1';

                $joOtherItem = JoOtherItem::find($request->id_jo_other_item);
                $nilaiHpp    = $joOtherItem ? (float)$joOtherItem->hpp_ops : 0;

                $nilaiKasbon = (float)$request->nilai_kasbon;
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                $item = KasbonOtherItem::create([
                    'id_kasbon_other_item' => $newIdKasbonOtherItem,
                    'id_kasbon_other'      => $request->id_kasbon_other,
                    'id_jo_other_item'     => $request->id_jo_other_item,
                    'nilai_hpp_other_item' => $nilaiHpp,
                    'nilai_kasbon'         => $nilaiKasbon,
                    'total_kasbon'         => $totalKasbon,
                ]);

                $item->load('joOtherItem');

                DB::commit();

                Log::info('KasbonOther Item Created', ['id' => $item->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Item added successfully',
                    'data'    => [
                        'id'                   => $item->id,
                        'id_kasbon_other_item' => $item->id_kasbon_other_item,
                        'id_kasbon_other'      => $item->id_kasbon_other,
                        'id_jo_other_item'     => $item->id_jo_other_item,
                        'nilai_hpp_other_item' => (float)$item->nilai_hpp_other_item,
                        'nilai_kasbon'         => (float)$item->nilai_kasbon,
                        'total_kasbon'         => (float)$item->total_kasbon,
                        'jo_other_item'        => $item->joOtherItem,
                    ]
                ], 201);
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                if ($e->getCode() === '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    sleep(1);
                    if (!$request->has('_retry')) {
                        $request->merge(['_retry' => true]);
                        return $this->storeItem($request);
                    }
                }
                throw $e;
            }
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            Log::error('KasbonOther Store Item Failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // UPDATE ITEM
    // ========================================

    public function updateItem(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_jo_other_item' => 'required|exists:b06_jo_other_item,id_jo_other_item',
                'nilai_kasbon'     => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $item        = KasbonOtherItem::findOrFail($id);
            $joOtherItem = JoOtherItem::find($request->id_jo_other_item);
            $nilaiHpp    = $joOtherItem ? (float)$joOtherItem->hpp_ops : (float)$item->nilai_hpp_other_item;
            $nilaiKasbon = (float)$request->nilai_kasbon;
            $totalKasbon = $nilaiHpp - $nilaiKasbon;

            $item->update([
                'id_jo_other_item'     => $request->id_jo_other_item,
                'nilai_hpp_other_item' => $nilaiHpp,
                'nilai_kasbon'         => $nilaiKasbon,
                'total_kasbon'         => $totalKasbon,
            ]);

            $item->refresh()->load('joOtherItem');
            DB::commit();

            Log::info('KasbonOther Item Updated', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data'    => [
                    'id'                   => $item->id,
                    'id_kasbon_other_item' => $item->id_kasbon_other_item,
                    'id_kasbon_other'      => $item->id_kasbon_other,
                    'id_jo_other_item'     => $item->id_jo_other_item,
                    'nilai_hpp_other_item' => (float)$item->nilai_hpp_other_item,
                    'nilai_kasbon'         => (float)$item->nilai_kasbon,
                    'total_kasbon'         => (float)$item->total_kasbon,
                    'jo_other_item'        => $item->joOtherItem,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonOther Update Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update item', 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // DESTROY ITEM
    // ========================================

    public function destroyItem($id)
    {
        try {
            DB::beginTransaction();
            $item = KasbonOtherItem::findOrFail($id);
            $item->delete();
            DB::commit();
            Log::info('KasbonOther Item Deleted', ['id' => $id]);
            return response()->json(['success' => true, 'message' => 'Item deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonOther Delete Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete item', 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // GET ITEMS
    // ========================================

    public function getItems($kasbonOtherId)
    {
        try {
            $items = KasbonOtherItem::where('id_kasbon_other', $kasbonOtherId)
                ->with('joOtherItem')
                ->orderBy('created_at')
                ->get();

            $formattedItems = $items->map(function ($item) {
                return [
                    'id'                   => $item->id,
                    'id_kasbon_other_item' => $item->id_kasbon_other_item,
                    'id_kasbon_other'      => $item->id_kasbon_other,
                    'id_jo_other_item'     => $item->id_jo_other_item,
                    'nilai_hpp_other_item' => (float)$item->nilai_hpp_other_item,
                    'nilai_kasbon'         => (float)$item->nilai_kasbon,
                    'total_kasbon'         => (float)$item->total_kasbon,
                    'jo_other_item'        => $item->joOtherItem,
                ];
            });

            return response()->json(['success' => true, 'data' => $formattedItems], 200);
        } catch (\Exception $e) {
            Log::error('KasbonOther Get Items Failed', ['kasbon_other_id' => $kasbonOtherId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch items', 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // SHOW ITEM
    // ========================================

    public function showItem($id)
    {
        try {
            $item = KasbonOtherItem::with('joOtherItem')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data'    => [
                    'id'                   => $item->id,
                    'id_kasbon_other_item' => $item->id_kasbon_other_item,
                    'id_jo_other_item'     => $item->id_jo_other_item,
                    'nilai_hpp_other_item' => (float)$item->nilai_hpp_other_item,
                    'nilai_kasbon'         => (float)$item->nilai_kasbon,
                    'total_kasbon'         => (float)$item->total_kasbon,
                    'jo_other_item'        => $item->joOtherItem,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Item not found: ' . $e->getMessage()], 404);
        }
    }

    // ========================================
    // STORE (Traditional fallback)
    // ========================================

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_jo_other'                    => 'required|exists:b05_jo_other,id_jo_other',
            'id_md_dep'                      => 'required|exists:a08_md_dep,id_md_dep',
            'id_md_cabang'                   => 'required|exists:a09_md_branch,id_md_branch',
            'id_md_release'                  => 'required|exists:a10_md_release_to,id_md_release',
            'tgl_kasbon'                     => 'required|date',
            'tgl_release'                    => 'nullable|date',
            'note'                           => 'nullable|string',
            'items'                          => 'required|array|min:1',
            'items.*.id_jo_other_item'       => 'required|exists:b06_jo_other_item,id_jo_other_item',
            'items.*.nilai_kasbon'           => 'required|numeric|min:0',
        ], [
            'id_jo_other.required'   => 'Job Order Other is required',
            'id_md_dep.required'     => 'Departemen is required',
            'id_md_cabang.required'  => 'Branch is required',
            'id_md_release.required' => 'Release To is required',
            'tgl_kasbon.required'    => 'Cash Advance Date is required',
            'items.required'         => 'At least one item is required',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $idKasbonOther = IdGenerator::generateCaNo('c05_kasbon_other', 'id_kasbon_other');
            $lastKasbon    = KasbonOther::orderBy('id', 'desc')->first();
            $newNomor      = $lastKasbon ? $lastKasbon->nomor + 1 : 1;

            $kasbonOther = KasbonOther::create([
                'id_kasbon_other' => $idKasbonOther,
                'id_jo_other'     => $request->id_jo_other,
                'id_md_dep'       => $request->id_md_dep,
                'id_md_cabang'    => $request->id_md_cabang,
                'id_md_release'   => $request->id_md_release,
                'nomor'           => $newNomor,
                'tgl_kasbon'      => $request->tgl_kasbon,
                'tgl_release'     => $request->tgl_release,
                'note'            => $request->note,
            ]);

            foreach ($request->items as $itemData) {
                $joOtherItem = JoOtherItem::find($itemData['id_jo_other_item']);
                $nilaiHpp    = $joOtherItem ? (float)$joOtherItem->hpp_ops : 0;
                $nilaiKasbon = (float)$itemData['nilai_kasbon'];
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                KasbonOtherItem::create([
                    'id_kasbon_other'      => $idKasbonOther,
                    'id_jo_other_item'     => $itemData['id_jo_other_item'],
                    'nilai_hpp_other_item' => $nilaiHpp,
                    'nilai_kasbon'         => $nilaiKasbon,
                    'total_kasbon'         => $totalKasbon,
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Kasbon Other successfully added', 'data' => $kasbonOther->load('items')], 201);
            }
            return redirect()->route('kasbon-other.index')->with('success', 'Kasbon Other successfully added with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonOther Store Failed', ['error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error creating Kasbon Other: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error creating Kasbon Other: ' . $e->getMessage());
        }
    }

    // ========================================
    // SHOW
    // ========================================

    public function show(Request $request, $id)
    {
        try {
            $kasbonOther = KasbonOther::with([
                'joOther',
                'departemen',
                'cabang',
                'release',
                'items.joOtherItem',
            ])->findOrFail($id);

            $summary = [
                'total_items'        => $kasbonOther->items->count(),
                'total_hpp'          => $kasbonOther->items->sum('nilai_hpp_other_item'),
                'total_nilai_kasbon' => $kasbonOther->items->sum('nilai_kasbon'),
                'total_kasbon'       => $kasbonOther->items->sum('total_kasbon'),
            ];

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $kasbonOther, 'summary' => $summary]);
            }
            return view('data.kasbon-other.show', compact('kasbonOther', 'summary'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kasbon Other not found'], 404);
            }
            return back()->with('error', 'Kasbon Other not found');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error loading Kasbon Other: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error loading Kasbon Other: ' . $e->getMessage());
        }
    }

    // ========================================
    // EDIT
    // ========================================

    public function edit($id)
    {
        $kasbonOther = KasbonOther::with([
            'items.joOtherItem.invoice'
        ])->findOrFail($id);

        $joOtherItems = JoOtherItem::where('id_jo_other', $kasbonOther->id_jo_other)
            ->with('invoice')
            ->get();

        $mergedItems = $joOtherItems->map(function ($joItem) use ($kasbonOther) {
            $kasbonItem = $kasbonOther->items
                ->firstWhere('id_jo_other_item', $joItem->id_jo_other_item);

            return [
                'id_jo_other_item'     => $joItem->id_jo_other_item,
                'invoice_typ'          => $joItem->invoice->invoice_typ ?? $joItem->id_jo_other_item,
                'invoice_ctg'          => $joItem->invoice->invoice_ctg ?? '-',
                'hargajual_idr'        => (float)$joItem->hargajual_idr,
                'hpp_ops'              => (float)$joItem->hpp_ops,
                'id_kasbon_other_item' => $kasbonItem?->id ?? null,
                'nilai_hpp_other_item' => $kasbonItem ? (float)$kasbonItem->nilai_hpp_other_item : (float)$joItem->hpp_ops,
                'nilai_kasbon'         => $kasbonItem ? (float)$kasbonItem->nilai_kasbon : 0,
                'total_kasbon'         => $kasbonItem ? (float)$kasbonItem->total_kasbon : (float)$joItem->hpp_ops,
                'has_kasbon'           => $kasbonItem !== null,
            ];
        });

        $joOthers    = JoOther::orderBy('no_jo_other')->get();
        $departemens = Departemen::orderBy('nama_dep')->get();
        $cabangs     = Branch::orderBy('nama_branch')->get();
        $releases    = ReleaseTo::orderBy('id_md_release')->get();

        return view('data.kasbon-other.edit', compact(
            'kasbonOther',
            'joOthers',
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
        $validator = Validator::make($request->all(), [
            'id_jo_other'              => 'required|exists:b05_jo_other,id_jo_other',
            'id_md_dep'                => 'required|exists:a08_md_dep,id_md_dep',
            'id_md_cabang'             => 'required|exists:a09_md_branch,id_md_branch',
            'id_md_release'            => 'required|exists:a10_md_release_to,id_md_release',
            'tgl_kasbon'               => 'required|date',
            'tgl_release'              => 'nullable|date',
            'note'                     => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.id_jo_other_item' => 'required|exists:b06_jo_other_item,id_jo_other_item',
            'items.*.nilai_kasbon'     => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $kasbonOther = KasbonOther::findOrFail($id);
            $kasbonOther->update([
                'id_jo_other'   => $request->id_jo_other,
                'id_md_dep'     => $request->id_md_dep,
                'id_md_cabang'  => $request->id_md_cabang,
                'id_md_release' => $request->id_md_release,
                'tgl_kasbon'    => $request->tgl_kasbon,
                'tgl_release'   => $request->tgl_release,
                'note'          => $request->note,
            ]);

            foreach ($kasbonOther->items as $oldItem) {
                $oldItem->delete();
            }

            foreach ($request->items as $itemData) {
                $joOtherItem = JoOtherItem::find($itemData['id_jo_other_item']);
                $nilaiHpp    = $joOtherItem ? (float)$joOtherItem->hpp_ops : 0;
                $nilaiKasbon = (float)$itemData['nilai_kasbon'];
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                KasbonOtherItem::create([
                    'id_kasbon_other'      => $kasbonOther->id_kasbon_other,
                    'id_jo_other_item'     => $itemData['id_jo_other_item'],
                    'nilai_hpp_other_item' => $nilaiHpp,
                    'nilai_kasbon'         => $nilaiKasbon,
                    'total_kasbon'         => $totalKasbon,
                ]);
            }

            DB::commit();
            Log::info('KasbonOther Update Success', ['id' => $id]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Kasbon Other successfully updated', 'data' => $kasbonOther->fresh()->load('items')]);
            }
            return redirect()->route('kasbon-other.index')->with('success', 'Kasbon Other successfully updated with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonOther Update Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating Kasbon Other: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error updating Kasbon Other: ' . $e->getMessage());
        }
    }

    // ========================================
    // DESTROY
    // ========================================

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $kasbonOther = KasbonOther::findOrFail($id);
            $itemsCount  = $kasbonOther->items()->count();

            foreach ($kasbonOther->items as $item) {
                $item->delete();
            }
            $kasbonOther->delete();

            DB::commit();
            Log::info('KasbonOther Deleted', ['id' => $id]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Kasbon Other and all related items successfully deleted']);
            }
            return redirect()->route('kasbon-other.index')->with('success', "Kasbon Other deleted successfully along with {$itemsCount} item(s)");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kasbon Other not found'], 404);
            }
            return back()->with('error', 'Kasbon Other not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonOther Delete Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting Kasbon Other: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting Kasbon Other: ' . $e->getMessage());
        }
    }

    // ========================================
    // GET FOR SELECT
    // ========================================

    public function getForSelect(Request $request)
    {
        try {
            $query = KasbonOther::with(['joOther', 'departemen']);

            if ($request->filled('id_jo_other')) {
                $query->where('id_jo_other', $request->id_jo_other);
            }
            if ($request->filled('id_md_dep')) {
                $query->where('id_md_dep', $request->id_md_dep);
            }

            $kasbonOthers = $query->orderBy('id', 'desc')->get()->map(function ($kasbon) {
                return [
                    'id'              => $kasbon->id,
                    'id_kasbon_other' => $kasbon->id_kasbon_other,
                    'text'            => $kasbon->id_kasbon_other . ' - ' . ($kasbon->joOther->no_jo_other ?? '-'),
                    'jo_other'        => $kasbon->joOther ? $kasbon->joOther->no_jo_other : null,
                    'departemen'      => $kasbon->departemen ? $kasbon->departemen->nama_dep : null,
                ];
            });

            return response()->json(['success' => true, 'data' => $kasbonOthers]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving kasbon others: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // GET JO OTHER ITEMS
    // ========================================

    public function getJoOtherItems(Request $request)
    {
        try {
            $idJoOther = $request->get('id_jo_other');

            if (!$idJoOther) {
                return response()->json(['success' => false, 'message' => 'id_jo_other is required'], 422);
            }

            $items = JoOtherItem::where('id_jo_other', $idJoOther)
                ->with('invoice')
                ->get()
                ->map(function ($item) {
                    return [
                        'id_jo_other_item' => $item->id_jo_other_item,
                        'text'             => $item->invoice ? $item->invoice->invoice_typ : $item->id_jo_other_item,
                        'invoice_typ'      => $item->invoice ? $item->invoice->invoice_typ : null,
                        'invoice_ctg'      => $item->invoice ? $item->invoice->invoice_ctg : null,
                        'hpp_ops'          => (float)$item->hpp_ops,
                        'hargajual_idr'    => (float)$item->hargajual_idr,
                    ];
                });

            return response()->json(['success' => true, 'data' => $items]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving JO other items: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK DELETE
    // ========================================

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:c05_kasbon_other,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $kasbonOthers = KasbonOther::whereIn('id', $request->ids)->get();
            foreach ($kasbonOthers as $kasbon) {
                foreach ($kasbon->items as $item) {
                    $item->delete();
                }
                $kasbon->delete();
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Kasbon Others successfully deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting kasbon others: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK SAVE ITEMS
    // ========================================

    public function bulkSaveItems(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'items'                    => 'required|array',
            'items.*.id_jo_other_item' => 'required|exists:b06_jo_other_item,id_jo_other_item',
            'items.*.nilai_kasbon'     => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $kasbonOther = KasbonOther::findOrFail($id);
            DB::beginTransaction();

            foreach ($request->items as $itemData) {
                $joOtherItem = JoOtherItem::find($itemData['id_jo_other_item']);
                $nilaiHpp    = $joOtherItem ? (float)$joOtherItem->hpp_ops : 0;
                $nilaiKasbon = (float)$itemData['nilai_kasbon'];
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                $existing = KasbonOtherItem::where('id_kasbon_other', $kasbonOther->id_kasbon_other)
                    ->where('id_jo_other_item', $itemData['id_jo_other_item'])
                    ->first();

                if ($existing) {
                    $existing->update([
                        'nilai_hpp_other_item' => $nilaiHpp,
                        'nilai_kasbon'         => $nilaiKasbon,
                        'total_kasbon'         => $totalKasbon,
                    ]);
                } else {
                    if ($nilaiKasbon > 0) {
                        $maxId = DB::table('c06_kasbon_other_item')->max('id_kasbon_other_item');
                        KasbonOtherItem::create([
                            'id_kasbon_other_item' => $maxId ? (string)((int)$maxId + 1) : '1',
                            'id_kasbon_other'      => $kasbonOther->id_kasbon_other,
                            'id_jo_other_item'     => $itemData['id_jo_other_item'],
                            'nilai_hpp_other_item' => $nilaiHpp,
                            'nilai_kasbon'         => $nilaiKasbon,
                            'total_kasbon'         => $totalKasbon,
                        ]);
                    }
                }
            }

            DB::commit();

            $totals = KasbonOtherItem::where('id_kasbon_other', $kasbonOther->id_kasbon_other)
                ->selectRaw('SUM(nilai_hpp_other_item) as total_hpp, SUM(nilai_kasbon) as total_kasbon, SUM(total_kasbon) as total_remaining')
                ->first();

            return response()->json(['success' => true, 'message' => 'Items saved successfully', 'totals' => $totals]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('bulkSaveItems KasbonOther failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to save items: ' . $e->getMessage()], 500);
        }
    }

    public function exportPdf($id)
    {
        try {
            $kasbonOther = KasbonOther::with([
                'joOther.customer',
                'joOther.items.invoice',   // seluruh JO items (sumber baris tabel)
                'departemen',
                'cabang',
                'release',
                'items',                   // kasbon items (lookup nilai_kasbon)
            ])->findOrFail($id);

            // Hitung total CA persis seperti footerTotalCA di edit view:
            // iterasi joOther->items, join ke kasbonLookup by id_jo_other_item
            $kasbonLookup = $kasbonOther->items->keyBy('id_jo_other_item');
            $joItems      = $kasbonOther->joOther
                ? $kasbonOther->joOther->items
                : collect();

            $totalCA = $joItems->sum(function ($joItem) use ($kasbonLookup) {
                $k = $kasbonLookup->get($joItem->id_jo_other_item);
                return $k ? (float) $k->nilai_kasbon : 0;
            });

            $terbilang = $this->toTerbilang((int) round($totalCA)) . ' Rupiah';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'data.kasbon-other.pdf',   // resources/views/data/kasbon-other/pdf.blade.php
                compact('kasbonOther', 'terbilang')
            )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'Arial',
                    'dpi'                  => 150,
                ]);

            $filename = 'Kasbon-Other-'
                . str_replace(['/', '\\'], '-', $kasbonOther->id_kasbon_other ?? $id)
                . '.pdf';

            return $pdf->stream($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kasbon Other not found'], 404);
            }
            return back()->with('error', 'Kasbon Other not found');
        } catch (\Exception $e) {
            Log::error('Kasbon Other Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to export PDF: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Simple Indonesian number-to-words (terbilang) helper.
     */
    private function toTerbilang(int $number): string
    {
        if ($number < 0) return 'minus ' . $this->toTerbilang(abs($number));

        $words = [
            '',
            'Satu',
            'Dua',
            'Tiga',
            'Empat',
            'Lima',
            'Enam',
            'Tujuh',
            'Delapan',
            'Sembilan',
            'Sepuluh',
            'Sebelas',
        ];

        if ($number === 0)  return 'Nol';
        if ($number < 12)   return $words[$number];
        if ($number < 20)   return $this->toTerbilang($number - 10) . ' Belas';
        if ($number < 100)  return $words[(int) ($number / 10)] . ' Puluh'
            . ($number % 10 ? ' ' . $this->toTerbilang($number % 10) : '');
        if ($number < 200)  return 'Seratus'
            . ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 1000) return $words[(int) ($number / 100)] . ' Ratus'
            . ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 2000) return 'Seribu'
            . ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000)
            return $this->toTerbilang((int) ($number / 1000)) . ' Ribu'
                . ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000_000)
            return $this->toTerbilang((int) ($number / 1_000_000)) . ' Juta'
                . ($number % 1_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000) : '');
        if ($number < 1_000_000_000_000)
            return $this->toTerbilang((int) ($number / 1_000_000_000)) . ' Miliar'
                . ($number % 1_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000) : '');

        return $this->toTerbilang((int) ($number / 1_000_000_000_000)) . ' Triliun'
            . ($number % 1_000_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000_000) : '');
    }

    public function export(Request $request)
    {
        $format       = $request->get('format', 'excel');
        $kasbonOthers = $this->buildExportQuery($request)->get();

        if ($format === 'pdf') {
            $filters = $this->activeFilters($request);

            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                    'data.kasbon-other.export_pdf',
                    compact('kasbonOthers', 'filters')
                )->setPaper('a4', 'landscape')->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'Arial',
                    'dpi'                  => 150,
                ]);
                return $pdf->stream('kasbon_other_' . date('Ymd_His') . '.pdf');
            }

            $html = view('data.kasbon-other.export_pdf', compact('kasbonOthers', 'filters'))->render();
            return response($html, 200, [
                'Content-Type'        => 'text/html; charset=UTF-8',
                'Content-Disposition' => 'inline; filename="kasbon_other_' . date('Ymd_His') . '.pdf"',
            ]);
        }

        return (new \App\Exports\Data\KasbonOtherExport($kasbonOthers))->download();
    }

    private function buildExportQuery(Request $request)
    {
        $query = KasbonOther::with(['joOther', 'departemen', 'cabang', 'release', 'items']);

        if ($request->filled('id_jo_other'))     $query->where('id_jo_other', $request->id_jo_other);
        if ($request->filled('id_md_dep'))       $query->where('id_md_dep', $request->id_md_dep);
        if ($request->filled('id_md_cabang'))    $query->where('id_md_cabang', $request->id_md_cabang);
        if ($request->filled('tgl_kasbon_from')) $query->where('tgl_kasbon', '>=', $request->tgl_kasbon_from);
        if ($request->filled('tgl_kasbon_to'))   $query->where('tgl_kasbon', '<=', $request->tgl_kasbon_to);

        return $query->orderBy('tgl_kasbon', 'desc')->orderBy('id', 'desc');
    }

    private function activeFilters(Request $request): array
    {
        $filters = [];

        if ($request->filled('id_jo_other')) {
            $jo = \App\Models\Data\JoOther::find($request->id_jo_other);
            $filters['jo_label'] = $jo ? $jo->no_jo_other : $request->id_jo_other;
        }
        if ($request->filled('id_md_dep')) {
            $dep = \App\Models\Master\Departemen::find($request->id_md_dep);
            $filters['dep_label'] = $dep ? $dep->nama_dep : $request->id_md_dep;
        }
        if ($request->filled('id_md_cabang')) {
            $cabang = \App\Models\Master\Branch::find($request->id_md_cabang);
            $filters['cabang_label'] = $cabang ? $cabang->nama_branch : $request->id_md_cabang;
        }
        if ($request->filled('tgl_kasbon_from')) $filters['tgl_kasbon_from'] = $request->tgl_kasbon_from;
        if ($request->filled('tgl_kasbon_to'))   $filters['tgl_kasbon_to']   = $request->tgl_kasbon_to;

        return $filters;
    }
}
