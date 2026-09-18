<?php

namespace App\Http\Controllers\Data;

use App\Helpers\IdGenerator;
use App\Http\Controllers\Controller;
use App\Models\Data\LpjTramper;
use App\Models\Data\LpjKasbonTramper;
use App\Models\Data\LpjTramperItem;
use App\Models\Data\JoTramper;
use App\Models\Data\JoTramperItem;
use App\Models\Data\KasbonTramper;
use App\Models\Data\KasbonTramperItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LpjTramperController extends Controller
{
    // ========================================
    // INDEX
    // ========================================

    public function index(Request $request)
    {
        try {
            $query = LpjTramper::with(['joTramper', 'kasbons.kasbonTramper', 'items']);

            if ($request->filled('id_jo_tram'))   $query->where('id_jo_tram', $request->id_jo_tram);
            if ($request->filled('no_lpj_tram'))  $query->where('no_lpj_tram', 'like', '%' . $request->no_lpj_tram . '%');
            if ($request->filled('date_from'))    $query->where('date', '>=', $request->date_from);
            if ($request->filled('date_to'))      $query->where('date', '<=', $request->date_to);
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('no_lpj_tram', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('joTramper', function ($q) use ($search) {
                            $q->where('no_jo_tram', 'like', "%{$search}%")
                                ->orWhere('title', 'like', "%{$search}%");
                        });
                });
            }

            $query->orderBy($request->get('sort_by', 'created_at'), $request->get('sort_order', 'desc'));
            $lpjTrampers = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $lpjTrampers]);
            }

            $joTrampers     = JoTramper::orderBy('no_jo_tram')->get();
            $currentFilters = [
                'id_jo_tram'  => $request->get('id_jo_tram', ''),
                'no_lpj_tram' => $request->get('no_lpj_tram', ''),
                'date_from'   => $request->get('date_from', ''),
                'date_to'     => $request->get('date_to', ''),
            ];

            return view('data.lpj-tramper.index', compact('lpjTrampers', 'joTrampers', 'currentFilters'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving LPJ trampers: ' . $e->getMessage());
        }
    }

    // ========================================
    // CREATE
    // ========================================

    public function create()
    {
        $joTrampers   = JoTramper::with(['customer', 'port'])->orderBy('no_jo_tram')->get();
        $previewNoLpj = IdGenerator::generateLpjNo('d04_lpj_tram', 'no_lpj_tram');

        return view('data.lpj-tramper.create', compact('joTrampers', 'previewNoLpj'));
    }

    // ========================================
    // STORE HEADER
    // ========================================

    public function storeHeader(Request $request)
    {
        Log::info('=== LpjTramper Store Header START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_jo_tram'   => 'required|exists:b03_jo_tram,id_jo_tram',
                'date'         => 'required|date',
                'note'         => 'nullable|string',
                'evidence'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'kasbon_ids'   => 'required|array|min:1',
                'kasbon_ids.*' => 'required|exists:c03_kasbon_tram,id_kasbon_tram',
            ], [
                'kasbon_ids.required' => 'Please select at least 1 Cash Advance.',
                'kasbon_ids.min'      => 'Please select at least 1 Cash Advance.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $idLpjTram    = IdGenerator::generate('D04', 'd04_lpj_tram', 'id_lpj_tram');
            $noLpjTram    = IdGenerator::generateLpjNo('d04_lpj_tram', 'no_lpj_tram');
            $evidencePath = null;

            if ($request->hasFile('evidence')) {
                $evidencePath = $request->file('evidence')->store('lpj/evidence', 'public');
            }

            $amount = KasbonTramperItem::whereHas('kasbonTramper', function ($q) use ($request) {
                $q->whereIn('id_kasbon_tram', $request->kasbon_ids);
            })->sum('nilai_kasbon');

            $lpj = LpjTramper::create([
                'id_lpj_tram' => $idLpjTram,
                'no_lpj_tram' => $noLpjTram,
                'id_jo_tram'  => $request->id_jo_tram,
                'date'        => $request->date,
                'amount'      => $amount,
                'note'        => $request->note,
                'evidence'    => $evidencePath,
            ]);

            foreach ($request->kasbon_ids as $kasbonTramId) {
                LpjKasbonTramper::create([
                    'id_lpj_kasbon_tram' => IdGenerator::generate('D05', 'd05_lpj_kasbon_tram', 'id_lpj_kasbon_tram'),
                    'id_lpj_tram'        => $idLpjTram,
                    'id_kasbon_tram'     => $kasbonTramId,
                ]);
            }

            DB::commit();
            Log::info('LpjTramper Header Created', ['id_lpj_tram' => $idLpjTram]);

            return response()->json([
                'success'      => true,
                'message'      => 'LPJ header saved successfully',
                'redirect_url' => route('lpj-tramper.edit', $lpj->id),
                'data'         => ['id' => $lpj->id, 'id_lpj_tram' => $idLpjTram],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjTramper Store Header Failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save LPJ header: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ========================================
    // EDIT
    // ========================================

    public function edit($id)
    {
        try {
            $lpj = LpjTramper::with([
                'joTramper.customer',
                'joTramper.port',
                'kasbons.kasbonTramper.items.joTramperItem.invoice',
                'items',
            ])->findOrFail($id);

            $joTrampers = JoTramper::with(['customer', 'port'])->orderBy('no_jo_tram')->get();
            $invoices   = \App\Models\Master\Invoice::where('jo_ctg', 'tramper')
                ->orderBy('invoice_ctg')->orderBy('invoice_typ')->get();

            $coaList = \App\Models\Master\ChartOfAccount::orderBy('no_account')->get();

            $joKurs = JoTramperItem::where('id_jo_tram', $lpj->id_jo_tram)
                ->where('kurs_usd', '>', 0)
                ->orderBy('created_at', 'asc')
                ->select('kurs_usd', 'tgl_kurs_usd')
                ->first();

            $lpjItemsMap = $lpj->items->keyBy('id_kasbon_tram_item');
            $mergedItems = [];

            foreach ($lpj->kasbons as $lpjKasbon) {
                $kasbon = $lpjKasbon->kasbonTramper;
                if (!$kasbon) continue;

                foreach ($kasbon->items as $kasbonItem) {
                    $lpjItem       = $lpjItemsMap->get($kasbonItem->id_kasbon_tram_item);
                    $mergedItems[] = [
                        'id_kasbon_tram_item'    => $kasbonItem->id_kasbon_tram_item,
                        'id_kasbon_tram'         => $kasbonItem->id_kasbon_tram,
                        'id_jo_tram_item'        => $kasbonItem->id_jo_tram_item,
                        'id_kasbon_tram_no'      => $kasbon->id_kasbon_tram,
                        'invoice_typ'            => $kasbonItem->joTramperItem->invoice->invoice_typ ?? '-',
                        'invoice_ctg'            => $kasbonItem->joTramperItem->invoice->invoice_ctg ?? '-',
                        'nilai_hpp_tram_item'    => (float) $kasbonItem->nilai_hpp_tram_item,
                        'nilai_kasbon'           => (float) $kasbonItem->nilai_kasbon,
                        'total_kasbon'           => (float) $kasbonItem->total_kasbon,
                        'id_lpj_tram_item'       => $lpjItem?->id ?? null,
                        'amount_lpj'             => $lpjItem ? (float) $lpjItem->amount_lpj : 0,
                        'has_lpj'                => $lpjItem !== null,
                        'id_md_chart_of_account' => $lpjItem?->id_md_chart_of_account ?? null,
                        'coa_no'                 => $lpjItem?->chartOfAccount->no_account ?? null,
                        'coa_name'               => $lpjItem?->chartOfAccount->account_name ?? null,
                        'origin_lpj_tram'        => $kasbonItem->joTramperItem->origin_lpj_tram ?? null,
                    ];
                }
            }

            return view('data.lpj-tramper.edit', compact(
                'lpj',
                'joTrampers',
                'invoices',
                'mergedItems',
                'joKurs',
                'coaList'
            ));
        } catch (\Exception $e) {
            Log::error('LpjTramper Edit Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Error loading LPJ: ' . $e->getMessage());
        }
    }

    // ========================================
    // UPDATE HEADER
    // ========================================

    public function updateHeader(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_jo_tram' => 'required|exists:b03_jo_tram,id_jo_tram',
                'date'       => 'required|date',
                'note'       => 'nullable|string',
                'evidence'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $lpj          = LpjTramper::findOrFail($id);
            $evidencePath = $lpj->evidence;

            if ($request->hasFile('evidence')) {
                if ($evidencePath) Storage::disk('public')->delete($evidencePath);
                $evidencePath = $request->file('evidence')->store('lpj/evidence', 'public');
            }

            $lpj->update([
                'date'     => $request->date,
                'note'     => $request->note,
                'evidence' => $evidencePath,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'LPJ header updated successfully', 'data' => $lpj]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjTramper Update Header Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update LPJ header: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK SAVE ITEMS
    // ========================================

    public function bulkSaveItems(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'items'                          => 'required|array',
            'items.*.id_kasbon_tram_item'    => 'required',
            'items.*.id_jo_tram_item'        => 'required',
            'items.*.amount_lpj'             => 'required|numeric|min:0',
            'items.*.id_md_chart_of_account' => 'nullable|exists:a13_md_chart_of_account,id_md_chart_of_account',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $lpj = LpjTramper::findOrFail($id);
            DB::beginTransaction();

            foreach ($request->items as $itemData) {
                $existing  = LpjTramperItem::where('id_lpj_tram', $lpj->id_lpj_tram)
                    ->where('id_kasbon_tram_item', $itemData['id_kasbon_tram_item'])
                    ->first();
                $amountLpj = (float) $itemData['amount_lpj'];
                $coa       = $itemData['id_md_chart_of_account'] ?? null;

                if ($existing) {
                    if ($amountLpj > 0) {
                        $existing->update(['amount_lpj' => $amountLpj, 'id_md_chart_of_account' => $coa]);
                    } else {
                        $existing->delete();
                    }
                } elseif ($amountLpj > 0) {
                    LpjTramperItem::create([
                        'id_lpj_tram_item'       => IdGenerator::generate('D06', 'd06_lpj_tram_item', 'id_lpj_tram_item'),
                        'id_lpj_tram'            => $lpj->id_lpj_tram,
                        'id_kasbon_tram_item'    => $itemData['id_kasbon_tram_item'],
                        'id_jo_tram_item'        => $itemData['id_jo_tram_item'],
                        'amount_lpj'             => $amountLpj,
                        'id_md_chart_of_account' => $coa,
                    ]);
                }
            }

            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_tram');
            $amount    = KasbonTramperItem::whereIn('id_kasbon_tram', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();

            $totals = LpjTramperItem::where('id_lpj_tram', $lpj->id_lpj_tram)
                ->selectRaw('SUM(amount_lpj) as total_amount_lpj')->first();

            return response()->json(['success' => true, 'message' => 'Items saved successfully', 'totals' => $totals]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjTramper bulkSaveItems failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to save items: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // STORE NEW ITEM FROM LPJ
    // ========================================

    public function storeNewItem(Request $request, $id)
    {
        Log::info('=== LpjTramper Store New Item START ===', ['data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'id_md_invoice'          => 'required|exists:a04_md_invoice,id_md_invoice',
            'invoice_ctg'            => 'required|string',
            'id_kasbon_tram'         => 'required|exists:c03_kasbon_tram,id_kasbon_tram',
            'pendapatan_idr'         => 'nullable|numeric|min:0',
            'pendapatan_usd'         => 'nullable|numeric|min:0',
            'hpp_ops'                => 'nullable|numeric|min:0',
            'nilai_kasbon'           => 'required|numeric|min:0',
            'amount_lpj'             => 'required|numeric|min:0',
            'id_md_chart_of_account' => 'nullable|exists:a13_md_chart_of_account,id_md_chart_of_account',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $lpj = LpjTramper::findOrFail($id);
            DB::beginTransaction();

            $pendapatanIDR = (float) ($request->pendapatan_idr ?? 0);
            $pendapatanUSD = (float) ($request->pendapatan_usd ?? 0);
            $hppOps        = (float) ($request->hpp_ops ?? 0);

            $joKursItem = JoTramperItem::where('id_jo_tram', $lpj->id_jo_tram)
                ->where('kurs_usd', '>', 0)
                ->orderBy('created_at', 'asc')
                ->first();

            $kursUSD      = $joKursItem ? (float) $joKursItem->kurs_usd : 0;
            $tglKursUSD   = $joKursItem ? $joKursItem->tgl_kurs_usd : null;
            $hargajualIDR = $pendapatanIDR > 0 ? $pendapatanIDR : ($pendapatanUSD * $kursUSD);

            // 1. b04_jo_tram_item
            $newJoItemId = IdGenerator::generate('B04', 'b04_jo_tram_item', 'id_jo_tram_item');

            $joItem = JoTramperItem::create([
                'id_jo_tram_item' => $newJoItemId,
                'id_jo_tram'      => $lpj->id_jo_tram,
                'id_md_invoice'   => $request->id_md_invoice,
                'pendapatan_idr'  => $pendapatanIDR,
                'pendapatan_usd'  => $pendapatanUSD,
                'kurs_usd'        => $pendapatanUSD > 0 ? $kursUSD : 0,
                'tgl_kurs_usd'    => $pendapatanUSD > 0 ? $tglKursUSD : null,
                'hpp_ops'         => $hppOps,
                'hargajual_idr'   => $hargajualIDR,
                'note'            => null,
                'origin_lpj_tram' => $lpj->id_lpj_tram,
            ]);

            // 2. c04_kasbon_tram_item
            $maxKasbonItemId = DB::table('c04_kasbon_tram_item')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(id_kasbon_tram_item AS UNSIGNED)) as max_id')
                ->value('max_id');
            $newKasbonItemId = (string)(((int) $maxKasbonItemId) + 1);
            $nilaiKasbon     = (float) $request->nilai_kasbon;
            $totalKasbon     = $hppOps - $nilaiKasbon;

            KasbonTramperItem::create([
                'id_kasbon_tram_item' => $newKasbonItemId,
                'id_kasbon_tram'      => $request->id_kasbon_tram,
                'id_jo_tram_item'     => $newJoItemId,
                'nilai_hpp_tram_item' => $hppOps,
                'nilai_kasbon'        => $nilaiKasbon,
                'total_kasbon'        => $totalKasbon,
            ]);

            // 3. d06_lpj_tram_item
            $lpjItem = LpjTramperItem::create([
                'id_lpj_tram_item'       => IdGenerator::generate('D06', 'd06_lpj_tram_item', 'id_lpj_tram_item'),
                'id_lpj_tram'            => $lpj->id_lpj_tram,
                'id_kasbon_tram_item'    => $newKasbonItemId,
                'id_jo_tram_item'        => $newJoItemId,
                'amount_lpj'             => (float) $request->amount_lpj,
                'id_md_chart_of_account' => $request->id_md_chart_of_account ?? null,
            ]);

            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_tram');
            $amount    = KasbonTramperItem::whereIn('id_kasbon_tram', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();
            Log::info('LpjTramper New Item Stored', ['jo_item' => $newJoItemId, 'kasbon_item' => $newKasbonItemId]);

            return response()->json([
                'success' => true,
                'message' => 'New item added successfully',
                'data'    => [
                    'id_lpj_tram_item'       => $lpjItem->id,
                    'id_kasbon_tram_item'     => $newKasbonItemId,
                    'id_jo_tram_item'         => $newJoItemId,
                    'invoice_typ'             => $joItem->invoice->invoice_typ ?? '-',
                    'invoice_ctg'             => $request->invoice_ctg,
                    'nilai_hpp_tram_item'     => $hppOps,
                    'nilai_kasbon'            => $nilaiKasbon,
                    'total_kasbon'            => $totalKasbon,
                    'amount_lpj'              => (float) $request->amount_lpj,
                    'id_kasbon_tram_no'       => $request->id_kasbon_tram,
                    'has_lpj'                 => true,
                    'hargajual_idr'           => $hargajualIDR,
                    'kurs_usd'                => $kursUSD,
                    'id_md_chart_of_account'  => $request->id_md_chart_of_account ?? null,
                    'coa_no'                  => null,
                    'coa_name'                => null,
                    'origin_lpj_tram'        => $lpj->id_lpj_tram,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjTramper Store New Item Failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to add new item: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // GET KURS DARI JO
    // ========================================

    public function getJoKurs(Request $request)
    {
        try {
            $idJoTram = $request->get('id_jo_tram');
            if (!$idJoTram) {
                return response()->json(['success' => false, 'message' => 'id_jo_tram is required'], 422);
            }

            $kursItem = JoTramperItem::where('id_jo_tram', $idJoTram)
                ->where('kurs_usd', '>', 0)
                ->orderBy('created_at', 'asc')
                ->select('kurs_usd', 'tgl_kurs_usd')
                ->first();

            if (!$kursItem) {
                return response()->json(['success' => true, 'has_kurs' => false, 'kurs_usd' => 0]);
            }

            return response()->json([
                'success'      => true,
                'has_kurs'     => true,
                'kurs_usd'     => (float) $kursItem->kurs_usd,
                'tgl_kurs_usd' => $kursItem->tgl_kurs_usd?->format('d/m/Y H:i'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // GET KASBONS BY JO
    // ========================================

    public function getKasbonsByJo(Request $request)
    {
        try {
            $idJoTram = $request->get('id_jo_tram');
            if (!$idJoTram) {
                return response()->json(['success' => false, 'message' => 'id_jo_tram is required'], 422);
            }

            $kasbons = KasbonTramper::where('id_jo_tram', $idJoTram)
                ->with(['items.joTramperItem.invoice'])
                ->get()
                ->map(fn($k) => [
                    'id_kasbon_tram' => $k->id_kasbon_tram,
                    'tgl_kasbon'     => $k->tgl_kasbon?->format('d/m/Y'),
                    'total_kasbon'   => (float) $k->items->sum('nilai_kasbon'),
                    'items_count'    => $k->items->count(),
                ]);

            $response = [
                'success'      => true,
                'data'         => $kasbons,
                'total_amount' => $kasbons->sum('total_kasbon'),
            ];

            $detailKasbonId = $request->get('detail_kasbon');
            if ($detailKasbonId) {
                $kasbon = KasbonTramper::where('id_kasbon_tram', $detailKasbonId)
                    ->with(['items.joTramperItem.invoice'])
                    ->first();

                if ($kasbon) {
                    $response['kasbon_detail'] = [
                        'id_kasbon_tram' => $kasbon->id_kasbon_tram,
                        'tgl_kasbon'     => $kasbon->tgl_kasbon?->format('d/m/Y'),
                        'items'          => $kasbon->items->map(fn($item) => [
                            'id_kasbon_tram_item' => $item->id_kasbon_tram_item,
                            'invoice_typ'         => $item->joTramperItem?->invoice?->invoice_typ ?? '—',
                            'invoice_ctg'         => $item->joTramperItem?->invoice?->invoice_ctg ?? '—',
                            'nilai_hpp_tram_item' => (float) $item->nilai_hpp_tram_item,
                            'nilai_kasbon'        => (float) $item->nilai_kasbon,
                            'total_kasbon'        => (float) $item->total_kasbon,
                        ])->values(),
                    ];
                }
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // REFRESH KASBONS
    // ========================================

    public function refreshKasbons(Request $request, $id)
    {
        try {
            $lpj = LpjTramper::with('kasbons')->findOrFail($id);

            $allKasbons  = KasbonTramper::where('id_jo_tram', $lpj->id_jo_tram)
                ->with(['items'])->get();
            $existingIds = $lpj->kasbons->pluck('id_kasbon_tram')->toArray();

            $newKasbons = $allKasbons
                ->filter(fn($k) => !in_array($k->id_kasbon_tram, $existingIds))
                ->map(fn($k) => [
                    'id_kasbon_tram' => $k->id_kasbon_tram,
                    'tgl_kasbon'     => $k->tgl_kasbon?->format('d/m/Y'),
                    'total_kasbon'   => (float) $k->items->sum('nilai_kasbon'),
                    'items_count'    => $k->items->count(),
                ])->values();

            return response()->json([
                'success'     => true,
                'new_kasbons' => $newKasbons,
                'found'       => $newKasbons->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // ADD KASBONS TO EXISTING LPJ
    // ========================================

    public function addKasbons(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kasbon_ids'   => 'required|array|min:1',
            'kasbon_ids.*' => 'required|exists:c03_kasbon_tram,id_kasbon_tram',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $lpj = LpjTramper::findOrFail($id);
            DB::beginTransaction();

            $added = 0;
            foreach ($request->kasbon_ids as $kasbonTramId) {
                $exists = LpjKasbonTramper::where('id_lpj_tram', $lpj->id_lpj_tram)
                    ->where('id_kasbon_tram', $kasbonTramId)
                    ->exists();

                if (!$exists) {
                    LpjKasbonTramper::create([
                        'id_lpj_kasbon_tram' => IdGenerator::generate('D05', 'd05_lpj_kasbon_tram', 'id_lpj_kasbon_tram'),
                        'id_lpj_tram'        => $lpj->id_lpj_tram,
                        'id_kasbon_tram'     => $kasbonTramId,
                    ]);
                    $added++;
                }
            }

            $lpj->load('kasbons');
            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_tram');
            $amount    = KasbonTramperItem::whereIn('id_kasbon_tram', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$added} cash advance(s) added to this LPJ.",
                'added'   => $added,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjTramper addKasbons Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // SHOW
    // ========================================

    public function show(Request $request, $id)
    {
        try {
            $lpj = LpjTramper::with([
                'joTramper.customer',
                'joTramper.port',
                'kasbons.kasbonTramper',
                'items.kasbonTramperItem.joTramperItem.invoice',
            ])->findOrFail($id);

            $summary = [
                'total_kasbon'     => $lpj->amount,
                'total_amount_lpj' => $lpj->items->sum('amount_lpj'),
                'total_items'      => $lpj->items->count(),
                'total_kasbons'    => $lpj->kasbons->count(),
            ];

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $lpj, 'summary' => $summary]);
            }

            return view('data.lpj-tramper.show', compact('lpj', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error loading LPJ: ' . $e->getMessage());
        }
    }

    // ========================================
    // DESTROY
    // ========================================

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $lpj      = LpjTramper::findOrFail($id);
            $lpjItems = LpjTramperItem::where('id_lpj_tram', $lpj->id_lpj_tram)->get();

            foreach ($lpjItems as $lpjItem) {
                $kasbonItem = KasbonTramperItem::find($lpjItem->id_kasbon_tram_item);
                if ($kasbonItem) {
                    JoTramperItem::where('id_jo_tram_item', $kasbonItem->id_jo_tram_item)->delete();
                    $kasbonItem->delete();
                }
                $lpjItem->delete();
            }

            LpjKasbonTramper::where('id_lpj_tram', $lpj->id_lpj_tram)->delete();
            if ($lpj->evidence) Storage::disk('public')->delete($lpj->evidence);
            $lpj->delete();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'LPJ Tramper deleted successfully']);
            }
            return redirect()->route('lpj-tramper.index')->with('success', 'LPJ Tramper deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjTramper Destroy Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting LPJ: ' . $e->getMessage());
        }
    }

    // ========================================
    // EXPORT PDF
    // ========================================

    public function exportPdf($id)
    {
        try {
            $lpj = LpjTramper::with([
                'joTramper.customer',
                'joTramper.port',
                'kasbons.kasbonTramper.items.joTramperItem.invoice',
                'items.kasbonTramperItem',
                'items.chartOfAccount',
            ])->findOrFail($id);

            $totalLpj  = $lpj->items->sum('amount_lpj');
            $terbilang = $this->toTerbilang((int) round($totalLpj)) . ' Rupiah';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'data.lpj-tramper.pdf',
                compact('lpj', 'terbilang')
            )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'Arial',
                    'dpi'                  => 150,
                ]);

            $filename = 'LPJ-Tramper-'
                . str_replace(['/', '\\'], '-', $lpj->no_lpj_tram ?? $id)
                . '.pdf';

            return $pdf->stream($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'LPJ Tramper not found'], 404);
            }
            return back()->with('error', 'LPJ Tramper not found');
        } catch (\Exception $e) {
            Log::error('LPJ Tramper Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to export PDF: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    // ========================================
    // TERBILANG
    // ========================================

    private function toTerbilang(int $number): string
    {
        if ($number < 0) return 'minus ' . $this->toTerbilang(abs($number));

        $words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        if ($number === 0)  return 'Nol';
        if ($number < 12)   return $words[$number];
        if ($number < 20)   return $this->toTerbilang($number - 10) . ' Belas';
        if ($number < 100)  return $words[(int) ($number / 10)] . ' Puluh' . ($number % 10 ? ' ' . $this->toTerbilang($number % 10) : '');
        if ($number < 200)  return 'Seratus' . ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 1000) return $words[(int) ($number / 100)] . ' Ratus' . ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 2000) return 'Seribu' . ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000)     return $this->toTerbilang((int) ($number / 1000)) . ' Ribu' . ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000_000) return $this->toTerbilang((int) ($number / 1_000_000)) . ' Juta' . ($number % 1_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000) : '');
        if ($number < 1_000_000_000_000) return $this->toTerbilang((int) ($number / 1_000_000_000)) . ' Miliar' . ($number % 1_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000) : '');

        return $this->toTerbilang((int) ($number / 1_000_000_000_000)) . ' Triliun' . ($number % 1_000_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000_000) : '');
    }
}
