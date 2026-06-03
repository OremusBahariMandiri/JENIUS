<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Data\JoContract;
use App\Models\Data\JoContractItem;
use App\Models\Master\Contract;
use App\Models\Master\Area;
use App\Models\Master\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class JoContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Jocontract::with(['contract.customer', 'area']);

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id_jo_cont', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('contract', function ($q) use ($search) {
                            $q->where('no_contract', 'like', "%{$search}%")
                                ->orWhere('contract', 'like', "%{$search}%");
                        })
                        ->orWhereHas('area', function ($q) use ($search) {
                            $q->where('area', 'like', "%{$search}%");
                        });
                });
            }

            // Filter by contract
            if ($request->has('id_md_cont') && !empty($request->id_md_cont)) {
                $query->where('id_md_cont', $request->id_md_cont);
            }

            // Filter by area
            if ($request->has('id_md_area') && !empty($request->id_md_area)) {
                $query->where('id_md_area', $request->id_md_area);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $joContracts = $query->paginate($perPage);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $joContracts
                ]);
            }

            // Get contracts and areas for filter
            $contracts = Contract::with('customer')
                ->orderBy('no_contract')
                ->get();
            $areas = Area::orderBy('area')->get();

            return view('data.jo-contract.index', compact('joContracts', 'contracts', 'areas'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving JO contracts: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving JO contracts: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contracts = Contract::with('customer')
            ->orderBy('no_contract')
            ->get();
        $areas = Area::orderBy('area')->get();
        $invoices = Invoice::orderBy('id')->get();

        return view('data.jo-contract.create', compact('contracts', 'areas', 'invoices'));
    }

    // ========================================
    // REALTIME AUTO-SAVE METHODS (NEW)
    // ========================================

    /**
     * Store JO Contract Header (Realtime Auto-save via AJAX)
     */
    public function storeHeader(Request $request)
    {
        Log::info('=== Store Header Request START ===', [
            'data' => $request->all()
        ]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_cont' => 'required|exists:a02_md_contract,id_md_cont',
                'id_md_area' => 'required|exists:a03_md_area,id_md_area',
                'title' => 'required|string|max:255',
                'note' => 'nullable|string',
            ], [
                'id_md_cont.required' => 'Contract is required',
                'id_md_cont.exists' => 'Selected contract does not exist',
                'id_md_area.required' => 'Area is required',
                'id_md_area.exists' => 'Selected area does not exist',
                'title.required' => 'Title is required',
            ]);

            if ($validator->fails()) {
                Log::error('Header Validation Failed', [
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // FIX: Generate ID dengan memperhitungkan soft deletes
            // Generate ID untuk JO Contract baru
            $lastJoContract = JoContract::orderBy('id_jo_cont', 'desc')
                ->first();

            $newJoContId = $lastJoContract ? $lastJoContract->id_jo_cont + 1 : 1;

            Log::info('Creating JO Contract Header', [
                'new_id' => $newJoContId
            ]);

            $joContract = JoContract::create([
                'id_jo_cont' => $newJoContId,
                'id_md_cont' => $request->id_md_cont,
                'id_md_area' => $request->id_md_area,
                'title' => $request->title,
                'note' => $request->note,
            ]);

            DB::commit();

            Log::info('Header Created Successfully', [
                'id' => $joContract->id_jo_cont
            ]);

            // Load relationships
            $joContract->load(['contract.customer', 'area']);

            return response()->json([
                'success' => true,
                'message' => 'JO Contract header saved successfully',
                'redirect_url' => route('jo-contract.edit', $joContract->id_jo_cont),
                'data' => [
                    'id' => $joContract->id_jo_cont,
                    'id_md_cont' => $joContract->id_md_cont,
                    'id_md_area' => $joContract->id_md_area,
                    'title' => $joContract->title,
                    'note' => $joContract->note,
                    'contract' => $joContract->contract,
                    'area' => $joContract->area,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Store Header Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save JO Contract header',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update JO Contract Header (Realtime Auto-save via AJAX)
     */
    public function updateHeader(Request $request, $id)
    {
        Log::info('=== Update Header Request START ===', [
            'id' => $id,
            'data' => $request->all()
        ]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_cont' => 'required|exists:a02_md_contract,id_md_cont',
                'id_md_area' => 'required|exists:a03_md_area,id_md_area',
                'title' => 'required|string|max:255',
                'note' => 'nullable|string',
                'global_tgl_kurs_usd' => 'required|date',
                'global_kurs_usd' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $joContract = JoContract::findOrFail($id);

            $joContract->update([
                'id_md_cont' => $request->id_md_cont,
                'id_md_area' => $request->id_md_area,
                'title' => $request->title,
                'note' => $request->note,
            ]);

            DB::commit();

            Log::info('Header Updated Successfully', [
                'id' => $joContract->id_jo_cont
            ]);

            $joContract->load(['contract.customer', 'area']);

            return response()->json([
                'success' => true,
                'message' => 'JO Contract header updated successfully',
                'data' => $joContract
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Update Header Failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update JO Contract header',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store JO Contract Item (Realtime Auto-save via AJAX)
     */
    /**
     * Store JO Contract Item (Realtime Auto-save via AJAX)
     * Manual ID Generation with Proper Locking
     */
    public function storeItem(Request $request)
    {
        Log::info('=== Store Item Request START ===', [
            'data' => $request->all()
        ]);

        try {
            // Conditional validation rules
            $rules = [
                'id_jo_cont' => 'required|exists:b01_jo_cont,id_jo_cont',
                'id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
                'invoice_ctg' => 'required|string',
                'pendapatan_idr' => 'nullable|numeric|min:0',
                'pendapatan_usd' => 'nullable|numeric|min:0',
                'hpp_ops' => 'nullable|numeric|min:0',
                'note' => 'nullable|string',
            ];

            // Di storeItem
            if ($request->pendapatan_usd && $request->pendapatan_usd > 0) {
                $rules['kurs_usd'] = 'required|numeric|min:0';
                $rules['tgl_kurs_usd'] = 'required|date_format:Y-m-d\TH:i'; // ← FIX: Format datetime-local
            } else {
                $rules['kurs_usd'] = 'nullable|numeric|min:0';
                $rules['tgl_kurs_usd'] = 'nullable|date_format:Y-m-d\TH:i'; // ← FIX: Format datetime-local
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                Log::error('Item Validation Failed', [
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                // ============================================================
                // PROPER ID GENERATION WITH LOCKING (CRITICAL FIX)
                // ============================================================

                // Lock the table for update to prevent race conditions
                $maxId = DB::table('b02_jo_cont_item')
                    ->lockForUpdate()
                    ->max('id_jo_cont_item');

                // Parse max ID
                if ($maxId === null) {
                    // Table is empty
                    $newId = 1;
                } else {
                    // Convert to integer and increment
                    $maxIdInt = is_numeric($maxId) ? (int)$maxId : 0;
                    $newId = $maxIdInt + 1;
                }

                Log::info('ID Generation', [
                    'max_id_from_db' => $maxId,
                    'max_id_type' => gettype($maxId),
                    'parsed_max_id' => $maxIdInt ?? 'N/A',
                    'new_id' => $newId,
                    'new_id_type' => gettype($newId)
                ]);

                // ============================================================
                // CALCULATE VALUES
                // ============================================================

                $pendapatanIDR = $request->pendapatan_idr ?? 0;
                $pendapatanUSD = $request->pendapatan_usd ?? 0;
                $kursUSD = $request->kurs_usd ?? 0;
                $hppOps = $request->hpp_ops ?? 0;

                // Calculate hargajual_idr based on formula
                $hargajualIDR = $pendapatanIDR > 0
                    ? $pendapatanIDR
                    : ($pendapatanUSD * $kursUSD);

                Log::info('Calculated Values', [
                    'pendapatan_idr' => $pendapatanIDR,
                    'pendapatan_usd' => $pendapatanUSD,
                    'kurs_usd' => $kursUSD,
                    'hpp_ops' => $hppOps,
                    'hargajual_idr' => $hargajualIDR
                ]);

                // ============================================================
                // CREATE ITEM WITH EXPLICIT ID
                // ============================================================

                // Determine if ID should be string or int based on model
                $model = new JoContractItem();
                $idValue = ($model->getKeyType() === 'string') ? (string)$newId : (int)$newId;

                Log::info('Creating Item', [
                    'id_value' => $idValue,
                    'id_type' => gettype($idValue),
                    'model_key_type' => $model->getKeyType()
                ]);

                $item = JoContractItem::create([
                    'id_jo_cont_item' => $idValue,
                    'id_jo_cont' => $request->id_jo_cont,
                    'id_md_invoice' => $request->id_md_invoice,
                    'pendapatan_idr' => $pendapatanIDR,
                    'pendapatan_usd' => $pendapatanUSD,
                    'kurs_usd' => $kursUSD,
                    'tgl_kurs_usd' => $request->tgl_kurs_usd,
                    'hpp_ops' => $hppOps,
                    'hargajual_idr' => $hargajualIDR,
                    'note' => $request->note,
                ]);

                // Load relationship
                $item->load('invoice');

                // Commit transaction
                DB::commit();

                Log::info('Item Created Successfully', [
                    'id_jo_cont_item' => $item->id_jo_cont_item,
                    'id_type' => gettype($item->id_jo_cont_item),
                    'complete_item' => $item->toArray()
                ]);

                // ============================================================
                // RETURN SUCCESS RESPONSE
                // ============================================================

                return response()->json([
                    'success' => true,
                    'message' => 'Item added successfully',
                    'data' => [
                        'id' => $item->id_jo_cont_item,
                        'id_jo_cont_item' => $item->id_jo_cont_item,
                        'id_jo_cont' => $item->id_jo_cont,
                        'id_md_invoice' => $item->id_md_invoice,
                        'invoice_ctg' => $request->invoice_ctg,
                        'invoice_typ' => $item->invoice ? $item->invoice->invoice_typ : null,
                        'pendapatan_idr' => (float) $item->pendapatan_idr,
                        'pendapatan_usd' => (float) $item->pendapatan_usd,
                        'kurs_usd' => $item->kurs_usd ? (float) $item->kurs_usd : null,
                        'tgl_kurs_usd' => $item->tgl_kurs_usd ? $item->tgl_kurs_usd->format('Y-m-d') : null,
                        'hpp_ops' => (float) $item->hpp_ops,
                        'hargajual_idr' => (float) $item->hargajual_idr,
                    ]
                ], 201);
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();

                // Check if it's a duplicate key error
                if ($e->getCode() === '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    Log::error('Duplicate Key Error - Retrying...', [
                        'error' => $e->getMessage()
                    ]);

                    // Retry once with a delay
                    sleep(1);

                    // Recursive call (only once)
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

            Log::error('Store Item Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add item',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update JO Contract Item (Realtime Auto-save via AJAX)
     */
    /**
     * Update JO Contract Item (Realtime Auto-save via AJAX)
     */
    public function updateItem(Request $request, $id)
    {
        Log::info('=== Update Item Request START ===', [
            'id' => $id,
            'data' => $request->all()
        ]);

        try {
            // Conditional validation rules
            $rules = [
                'id_jo_cont' => 'required|exists:b01_jo_cont,id_jo_cont',
                'id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
                'invoice_ctg' => 'required|string',
                'pendapatan_idr' => 'nullable|numeric|min:0',
                'pendapatan_usd' => 'nullable|numeric|min:0',
                'hpp_ops' => 'nullable|numeric|min:0',
                'note' => 'nullable|string|max:1000',
            ];

            // Di storeItem
            if ($request->pendapatan_usd && $request->pendapatan_usd > 0) {
                $rules['kurs_usd'] = 'required|numeric|min:0';
                $rules['tgl_kurs_usd'] = 'required'; // ← UBAH: Hapus date_format, biar lebih flexible
            } else {
                $rules['kurs_usd'] = 'nullable|numeric|min:0';
                $rules['tgl_kurs_usd'] = 'nullable';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $item = JoContractItem::findOrFail($id);

            // Calculate hargajual_idr
            $pendapatanIDR = $request->pendapatan_idr ?? 0;
            $pendapatanUSD = $request->pendapatan_usd ?? 0;
            $kursUSD = $request->kurs_usd ?? 0;
            $hppOps = $request->hpp_ops ?? 0;

            $hargajualIDR = $pendapatanIDR > 0
                ? $pendapatanIDR
                : ($pendapatanUSD * $kursUSD);

            $item->update([
                'id_md_invoice' => $request->id_md_invoice,
                'pendapatan_idr' => $pendapatanIDR,
                'pendapatan_usd' => $pendapatanUSD,
                'kurs_usd' => $kursUSD,
                'tgl_kurs_usd' => $request->tgl_kurs_usd ?? null,
                'hpp_ops' => $hppOps,
                'hargajual_idr' => $hargajualIDR,
                'note' => $request->note,
            ]);

            // Refresh and load relationship
            $item->refresh();
            $item->load('invoice');

            DB::commit();

            Log::info('Item Updated Successfully', [
                'id' => $item->id_jo_cont_item,
                'updated_data' => $item->toArray()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => [
                    'id' => $item->id_jo_cont_item,
                    'id_jo_cont_item' => $item->id_jo_cont_item,
                    'id_jo_cont' => $item->id_jo_cont,
                    'id_md_invoice' => $item->id_md_invoice,
                    'invoice_ctg' => $request->invoice_ctg,
                    'invoice_typ' => $item->invoice ? $item->invoice->invoice_typ : null,
                    'pendapatan_idr' => (float) $item->pendapatan_idr,
                    'pendapatan_usd' => (float) $item->pendapatan_usd,
                    'kurs_usd' => $item->kurs_usd ? (float) $item->kurs_usd : null,
                    'tgl_kurs_usd' => $request->tgl_kurs_usd,
                    'hpp_ops' => (float) $item->hpp_ops,
                    'hargajual_idr' => (float) $item->hargajual_idr,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Update Item Failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete JO Contract Item (Realtime Auto-delete via AJAX)
     */
    public function destroyItem($id)
    {
        Log::info('=== Delete Item Request START ===', [
            'id' => $id
        ]);

        try {
            DB::beginTransaction();

            $item = JoContractItem::findOrFail($id);
            $item->delete();

            DB::commit();

            Log::info('Item Deleted Successfully', [
                'id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Delete Item Failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all items for a JO Contract (For loading existing data)
     */
    public function getItems($joContractId)
    {
        Log::info('=== Get Items Request START ===', [
            'jo_contract_id' => $joContractId
        ]);

        try {
            $items = JoContractItem::where('id_jo_cont', $joContractId)
                ->with('invoice')
                ->orderBy('created_at')
                ->get();

            Log::info('Items Retrieved Successfully', [
                'count' => $items->count()
            ]);

            $formattedItems = $items->map(function ($item) {
                return [
                    'id' => $item->id_jo_cont_item,
                    'id_jo_cont' => $item->id_jo_cont,
                    'id_md_invoice' => $item->id_md_invoice,
                    'invoice_typ' => $item->invoice ? $item->invoice->invoice_typ : 'Unknown',
                    'invoice_ctg' => $item->invoice ? $item->invoice->invoice_ctg : 'Unknown',
                    'pendapatan_idr' => $item->pendapatan_idr,
                    'pendapatan_usd' => $item->pendapatan_usd,
                    'kurs_usd' => $item->kurs_usd,
                    'tgl_kurs_usd' => $item->tgl_kurs_usd,
                    'hpp_ops' => $item->hpp_ops,
                    'hargajual_idr' => $item->hargajual_idr,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedItems
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get Items Failed', [
                'jo_contract_id' => $joContractId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show single JO Contract Item (for edit form)
     */
    /**
     * Show single JO Contract Item (for edit form)
     */
    public function showItem($id)
    {
        try {
            $item = JoContractItem::with('invoice')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id_jo_cont_item' => $item->id_jo_cont_item,
                    'id_md_invoice' => $item->id_md_invoice,
                    'invoice_ctg' => $item->invoice->invoice_ctg,
                    'invoice_typ' => $item->invoice->invoice_typ,
                    'pendapatan_idr' => $item->pendapatan_idr,
                    'pendapatan_usd' => $item->pendapatan_usd,
                    'hpp_ops' => $item->hpp_ops,
                    'kurs_usd' => $item->kurs_usd,
                    // FIX: Return datetime format untuk input datetime-local
                    'tgl_kurs_usd' => $item->tgl_kurs_usd ? $item->tgl_kurs_usd->format('Y-m-d\TH:i') : null,
                    'hargajual_idr' => $item->hargajual_idr,
                    'note' => $item->note,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Show Item Failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Item not found: ' . $e->getMessage()
            ], 404);
        }
    }

    // ========================================
    // EXISTING METHODS (KEEP AS IS)
    // ========================================

    /**
     * Store a newly created resource in storage (TRADITIONAL - Keep for fallback)
     */
    public function store(Request $request)
    {
        // Log input data untuk debugging
        Log::info('JO Contract Store Request', [
            'all_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'id_md_cont' => 'required|exists:a02_md_contract,id_md_cont',
            'id_md_area' => 'required|exists:a03_md_area,id_md_area',
            'title' => 'required|string|max:255',
            'note' => 'nullable|string',

            // Global kurs validation
            'global_kurs_usd' => 'required|numeric|min:0',
            'global_tgl_kurs_usd' => 'required|date',

            // Validation untuk items
            'items' => 'required|array|min:1',
            'items.*.id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
            'items.*.invoice_ctg' => 'nullable|string',
            'items.*.pendapatan_idr' => 'required|numeric|min:0',
            'items.*.pendapatan_usd' => 'required|numeric|min:0',
            'items.*.hpp_ops' => 'required|numeric|min:0',
            'items.*.hargajual_idr' => 'required|numeric|min:0',
        ], [
            'id_md_cont.required' => 'Contract is required',
            'id_md_cont.exists' => 'Selected contract does not exist',
            'id_md_area.required' => 'Area is required',
            'id_md_area.exists' => 'Selected area does not exist',
            'title.required' => 'Title is required',
            'global_kurs_usd.required' => 'Exchange rate is required',
            'global_tgl_kurs_usd.required' => 'Exchange rate date is required',
            'items.required' => 'At least one item is required',
            'items.min' => 'At least one item is required',
            'items.*.id_md_invoice.required' => 'Invoice is required for each item',
            'items.*.id_md_invoice.exists' => 'Selected invoice does not exist',
            'items.*.pendapatan_idr.required' => 'Revenue IDR is required for each item',
            'items.*.pendapatan_usd.required' => 'Revenue USD is required for each item',
            'items.*.hpp_ops.required' => 'HPP Operational is required for each item',
            'items.*.hargajual_idr.required' => 'Selling price is required for each item',
        ]);

        if ($validator->fails()) {
            Log::error('JO Contract Validation Failed', [
                'errors' => $validator->errors()->toArray()
            ]);

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
            // Generate ID untuk JO Contract
            $lastJoContract = JoContract::orderBy('id_jo_cont', 'desc')->first();
            $newJoContId = $lastJoContract ? $lastJoContract->id_jo_cont + 1 : 1;

            Log::info('Creating JO Contract', [
                'new_id' => $newJoContId,
                'contract_data' => [
                    'id_md_cont' => $request->id_md_cont,
                    'id_md_area' => $request->id_md_area,
                    'title' => $request->title,
                    'note' => $request->note,
                ]
            ]);

            // Create JO Contract
            $joContract = JoContract::create([
                'id_jo_cont' => $newJoContId,
                'id_md_cont' => $request->id_md_cont,
                'id_md_area' => $request->id_md_area,
                'title' => $request->title,
                'note' => $request->note,
            ]);

            Log::info('JO Contract Created Successfully', [
                'jo_contract_id' => $joContract->id_jo_cont
            ]);

            // Get global kurs
            $globalKursUsd = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            // Create JO Contract Items - ID akan di-generate otomatis oleh Model
            foreach ($request->items as $index => $item) {
                Log::info('Creating JO Contract Item', [
                    'item_index' => $index,
                    'item_data' => [
                        'id_jo_cont' => $newJoContId,
                        'id_md_invoice' => $item['id_md_invoice'],
                        'pendapatan_idr' => $item['pendapatan_idr'],
                        'pendapatan_usd' => $item['pendapatan_usd'],
                        'kurs_usd' => $globalKursUsd,
                        'tgl_kurs_usd' => $globalTglKursUsd,
                        'hpp_ops' => $item['hpp_ops'],
                        'hargajual_idr' => $item['hargajual_idr'],
                    ]
                ]);

                // ID akan di-generate otomatis oleh boot() method di Model
                $createdItem = JoContractItem::create([
                    // TIDAK PERLU SET id_jo_cont_item, akan auto-generate
                    'id_jo_cont' => $newJoContId,
                    'id_md_invoice' => $item['id_md_invoice'],
                    'pendapatan_idr' => $item['pendapatan_idr'],
                    'pendapatan_usd' => $item['pendapatan_usd'],
                    'kurs_usd' => $globalKursUsd,
                    'tgl_kurs_usd' => $globalTglKursUsd,
                    'hpp_ops' => $item['hpp_ops'],
                    'hargajual_idr' => $item['hargajual_idr'],
                ]);

                Log::info('JO Contract Item Created', [
                    'item_id' => $createdItem->id_jo_cont_item
                ]);
            }

            DB::commit();

            Log::info('JO Contract Transaction Committed Successfully', [
                'jo_contract_id' => $newJoContId,
                'total_items' => count($request->items)
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Contract successfully added',
                    'data' => $joContract->load('items')
                ], 201);
            }

            return redirect()
                ->route('jo-contract.index')
                ->with('success', 'JO Contract successfully added with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('JO Contract Store Failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating JO contract: ' . $e->getMessage(),
                    'error_detail' => [
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating JO contract: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        Log::info('=== JO Contract Show Request START ===', [
            'id' => $id,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id() ?? 'guest',
            'ip' => $request->ip()
        ]);

        try {
            Log::info('Attempting to load JO Contract', [
                'id' => $id
            ]);

            $joContract = JoContract::with([
                'contract.customer',
                'area',
                'items.invoice'
            ])->findOrFail($id);

            Log::info('JO Contract loaded successfully', [
                'id' => $joContract->id_jo_cont,
                'title' => $joContract->title,
                'items_count' => $joContract->items->count()
            ]);

            // Calculate summary
            $summary = [
                'total_items' => $joContract->items->count(),
                'total_revenue_idr' => $joContract->items->sum('pendapatan_idr'),
                'total_revenue_usd' => $joContract->items->sum('pendapatan_usd'),
                'total_hpp_ops' => $joContract->items->sum('hpp_ops'),
                'total_selling_price' => $joContract->items->sum('hargajual_idr'),
            ];

            Log::info('Summary calculated', $summary);

            if ($request->expectsJson()) {
                Log::info('Returning JSON response');
                return response()->json([
                    'success' => true,
                    'data' => $joContract,
                    'summary' => $summary
                ]);
            }

            Log::info('Attempting to load view: data.jo-contract.show');

            // Check if view exists
            if (!view()->exists('data.jo-contract.show')) {
                Log::error('VIEW NOT FOUND: data.jo-contract.show', [
                    'expected_path' => 'resources/views/data/jo-contract/show.blade.php',
                    'searched_paths' => config('view.paths')
                ]);

                return back()->with('error', 'View file not found: data.jo-contract.show');
            }

            Log::info('View exists, rendering...');

            return view('data.jo-contract.show', compact('joContract', 'summary'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('JO Contract NOT FOUND in database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'JO Contract not found'
                ], 404);
            }

            return back()->with('error', 'JO Contract not found');
        } catch (\Exception $e) {
            Log::error('JO Contract Show Failed', [
                'id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading JO Contract: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error loading JO Contract: ' . $e->getMessage());
        } finally {
            Log::info('=== JO Contract Show Request END ===');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        Log::info('=== JO Contract Edit Request START ===', [
            'id' => $id,
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'user_id' => auth()->id() ?? 'guest',
            'ip' => request()->ip()
        ]);

        try {
            Log::info('Attempting to load JO Contract for editing', [
                'id' => $id
            ]);

            $joContract = JoContract::with(['items.invoice'])->findOrFail($id);

            Log::info('JO Contract loaded successfully', [
                'id' => $joContract->id_jo_cont,
                'title' => $joContract->title,
                'items_count' => $joContract->items->count()
            ]);

            Log::info('Loading related data (contracts, areas, invoices)');

            $contracts = Contract::with('customer')
                ->orderBy('no_contract')
                ->get();

            Log::info('Contracts loaded', ['count' => $contracts->count()]);

            $areas = Area::orderBy('area')->get();

            Log::info('Areas loaded', ['count' => $areas->count()]);

            $invoices = Invoice::orderBy('id')->get();

            Log::info('Invoices loaded', ['count' => $invoices->count()]);

            Log::info('Attempting to load view: data.jo-contract.edit');

            // Check if view exists
            if (!view()->exists('data.jo-contract.edit')) {
                Log::error('VIEW NOT FOUND: data.jo-contract.edit', [
                    'expected_path' => 'resources/views/data/jo-contract/edit.blade.php',
                    'searched_paths' => config('view.paths')
                ]);

                return back()->with('error', 'View file not found: data.jo-contract.edit');
            }

            Log::info('View exists, rendering...');

            return view('data.jo-contract.edit', compact('joContract', 'contracts', 'areas', 'invoices'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('JO Contract NOT FOUND in database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'JO Contract not found in database');
        } catch (\Exception $e) {
            Log::error('JO Contract Edit Failed', [
                'id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            return back()->with('error', 'Error loading JO Contract: ' . $e->getMessage());
        } finally {
            Log::info('=== JO Contract Edit Request END ===');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Log input data untuk debugging
        Log::info('JO Contract Update Request', [
            'id' => $id,
            'all_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'id_md_cont' => 'required|exists:a02_md_contract,id_md_cont',
            'id_md_area' => 'required|exists:a03_md_area,id_md_area',
            'title' => 'required|string|max:255',
            'note' => 'nullable|string',

            // Global kurs validation
            'global_kurs_usd' => 'required|numeric|min:0',
            'global_tgl_kurs_usd' => 'required|date',

            // Validation untuk items
            'items' => 'required|array|min:1',
            'items.*.id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
            'items.*.pendapatan_idr' => 'required|numeric|min:0',
            'items.*.pendapatan_usd' => 'required|numeric|min:0',
            'items.*.hpp_ops' => 'required|numeric|min:0',
            'items.*.hargajual_idr' => 'required|numeric|min:0',
        ], [
            'id_md_cont.required' => 'Contract is required',
            'id_md_cont.exists' => 'Selected contract does not exist',
            'id_md_area.required' => 'Area is required',
            'id_md_area.exists' => 'Selected area does not exist',
            'title.required' => 'Title is required',
            'global_kurs_usd.required' => 'Exchange rate is required',
            'global_tgl_kurs_usd.required' => 'Exchange rate date is required',
            'items.required' => 'At least one item is required',
            'items.min' => 'At least one item is required',
        ]);

        if ($validator->fails()) {
            Log::error('JO Contract Update Validation Failed', [
                'id' => $id,
                'errors' => $validator->errors()->toArray()
            ]);

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
            $joContract = JoContract::findOrFail($id);

            Log::info('Updating JO Contract', [
                'id' => $id,
                'old_data' => $joContract->toArray()
            ]);

            // Update JO Contract
            $joContract->update([
                'id_md_cont' => $request->id_md_cont,
                'id_md_area' => $request->id_md_area,
                'title' => $request->title,
                'note' => $request->note,
            ]);

            Log::info('JO Contract Updated Successfully', [
                'id' => $id
            ]);

            // Delete old items
            $oldItems = $joContract->items;
            Log::info('Deleting old JO Contract Items', [
                'jo_cont_id' => $id,
                'old_items_count' => $oldItems->count(),
                'old_item_ids' => $oldItems->pluck('id_jo_cont_item')->toArray()
            ]);

            foreach ($oldItems as $oldItem) {
                $oldItem->delete(); // Soft delete
            }

            Log::info('Old items deleted successfully');

            // Get global kurs
            $globalKursUsd = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            // Create new items - ID akan di-generate otomatis
            foreach ($request->items as $index => $item) {
                Log::info('Creating new JO Contract Item', [
                    'item_index' => $index,
                    'item_data' => [
                        'id_jo_cont' => $id,
                        'id_md_invoice' => $item['id_md_invoice'],
                        'pendapatan_idr' => $item['pendapatan_idr'],
                        'pendapatan_usd' => $item['pendapatan_usd'],
                        'kurs_usd' => $globalKursUsd,
                        'tgl_kurs_usd' => $globalTglKursUsd,
                        'hpp_ops' => $item['hpp_ops'],
                        'hargajual_idr' => $item['hargajual_idr'],
                    ]
                ]);

                // ID akan di-generate otomatis
                $createdItem = JoContractItem::create([
                    'id_jo_cont' => $id,
                    'id_md_invoice' => $item['id_md_invoice'],
                    'pendapatan_idr' => $item['pendapatan_idr'],
                    'pendapatan_usd' => $item['pendapatan_usd'],
                    'kurs_usd' => $globalKursUsd,
                    'tgl_kurs_usd' => $globalTglKursUsd,
                    'hpp_ops' => $item['hpp_ops'],
                    'hargajual_idr' => $item['hargajual_idr'],
                ]);

                Log::info('JO Contract Item Created Successfully', [
                    'item_id' => $createdItem->id_jo_cont_item
                ]);
            }

            DB::commit();

            Log::info('JO Contract Update Transaction Committed Successfully', [
                'jo_contract_id' => $id,
                'total_new_items' => count($request->items)
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Contract successfully updated',
                    'data' => $joContract->fresh()->load('items')
                ]);
            }

            return redirect()
                ->route('jo-contract.index')
                ->with('success', 'JO Contract successfully updated with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('JO Contract Update Failed', [
                'id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating JO contract: ' . $e->getMessage(),
                    'error_detail' => [
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating JO contract: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        Log::info('=== Delete JO Contract Request START ===', [
            'id' => $id
        ]);

        DB::beginTransaction();
        try {
            $joContract = JoContract::findOrFail($id);

            Log::info('JO Contract Found', [
                'id' => $joContract->id_jo_cont,
                'title' => $joContract->title,
                'items_count' => $joContract->items()->count()
            ]);

            // HAPUS SEMUA ITEMS TERLEBIH DAHULU
            $itemsCount = $joContract->items()->count();

            if ($itemsCount > 0) {
                Log::info('Deleting JO Contract Items', [
                    'items_count' => $itemsCount
                ]);

                // Delete all items (soft delete)
                foreach ($joContract->items as $item) {
                    $item->delete();
                }

                Log::info('All items deleted successfully');
            }

            // KEMUDIAN HAPUS JO CONTRACT
            $joContract->delete();

            DB::commit();

            Log::info('JO Contract Deleted Successfully', [
                'id' => $id,
                'deleted_items_count' => $itemsCount
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Contract and all related items successfully deleted'
                ]);
            }

            return redirect()
                ->route('jo-contract.index')
                ->with('success', "JO Contract deleted successfully along with {$itemsCount} item(s)");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            Log::error('JO Contract Not Found', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'JO Contract not found'
                ], 404);
            }

            return back()->with('error', 'JO Contract not found');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Delete JO Contract Failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting JO contract: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting JO contract: ' . $e->getMessage());
        }
    }
    /**
     * Get JO contracts for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $query = JoContract::with(['contract', 'area']);

            // Filter by contract if provided
            if ($request->has('id_md_cont')) {
                $query->where('id_md_cont', $request->id_md_cont);
            }

            // Filter by area if provided
            if ($request->has('id_md_area')) {
                $query->where('id_md_area', $request->id_md_area);
            }

            $joContracts = $query->orderBy('id_jo_cont', 'desc')
                ->get()
                ->map(function ($joContract) {
                    return [
                        'id' => $joContract->id_jo_cont,
                        'text' => 'JO-' . $joContract->id_jo_cont . ' - ' . $joContract->title,
                        'contract' => $joContract->contract ? $joContract->contract->no_contract : null,
                        'area' => $joContract->area ? $joContract->area->area : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $joContracts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving JO contracts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete JO contracts
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:b01_jo_cont,id_jo_cont'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $joContracts = JoContract::whereIn('id_jo_cont', $request->ids)->get();

            // Check if any has items
            $hasRelations = false;
            foreach ($joContracts as $joContract) {
                if ($joContract->items()->count() > 0) {
                    $hasRelations = true;
                    break;
                }
            }

            if ($hasRelations) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Some JO contracts cannot be deleted because they have related items'
                ], 422);
            }

            JoContract::whereIn('id_jo_cont', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'JO Contracts successfully deleted'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting JO contracts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save All Changes (Header + Update all items with global kurs)
     */
    public function saveAllChanges(Request $request, $id)
    {
        Log::info('=== Save All Changes Request START ===', [
            'id' => $id,
            'data' => $request->all()
        ]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_cont' => 'required|exists:a02_md_contract,id_md_cont',
                'id_md_area' => 'required|exists:a03_md_area,id_md_area',
                'title' => 'required|string|max:255',
                'note' => 'nullable|string',
                'global_tgl_kurs_usd' => 'nullable',
                'global_kurs_usd' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $joContract = JoContract::findOrFail($id);

            // Update header
            $joContract->update([
                'id_md_cont' => $request->id_md_cont,
                'id_md_area' => $request->id_md_area,
                'title' => $request->title,
                'note' => $request->note,
            ]);

            Log::info('Header updated successfully');

            // Update all items with global kurs
            $globalKursUsd = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            $items = JoContractItem::where('id_jo_cont', $id)->get();

            Log::info('Updating items with global kurs', [
                'items_count' => $items->count(),
                'global_kurs_usd' => $globalKursUsd,
                'global_tgl_kurs_usd' => $globalTglKursUsd
            ]);

            foreach ($items as $item) {
                // Hanya update item yang punya pendapatan USD
                if ($item->pendapatan_usd > 0) {
                    // Recalculate hargajual_idr dengan kurs baru
                    $hargajualIDR = $item->pendapatan_usd * $globalKursUsd;

                    $item->update([
                        'kurs_usd' => $globalKursUsd,
                        'tgl_kurs_usd' => $globalTglKursUsd,
                        'hargajual_idr' => $hargajualIDR
                    ]);

                    Log::info('Item updated', [
                        'item_id' => $item->id_jo_cont_item,
                        'old_kurs' => $item->getOriginal('kurs_usd'),
                        'new_kurs' => $globalKursUsd,
                        'old_hargajual' => $item->getOriginal('hargajual_idr'),
                        'new_hargajual' => $hargajualIDR
                    ]);
                }
            }

            DB::commit();

            Log::info('Save All Changes SUCCESS');

            return response()->json([
                'success' => true,
                'message' => 'All changes saved successfully',
                'data' => [
                    'jo_contract' => $joContract,
                    'updated_items_count' => $items->count()
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Save All Changes Failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save all changes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
