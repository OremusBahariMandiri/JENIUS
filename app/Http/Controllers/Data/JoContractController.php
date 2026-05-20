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
        $validator = Validator::make($request->all(), [
            'id_md_cont' => 'required|exists:a02_md_contract,id_md_cont',
            'id_md_area' => 'required|exists:a03_md_area,id_md_area',
            'title' => 'required|string|max:255',
            'note' => 'nullable|string',

            // Validation untuk items
            'items' => 'required|array|min:1',
            'items.*.id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
            'items.*.pendapatan_idr' => 'required|numeric|min:0',
            'items.*.pendapatan_usd' => 'required|numeric|min:0',
            'items.*.kurs_usd' => 'required|numeric|min:0',
            'items.*.tgl_kurs_usd' => 'nullable|date',
            'items.*.hpp_ops' => 'required|numeric|min:0',
            'items.*.hargajual_idr' => 'required|numeric|min:0',
        ], [
            'id_md_cont.required' => 'Contract is required',
            'id_md_cont.exists' => 'Selected contract does not exist',
            'id_md_area.required' => 'Area is required',
            'id_md_area.exists' => 'Selected area does not exist',
            'title.required' => 'Title is required',
            'items.required' => 'At least one item is required',
            'items.min' => 'At least one item is required',
            'items.*.id_md_invoice.required' => 'Invoice is required',
            'items.*.id_md_invoice.exists' => 'Selected invoice does not exist',
            'items.*.pendapatan_idr.required' => 'Revenue IDR is required',
            'items.*.pendapatan_usd.required' => 'Revenue USD is required',
            'items.*.kurs_usd.required' => 'Exchange rate is required',
            'items.*.hpp_ops.required' => 'HPP Operational is required',
            'items.*.hargajual_idr.required' => 'Selling price is required',
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
            // Generate ID untuk JO Contract
            $lastJoContract = JoContract::orderBy('id_jo_cont', 'desc')->first();
            $newJoContId = $lastJoContract ? $lastJoContract->id_jo_cont + 1 : 1;

            // Create JO Contract
            $joContract = JoContract::create([
                'id_jo_cont' => $newJoContId,
                'id_md_cont' => $request->id_md_cont,
                'id_md_area' => $request->id_md_area,
                'title' => $request->title,
                'note' => $request->note,
            ]);

            // Create JO Contract Items
            $lastItem = JoContractItem::orderBy('id_jo_cont_item', 'desc')->first();
            $itemIdCounter = $lastItem ? $lastItem->id_jo_cont_item : 0;

            foreach ($request->items as $item) {
                $itemIdCounter++;

                JoContractItem::create([
                    'id_jo_cont_item' => $itemIdCounter,
                    'id_jo_cont' => $newJoContId,
                    'id_md_invoice' => $item['id_md_invoice'],
                    'pendapatan_idr' => $item['pendapatan_idr'],
                    'pendapatan_usd' => $item['pendapatan_usd'],
                    'kurs_usd' => $item['kurs_usd'],
                    'tgl_kurs_usd' => $item['tgl_kurs_usd'] ?? null,
                    'hpp_ops' => $item['hpp_ops'],
                    'hargajual_idr' => $item['hargajual_idr'],
                ]);
            }

            DB::commit();

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

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating JO contract: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating JO contract: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $joContract = JoContract::with([
                'contract.customer',
                'area',
                'items.invoice'
            ])->findOrFail($id);

            // Calculate summary
            $summary = [
                'total_items' => $joContract->items->count(),
                'total_revenue_idr' => $joContract->items->sum('pendapatan_idr'),
                'total_revenue_usd' => $joContract->items->sum('pendapatan_usd'),
                'total_hpp_ops' => $joContract->items->sum('hpp_ops'),
                'total_selling_price' => $joContract->items->sum('hargajual_idr'),
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $joContract,
                    'summary' => $summary
                ]);
            }

            return view('data.jo-contract.show', compact('joContract', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'JO Contract not found'
                ], 404);
            }

            return back()->with('error', 'JO Contract not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $joContract = JoContract::findOrFail($id);

            $contracts = Contract::with('customer')
                ->orderBy('no_contract')
                ->get();
            $areas = Area::orderBy('area')->get();

            return view('data.jo-contract.edit', compact('joContract', 'contracts', 'areas'));
        } catch (\Exception $e) {
            return back()->with('error', 'JO Contract not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
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

            $joContract->update([
                'id_md_cont' => $request->id_md_cont,
                'id_md_area' => $request->id_md_area,
                'title' => $request->title,
                'note' => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Contract successfully updated',
                    'data' => $joContract
                ]);
            }

            return redirect()
                ->route('jo-contract.index')
                ->with('success', 'JO Contract successfully updated');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating JO contract: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating JO contract: ' . $e->getMessage());
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