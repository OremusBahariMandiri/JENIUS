<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Data\JoTramper;
use App\Models\Data\JoTramperItem;
use App\Models\Master\Customer;
use App\Models\Master\Port;
use App\Models\Master\Invoice;
use App\Helpers\IdGenerator;
use App\Models\Master\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class JoTramperController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = JoTramper::with(['customer', 'port']);

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id_jo_tram', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhere('sts_proses', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q) use ($search) {
                            $q->where('customer', 'like', "%{$search}%")
                                ->orWhere('no_customer', 'like', "%{$search}%");
                        })
                        ->orWhereHas('port', function ($q) use ($search) {
                            $q->where('name_port', 'like', "%{$search}%")
                                ->orWhere('no_port', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->has('id_md_cust') && !empty($request->id_md_cust)) {
                $query->where('id_md_cust', $request->id_md_cust);
            }

            if ($request->has('id_md_port') && !empty($request->id_md_port)) {
                $query->where('id_md_port', $request->id_md_port);
            }

            if ($request->has('sts_proses') && !empty($request->sts_proses)) {
                $query->where('sts_proses', $request->sts_proses);
            }

            if ($request->has('date_start') && !empty($request->date_start)) {
                $query->where('date_start', '>=', $request->date_start);
            }

            if ($request->has('date_end') && !empty($request->date_end)) {
                $query->where('date_end', '<=', $request->date_end);
            }

            $sortBy    = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $joTrampers = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $joTrampers]);
            }

            $customers = Customer::orderBy('customer')->get();
            $ports     = Port::orderBy('name_port')->get();

            return view('data.jo-tramper.index', compact('joTrampers', 'customers', 'ports'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error retrieving JO Trampers: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving JO Trampers: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('customer')->get();
        $ports     = Port::orderBy('name_port')->get();
        $invoices = Invoice::where('jo_ctg', 'tramper')->orderBy('invoice_ctg')->orderBy('invoice_typ')->get();
        $vessels = Vessel::orderBy('vessel_name')->get();
        $previewNoJot = IdGenerator::generateDocNo('b03_jo_tram', 'no_jo_tram');

        return view('data.jo-tramper.create', compact('customers', 'ports', 'invoices', 'vessels', 'previewNoJot'));
    }

    // =========================================================================
    // REALTIME AUTO-SAVE METHODS
    // =========================================================================

    /**
     * Store JO Tramper Header (Realtime Auto-save via AJAX)
     */
    public function storeHeader(Request $request)
    {
        Log::info('=== JO Tramper Store Header Request START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_cust' => 'required|exists:a01_md_customer,id_md_cust',
                'id_md_port' => 'required|exists:a06_md_port,id_md_port',
                'id_md_vessel' => 'nullable|exists:a05_md_vessel,id_md_vessel',
                'tgl_jo_tram'  => 'required|date',
                'date_start' => 'required|date',
                'date_end'   => 'required|date|after_or_equal:date_start',
                'title'      => 'required|string|max:255',
                'note'       => 'nullable|string',
            ], [
                'id_md_cust.required'     => 'Customer is required',
                'id_md_cust.exists'       => 'Selected customer does not exist',
                'id_md_port.required'     => 'Port is required',
                'id_md_port.exists'       => 'Selected port does not exist',
                'date_start.required'     => 'Start date is required',
                'date_end.required'       => 'End date is required',
                'date_end.after_or_equal' => 'End date must be after or equal to start date',
                'title.required'          => 'Title is required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $newJoTramId = IdGenerator::generate('B03', 'b03_jo_tram', 'id_jo_tram');
            $noJoTram = IdGenerator::generateDocNo('b03_jo_tram', 'no_jo_tram');

            DB::table('b03_jo_tram')->insert([
                'id_jo_tram' => $newJoTramId,
                'no_jo_tram'   => $noJoTram,
                'tgl_jo_tram'  => $request->tgl_jo_tram,
                'id_md_cust' => $request->id_md_cust,
                'id_md_port' => $request->id_md_port,
                'id_md_vessel' => $request->id_md_vessel,
                'date_start' => $request->date_start,
                'date_end'   => $request->date_end,
                'title'      => $request->title,
                'note'       => $request->note,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            Log::info('JO Tramper Header Created Successfully', ['id_jo_tram' => $newJoTramId]);

            return response()->json([
                'success'      => true,
                'message'      => 'JO Tramper header saved successfully',
                'redirect_url' => route('jo-tramper.edit', $newJoTramId),
                'data'         => [
                    'id'         => $newJoTramId,
                    'id_md_cust' => $request->id_md_cust,
                    'id_md_port' => $request->id_md_port,
                    'id_md_vessel' => $request->id_md_vessel,
                    'date_start' => $request->date_start,
                    'date_end'   => $request->date_end,
                    'title'      => $request->title,
                    'note'       => $request->note,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JO Tramper Store Header Failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to save JO Tramper header', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update JO Tramper Header (Realtime Auto-save via AJAX)
     */
    public function updateHeader(Request $request, $id)
    {
        Log::info('=== JO Tramper Update Header Request START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_cust' => 'required|exists:a01_md_customer,id_md_cust',
                'id_md_port' => 'required|exists:a06_md_port,id_md_port',
                'id_md_vessel' => 'nullable|exists:a05_md_vessel,id_md_vessel',
                'tgl_jo_tram'  => 'required|date',
                'date_start' => 'required|date',
                'date_end'   => 'required|date|after_or_equal:date_start',
                'title'      => 'required|string|max:255',
                'note'       => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $joTramper = JoTramper::where('id_jo_tram', (string) $id)->firstOrFail();

            $joTramper->update([
                'tgl_jo_tram'  => $request->tgl_jo_tram,
                'id_md_cust' => $request->id_md_cust,
                'id_md_port' => $request->id_md_port,
                'id_md_vessel' => $request->id_md_vessel,
                'date_start' => $request->date_start,
                'date_end'   => $request->date_end,
                'title'      => $request->title,
                'note'       => $request->note,
            ]);

            DB::commit();

            $joTramper->load(['customer', 'port']);

            return response()->json([
                'success' => true,
                'message' => 'JO Tramper header updated successfully',
                'data'    => $joTramper
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JO Tramper Update Header Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update JO Tramper header', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store JO Tramper Item (Realtime Auto-save via AJAX)
     */
    public function storeItem(Request $request)
    {
        Log::info('=== JO Tramper Store Item Request START ===', ['data' => $request->all()]);

        try {
            $rules = [
                'id_jo_tram'    => 'required|exists:b03_jo_tram,id_jo_tram',
                'id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
                'id_md_vessel' => 'nullable|exists:a05_md_vessel,id_md_vessel',
                'invoice_ctg'   => 'required|string',
                'pendapatan_idr' => 'nullable|numeric|min:0',
                'pendapatan_usd' => 'nullable|numeric|min:0',
                'hpp_ops'        => 'nullable|numeric|min:0',
                'note' => 'nullable|string',
            ];

            if ($request->pendapatan_usd && $request->pendapatan_usd > 0) {
                $rules['kurs_usd']     = 'required|numeric|min:0';
                $rules['tgl_kurs_usd'] = 'required|date';
            } else {
                $rules['kurs_usd']     = 'nullable|numeric|min:0';
                $rules['tgl_kurs_usd'] = 'nullable|date';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $pendapatanIDR = $request->pendapatan_idr ?? 0;
            $pendapatanUSD = $request->pendapatan_usd ?? 0;
            $kursUSD       = $request->kurs_usd ?? 0;
            $hppOps        = $request->hpp_ops ?? 0;

            $hargajualIDR = $pendapatanIDR > 0
                ? $pendapatanIDR
                : ($pendapatanUSD * $kursUSD);

            $item = JoTramperItem::create([
                'id_jo_tram'     => $request->id_jo_tram,
                'id_md_invoice'  => $request->id_md_invoice,
                'id_md_vessel' => $request->id_md_vessel,
                'pendapatan_idr' => $pendapatanIDR,
                'pendapatan_usd' => $pendapatanUSD,
                'kurs_usd'       => $kursUSD,
                'tgl_kurs_usd'   => $request->tgl_kurs_usd,
                'hpp_ops'        => $hppOps,
                'hargajual_idr'  => $hargajualIDR,
                'note' => $request->note,
            ]);

            $item->load('invoice');

            DB::commit();

            Log::info('JO Tramper Item Created Successfully', ['id_jo_tram_item' => $item->id_jo_tram_item]);

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data'    => [
                    'id'              => $item->id_jo_tram_item,
                    'id_jo_tram_item' => $item->id_jo_tram_item,
                    'id_jo_tram'      => $item->id_jo_tram,
                    'id_md_invoice'   => $item->id_md_invoice,
                    'invoice_ctg'     => $request->invoice_ctg,
                    'invoice_typ'     => $item->invoice ? $item->invoice->invoice_typ : null,
                    'pendapatan_idr'  => (float) $item->pendapatan_idr,
                    'pendapatan_usd'  => (float) $item->pendapatan_usd,
                    'kurs_usd'        => $item->kurs_usd ? (float) $item->kurs_usd : null,
                    'tgl_kurs_usd'    => $item->tgl_kurs_usd ? $item->tgl_kurs_usd->format('Y-m-d') : null,
                    'hpp_ops'         => (float) $item->hpp_ops,
                    'hargajual_idr'   => (float) $item->hargajual_idr,
                ]
            ], 201);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            Log::error('JO Tramper Store Item Failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to add item', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show single JO Tramper Item (for edit form)
     */
    public function showItem($id)
    {
        try {
            $item = JoTramperItem::with('invoice')->where('id_jo_tram_item', (string) $id)->firstOrFail();

            return response()->json([
                'success' => true,
                'data'    => [
                    'id_jo_tram_item' => $item->id_jo_tram_item,
                    'id_md_invoice'   => $item->id_md_invoice,
                    'invoice_ctg'     => $item->invoice->invoice_ctg,
                    'invoice_typ'     => $item->invoice->invoice_typ,
                    'pendapatan_idr'  => $item->pendapatan_idr,
                    'pendapatan_usd'  => $item->pendapatan_usd,
                    'hpp_ops'         => $item->hpp_ops,
                    'kurs_usd'        => $item->kurs_usd,
                    'tgl_kurs_usd'    => $item->tgl_kurs_usd ? $item->tgl_kurs_usd->format('Y-m-d') : null,
                    'hargajual_idr'   => $item->hargajual_idr,
                    'note'            => $item->note,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Item not found: ' . $e->getMessage()], 404);
        }
    }

    /**
     * Update JO Tramper Item (Realtime Auto-save via AJAX)
     */
    public function updateItem(Request $request, $id)
    {
        Log::info('=== JO Tramper Update Item Request START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $rules = [
                'id_jo_tram'     => 'required|exists:b03_jo_tram,id_jo_tram',
                'id_md_invoice'  => 'required|exists:a04_md_invoice,id_md_invoice',
                'invoice_ctg'    => 'required|string',
                'pendapatan_idr' => 'nullable|numeric|min:0',
                'pendapatan_usd' => 'nullable|numeric|min:0',
                'hpp_ops'        => 'nullable|numeric|min:0',
                'note' => 'nullable|string',
            ];

            if ($request->pendapatan_usd && $request->pendapatan_usd > 0) {
                $rules['kurs_usd']     = 'required|numeric|min:0';
                $rules['tgl_kurs_usd'] = 'required';
            } else {
                $rules['kurs_usd']     = 'nullable|numeric|min:0';
                $rules['tgl_kurs_usd'] = 'nullable';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $item = JoTramperItem::where('id_jo_tram_item', (string) $id)->firstOrFail();

            $pendapatanIDR = $request->pendapatan_idr ?? 0;
            $pendapatanUSD = $request->pendapatan_usd ?? 0;
            $kursUSD       = $request->kurs_usd ?? 0;
            $hppOps        = $request->hpp_ops ?? 0;

            $hargajualIDR = $pendapatanIDR > 0
                ? $pendapatanIDR
                : ($pendapatanUSD * $kursUSD);

            $item->update([
                'id_md_invoice'  => $request->id_md_invoice,
                'pendapatan_idr' => $pendapatanIDR,
                'pendapatan_usd' => $pendapatanUSD,
                'kurs_usd'       => $kursUSD,
                'tgl_kurs_usd'   => $request->tgl_kurs_usd ?? null,
                'hpp_ops'        => $hppOps,
                'hargajual_idr'  => $hargajualIDR,
                'note' => $request->note,
            ]);

            $item->refresh();
            $item->load('invoice');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data'    => [
                    'id'              => $item->id_jo_tram_item,
                    'id_jo_tram_item' => $item->id_jo_tram_item,
                    'id_jo_tram'      => $item->id_jo_tram,
                    'id_md_invoice'   => $item->id_md_invoice,
                    'invoice_ctg'     => $request->invoice_ctg,
                    'invoice_typ'     => $item->invoice ? $item->invoice->invoice_typ : null,
                    'pendapatan_idr'  => (float) $item->pendapatan_idr,
                    'pendapatan_usd'  => (float) $item->pendapatan_usd,
                    'kurs_usd'        => $item->kurs_usd ? (float) $item->kurs_usd : null,
                    'tgl_kurs_usd'    => $request->tgl_kurs_usd,
                    'hpp_ops'         => (float) $item->hpp_ops,
                    'hargajual_idr'   => (float) $item->hargajual_idr,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JO Tramper Update Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update item', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete JO Tramper Item (Realtime Auto-delete via AJAX)
     */
    public function destroyItem($id)
    {
        Log::info('=== JO Tramper Delete Item Request START ===', ['id' => $id]);

        try {
            DB::beginTransaction();

            $item = JoTramperItem::where('id_jo_tram_item', (string) $id)->firstOrFail();
            $item->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Item deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JO Tramper Delete Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete item', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all items for a JO Tramper
     */
    public function getItems($joTramperId)
    {
        try {
            $items = JoTramperItem::where('id_jo_tram', $joTramperId)
                ->with('invoice')
                ->orderBy('created_at')
                ->get();

            $formattedItems = $items->map(function ($item) {
                return [
                    'id'              => $item->id_jo_tram_item,
                    'id_jo_tram'      => $item->id_jo_tram,
                    'id_md_invoice'   => $item->id_md_invoice,
                    'invoice_typ'     => $item->invoice ? $item->invoice->invoice_typ : 'Unknown',
                    'invoice_ctg'     => $item->invoice ? $item->invoice->invoice_ctg : 'Unknown',
                    'pendapatan_idr'  => $item->pendapatan_idr,
                    'pendapatan_usd'  => $item->pendapatan_usd,
                    'kurs_usd'        => $item->kurs_usd,
                    'tgl_kurs_usd'    => $item->tgl_kurs_usd,
                    'hpp_ops'         => $item->hpp_ops,
                    'hargajual_idr'   => $item->hargajual_idr,
                ];
            });

            return response()->json(['success' => true, 'data' => $formattedItems]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch items', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Save All Changes (Header + Update all items with global kurs)
     */
    public function saveAllChanges(Request $request, $id)
    {
        Log::info('=== JO Tramper Save All Changes Request START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_cust'         => 'required|exists:a01_md_customer,id_md_cust',
                'id_md_port'         => 'required|exists:a06_md_port,id_md_port',
                'id_md_vessel' => 'nullable|exists:a05_md_vessel,id_md_vessel',
                'date_start'         => 'required|date',
                'date_end'           => 'required|date|after_or_equal:date_start',
                'title'              => 'required|string|max:255',
                'note'               => 'nullable|string',
                'global_tgl_kurs_usd' => 'nullable|date',
                'global_kurs_usd'    => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $joTramper = JoTramper::where('id_jo_tram', (string) $id)->firstOrFail();

            $joTramper->update([
                'id_md_cust' => $request->id_md_cust,
                'id_md_port' => $request->id_md_port,
                'id_md_vessel' => $request->id_md_vessel,
                'date_start' => $request->date_start,
                'date_end'   => $request->date_end,
                'title'      => $request->title,
                'note'       => $request->note,
            ]);

            $globalKursUsd    = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            $items = JoTramperItem::where('id_jo_tram', $id)->get();

            foreach ($items as $item) {
                if ($item->pendapatan_usd > 0 && $globalKursUsd) {
                    $hargajualIDR = $item->pendapatan_usd * $globalKursUsd;
                    $item->update([
                        'kurs_usd'     => $globalKursUsd,
                        'tgl_kurs_usd' => $globalTglKursUsd,
                        'hargajual_idr' => $hargajualIDR,
                    ]);
                }
            }

            DB::commit();

            Log::info('JO Tramper Save All Changes SUCCESS');

            return response()->json([
                'success' => true,
                'message' => 'All changes saved successfully',
                'data'    => [
                    'jo_tramper'          => $joTramper,
                    'updated_items_count' => $items->count()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JO Tramper Save All Changes Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to save all changes', 'error' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // TRADITIONAL METHODS
    // =========================================================================

    /**
     * Store a newly created resource in storage (traditional fallback).
     */
    public function store(Request $request)
    {
        Log::info('JO Tramper Store Request', ['all_data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'id_md_cust'             => 'required|exists:a01_md_customer,id_md_cust',
            'id_md_port'             => 'required|exists:a06_md_port,id_md_port',
            'date_start'             => 'required|date',
            'date_end'               => 'required|date|after_or_equal:date_start',
            'title'                  => 'required|string|max:255',
            'note'                   => 'nullable|string',
            'global_kurs_usd'        => 'nullable|numeric|min:0',
            'global_tgl_kurs_usd'    => 'nullable|date',
            'items'                  => 'nullable|array|min:1',
            'items.*.id_md_invoice'  => 'nullable|exists:a04_md_invoice,id_md_invoice',
            'items.*.pendapatan_idr' => 'nullable|numeric|min:0',
            'items.*.pendapatan_usd' => 'nullable|numeric|min:0',
            'items.*.hpp_ops'        => 'nullable|numeric|min:0',
            'items.*.hargajual_idr'  => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $newJoTramId = IdGenerator::generate('B03', 'b03_jo_tram', 'id_jo_tram');

            $joTramper = JoTramper::create([
                'id_jo_tram' => $newJoTramId,
                'id_md_cust' => $request->id_md_cust,
                'id_md_port' => $request->id_md_port,
                'date_start' => $request->date_start,
                'date_end'   => $request->date_end,
                'title'      => $request->title,
                'note'       => $request->note,
                'sts_proses' => null,
            ]);

            $globalKursUsd    = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            foreach ($request->items ?? [] as $item) {
                JoTramperItem::create([
                    'id_jo_tram'     => $newJoTramId,
                    'id_md_invoice'  => $item['id_md_invoice'],
                    'pendapatan_idr' => $item['pendapatan_idr'],
                    'pendapatan_usd' => $item['pendapatan_usd'],
                    'kurs_usd'       => $globalKursUsd,
                    'tgl_kurs_usd'   => $globalTglKursUsd,
                    'hpp_ops'        => $item['hpp_ops'],
                    'hargajual_idr'  => $item['hargajual_idr'],
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'JO Tramper successfully added', 'data' => $joTramper->load('items')], 201);
            }

            return redirect()->route('jo-tramper.index')->with('success', 'JO Tramper successfully added');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error creating JO Tramper: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error creating JO Tramper: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $joTramper = JoTramper::with(['customer', 'port', 'vessel', 'items.invoice'])
                ->where('id_jo_tram', (string) $id)->firstOrFail();

            $summary = [
                'total_items'         => $joTramper->items->count(),
                'total_revenue_idr'   => $joTramper->items->sum('pendapatan_idr'),
                'total_revenue_usd'   => $joTramper->items->sum('pendapatan_usd'),
                'total_hpp_ops'       => $joTramper->items->sum('hpp_ops'),
                'total_selling_price' => $joTramper->items->sum('hargajual_idr'),
            ];

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $joTramper, 'summary' => $summary]);
            }

            return view('data.jo-tramper.show', compact('joTramper', 'summary'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'JO Tramper not found'], 404);
            }
            return back()->with('error', 'JO Tramper not found');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error loading JO Tramper: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error loading JO Tramper: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        Log::info('=== JO Tramper Edit Request START ===', ['id' => $id]);

        try {
            $joTramper = JoTramper::with(['items.invoice'])
                ->where('id_jo_tram', (string) $id)
                ->firstOrFail();

            $customers = Customer::orderBy('customer')->get();
            $ports     = Port::orderBy('name_port')->get();
            $invoices = Invoice::where('jo_ctg', 'tramper')->orderBy('invoice_ctg')->orderBy('invoice_typ')->get();
            $vessels = Vessel::orderBy('vessel_name')->get();

            if (!view()->exists('data.jo-tramper.edit')) {
                return back()->with('error', 'View file not found: data.jo-tramper.edit');
            }

            return view('data.jo-tramper.edit', compact('joTramper', 'customers', 'ports', 'invoices', 'vessels'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'JO Tramper not found in database');
        } catch (\Exception $e) {
            Log::error('JO Tramper Edit Failed', ['id' => $id, 'error_message' => $e->getMessage()]);
            return back()->with('error', 'Error loading JO Tramper: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_md_cust'             => 'required|exists:a01_md_customer,id_md_cust',
            'id_md_port'             => 'required|exists:a06_md_port,id_md_port',
            'date_start'             => 'required|date',
            'date_end'               => 'required|date|after_or_equal:date_start',
            'title'                  => 'required|string|max:255',
            'note'                   => 'nullable|string',
            'global_kurs_usd'        => 'nullable|numeric|min:0',
            'global_tgl_kurs_usd'    => 'nullable|date',
            'items'                  => 'nullable|array|min:1',
            'items.*.id_md_invoice'  => 'nullable|exists:a04_md_invoice,id_md_invoice',
            'items.*.pendapatan_idr' => 'nullable|numeric|min:0',
            'items.*.pendapatan_usd' => 'nullable|numeric|min:0',
            'items.*.hpp_ops'        => 'nullable|numeric|min:0',
            'items.*.hargajual_idr'  => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $joTramper = JoTramper::where('id_jo_tram', (string) $id)->firstOrFail();

            $joTramper->update([
                'id_md_cust' => $request->id_md_cust,
                'id_md_port' => $request->id_md_port,
                'date_start' => $request->date_start,
                'date_end'   => $request->date_end,
                'title'      => $request->title,
                'note'       => $request->note,
            ]);

            foreach ($joTramper->items as $oldItem) {
                $oldItem->delete();
            }

            $globalKursUsd    = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            foreach ($request->items ?? [] as $item) {
                JoTramperItem::create([
                    'id_jo_tram'     => $id,
                    'id_md_invoice'  => $item['id_md_invoice'],
                    'pendapatan_idr' => $item['pendapatan_idr'],
                    'pendapatan_usd' => $item['pendapatan_usd'],
                    'kurs_usd'       => $globalKursUsd,
                    'tgl_kurs_usd'   => $globalTglKursUsd,
                    'hpp_ops'        => $item['hpp_ops'],
                    'hargajual_idr'  => $item['hargajual_idr'],
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'JO Tramper successfully updated', 'data' => $joTramper->fresh()->load('items')]);
            }

            return redirect()->route('jo-tramper.index')->with('success', 'JO Tramper successfully updated');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating JO Tramper: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error updating JO Tramper: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        Log::info('=== Delete JO Tramper Request START ===', ['id' => $id]);

        DB::beginTransaction();
        try {
            $joTramper = JoTramper::where('id_jo_tram', (string) $id)->firstOrFail();

            $itemsCount = $joTramper->items()->count();

            Log::info('JO Tramper Found', [
                'id'          => $joTramper->id_jo_tram,
                'title'       => $joTramper->title,
                'items_count' => $itemsCount,
            ]);

            if ($itemsCount > 0) {
                foreach ($joTramper->items as $item) {
                    $item->delete();
                }
                Log::info('All items deleted successfully');
            }

            $joTramper->delete();
            DB::commit();

            Log::info('JO Tramper Deleted Successfully', ['id' => $id, 'deleted_items_count' => $itemsCount]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'JO Tramper successfully deleted']);
            }

            return redirect()->route('jo-tramper.index')
                ->with('success', "JO Tramper deleted successfully along with {$itemsCount} item(s)");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'JO Tramper not found'], 404);
            }
            return back()->with('error', 'JO Tramper not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete JO Tramper Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting JO Tramper: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting JO Tramper: ' . $e->getMessage());
        }
    }

    /**
     * Get JO Trampers for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $query = JoTramper::with(['customer', 'port']);

            if ($request->has('id_md_cust')) $query->where('id_md_cust', $request->id_md_cust);
            if ($request->has('id_md_port')) $query->where('id_md_port', $request->id_md_port);
            if ($request->has('sts_proses')) $query->where('sts_proses', $request->sts_proses);

            $joTrampers = $query->orderBy('id_jo_tram', 'desc')->get()->map(function ($joTramper) {
                return [
                    'id'       => $joTramper->id_jo_tram,
                    'text'     => 'JOT-' . $joTramper->id_jo_tram . ' - ' . $joTramper->title,
                    'customer' => $joTramper->customer ? $joTramper->customer->customer : null,
                    'port'     => $joTramper->port ? $joTramper->port->name_port : null,
                    'status'   => $joTramper->sts_proses,
                ];
            });

            return response()->json(['success' => true, 'data' => $joTrampers]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving JO Trampers: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk delete JO Trampers
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|exists:b03_jo_tram,id_jo_tram'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $joTrampers = JoTramper::whereIn('id_jo_tram', $request->ids)->get();
            foreach ($joTrampers as $joTramper) {
                foreach ($joTramper->items as $item) {
                    $item->delete();
                }
                $joTramper->delete();
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'JO Trampers successfully deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting JO Trampers: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export JO Tramper as PDF
     *
     * Route: GET /data/jo-tramper/{id}/export-pdf
     * Name:  jo-tramper.export-pdf
     */
    public function exportPdf($id)
    {
        try {
            $joTramper = JoTramper::with([
                'customer',
                'port',
                'vessel',
                'items.invoice',
            ])->where('id_jo_tram', (string) $id)->firstOrFail();

            // Terbilang (number-to-words) — uses a helper if available,
            // otherwise falls back to a simple inline conversion.
            $totalSell = $joTramper->items->sum('hargajual_idr');
            $terbilang = $this->toTerbilang((int) round($totalSell)) . ' Rupiah';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'data.jo-tramper.pdf',          // resources/views/data/jo-tramper/pdf.blade.php
                compact('joTramper', 'terbilang')
            )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'Arial',
                    'dpi'                  => 150,
                ]);

            $noJo = str_replace(['/', '\\'], '-', $joTramper->no_jo_tram ?? $id);
            $filename = 'JO-Tramper-' . $noJo . '.pdf';
            return $pdf->stream($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'JO Tramper not found'], 404);
            }
            return back()->with('error', 'JO Tramper not found');
        } catch (\Exception $e) {
            Log::error('JO Tramper Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to export PDF: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Simple Indonesian number-to-words (terbilang) helper.
     * Replace with your existing App\Helpers\Terbilang if you have one.
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
            'Sebelas'
        ];

        if ($number === 0)  return 'Nol';
        if ($number < 12)   return $words[$number];
        if ($number < 20)   return $this->toTerbilang($number - 10) . ' Belas';
        if ($number < 100)  return $words[(int)($number / 10)] . ' Puluh' .
            ($number % 10 ? ' ' . $this->toTerbilang($number % 10) : '');
        if ($number < 200)  return 'Seratus' .
            ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 1000) return $words[(int)($number / 100)] . ' Ratus' .
            ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 2000) return 'Seribu' .
            ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000)
            return $this->toTerbilang((int)($number / 1000)) . ' Ribu' .
                ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000_000)
            return $this->toTerbilang((int)($number / 1_000_000)) . ' Juta' .
                ($number % 1_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000) : '');
        if ($number < 1_000_000_000_000)
            return $this->toTerbilang((int)($number / 1_000_000_000)) . ' Miliar' .
                ($number % 1_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000) : '');

        return $this->toTerbilang((int)($number / 1_000_000_000_000)) . ' Triliun' .
            ($number % 1_000_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000_000) : '');
    }
}
