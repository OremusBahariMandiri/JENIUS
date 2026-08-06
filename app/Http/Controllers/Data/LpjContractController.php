<?php

namespace App\Http\Controllers\Data;

use App\Helpers\IdGenerator;
use App\Http\Controllers\Controller;
use App\Models\Data\LpjContract;
use App\Models\Data\LpjKasbon;
use App\Models\Data\LpjContractItem;
use App\Models\Data\JoContract;
use App\Models\Data\JoContractItem;
use App\Models\Data\KasbonContract;
use App\Models\Data\KasbonContractItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LpjContractController extends Controller
{
    // ========================================
    // INDEX
    // ========================================

    public function index(Request $request)
    {
        try {
            $query = LpjContract::with(['joContract', 'kasbons.kasbonContract', 'items']);

            if ($request->filled('id_jo_cont'))  $query->where('id_jo_cont', $request->id_jo_cont);
            if ($request->filled('no_lpj_cont')) $query->where('no_lpj_cont', 'like', '%' . $request->no_lpj_cont . '%');
            if ($request->filled('date_from'))   $query->where('date', '>=', $request->date_from);
            if ($request->filled('date_to'))     $query->where('date', '<=', $request->date_to);
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('no_lpj_cont', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('joContract', function ($q) use ($search) {
                            $q->where('no_jo_cont', 'like', "%{$search}%")
                                ->orWhere('title', 'like', "%{$search}%");
                        });
                });
            }

            $query->orderBy($request->get('sort_by', 'created_at'), $request->get('sort_order', 'desc'));
            $lpjContracts = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $lpjContracts]);
            }

            $joContracts    = JoContract::orderBy('no_jo_cont')->get();
            $currentFilters = [
                'id_jo_cont'  => $request->get('id_jo_cont', ''),
                'no_lpj_cont' => $request->get('no_lpj_cont', ''),
                'date_from'   => $request->get('date_from', ''),
                'date_to'     => $request->get('date_to', ''),
            ];

            return view('data.lpj-contract.index', compact('lpjContracts', 'joContracts', 'currentFilters'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving LPJ contracts: ' . $e->getMessage());
        }
    }

    // ========================================
    // CREATE
    // ========================================

    public function create()
    {
        $joContracts  = JoContract::with(['contract', 'area'])->orderBy('no_jo_cont')->get();
        $previewNoLpj = IdGenerator::generateLpjNo('d01_lpj_cont', 'no_lpj_cont');

        return view('data.lpj-contract.create', compact('joContracts', 'previewNoLpj'));
    }

    // ========================================
    // STORE HEADER
    // kasbon_ids dipilih manual oleh user dari create view
    // ========================================

    public function storeHeader(Request $request)
    {
        Log::info('=== LpjContract Store Header START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'id_jo_cont'   => 'required|exists:b01_jo_cont,id_jo_cont',
                'date'         => 'required|date',
                'note'         => 'nullable|string',
                'evidence'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'kasbon_ids'   => 'required|array|min:1',
                'kasbon_ids.*' => 'required|exists:c01_kasbon_cont,id_kasbon_cont',
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

            $idLpjCont    = IdGenerator::generate('D01', 'd01_lpj_cont', 'id_lpj_cont');
            $noLpjCont    = IdGenerator::generateLpjNo('d01_lpj_cont', 'no_lpj_cont');
            $evidencePath = null;

            if ($request->hasFile('evidence')) {
                $evidencePath = $request->file('evidence')->store('lpj/evidence', 'public');
            }

            // Amount dihitung hanya dari kasbon yang dipilih
            $amount = KasbonContractItem::whereHas('kasbonContract', function ($q) use ($request) {
                $q->whereIn('id_kasbon_cont', $request->kasbon_ids);
            })->sum('nilai_kasbon');

            $lpj = LpjContract::create([
                'id_lpj_cont' => $idLpjCont,
                'no_lpj_cont' => $noLpjCont,
                'id_jo_cont'  => $request->id_jo_cont,
                'date'        => $request->date,
                'amount'      => $amount,
                'note'        => $request->note,
                'evidence'    => $evidencePath,
            ]);

            // Insert hanya kasbon yang dipilih user — tidak semua kasbon JO
            foreach ($request->kasbon_ids as $kasbonContId) {
                LpjKasbon::create([
                    'id_lpj_kasbon'  => IdGenerator::generate('D02', 'd02_lpj_kasbon', 'id_lpj_kasbon'),
                    'id_lpj_cont'    => $idLpjCont,
                    'id_kasbon_cont' => $kasbonContId,
                ]);
            }

            DB::commit();
            Log::info('LpjContract Header Created', ['id_lpj_cont' => $idLpjCont]);

            return response()->json([
                'success'      => true,
                'message'      => 'LPJ header saved successfully',
                'redirect_url' => route('lpj-contract.edit', $lpj->id),
                'data'         => ['id' => $lpj->id, 'id_lpj_cont' => $idLpjCont],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjContract Store Header Failed', ['error' => $e->getMessage()]);
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
            $lpj = LpjContract::with([
                'joContract.contract',
                'joContract.area',
                'kasbons.kasbonContract.items.joContractItem.invoice',
                'items',
            ])->findOrFail($id);

            $joContracts = JoContract::with(['contract', 'area'])->orderBy('no_jo_cont')->get();
            $invoices    = \App\Models\Master\Invoice::where('jo_ctg', 'contract')
                ->orderBy('invoice_ctg')->orderBy('invoice_typ')->get();

            $coaList = \App\Models\Master\ChartOfAccount::orderBy('no_account')->get();

            $joKurs = JoContractItem::where('id_jo_cont', $lpj->id_jo_cont)
                ->where('kurs_usd', '>', 0)
                ->orderBy('created_at', 'asc')
                ->select('kurs_usd', 'tgl_kurs_usd')
                ->first();

            $lpjItemsMap = $lpj->items->keyBy('id_kasbon_cont_item');
            $mergedItems = [];

            foreach ($lpj->kasbons as $lpjKasbon) {
                $kasbon = $lpjKasbon->kasbonContract;
                if (!$kasbon) continue;

                foreach ($kasbon->items as $kasbonItem) {
                    $lpjItem       = $lpjItemsMap->get($kasbonItem->id_kasbon_cont_item);
                    $mergedItems[] = [
                        'id_kasbon_cont_item'    => $kasbonItem->id_kasbon_cont_item,
                        'id_kasbon_cont'         => $kasbonItem->id_kasbon_cont,
                        'id_jo_cont_item'        => $kasbonItem->id_jo_cont_item,
                        'id_kasbon_cont_no'      => $kasbon->id_kasbon_cont,
                        'invoice_typ'            => $kasbonItem->joContractItem->invoice->invoice_typ ?? '-',
                        'invoice_ctg'            => $kasbonItem->joContractItem->invoice->invoice_ctg ?? '-',
                        'nilai_hpp_cont_item'    => (float) $kasbonItem->nilai_hpp_cont_item,
                        'nilai_kasbon'           => (float) $kasbonItem->nilai_kasbon,
                        'total_kasbon'           => (float) $kasbonItem->total_kasbon,
                        'id_lpj_cont_item'       => $lpjItem?->id ?? null,
                        'amount_lpj'             => $lpjItem ? (float) $lpjItem->amount_lpj : 0,
                        'has_lpj'                => $lpjItem !== null,
                        'id_md_chart_of_account' => $lpjItem?->id_md_chart_of_account ?? null,
                        'coa_no'                 => $lpjItem?->chartOfAccount->no_account ?? null,
                        'coa_name'               => $lpjItem?->chartOfAccount->account_name ?? null,
                        'origin_lpj_cont'        => $kasbonItem->joContractItem->origin_lpj_cont ?? null,
                    ];
                }
            }

            return view('data.lpj-contract.edit', compact(
                'lpj', 'joContracts', 'invoices', 'mergedItems', 'joKurs', 'coaList'
            ));
        } catch (\Exception $e) {
            Log::error('LpjContract Edit Failed', ['id' => $id, 'error' => $e->getMessage()]);
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
                'id_jo_cont' => 'required|exists:b01_jo_cont,id_jo_cont',
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

            $lpj          = LpjContract::findOrFail($id);
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
            Log::error('LpjContract Update Header Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update LPJ header: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK SAVE ITEMS
    // ========================================

    public function bulkSaveItems(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'items'                            => 'required|array',
            'items.*.id_kasbon_cont_item'      => 'required',
            'items.*.id_jo_cont_item'          => 'required',
            'items.*.amount_lpj'               => 'required|numeric|min:0',
            'items.*.id_md_chart_of_account'   => 'nullable|exists:a13_md_chart_of_account,id_md_chart_of_account',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $lpj = LpjContract::findOrFail($id);
            DB::beginTransaction();

            foreach ($request->items as $itemData) {
                $existing  = LpjContractItem::where('id_lpj_cont', $lpj->id_lpj_cont)
                    ->where('id_kasbon_cont_item', $itemData['id_kasbon_cont_item'])
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
                    LpjContractItem::create([
                        'id_lpj_cont_item'       => IdGenerator::generate('D03', 'd03_lpj_cont_item', 'id_lpj_cont_item'),
                        'id_lpj_cont'            => $lpj->id_lpj_cont,
                        'id_kasbon_cont_item'    => $itemData['id_kasbon_cont_item'],
                        'id_jo_cont_item'        => $itemData['id_jo_cont_item'],
                        'amount_lpj'             => $amountLpj,
                        'id_md_chart_of_account' => $coa,
                    ]);
                }
            }

            // Recompute amount dari kasbon yang terkait LPJ ini
            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_cont');
            $amount    = KasbonContractItem::whereIn('id_kasbon_cont', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();

            $totals = LpjContractItem::where('id_lpj_cont', $lpj->id_lpj_cont)
                ->selectRaw('SUM(amount_lpj) as total_amount_lpj')->first();

            return response()->json(['success' => true, 'message' => 'Items saved successfully', 'totals' => $totals]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjContract bulkSaveItems failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to save items: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // STORE NEW ITEM FROM LPJ
    // ========================================

    public function storeNewItem(Request $request, $id)
    {
        Log::info('=== LpjContract Store New Item START ===', ['data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'id_md_invoice'          => 'required|exists:a04_md_invoice,id_md_invoice',
            'invoice_ctg'            => 'required|string',
            'id_kasbon_cont'         => 'required|exists:c01_kasbon_cont,id_kasbon_cont',
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
            $lpj = LpjContract::findOrFail($id);
            DB::beginTransaction();

            $pendapatanIDR = (float) ($request->pendapatan_idr ?? 0);
            $pendapatanUSD = (float) ($request->pendapatan_usd ?? 0);
            $hppOps        = (float) ($request->hpp_ops ?? 0);

            $joKursItem = JoContractItem::where('id_jo_cont', $lpj->id_jo_cont)
                ->where('kurs_usd', '>', 0)
                ->orderBy('created_at', 'asc')
                ->first();

            $kursUSD    = $joKursItem ? (float) $joKursItem->kurs_usd : 0;
            $tglKursUSD = $joKursItem ? $joKursItem->tgl_kurs_usd : null;
            $hargajualIDR = $pendapatanIDR > 0 ? $pendapatanIDR : ($pendapatanUSD * $kursUSD);

            // 1. b02_jo_cont_item
            $maxJoItemId = DB::table('b02_jo_cont_item')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(id_jo_cont_item AS UNSIGNED)) as max_id')
                ->value('max_id');
            $newJoItemId = (string)(((int) $maxJoItemId) + 1);

            $joItem = JoContractItem::create([
                'id_jo_cont_item' => $newJoItemId,
                'id_jo_cont'      => $lpj->id_jo_cont,
                'id_md_invoice'   => $request->id_md_invoice,
                'pendapatan_idr'  => $pendapatanIDR,
                'pendapatan_usd'  => $pendapatanUSD,
                'kurs_usd'        => $pendapatanUSD > 0 ? $kursUSD : 0,
                'tgl_kurs_usd'    => $pendapatanUSD > 0 ? $tglKursUSD : null,
                'hpp_ops'         => $hppOps,
                'hargajual_idr'   => $hargajualIDR,
                'note'            => null,
                'origin_lpj_cont' => $lpj->id_lpj_cont,
            ]);

            // 2. c02_kasbon_cont_item
            $maxKasbonItemId = DB::table('c02_kasbon_cont_item')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(id_kasbon_cont_item AS UNSIGNED)) as max_id')
                ->value('max_id');
            $newKasbonItemId = (string)(((int) $maxKasbonItemId) + 1);
            $nilaiKasbon     = (float) $request->nilai_kasbon;
            $totalKasbon     = $hppOps - $nilaiKasbon;

            KasbonContractItem::create([
                'id_kasbon_cont_item' => $newKasbonItemId,
                'id_kasbon_cont'      => $request->id_kasbon_cont,
                'id_jo_cont_item'     => $newJoItemId,
                'nilai_hpp_cont_item' => $hppOps,
                'nilai_kasbon'        => $nilaiKasbon,
                'total_kasbon'        => $totalKasbon,
                'origin_lpj_cont'     => $lpj->id_lpj_cont,
            ]);

            // 3. d03_lpj_cont_item
            $lpjItem = LpjContractItem::create([
                'id_lpj_cont_item'       => IdGenerator::generate('D03', 'd03_lpj_cont_item', 'id_lpj_cont_item'),
                'id_lpj_cont'            => $lpj->id_lpj_cont,
                'id_kasbon_cont_item'    => $newKasbonItemId,
                'id_jo_cont_item'        => $newJoItemId,
                'amount_lpj'             => (float) $request->amount_lpj,
                'id_md_chart_of_account' => $request->id_md_chart_of_account ?? null,
            ]);

            // Recompute amount dari kasbon yang terkait LPJ ini
            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_cont');
            $amount    = KasbonContractItem::whereIn('id_kasbon_cont', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();
            Log::info('LpjContract New Item Stored', ['jo_item' => $newJoItemId, 'kasbon_item' => $newKasbonItemId]);

            return response()->json([
                'success' => true,
                'message' => 'New item added successfully',
                'data'    => [
                    'id_lpj_cont_item'       => $lpjItem->id,
                    'id_kasbon_cont_item'     => $newKasbonItemId,
                    'id_jo_cont_item'         => $newJoItemId,
                    'invoice_typ'             => $joItem->invoice->invoice_typ ?? '-',
                    'invoice_ctg'             => $request->invoice_ctg,
                    'nilai_hpp_cont_item'     => $hppOps,
                    'nilai_kasbon'            => $nilaiKasbon,
                    'total_kasbon'            => $totalKasbon,
                    'amount_lpj'              => (float) $request->amount_lpj,
                    'id_kasbon_cont_no'       => $request->id_kasbon_cont,
                    'has_lpj'                 => true,
                    'hargajual_idr'           => $hargajualIDR,
                    'kurs_usd'                => $kursUSD,
                    'id_md_chart_of_account'  => $request->id_md_chart_of_account ?? null,
                    'coa_no'                  => null,
                    'coa_name'                => null,
                    'origin_lpj_cont'         => $lpj->id_lpj_cont,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjContract Store New Item Failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to add new item: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // GET KURS DARI JO (readonly)
    // ========================================

    public function getJoKurs(Request $request)
    {
        try {
            $idJoCont = $request->get('id_jo_cont');
            if (!$idJoCont) {
                return response()->json(['success' => false, 'message' => 'id_jo_cont is required'], 422);
            }

            $kursItem = JoContractItem::where('id_jo_cont', $idJoCont)
                ->where('kurs_usd', '>', 0)
                ->orderBy('created_at', 'asc')
                ->select('kurs_usd', 'tgl_kurs_usd')
                ->first();

            if (!$kursItem) {
                return response()->json([
                    'success'  => true,
                    'has_kurs' => false,
                    'kurs_usd' => 0,
                ]);
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
    // Support parameter detail_kasbon untuk info items per kasbon
    // ========================================

    public function getKasbonsByJo(Request $request)
    {
        try {
            $idJoCont = $request->get('id_jo_cont');
            if (!$idJoCont) {
                return response()->json(['success' => false, 'message' => 'id_jo_cont is required'], 422);
            }

            $kasbons = KasbonContract::where('id_jo_cont', $idJoCont)
                ->with(['items.joContractItem.invoice'])
                ->get()
                ->map(fn($k) => [
                    'id_kasbon_cont' => $k->id_kasbon_cont,
                    'tgl_kasbon'     => $k->tgl_kasbon?->format('d/m/Y'),
                    'total_kasbon'   => (float) $k->items->sum('nilai_kasbon'),
                    'items_count'    => $k->items->count(),
                ]);

            $response = [
                'success'      => true,
                'data'         => $kasbons,
                'total_amount' => $kasbons->sum('total_kasbon'),
            ];

            // Detail items untuk satu kasbon (dipakai modal info di create view)
            $detailKasbonId = $request->get('detail_kasbon');
            if ($detailKasbonId) {
                $kasbon = KasbonContract::where('id_kasbon_cont', $detailKasbonId)
                    ->with(['items.joContractItem.invoice'])
                    ->first();

                if ($kasbon) {
                    $response['kasbon_detail'] = [
                        'id_kasbon_cont' => $kasbon->id_kasbon_cont,
                        'tgl_kasbon'     => $kasbon->tgl_kasbon?->format('d/m/Y'),
                        'items'          => $kasbon->items->map(fn($item) => [
                            'id_kasbon_cont_item' => $item->id_kasbon_cont_item,
                            'invoice_typ'         => $item->joContractItem?->invoice?->invoice_typ ?? '—',
                            'invoice_ctg'         => $item->joContractItem?->invoice?->invoice_ctg ?? '—',
                            'nilai_hpp_cont_item' => (float) $item->nilai_hpp_cont_item,
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
    // REFRESH KASBONS (cek kasbon baru dari JO yang belum masuk LPJ)
    // ========================================

    public function refreshKasbons(Request $request, $id)
    {
        try {
            $lpj = LpjContract::with('kasbons')->findOrFail($id);

            $allKasbons      = KasbonContract::where('id_jo_cont', $lpj->id_jo_cont)
                ->with(['items'])->get();
            $existingIds     = $lpj->kasbons->pluck('id_kasbon_cont')->toArray();

            $newKasbons = $allKasbons
                ->filter(fn($k) => !in_array($k->id_kasbon_cont, $existingIds))
                ->map(fn($k) => [
                    'id_kasbon_cont' => $k->id_kasbon_cont,
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
            'kasbon_ids.*' => 'required|exists:c01_kasbon_cont,id_kasbon_cont',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $lpj = LpjContract::findOrFail($id);
            DB::beginTransaction();

            $added = 0;
            foreach ($request->kasbon_ids as $kasbonContId) {
                $exists = LpjKasbon::where('id_lpj_cont', $lpj->id_lpj_cont)
                    ->where('id_kasbon_cont', $kasbonContId)
                    ->exists();

                if (!$exists) {
                    LpjKasbon::create([
                        'id_lpj_kasbon'  => IdGenerator::generate('D02', 'd02_lpj_kasbon', 'id_lpj_kasbon'),
                        'id_lpj_cont'    => $lpj->id_lpj_cont,
                        'id_kasbon_cont' => $kasbonContId,
                    ]);
                    $added++;
                }
            }

            // Recompute amount
            $lpj->load('kasbons');
            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_cont');
            $amount    = KasbonContractItem::whereIn('id_kasbon_cont', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$added} cash advance(s) added to this LPJ.",
                'added'   => $added,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjContract addKasbons Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // SHOW
    // ========================================

    public function show(Request $request, $id)
    {
        try {
            $lpj = LpjContract::with([
                'joContract.contract.customer',
                'joContract.area',
                'kasbons.kasbonContract',
                'items.kasbonContractItem.joContractItem.invoice',
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

            return view('data.lpj-contract.show', compact('lpj', 'summary'));
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
            $lpj      = LpjContract::findOrFail($id);
            $lpjItems = LpjContractItem::where('id_lpj_cont', $lpj->id_lpj_cont)->get();

            foreach ($lpjItems as $lpjItem) {
                $kasbonItem = KasbonContractItem::find($lpjItem->id_kasbon_cont_item);
                if ($kasbonItem && $kasbonItem->origin_lpj_cont === $lpj->id_lpj_cont) {
                    JoContractItem::where('id_jo_cont_item', $kasbonItem->id_jo_cont_item)
                        ->where('origin_lpj_cont', $lpj->id_lpj_cont)->delete();
                    $kasbonItem->delete();
                }
                $lpjItem->delete();
            }

            LpjKasbon::where('id_lpj_cont', $lpj->id_lpj_cont)->delete();
            if ($lpj->evidence) Storage::disk('public')->delete($lpj->evidence);
            $lpj->delete();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'LPJ Contract deleted successfully']);
            }
            return redirect()->route('lpj-contract.index')->with('success', 'LPJ Contract deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjContract Destroy Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting LPJ: ' . $e->getMessage());
        }
    }
}