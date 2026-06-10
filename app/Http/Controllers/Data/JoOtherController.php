<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Data\JoOther;
use App\Models\Data\JoOtherItem;
use App\Models\Master\Customer;
use App\Models\Master\Other;
use App\Models\Master\Port;
use App\Models\Master\Invoice;
use App\Models\Master\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Helpers\IdGenerator;

class JoOtherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = JoOther::with(['customer', 'other', 'port', 'vessel', 'items']);

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id_jo_other', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhere('sts_proses', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q) use ($search) {
                            $q->where('customer', 'like', "%{$search}%")
                                ->orWhere('no_customer', 'like', "%{$search}%");
                        })
                        ->orWhereHas('other', function ($q) use ($search) {
                            $q->where('other', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
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

            // Filter by other type
            if ($request->has('id_md_other') && !empty($request->id_md_other)) {
                $query->where('id_md_other', $request->id_md_other);
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
            $joOthers = $query->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $joOthers
                ]);
            }

            // Get customers, others, and ports for filter
            $customers = Customer::orderBy('customer')->get();
            $others = Other::orderBy('other')->get();
            $ports = Port::orderBy('name_port')->get();

            return view('data.jo-other.index', compact('joOthers', 'customers', 'others', 'ports'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving JO Others: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving JO Others: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('customer')->get();
        $others    = Other::orderBy('other')->get();
        $ports     = Port::orderBy('name_port')->get();
        $invoices = Invoice::where('jo_ctg', 'other')->orderBy('invoice_ctg')->orderBy('invoice_typ')->get();
        $vessels   = Vessel::orderBy('vessel_name')->get();
        $previewNoJo  = IdGenerator::generateDocNo('b05_jo_other', 'no_jo_other');

        return view('data.jo-other.create', compact(
            'customers',
            'others',
            'ports',
            'invoices',
            'vessels',
            'previewNoJo'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log input data untuk debugging
        Log::info('JO Other Store Request', [
            'all_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'id_md_cust' => 'required|exists:a01_md_customer,id_md_cust',
            'id_md_other' => 'required|exists:a07_md_other,id_md_other',
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
            'id_md_other.required' => 'Other Type is required',
            'id_md_other.exists' => 'Selected other type does not exist',
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
            Log::error('JO Other Validation Failed', [
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
            // Generate ID untuk JO Other
            $lastJoOther = JoOther::orderBy('id_jo_other', 'desc')->first();
            $newJoOtherId = $lastJoOther ? $lastJoOther->id_jo_other + 1 : 1;

            Log::info('Creating JO Other', [
                'new_id' => $newJoOtherId,
                'other_data' => [
                    'id_md_cust' => $request->id_md_cust,
                    'id_md_other' => $request->id_md_other,
                    'id_md_port' => $request->id_md_port,
                    'date_start' => $request->date_start,
                    'date_end' => $request->date_end,
                    'title' => $request->title,
                    'note' => $request->note,
                    'sts_proses' => $request->sts_proses,
                ]
            ]);

            // Create JO Other
            $joOther = JoOther::create([
                'id_jo_other' => $newJoOtherId,
                'id_md_cust' => $request->id_md_cust,
                'id_md_other' => $request->id_md_other,
                'id_md_port' => $request->id_md_port,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'title' => $request->title,
                'note' => $request->note,
                'sts_proses' => $request->sts_proses,
            ]);

            Log::info('JO Other Created Successfully', [
                'jo_other_id' => $joOther->id_jo_other
            ]);

            // Get global kurs
            $globalKursUsd = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            // Create JO Other Items - ID akan di-generate otomatis oleh Model
            foreach ($request->items as $index => $item) {
                Log::info('Creating JO Other Item', [
                    'item_index' => $index,
                    'item_data' => [
                        'id_jo_other' => $newJoOtherId,
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
                $createdItem = JoOtherItem::create([
                    // TIDAK PERLU SET id_jo_other_item, akan auto-generate
                    'id_jo_other' => $newJoOtherId,
                    'id_md_invoice' => $item['id_md_invoice'],
                    'pendapatan_idr' => $item['pendapatan_idr'],
                    'pendapatan_usd' => $item['pendapatan_usd'],
                    'kurs_usd' => $globalKursUsd,
                    'tgl_kurs_usd' => $globalTglKursUsd,
                    'hpp_ops' => $item['hpp_ops'],
                    'hargajual_idr' => $item['hargajual_idr'],
                ]);

                Log::info('JO Other Item Created', [
                    'item_id' => $createdItem->id_jo_other_item
                ]);
            }

            DB::commit();

            Log::info('JO Other Transaction Committed Successfully', [
                'jo_other_id' => $newJoOtherId,
                'total_items' => count($request->items)
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Other successfully added',
                    'data' => $joOther->load('items')
                ], 201);
            }

            return redirect()
                ->route('jo-other.index')
                ->with('success', 'JO Other successfully added with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('JO Other Store Failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating JO Other: ' . $e->getMessage(),
                    'error_detail' => [
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating JO Other: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        Log::info('=== JO Other Show Request START ===', [
            'id' => $id,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id() ?? 'guest',
            'ip' => $request->ip()
        ]);

        try {
            Log::info('Attempting to load JO Other', [
                'id' => $id
            ]);

            $joOther = JoOther::with([
                'customer',
                'other',
                'port',
                'items.invoice'
            ])->findOrFail($id);

            Log::info('JO Other loaded successfully', [
                'id' => $joOther->id_jo_other,
                'title' => $joOther->title,
                'items_count' => $joOther->items->count()
            ]);

            // Calculate summary
            $summary = [
                'total_items' => $joOther->items->count(),
                'total_revenue_idr' => $joOther->items->sum('pendapatan_idr'),
                'total_revenue_usd' => $joOther->items->sum('pendapatan_usd'),
                'total_hpp_ops' => $joOther->items->sum('hpp_ops'),
                'total_selling_price' => $joOther->items->sum('hargajual_idr'),
            ];

            Log::info('Summary calculated', $summary);

            if ($request->expectsJson()) {
                Log::info('Returning JSON response');
                return response()->json([
                    'success' => true,
                    'data' => $joOther,
                    'summary' => $summary
                ]);
            }

            Log::info('Attempting to load view: data.jo-other.show');

            // Check if view exists
            if (!view()->exists('data.jo-other.show')) {
                Log::error('VIEW NOT FOUND: data.jo-other.show', [
                    'expected_path' => 'resources/views/data/jo-other/show.blade.php',
                    'searched_paths' => config('view.paths')
                ]);

                return back()->with('error', 'View file not found: data.jo-other.show');
            }

            Log::info('View exists, rendering...');

            return view('data.jo-other.show', compact('joOther', 'summary'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('JO Other NOT FOUND in database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'JO Other not found'
                ], 404);
            }

            return back()->with('error', 'JO Other not found');
        } catch (\Exception $e) {
            Log::error('JO Other Show Failed', [
                'id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading JO Other: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error loading JO Other: ' . $e->getMessage());
        } finally {
            Log::info('=== JO Other Show Request END ===');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        Log::info('=== JO Other Edit Request START ===', [
            'id' => $id,
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'user_id' => auth()->id() ?? 'guest',
            'ip' => request()->ip()
        ]);

        try {
            Log::info('Attempting to load JO Other for editing', [
                'id' => $id
            ]);

            $joOther = JoOther::with(['items.invoice'])->findOrFail($id);

            Log::info('JO Other loaded successfully', [
                'id' => $joOther->id_jo_other,
                'title' => $joOther->title,
                'items_count' => $joOther->items->count()
            ]);

            Log::info('Loading related data (customers, others, ports, invoices)');
            $customers = Customer::orderBy('customer')->get();
            $others    = Other::orderBy('other')->get();
            $ports     = Port::orderBy('name_port')->get();
            $invoices = Invoice::where('jo_ctg', 'other')->orderBy('invoice_ctg')->orderBy('invoice_typ')->get();
            $vessels   = Vessel::orderBy('vessel_name')->get();

            Log::info('Invoices loaded', ['count' => $invoices->count()]);

            Log::info('Attempting to load view: data.jo-other.edit');

            // Check if view exists
            if (!view()->exists('data.jo-other.edit')) {
                Log::error('VIEW NOT FOUND: data.jo-other.edit', [
                    'expected_path' => 'resources/views/data/jo-other/edit.blade.php',
                    'searched_paths' => config('view.paths')
                ]);

                return back()->with('error', 'View file not found: data.jo-other.edit');
            }

            Log::info('View exists, rendering...');
            return view('data.jo-other.edit', compact('joOther', 'customers', 'others', 'ports', 'invoices', 'vessels'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('JO Other NOT FOUND in database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'JO Other not found in database');
        } catch (\Exception $e) {
            Log::error('JO Other Edit Failed', [
                'id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            return back()->with('error', 'Error loading JO Other: ' . $e->getMessage());
        } finally {
            Log::info('=== JO Other Edit Request END ===');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Log input data untuk debugging
        Log::info('JO Other Update Request', [
            'id' => $id,
            'all_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'id_md_cust' => 'required|exists:a01_md_customer,id_md_cust',
            'id_md_other' => 'required|exists:a07_md_other,id_md_other',
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
            'id_md_other.required' => 'Other Type is required',
            'id_md_other.exists' => 'Selected other type does not exist',
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
            Log::error('JO Other Update Validation Failed', [
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
            $joOther = JoOther::findOrFail($id);

            Log::info('Updating JO Other', [
                'id' => $id,
                'old_data' => $joOther->toArray()
            ]);

            // Update JO Other
            $joOther->update([
                'id_md_cust' => $request->id_md_cust,
                'id_md_other' => $request->id_md_other,
                'id_md_port' => $request->id_md_port,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'title' => $request->title,
                'note' => $request->note,
                'sts_proses' => $request->sts_proses,
            ]);

            Log::info('JO Other Updated Successfully', [
                'id' => $id
            ]);

            // Delete old items
            $oldItems = $joOther->items;
            Log::info('Deleting old JO Other Items', [
                'jo_other_id' => $id,
                'old_items_count' => $oldItems->count(),
                'old_item_ids' => $oldItems->pluck('id_jo_other_item')->toArray()
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
                Log::info('Creating new JO Other Item', [
                    'item_index' => $index,
                    'item_data' => [
                        'id_jo_other' => $id,
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
                $createdItem = JoOtherItem::create([
                    'id_jo_other' => $id,
                    'id_md_invoice' => $item['id_md_invoice'],
                    'pendapatan_idr' => $item['pendapatan_idr'],
                    'pendapatan_usd' => $item['pendapatan_usd'],
                    'kurs_usd' => $globalKursUsd,
                    'tgl_kurs_usd' => $globalTglKursUsd,
                    'hpp_ops' => $item['hpp_ops'],
                    'hargajual_idr' => $item['hargajual_idr'],
                ]);

                Log::info('JO Other Item Created Successfully', [
                    'item_id' => $createdItem->id_jo_other_item
                ]);
            }

            DB::commit();

            Log::info('JO Other Update Transaction Committed Successfully', [
                'jo_other_id' => $id,
                'total_new_items' => count($request->items)
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'JO Other successfully updated',
                    'data' => $joOther->fresh()->load('items')
                ]);
            }

            return redirect()
                ->route('jo-other.index')
                ->with('success', 'JO Other successfully updated with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('JO Other Update Failed', [
                'id' => $id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating JO Other: ' . $e->getMessage(),
                    'error_detail' => [
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating JO Other: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        Log::info('=== Delete JO Other Request START ===', ['id' => $id]);

        DB::beginTransaction();
        try {
            $joOther = JoOther::findOrFail($id);

            $itemsCount = $joOther->items()->count();

            Log::info('JO Other Found', [
                'id'          => $joOther->id_jo_other,
                'title'       => $joOther->title,
                'items_count' => $itemsCount,
            ]);

            if ($itemsCount > 0) {
                foreach ($joOther->items as $item) {
                    $item->delete();
                }
                Log::info('All items deleted successfully');
            }

            $joOther->delete();
            DB::commit();

            Log::info('JO Other Deleted Successfully', ['id' => $id, 'deleted_items_count' => $itemsCount]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'JO Other successfully deleted']);
            }

            return redirect()->route('jo-other.index')
                ->with('success', "JO Other deleted successfully along with {$itemsCount} item(s)");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'JO Other not found'], 404);
            }
            return back()->with('error', 'JO Other not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete JO Other Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting JO Other: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting JO Other: ' . $e->getMessage());
        }
    }

    /**
     * Get JO Others for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $query = JoOther::with(['customer', 'other', 'port']);

            // Filter by customer if provided
            if ($request->has('id_md_cust')) {
                $query->where('id_md_cust', $request->id_md_cust);
            }

            // Filter by other type if provided
            if ($request->has('id_md_other')) {
                $query->where('id_md_other', $request->id_md_other);
            }

            // Filter by port if provided
            if ($request->has('id_md_port')) {
                $query->where('id_md_port', $request->id_md_port);
            }

            // Filter by status if provided
            if ($request->has('sts_proses')) {
                $query->where('sts_proses', $request->sts_proses);
            }

            $joOthers = $query->orderBy('id_jo_other', 'desc')
                ->get()
                ->map(function ($joOther) {
                    return [
                        'id' => $joOther->id_jo_other,
                        'text' => 'JOO-' . $joOther->id_jo_other . ' - ' . $joOther->title,
                        'customer' => $joOther->customer ? $joOther->customer->customer : null,
                        'other' => $joOther->other ? $joOther->other->other : null,
                        'port' => $joOther->port ? $joOther->port->name_port : null,
                        'status' => $joOther->sts_proses,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $joOthers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving JO Others: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete JO Others
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:b05_jo_other,id_jo_other'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $joOthers = JoOther::whereIn('id_jo_other', $request->ids)->get();

            foreach ($joOthers as $joOther) {
                // Soft delete all items
                foreach ($joOther->items as $item) {
                    $item->delete();
                }
                // Soft delete the other
                $joOther->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'JO Others successfully deleted'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting JO Others: ' . $e->getMessage()
            ], 500);
        }
    }

    // Tambah storeHeader()
    public function storeHeader(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_md_cust'   => 'required|exists:a01_md_customer,id_md_cust',
                'id_md_other'  => 'required|exists:a07_md_other,id_md_other',
                'id_md_port'   => 'required|exists:a06_md_port,id_md_port',
                'id_md_vessel' => 'nullable|exists:a05_md_vessel,id_md_vessel',
                'tgl_jo_other' => 'required|date',
                'date_start'   => 'required|date',
                'date_end'     => 'required|date|after_or_equal:date_start',
                'title'        => 'required|string|max:255',
                'note'         => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $newId = IdGenerator::generate('B05', 'b05_jo_other', 'id_jo_other');
            $noJoOther = IdGenerator::generateDocNo('b05_jo_other', 'no_jo_other');

            $joOther = JoOther::create([
                'id_jo_other'  => $newId,
                'no_jo_other'  => $noJoOther,
                'tgl_jo_other' => $request->tgl_jo_other,
                'id_md_cust'   => $request->id_md_cust,
                'id_md_other'  => $request->id_md_other,
                'id_md_port'   => $request->id_md_port,
                'id_md_vessel' => $request->id_md_vessel,
                'date_start'   => $request->date_start,
                'date_end'     => $request->date_end,
                'title'        => $request->title,
                'note'         => $request->note,
            ]);

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'JO Other header saved successfully',
                'redirect_url' => route('jo-other.edit', $newId),
                'data'         => ['id' => $newId]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Tambah updateHeader()
    public function updateHeader(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_md_cust'   => 'required|exists:a01_md_customer,id_md_cust',
                'id_md_other'  => 'required|exists:a07_md_other,id_md_other',
                'id_md_port'   => 'required|exists:a06_md_port,id_md_port',
                'id_md_vessel' => 'nullable|exists:a05_md_vessel,id_md_vessel',
                'date_start'   => 'required|date',
                'date_end'     => 'required|date|after_or_equal:date_start',
                'title'        => 'required|string|max:255',
                'note'         => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $joOther = JoOther::findOrFail($id);
            $joOther->update([
                'tgl_jo_other' => $request->tgl_jo_other,
                'id_md_cust'   => $request->id_md_cust,
                'id_md_other'  => $request->id_md_other,
                'id_md_port'   => $request->id_md_port,
                'id_md_vessel' => $request->id_md_vessel,
                'date_start'   => $request->date_start,
                'date_end'     => $request->date_end,
                'title'        => $request->title,
                'note'         => $request->note,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Header updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Tambah storeItem()
    public function storeItem(Request $request)
    {
        try {
            $rules = [
                'id_jo_other'    => 'required|exists:b05_jo_other,id_jo_other',
                'id_md_invoice'  => 'required|exists:a04_md_invoice,id_md_invoice',
                'invoice_ctg'    => 'required|string',
                'pendapatan_idr' => 'nullable|numeric|min:0',
                'pendapatan_usd' => 'nullable|numeric|min:0',
                'hpp_ops'        => 'nullable|numeric|min:0',
                'note'           => 'nullable|string',
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

            $item = JoOtherItem::create([
                'id_jo_other'    => $request->id_jo_other,
                'id_md_invoice'  => $request->id_md_invoice,
                'pendapatan_idr' => $pendapatanIDR,
                'pendapatan_usd' => $pendapatanUSD,
                'kurs_usd'       => $kursUSD,
                'tgl_kurs_usd'   => $request->tgl_kurs_usd,
                'hpp_ops'        => $hppOps,
                'hargajual_idr'  => $hargajualIDR,
                'note'           => $request->note,
            ]);

            $item->load('invoice');
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data'    => [
                    'id'             => $item->id_jo_other_item,
                    'id_jo_other_item' => $item->id_jo_other_item,
                    'invoice_ctg'    => $request->invoice_ctg,
                    'invoice_typ'    => $item->invoice?->invoice_typ,
                    'pendapatan_idr' => (float) $item->pendapatan_idr,
                    'pendapatan_usd' => (float) $item->pendapatan_usd,
                    'kurs_usd'       => $item->kurs_usd ? (float) $item->kurs_usd : null,
                    'tgl_kurs_usd'   => $item->tgl_kurs_usd?->format('Y-m-d'),
                    'hpp_ops'        => (float) $item->hpp_ops,
                    'hargajual_idr'  => (float) $item->hargajual_idr,
                ]
            ], 201);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Tambah showItem()
    public function showItem($id)
    {
        try {
            $item = JoOtherItem::with('invoice')
                ->where('id_jo_other_item', (string) $id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data'    => [
                    'id_jo_other_item' => $item->id_jo_other_item,
                    'id_md_invoice'    => $item->id_md_invoice,
                    'invoice_ctg'      => $item->invoice->invoice_ctg,
                    'invoice_typ'      => $item->invoice->invoice_typ,
                    'pendapatan_idr'   => $item->pendapatan_idr,
                    'pendapatan_usd'   => $item->pendapatan_usd,
                    'hpp_ops'          => $item->hpp_ops,
                    'kurs_usd'         => $item->kurs_usd,
                    'tgl_kurs_usd'     => $item->tgl_kurs_usd?->format('Y-m-d'),
                    'hargajual_idr'    => $item->hargajual_idr,
                    'note'             => $item->note,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Item not found: ' . $e->getMessage()], 404);
        }
    }

    // Tambah updateItem()
    public function updateItem(Request $request, $id)
    {
        try {
            $rules = [
                'id_jo_other'    => 'required|exists:b05_jo_other,id_jo_other',
                'id_md_invoice'  => 'required|exists:a04_md_invoice,id_md_invoice',
                'invoice_ctg'    => 'required|string',
                'pendapatan_idr' => 'nullable|numeric|min:0',
                'pendapatan_usd' => 'nullable|numeric|min:0',
                'hpp_ops'        => 'nullable|numeric|min:0',
                'note'           => 'nullable|string',
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

            $item = JoOtherItem::where('id_jo_other_item', (string) $id)->firstOrFail();
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
                'note'           => $request->note,
            ]);

            $item->refresh()->load('invoice');
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data'    => [
                    'id'             => $item->id_jo_other_item,
                    'id_jo_other_item' => $item->id_jo_other_item,
                    'invoice_ctg'    => $request->invoice_ctg,
                    'invoice_typ'    => $item->invoice?->invoice_typ,
                    'pendapatan_idr' => (float) $item->pendapatan_idr,
                    'pendapatan_usd' => (float) $item->pendapatan_usd,
                    'kurs_usd'       => $item->kurs_usd ? (float) $item->kurs_usd : null,
                    'tgl_kurs_usd'   => $request->tgl_kurs_usd,
                    'hpp_ops'        => (float) $item->hpp_ops,
                    'hargajual_idr'  => (float) $item->hargajual_idr,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Tambah destroyItem()
    public function destroyItem($id)
    {
        try {
            DB::beginTransaction();
            $item = JoOtherItem::where('id_jo_other_item', (string) $id)->firstOrFail();
            $item->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Item deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Tambah getItems()
    public function getItems($joOtherId)
    {
        try {
            $items = JoOtherItem::where('id_jo_other', $joOtherId)->with('invoice')->orderBy('created_at')->get();
            return response()->json(['success' => true, 'data' => $items]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Tambah saveAllChanges()
    public function saveAllChanges(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_md_cust'          => 'required|exists:a01_md_customer,id_md_cust',
                'id_md_other'         => 'required|exists:a07_md_other,id_md_other',
                'id_md_port'          => 'required|exists:a06_md_port,id_md_port',
                'id_md_vessel'        => 'nullable|exists:a05_md_vessel,id_md_vessel',
                'date_start'          => 'required|date',
                'date_end'            => 'required|date|after_or_equal:date_start',
                'title'               => 'required|string|max:255',
                'note'                => 'nullable|string',
                'global_tgl_kurs_usd' => 'nullable|date',
                'global_kurs_usd'     => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $joOther = JoOther::findOrFail($id);
            $joOther->update([
                'id_md_cust'   => $request->id_md_cust,
                'id_md_other'  => $request->id_md_other,
                'id_md_port'   => $request->id_md_port,
                'id_md_vessel' => $request->id_md_vessel,
                'date_start'   => $request->date_start,
                'date_end'     => $request->date_end,
                'title'        => $request->title,
                'note'         => $request->note,
            ]);

            $globalKursUsd    = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            foreach (JoOtherItem::where('id_jo_other', $id)->get() as $item) {
                if ($item->pendapatan_usd > 0 && $globalKursUsd) {
                    $item->update([
                        'kurs_usd'      => $globalKursUsd,
                        'tgl_kurs_usd'  => $globalTglKursUsd,
                        'hargajual_idr' => $item->pendapatan_usd * $globalKursUsd,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'All changes saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export JO Other as PDF
     *
     * Route: GET /data/jo-other/{id}/export-pdf
     * Name:  jo-other.export-pdf
     */
    public function exportPdf($id)
    {
        try {
            $joOther = JoOther::with([
                'customer',
                'other',
                'port',
                'vessel',
                'items.invoice',
            ])->findOrFail($id);

            $totalSell = $joOther->items->sum('hargajual_idr');
            $terbilang = $this->toTerbilang((int) round($totalSell)) . ' Rupiah';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'data.jo-other.pdf',        // resources/views/data/jo-other/pdf.blade.php
                compact('joOther', 'terbilang')
            )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'Arial',
                    'dpi'                  => 150,
                ]);

            $noJo     = str_replace(['/', '\\'], '-', $joOther->no_jo_other ?? $id);
            $filename = 'JO-Other-' . $noJo . '.pdf';
            return $pdf->stream($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'JO Other not found'], 404);
            }
            return back()->with('error', 'JO Other not found');
        } catch (\Exception $e) {
            Log::error('JO Other Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
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
