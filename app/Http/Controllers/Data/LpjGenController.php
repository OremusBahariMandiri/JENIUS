<?php

namespace App\Http\Controllers\Data;

use App\Helpers\IdGenerator;
use App\Http\Controllers\Controller;
use App\Models\Data\LpjGen;
use App\Models\Data\LpjGenKasbon;
use App\Models\Data\LpjGenItem;
use App\Models\Data\KasbonGen;
use App\Models\Data\KasbonGenItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Exports\Data\LpjGenExport;

class LpjGenController extends Controller
{
    // ========================================
    // INDEX
    // ========================================

    public function index(Request $request)
    {
        try {
            $query = LpjGen::with(['kasbons.kasbonGen', 'items']);

            if ($request->filled('no_lpj_gen')) {
                $query->where('no_lpj_gen', 'like', '%' . $request->no_lpj_gen . '%');
            }
            if ($request->filled('date_from')) {
                $query->where('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('date', '<=', $request->date_to);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('no_lpj_gen', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%");
                });
            }

            $query->orderBy(
                $request->get('sort_by', 'created_at'),
                $request->get('sort_order', 'desc')
            );

            $lpjGens = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $lpjGens]);
            }

            $currentFilters = [
                'no_lpj_gen' => $request->get('no_lpj_gen', ''),
                'date_from'  => $request->get('date_from', ''),
                'date_to'    => $request->get('date_to', ''),
            ];

            return view('data.lpj-gen.index', compact('lpjGens', 'currentFilters'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ========================================
    // CREATE
    // ========================================

    public function create()
    {
        $previewNoLpj = IdGenerator::generateLpjNo('d10_lpj_gen', 'no_lpj_gen');

        return view('data.lpj-gen.create', compact('previewNoLpj'));
    }

    // ========================================
    // STORE HEADER
    // kasbon_ids dipilih manual dari create view
    // ========================================

    public function storeHeader(Request $request)
    {
        Log::info('=== LpjGen Store Header START ===', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                'date'         => 'required|date',
                'note'         => 'nullable|string',
                'evidence'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'kasbon_ids'   => 'required|array|min:1',
                'kasbon_ids.*' => 'required|exists:c07_kasbon_gen,id_kasbon_gen',
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

            $idLpjGen     = IdGenerator::generate('D10', 'd10_lpj_gen', 'id_lpj_gen');
            $noLpjGen     = IdGenerator::generateLpjNo('d10_lpj_gen', 'no_lpj_gen');
            $evidencePath = null;

            if ($request->hasFile('evidence')) {
                $evidencePath = $request->file('evidence')->store('lpj-gen/evidence', 'public');
            }

            // amount = total nilai_kasbon dari kasbon items yang dipilih
            $amount = KasbonGenItem::whereHas('kasbonGen', function ($q) use ($request) {
                $q->whereIn('id_kasbon_gen', $request->kasbon_ids);
            })->sum('nilai_kasbon');

            $lpj = LpjGen::create([
                'id_lpj_gen' => $idLpjGen,
                'no_lpj_gen' => $noLpjGen,
                'date'       => $request->date,
                'amount'     => $amount,
                'note'       => $request->note,
                'evidence'   => $evidencePath,
            ]);

            foreach ($request->kasbon_ids as $kasbonGenId) {
                LpjGenKasbon::create([
                    'id_lpj_gen_kasbon' => IdGenerator::generate('D11', 'd11_lpj_gen_kasbon', 'id_lpj_gen_kasbon'),
                    'id_lpj_gen'        => $idLpjGen,
                    'id_kasbon_gen'     => $kasbonGenId,
                ]);
            }

            DB::commit();
            Log::info('LpjGen Header Created', ['id_lpj_gen' => $idLpjGen]);

            return response()->json([
                'success'      => true,
                'message'      => 'LPJ General header saved successfully',
                'redirect_url' => route('lpj-gen.edit', $lpj->id),
                'data'         => ['id' => $lpj->id, 'id_lpj_gen' => $idLpjGen],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjGen Store Header Failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save LPJ General header: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ========================================
    // EDIT
    // ========================================

    public function edit($id)
    {
        try {
            $lpj = LpjGen::with([
                'kasbons.kasbonGen.items.invoice',
                'items',
            ])->findOrFail($id);

            $coaList = \App\Models\Master\ChartOfAccount::orderBy('no_account')->get();

            // Build mergedItems: per kasbon gen item, merge dengan lpj item jika ada
            $lpjItemsMap = $lpj->items->keyBy('id_kasbon_gen_item');
            $mergedItems = [];

            foreach ($lpj->kasbons as $lpjKasbon) {
                $kasbon = $lpjKasbon->kasbonGen;
                if (!$kasbon) continue;

                foreach ($kasbon->items as $kasbonItem) {
                    $lpjItem       = $lpjItemsMap->get($kasbonItem->id_kasbon_gen_item);
                    $mergedItems[] = [
                        'id_kasbon_gen_item'     => $kasbonItem->id_kasbon_gen_item,
                        'id_kasbon_gen'          => $kasbonItem->id_kasbon_gen,
                        'id_kasbon_gen_no'       => $kasbon->id_kasbon_gen,
                        'invoice_typ'            => $kasbonItem->invoice->invoice_typ ?? '-',
                        'invoice_ctg'            => $kasbonItem->invoice->invoice_ctg ?? '-',
                        'nilai_kasbon'           => (float) $kasbonItem->nilai_kasbon,
                        'id_lpj_gen_item'        => $lpjItem?->id ?? null,
                        'amount_lpj'             => $lpjItem ? (float) $lpjItem->amount_lpj : 0,
                        'has_lpj'                => $lpjItem !== null,
                        'id_md_chart_of_account' => $lpjItem?->id_md_chart_of_account ?? null,
                        'coa_no'                 => $lpjItem?->chartOfAccount->no_account ?? null,
                        'coa_name'               => $lpjItem?->chartOfAccount->account_name ?? null,
                        'origin_lpj_gen'         => $lpjItem?->origin_lpj_gen ?? null,
                    ];
                }
            }

            return view('data.lpj-gen.edit', compact('lpj', 'mergedItems', 'coaList'));
        } catch (\Exception $e) {
            Log::error('LpjGen Edit Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Error loading LPJ General: ' . $e->getMessage());
        }
    }

    // ========================================
    // UPDATE HEADER
    // ========================================

    public function updateHeader(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'date'     => 'required|date',
                'note'     => 'nullable|string',
                'evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $lpj          = LpjGen::findOrFail($id);
            $evidencePath = $lpj->evidence;

            if ($request->hasFile('evidence')) {
                if ($evidencePath) Storage::disk('public')->delete($evidencePath);
                $evidencePath = $request->file('evidence')->store('lpj-gen/evidence', 'public');
            }

            $lpj->update([
                'date'     => $request->date,
                'note'     => $request->note,
                'evidence' => $evidencePath,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'LPJ General header updated', 'data' => $lpj]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // BULK SAVE ITEMS
    // ========================================

    public function bulkSaveItems(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'items'                          => 'required|array',
            'items.*.id_kasbon_gen_item'     => 'required',
            'items.*.amount_lpj'             => 'required|numeric|min:0',
            'items.*.id_md_chart_of_account' => 'nullable|exists:a13_md_chart_of_account,id_md_chart_of_account',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $lpj = LpjGen::findOrFail($id);
            DB::beginTransaction();

            foreach ($request->items as $itemData) {
                $existing  = LpjGenItem::where('id_lpj_gen', $lpj->id_lpj_gen)
                    ->where('id_kasbon_gen_item', $itemData['id_kasbon_gen_item'])
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
                    LpjGenItem::create([
                        'id_lpj_gen_item'        => IdGenerator::generate('D12', 'd12_lpj_gen_item', 'id_lpj_gen_item'),
                        'id_lpj_gen'             => $lpj->id_lpj_gen,
                        'id_kasbon_gen_item'     => $itemData['id_kasbon_gen_item'],
                        'amount_lpj'             => $amountLpj,
                        'id_md_chart_of_account' => $coa,
                    ]);
                }
            }

            // Recompute amount dari kasbon yang terkait
            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_gen');
            $amount    = KasbonGenItem::whereIn('id_kasbon_gen', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();

            $totals = LpjGenItem::where('id_lpj_gen', $lpj->id_lpj_gen)
                ->selectRaw('SUM(amount_lpj) as total_amount_lpj')->first();

            return response()->json(['success' => true, 'message' => 'Items saved', 'totals' => $totals]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // STORE NEW ITEM FROM LPJ
    // Tambah kasbon item baru langsung dari halaman LPJ (tanpa JO)
    // ========================================

    public function storeNewItem(Request $request, $id)
    {
        Log::info('=== LpjGen Store New Item START ===', ['data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'id_kasbon_gen'          => 'required|exists:c07_kasbon_gen,id_kasbon_gen',
            'id_md_invoice'          => 'required|exists:a04_md_invoice,id_md_invoice',
            'invoice_ctg'            => 'required|string',
            'nilai_kasbon'           => 'required|numeric|min:0',
            'amount_lpj'             => 'required|numeric|min:0',
            'id_md_chart_of_account' => 'nullable|exists:a13_md_chart_of_account,id_md_chart_of_account',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $lpj = LpjGen::findOrFail($id);
            DB::beginTransaction();

            $nilaiKasbon = (float) $request->nilai_kasbon;

            // 1. Buat kasbon gen item baru (origin dari LPJ)
            $kasbonItem = KasbonGenItem::create([
                'id_kasbon_gen_item' => IdGenerator::generate('C08', 'c08_kasbon_gen_item', 'id_kasbon_gen_item'),
                'id_kasbon_gen'      => $request->id_kasbon_gen,
                'id_md_invoice'      => $request->id_md_invoice,
                'nilai_kasbon'       => $nilaiKasbon,
                'origin_lpj_gen'     => $lpj->id_lpj_gen, // tandai bahwa item ini dibuat dari LPJ
            ]);

            // 2. Buat LPJ Gen Item
            $lpjItem = LpjGenItem::create([
                'id_lpj_gen_item'        => IdGenerator::generate('D12', 'd12_lpj_gen_item', 'id_lpj_gen_item'),
                'id_lpj_gen'             => $lpj->id_lpj_gen,
                'id_kasbon_gen_item'     => $kasbonItem->id_kasbon_gen_item,
                'amount_lpj'             => (float) $request->amount_lpj,
                'id_md_chart_of_account' => $request->id_md_chart_of_account ?? null,
                'origin_lpj_gen'         => $lpj->id_lpj_gen, // tandai origin
            ]);

            // Recompute amount
            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_gen');
            $amount    = KasbonGenItem::whereIn('id_kasbon_gen', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'New item added successfully',
                'data'    => [
                    'id_lpj_gen_item'        => $lpjItem->id,
                    'id_kasbon_gen_item'      => $kasbonItem->id_kasbon_gen_item,
                    'id_kasbon_gen'          => $request->id_kasbon_gen,
                    'id_kasbon_gen_no'       => $request->id_kasbon_gen,
                    'invoice_typ'            => $kasbonItem->invoice->invoice_typ ?? '-',
                    'invoice_ctg'            => $request->invoice_ctg,
                    'nilai_kasbon'           => $nilaiKasbon,
                    'amount_lpj'             => (float) $request->amount_lpj,
                    'has_lpj'                => true,
                    'id_md_chart_of_account' => $request->id_md_chart_of_account ?? null,
                    'coa_no'                 => null,
                    'coa_name'               => null,
                    'origin_lpj_gen'         => $lpj->id_lpj_gen,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjGen Store New Item Failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // GET KASBONS (untuk create: daftar kasbon gen yang belum dipakai LPJ)
    // ========================================

    public function getKasbons(Request $request)
    {
        try {
            $kasbons = KasbonGen::with(['items.invoice'])
                ->orderBy('tgl_kasbon', 'desc')
                ->get()
                ->map(fn($k) => [
                    'id_kasbon_gen' => $k->id_kasbon_gen,
                    'tgl_kasbon'    => $k->tgl_kasbon?->format('d/m/Y'),
                    'total_kasbon'  => (float) $k->items->sum('nilai_kasbon'),
                    'items_count'   => $k->items->count(),
                    'dep'           => $k->departemen->nama_dep ?? '-',
                ]);

            // Detail items satu kasbon
            $detailKasbonId = $request->get('detail_kasbon');
            $response = ['success' => true, 'data' => $kasbons];

            if ($detailKasbonId) {
                $kasbon = KasbonGen::where('id_kasbon_gen', $detailKasbonId)
                    ->with(['items.invoice'])
                    ->first();

                if ($kasbon) {
                    $response['kasbon_detail'] = [
                        'id_kasbon_gen' => $kasbon->id_kasbon_gen,
                        'tgl_kasbon'    => $kasbon->tgl_kasbon?->format('d/m/Y'),
                        'items'         => $kasbon->items->map(fn($item) => [
                            'id_kasbon_gen_item' => $item->id_kasbon_gen_item,
                            'invoice_typ'        => $item->invoice?->invoice_typ ?? '—',
                            'invoice_ctg'        => $item->invoice?->invoice_ctg ?? '—',
                            'nilai_kasbon'       => (float) $item->nilai_kasbon,
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
    // REFRESH KASBONS (cek kasbon baru yang belum masuk LPJ)
    // ========================================

    public function refreshKasbons(Request $request, $id)
    {
        try {
            $lpj = LpjGen::with('kasbons')->findOrFail($id);

            $existingIds = $lpj->kasbons->pluck('id_kasbon_gen')->toArray();

            $newKasbons = KasbonGen::with('items')
                ->whereNotIn('id_kasbon_gen', $existingIds)
                ->get()
                ->map(fn($k) => [
                    'id_kasbon_gen' => $k->id_kasbon_gen,
                    'tgl_kasbon'    => $k->tgl_kasbon?->format('d/m/Y'),
                    'total_kasbon'  => (float) $k->items->sum('nilai_kasbon'),
                    'items_count'   => $k->items->count(),
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
            'kasbon_ids.*' => 'required|exists:c07_kasbon_gen,id_kasbon_gen',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $lpj = LpjGen::findOrFail($id);
            DB::beginTransaction();

            $added = 0;
            foreach ($request->kasbon_ids as $kasbonGenId) {
                $exists = LpjGenKasbon::where('id_lpj_gen', $lpj->id_lpj_gen)
                    ->where('id_kasbon_gen', $kasbonGenId)
                    ->exists();

                if (!$exists) {
                    LpjGenKasbon::create([
                        'id_lpj_gen_kasbon' => IdGenerator::generate('D11', 'd11_lpj_gen_kasbon', 'id_lpj_gen_kasbon'),
                        'id_lpj_gen'        => $lpj->id_lpj_gen,
                        'id_kasbon_gen'     => $kasbonGenId,
                    ]);
                    $added++;
                }
            }

            $lpj->load('kasbons');
            $kasbonIds = $lpj->kasbons->pluck('id_kasbon_gen');
            $amount    = KasbonGenItem::whereIn('id_kasbon_gen', $kasbonIds)->sum('nilai_kasbon');
            $lpj->update(['amount' => $amount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$added} cash advance(s) added.",
                'added'   => $added,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ========================================
    // SHOW
    // ========================================

    public function show(Request $request, $id)
    {
        try {
            $lpj = LpjGen::with([
                'kasbons.kasbonGen',
                'items.kasbonGenItem.invoice',
                'items.chartOfAccount',
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

            return view('data.lpj-gen.show', compact('lpj', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ========================================
    // DESTROY
    // ========================================

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $lpj      = LpjGen::findOrFail($id);
            $lpjItems = LpjGenItem::where('id_lpj_gen', $lpj->id_lpj_gen)->get();

            // Hapus kasbon gen item yang origin dari LPJ ini
            foreach ($lpjItems as $lpjItem) {
                $kasbonItem = KasbonGenItem::find($lpjItem->id_kasbon_gen_item);
                if ($kasbonItem && $kasbonItem->origin_lpj_gen === $lpj->id_lpj_gen) {
                    $kasbonItem->delete();
                }
                $lpjItem->delete();
            }

            LpjGenKasbon::where('id_lpj_gen', $lpj->id_lpj_gen)->delete();
            if ($lpj->evidence) Storage::disk('public')->delete($lpj->evidence);
            $lpj->delete();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'LPJ General deleted successfully']);
            }
            return redirect()->route('lpj-gen.index')->with('success', 'LPJ General deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LpjGen Destroy Failed', ['id' => $id, 'error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ========================================
    // EXPORT PDF
    // ========================================

    public function exportPdf($id)
    {
        try {
            $lpj = LpjGen::with([
                'kasbons.kasbonGen.departemen',
                'items.kasbonGenItem.invoice',
                'items.chartOfAccount',
            ])->findOrFail($id);

            $totalLpj  = $lpj->items->sum('amount_lpj');
            $terbilang = $this->toTerbilang((int) round($totalLpj)) . ' Rupiah';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'data.lpj-gen.pdf',
                compact('lpj', 'terbilang')
            )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'Arial',
                    'dpi'                  => 150,
                ]);

            $filename = 'LPJ-General-'
                . str_replace(['/', '\\'], '-', $lpj->no_lpj_gen ?? $id)
                . '.pdf';

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            Log::error('LpjGen Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
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
        if ($number < 100)  return $words[(int)($number / 10)] . ' Puluh' . ($number % 10 ? ' ' . $this->toTerbilang($number % 10) : '');
        if ($number < 200)  return 'Seratus' . ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 1000) return $words[(int)($number / 100)] . ' Ratus' . ($number % 100 ? ' ' . $this->toTerbilang($number % 100) : '');
        if ($number < 2000) return 'Seribu' . ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000)     return $this->toTerbilang((int)($number / 1000)) . ' Ribu' . ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1_000_000_000) return $this->toTerbilang((int)($number / 1_000_000)) . ' Juta' . ($number % 1_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000) : '');
        if ($number < 1_000_000_000_000) return $this->toTerbilang((int)($number / 1_000_000_000)) . ' Miliar' . ($number % 1_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000) : '');

        return $this->toTerbilang((int)($number / 1_000_000_000_000)) . ' Triliun' . ($number % 1_000_000_000_000 ? ' ' . $this->toTerbilang($number % 1_000_000_000_000) : '');
    }

    public function export(Request $request)
    {
        try {
            $query = LpjGen::with(['kasbons', 'items']);

            if ($request->filled('no_lpj_gen')) $query->where('no_lpj_gen', 'like', '%' . $request->no_lpj_gen . '%');
            if ($request->filled('date_from'))  $query->where('date', '>=', $request->date_from);
            if ($request->filled('date_to'))    $query->where('date', '<=', $request->date_to);

            $lpjGens = $query->orderBy('date', 'desc')->get();

            $format = $request->get('format', 'excel');

            if ($format === 'pdf') {
                $filters = [
                    'no_lpj_gen' => $request->get('no_lpj_gen', ''),
                    'date_from'  => $request->get('date_from', ''),
                    'date_to'    => $request->get('date_to', ''),
                ];

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                    'data.lpj-gen.export_pdf',
                    compact('lpjGens', 'filters')
                )->setPaper('a4', 'landscape');

                return $pdf->stream('lpj_general_' . date('Ymd_His') . '.pdf');
            }

            return (new LpjGenExport($lpjGens))->download();
        } catch (\Exception $e) {
            Log::error('LPJ General Export Failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
}
