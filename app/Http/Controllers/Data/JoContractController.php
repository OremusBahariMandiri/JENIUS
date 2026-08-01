<?php

namespace App\Http\Controllers\Data;

use App\Helpers\IdGenerator;
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
    public function index(Request $request)
    {
        try {
            $query = JoContract::with(['contract.customer', 'area', 'items']);

            if ($request->filled('no_jo')) {
                $query->where('no_jo_cont', 'like', '%' . $request->no_jo . '%');
            }
            if ($request->filled('no_contract')) {
                $query->whereHas('contract', function ($q) use ($request) {
                    $q->where('no_contract', 'like', '%' . $request->no_contract . '%');
                });
            }
            if ($request->filled('contract_name')) {
                $query->whereHas('contract', function ($q) use ($request) {
                    $q->where('contract', 'like', '%' . $request->contract_name . '%');
                });
            }
            if ($request->filled('id_md_cust')) {
                $query->whereHas('contract.customer', function ($q) use ($request) {
                    $q->where('id_md_cust', $request->id_md_cust);
                });
            }
            if ($request->filled('id_md_area')) {
                $query->where('id_md_area', $request->id_md_area);
            }
            if ($request->filled('title')) {
                $query->where('title', 'like', '%' . $request->title . '%');
            }
            if ($request->filled('search')) {
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

            $sortBy    = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $joContracts = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $joContracts]);
            }

            $contracts = Contract::with('customer')->orderBy('no_contract')->get();
            $areas     = Area::orderBy('area')->get();
            $customers = \App\Models\Master\Customer::orderBy('customer')->get();

            $currentFilters = [
                'no_jo'         => $request->get('no_jo', ''),
                'no_contract'   => $request->get('no_contract', ''),
                'contract_name' => $request->get('contract_name', ''),
                'id_md_cust'    => $request->get('id_md_cust', ''),
                'id_md_area'    => $request->get('id_md_area', ''),
                'title'         => $request->get('title', ''),
            ];

            if (!empty($currentFilters['id_md_cust'])) {
                $cust = $customers->firstWhere('id_md_cust', $currentFilters['id_md_cust']);
                $currentFilters['customer_label'] = $cust ? $cust->customer : $currentFilters['id_md_cust'];
            }
            if (!empty($currentFilters['id_md_area'])) {
                $ar = $areas->firstWhere('id_md_area', $currentFilters['id_md_area']);
                $currentFilters['area_label'] = $ar ? $ar->area : $currentFilters['id_md_area'];
            }

            return view('data.jo-contract.index', compact(
                'joContracts',
                'contracts',
                'areas',
                'customers',
                'currentFilters'
            ));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error retrieving JO contracts: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving JO contracts: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $contracts   = Contract::with('customer')->orderBy('no_contract')->get();
        $areas       = Area::orderBy('area')->get();
        $invoices    = Invoice::where('jo_ctg', 'contract')->orderBy('invoice_ctg')->orderBy('invoice_typ')->get();
        $previewNoJo = IdGenerator::generateDocNo('b01_jo_cont', 'no_jo_cont');

        return view('data.jo-contract.create', compact('contracts', 'areas', 'invoices', 'previewNoJo'));
    }

    public function storeHeader(Request $request)
    {
        Log::info('=== Store Header Request START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_cont'  => 'required|exists:a02_md_contract,id_md_cont',
                'id_md_area'  => 'required|exists:a03_md_area,id_md_area',
                'tgl_jo_cont' => 'required|date',
                'title'       => 'required|string|max:255',
                'note'        => 'nullable|string',
            ], [
                'id_md_cont.required' => 'Contract is required',
                'id_md_cont.exists'   => 'Selected contract does not exist',
                'id_md_area.required' => 'Area is required',
                'id_md_area.exists'   => 'Selected area does not exist',
                'title.required'      => 'Title is required',
            ]);

            if ($validator->fails()) {
                Log::error('Header Validation Failed', ['errors' => $validator->errors()->toArray()]);
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $lastJoContract = JoContract::orderBy('id_jo_cont', 'desc')->first();
            $newJoContId    = $lastJoContract ? $lastJoContract->id_jo_cont + 1 : 1;
            $noJoCont       = IdGenerator::generateDocNo('b01_jo_cont', 'no_jo_cont');

            Log::info('Creating JO Contract Header', ['new_id' => $newJoContId]);

            $joContract = JoContract::create([
                'id_jo_cont'  => $newJoContId,
                'no_jo_cont'  => $noJoCont,
                'tgl_jo_cont' => $request->tgl_jo_cont,
                'id_md_cont'  => $request->id_md_cont,
                'id_md_area'  => $request->id_md_area,
                'title'       => $request->title,
                'note'        => $request->note,
            ]);

            DB::commit();

            Log::info('Header Created Successfully', ['id' => $joContract->id_jo_cont]);

            $joContract->load(['contract.customer', 'area']);

            return response()->json([
                'success'      => true,
                'message'      => 'JO Contract header saved successfully',
                'redirect_url' => route('jo-contract.edit', $joContract->id_jo_cont),
                'data'         => [
                    'id'         => $joContract->id_jo_cont,
                    'id_md_cont' => $joContract->id_md_cont,
                    'id_md_area' => $joContract->id_md_area,
                    'title'      => $joContract->title,
                    'note'       => $joContract->note,
                    'contract'   => $joContract->contract,
                    'area'       => $joContract->area,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Header Failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to save JO Contract header', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateHeader(Request $request, $id)
    {
        Log::info('=== Update Header Request START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_cont'          => 'required|exists:a02_md_contract,id_md_cont',
                'id_md_area'          => 'required|exists:a03_md_area,id_md_area',
                'tgl_jo_cont'         => 'required|date',
                'title'               => 'required|string|max:255',
                'note'                => 'nullable|string',
                'global_tgl_kurs_usd' => 'required|date',
                'global_kurs_usd'     => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $joContract = JoContract::findOrFail($id);
            $joContract->update([
                'tgl_jo_cont' => $request->tgl_jo_cont,
                'id_md_cont'  => $request->id_md_cont,
                'id_md_area'  => $request->id_md_area,
                'title'       => $request->title,
                'note'        => $request->note,
            ]);

            DB::commit();

            Log::info('Header Updated Successfully', ['id' => $joContract->id_jo_cont]);

            $joContract->load(['contract.customer', 'area']);

            return response()->json(['success' => true, 'message' => 'JO Contract header updated successfully', 'data' => $joContract], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Header Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update JO Contract header', 'error' => $e->getMessage()], 500);
        }
    }

    public function storeItem(Request $request)
    {
        Log::info('=== Store Item Request START ===', ['data' => $request->all()]);

        try {
            $rules = [
                'id_jo_cont'    => 'required|exists:b01_jo_cont,id_jo_cont',
                'id_md_invoice' => 'required|exists:a04_md_invoice,id_md_invoice',
                'invoice_ctg'   => 'required|string',
                'pendapatan_idr' => 'nullable|numeric|min:0',
                'pendapatan_usd' => 'nullable|numeric|min:0',
                'hpp_ops'        => 'nullable|numeric|min:0',
                'note'           => 'nullable|string',
            ];

            if ($request->pendapatan_usd && $request->pendapatan_usd > 0) {
                $rules['kurs_usd']     = 'required|numeric|min:0';
                $rules['tgl_kurs_usd'] = 'required|date_format:Y-m-d\TH:i';
            } else {
                $rules['kurs_usd']     = 'nullable|numeric|min:0';
                $rules['tgl_kurs_usd'] = 'nullable|date_format:Y-m-d\TH:i';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                Log::error('Item Validation Failed', ['errors' => $validator->errors()->toArray()]);
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            // ============================================================
            // FIX: Gunakan CAST ke UNSIGNED agar perbandingan numerik
            // bukan string ("9" > "10" secara string comparison)
            // ============================================================
            $maxId = DB::table('b02_jo_cont_item')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(id_jo_cont_item AS UNSIGNED)) as max_id')
                ->value('max_id');

            $newId = ((int) $maxId) + 1;

            Log::info('ID Generation', [
                'max_id_from_db' => $maxId,
                'new_id'         => $newId,
            ]);

            $pendapatanIDR = $request->pendapatan_idr ?? 0;
            $pendapatanUSD = $request->pendapatan_usd ?? 0;
            $kursUSD       = $request->kurs_usd ?? 0;
            $hppOps        = $request->hpp_ops ?? 0;

            $hargajualIDR = $pendapatanIDR > 0
                ? $pendapatanIDR
                : ($pendapatanUSD * $kursUSD);

            Log::info('Calculated Values', [
                'pendapatan_idr' => $pendapatanIDR,
                'pendapatan_usd' => $pendapatanUSD,
                'kurs_usd'       => $kursUSD,
                'hpp_ops'        => $hppOps,
                'hargajual_idr'  => $hargajualIDR,
            ]);

            $idValue = (string) $newId;

            Log::info('Creating Item', ['id_value' => $idValue]);

            $item = JoContractItem::create([
                'id_jo_cont_item' => $idValue,
                'id_jo_cont'      => $request->id_jo_cont,
                'id_md_invoice'   => $request->id_md_invoice,
                'pendapatan_idr'  => $pendapatanIDR,
                'pendapatan_usd'  => $pendapatanUSD,
                'kurs_usd'        => $kursUSD,
                'tgl_kurs_usd'    => $request->tgl_kurs_usd,
                'hpp_ops'         => $hppOps,
                'hargajual_idr'   => $hargajualIDR,
                'note'            => $request->note,
            ]);

            $item->load('invoice');

            DB::commit();

            Log::info('Item Created Successfully', ['id_jo_cont_item' => $item->id_jo_cont_item]);

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data'    => [
                    'id'              => $item->id_jo_cont_item,
                    'id_jo_cont_item' => $item->id_jo_cont_item,
                    'id_jo_cont'      => $item->id_jo_cont,
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

            Log::error('Store Item Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to add item', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateItem(Request $request, $id)
    {
        Log::info('=== Update Item Request START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $rules = [
                'id_jo_cont'     => 'required|exists:b01_jo_cont,id_jo_cont',
                'id_md_invoice'  => 'required|exists:a04_md_invoice,id_md_invoice',
                'invoice_ctg'    => 'required|string',
                'pendapatan_idr' => 'nullable|numeric|min:0',
                'pendapatan_usd' => 'nullable|numeric|min:0',
                'hpp_ops'        => 'nullable|numeric|min:0',
                'note'           => 'nullable|string|max:1000',
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

            $item          = JoContractItem::findOrFail($id);
            $pendapatanIDR = $request->pendapatan_idr ?? 0;
            $pendapatanUSD = $request->pendapatan_usd ?? 0;
            $kursUSD       = $request->kurs_usd ?? 0;
            $hppOps        = $request->hpp_ops ?? 0;
            $hargajualIDR  = $pendapatanIDR > 0 ? $pendapatanIDR : ($pendapatanUSD * $kursUSD);

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

            $item->refresh();
            $item->load('invoice');

            DB::commit();

            Log::info('Item Updated Successfully', ['id' => $item->id_jo_cont_item]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data'    => [
                    'id'              => $item->id_jo_cont_item,
                    'id_jo_cont_item' => $item->id_jo_cont_item,
                    'id_jo_cont'      => $item->id_jo_cont,
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
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Item Failed', ['id' => $id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to update item', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroyItem($id)
    {
        Log::info('=== Delete Item Request START ===', ['id' => $id]);

        try {
            DB::beginTransaction();
            $item = JoContractItem::findOrFail($id);
            $item->delete();
            DB::commit();

            Log::info('Item Deleted Successfully', ['id' => $id]);

            return response()->json(['success' => true, 'message' => 'Item deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete item', 'error' => $e->getMessage()], 500);
        }
    }

    public function getItems($joContractId)
    {
        Log::info('=== Get Items Request START ===', ['jo_contract_id' => $joContractId]);

        try {
            $items = JoContractItem::where('id_jo_cont', $joContractId)
                ->with('invoice')
                ->orderBy('created_at')
                ->get();

            Log::info('Items Retrieved Successfully', ['count' => $items->count()]);

            $formattedItems = $items->map(function ($item) {
                return [
                    'id'             => $item->id_jo_cont_item,
                    'id_jo_cont'     => $item->id_jo_cont,
                    'id_md_invoice'  => $item->id_md_invoice,
                    'invoice_typ'    => $item->invoice ? $item->invoice->invoice_typ : 'Unknown',
                    'invoice_ctg'    => $item->invoice ? $item->invoice->invoice_ctg : 'Unknown',
                    'pendapatan_idr' => $item->pendapatan_idr,
                    'pendapatan_usd' => $item->pendapatan_usd,
                    'kurs_usd'       => $item->kurs_usd,
                    'tgl_kurs_usd'   => $item->tgl_kurs_usd,
                    'hpp_ops'        => $item->hpp_ops,
                    'hargajual_idr'  => $item->hargajual_idr,
                ];
            });

            return response()->json(['success' => true, 'data' => $formattedItems], 200);
        } catch (\Exception $e) {
            Log::error('Get Items Failed', ['jo_contract_id' => $joContractId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch items', 'error' => $e->getMessage()], 500);
        }
    }

    public function showItem($id)
    {
        try {
            $item = JoContractItem::with('invoice')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => [
                    'id_jo_cont_item' => $item->id_jo_cont_item,
                    'id_md_invoice'   => $item->id_md_invoice,
                    'invoice_ctg'     => $item->invoice->invoice_ctg,
                    'invoice_typ'     => $item->invoice->invoice_typ,
                    'pendapatan_idr'  => $item->pendapatan_idr,
                    'pendapatan_usd'  => $item->pendapatan_usd,
                    'hpp_ops'         => $item->hpp_ops,
                    'kurs_usd'        => $item->kurs_usd,
                    'tgl_kurs_usd'    => $item->tgl_kurs_usd ? $item->tgl_kurs_usd->format('Y-m-d\TH:i') : null,
                    'hargajual_idr'   => $item->hargajual_idr,
                    'note'            => $item->note,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Show Item Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Item not found: ' . $e->getMessage()], 404);
        }
    }

    public function store(Request $request)
    {
        Log::info('JO Contract Store Request', ['all_data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'id_md_cont'              => 'required|exists:a02_md_contract,id_md_cont',
            'id_md_area'              => 'required|exists:a03_md_area,id_md_area',
            'title'                   => 'required|string|max:255',
            'note'                    => 'nullable|string',
            'global_kurs_usd'         => 'required|numeric|min:0',
            'global_tgl_kurs_usd'     => 'required|date',
            'items'                   => 'required|array|min:1',
            'items.*.id_md_invoice'   => 'required|exists:a04_md_invoice,id_md_invoice',
            'items.*.invoice_ctg'     => 'nullable|string',
            'items.*.pendapatan_idr'  => 'required|numeric|min:0',
            'items.*.pendapatan_usd'  => 'required|numeric|min:0',
            'items.*.hpp_ops'         => 'required|numeric|min:0',
            'items.*.hargajual_idr'   => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $lastJoContract = JoContract::orderBy('id_jo_cont', 'desc')->first();
            $newJoContId    = $lastJoContract ? $lastJoContract->id_jo_cont + 1 : 1;

            $joContract = JoContract::create([
                'id_jo_cont' => $newJoContId,
                'id_md_cont' => $request->id_md_cont,
                'id_md_area' => $request->id_md_area,
                'title'      => $request->title,
                'note'       => $request->note,
            ]);

            $globalKursUsd    = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            foreach ($request->items as $item) {
                $createdItem = JoContractItem::create([
                    'id_jo_cont'     => $newJoContId,
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
                return response()->json(['success' => true, 'message' => 'JO Contract successfully added', 'data' => $joContract->load('items')], 201);
            }

            return redirect()->route('jo-contract.index')->with('success', 'JO Contract successfully added with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JO Contract Store Failed', ['error_message' => $e->getMessage(), 'error_line' => $e->getLine(), 'error_file' => $e->getFile()]);

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error creating JO contract: ' . $e->getMessage()], 500);
            }

            return back()->withInput()->with('error', 'Error creating JO contract: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
        }
    }

    public function show(Request $request, $id)
    {
        Log::info('=== JO Contract Show Request START ===', ['id' => $id, 'url' => $request->fullUrl(), 'method' => $request->method(), 'user_id' => auth()->id() ?? 'guest', 'ip' => $request->ip()]);

        try {
            $joContract = JoContract::with(['contract.customer', 'area', 'items.invoice'])->findOrFail($id);

            $summary = [
                'total_items'         => $joContract->items->count(),
                'total_revenue_idr'   => $joContract->items->sum('pendapatan_idr'),
                'total_revenue_usd'   => $joContract->items->sum('pendapatan_usd'),
                'total_hpp_ops'       => $joContract->items->sum('hpp_ops'),
                'total_selling_price' => $joContract->items->sum('hargajual_idr'),
            ];

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $joContract, 'summary' => $summary]);
            }

            if (!view()->exists('data.jo-contract.show')) {
                return back()->with('error', 'View file not found: data.jo-contract.show');
            }

            return view('data.jo-contract.show', compact('joContract', 'summary'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => 'JO Contract not found'], 404);
            return back()->with('error', 'JO Contract not found');
        } catch (\Exception $e) {
            Log::error('JO Contract Show Failed', ['id' => $id, 'error_message' => $e->getMessage()]);
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => 'Error loading JO Contract: ' . $e->getMessage()], 500);
            return back()->with('error', 'Error loading JO Contract: ' . $e->getMessage());
        } finally {
            Log::info('=== JO Contract Show Request END ===');
        }
    }

    public function edit($id)
    {
        Log::info('=== JO Contract Edit Request START ===', ['id' => $id, 'url' => request()->fullUrl(), 'method' => request()->method(), 'user_id' => auth()->id() ?? 'guest', 'ip' => request()->ip()]);

        try {
            Log::info('Attempting to load JO Contract for editing', ['id' => $id]);

            $joContract = JoContract::with(['items.invoice'])->findOrFail($id);

            Log::info('JO Contract loaded successfully', ['id' => $joContract->id_jo_cont, 'title' => $joContract->title, 'items_count' => $joContract->items->count()]);
            Log::info('Loading related data (contracts, areas, invoices)');

            $contracts = Contract::with('customer')->orderBy('no_contract')->get();
            Log::info('Contracts loaded', ['count' => $contracts->count()]);

            $areas = Area::orderBy('area')->get();
            Log::info('Areas loaded', ['count' => $areas->count()]);

            $invoices = Invoice::where('jo_ctg', 'contract')->orderBy('invoice_ctg')->orderBy('invoice_typ')->get();
            Log::info('Invoices loaded', ['count' => $invoices->count()]);
            Log::info('Attempting to load view: data.jo-contract.edit');

            if (!view()->exists('data.jo-contract.edit')) {
                Log::error('VIEW NOT FOUND: data.jo-contract.edit');
                return back()->with('error', 'View file not found: data.jo-contract.edit');
            }

            Log::info('View exists, rendering...');

            return view('data.jo-contract.edit', compact('joContract', 'contracts', 'areas', 'invoices'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('JO Contract NOT FOUND in database', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'JO Contract not found in database');
        } catch (\Exception $e) {
            Log::error('JO Contract Edit Failed', ['id' => $id, 'error_message' => $e->getMessage(), 'error_line' => $e->getLine(), 'error_file' => $e->getFile()]);
            return back()->with('error', 'Error loading JO Contract: ' . $e->getMessage());
        } finally {
            Log::info('=== JO Contract Edit Request END ===');
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('JO Contract Update Request', ['id' => $id, 'all_data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'id_md_cont'             => 'required|exists:a02_md_contract,id_md_cont',
            'id_md_area'             => 'required|exists:a03_md_area,id_md_area',
            'title'                  => 'required|string|max:255',
            'note'                   => 'nullable|string',
            'global_kurs_usd'        => 'required|numeric|min:0',
            'global_tgl_kurs_usd'    => 'required|date',
            'items'                  => 'required|array|min:1',
            'items.*.id_md_invoice'  => 'required|exists:a04_md_invoice,id_md_invoice',
            'items.*.pendapatan_idr' => 'required|numeric|min:0',
            'items.*.pendapatan_usd' => 'required|numeric|min:0',
            'items.*.hpp_ops'        => 'required|numeric|min:0',
            'items.*.hargajual_idr'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $joContract = JoContract::findOrFail($id);
            $joContract->update([
                'id_md_cont' => $request->id_md_cont,
                'id_md_area' => $request->id_md_area,
                'title'      => $request->title,
                'note'       => $request->note,
            ]);

            foreach ($joContract->items as $oldItem) {
                $oldItem->delete();
            }

            $globalKursUsd    = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;

            foreach ($request->items as $item) {
                JoContractItem::create([
                    'id_jo_cont'     => $id,
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
                return response()->json(['success' => true, 'message' => 'JO Contract successfully updated', 'data' => $joContract->fresh()->load('items')]);
            }

            return redirect()->route('jo-contract.index')->with('success', 'JO Contract successfully updated with ' . count($request->items) . ' item(s)');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JO Contract Update Failed', ['id' => $id, 'error_message' => $e->getMessage(), 'error_line' => $e->getLine(), 'error_file' => $e->getFile()]);

            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => 'Error updating JO contract: ' . $e->getMessage()], 500);
            return back()->withInput()->with('error', 'Error updating JO contract: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
        }
    }

    public function destroy(Request $request, $id)
    {
        Log::info('=== Delete JO Contract Request START ===', ['id' => $id]);

        DB::beginTransaction();
        try {
            $joContract  = JoContract::findOrFail($id);
            $itemsCount  = $joContract->items()->count();

            foreach ($joContract->items as $item) {
                $item->delete();
            }

            $joContract->delete();
            DB::commit();

            Log::info('JO Contract Deleted Successfully', ['id' => $id, 'deleted_items_count' => $itemsCount]);

            if ($request->expectsJson()) return response()->json(['success' => true, 'message' => 'JO Contract and all related items successfully deleted']);
            return redirect()->route('jo-contract.index')->with('success', "JO Contract deleted successfully along with {$itemsCount} item(s)");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => 'JO Contract not found'], 404);
            return back()->with('error', 'JO Contract not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete JO Contract Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) return response()->json(['success' => false, 'message' => 'Error deleting JO contract: ' . $e->getMessage()], 500);
            return back()->with('error', 'Error deleting JO contract: ' . $e->getMessage());
        }
    }

    public function getForSelect(Request $request)
    {
        try {
            $query = JoContract::with(['contract', 'area']);

            if ($request->has('id_md_cont')) $query->where('id_md_cont', $request->id_md_cont);
            if ($request->has('id_md_area')) $query->where('id_md_area', $request->id_md_area);

            $joContracts = $query->orderBy('id_jo_cont', 'desc')->get()->map(function ($joContract) {
                return [
                    'id'       => $joContract->id_jo_cont,
                    'text'     => 'JO-' . $joContract->id_jo_cont . ' - ' . $joContract->title,
                    'contract' => $joContract->contract ? $joContract->contract->no_contract : null,
                    'area'     => $joContract->area ? $joContract->area->area : null,
                ];
            });

            return response()->json(['success' => true, 'data' => $joContracts]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving JO contracts: ' . $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:b01_jo_cont,id_jo_cont',
        ]);

        if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            $joContracts = JoContract::whereIn('id_jo_cont', $request->ids)->get();

            foreach ($joContracts as $joContract) {
                if ($joContract->items()->count() > 0) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Some JO contracts cannot be deleted because they have related items'], 422);
                }
            }

            JoContract::whereIn('id_jo_cont', $request->ids)->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'JO Contracts successfully deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting JO contracts: ' . $e->getMessage()], 500);
        }
    }

    public function saveAllChanges(Request $request, $id)
    {
        Log::info('=== Save All Changes Request START ===', ['id' => $id, 'data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_md_cont'          => 'required|exists:a02_md_contract,id_md_cont',
                'id_md_area'          => 'required|exists:a03_md_area,id_md_area',
                'title'               => 'required|string|max:255',
                'note'                => 'nullable|string',
                'global_tgl_kurs_usd' => 'nullable',
                'global_kurs_usd'     => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);

            DB::beginTransaction();

            $joContract = JoContract::findOrFail($id);
            $joContract->update([
                'id_md_cont' => $request->id_md_cont,
                'id_md_area' => $request->id_md_area,
                'title'      => $request->title,
                'note'       => $request->note,
            ]);

            $globalKursUsd    = $request->global_kurs_usd;
            $globalTglKursUsd = $request->global_tgl_kurs_usd;
            $items            = JoContractItem::where('id_jo_cont', $id)->get();

            foreach ($items as $item) {
                if ($item->pendapatan_usd > 0) {
                    $hargajualIDR = $item->pendapatan_usd * $globalKursUsd;
                    $item->update(['kurs_usd' => $globalKursUsd, 'tgl_kurs_usd' => $globalTglKursUsd, 'hargajual_idr' => $hargajualIDR]);
                }
            }

            DB::commit();

            Log::info('Save All Changes SUCCESS');

            return response()->json(['success' => true, 'message' => 'All changes saved successfully', 'data' => ['jo_contract' => $joContract, 'updated_items_count' => $items->count()]], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save All Changes Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to save all changes', 'error' => $e->getMessage()], 500);
        }
    }

    public function exportPdf($id)
    {
        try {
            $joContract = JoContract::with(['contract.customer', 'area', 'items.invoice'])->findOrFail($id);
            $totalSell  = $joContract->items->sum('hargajual_idr');
            $terbilang  = $this->toTerbilang((int) round($totalSell)) . ' Rupiah';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('data.jo-contract.pdf', compact('joContract', 'terbilang'))
                ->setPaper('a4', 'portrait')
                ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false, 'defaultFont' => 'Arial', 'dpi' => 150]);

            $noJo     = str_replace(['/', '\\'], '-', $joContract->no_jo_cont ?? $id);
            $filename = 'JO-Contract-' . $noJo . '.pdf';
            return $pdf->stream($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->expectsJson()) return response()->json(['success' => false, 'message' => 'JO Contract not found'], 404);
            return back()->with('error', 'JO Contract not found');
        } catch (\Exception $e) {
            Log::error('JO Contract Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if (request()->expectsJson()) return response()->json(['success' => false, 'message' => 'Failed to export PDF: ' . $e->getMessage()], 500);
            return back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $format      = $request->get('format', 'excel');
        $joContracts = $this->buildExportQuery($request)->get();

        if ($format === 'pdf') {
            $filters = $this->activeFilters($request);

            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('data.jo-contract.export_pdf', compact('joContracts', 'filters'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false, 'defaultFont' => 'Arial', 'dpi' => 150]);
                return $pdf->stream('jo_contract_' . date('Ymd_His') . '.pdf');
            }

            $html = view('data.jo-contract.export_pdf', compact('joContracts', 'filters'))->render();
            return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8', 'Content-Disposition' => 'inline; filename="jo_contract_' . date('Ymd_His') . '.pdf"']);
        }

        return (new \App\Exports\Data\JoContractExport($joContracts))->download();
    }

    private function buildExportQuery(Request $request)
    {
        $query = \App\Models\Data\JoContract::with(['contract.customer', 'area', 'items']);

        if ($request->filled('no_jo'))        $query->where('no_jo_cont', 'like', '%' . $request->no_jo . '%');
        if ($request->filled('no_contract'))  $query->whereHas('contract', fn($q) => $q->where('no_contract', 'like', '%' . $request->no_contract . '%'));
        if ($request->filled('contract_name')) $query->whereHas('contract', fn($q) => $q->where('contract', 'like', '%' . $request->contract_name . '%'));
        if ($request->filled('id_customer'))  $query->whereHas('contract.customer', fn($q) => $q->where('id_customer', $request->id_customer));
        if ($request->filled('id_md_area'))   $query->where('id_md_area', $request->id_md_area);
        if ($request->filled('title'))        $query->where('title', 'like', '%' . $request->title . '%');

        return $query->orderBy('tgl_jo_cont', 'desc')->orderBy('id_jo_cont', 'desc');
    }

    private function activeFilters(Request $request): array
    {
        $filters = [];
        if ($request->filled('no_jo'))        $filters['no_jo']         = $request->no_jo;
        if ($request->filled('no_contract'))  $filters['no_contract']   = $request->no_contract;
        if ($request->filled('contract_name')) $filters['contract_name'] = $request->contract_name;
        if ($request->filled('id_customer')) {
            $customer = \App\Models\Master\Customer::find($request->id_customer);
            $filters['customer_name'] = $customer ? $customer->customer : $request->id_customer;
        }
        if ($request->filled('id_md_area')) {
            $area = \App\Models\Master\Area::find($request->id_md_area);
            $filters['area_name'] = $area ? $area->area : $request->id_md_area;
        }
        if ($request->filled('title')) $filters['title'] = $request->title;
        return $filters;
    }

    public function updateItemKurs(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kurs_usd'      => 'required|numeric|min:0',
                'tgl_kurs_usd'  => 'nullable',
                'hargajual_idr' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

            $item = JoContractItem::findOrFail($id);
            $item->update(['kurs_usd' => $request->kurs_usd, 'tgl_kurs_usd' => $request->tgl_kurs_usd ?? null, 'hargajual_idr' => $request->hargajual_idr]);

            return response()->json(['success' => true, 'data' => ['id_jo_cont_item' => $item->id_jo_cont_item, 'kurs_usd' => (float) $item->kurs_usd, 'hargajual_idr' => (float) $item->hargajual_idr]]);
        } catch (\Exception $e) {
            Log::error('updateItemKurs failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function toTerbilang(int $number): string
    {
        if ($number < 0) return 'minus ' . $this->toTerbilang(abs($number));
        $words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        if ($number === 0)  return 'Nol';
        if ($number < 12)   return $words[$number];
        if ($number < 20)   return $this->toTerbilang($number - 10) . ' Belas';
        if ($number < 100)  return $words[(int)($number / 10)] . ' Puluh' . ($number % 10 ? ' ' . $this->toTerbilang($number % 10) : '');
        if ($number < 200)  return 'Seratus' . ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 1000) return $words[(int)($number / 100)] . ' Ratus' . ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 2000) return 'Seribu' . ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000)     return $this->toTerbilang((int)($number / 1000)) . ' Ribu' . ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000_000) return $this->toTerbilang((int)($number / 1_000_000)) . ' Juta' . ($number % 1_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000) : '');
        if ($number < 1_000_000_000_000) return $this->toTerbilang((int)($number / 1_000_000_000)) . ' Miliar' . ($number % 1_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000) : '');
        return $this->toTerbilang((int)($number / 1_000_000_000_000)) . ' Triliun' . ($number % 1_000_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000_000) : '');
    }

    // syncItemsKurs — jika ada, tambahkan di sini sesuai kebutuhan
    public function syncItemsKurs(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kurs_usd'     => 'required|numeric|min:0',
                'tgl_kurs_usd' => 'nullable',
            ]);

            if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

            DB::beginTransaction();

            $items   = JoContractItem::where('id_jo_cont', $id)->where('pendapatan_usd', '>', 0)->get();
            $updated = 0;

            foreach ($items as $item) {
                $hargajualIDR = $item->pendapatan_usd * $request->kurs_usd;
                $item->update(['kurs_usd' => $request->kurs_usd, 'tgl_kurs_usd' => $request->tgl_kurs_usd ?? null, 'hargajual_idr' => $hargajualIDR]);
                $updated++;
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => "{$updated} item(s) kurs updated", 'updated_count' => $updated]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('syncItemsKurs failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}