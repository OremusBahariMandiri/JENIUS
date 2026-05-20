<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Data\JoContractItem;
use App\Models\Data\JoContract;
use App\Models\Master\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JoContractItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = JoContractItem::with(['joContract.contract.customer', 'joContract.area', 'invoice']);

            // Include trashed records if requested
            if ($request->has('with_trashed') && $request->with_trashed) {
                $query->withTrashed();
            }

            // Only trashed records if requested
            if ($request->has('only_trashed') && $request->only_trashed) {
                $query->onlyTrashed();
            }

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('id_jo_cont_item', 'like', "%{$search}%")
                      ->orWhereHas('joContract', function($q) use ($search) {
                          $q->where('title', 'like', "%{$search}%");
                      })
                      ->orWhereHas('invoice', function($q) use ($search) {
                          $q->where('code', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by JO Contract
            if ($request->has('id_jo_cont') && !empty($request->id_jo_cont)) {
                $query->where('id_jo_cont', $request->id_jo_cont);
            }

            // Filter by Invoice
            if ($request->has('id_md_invoice') && !empty($request->id_md_invoice)) {
                $query->where('id_md_invoice', $request->id_md_invoice);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $joContractItems = $query->paginate($perPage);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $joContractItems
                ]);
            }

            // Get JO contracts and invoices for filter
            $joContracts = JoContract::with('contract')->orderBy('id_jo_cont', 'desc')->get();
            $invoices = Invoice::orderBy('code')->get();

            return view('data.jo-contract-item.index', compact('joContractItems', 'joContracts', 'invoices'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving JO contract items: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving JO contract items: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $joContracts = JoContract::with(['contract.customer', 'area'])
            ->orderBy('id_jo_cont', 'desc')
            ->get();
        $invoices = Invoice::orderBy('code')->get();

        return view('data.jo-contract-item.create', compact('joContracts', 'invoices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_jo_cont' => 'required|exists:b01_jo_cont,id_jo_cont',
            'id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
            'pendapatan_idr' => 'nullable|numeric|min:0',
            'pendapatan_usd' => 'nullable|numeric|min:0',
            'kurs_usd' => 'nullable|numeric|min:0',
            'tgl_kurs_usd' => 'nullable|date',
            'hpp_ops' => 'nullable|numeric|min:0',
            'hargajual_idr' => 'nullable|numeric|min:0',
        ], [
            'id_jo_cont.required' => 'JO Contract is required',
            'id_jo_cont.exists' => 'Selected JO Contract does not exist',
            'id_md_invoice.required' => 'Invoice is required',
            'id_md_invoice.exists' => 'Selected invoice does not exist',
            'pendapatan_idr.numeric' => 'Revenue IDR must be a number',
            'pendapatan_usd.numeric' => 'Revenue USD must be a number',
            'kurs_usd.numeric' => 'Exchange rate must be a number',
            'hpp_ops.numeric' => 'HPP Ops must be a number',
            'hargajual_idr.numeric' => 'Selling price must be a number',
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
            // Generate ID
            $lastItem = JoContractItem::withTrashed()->orderBy('id_jo_cont_item', 'desc')->first();
            $newId = $lastItem ? $lastItem->id_jo_cont_item + 1 : 1;

            $joContractItem = JoContractItem::create([
                'id_jo_cont_item' => $newId,
                'id_jo_cont' => $request->id_jo_cont,
                'id_md_invoice' => $request->id_md_invoice,
                'pendapatan_idr' => $request->pendapatan_idr ?? 0,
                'pendapatan_usd' => $request->pendapatan_usd ?? 0,
                'kurs_usd' => $request->kurs_usd ?? 0,
                'tgl_kurs_usd' => $request->tgl_kurs_usd,
                'hpp_ops' => $request->hpp_ops ?? 0,
                'hargajual_idr' => $request->hargajual_idr ?? 0,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Contract Item successfully added',
                    'data' => $joContractItem
                ], 201);
            }

            return redirect()
                ->route('jo-contract-item.index')
                ->with('success', 'JO Contract Item successfully added');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating JO contract item: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating JO contract item: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $query = JoContractItem::with([
                'joContract.contract.customer',
                'joContract.area',
                'invoice'
            ]);

            if ($request->has('with_trashed') && $request->with_trashed) {
                $query->withTrashed();
            }

            $joContractItem = $query->findOrFail($id);

            // Calculate profit
            $profit = $joContractItem->hargajual_idr - $joContractItem->hpp_ops;
            $profitMargin = $joContractItem->hargajual_idr > 0
                ? ($profit / $joContractItem->hargajual_idr) * 100
                : 0;

            $calculations = [
                'profit' => $profit,
                'profit_margin' => $profitMargin,
                'total_revenue' => $joContractItem->pendapatan_idr +
                    ($joContractItem->pendapatan_usd * $joContractItem->kurs_usd),
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $joContractItem,
                    'calculations' => $calculations
                ]);
            }

            return view('data.jo-contract-item.show', compact('joContractItem', 'calculations'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'JO Contract Item not found'
                ], 404);
            }

            return back()->with('error', 'JO Contract Item not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $joContractItem = JoContractItem::findOrFail($id);

            $joContracts = JoContract::with(['contract.customer', 'area'])
                ->orderBy('id_jo_cont', 'desc')
                ->get();
            $invoices = Invoice::orderBy('code')->get();

            return view('data.jo-contract-item.edit', compact('joContractItem', 'joContracts', 'invoices'));
        } catch (\Exception $e) {
            return back()->with('error', 'JO Contract Item not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_jo_cont' => 'required|exists:b01_jo_cont,id_jo_cont',
            'id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
            'pendapatan_idr' => 'nullable|numeric|min:0',
            'pendapatan_usd' => 'nullable|numeric|min:0',
            'kurs_usd' => 'nullable|numeric|min:0',
            'tgl_kurs_usd' => 'nullable|date',
            'hpp_ops' => 'nullable|numeric|min:0',
            'hargajual_idr' => 'nullable|numeric|min:0',
        ], [
            'id_jo_cont.required' => 'JO Contract is required',
            'id_jo_cont.exists' => 'Selected JO Contract does not exist',
            'id_md_invoice.required' => 'Invoice is required',
            'id_md_invoice.exists' => 'Selected invoice does not exist',
            'pendapatan_idr.numeric' => 'Revenue IDR must be a number',
            'pendapatan_usd.numeric' => 'Revenue USD must be a number',
            'kurs_usd.numeric' => 'Exchange rate must be a number',
            'hpp_ops.numeric' => 'HPP Ops must be a number',
            'hargajual_idr.numeric' => 'Selling price must be a number',
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
            $joContractItem = JoContractItem::findOrFail($id);

            $joContractItem->update([
                'id_jo_cont' => $request->id_jo_cont,
                'id_md_invoice' => $request->id_md_invoice,
                'pendapatan_idr' => $request->pendapatan_idr ?? 0,
                'pendapatan_usd' => $request->pendapatan_usd ?? 0,
                'kurs_usd' => $request->kurs_usd ?? 0,
                'tgl_kurs_usd' => $request->tgl_kurs_usd,
                'hpp_ops' => $request->hpp_ops ?? 0,
                'hargajual_idr' => $request->hargajual_idr ?? 0,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Contract Item successfully updated',
                    'data' => $joContractItem
                ]);
            }

            return redirect()
                ->route('jo-contract-item.index')
                ->with('success', 'JO Contract Item successfully updated');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating JO contract item: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating JO contract item: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $joContractItem = JoContractItem::findOrFail($id);

            // Soft delete
            $joContractItem->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Contract Item successfully deleted'
                ]);
            }

            return redirect()
                ->route('jo-contract-item.index')
                ->with('success', 'JO Contract Item successfully deleted');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting JO contract item: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting JO contract item: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft deleted item
     */
    public function restore(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $joContractItem = JoContractItem::withTrashed()->findOrFail($id);

            if (!$joContractItem->trashed()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Item is not in deleted status'
                    ], 422);
                }

                return back()->with('error', 'Item is not in deleted status');
            }

            $joContractItem->restore();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Contract Item successfully restored',
                    'data' => $joContractItem
                ]);
            }

            return redirect()
                ->route('jo-contract-item.index')
                ->with('success', 'JO Contract Item successfully restored');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error restoring JO contract item: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error restoring JO contract item: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete an item
     */
    public function forceDelete(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $joContractItem = JoContractItem::withTrashed()->findOrFail($id);

            // Force delete (permanent)
            $joContractItem->forceDelete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Contract Item permanently deleted'
                ]);
            }

            return redirect()
                ->route('jo-contract-item.index')
                ->with('success', 'JO Contract Item permanently deleted');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error permanently deleting JO contract item: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error permanently deleting JO contract item: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete items
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:b02_jo_cont_item,id_jo_cont_item'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            JoContractItem::whereIn('id_jo_cont_item', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'JO Contract Items successfully deleted'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting JO contract items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk restore items
     */
    public function bulkRestore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            JoContractItem::withTrashed()
                ->whereIn('id_jo_cont_item', $request->ids)
                ->restore();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'JO Contract Items successfully restored'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error restoring JO contract items: ' . $e->getMessage()
            ], 500);
        }
    }
}