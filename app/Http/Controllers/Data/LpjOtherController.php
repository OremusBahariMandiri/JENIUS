<?php

namespace App\Http\Controllers\Data;

use App\Helpers\IdGenerator;
use App\Http\Controllers\Controller;
use App\Models\Data\LpjOther;
use App\Models\Data\LpjKasbonOther;
use App\Models\Data\LpjOtherItem;
use App\Models\Data\JoOther;
use App\Models\Data\JoOtherItem;
use App\Models\Data\KasbonOther;
use App\Models\Data\KasbonOtherItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LpjOtherController extends Controller
{
    // ========================================
    // INDEX
    // ========================================

    public function index(Request $request)
    {
        try {
            $query = LpjOther::with(['joOther', 'kasbons.kasbonOther', 'items']);

            if ($request->filled('id_jo_other'))   $query->where('id_jo_other', $request->id_jo_other);
            if ($request->filled('no_lpj_other'))  $query->where('no_lpj_other', 'like', '%' . $request->no_lpj_other . '%');
            if ($request->filled('date_from'))     $query->where('date', '>=', $request->date_from);
            if ($request->filled('date_to'))       $query->where('date', '<=', $request->date_to);
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('no_lpj_other', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('joOther', function ($q) use ($search) {
                            $q->where('no_jo_other', 'like', "%{$search}%")
                                ->orWhere('title', 'like', "%{$search}%");
                        });
                });
            }

            $query->orderBy($request->get('sort_by', 'created_at'), $request->get('sort_order', 'desc'));
            $lpjOthers = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $lpjOthers]);
            }

            $joOthers       = JoOther::orderBy('no_jo_other')->get();
            $currentFilters = [
                'id_jo_other'  => $request->get('id_jo_other', ''),
                'no_lpj_other' => $request->get('no_lpj_other', ''),
                'date_from'    => $request->get('date_from', ''),
                'date_to'      => $request->get('date_to', ''),
            ];

            return view('data.lpj-other.index', compact('lpjOthers', 'joOthers', 'currentFilters'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving LPJ others: ' . $e->getMessage());
        }
    }

    // ========================================
    // CREATE
    // ========================================

    public function create()
    {
        $joOthers     = JoOther::orderBy('no_jo_other')->get();
        $previewNoLpj = IdGenerator::generateLpjNo('d07_lpj_other', 'no_lpj_other');

        return view('data.lpj-other.create', compact('joOthers', 'previewNoLpj'));
    }

    // ========================================
    // STORE HEADER
    // ========================================

    public function storeHeader(Request $request)
    {
        Log::info('=== LpjOther Store Header START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_jo_other'  => 'required|exists:b05_jo_other,id_jo_other',
                'date'         => 'required|date',
                'note'         => 'nullable|string',
                'evidence'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'kasbon_ids'   => 'required|array|min:1',
                'kasbon_ids.*' => 'required|exists:c05_kasbon_other,id_kasbon_other',
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

            $idLpjOther   = IdGenerator::generate('D07', 'd07_lpj_other', 'id_lpj_other');
            $noLpjOther   = IdGenerator::generateLpjNo('d07_lpj_other', 'no_lpj_other');
            $evidencePath = null;

            if ($request->hasFile('evidence')) {
                $evidencePath = $request->file('evidence')->store('lpj/evidence', 'public');
            }

            $amount = KasbonOtherItem::whereHas('kasbonOther', function ($q) use ($request) {
                $q->whereIn('id_kasbon_other', $request->kasbon_ids);
            })->sum('nilai_kasbon');

            $lpj = LpjOther::create([
                'id_lpj_other' => $idLpjOther,
                'no_lpj_other' => $noLpjOther,
                'id_jo_other'  => $request->id_jo_other,
                'date'         => $request->date,
                'amount'       => $amount,
                'note'         => $request->note,
                'evidence'     => $evidencePath,
            ]);

            foreach ($request->kasbon_ids as $kasbonOtherId) {
                LpjKasbonOther::create([
                    'id_lpj_kasbon_other' => IdGenerator::generate('D08', 'd08_lpj_kasbon_other', 'id_lpj_kasbon_other'),
                    'id_lpj_other'        => $idLpjOther,
                    'id_kasbon_other'     => $kasbonOtherId,
                ]);
            }

            DB::commit();
            Log::info('LpjOther Header Created', ['id_lpj_other' => $idLpjOther]);

            return response()->json([
                'success'      => true,
                'message'      => 'LPJ header saved successfully',
                'redirect_url' => route('lpj-other.edit', $lpj->id),
                'data'         => ['id' => $lpj->id, 'id_lpj_other' => $idLpjOther],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjOther Store Header Failed', ['error' => $e->getMessage()]);
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
            $lpj = LpjOther::with([
                'joOther',
                'kasbons.kasbonOther.items.joOtherItem.invoice',
                'items',
            ])->findOrFail($id);

            $joOthers = JoOther::orderBy('no_jo_other')->get();
            $invoices = \App\Models\Master\Invoice::where('jo_ctg', 'other')
                ->orderBy('invoice_ctg')->orderBy('invoice_typ')->get();

            $coaList = \App\Models\Master\ChartOfAccount::orderBy('no_account')->get();

            $joKurs = JoOtherItem::where('id_jo_other', $lpj->id_jo_other)
                ->where('kurs_usd', '>', 0)
                ->orderBy('created_at', 'asc')
                ->select('kurs_usd', 'tgl_kurs_usd')
                ->first();

            $lpjItemsMap = $lpj->items->keyBy('id_kasbon_other_item');
            $mergedItems = [];

            foreach ($lpj->kasbons as $lpjKasbon) {
                $kasbon = $lpjKasbon->kasbonOther;
                if (!$kasbon) continue;

                foreach ($kasbon->items as $kasbonItem) {
                    $lpjItem       = $lpjItemsMap->get($kasbonItem->id_kasbon_other_item);
                    $mergedItems[] = [
                        'id_kasbon_other_item'   => $kasbonItem->id_kasbon_other_item,
                        'id_kasbon_other'        => $kasbonItem->id_kasbon_other,
                        'id_jo_other_item'       => $kasbonItem->id_jo_other_item,
                        'id_kasbon_other_no'     => $kasbon->id_kasbon_other,
                        'invoice_typ'            => $kasbonItem->joOtherItem->invoice->invoice_typ ?? '-',
                        'invoice_ctg'            => $kasbonItem->joOtherItem->invoice->invoice_ctg ?? '-',
                        'nilai_hpp_other_item'   => (float) $kasbonItem->nilai_hpp_other_item,
                        'nilai_kasbon'           => (float) $kasbonItem->nilai_kasbon,
                        'total_kasbon'           => (float) $kasbonItem->total_kasbon,
                        'id_lpj_other_item'      => $lpjItem?->id ?? null,
                        'amount_lpj'             => $lpjItem ? (float) $lpjItem->amount_lpj : 0,
                        'has_lpj'                => $lpjItem !== null,
                        'id_md_chart_of_account' => $lpjItem?->id_md_chart_of_account ?? null,
                        'coa_no'                 => $lpjItem?->chartOfAccount->no_account ?? null,
                        'coa_name'               => $lpjItem?->chartOfAccount->account_name ?? null,
                        'origin_lpj_other' => $kasbonItem->origin_lpj_other ?? null,
                    ];
                }
            }

            return view('data.lpj-other.edit', compact(
                'lpj',
                'joOthers',
                'invoices',
                'mergedItems',
                'joKurs',
                'coaList'
            ));
        } catch (\Exception $e) {
            Log::error('LpjOther Edit Failed', ['id' => $id, 'error' => $e->getMessage()]);
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
                'id_jo_other' => 'required|exists:b05_jo_other,id_jo_other',
                'date'        => 'required|date',
                'note'        => 'nullable|string',
                'evidence'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $lpj          = LpjOther::findOrFail($id);
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
            Log::error('LpjOther Update Header Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update LPJ header: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK SAVE ITEMS
    // ========================================

    public function bulkSaveItems(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'items'                           => 'required|array',
            'items.*.id_kasbon_other_item'    => 'required',
            'items.*.id_jo_other_item'        => 'required',
            'items.*.amount_lpj'              => 'required|numeric|min:0',
            'items.*.id_md_chart_of_account'  => 'nullable|exists:a13_md_chart_of_account,id_md_chart_of_account',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $lpj = LpjOther::findOrFail($id);
            DB::beginTransaction();

            foreach ($request->items as $itemData) {
                $existing  = LpjOtherItem::where('id_lpj_other', $lpj->id_lpj_other)
                    ->where('id_kasbon_other_item', $itemData['id_kasbon_other_item'])
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
                    LpjOtherItem::create([
                        'id_lpj_other_item'      => IdGenerator::generate('D09', 'd09_lpj_other_item', 'id_lpj_other_item'),
                        'id_lpj_other'           => $lpj->id_lpj_other,
                        'id_kasbon_other_item'   => $itemData['id_kasbon_other_item'],
                        'id_jo_other_item'       => $itemData['id_jo_other_item'],
                        'amount_lpj'             => $amountLpj,
                        'id_md_chart_of_account' => $coa,
                    ]);
                }
            }

            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_other');
            $amount    = KasbonOtherItem::whereIn('id_kasbon_other', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();

            $totals = LpjOtherItem::where('id_lpj_other', $lpj->id_lpj_other)
                ->selectRaw('SUM(amount_lpj) as total_amount_lpj')->first();

            return response()->json(['success' => true, 'message' => 'Items saved successfully', 'totals' => $totals]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjOther bulkSaveItems failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to save items: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // STORE NEW ITEM FROM LPJ
    // ========================================

    public function storeNewItem(Request $request, $id)
    {
        Log::info('=== LpjOther Store New Item START ===', ['data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'id_md_invoice'          => 'required|exists:a04_md_invoice,id_md_invoice',
            'invoice_ctg'            => 'required|string',
            'id_kasbon_other'        => 'required|exists:c05_kasbon_other,id_kasbon_other',
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
            $lpj = LpjOther::findOrFail($id);
            DB::beginTransaction();

            $pendapatanIDR = (float) ($request->pendapatan_idr ?? 0);
            $pendapatanUSD = (float) ($request->pendapatan_usd ?? 0);
            $hppOps        = (float) ($request->hpp_ops ?? 0);

            $joKursItem = JoOtherItem::where('id_jo_other', $lpj->id_jo_other)
                ->where('kurs_usd', '>', 0)
                ->orderBy('created_at', 'asc')
                ->first();

            $kursUSD      = $joKursItem ? (float) $joKursItem->kurs_usd : 0;
            $tglKursUSD   = $joKursItem ? $joKursItem->tgl_kurs_usd : null;
            $hargajualIDR = $pendapatanIDR > 0 ? $pendapatanIDR : ($pendapatanUSD * $kursUSD);

            // 1. b06_jo_other_item
            $newJoItemId = IdGenerator::generate('B06', 'b06_jo_other_item', 'id_jo_other_item');

            $joItem = JoOtherItem::create([
                'id_jo_other_item' => $newJoItemId,
                'id_jo_other'      => $lpj->id_jo_other,
                'id_md_invoice'    => $request->id_md_invoice,
                'pendapatan_idr'   => $pendapatanIDR,
                'pendapatan_usd'   => $pendapatanUSD,
                'kurs_usd'         => $pendapatanUSD > 0 ? $kursUSD : 0,
                'tgl_kurs_usd'     => $pendapatanUSD > 0 ? $tglKursUSD : null,
                'hpp_ops'          => $hppOps,
                'hargajual_idr'    => $hargajualIDR,
                'note'             => null,
                'origin_lpj_other' => $lpj->id_lpj_other,
            ]);

            // 2. c06_kasbon_other_item
            $maxKasbonItemId = DB::table('c06_kasbon_other_item')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(id_kasbon_other_item AS UNSIGNED)) as max_id')
                ->value('max_id');
            $newKasbonItemId = (string)(((int) $maxKasbonItemId) + 1);
            $nilaiKasbon     = (float) $request->nilai_kasbon;
            $totalKasbon     = $hppOps - $nilaiKasbon;

            KasbonOtherItem::create([
                'id_kasbon_other_item' => $newKasbonItemId,
                'id_kasbon_other'      => $request->id_kasbon_other,
                'id_jo_other_item'     => $newJoItemId,
                'nilai_hpp_other_item' => $hppOps,
                'nilai_kasbon'         => $nilaiKasbon,
                'total_kasbon'         => $totalKasbon,
                'origin_lpj_other'     => $lpj->id_lpj_other,
            ]);

            // 3. d09_lpj_other_item
            $lpjItem = LpjOtherItem::create([
                'id_lpj_other_item'      => IdGenerator::generate('D09', 'd09_lpj_other_item', 'id_lpj_other_item'),
                'id_lpj_other'           => $lpj->id_lpj_other,
                'id_kasbon_other_item'   => $newKasbonItemId,
                'id_jo_other_item'       => $newJoItemId,
                'amount_lpj'             => (float) $request->amount_lpj,
                'id_md_chart_of_account' => $request->id_md_chart_of_account ?? null,
            ]);

            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_other');
            $amount    = KasbonOtherItem::whereIn('id_kasbon_other', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();
            Log::info('LpjOther New Item Stored', ['jo_item' => $newJoItemId, 'kasbon_item' => $newKasbonItemId]);

            return response()->json([
                'success' => true,
                'message' => 'New item added successfully',
                'data'    => [
                    'id_lpj_other_item'      => $lpjItem->id,
                    'id_kasbon_other_item'   => $newKasbonItemId,
                    'id_kasbon_other'        => $request->id_kasbon_other,
                    'id_jo_other_item'       => $newJoItemId,
                    'id_kasbon_other_no'     => $request->id_kasbon_other,
                    'invoice_typ'            => $joItem->invoice->invoice_typ ?? '-',
                    'invoice_ctg'            => $request->invoice_ctg,
                    'nilai_hpp_other_item'   => $hppOps,
                    'nilai_kasbon'           => $nilaiKasbon,
                    'total_kasbon'           => $totalKasbon,
                    'amount_lpj'             => (float) $request->amount_lpj,
                    'has_lpj'                => true,
                    'hargajual_idr'          => $hargajualIDR,
                    'kurs_usd'               => $kursUSD,
                    'id_md_chart_of_account' => $request->id_md_chart_of_account ?? null,
                    'coa_no'                 => null,
                    'coa_name'               => null,
                    'origin_lpj_other'       => $lpj->id_lpj_other,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjOther Store New Item Failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to add new item: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // GET KURS DARI JO
    // ========================================

    public function getJoKurs(Request $request)
    {
        try {
            $idJoOther = $request->get('id_jo_other');
            if (!$idJoOther) {
                return response()->json(['success' => false, 'message' => 'id_jo_other is required'], 422);
            }

            $kursItem = JoOtherItem::where('id_jo_other', $idJoOther)
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
            $idJoOther = $request->get('id_jo_other');
            if (!$idJoOther) {
                return response()->json(['success' => false, 'message' => 'id_jo_other is required'], 422);
            }

            $kasbons = KasbonOther::where('id_jo_other', $idJoOther)
                ->with(['items.joOtherItem.invoice'])
                ->get()
                ->map(fn($k) => [
                    'id_kasbon_other' => $k->id_kasbon_other,
                    'tgl_kasbon'      => $k->tgl_kasbon?->format('d/m/Y'),
                    'total_kasbon'    => (float) $k->items->sum('nilai_kasbon'),
                    'items_count'     => $k->items->count(),
                ]);

            $response = [
                'success'      => true,
                'data'         => $kasbons,
                'total_amount' => $kasbons->sum('total_kasbon'),
            ];

            $detailKasbonId = $request->get('detail_kasbon');
            if ($detailKasbonId) {
                $kasbon = KasbonOther::where('id_kasbon_other', $detailKasbonId)
                    ->with(['items.joOtherItem.invoice'])
                    ->first();

                if ($kasbon) {
                    $response['kasbon_detail'] = [
                        'id_kasbon_other' => $kasbon->id_kasbon_other,
                        'tgl_kasbon'      => $kasbon->tgl_kasbon?->format('d/m/Y'),
                        'items'           => $kasbon->items->map(fn($item) => [
                            'id_kasbon_other_item' => $item->id_kasbon_other_item,
                            'invoice_typ'          => $item->joOtherItem?->invoice?->invoice_typ ?? '—',
                            'invoice_ctg'          => $item->joOtherItem?->invoice?->invoice_ctg ?? '—',
                            'nilai_hpp_other_item' => (float) $item->nilai_hpp_other_item,
                            'nilai_kasbon'         => (float) $item->nilai_kasbon,
                            'total_kasbon'         => (float) $item->total_kasbon,
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
            $lpj = LpjOther::with('kasbons')->findOrFail($id);

            $allKasbons  = KasbonOther::where('id_jo_other', $lpj->id_jo_other)
                ->with(['items'])->get();
            $existingIds = $lpj->kasbons->pluck('id_kasbon_other')->toArray();

            $newKasbons = $allKasbons
                ->filter(fn($k) => !in_array($k->id_kasbon_other, $existingIds))
                ->map(fn($k) => [
                    'id_kasbon_other' => $k->id_kasbon_other,
                    'tgl_kasbon'      => $k->tgl_kasbon?->format('d/m/Y'),
                    'total_kasbon'    => (float) $k->items->sum('nilai_kasbon'),
                    'items_count'     => $k->items->count(),
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
            'kasbon_ids.*' => 'required|exists:c05_kasbon_other,id_kasbon_other',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $lpj = LpjOther::findOrFail($id);
            DB::beginTransaction();

            $added = 0;
            foreach ($request->kasbon_ids as $kasbonOtherId) {
                $exists = LpjKasbonOther::where('id_lpj_other', $lpj->id_lpj_other)
                    ->where('id_kasbon_other', $kasbonOtherId)
                    ->exists();

                if (!$exists) {
                    LpjKasbonOther::create([
                        'id_lpj_kasbon_other' => IdGenerator::generate('D08', 'd08_lpj_kasbon_other', 'id_lpj_kasbon_other'),
                        'id_lpj_other'        => $lpj->id_lpj_other,
                        'id_kasbon_other'     => $kasbonOtherId,
                    ]);
                    $added++;
                }
            }

            $lpj->load('kasbons');
            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_other');
            $amount    = KasbonOtherItem::whereIn('id_kasbon_other', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$added} cash advance(s) added to this LPJ.",
                'added'   => $added,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjOther addKasbons Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // SHOW
    // ========================================

    public function show(Request $request, $id)
    {
        try {
            $lpj = LpjOther::with([
                'joOther',
                'kasbons.kasbonOther',
                'items.kasbonOtherItem.joOtherItem.invoice',
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

            return view('data.lpj-other.show', compact('lpj', 'summary'));
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
            $lpj      = LpjOther::findOrFail($id);
            $lpjItems = LpjOtherItem::where('id_lpj_other', $lpj->id_lpj_other)->get();

            foreach ($lpjItems as $lpjItem) {
                $kasbonItem = KasbonOtherItem::find($lpjItem->id_kasbon_other_item);
                if ($kasbonItem) {
                    JoOtherItem::where('id_jo_other_item', $kasbonItem->id_jo_other_item)
                        ->where('origin_lpj_other', $lpj->id_lpj_other)->delete();
                    $kasbonItem->delete();
                }
                $lpjItem->delete();
            }

            LpjKasbonOther::where('id_lpj_other', $lpj->id_lpj_other)->delete();
            if ($lpj->evidence) Storage::disk('public')->delete($lpj->evidence);
            $lpj->delete();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'LPJ Other deleted successfully']);
            }
            return redirect()->route('lpj-other.index')->with('success', 'LPJ Other deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjOther Destroy Failed', ['id' => $id, 'error' => $e->getMessage()]);
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
            $lpj = LpjOther::with([
                'joOther',
                'kasbons.kasbonOther.items.joOtherItem.invoice',
                'items.kasbonOtherItem',
                'items.chartOfAccount',
            ])->findOrFail($id);

            $totalLpj  = $lpj->items->sum('amount_lpj');
            $terbilang = $this->toTerbilang((int) round($totalLpj)) . ' Rupiah';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'data.lpj-other.pdf',
                compact('lpj', 'terbilang')
            )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'Arial',
                    'dpi'                  => 150,
                ]);

            $filename = 'LPJ-Other-'
                . str_replace(['/', '\\'], '-', $lpj->no_lpj_other ?? $id)
                . '.pdf';

            return $pdf->stream($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'LPJ Other not found'], 404);
            }
            return back()->with('error', 'LPJ Other not found');
        } catch (\Exception $e) {
            Log::error('LPJ Other Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
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
