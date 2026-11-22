<?php

namespace App\Http\Controllers\Tracking;

use App\Http\Controllers\Controller;
use App\Models\Data\JoContract;
use App\Models\Data\JoTramper;
use App\Models\Data\JoOther;
use App\Models\Data\JoContractItem;
use App\Models\Data\JoTramperItem;
use App\Models\Data\JoOtherItem;
use App\Models\Data\KasbonContractItem;
use App\Models\Data\KasbonTramperItem;
use App\Models\Data\KasbonOtherItem;
use App\Models\Data\LpjContractItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingJoController extends Controller
{
    /**
     * Halaman utama Tracking JO
     */
    public function index(Request $request)
    {
        $joType = $request->input('jo_type', 'contract');
        $joId   = $request->input('jo_id');
        $joData = null;
        $joList = collect();
        $groups = collect(); // grouped: [['category'=>..., 'rows'=>[...]], ...]

        if ($joType === 'contract') {
            $joList = JoContract::with('contract')
                ->orderByDesc('tgl_jo_cont')
                ->get(['id_jo_cont', 'no_jo_cont', 'tgl_jo_cont', 'title']);
        } elseif ($joType === 'tramper') {
            $joList = JoTramper::with('customer')
                ->orderByDesc('tgl_jo_tram')
                ->get(['id_jo_tram', 'no_jo_tram', 'tgl_jo_tram', 'title']);
        } elseif ($joType === 'other') {
            $joList = JoOther::with('customer')
                ->orderByDesc('tgl_jo_other')
                ->get(['id_jo_other', 'no_jo_other', 'tgl_jo_other', 'title']);
        }

        if ($joId) {
            if ($joType === 'contract') {
                $joData = JoContract::with(['contract', 'area', 'items.invoice', 'pajakItems'])->find($joId);
            } elseif ($joType === 'tramper') {
                $joData = JoTramper::with(['customer', 'items.invoice', 'pajakItems'])->find($joId);
            } elseif ($joType === 'other') {
                $joData = JoOther::with(['customer', 'items.invoice', 'pajakItems'])->find($joId);
            }

            if ($joData) {
                $groups = $this->getGroupedTracking($joType, $joData);
            }
        }

        // Flatten semua rows untuk summary stats
        $allRows = $groups->flatMap(fn($g) => $g['rows']);

        return view('tracking.jo.index', compact('joType', 'joId', 'joList', 'joData', 'groups', 'allRows'));
    }

    /**
     * API: get tracking data sebagai JSON
     */
    public function getData(Request $request)
    {
        $joType = $request->input('jo_type', 'contract');
        $joId   = $request->input('jo_id');

        if (!$joId) {
            return response()->json(['error' => 'jo_id required'], 422);
        }

        if ($joType === 'contract') {
            $joData = JoContract::with(['items.invoice', 'pajakItems'])->find($joId);
        } elseif ($joType === 'tramper') {
            $joData = JoTramper::with(['items.invoice', 'pajakItems'])->find($joId);
        } else {
            $joData = JoOther::with(['items.invoice', 'pajakItems'])->find($joId);
        }

        if (!$joData) return response()->json(['error' => 'not found'], 404);

        $groups = $this->getGroupedTracking($joType, $joData);
        return response()->json($groups);
    }

    /**
     * API: get list JO (AJAX select)
     */
    public function getJoList(Request $request)
    {
        $joType = $request->input('jo_type', 'contract');
        $search = $request->input('q', '');

        if ($joType === 'contract') {
            $data = JoContract::where('no_jo_cont', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orderByDesc('tgl_jo_cont')
                ->limit(50)
                ->get(['id_jo_cont as id', 'no_jo_cont as text', 'tgl_jo_cont as tgl']);
        } elseif ($joType === 'tramper') {
            $data = JoTramper::where('no_jo_tram', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orderByDesc('tgl_jo_tram')
                ->limit(50)
                ->get(['id_jo_tram as id', 'no_jo_tram as text', 'tgl_jo_tram as tgl']);
        } elseif ($joType === 'other') {
            $data = JoOther::where('no_jo_other', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orderByDesc('tgl_jo_other')
                ->limit(50)
                ->get(['id_jo_other as id', 'no_jo_other as text', 'tgl_jo_other as tgl']);
        } else {
            $data = collect();
        }

        return response()->json(['results' => $data]);
    }

    // ─────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Kembalikan koleksi group:
     * [
     *   ['category' => 'nama ctg', 'is_tax' => false, 'rows' => [...], 'count' => N],
     *   ['category' => 'Tax',      'is_tax' => true,  'rows' => [...], 'count' => N],
     * ]
     */
    private function getGroupedTracking(string $joType, $joData): \Illuminate\Support\Collection
    {
        // ── 1. Regular items ──────────────────────────────────────────
        $joItems = $joData->items ?? collect();

        // group by invoice_ctg
        $grouped = $joItems->groupBy(function ($item) {
            return $item->invoice->invoice_ctg ?? 'Lainnya';
        });

        $result = collect();

        foreach ($grouped as $category => $items) {
            $rows = $items->map(function ($item) use ($joType) {
                return $this->buildItemRow($joType, $item);
            });

            $result->push([
                'category' => $category,
                'is_tax'   => false,
                'count'    => $rows->count(),
                'rows'     => $rows,
            ]);
        }

        // ── 2. Tax rows ───────────────────────────────────────────────
        $pajakItems = $joData->pajakItems ?? collect();
        if ($pajakItems->count() > 0) {
            $taxRows = $pajakItems->map(function ($pi) use ($joType) {
                return $this->buildTaxRow($joType, $pi);
            });
            $result->push([
                'category' => 'Tax',
                'is_tax'   => true,
                'count'    => $taxRows->count(),
                'rows'     => $taxRows,
            ]);
        }

        return $result;
    }

    private function buildItemRow(string $joType, $item): array
    {
        // FK column per type
        $fkCol = match($joType) {
            'contract' => 'id_jo_cont_item',
            'tramper'  => 'id_jo_tram_item',
            default    => 'id_jo_other_item',
        };
        $itemId = $item->{$fkCol};

        // Kasbon
        if ($joType === 'contract') {
            $kasbonItems = KasbonContractItem::with('kasbonContract')
                ->where('id_jo_cont_item', $itemId)
                ->where('item_type', 'item')
                ->whereNull('deleted_at')
                ->get();
            $kasbonGroups = $kasbonItems->groupBy('id_kasbon_cont')->map(function ($grp) {
                $f = $grp->first();
                return [
                    'nomor_kasbon' => optional($f->kasbonContract)->no_kasbon_cont ?? $grp->keys()->first(),
                    'tgl_kasbon'   => optional($f->kasbonContract)->tgl_kasbon_cont,
                    'total_kasbon' => $grp->sum('total_kasbon'),
                ];
            })->values();

            $totalLpj = LpjContractItem::where('id_jo_cont_item', $itemId)
                ->where('item_type', 'item')
                ->sum('amount_lpj');

        } elseif ($joType === 'tramper') {
            $kasbonItems = KasbonTramperItem::with('kasbonTramper')
                ->where('id_jo_tram_item', $itemId)
                ->where('item_type', 'item')
                ->whereNull('deleted_at')
                ->get();
            $kasbonGroups = $kasbonItems->groupBy('id_kasbon_tram')->map(function ($grp) {
                $f = $grp->first();
                return [
                    'nomor_kasbon' => optional($f->kasbonTramper)->no_kasbon_tram ?? $grp->keys()->first(),
                    'tgl_kasbon'   => optional($f->kasbonTramper)->tgl_kasbon_tram,
                    'total_kasbon' => $grp->sum('total_kasbon'),
                ];
            })->values();

            $totalLpj = DB::table('d05_lpj_tram_item')
                ->where('id_jo_tram_item', $itemId)
                ->where('item_type', 'item')
                ->sum('amount_lpj');

        } else {
            $kasbonItems = KasbonOtherItem::with('kasbonOther')
                ->where('id_jo_other_item', $itemId)
                ->where('item_type', 'item')
                ->whereNull('deleted_at')
                ->get();
            $kasbonGroups = $kasbonItems->groupBy('id_kasbon_other')->map(function ($grp) {
                $f = $grp->first();
                return [
                    'nomor_kasbon' => optional($f->kasbonOther)->no_kasbon_other ?? $grp->keys()->first(),
                    'tgl_kasbon'   => optional($f->kasbonOther)->tgl_kasbon_other,
                    'total_kasbon' => $grp->sum('total_kasbon'),
                ];
            })->values();

            $totalLpj = DB::table('d07_lpj_other_item')
                ->where('id_jo_other_item', $itemId)
                ->where('item_type', 'item')
                ->sum('amount_lpj');
        }

        $totalKasbon = $kasbonItems->sum('total_kasbon');
        $hpp         = floatval($item->hpp_ops);
        $selling     = floatval($item->hargajual_idr);
        $selisih     = $totalKasbon - $hpp;

        $status = $this->resolveStatus($hpp, $totalKasbon, $selisih);

        return [
            'id_item'       => $itemId,
            'is_tax'        => false,
            'invoice_ctg'   => $item->invoice->invoice_ctg ?? '-',
            'nama_item'     => $item->invoice->invoice_typ ?? '-',
            'selling'       => $selling,
            'hpp'           => $hpp,
            'total_kasbon'  => $totalKasbon,
            'total_lpj'     => floatval($totalLpj),
            'selisih'       => $selisih,
            'persen_kasbon' => $hpp > 0 ? round(($totalKasbon / $hpp) * 100, 1) : 0,
            'status'        => $status,
            'jumlah_kasbon' => $kasbonGroups->count(),
            'kasbon_detail' => $kasbonGroups,
        ];
    }

    private function buildTaxRow(string $joType, $pi): array
    {
        // Tax tidak ada kasbon langsung; HPP = nominal_pajak_hpp, selling = nominal_pajak
        $hpp     = $pi->include_hpp     ? floatval($pi->nominal_pajak_hpp) : 0;
        $selling = $pi->include_selling ? floatval($pi->nominal_pajak)     : 0;

        return [
            'id_item'       => $pi->getKey(),
            'is_tax'        => true,
            'invoice_ctg'   => 'Tax',
            'nama_item'     => $pi->nama_pajak . ' (' . $pi->persentase . '%)',
            'selling'       => $selling,
            'hpp'           => $hpp,
            'total_kasbon'  => 0,
            'total_lpj'     => 0,
            'selisih'       => 0 - $hpp,
            'persen_kasbon' => 0,
            'status'        => $hpp > 0 ? 'belum' : 'no_hpp',
            'jumlah_kasbon' => 0,
            'kasbon_detail' => collect(),
        ];
    }

    private function resolveStatus(float $hpp, float $totalKasbon, float $selisih): string
    {
        if ($hpp == 0)             return 'no_hpp';
        if ($totalKasbon == 0)     return 'belum';
        if ($selisih < -0.01)      return 'kurang';
        if ($selisih >  0.01)      return 'lebih';
        return 'pas';
    }
}
