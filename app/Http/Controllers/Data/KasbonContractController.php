<?php

namespace App\Http\Controllers\Data;

use App\Helpers\IdGenerator;
use App\Http\Controllers\Controller;
use App\Models\Data\KasbonContract;
use App\Models\Data\KasbonContractItem;
use App\Models\Data\JoContract;
use App\Models\Data\JoContractItem;
use App\Models\Master\Departemen;
use App\Models\Master\Branch;
use App\Models\Master\ReleaseTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KasbonContractController extends Controller
{
    // ========================================
    // INDEX
    // ========================================

    public function index(Request $request)
    {
        try {
            $query = KasbonContract::with(['joContract', 'departemen', 'cabang', 'release', 'items']);

            if ($request->filled('id_jo_cont')) {
                $query->where('id_jo_cont', $request->id_jo_cont);
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
                    $q->where('id_kasbon_cont', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('joContract', function ($q) use ($search) {
                            $q->where('no_jo_cont', 'like', "%{$search}%");
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

            $kasbonContracts = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $kasbonContracts]);
            }

            $joContracts = JoContract::orderBy('no_jo_cont')->get();
            $departemens = Departemen::orderBy('nama_dep')->get();
            $cabangs     = Branch::orderBy('nama_branch')->get();
            $releases    = ReleaseTo::orderBy('id_md_release')->get();

            $currentFilters = [
                'id_jo_cont'      => $request->get('id_jo_cont', ''),
                'id_md_dep'       => $request->get('id_md_dep', ''),
                'id_md_cabang'    => $request->get('id_md_cabang', ''),
                'tgl_kasbon_from' => $request->get('tgl_kasbon_from', ''),
                'tgl_kasbon_to'   => $request->get('tgl_kasbon_to', ''),
            ];

            return view('data.kasbon-contract.index', compact(
                'kasbonContracts',
                'joContracts',
                'departemens',
                'cabangs',
                'releases',
                'currentFilters'
            ));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error retrieving kasbon contracts: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving kasbon contracts: ' . $e->getMessage());
        }
    }

    // ========================================
    // CREATE
    // ========================================

    public function create()
    {
        $joContracts = JoContract::with(['contract', 'area'])
            ->orderBy('no_jo_cont')
            ->get();
        $departemens = Departemen::orderBy('nama_dep')->get();
        $cabangs     = Branch::orderBy('nama_branch')->get();
        $releases    = ReleaseTo::orderBy('id_md_release')->get();
        $previewNoKasbon = IdGenerator::generateCaNo('c01_kasbon_cont', 'id_kasbon_cont');

        return view('data.kasbon-contract.create', compact(
            'joContracts',
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
        Log::info('=== KasbonContract Store Header START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_jo_cont'       => 'required|exists:b01_jo_cont,id_jo_cont',
                'id_md_dep'        => 'required|exists:a08_md_dep,id_md_dep',
                'id_md_cabang'     => 'required|exists:a09_md_branch,id_md_branch',
                'id_md_release'    => 'required|exists:a10_md_release_to,id_md_release',
                'tgl_kasbon'       => 'required|date',
                'tgl_release'      => 'nullable|date',
                'note'             => 'nullable|string',
                'priority'         => 'required|in:high,normal',
                'due_date'         => 'nullable|date_format:Y-m-d\TH:i',
                'ca_release_status' => 'nullable|in:pending,release',
                'ca_release_date'  => 'nullable|date',
            ], [
                'id_jo_cont.required'    => 'Job Order is required',
                'id_jo_cont.exists'      => 'Selected Job Order does not exist',
                'id_md_dep.required'     => 'Departemen is required',
                'id_md_cabang.required'  => 'Branch is required',
                'id_md_release.required' => 'Release To is required',
                'tgl_kasbon.required'    => 'Cash Advance Date is required',
                'priority.required'      => 'Priority is required',
            ]);

            if ($validator->fails()) {
                Log::error('KasbonContract Header Validation Failed', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Samakan dengan JoContract: pakai generateDocNo
            $idKasbonCont = IdGenerator::generateCaNo('c01_kasbon_cont', 'id_kasbon_cont');

            $lastKasbon = KasbonContract::orderBy('id', 'desc')->first();
            $newNomor   = $lastKasbon ? $lastKasbon->nomor + 1 : 1;

            $caReleaseStatus = $request->ca_release_status ?: 'pending';

            $kasbonContract = KasbonContract::create([
                'id_kasbon_cont'    => $idKasbonCont,
                'id_jo_cont'        => $request->id_jo_cont,
                'id_md_dep'         => $request->id_md_dep,
                'id_md_cabang'      => $request->id_md_cabang,
                'id_md_release'     => $request->id_md_release,
                'nomor'             => $newNomor,
                'tgl_kasbon'        => $request->tgl_kasbon,
                'tgl_release'       => $request->tgl_release,
                'note'              => $request->note,
                'priority'          => $request->priority ?? 'normal',
                'due_date'          => $request->due_date,
                'ca_release_status' => $caReleaseStatus,
                'ca_release_date'   => $caReleaseStatus === 'release' ? $request->ca_release_date : null,
            ]);

            DB::commit();

            $kasbonContract->load(['joContract', 'departemen', 'cabang', 'release']);

            Log::info('KasbonContract Header Created', ['id' => $kasbonContract->id, 'id_kasbon_cont' => $idKasbonCont]);

            return response()->json([
                'success'      => true,
                'message'      => 'Kasbon Contract header saved successfully',
                'redirect_url' => route('kasbon-contract.edit', $kasbonContract->id),
                'data'         => [
                    'id'                => $kasbonContract->id,
                    'id_kasbon_cont'    => $kasbonContract->id_kasbon_cont,
                    'id_jo_cont'        => $kasbonContract->id_jo_cont,
                    'id_md_dep'         => $kasbonContract->id_md_dep,
                    'id_md_cabang'      => $kasbonContract->id_md_cabang,
                    'id_md_release'     => $kasbonContract->id_md_release,
                    'nomor'             => $kasbonContract->nomor,
                    'tgl_kasbon'        => $kasbonContract->tgl_kasbon,
                    'tgl_release'       => $kasbonContract->tgl_release,
                    'note'              => $kasbonContract->note,
                    'ca_release_status' => $kasbonContract->ca_release_status,
                    'ca_release_date'   => $kasbonContract->ca_release_date,
                    'joContract'        => $kasbonContract->joContract,
                    'departemen'        => $kasbonContract->departemen,
                    'cabang'            => $kasbonContract->cabang,
                    'release'           => $kasbonContract->release,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonContract Store Header Failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save Kasbon Contract header',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // UPDATE HEADER (Realtime Auto-save AJAX)
    // ========================================

    public function updateHeader(Request $request, $id)
    {
        Log::info('=== KasbonContract Update Header START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_jo_cont'        => 'required|exists:b01_jo_cont,id_jo_cont',
                'id_md_dep'         => 'required|exists:a08_md_dep,id_md_dep',
                'id_md_cabang'      => 'required|exists:a09_md_branch,id_md_branch',
                'id_md_release'     => 'required|exists:a10_md_release_to,id_md_release',
                'tgl_kasbon'        => 'required|date',
                'tgl_release'       => 'nullable|date',
                'note'              => 'nullable|string',
                'priority'          => 'required|in:urgent,high,normal',
                'due_date'          => 'nullable|date_format:Y-m-d\TH:i',
                'ca_release_status' => 'nullable|in:pending,release',
                'ca_release_date'   => 'nullable|date',
            ], [
                'id_jo_cont.required'    => 'Job Order is required',
                'id_md_dep.required'     => 'Departemen is required',
                'id_md_cabang.required'  => 'Branch is required',
                'id_md_release.required' => 'Release To is required',
                'tgl_kasbon.required'    => 'Cash Advance Date is required',
                'priority.required'      => 'Priority is required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $kasbonContract = KasbonContract::findOrFail($id);

            $caReleaseStatus = $request->ca_release_status ?: 'pending';

            $kasbonContract->update([
                'id_jo_cont'        => $request->id_jo_cont,
                'id_md_dep'         => $request->id_md_dep,
                'id_md_cabang'      => $request->id_md_cabang,
                'id_md_release'     => $request->id_md_release,
                'tgl_kasbon'        => $request->tgl_kasbon,
                'tgl_release'       => $request->tgl_release,
                'note'              => $request->note,
                'priority'          => $request->priority,
                'due_date'          => $request->due_date ?: null,
                'ca_release_status' => $caReleaseStatus,
                'ca_release_date'   => $caReleaseStatus === 'release' ? ($request->ca_release_date ?: null) : null,
            ]);

            DB::commit();

            $kasbonContract->load(['joContract', 'departemen', 'cabang', 'release']);

            Log::info('KasbonContract Header Updated', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Kasbon Contract header updated successfully',
                'data'    => $kasbonContract
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonContract Update Header Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Kasbon Contract header',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // STORE ITEM (Realtime Auto-save AJAX)
    // ========================================

    public function storeItem(Request $request)
    {
        Log::info('=== KasbonContract Store Item START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_kasbon_cont'  => 'required|exists:c01_kasbon_cont,id_kasbon_cont',
                'id_jo_cont_item' => 'required|exists:b02_jo_cont_item,id_jo_cont_item',
                'nilai_kasbon'    => 'required|numeric|min:0',
            ], [
                'id_kasbon_cont.required'  => 'Kasbon Contract is required',
                'id_jo_cont_item.required' => 'Job Order Item is required',
                'nilai_kasbon.required'    => 'Advance Number Amount is required',
            ]);

            if ($validator->fails()) {
                Log::error('KasbonContract Item Validation Failed', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Generate ID dengan locking untuk mencegah race condition
                $maxId = DB::table('c02_kasbon_cont_item')
                    ->lockForUpdate()
                    ->selectRaw('MAX(CAST(id_kasbon_cont_item AS UNSIGNED)) as max_id')
                    ->value('max_id');
                $newIdKasbonContItem = (string)(((int) $maxId) + 1);

                // Ambil nilai HPP dari JO Contract Item
                $joContItem = JoContractItem::find($request->id_jo_cont_item);
                $nilaiHpp   = $joContItem ? (float)$joContItem->hpp_ops : 0;

                $nilaiKasbon  = (float)$request->nilai_kasbon;

                // Total kasbon: nilai HPP dikurangi nilai kasbon (advance)
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                $item = KasbonContractItem::create([
                    'id_kasbon_cont_item' => $newIdKasbonContItem,
                    'id_kasbon_cont'      => $request->id_kasbon_cont,
                    'id_jo_cont_item'     => $request->id_jo_cont_item,
                    'nilai_hpp_cont_item' => $nilaiHpp,
                    'nilai_kasbon'        => $nilaiKasbon,
                    'total_kasbon'        => $totalKasbon,
                ]);

                $item->load('joContractItem');

                DB::commit();

                Log::info('KasbonContract Item Created', ['id' => $item->id, 'id_kasbon_cont_item' => $item->id_kasbon_cont_item]);

                return response()->json([
                    'success' => true,
                    'message' => 'Item added successfully',
                    'data'    => [
                        'id'                  => $item->id,
                        'id_kasbon_cont_item' => $item->id_kasbon_cont_item,
                        'id_kasbon_cont'      => $item->id_kasbon_cont,
                        'id_jo_cont_item'     => $item->id_jo_cont_item,
                        'nilai_hpp_cont_item' => (float)$item->nilai_hpp_cont_item,
                        'nilai_kasbon'        => (float)$item->nilai_kasbon,
                        'total_kasbon'        => (float)$item->total_kasbon,
                        'jo_cont_item'        => $item->joContractItem,
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

            Log::error('KasbonContract Store Item Failed', [
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
        Log::info('=== KasbonContract Update Item START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_jo_cont_item' => 'required|exists:b02_jo_cont_item,id_jo_cont_item',
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

            $item = KasbonContractItem::findOrFail($id);

            // Ambil nilai HPP dari JO Contract Item (bisa saja berubah)
            $joContItem = JoContractItem::find($request->id_jo_cont_item);
            $nilaiHpp   = $joContItem ? (float)$joContItem->hpp_ops : (float)$item->nilai_hpp_cont_item;

            $nilaiKasbon = (float)$request->nilai_kasbon;
            $totalKasbon = $nilaiHpp - $nilaiKasbon;

            $item->update([
                'id_jo_cont_item'     => $request->id_jo_cont_item,
                'nilai_hpp_cont_item' => $nilaiHpp,
                'nilai_kasbon'        => $nilaiKasbon,
                'total_kasbon'        => $totalKasbon,
            ]);

            $item->refresh();
            $item->load('joContractItem');

            DB::commit();

            Log::info('KasbonContract Item Updated', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data'    => [
                    'id'                  => $item->id,
                    'id_kasbon_cont_item' => $item->id_kasbon_cont_item,
                    'id_kasbon_cont'      => $item->id_kasbon_cont,
                    'id_jo_cont_item'     => $item->id_jo_cont_item,
                    'nilai_hpp_cont_item' => (float)$item->nilai_hpp_cont_item,
                    'nilai_kasbon'        => (float)$item->nilai_kasbon,
                    'total_kasbon'        => (float)$item->total_kasbon,
                    'jo_cont_item'        => $item->joContractItem,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonContract Update Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
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
        Log::info('=== KasbonContract Delete Item START ===', ['id' => $id]);

        try {
            DB::beginTransaction();

            $item = KasbonContractItem::findOrFail($id);
            $item->delete();

            DB::commit();

            Log::info('KasbonContract Item Deleted', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonContract Delete Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
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

    public function getItems($kasbonContractId)
    {
        Log::info('=== KasbonContract Get Items START ===', ['kasbon_contract_id' => $kasbonContractId]);

        try {
            $items = KasbonContractItem::where('id_kasbon_cont', $kasbonContractId)
                ->with('joContractItem')
                ->orderBy('created_at')
                ->get();

            $formattedItems = $items->map(function ($item) {
                return [
                    'id'                  => $item->id,
                    'id_kasbon_cont_item' => $item->id_kasbon_cont_item,
                    'id_kasbon_cont'      => $item->id_kasbon_cont,
                    'id_jo_cont_item'     => $item->id_jo_cont_item,
                    'nilai_hpp_cont_item' => (float)$item->nilai_hpp_cont_item,
                    'nilai_kasbon'        => (float)$item->nilai_kasbon,
                    'total_kasbon'        => (float)$item->total_kasbon,
                    'jo_cont_item'        => $item->joContractItem,
                ];
            });

            Log::info('KasbonContract Items Retrieved', ['count' => $items->count()]);

            return response()->json([
                'success' => true,
                'data'    => $formattedItems
            ], 200);
        } catch (\Exception $e) {
            Log::error('KasbonContract Get Items Failed', ['kasbon_contract_id' => $kasbonContractId, 'error' => $e->getMessage()]);
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
            $item = KasbonContractItem::with('joContractItem')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'                  => $item->id,
                    'id_kasbon_cont_item' => $item->id_kasbon_cont_item,
                    'id_jo_cont_item'     => $item->id_jo_cont_item,
                    'nilai_hpp_cont_item' => (float)$item->nilai_hpp_cont_item,
                    'nilai_kasbon'        => (float)$item->nilai_kasbon,
                    'total_kasbon'        => (float)$item->total_kasbon,
                    'jo_cont_item'        => $item->joContractItem,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('KasbonContract Show Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
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
        Log::info('KasbonContract Store Request', ['all_data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'id_jo_cont'              => 'required|exists:b01_jo_cont,id_jo_cont',
            'id_md_dep'               => 'required|exists:a08_md_dep,id_md_dep',
            'id_md_cabang'            => 'required|exists:a09_md_branch,id_md_branch',
            'id_md_release'           => 'required|exists:a10_md_release_to,id_md_release',
            'tgl_kasbon'              => 'required|date',
            'tgl_release'             => 'nullable|date',
            'note'                    => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.id_jo_cont_item' => 'required|exists:b02_jo_cont_item,id_jo_cont_item',
            'items.*.nilai_kasbon'    => 'required|numeric|min:0',
            'priority'                => 'required|in:high,normal',
            'due_date'                => 'nullable|date_format:Y-m-d\TH:i',
            'ca_release_status'       => 'nullable|in:pending,release',
            'ca_release_date'         => 'nullable|date',
        ], [
            'id_jo_cont.required'    => 'Job Order is required',
            'id_md_dep.required'     => 'Departemen is required',
            'id_md_cabang.required'  => 'Branch is required',
            'id_md_release.required' => 'Release To is required',
            'priority.required'      => 'Priority is required',
            'tgl_kasbon.required'    => 'Cash Advance Date is required',
            'items.required'         => 'At least one item is required',
            'items.min'              => 'At least one item is required',
        ]);

        if ($validator->fails()) {
            Log::error('KasbonContract Validation Failed', ['errors' => $validator->errors()->toArray()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $lastKasbon      = KasbonContract::orderBy('id', 'desc')->first();
            $newNomor        = $lastKasbon ? $lastKasbon->nomor + 1 : 1;
            $idKasbonCont    = IdGenerator::generateDocNo('c01_kasbon_cont', 'id_kasbon_cont');
            $caReleaseStatus = $request->ca_release_status ?: 'pending';

            $kasbonContract = KasbonContract::create([
                'id_kasbon_cont'    => $idKasbonCont,
                'id_jo_cont'        => $request->id_jo_cont,
                'id_md_dep'         => $request->id_md_dep,
                'id_md_cabang'      => $request->id_md_cabang,
                'id_md_release'     => $request->id_md_release,
                'nomor'             => $newNomor,
                'tgl_kasbon'        => $request->tgl_kasbon,
                'tgl_release'       => $request->tgl_release,
                'note'              => $request->note,
                'ca_release_status' => $caReleaseStatus,
                'ca_release_date'   => $caReleaseStatus === 'release' ? $request->ca_release_date : null,
            ]);

            foreach ($request->items as $index => $itemData) {
                $joContItem = JoContractItem::find($itemData['id_jo_cont_item']);
                $nilaiHpp   = $joContItem ? (float)$joContItem->hpp_ops : 0;

                $nilaiKasbon = (float)$itemData['nilai_kasbon'];
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                KasbonContractItem::create([
                    'id_kasbon_cont'      => $idKasbonCont,
                    'id_jo_cont_item'     => $itemData['id_jo_cont_item'],
                    'nilai_hpp_cont_item' => $nilaiHpp,
                    'nilai_kasbon'        => $nilaiKasbon,
                    'total_kasbon'        => $totalKasbon,
                ]);
            }

            DB::commit();

            Log::info('KasbonContract Store Success', ['id' => $kasbonContract->id, 'total_items' => count($request->items)]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kasbon Contract successfully added',
                    'data'    => $kasbonContract->load('items')
                ], 201);
            }

            return redirect()
                ->route('kasbon-contract.index')
                ->with('success', 'Kasbon Contract successfully added with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonContract Store Failed', [
                'error_message' => $e->getMessage(),
                'error_line'    => $e->getLine(),
                'error_file'    => $e->getFile(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating Kasbon Contract: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Error creating Kasbon Contract: ' . $e->getMessage());
        }
    }

    // ========================================
    // SHOW
    // ========================================

    public function show(Request $request, $id)
    {
        Log::info('=== KasbonContract Show START ===', ['id' => $id]);

        try {
            $kasbonContract = KasbonContract::with([
                'joContract',
                'departemen',
                'cabang',
                'release',
                'items.joContractItem',
            ])->findOrFail($id);

            $summary = [
                'total_items'        => $kasbonContract->items->count(),
                'total_hpp'          => $kasbonContract->items->sum('nilai_hpp_cont_item'),
                'total_nilai_kasbon' => $kasbonContract->items->sum('nilai_kasbon'),
                'total_kasbon'       => $kasbonContract->items->sum('total_kasbon'),
            ];

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $kasbonContract, 'summary' => $summary]);
            }

            if (!view()->exists('data.kasbon-contract.show')) {
                return back()->with('error', 'View file not found: data.kasbon-contract.show');
            }

            return view('data.kasbon-contract.show', compact('kasbonContract', 'summary'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('KasbonContract Not Found', ['id' => $id]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kasbon Contract not found'], 404);
            }
            return back()->with('error', 'Kasbon Contract not found');
        } catch (\Exception $e) {
            Log::error('KasbonContract Show Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error loading Kasbon Contract: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error loading Kasbon Contract: ' . $e->getMessage());
        }
    }

    // ========================================
    // EDIT
    // ========================================

    public function edit($id)
    {
        $kasbonContract = KasbonContract::with([
            'items.joContractItem.invoice'
        ])->findOrFail($id);

        // Ambil semua JO Contract Items berdasarkan JO yang dipilih
        $joContractItems = JoContractItem::where('id_jo_cont', $kasbonContract->id_jo_cont)
            ->with('invoice')
            ->get();

        // Map: gabungkan jo_cont_item dengan kasbon_item yang sudah ada
        $mergedItems = $joContractItems->map(function ($joItem) use ($kasbonContract) {
            $kasbonItem = $kasbonContract->items
                ->firstWhere('id_jo_cont_item', $joItem->id_jo_cont_item);

            return [
                'id_jo_cont_item'     => $joItem->id_jo_cont_item,
                'invoice_typ'         => $joItem->invoice->invoice_typ ?? $joItem->id_jo_cont_item,
                'invoice_ctg'         => $joItem->invoice->invoice_ctg ?? '-',
                'hargajual_idr'       => (float) $joItem->hargajual_idr,
                'hpp_ops'             => (float) $joItem->hpp_ops,
                // dari kasbon item (jika sudah ada)
                'id_kasbon_cont_item' => $kasbonItem?->id ?? null,
                'nilai_hpp_cont_item' => $kasbonItem ? (float) $kasbonItem->nilai_hpp_cont_item : (float) $joItem->hpp_ops,
                'nilai_kasbon'        => $kasbonItem ? (float) $kasbonItem->nilai_kasbon : 0,
                'total_kasbon'        => $kasbonItem ? (float) $kasbonItem->total_kasbon : (float) $joItem->hpp_ops,
                'has_kasbon'          => $kasbonItem !== null,
                'origin_lpj_cont'     => $joItem->origin_lpj_cont ?? null,
            ];
        });

        $joContracts = JoContract::orderBy('no_jo_cont')->get();
        $departemens = Departemen::orderBy('nama_dep')->get();
        $cabangs     = Branch::orderBy('nama_branch')->get();
        $releases    = ReleaseTo::orderBy('id_md_release')->get();

        return view('data.kasbon-contract.edit', compact(
            'kasbonContract',
            'joContracts',
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
        Log::info('KasbonContract Update Request', ['id' => $id, 'all_data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'id_jo_cont'              => 'required|exists:b01_jo_cont,id_jo_cont',
            'id_md_dep'               => 'required|exists:a08_md_dep,id_md_dep',
            'id_md_cabang'            => 'required|exists:a09_md_branch,id_md_branch',
            'id_md_release'           => 'required|exists:a10_md_release_to,id_md_release',
            'tgl_kasbon'              => 'required|date',
            'tgl_release'             => 'nullable|date',
            'note'                    => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.id_jo_cont_item' => 'required|exists:b02_jo_cont_item,id_jo_cont_item',
            'items.*.nilai_kasbon'    => 'required|numeric|min:0',
            'priority'                => 'required|in:high,normal',
            'due_date'                => 'nullable|date_format:Y-m-d\TH:i',
            'ca_release_status'       => 'nullable|in:pending,release',
            'ca_release_date'         => 'nullable|date',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $kasbonContract  = KasbonContract::findOrFail($id);
            $caReleaseStatus = $request->ca_release_status ?: 'pending';

            $kasbonContract->update([
                'id_jo_cont'        => $request->id_jo_cont,
                'id_md_dep'         => $request->id_md_dep,
                'id_md_cabang'      => $request->id_md_cabang,
                'id_md_release'     => $request->id_md_release,
                'tgl_kasbon'        => $request->tgl_kasbon,
                'tgl_release'       => $request->tgl_release,
                'note'              => $request->note,
                'ca_release_status' => $caReleaseStatus,
                'ca_release_date'   => $caReleaseStatus === 'release' ? $request->ca_release_date : null,
            ]);

            // Hapus item lama (soft delete)
            foreach ($kasbonContract->items as $oldItem) {
                $oldItem->delete();
            }

            // Buat item baru
            foreach ($request->items as $itemData) {
                $joContItem = JoContractItem::find($itemData['id_jo_cont_item']);
                $nilaiHpp   = $joContItem ? (float)$joContItem->hpp_ops : 0;

                $nilaiKasbon = (float)$itemData['nilai_kasbon'];
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                KasbonContractItem::create([
                    'id_kasbon_cont'      => $kasbonContract->id_kasbon_cont,
                    'id_jo_cont_item'     => $itemData['id_jo_cont_item'],
                    'nilai_hpp_cont_item' => $nilaiHpp,
                    'nilai_kasbon'        => $nilaiKasbon,
                    'total_kasbon'        => $totalKasbon,
                ]);
            }

            DB::commit();

            Log::info('KasbonContract Update Success', ['id' => $id]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kasbon Contract successfully updated',
                    'data'    => $kasbonContract->fresh()->load('items')
                ]);
            }

            return redirect()
                ->route('kasbon-contract.index')
                ->with('success', 'Kasbon Contract successfully updated with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonContract Update Failed', ['id' => $id, 'error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating Kasbon Contract: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Error updating Kasbon Contract: ' . $e->getMessage());
        }
    }

    // ========================================
    // DESTROY
    // ========================================

    public function destroy(Request $request, $id)
    {
        Log::info('=== KasbonContract Delete START ===', ['id' => $id]);

        DB::beginTransaction();
        try {
            $kasbonContract = KasbonContract::findOrFail($id);

            // ── Cek apakah kasbon ini sudah dipakai di LPJ ──────────────
            $usedInLpj = \App\Models\Data\LpjKasbon::where('id_kasbon_cont', $kasbonContract->id_kasbon_cont)
                ->exists();

            if ($usedInLpj) {
                DB::rollBack();

                // Ambil nomor LPJ yang memakai kasbon ini untuk pesan yang lebih informatif
                $lpjNumbers = \App\Models\Data\LpjKasbon::where('id_kasbon_cont', $kasbonContract->id_kasbon_cont)
                    ->with('lpjContract:id,id_lpj_cont,no_lpj_cont')
                    ->get()
                    ->map(fn($lk) => $lk->lpjContract?->no_lpj_cont ?? $lk->id_lpj_cont)
                    ->filter()
                    ->unique()
                    ->implode(', ');

                $message = "Cannot delete Cash Advance \"{$kasbonContract->id_kasbon_cont}\" because it is already used in LPJ: {$lpjNumbers}. "
                    . "Please remove it from the related LPJ first before deleting.";

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            // ── Cek apakah ada kasbon item yang origin-nya dari LPJ ─────
            $hasLpjOriginItems = $kasbonContract->items()
                ->whereNotNull('origin_lpj_cont')
                ->exists();

            if ($hasLpjOriginItems) {
                DB::rollBack();

                $message = "Cannot delete Cash Advance \"{$kasbonContract->id_kasbon_cont}\" because some of its items were created from an LPJ entry. "
                    . "Please delete the related LPJ items first.";

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            // ── Aman untuk dihapus ───────────────────────────────────────
            $itemsCount = $kasbonContract->items()->count();

            foreach ($kasbonContract->items as $item) {
                $item->delete();
            }

            $kasbonContract->delete();

            DB::commit();

            Log::info('KasbonContract Deleted', ['id' => $id, 'deleted_items_count' => $itemsCount]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cash Advance deleted successfully along with ' . $itemsCount . ' item(s)',
                ]);
            }

            return redirect()
                ->route('kasbon-contract.index')
                ->with('success', "Cash Advance deleted successfully along with {$itemsCount} item(s)");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Cash Advance not found'], 404);
            }
            return back()->with('error', 'Cash Advance not found');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('KasbonContract Delete FK Constraint', ['id' => $id, 'error' => $e->getMessage()]);

            if ($e->getCode() === '23000') {
                $message = $this->resolveFkDeleteMessage($e->getMessage());
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete Cash Advance. Please try again.'], 500);
            }
            return back()->with('error', 'Failed to delete Cash Advance. Please try again.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KasbonContract Delete Failed', ['id' => $id, 'error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete Cash Advance. Please try again.'], 500);
            }
            return back()->with('error', 'Failed to delete Cash Advance. Please try again.');
        }
    }

    // ========================================
    // GET FOR SELECT (API dropdown)
    // ========================================

    public function getForSelect(Request $request)
    {
        try {
            $query = KasbonContract::with(['joContract', 'departemen']);

            if ($request->filled('id_jo_cont')) {
                $query->where('id_jo_cont', $request->id_jo_cont);
            }
            if ($request->filled('id_md_dep')) {
                $query->where('id_md_dep', $request->id_md_dep);
            }

            $kasbonContracts = $query->orderBy('id', 'desc')
                ->get()
                ->map(function ($kasbon) {
                    return [
                        'id'             => $kasbon->id,
                        'id_kasbon_cont' => $kasbon->id_kasbon_cont,
                        'text'           => $kasbon->id_kasbon_cont . ' - ' . ($kasbon->joContract->no_jo_cont ?? '-'),
                        'jo_contract'    => $kasbon->joContract ? $kasbon->joContract->no_jo_cont : null,
                        'departemen'     => $kasbon->departemen ? $kasbon->departemen->nama_dep : null,
                    ];
                });

            return response()->json(['success' => true, 'data' => $kasbonContracts]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving kasbon contracts: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // GET JO CONTRACT ITEMS BY JO CONT (API)
    // ========================================

    public function getJoContractItems(Request $request)
    {
        try {
            $idJoCont = $request->get('id_jo_cont');

            if (!$idJoCont) {
                return response()->json(['success' => false, 'message' => 'id_jo_cont is required'], 422);
            }

            $items = JoContractItem::where('id_jo_cont', $idJoCont)
                ->with('invoice')
                ->get()
                ->map(function ($item) {
                    return [
                        'id_jo_cont_item' => $item->id_jo_cont_item,
                        'text'            => $item->invoice ? $item->invoice->invoice_typ : $item->id_jo_cont_item,
                        'invoice_typ'     => $item->invoice ? $item->invoice->invoice_typ : null,
                        'invoice_ctg'     => $item->invoice ? $item->invoice->invoice_ctg : null,
                        'hpp_ops'         => (float)$item->hpp_ops,
                        'hargajual_idr'   => (float)$item->hargajual_idr,
                    ];
                });

            return response()->json(['success' => true, 'data' => $items]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving JO contract items: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK DELETE
    // ========================================

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:c01_kasbon_cont,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $kasbonContracts = KasbonContract::whereIn('id', $request->ids)->get();

            foreach ($kasbonContracts as $kasbon) {
                foreach ($kasbon->items as $item) {
                    $item->delete();
                }
                $kasbon->delete();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Kasbon Contracts successfully deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting kasbon contracts: ' . $e->getMessage()], 500);
        }
    }

    public function bulkSaveItems(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'items'                   => 'required|array',
            'items.*.id_jo_cont_item' => 'required|exists:b02_jo_cont_item,id_jo_cont_item',
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
            $kasbonContract = KasbonContract::findOrFail($id);

            DB::beginTransaction();

            foreach ($request->items as $itemData) {
                $joContItem  = JoContractItem::find($itemData['id_jo_cont_item']);
                $nilaiHpp    = $joContItem ? (float) $joContItem->hpp_ops : 0;
                $nilaiKasbon = (float) $itemData['nilai_kasbon'];
                $totalKasbon = $nilaiHpp - $nilaiKasbon;

                // Upsert: update jika sudah ada, insert jika belum
                $existing = KasbonContractItem::where('id_kasbon_cont', $kasbonContract->id_kasbon_cont)
                    ->where('id_jo_cont_item', $itemData['id_jo_cont_item'])
                    ->first();

                if ($existing) {
                    $existing->update([
                        'nilai_hpp_cont_item' => $nilaiHpp,
                        'nilai_kasbon'        => $nilaiKasbon,
                        'total_kasbon'        => $totalKasbon,
                    ]);
                } else {
                    // Hanya buat record baru jika nilai_kasbon > 0
                    if ($nilaiKasbon > 0) {
                        $maxId = DB::table('c02_kasbon_cont_item')
                            ->lockForUpdate()
                            ->selectRaw('MAX(CAST(id_kasbon_cont_item AS UNSIGNED)) as max_id')
                            ->value('max_id');
                        KasbonContractItem::create([
                            'id_kasbon_cont_item' => (string)(((int) $maxId) + 1),
                            'id_kasbon_cont'      => $kasbonContract->id_kasbon_cont,
                            'id_jo_cont_item'     => $itemData['id_jo_cont_item'],
                            'nilai_hpp_cont_item' => $nilaiHpp,
                            'nilai_kasbon'        => $nilaiKasbon,
                            'total_kasbon'        => $totalKasbon,
                        ]);
                    }
                }
            }

            DB::commit();

            // Hitung ulang total
            $totals = KasbonContractItem::where('id_kasbon_cont', $kasbonContract->id_kasbon_cont)
                ->selectRaw('SUM(nilai_hpp_cont_item) as total_hpp, SUM(nilai_kasbon) as total_kasbon, SUM(total_kasbon) as total_remaining')
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Items saved successfully',
                'totals'  => $totals,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('bulkSaveItems failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save items: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf($id)
    {
        try {
            $kasbonContract = KasbonContract::with([
                'joContract.area',
                'joContract.items.invoice',   // seluruh JO items (sumber baris tabel)
                'departemen',
                'cabang',
                'release',
                'items',                      // kasbon items (lookup nilai_kasbon)
            ])->findOrFail($id);

            // Hitung total CA persis seperti footerTotalCA di edit view:
            // iterasi joContract->items, join ke kasbonLookup by id_jo_cont_item
            $kasbonLookup = $kasbonContract->items->keyBy('id_jo_cont_item');
            $joItems      = $kasbonContract->joContract
                ? $kasbonContract->joContract->items
                : collect();

            $totalCA = $joItems->sum(function ($joItem) use ($kasbonLookup) {
                $k = $kasbonLookup->get($joItem->id_jo_cont_item);
                return $k ? (float) $k->nilai_kasbon : 0;
            });

            $terbilang = $this->toTerbilang((int) round($totalCA)) . ' Rupiah';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'data.kasbon-contract.pdf',   // resources/views/data/kasbon-contract/pdf.blade.php
                compact('kasbonContract', 'terbilang')
            )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'Arial',
                    'dpi'                  => 150,
                ]);

            $filename = 'Kasbon-Contract-'
                . str_replace(['/', '\\'], '-', $kasbonContract->id_kasbon_cont ?? $id)
                . '.pdf';

            return $pdf->stream($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kasbon Contract not found'], 404);
            }
            return back()->with('error', 'Kasbon Contract not found');
        } catch (\Exception $e) {
            Log::error('Kasbon Contract Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
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
        $format          = $request->get('format', 'excel');
        $kasbonContracts = $this->buildExportQuery($request)->get();

        if ($format === 'pdf') {
            $filters = $this->activeFilters($request);

            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                    'data.kasbon-contract.export_pdf',
                    compact('kasbonContracts', 'filters')
                )->setPaper('a4', 'landscape')->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'Arial',
                    'dpi'                  => 150,
                ]);
                return $pdf->stream('kasbon_contract_' . date('Ymd_His') . '.pdf');
            }

            $html = view('data.kasbon-contract.export_pdf', compact('kasbonContracts', 'filters'))->render();
            return response($html, 200, [
                'Content-Type'        => 'text/html; charset=UTF-8',
                'Content-Disposition' => 'inline; filename="kasbon_contract_' . date('Ymd_His') . '.pdf"',
            ]);
        }

        return (new \App\Exports\Data\KasbonContractExport($kasbonContracts))->download();
    }

    private function buildExportQuery(Request $request)
    {
        $query = KasbonContract::with(['joContract', 'departemen', 'cabang', 'release', 'items']);

        if ($request->filled('id_jo_cont'))      $query->where('id_jo_cont', $request->id_jo_cont);
        if ($request->filled('id_md_dep'))       $query->where('id_md_dep', $request->id_md_dep);
        if ($request->filled('id_md_cabang'))    $query->where('id_md_cabang', $request->id_md_cabang);
        if ($request->filled('tgl_kasbon_from')) $query->where('tgl_kasbon', '>=', $request->tgl_kasbon_from);
        if ($request->filled('tgl_kasbon_to'))   $query->where('tgl_kasbon', '<=', $request->tgl_kasbon_to);

        return $query->orderBy('tgl_kasbon', 'desc')->orderBy('id', 'desc');
    }

    private function activeFilters(Request $request): array
    {
        $filters = [];

        if ($request->filled('id_jo_cont')) {
            $jo = \App\Models\Data\JoContract::find($request->id_jo_cont);
            $filters['jo_label'] = $jo ? $jo->no_jo_cont : $request->id_jo_cont;
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

    // ========================================
    // CHECK ITEM CONFLICT (dipakai sebelum bulk save)
    // ========================================
    public function checkItemConflict(Request $request)
    {
        try {
            $idJoContItem = $request->get('id_jo_cont_item');
            $idKasbonCont = $request->get('id_kasbon_cont'); // kasbon yang sedang diedit (exclude ini)

            if (!$idJoContItem) {
                return response()->json(['success' => false, 'message' => 'id_jo_cont_item is required'], 422);
            }

            // Cari kasbon item lain yang punya jo_cont_item ini,
            // bukan dari kasbon yang sedang diedit, dan nilai_kasbon > 0
            $conflict = KasbonContractItem::where('id_jo_cont_item', $idJoContItem)
                ->where('id_kasbon_cont', '!=', $idKasbonCont)
                ->where('nilai_kasbon', '>', 0)
                ->whereNull('deleted_at')
                ->with('kasbonContract')
                ->first();

            if ($conflict) {
                return response()->json([
                    'success'      => true,
                    'has_conflict' => true,
                    'conflict'     => [
                        'id_kasbon_cont'      => $conflict->id_kasbon_cont,
                        'nilai_kasbon'        => (float) $conflict->nilai_kasbon,
                        'id_kasbon_cont_item' => $conflict->id_kasbon_cont_item,
                    ]
                ]);
            }

            return response()->json([
                'success'      => true,
                'has_conflict' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // CLEAR CONFLICT ITEM (hapus nilai kasbon di kasbon lain)
    // ========================================
    public function clearConflictItem(Request $request)
    {
        try {
            $idJoContItem = $request->get('id_jo_cont_item');
            $idKasbonCont = $request->get('id_kasbon_cont'); // kasbon yang sedang diedit (exclude)

            if (!$idJoContItem) {
                return response()->json(['success' => false, 'message' => 'id_jo_cont_item is required'], 422);
            }

            DB::beginTransaction();

            // Nol-kan nilai kasbon di semua kasbon LAIN yang punya item ini
            KasbonContractItem::where('id_jo_cont_item', $idJoContItem)
                ->where('id_kasbon_cont', '!=', $idKasbonCont)
                ->where('nilai_kasbon', '>', 0)
                ->each(function ($item) {
                    $item->update([
                        'nilai_kasbon' => 0,
                        'total_kasbon' => $item->nilai_hpp_cont_item, // total = hpp - 0 = hpp
                    ]);
                });

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Conflict cleared successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('clearConflictItem failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function resolveFkDeleteMessage(string $rawError): string
    {
        $map = [
            'd01_lpj_cont'         => 'LPJ Contract',
            'd02_lpj_kasbon'       => 'LPJ Cash Advance',
            'd03_lpj_cont_item'    => 'LPJ Contract Item',
            'c02_kasbon_cont_item' => 'Cash Advance Item',
        ];

        foreach ($map as $table => $label) {
            if (str_contains($rawError, $table)) {
                return "Cannot delete this Cash Advance because it is still referenced by \"{$label}\" data. "
                    . "Please remove the related {$label} records first.";
            }
        }

        return 'Cannot delete this Cash Advance because it is still being used by other related data. '
            . 'Please remove all related records first before deleting.';
    }
}
