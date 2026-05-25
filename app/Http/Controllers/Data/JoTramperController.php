<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Data\JoTramper;
use App\Models\Data\JoTramperItem;
use App\Models\Master\Customer;
use App\Models\Master\Port;
use App\Models\Master\Invoice;
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

            // Search functionality
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

            // Filter by customer
            if ($request->has('id_md_cust') && !empty($request->id_md_cust)) {
                $query->where('id_md_cust', $request->id_md_cust);
            }

            // Filter by port
            if ($request->has('id_md_port') && !empty($request->id_md_port)) {
                $query->where('id_md_port', $request->id_md_port);
            }

            // Filter by status
            if ($request->has('sts_proses') && !empty($request->sts_proses)) {
                $query->where('sts_proses', $request->sts_proses);
            }

            // Filter by date range
            if ($request->has('date_start') && !empty($request->date_start)) {
                $query->where('date_start', '>=', $request->date_start);
            }

            if ($request->has('date_end') && !empty($request->date_end)) {
                $query->where('date_end', '<=', $request->date_end);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $joTrampers = $query->paginate($perPage);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $joTrampers
                ]);
            }

            // Get customers and ports for filter
            $customers = Customer::orderBy('customer')->get();
            $ports = Port::orderBy('name_port')->get();

            return view('data.jo-tramper.index', compact('joTrampers', 'customers', 'ports'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving JO Trampers: ' . $e->getMessage()
                ], 500);
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
        $ports = Port::orderBy('name_port')->get();
        $invoices = Invoice::orderBy('id')->get();

        return view('data.jo-tramper.create', compact('customers', 'ports', 'invoices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log input data untuk debugging
        Log::info('JO Tramper Store Request', [
            'all_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'id_md_cust' => 'required|exists:a01_md_customer,id_md_cust',
            'id_md_port' => 'required|exists:a06_md_port,id_md_port',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'title' => 'required|string|max:255',
            'note' => 'nullable|string',
            'sts_proses' => 'nullable|string|max:50',

            // Global kurs validation
            'global_kurs_usd' => 'nullable|numeric|min:0',
            'global_tgl_kurs_usd' => 'nullable|date',

            // Validation untuk items
            'items' => 'nullable|array|min:1',
            'items.*.id_md_invoice' => 'nullable|exists:a04_md_invoice,id_md_invoice',
            'items.*.invoice_ctg' => 'nullable|string',
            'items.*.pendapatan_idr' => 'nullable|numeric|min:0',
            'items.*.pendapatan_usd' => 'nullable|numeric|min:0',
            'items.*.hpp_ops' => 'nullable|numeric|min:0',
            'items.*.hargajual_idr' => 'nullable|numeric|min:0',
        ], [
            'id_md_cust.required' => 'Customer is required',
            'id_md_cust.exists' => 'Selected customer does not exist',
            'id_md_port.required' => 'Port is required',
            'id_md_port.exists' => 'Selected port does not exist',
            'date_start.required' => 'Start date is required',
            'date_end.required' => 'End date is required',
            'date_end.after_or_equal' => 'End date must be after or equal to start date',
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
            Log::error('JO Tramper Validation Failed', [
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
            // Generate ID untuk JO Tramper
            $lastJoTramper = JoTramper::orderBy('id_jo_tram', 'desc')->first();
            $newJoTramId = $lastJoTramper ? $lastJoTramper->id_jo_tram + 1 : 1;

            Log::info('Creating JO Tramper', [
                'new_id' => $newJoTramId,
                'tramper_data' => [
                    'id_md_cust' => $request->id_md_cust,
                    'id_md_port' => $request->id_md_port,
                    'date_start' => $request->date_start,
                    'date_end' => $request->date_end,
                    'title' => $request->title,
                    'note' => $request->note,
                    'sts_proses' => $request->sts_proses,
                ]
            ]);

            // Create JO Tramper
            $joTramper = JoTramper::create([
                'id_jo_tram' => $newJoTramId,
                'id_md_cust' => $request->id_md_cust,
                'id_md_port' => $request->id_md_port,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'title' => $request->title,
                'note' => $request->note,
                'sts_proses' => $request->sts_proses,
            ]);

            Log::info('JO Tramper Created Successfully', [
                'jo_tramper_id' => $joTramper->id_jo_tram
            ]);

            // Get global kurs
            $globalKursUsd = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            // Create JO Tramper Items - ID akan di-generate otomatis oleh Model
            foreach ($request->items as $index => $item) {
                Log::info('Creating JO Tramper Item', [
                    'item_index' => $index,
                    'item_data' => [
                        'id_jo_tram' => $newJoTramId,
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
                $createdItem = JoTramperItem::create([
                    // TIDAK PERLU SET id_jo_tram_item, akan auto-generate
                    'id_jo_tram' => $newJoTramId,
                    'id_md_invoice' => $item['id_md_invoice'],
                    'pendapatan_idr' => $item['pendapatan_idr'],
                    'pendapatan_usd' => $item['pendapatan_usd'],
                    'kurs_usd' => $globalKursUsd,
                    'tgl_kurs_usd' => $globalTglKursUsd,
                    'hpp_ops' => $item['hpp_ops'],
                    'hargajual_idr' => $item['hargajual_idr'],
                ]);

                Log::info('JO Tramper Item Created', [
                    'item_id' => $createdItem->id_jo_tram_item
                ]);
            }

            DB::commit();

            Log::info('JO Tramper Transaction Committed Successfully', [
                'jo_tramper_id' => $newJoTramId,
                'total_items' => count($request->items)
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Tramper successfully added',
                    'data' => $joTramper->load('items')
                ], 201);
            }

            return redirect()
                ->route('jo-tramper.index')
                ->with('success', 'JO Tramper successfully added with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('JO Tramper Store Failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating JO Tramper: ' . $e->getMessage(),
                    'error_detail' => [
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating JO Tramper: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        Log::info('=== JO Tramper Show Request START ===', [
            'id' => $id,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id() ?? 'guest',
            'ip' => $request->ip()
        ]);

        try {
            Log::info('Attempting to load JO Tramper', [
                'id' => $id
            ]);

            $joTramper = JoTramper::with([
                'customer',
                'port',
                'items.invoice'
            ])->findOrFail($id);

            Log::info('JO Tramper loaded successfully', [
                'id' => $joTramper->id_jo_tram,
                'title' => $joTramper->title,
                'items_count' => $joTramper->items->count()
            ]);

            // Calculate summary
            $summary = [
                'total_items' => $joTramper->items->count(),
                'total_revenue_idr' => $joTramper->items->sum('pendapatan_idr'),
                'total_revenue_usd' => $joTramper->items->sum('pendapatan_usd'),
                'total_hpp_ops' => $joTramper->items->sum('hpp_ops'),
                'total_selling_price' => $joTramper->items->sum('hargajual_idr'),
            ];

            Log::info('Summary calculated', $summary);

            if ($request->expectsJson()) {
                Log::info('Returning JSON response');
                return response()->json([
                    'success' => true,
                    'data' => $joTramper,
                    'summary' => $summary
                ]);
            }

            Log::info('Attempting to load view: data.jo-tramper.show');

            // Check if view exists
            if (!view()->exists('data.jo-tramper.show')) {
                Log::error('VIEW NOT FOUND: data.jo-tramper.show', [
                    'expected_path' => 'resources/views/data/jo-tramper/show.blade.php',
                    'searched_paths' => config('view.paths')
                ]);

                return back()->with('error', 'View file not found: data.jo-tramper.show');
            }

            Log::info('View exists, rendering...');

            return view('data.jo-tramper.show', compact('joTramper', 'summary'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('JO Tramper NOT FOUND in database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'JO Tramper not found'
                ], 404);
            }

            return back()->with('error', 'JO Tramper not found');
        } catch (\Exception $e) {
            Log::error('JO Tramper Show Failed', [
                'id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading JO Tramper: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error loading JO Tramper: ' . $e->getMessage());
        } finally {
            Log::info('=== JO Tramper Show Request END ===');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        Log::info('=== JO Tramper Edit Request START ===', [
            'id' => $id,
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'user_id' => auth()->id() ?? 'guest',
            'ip' => request()->ip()
        ]);

        try {
            Log::info('Attempting to load JO Tramper for editing', [
                'id' => $id
            ]);

            $joTramper = JoTramper::with(['items.invoice'])->findOrFail($id);

            Log::info('JO Tramper loaded successfully', [
                'id' => $joTramper->id_jo_tram,
                'title' => $joTramper->title,
                'items_count' => $joTramper->items->count()
            ]);

            Log::info('Loading related data (customers, ports, invoices)');

            $customers = Customer::orderBy('customer')->get();

            Log::info('Customers loaded', ['count' => $customers->count()]);

            $ports = Port::orderBy('name_port')->get();

            Log::info('Ports loaded', ['count' => $ports->count()]);

            $invoices = Invoice::orderBy('id')->get();

            Log::info('Invoices loaded', ['count' => $invoices->count()]);

            Log::info('Attempting to load view: data.jo-tramper.edit');

            // Check if view exists
            if (!view()->exists('data.jo-tramper.edit')) {
                Log::error('VIEW NOT FOUND: data.jo-tramper.edit', [
                    'expected_path' => 'resources/views/data/jo-tramper/edit.blade.php',
                    'searched_paths' => config('view.paths')
                ]);

                return back()->with('error', 'View file not found: data.jo-tramper.edit');
            }

            Log::info('View exists, rendering...');

            return view('data.jo-tramper.edit', compact('joTramper', 'customers', 'ports', 'invoices'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('JO Tramper NOT FOUND in database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'JO Tramper not found in database');
        } catch (\Exception $e) {
            Log::error('JO Tramper Edit Failed', [
                'id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            return back()->with('error', 'Error loading JO Tramper: ' . $e->getMessage());
        } finally {
            Log::info('=== JO Tramper Edit Request END ===');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Log input data untuk debugging
        Log::info('JO Tramper Update Request', [
            'id' => $id,
            'all_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'id_md_cust' => 'required|exists:a01_md_customer,id_md_cust',
            'id_md_port' => 'required|exists:a06_md_port,id_md_port',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'title' => 'required|string|max:255',
            'note' => 'nullable|string',
            'sts_proses' => 'nullable|string|max:50',

            // Global kurs validation
            'global_kurs_usd' => 'nullable|numeric|min:0',
            'global_tgl_kurs_usd' => 'nullable|date',

            // Validation untuk items
            'items' => 'nullable|array|min:1',
            'items.*.id_md_invoice' => 'nullable|exists:a04_md_invoice,id_md_invoice',
            'items.*.pendapatan_idr' => 'nullable|numeric|min:0',
            'items.*.pendapatan_usd' => 'nullable|numeric|min:0',
            'items.*.hpp_ops' => 'nullable|numeric|min:0',
            'items.*.hargajual_idr' => 'nullable|numeric|min:0',
        ], [
            'id_md_cust.required' => 'Customer is required',
            'id_md_cust.exists' => 'Selected customer does not exist',
            'id_md_port.required' => 'Port is required',
            'id_md_port.exists' => 'Selected port does not exist',
            'date_start.required' => 'Start date is required',
            'date_end.required' => 'End date is required',
            'date_end.after_or_equal' => 'End date must be after or equal to start date',
            'title.required' => 'Title is required',
            'global_kurs_usd.required' => 'Exchange rate is required',
            'global_tgl_kurs_usd.required' => 'Exchange rate date is required',
            'items.required' => 'At least one item is required',
            'items.min' => 'At least one item is required',
        ]);

        if ($validator->fails()) {
            Log::error('JO Tramper Update Validation Failed', [
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
            $joTramper = JoTramper::findOrFail($id);

            Log::info('Updating JO Tramper', [
                'id' => $id,
                'old_data' => $joTramper->toArray()
            ]);

            // Update JO Tramper
            $joTramper->update([
                'id_md_cust' => $request->id_md_cust,
                'id_md_port' => $request->id_md_port,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'title' => $request->title,
                'note' => $request->note,
                'sts_proses' => $request->sts_proses,
            ]);

            Log::info('JO Tramper Updated Successfully', [
                'id' => $id
            ]);

            // Delete old items
            $oldItems = $joTramper->items;
            Log::info('Deleting old JO Tramper Items', [
                'jo_tram_id' => $id,
                'old_items_count' => $oldItems->count(),
                'old_item_ids' => $oldItems->pluck('id_jo_tram_item')->toArray()
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
                Log::info('Creating new JO Tramper Item', [
                    'item_index' => $index,
                    'item_data' => [
                        'id_jo_tram' => $id,
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
                $createdItem = JoTramperItem::create([
                    'id_jo_tram' => $id,
                    'id_md_invoice' => $item['id_md_invoice'],
                    'pendapatan_idr' => $item['pendapatan_idr'],
                    'pendapatan_usd' => $item['pendapatan_usd'],
                    'kurs_usd' => $globalKursUsd,
                    'tgl_kurs_usd' => $globalTglKursUsd,
                    'hpp_ops' => $item['hpp_ops'],
                    'hargajual_idr' => $item['hargajual_idr'],
                ]);

                Log::info('JO Tramper Item Created Successfully', [
                    'item_id' => $createdItem->id_jo_tram_item
                ]);
            }

            DB::commit();

            Log::info('JO Tramper Update Transaction Committed Successfully', [
                'jo_tramper_id' => $id,
                'total_new_items' => count($request->items)
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Tramper successfully updated',
                    'data' => $joTramper->fresh()->load('items')
                ]);
            }

            return redirect()
                ->route('jo-tramper.index')
                ->with('success', 'JO Tramper successfully updated with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('JO Tramper Update Failed', [
                'id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating JO Tramper: ' . $e->getMessage(),
                    'error_detail' => [
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating JO Tramper: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $joTramper = JoTramper::findOrFail($id);

            // Check if has items
            if ($joTramper->items()->count() > 0) {
                // Soft delete all items first
                foreach ($joTramper->items as $item) {
                    $item->delete();
                }
            }

            // Soft delete the tramper
            $joTramper->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Tramper successfully deleted'
                ]);
            }

            return redirect()
                ->route('jo-tramper.index')
                ->with('success', 'JO Tramper successfully deleted');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting JO Tramper: ' . $e->getMessage()
                ], 500);
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

            // Filter by customer if provided
            if ($request->has('id_md_cust')) {
                $query->where('id_md_cust', $request->id_md_cust);
            }

            // Filter by port if provided
            if ($request->has('id_md_port')) {
                $query->where('id_md_port', $request->id_md_port);
            }

            // Filter by status if provided
            if ($request->has('sts_proses')) {
                $query->where('sts_proses', $request->sts_proses);
            }

            $joTrampers = $query->orderBy('id_jo_tram', 'desc')
                ->get()
                ->map(function ($joTramper) {
                    return [
                        'id' => $joTramper->id_jo_tram,
                        'text' => 'JOT-' . $joTramper->id_jo_tram . ' - ' . $joTramper->title,
                        'customer' => $joTramper->customer ? $joTramper->customer->customer : null,
                        'port' => $joTramper->port ? $joTramper->port->name_port : null,
                        'status' => $joTramper->sts_proses,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $joTrampers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving JO Trampers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete JO Trampers
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:b03_jo_tram,id_jo_tram'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $joTrampers = JoTramper::whereIn('id_jo_tram', $request->ids)->get();

            foreach ($joTrampers as $joTramper) {
                // Soft delete all items
                foreach ($joTramper->items as $item) {
                    $item->delete();
                }
                // Soft delete the tramper
                $joTramper->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'JO Trampers successfully deleted'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting JO Trampers: ' . $e->getMessage()
            ], 500);
        }
    }
}