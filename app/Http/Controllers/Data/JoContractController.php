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
            $query = JoContract::with(['contract.customer', 'area']);

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

    /**
     * Store a newly created resource in storage.
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

            // Create JO Contract Items
            $lastItem = JoContractItem::orderBy('id_jo_cont_item', 'desc')->first();
            $itemIdCounter = $lastItem ? $lastItem->id_jo_cont_item : 0;

            foreach ($request->items as $index => $item) {
                $itemIdCounter++;

                Log::info('Creating JO Contract Item', [
                    'item_index' => $index,
                    'item_id' => $itemIdCounter,
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

                $createdItem = JoContractItem::create([
                    'id_jo_cont_item' => $itemIdCounter,
                    'id_jo_cont' => $newJoContId,
                    'id_md_invoice' => $item['id_md_invoice'],
                    'pendapatan_idr' => $item['pendapatan_idr'],
                    'pendapatan_usd' => $item['pendapatan_usd'],
                    'kurs_usd' => $globalKursUsd, // Menggunakan global kurs
                    'tgl_kurs_usd' => $globalTglKursUsd, // Menggunakan global tanggal kurs
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
            Log::info('Deleting old JO Contract Items', [
                'jo_cont_id' => $id,
                'old_items_count' => $joContract->items()->count()
            ]);

            $joContract->items()->delete(); // Soft delete

            // Get global kurs
            $globalKursUsd = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            // Create new items
            $lastItem = JoContractItem::withTrashed()->orderBy('id_jo_cont_item', 'desc')->first();
            $itemIdCounter = $lastItem ? $lastItem->id_jo_cont_item : 0;

            foreach ($request->items as $index => $item) {
                $itemIdCounter++;

                Log::info('Creating new JO Contract Item', [
                    'item_index' => $index,
                    'item_id' => $itemIdCounter,
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

                $createdItem = JoContractItem::create([
                    'id_jo_cont_item' => $itemIdCounter,
                    'id_jo_cont' => $id,
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

            Log::info('JO Contract Update Transaction Committed Successfully', [
                'jo_contract_id' => $id,
                'total_new_items' => count($request->items)
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Contract successfully updated',
                    'data' => $joContract->load('items')
                ]);
            }

            return redirect()
                ->route('jo-contract.show', $id)
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
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $joContract = JoContract::findOrFail($id);

            // Check if has items
            if ($joContract->items()->count() > 0) {
                DB::rollBack();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'JO Contract cannot be deleted because it has related items'
                    ], 422);
                }

                return back()->with('error', 'JO Contract cannot be deleted because it has related items');
            }

            $joContract->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Contract successfully deleted'
                ]);
            }

            return redirect()
                ->route('jo-contract.index')
                ->with('success', 'JO Contract successfully deleted');
        } catch (\Exception $e) {
            DB::rollBack();

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
}