<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Helpers\IdGenerator;
use App\Models\Data\MutasiPembayaran;
use App\Models\Data\MutasiPembayaranVoucher;
use App\Models\Data\MutasiPembayaranDetail;

use App\Models\Data\JoTramper;
use App\Models\Data\JoOther;
use App\Models\Data\JoContract;

use App\Models\Data\KasbonTramper;
use App\Models\Data\KasbonTramperItem;
use App\Models\Data\KasbonOther;
use App\Models\Data\KasbonOtherItem;
use App\Models\Data\KasbonContract;
use App\Models\Data\KasbonContractItem;
use App\Models\Data\KasbonGen;
use App\Models\Data\KasbonGenItem;

use App\Models\Data\LpjTramper;
use App\Models\Data\LpjKasbonTramper;
use App\Models\Data\LpjTramperItem;
use App\Models\Data\LpjOther;
use App\Models\Data\LpjKasbonOther;
use App\Models\Data\LpjOtherItem;
use App\Models\Data\LpjContract;
use App\Models\Data\LpjKasbon;
use App\Models\Data\LpjContractItem;
use App\Models\Data\LpjGen;
use App\Models\Data\LpjGenKasbon;
use App\Models\Data\LpjGenItem;

use App\Models\Master\ChartOfAccount;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MutasiPembayaranController extends Controller
{
    // ═══════════════════════════════════════════════════════
    //  HEADER
    // ═══════════════════════════════════════════════════════

    public function index(Request $request)
    {
        try {
            $query = MutasiPembayaran::with(['vouchers']);

            if ($request->filled('nomor'))
                $query->where('nomor', 'like', '%' . $request->nomor . '%');
            if ($request->filled('tanggal_dari'))
                $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            if ($request->filled('tanggal_sampai'))
                $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
            if ($request->filled('id_md_chart_of_account'))
                $query->where('id_md_chart_of_account', $request->id_md_chart_of_account);
            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(
                    fn($q) => $q
                        ->where('nomor', 'like', "%{$s}%")
                        ->orWhere('memo', 'like', "%{$s}%")
                        ->orWhere('no_cek', 'like', "%{$s}%")
                        ->orWhereHas('coa', fn($q2) => $q2->where('account_name', 'like', "%{$s}%"))
                );
            }

            $query->orderBy($request->get('sort_by', 'tanggal'), $request->get('sort_order', 'desc'));
            $mutasiPembayarans = $query->get();

            if ($request->expectsJson())
                return response()->json(['success' => true, 'data' => $mutasiPembayarans]);

            $chartOfAccounts = ChartOfAccount::orderBy('no_account')->get();
            $currentFilters  = [
                'nomor'                  => $request->get('nomor', ''),
                'tanggal_dari'           => $request->get('tanggal_dari', ''),
                'tanggal_sampai'         => $request->get('tanggal_sampai', ''),
                'id_md_chart_of_account' => $request->get('id_md_chart_of_account', ''),
            ];

            return view('data.mutasi-pembayaran.index', compact('mutasiPembayarans', 'chartOfAccounts', 'currentFilters'));
        } catch (\Exception $e) {
            if ($request->expectsJson())
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $chartOfAccounts = ChartOfAccount::orderBy('no_account')->get();
        $nextNomor = IdGenerator::generateMutasiNo();
        return view('data.mutasi-pembayaran.create', compact('chartOfAccounts', 'nextNomor'));
    }

    public function store(Request $request)
    {
        Log::info('=== STORE MUTASI START ===', $request->all()); // ← TAMBAH

        $validator = Validator::make($request->all(), [
            'id_md_chart_of_account' => 'nullable|exists:a13_md_chart_of_account,id_md_chart_of_account',
            'no_cek'                 => 'nullable|string|max:100',
            'tanggal'                => 'required|date',
            'kurs'                   => 'nullable|numeric|min:0',
            'memo'                   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning('=== VALIDASI GAGAL ===', $validator->errors()->toArray()); // ← TAMBAH
            if ($request->expectsJson())
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $nomor = IdGenerator::generateMutasiNo();
            Log::info('=== NOMOR GENERATED ===', ['nomor' => $nomor]);
            $mp    = MutasiPembayaran::create([
                'nomor'                  => $nomor,
                'id_md_chart_of_account' => $request->id_md_chart_of_account,
                'no_cek'                 => $request->no_cek,
                'tanggal'                => $request->tanggal,
                'kurs'                   => $request->kurs ?? 1,
                'memo'                   => $request->memo,
                'created_by'             => auth()->id(),
            ]);
            DB::commit();

            if ($request->expectsJson())
                return response()->json([
                    'success'      => true,
                    'message'      => 'Mutasi Pembayaran berhasil dibuat',
                    'redirect_url' => route('mutasi-pembayaran.edit', $mp->id),
                    'data'         => ['id' => $mp->id, 'nomor' => $mp->nomor],
                ], 201);

            return redirect()->route('mutasi-pembayaran.edit', $mp->id)
                ->with('success', 'Mutasi Pembayaran ' . $nomor . ' berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson())
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $mp = MutasiPembayaran::with([
                'coa',
                'vouchers.details.joTramper',
                'vouchers.details.joOther',
                'vouchers.details.joContract',
                'vouchers.details.kasbonTramper.departemen',
                'vouchers.details.kasbonTramper.cabang',
                'vouchers.details.kasbonOther.departemen',
                'vouchers.details.kasbonOther.cabang',
                'vouchers.details.kasbonContract.departemen',
                'vouchers.details.kasbonContract.cabang',
                'vouchers.details.kasbonGen.departemen',
                'vouchers.details.kasbonGen.cabang',
            ])->findOrFail($id);

            $summary = [
                'total_voucher' => $mp->vouchers->count(),
                'total_nilai'   => $mp->vouchers->flatMap->details->sum('nilai'),
                'total_pj'      => $mp->vouchers->flatMap->details->sum('pj'),
                'total_selisih' => $mp->vouchers->flatMap->details->sum('selisih'),
            ];

            if ($request->expectsJson())
                return response()->json(['success' => true, 'data' => $mp, 'summary' => $summary]);

            return view('data.mutasi-pembayaran.show', compact('mp', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson())
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $mutasiPembayaran = MutasiPembayaran::with([
                'coa',
                'vouchers'                    => fn($q) => $q->orderBy('urutan'),
                'vouchers.coa',
                'vouchers.details.joTramper',
                'vouchers.details.joOther',
                'vouchers.details.joContract',
                'vouchers.details.kasbonTramper',
                'vouchers.details.kasbonOther',
                'vouchers.details.kasbonContract',
                'vouchers.details.kasbonGen',
            ])->findOrFail($id);

            $chartOfAccounts = ChartOfAccount::orderBy('no_account')->get();
            return view('data.mutasi-pembayaran.edit', compact('mutasiPembayaran', 'chartOfAccounts'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Mutasi Pembayaran tidak ditemukan');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_md_chart_of_account' => 'nullable|exists:a13_md_chart_of_account,id_md_chart_of_account',
            'no_cek'                 => 'nullable|string|max:100',
            'tanggal'                => 'required|date',
            'kurs'                   => 'nullable|numeric|min:0',
            'memo'                   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson())
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $mp = MutasiPembayaran::findOrFail($id);
            $mp->update([
                'id_md_chart_of_account' => $request->id_md_chart_of_account,
                'no_cek'                 => $request->no_cek,
                'tanggal'                => $request->tanggal,
                'kurs'                   => $request->kurs ?? 1,
                'memo'                   => $request->memo,
            ]);
            DB::commit();

            if ($request->expectsJson())
                return response()->json(['success' => true, 'message' => 'Header berhasil diupdate']);
            return redirect()->route('mutasi-pembayaran.show', $id)->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson())
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $mp = MutasiPembayaran::with('vouchers.details')->findOrFail($id);
            foreach ($mp->vouchers as $v) $v->details()->delete();
            $mp->vouchers()->delete();
            $mp->delete();
            DB::commit();

            if ($request->expectsJson())
                return response()->json(['success' => true, 'message' => 'Mutasi Pembayaran berhasil dihapus']);
            return redirect()->route('mutasi-pembayaran.index')->with('success', 'Mutasi Pembayaran berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson())
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════════
    //  VOUCHER
    // ═══════════════════════════════════════════════════════

    public function storeVoucher(Request $request)
    {
        // Sanitize: buang nilai kosong / NaN saja — JANGAN cek is_numeric()
        // karena id_md_chart_of_account bertipe string (bukan auto-increment integer)
        if ($request->has('id_md_chart_of_account')) {
            $val = $request->id_md_chart_of_account;
            if ($val === '' || $val === 'NaN' || $val === '0') {
                $request->merge(['id_md_chart_of_account' => null]);
            }
        }
        if ($request->has('tgl_keluar') && $request->tgl_keluar === '') {
            $request->merge(['tgl_keluar' => null]);
        }

        $validator = Validator::make($request->all(), [
            'id_mutasi_pembayaran' => 'required|exists:e01_mutasi_pembayaran,id',
            'nomor_voucher'        => 'nullable|string|max:100',
            'keterangan'           => 'nullable|string',
            'urutan'               => 'nullable|integer|min:1',
            'tgl_keluar'             => 'nullable|date',
            'id_md_chart_of_account' => 'nullable|string',
        ]);
        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            $maxUrutan    = MutasiPembayaranVoucher::where('id_mutasi_pembayaran', $request->id_mutasi_pembayaran)->max('urutan') ?? 0;
            $nomorVoucher = IdGenerator::generateVoucherNo();
            $voucher = MutasiPembayaranVoucher::create([
                'id_mutasi_pembayaran'   => $request->id_mutasi_pembayaran,
                'nomor_voucher'          => $nomorVoucher,
                'keterangan'             => $request->keterangan,
                'urutan'                 => $request->urutan ?? ($maxUrutan + 1),
                'tgl_keluar'             => $request->tgl_keluar ?: null,
                'id_md_chart_of_account' => $request->id_md_chart_of_account ?: null,
            ]);
            DB::commit();

            $voucher->load('coa');
            $coaDisplay = $voucher->coa
                ? trim(($voucher->coa->no_account ?? '') . ' ' . ($voucher->coa->account_name ?? $voucher->coa->nama_account ?? ''))
                : null;

            return response()->json(['success' => true, 'message' => 'Voucher berhasil ditambahkan', 'data' => [
                'id'                     => $voucher->id,
                'nomor_voucher'          => $voucher->nomor_voucher,
                'keterangan'             => $voucher->keterangan,
                'urutan'                 => $voucher->urutan,
                'tgl_keluar'             => $voucher->tgl_keluar ? $voucher->tgl_keluar->format('Y-m-d') : null,
                'id_md_chart_of_account' => $voucher->id_md_chart_of_account,
                'coa_display'            => $coaDisplay,
            ]], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateVoucher(Request $request, $id)
    {
        // Sanitize: buang nilai kosong / NaN saja — JANGAN cek is_numeric()
        // karena id_md_chart_of_account bertipe string (bukan auto-increment integer)
        if ($request->has('id_md_chart_of_account')) {
            $val = $request->id_md_chart_of_account;
            if ($val === '' || $val === 'NaN' || $val === '0') {
                $request->merge(['id_md_chart_of_account' => null]);
            }
        }
        if ($request->has('tgl_keluar') && $request->tgl_keluar === '') {
            $request->merge(['tgl_keluar' => null]);
        }

        $validator = Validator::make($request->all(), [
            'nomor_voucher' => 'nullable|string|max:100',
            'keterangan'    => 'nullable|string',
            'urutan'        => 'nullable|integer|min:1',
            'tgl_keluar'             => 'nullable|date',
            'id_md_chart_of_account' => 'nullable|string',
        ]);
        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            $voucher = MutasiPembayaranVoucher::findOrFail($id);
            $voucher->update([
                'nomor_voucher'          => $request->nomor_voucher ?? $voucher->nomor_voucher,
                'keterangan'             => $request->keterangan,
                'urutan'                 => $request->urutan ?? $voucher->urutan,
                'tgl_keluar'             => $request->has('tgl_keluar') ? ($request->tgl_keluar ?: null) : $voucher->tgl_keluar,
                'id_md_chart_of_account' => $request->has('id_md_chart_of_account') ? ($request->id_md_chart_of_account ?: null) : $voucher->id_md_chart_of_account,
            ]);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Voucher berhasil diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyVoucher(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $voucher = MutasiPembayaranVoucher::with('details')->findOrFail($id);
            $voucher->details()->delete();
            $voucher->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Voucher berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    //  DETAIL
    // ═══════════════════════════════════════════════════════

    public function storeDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_mutasi_voucher' => 'required|exists:e02_mutasi_pembayaran_voucher,id',
            'jenis'             => 'required|in:tramper,other,contract,general',
            'id_jo_tram'        => 'nullable|exists:b03_jo_tram,id_jo_tram',
            'id_jo_other'       => 'nullable|exists:b05_jo_other,id_jo_other',
            'id_jo_cont'        => 'nullable|exists:b01_jo_cont,id_jo_cont',
            'id_kasbon_tram'    => 'nullable|exists:c03_kasbon_tram,id',
            'id_kasbon_other'   => 'nullable|exists:c05_kasbon_other,id',
            'id_kasbon_cont'    => 'nullable|exists:c01_kasbon_cont,id',
            'id_kasbon_gen'     => 'nullable|exists:c07_kasbon_gen,id',
            'keterangan'        => 'nullable|string|max:255',
        ]);

        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        $dup = $this->checkKasbonDuplicate($request);
        if ($dup)
            return response()->json(['success' => false, 'message' => $dup], 422);

        DB::beginTransaction();
        try {
            $amounts = $this->resolveKasbonAmounts($request->jenis, $request);
            $detail  = MutasiPembayaranDetail::create([
                'id_mutasi_voucher' => $request->id_mutasi_voucher,
                'jenis'             => $request->jenis,
                'id_jo_tram'        => $request->id_jo_tram,
                'id_jo_other'       => $request->id_jo_other,
                'id_jo_cont'        => $request->id_jo_cont,
                'id_kasbon_tram'    => $request->id_kasbon_tram,
                'id_kasbon_other'   => $request->id_kasbon_other,
                'id_kasbon_cont'    => $request->id_kasbon_cont,
                'id_kasbon_gen'     => $request->id_kasbon_gen,
                'nilai'             => $amounts['nilai'],
                'pj'                => $amounts['pj'],
                'selisih'           => $amounts['nilai'] - $amounts['pj'],
                'keterangan'        => $request->keterangan,
            ]);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Detail berhasil ditambahkan', 'data' => $this->formatDetail($detail)], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Detail Failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function showDetail($id)
    {
        try {
            $detail = MutasiPembayaranDetail::findOrFail($id);
            return response()->json(['success' => true, 'data' => $this->formatDetail($detail)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Detail tidak ditemukan'], 404);
        }
    }

    public function updateDetail(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis'           => 'sometimes|in:tramper,other,contract,general',
            'id_jo_tram'      => 'nullable|exists:b03_jo_tram,id_jo_tram',
            'id_jo_other'     => 'nullable|exists:b05_jo_other,id_jo_other',
            'id_jo_cont'      => 'nullable|exists:b01_jo_cont,id_jo_cont',
            'id_kasbon_tram'  => 'nullable|exists:c03_kasbon_tram,id',
            'id_kasbon_other' => 'nullable|exists:c05_kasbon_other,id',
            'id_kasbon_cont'  => 'nullable|exists:c01_kasbon_cont,id',
            'id_kasbon_gen'   => 'nullable|exists:c07_kasbon_gen,id',
            'keterangan'      => 'nullable|string|max:255',
        ]);
        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            $detail  = MutasiPembayaranDetail::findOrFail($id);
            $jenis   = $request->jenis ?? $detail->jenis;
            $amounts = $this->resolveKasbonAmounts($jenis, $request, $detail);

            $detail->update([
                'jenis'           => $jenis,
                'id_jo_tram'      => $request->has('id_jo_tram')      ? $request->id_jo_tram      : $detail->id_jo_tram,
                'id_jo_other'     => $request->has('id_jo_other')     ? $request->id_jo_other     : $detail->id_jo_other,
                'id_jo_cont'      => $request->has('id_jo_cont')      ? $request->id_jo_cont      : $detail->id_jo_cont,
                'id_kasbon_tram'  => $request->has('id_kasbon_tram')  ? $request->id_kasbon_tram  : $detail->id_kasbon_tram,
                'id_kasbon_other' => $request->has('id_kasbon_other') ? $request->id_kasbon_other : $detail->id_kasbon_other,
                'id_kasbon_cont'  => $request->has('id_kasbon_cont')  ? $request->id_kasbon_cont  : $detail->id_kasbon_cont,
                'id_kasbon_gen'   => $request->has('id_kasbon_gen')   ? $request->id_kasbon_gen   : $detail->id_kasbon_gen,
                'nilai'           => $amounts['nilai'],
                'pj'              => $amounts['pj'],
                'selisih'         => $amounts['nilai'] - $amounts['pj'],
                'keterangan'      => $request->keterangan ?? $detail->keterangan,
            ]);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Detail berhasil diupdate', 'data' => $this->formatDetail($detail->fresh())]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyDetail(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            MutasiPembayaranDetail::findOrFail($id)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Detail berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    //  DROPDOWN / OPTIONS
    // ═══════════════════════════════════════════════════════

    public function joOptions(Request $request)
    {
        try {
            $data = match ($request->get('jenis')) {
                'tramper'  => JoTramper::orderBy('created_at', 'desc')->get()->map(fn($j) => [
                    'id' => $j->id_jo_tram,
                    'nomor' => $j->no_jo ?? $j->nomor ?? $j->id_jo_tram,
                    'label' => $j->title ?? $j->keterangan ?? '',
                ]),
                'other'    => JoOther::orderBy('created_at', 'desc')->get()->map(fn($j) => [
                    'id' => $j->id_jo_other,
                    'nomor' => $j->no_jo ?? $j->nomor ?? $j->id_jo_other,
                    'label' => $j->title ?? $j->keterangan ?? '',
                ]),
                'contract' => JoContract::orderBy('created_at', 'desc')->get()->map(fn($j) => [
                    'id' => $j->id_jo_cont,
                    'nomor' => $j->no_jo_cont ?? $j->no_jo ?? $j->nomor ?? $j->id_jo_cont,
                    'label' => $j->title ?? $j->keterangan ?? '',
                ]),
                default    => collect(),
            };
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function kasbonOptions(Request $request)
    {
        $jenis = $request->get('jenis');
        $joId  = $request->get('jo_id');

        try {
            $usedTram  = MutasiPembayaranDetail::whereNotNull('id_kasbon_tram')->pluck('id_kasbon_tram');
            $usedOther = MutasiPembayaranDetail::whereNotNull('id_kasbon_other')->pluck('id_kasbon_other');
            $usedCont  = MutasiPembayaranDetail::whereNotNull('id_kasbon_cont')->pluck('id_kasbon_cont');
            $usedGen   = MutasiPembayaranDetail::whereNotNull('id_kasbon_gen')->pluck('id_kasbon_gen');

            $mapFn = fn($k, $nomor) => ['id' => $k->id, 'nomor' => $nomor, 'label' => implode(' / ', array_filter([
                $k->departemen?->nama_dep,
                $k->cabang?->nama_branch,
                optional($k->tgl_kasbon)->format('d/m/Y'),
            ]))];

            $data = match ($jenis) {
                'tramper' => KasbonTramper::with(['departemen', 'cabang'])->whereNotIn('id', $usedTram)
                    ->when($joId, fn($q) => $q->where('id_jo_tram', $joId))
                    ->get()->map(fn($k) => $mapFn($k, $k->nomor ?? $k->id_kasbon_tram ?? 'C03-' . $k->id))->values(),
                'other'   => KasbonOther::with(['departemen', 'cabang'])->whereNotIn('id', $usedOther)
                    ->when($joId, fn($q) => $q->where('id_jo_other', $joId))
                    ->get()->map(fn($k) => $mapFn($k, $k->nomor ?? $k->id_kasbon_other ?? 'C05-' . $k->id))->values(),
                'contract' => KasbonContract::with(['departemen', 'cabang'])->whereNotIn('id', $usedCont)
                    ->when($joId, fn($q) => $q->where('id_jo_cont', $joId))
                    ->get()->map(fn($k) => $mapFn($k, $k->nomor ?? $k->id_kasbon_cont ?? 'C01-' . $k->id))->values(),
                'general' => KasbonGen::with(['departemen', 'cabang'])->whereNotIn('id', $usedGen)
                    ->get()->map(fn($k) => $mapFn($k, $k->nomor ?? $k->id_kasbon_gen ?? 'C07-' . $k->id))->values(),
                default   => collect(),
            };

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function kasbonInfo(Request $request)
    {
        $jenis    = $request->get('jenis');
        $kasbonId = $request->get('kasbon_id');
        try {
            $info = match ($jenis) {
                'tramper'  => $this->resolveKasbonTramper($kasbonId),
                'other'    => $this->resolveKasbonOther($kasbonId),
                'contract' => $this->resolveKasbonContract($kasbonId),
                'general'  => $this->resolveKasbonGen($kasbonId),
                default    => null,
            };
            if (!$info)
                return response()->json(['success' => false, 'message' => 'Kasbon tidak ditemukan'], 404);
            return response()->json(['success' => true, 'data' => $info]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /data/mutasi-pembayaran/jo-kasbon-summary?jenis=contract&jo_id=xxx
     * Return semua kasbon dalam JO beserta rincian item + kategori.
     */
    public function joKasbonSummary(Request $request)
    {
        $jenis = $request->get('jenis');
        $joId  = $request->get('jo_id');

        if (!$jenis) {
            return response()->json(['success' => false, 'message' => 'Parameter jenis wajib diisi'], 422);
        }

        try {
            // ── GENERAL ──────────────────────────────────────────────────────────
            if ($jenis === 'general') {
                $kasbonId = $request->get('kasbon_id');
                if (!$kasbonId) {
                    return response()->json(['success' => false, 'message' => 'Parameter kasbon_id wajib diisi'], 422);
                }

                $k = KasbonGen::with(['departemen', 'cabang', 'release', 'items.invoice'])
                    ->findOrFail($kasbonId);

                $items = $k->items->map(function ($item) use ($k) {
                    $inv    = $item->invoice;
                    $lpjIds = LpjGenKasbon::where('id_kasbon_gen', $k->id_kasbon_gen)->pluck('id_lpj_gen');
                    $pj     = LpjGenItem::whereIn('id_lpj_gen', $lpjIds)
                        ->where('id_kasbon_gen_item', $item->id_kasbon_gen_item)
                        ->sum('amount_lpj');
                    return [
                        'id'           => $item->id_kasbon_gen_item,
                        'label'        => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? $item->id_kasbon_gen_item,
                        'kategori'     => $inv?->invoice_ctg ?? '—',
                        'nilai_kasbon' => (float) $item->nilai_kasbon,
                        'pj'           => (float) $pj,
                        'selisih'      => (float) $item->nilai_kasbon - (float) $pj,
                        'akun'         => '',
                    ];
                });

                $totalNilai = (float) $items->sum('nilai_kasbon');
                $totalPj    = (float) $items->sum('pj');

                $sudahDipakai = MutasiPembayaranDetail::whereNotNull('id_kasbon_gen')
                    ->pluck('id_kasbon_gen')
                    ->contains($k->id);

                return response()->json(['success' => true, 'data' => [[
                    'id_kasbon'     => $k->id,
                    'nomor'         => $k->id_kasbon_gen ?? $k->nomor,
                    'nilai'         => $totalNilai,
                    'pj'            => $totalPj,
                    'selisih'       => $totalNilai - $totalPj,
                    'departemen'    => $k->departemen?->nama_dep   ?? '—',
                    'cabang'        => $k->cabang?->nama_branch    ?? '—',
                    'release'       => $k->release?->nama_release  ?? '—',
                    'tgl_kasbon'    => optional($k->tgl_kasbon)->format('d/m/Y')  ?? '',
                    'tgl_release'   => optional($k->tgl_release)->format('d/m/Y') ?? '',
                    'sudah_dipakai' => $sudahDipakai,
                    'items'         => $items->values()->toArray(),
                ]]]);
            }

            // ── TRAMPER / OTHER / CONTRACT ────────────────────────────────────────
            if (!$joId) {
                return response()->json(['success' => false, 'message' => 'Parameter jo_id wajib diisi'], 422);
            }

            $voucherId = $request->get('voucher_id'); // tambah param dari JS

            $usedTram  = MutasiPembayaranDetail::whereNotNull('id_kasbon_tram')
                ->when($voucherId, fn($q) => $q->where('id_mutasi_voucher', $voucherId))
                ->pluck('id_kasbon_tram');
            $usedOther = MutasiPembayaranDetail::whereNotNull('id_kasbon_other')
                ->when($voucherId, fn($q) => $q->where('id_mutasi_voucher', $voucherId))
                ->pluck('id_kasbon_other');
            $usedCont  = MutasiPembayaranDetail::whereNotNull('id_kasbon_cont')
                ->when($voucherId, fn($q) => $q->where('id_mutasi_voucher', $voucherId))
                ->pluck('id_kasbon_cont');

            $data = match ($jenis) {
                'tramper'  => $this->kasbonItemSummaryTramper($joId, $usedTram),
                'other'    => $this->kasbonItemSummaryOther($joId, $usedOther),
                'contract' => $this->kasbonItemSummaryContract($joId, $usedCont),
                default    => collect(),
            };

            return response()->json(['success' => true, 'data' => $data->values()]);
        } catch (\Exception $e) {
            Log::error('JO Kasbon Summary Failed', [
                'jenis'    => $jenis,
                'jo_id'    => $joId,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ═══════════════════════════════════════════════════════

    private function resolveKasbonAmounts(string $jenis, Request $request, ?MutasiPembayaranDetail $existing = null): array
    {
        $kasbonId = match ($jenis) {
            'tramper'  => $request->id_kasbon_tram  ?? $existing?->id_kasbon_tram,
            'other'    => $request->id_kasbon_other ?? $existing?->id_kasbon_other,
            'contract' => $request->id_kasbon_cont  ?? $existing?->id_kasbon_cont,
            'general'  => $request->id_kasbon_gen   ?? $existing?->id_kasbon_gen,
            default    => null,
        };
        if (!$kasbonId) return ['nilai' => 0, 'pj' => 0];

        $info = match ($jenis) {
            'tramper'  => $this->resolveKasbonTramper($kasbonId),
            'other'    => $this->resolveKasbonOther($kasbonId),
            'contract' => $this->resolveKasbonContract($kasbonId),
            'general'  => $this->resolveKasbonGen($kasbonId),
            default    => null,
        };

        return ['nilai' => $info['nilai'] ?? 0, 'pj' => $info['pj'] ?? 0];
    }

    private function resolveKasbonTramper($id): ?array
    {
        $k = KasbonTramper::with(['departemen', 'cabang', 'release', 'items'])->where('id', $id)->first();
        if (!$k) return null;
        $nilai       = $k->items->sum('nilai_kasbon');
        $itemIds     = $k->items->pluck('id_kasbon_tram_item');
        $lpjIds      = LpjKasbonTramper::where('id_kasbon_tram', $k->id_kasbon_tram)->pluck('id_lpj_tram');
        $pj          = LpjTramperItem::whereIn('id_lpj_tram', $lpjIds)
            ->whereIn('id_kasbon_tram_item', $itemIds)
            ->sum('amount_lpj');
        return [
            'id_kasbon' => $k->id,
            'nomor' => $k->id_kasbon_tram ?? $k->nomor,
            'tgl_kasbon' => optional($k->tgl_kasbon)->format('d/m/Y'),
            'tgl_release' => optional($k->tgl_release)->format('d/m/Y'),
            'departemen' => $k->departemen?->nama_dep,
            'cabang' => $k->cabang?->nama_branch,
            'release' => $k->release?->nama_release,
            'nilai' => (float)$nilai,
            'pj' => (float)$pj,
            'selisih' => (float)($nilai - $pj)
        ];
    }

    private function resolveKasbonOther($id): ?array
    {
        $k = KasbonOther::with(['departemen', 'cabang', 'release', 'items'])->where('id', $id)->first();
        if (!$k) return null;
        $nilai   = $k->items->sum('nilai_kasbon');
        $itemIds = $k->items->pluck('id_kasbon_other_item');
        $lpjIds  = LpjKasbonOther::where('id_kasbon_other', $k->id_kasbon_other)->pluck('id_lpj_other');
        $pj      = LpjOtherItem::whereIn('id_lpj_other', $lpjIds)
            ->whereIn('id_kasbon_other_item', $itemIds)
            ->sum('amount_lpj');
        return [
            'id_kasbon'   => $k->id,
            'nomor'       => $k->id_kasbon_other ?? $k->nomor,
            'jo_id'       => $k->id_jo_other,
            'tgl_kasbon'  => optional($k->tgl_kasbon)->format('d/m/Y'),
            'tgl_release' => optional($k->tgl_release)->format('d/m/Y'),
            'departemen'  => $k->departemen?->nama_dep,
            'cabang'      => $k->cabang?->nama_branch,
            'release'     => $k->release?->nama_release,
            'nilai'       => (float)$nilai,
            'pj'          => (float)$pj,
            'selisih'     => (float)($nilai - $pj),
        ];
    }

    private function resolveKasbonContract($id): ?array
    {
        $k = KasbonContract::with(['departemen', 'cabang', 'release', 'items'])->where('id', $id)->first();
        if (!$k) return null;
        $nilai   = $k->items->sum('nilai_kasbon');
        $itemIds = $k->items->pluck('id_kasbon_cont_item');
        $lpjIds  = LpjKasbon::where('id_kasbon_cont', $k->id_kasbon_cont)->pluck('id_lpj_cont');
        $pj      = LpjContractItem::whereIn('id_lpj_cont', $lpjIds)
            ->whereIn('id_kasbon_cont_item', $itemIds)
            ->sum('amount_lpj');
        return [
            'id_kasbon'   => $k->id,
            'nomor'       => $k->id_kasbon_cont ?? $k->nomor,
            'jo_id'       => $k->id_jo_cont,
            'tgl_kasbon'  => optional($k->tgl_kasbon)->format('d/m/Y'),
            'tgl_release' => optional($k->tgl_release)->format('d/m/Y'),
            'departemen'  => $k->departemen?->nama_dep,
            'cabang'      => $k->cabang?->nama_branch,
            'release'     => $k->release?->nama_release,
            'nilai'       => (float)$nilai,
            'pj'          => (float)$pj,
            'selisih'     => (float)($nilai - $pj),
        ];
    }

    private function resolveKasbonGen($id): ?array
    {
        $k = KasbonGen::with(['departemen', 'cabang', 'release', 'items'])->findOrFail($id);
        if (!$k) return null;
        $nilai   = $k->items->sum('nilai_kasbon');
        $itemIds = $k->items->pluck('id_kasbon_gen_item');
        $lpjIds  = LpjGenKasbon::where('id_kasbon_gen', $k->id_kasbon_gen)->pluck('id_lpj_gen');
        $pj      = LpjGenItem::whereIn('id_lpj_gen', $lpjIds)
            ->whereIn('id_kasbon_gen_item', $itemIds)
            ->sum('amount_lpj');
        return [
            'id_kasbon' => $k->id,
            'nomor' => $k->id_kasbon_gen ?? $k->nomor,
            'tgl_kasbon' => optional($k->tgl_kasbon)->format('d/m/Y'),
            'tgl_release' => optional($k->tgl_release)->format('d/m/Y'),
            'departemen' => $k->departemen?->nama_dep,
            'cabang' => $k->cabang?->nama_branch,
            'release' => $k->release?->nama_release,
            'nilai' => (float)$nilai,
            'pj' => (float)$pj,
            'selisih' => (float)($nilai - $pj)
        ];
    }

    // ─── Kasbon Item Summary (untuk joKasbonSummary) ─────────────────────────

    private function kasbonItemSummaryContract($joId, $usedIds): \Illuminate\Support\Collection
    {
        return KasbonContract::with(['departemen', 'cabang', 'release', 'items.joContractItem.invoice'])
            ->where('id_jo_cont', $joId)->get()
            ->map(function ($k) use ($usedIds) {
                $lpjIds = LpjKasbon::where('id_kasbon_cont', $k->id_kasbon_cont)->pluck('id_lpj_cont');
                $items  = $k->items->map(function ($item) use ($lpjIds) {
                    $pj  = LpjContractItem::whereIn('id_lpj_cont', $lpjIds)
                        ->where('id_kasbon_cont_item', $item->id_kasbon_cont_item)->sum('amount_lpj');
                    $inv = $item->joContractItem?->invoice;
                    $coa = ChartOfAccount::whereIn(
                        'id_md_chart_of_account',
                        LpjContractItem::whereIn('id_lpj_cont', $lpjIds)
                            ->where('id_kasbon_cont_item', $item->id_kasbon_cont_item)
                            ->pluck('id_md_chart_of_account')->unique()->filter()
                    )->get(['no_account', 'account_name']);
                    return [
                        'id'           => $item->id_kasbon_cont_item,
                        'label'        => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $item->id),
                        'kategori'     => $inv?->invoice_ctg ?? '—',
                        'nilai_kasbon' => (float)$item->nilai_kasbon,
                        'pj'           => (float)$pj,
                        'selisih'      => (float)($item->nilai_kasbon - $pj),
                        'akun'         => $coa->map(fn($c) => $c->no_account . ' — ' . $c->account_name)->implode(', '),
                    ];
                });
                return $this->buildKasbonSummaryRow($k, $k->id_kasbon_cont, $items, $usedIds);
            });
    }

    private function kasbonItemSummaryTramper($joId, $usedIds): \Illuminate\Support\Collection
    {
        return KasbonTramper::with(['departemen', 'cabang', 'release', 'items.joTramperItem.invoice'])
            ->where('id_jo_tram', $joId)->get()
            ->map(function ($k) use ($usedIds) {
                $lpjIds = LpjKasbonTramper::where('id_kasbon_tram', $k->id_kasbon_tram)->pluck('id_lpj_tram');
                $items  = $k->items->map(function ($item) use ($lpjIds) {
                    $pj  = LpjTramperItem::whereIn('id_lpj_tram', $lpjIds)
                        ->where('id_kasbon_tram_item', $item->id_kasbon_tram_item)->sum('amount_lpj');
                    $inv = $item->joTramperItem?->invoice;
                    $coa = ChartOfAccount::whereIn(
                        'id_md_chart_of_account',
                        LpjTramperItem::whereIn('id_lpj_tram', $lpjIds)
                            ->where('id_kasbon_tram_item', $item->id_kasbon_tram_item)
                            ->pluck('id_md_chart_of_account')->unique()->filter()
                    )->get(['no_account', 'account_name']);
                    return [
                        'id'           => $item->id_kasbon_tram_item,
                        'label'        => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $item->id),
                        'kategori'     => $inv?->invoice_ctg ?? '—',
                        'nilai_kasbon' => (float)$item->nilai_kasbon,
                        'pj'           => (float)$pj,
                        'selisih'      => (float)($item->nilai_kasbon - $pj),
                        'akun'         => $coa->map(fn($c) => $c->no_account . ' — ' . $c->account_name)->implode(', '),
                    ];
                });
                return $this->buildKasbonSummaryRow($k, $k->id_kasbon_tram, $items, $usedIds);
            });
    }

    private function kasbonItemSummaryOther($joId, $usedIds): \Illuminate\Support\Collection
    {
        return KasbonOther::with(['departemen', 'cabang', 'release', 'items.joOtherItem.invoice'])
            ->where('id_jo_other', $joId)->get()
            ->map(function ($k) use ($usedIds) {
                $lpjIds = LpjKasbonOther::where('id_kasbon_other', $k->id_kasbon_other)->pluck('id_lpj_other');
                $items  = $k->items->map(function ($item) use ($lpjIds) {
                    $pj  = LpjOtherItem::whereIn('id_lpj_other', $lpjIds)
                        ->where('id_kasbon_other_item', $item->id_kasbon_other_item)->sum('amount_lpj');
                    $inv = $item->joOtherItem?->invoice;
                    $coa = ChartOfAccount::whereIn(
                        'id_md_chart_of_account',
                        LpjOtherItem::whereIn('id_lpj_other', $lpjIds)
                            ->where('id_kasbon_other_item', $item->id_kasbon_other_item)
                            ->pluck('id_md_chart_of_account')->unique()->filter()
                    )->get(['no_account', 'account_name']);
                    return [
                        'id'           => $item->id_kasbon_other_item,
                        'label'        => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $item->id),
                        'kategori'     => $inv?->invoice_ctg ?? '—',
                        'nilai_kasbon' => (float)$item->nilai_kasbon,
                        'pj'           => (float)$pj,
                        'selisih'      => (float)($item->nilai_kasbon - $pj),
                        'akun'         => $coa->map(fn($c) => $c->no_account . ' — ' . $c->account_name)->implode(', '),
                    ];
                });
                return $this->buildKasbonSummaryRow($k, $k->id_kasbon_other, $items, $usedIds);
            });
    }

    private function kasbonItemSummaryGeneral($usedIds): \Illuminate\Support\Collection
    {
        return KasbonGen::with(['departemen', 'cabang', 'release', 'items'])
            ->orderBy('tgl_kasbon', 'desc')
            ->get()
            ->map(function ($k) use ($usedIds) {
                $items = $k->items->map(function ($item) {
                    $pj = (float) LpjGenItem::where('id_kasbon_gen_item', $item->id_kasbon_gen_item)
                        ->sum('amount_lpj');
                    return [
                        'id'           => $item->id_kasbon_gen_item,
                        'label'        => $item->id_kasbon_gen_item,
                        'kategori'     => '—',
                        'nilai_kasbon' => (float) $item->nilai_kasbon,
                        'pj'           => $pj,
                        'selisih'      => (float) $item->nilai_kasbon - $pj,
                        'akun'         => '',
                    ];
                });
                return $this->buildKasbonSummaryRow($k, $k->nomor ?? $k->id_kasbon_gen, $items, $usedIds);
            });
    }


    /** Shared row builder untuk semua kasbonItemSummary* */
    private function buildKasbonSummaryRow($kasbon, $bizKey, $items, $usedIds): array
    {
        $totalNilai = $items->sum('nilai_kasbon');
        $totalPj    = $items->sum('pj');
        return [
            'id_kasbon'     => $kasbon->id,
            'nomor' => $bizKey ?? $kasbon->nomor,
            'tgl_kasbon'    => optional($kasbon->tgl_kasbon)->format('d/m/Y'),
            'tgl_release'   => optional($kasbon->tgl_release)->format('d/m/Y'),
            'departemen'    => $kasbon->departemen?->nama_dep,
            'cabang'        => $kasbon->cabang?->nama_branch,
            'release'       => $kasbon->release?->nama_release,
            'nilai'         => $totalNilai,
            'pj'            => $totalPj,
            'selisih'       => $totalNilai - $totalPj,
            'sudah_dipakai' => $usedIds->contains($kasbon->id),
            'items'         => $items->values(),
        ];
    }

    // ─── Items untuk formatDetail (preview setelah simpan) ───────────────────

    private function getKasbonItems(string $jenis, $kasbon): array
    {
        if (!$kasbon) return [];

        return match ($jenis) {
            'tramper' => KasbonTramperItem::with('joTramperItem.invoice')
                ->where('id_kasbon_tram', $kasbon->id_kasbon_tram)->get()
                ->map(function ($item) use ($kasbon) {
                    $lpjIds = LpjKasbonTramper::where('id_kasbon_tram', $kasbon->id_kasbon_tram)->pluck('id_lpj_tram');
                    $pj     = LpjTramperItem::whereIn('id_lpj_tram', $lpjIds)
                        ->where('id_kasbon_tram_item', $item->id_kasbon_tram_item)->sum('amount_lpj');
                    $inv    = $item->joTramperItem?->invoice;
                    $coa    = ChartOfAccount::whereIn(
                        'id_md_chart_of_account',
                        LpjTramperItem::whereIn('id_lpj_tram', $lpjIds)
                            ->where('id_kasbon_tram_item', $item->id_kasbon_tram_item)
                            ->pluck('id_md_chart_of_account')->unique()->filter()
                    )->get(['no_account', 'account_name']);
                    return [
                        'label'        => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $item->id),
                        'kategori'     => $inv?->invoice_ctg ?? '—',
                        'nilai_kasbon' => (float)$item->nilai_kasbon,
                        'pj'           => (float)$pj,
                        'selisih'      => (float)($item->nilai_kasbon - $pj),
                        'akun'         => $coa->map(fn($c) => $c->no_account . ' — ' . $c->account_name)->implode(', '),
                    ];
                })->toArray(),

            'other' => KasbonOtherItem::with('joOtherItem.invoice')
                ->where('id_kasbon_other', $kasbon->id_kasbon_other)->get()
                ->map(function ($item) use ($kasbon) {
                    $lpjIds = LpjKasbonOther::where('id_kasbon_other', $kasbon->id_kasbon_other)->pluck('id_lpj_other');
                    $pj     = LpjOtherItem::whereIn('id_lpj_other', $lpjIds)
                        ->where('id_kasbon_other_item', $item->id_kasbon_other_item)->sum('amount_lpj');
                    $inv    = $item->joOtherItem?->invoice;
                    $coa    = ChartOfAccount::whereIn(
                        'id_md_chart_of_account',
                        LpjOtherItem::whereIn('id_lpj_other', $lpjIds)
                            ->where('id_kasbon_other_item', $item->id_kasbon_other_item)
                            ->pluck('id_md_chart_of_account')->unique()->filter()
                    )->get(['no_account', 'account_name']);
                    return [
                        'label'        => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $item->id),
                        'kategori'     => $inv?->invoice_ctg ?? '—',
                        'nilai_kasbon' => (float)$item->nilai_kasbon,
                        'pj'           => (float)$pj,
                        'selisih'      => (float)($item->nilai_kasbon - $pj),
                        'akun'         => $coa->map(fn($c) => $c->no_account . ' — ' . $c->account_name)->implode(', '),
                    ];
                })->toArray(),

            'contract' => KasbonContractItem::with('joContractItem.invoice')
                ->where('id_kasbon_cont', $kasbon->id_kasbon_cont)->get()
                ->map(function ($item) use ($kasbon) {
                    $lpjIds = LpjKasbon::where('id_kasbon_cont', $kasbon->id_kasbon_cont)->pluck('id_lpj_cont');
                    $pj     = LpjContractItem::whereIn('id_lpj_cont', $lpjIds)
                        ->where('id_kasbon_cont_item', $item->id_kasbon_cont_item)->sum('amount_lpj');
                    $inv    = $item->joContractItem?->invoice;
                    $coa    = ChartOfAccount::whereIn(
                        'id_md_chart_of_account',
                        LpjContractItem::whereIn('id_lpj_cont', $lpjIds)
                            ->where('id_kasbon_cont_item', $item->id_kasbon_cont_item)
                            ->pluck('id_md_chart_of_account')->unique()->filter()
                    )->get(['no_account', 'account_name']);
                    return [
                        'label'        => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $item->id),
                        'kategori'     => $inv?->invoice_ctg ?? '—',
                        'nilai_kasbon' => (float)$item->nilai_kasbon,
                        'pj'           => (float)$pj,
                        'selisih'      => (float)($item->nilai_kasbon - $pj),
                        'akun'         => $coa->map(fn($c) => $c->no_account . ' — ' . $c->account_name)->implode(', '),
                    ];
                })->toArray(),

            'general' => KasbonGenItem::with('invoice')
                ->where('id_kasbon_gen', $kasbon->id_kasbon_gen)->get()
                ->map(function ($item) use ($kasbon) {
                    $inv    = $item->invoice;
                    $lpjIds = LpjGenKasbon::where('id_kasbon_gen', $kasbon->id_kasbon_gen)->pluck('id_lpj_gen');
                    $pj     = LpjGenItem::whereIn('id_lpj_gen', $lpjIds)
                        ->where('id_kasbon_gen_item', $item->id_kasbon_gen_item)
                        ->sum('amount_lpj');
                    return [
                        'label'        => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? $item->id_kasbon_gen_item,
                        'kategori'     => $inv?->invoice_ctg ?? '—',
                        'nilai_kasbon' => (float) $item->nilai_kasbon,
                        'pj'           => (float) $pj,
                        'selisih'      => (float) $item->nilai_kasbon - (float) $pj,
                        'akun'         => '',
                    ];
                })->toArray(),

            default => [],
        };
    }

    private function checkKasbonDuplicate(Request $request): ?string
    {
        $map = [
            'tramper'  => ['col' => 'id_kasbon_tram',  'val' => $request->id_kasbon_tram],
            'other'    => ['col' => 'id_kasbon_other', 'val' => $request->id_kasbon_other],
            'contract' => ['col' => 'id_kasbon_cont',  'val' => $request->id_kasbon_cont],
            'general'  => ['col' => 'id_kasbon_gen',   'val' => $request->id_kasbon_gen],
        ];
        $jenis = $request->jenis;
        if (!isset($map[$jenis]) || !$map[$jenis]['val']) return null;
        // Hanya blokir jika sudah ada di voucher yang sama
        return MutasiPembayaranDetail::where($map[$jenis]['col'], $map[$jenis]['val'])
            ->where('id_mutasi_voucher', $request->id_mutasi_voucher)
            ->exists()
            ? 'Kasbon ini sudah ditambahkan ke voucher ini.'
            : null;
    }

    private function formatDetail(MutasiPembayaranDetail $detail): array
    {
        $kasbon = match ($detail->jenis) {
            'tramper'  => $detail->id_kasbon_tram  ? KasbonTramper::with(['departemen', 'cabang', 'release'])->where('id', $detail->id_kasbon_tram)->first()  : null,
            'other'    => $detail->id_kasbon_other ? KasbonOther::with(['departemen', 'cabang', 'release'])->where('id', $detail->id_kasbon_other)->first()    : null,
            'contract' => $detail->id_kasbon_cont  ? KasbonContract::with(['departemen', 'cabang', 'release'])->where('id', $detail->id_kasbon_cont)->first()  : null,
            'general'  => $detail->id_kasbon_gen   ? KasbonGen::with(['departemen', 'cabang', 'release'])->where('id', $detail->id_kasbon_gen)->first()         : null,
            default    => null,
        };

        // Pakai resolve helpers supaya nomor kasbon sama persis dengan voucherDetails
        $kasbonDbId = match ($detail->jenis) {
            'tramper'  => $detail->id_kasbon_tram,
            'other'    => $detail->id_kasbon_other,
            'contract' => $detail->id_kasbon_cont,
            'general'  => $detail->id_kasbon_gen,
            default    => null,
        };
        $resolvedInfo = $kasbonDbId ? match ($detail->jenis) {
            'tramper'  => $this->resolveKasbonTramper($kasbonDbId),
            'other'    => $this->resolveKasbonOther($kasbonDbId),
            'contract' => $this->resolveKasbonContract($kasbonDbId),
            'general'  => $this->resolveKasbonGen($kasbonDbId),
            default    => null,
        } : null;

        $jo = match ($detail->jenis) {
            'tramper'  => $detail->id_jo_tram  ? JoTramper::where('id_jo_tram',  $detail->id_jo_tram)->first()  : null,
            'other'    => $detail->id_jo_other ? JoOther::where('id_jo_other',   $detail->id_jo_other)->first() : null,
            'contract' => $detail->id_jo_cont  ? JoContract::where('id_jo_cont', $detail->id_jo_cont)->first()  : null,
            default    => null,
        };

        $joKey = match ($detail->jenis) {
            'tramper'  => $detail->id_jo_tram,
            'other'    => $detail->id_jo_other,
            'contract' => $detail->id_jo_cont,
            default    => null,
        };

        $items = $this->getKasbonItems($detail->jenis, $kasbon);

        return [
            'id'                => $detail->id,
            'id_mutasi_voucher' => $detail->id_mutasi_voucher,
            'jenis'             => $detail->jenis,
            'id_jo_tram'        => $detail->id_jo_tram,
            'id_jo_other'       => $detail->id_jo_other,
            'id_jo_cont'        => $detail->id_jo_cont,
            'id_kasbon_tram'    => $detail->id_kasbon_tram,
            'id_kasbon_other'   => $detail->id_kasbon_other,
            'id_kasbon_cont'    => $detail->id_kasbon_cont,
            'id_kasbon_gen'     => $detail->id_kasbon_gen,
            'nilai'             => (float)($resolvedInfo['nilai']   ?? $detail->nilai),
            'pj'                => (float)($resolvedInfo['pj']      ?? $detail->pj),
            'selisih'           => (float)($resolvedInfo['selisih'] ?? $detail->selisih),
            'keterangan'        => $detail->keterangan,
            'nomor_kasbon'      => $resolvedInfo['nomor'] ?? null,
            'tgl_kasbon'        => $resolvedInfo['tgl_kasbon'] ?? optional($kasbon?->tgl_kasbon)->format('d/m/Y'),
            'departemen'        => $resolvedInfo['departemen'] ?? $kasbon?->departemen?->nama_dep,
            'cabang'            => $resolvedInfo['cabang']     ?? $kasbon?->cabang?->nama_branch,
            'release'           => $resolvedInfo['release']    ?? $kasbon?->release?->nama_release,
            'nomor_jo'          => $jo ? ($jo->no_jo ?? $jo->no_jo_cont ?? $jo->no_jo_tram ?? $jo->no_jo_other ?? $jo->nomor ?? null) : null,
            'jo'     => $jo     ? ['id' => $joKey, 'nomor' => $jo->no_jo ?? $jo->no_jo_cont ?? $jo->no_jo_tram ?? $jo->no_jo_other ?? $jo->nomor ?? $joKey] : null,
            'kasbon' => $kasbon ? ['id' => $kasbon->id, 'nomor' => $resolvedInfo['nomor'] ?? $kasbon->id] : null,
            'items'             => $items,
        ];
    }

    public function voucherDetails($voucherId)
    {
        try {
            $voucher = MutasiPembayaranVoucher::findOrFail($voucherId);
            $details = $voucher->details()->orderBy('id')->get();

            $data = $details->map(function ($d) {
                // ── Pakai helper yang sudah ada (compute dari items + LPJ) ──
                $kasbonId = match ($d->jenis) {
                    'tramper'  => $d->id_kasbon_tram,
                    'other'    => $d->id_kasbon_other,
                    'contract' => $d->id_kasbon_cont,
                    'general'  => $d->id_kasbon_gen,
                    default    => null,
                };

                $info = $kasbonId ? match ($d->jenis) {
                    'tramper'  => $this->resolveKasbonTramper($kasbonId),
                    'other'    => $this->resolveKasbonOther($kasbonId),
                    'contract' => $this->resolveKasbonContract($kasbonId),
                    'general'  => $this->resolveKasbonGen($kasbonId),
                    default    => null,
                } : null;

                // ── Items + kategori via getKasbonItems ──
                $kasbon = $kasbonId ? match ($d->jenis) {
                    'tramper'  => KasbonTramper::where('id',  $kasbonId)->first(),
                    'other'    => KasbonOther::where('id',    $kasbonId)->first(),
                    'contract' => KasbonContract::where('id', $kasbonId)->first(),
                    'general'  => KasbonGen::where('id',      $kasbonId)->first(),
                    default    => null,
                } : null;

                $items = $this->getKasbonItems($d->jenis, $kasbon);

                // ── Nomor JO ──
                $nomor_jo = null;
                if ($d->jenis !== 'general') {
                    $jo = match ($d->jenis) {
                        'tramper'  => $d->id_jo_tram  ? (JoTramper::where('id_jo_tram',  $d->id_jo_tram)->first()  ?? JoTramper::find($d->id_jo_tram))  : null,
                        'other'    => $d->id_jo_other ? (JoOther::where('id_jo_other',   $d->id_jo_other)->first()  ?? JoOther::find($d->id_jo_other))   : null,
                        'contract' => $d->id_jo_cont  ? (JoContract::where('id_jo_cont', $d->id_jo_cont)->first()   ?? JoContract::find($d->id_jo_cont)) : null,
                        default    => null,
                    };
                    $nomor_jo = $jo ? ($jo->no_jo_cont ?? $jo->no_jo ?? $jo->nomor ?? $jo->no_jo_tram ?? $jo->no_jo_other ?? null) : null;
                }

                // ── BARU ──
                $akun = collect();
                if ($kasbon) {
                    $coaIds = match ($d->jenis) {
                        'tramper'  => LpjTramperItem::whereIn(
                            'id_lpj_tram',
                            LpjKasbonTramper::where('id_kasbon_tram', $kasbon->id_kasbon_tram)->pluck('id_lpj_tram')
                        )->pluck('id_md_chart_of_account'),
                        'other'    => LpjOtherItem::whereIn(
                            'id_lpj_other',
                            LpjKasbonOther::where('id_kasbon_other', $kasbon->id_kasbon_other)->pluck('id_lpj_other')
                        )->pluck('id_md_chart_of_account'),
                        'contract' => LpjContractItem::whereIn(
                            'id_lpj_cont',
                            LpjKasbon::where('id_kasbon_cont', $kasbon->id_kasbon_cont)->pluck('id_lpj_cont')
                        )->pluck('id_md_chart_of_account'),
                        default    => collect(),
                    };
                    $akun = ChartOfAccount::whereIn('id_md_chart_of_account', $coaIds->unique()->filter())
                        ->get(['no_account', 'account_name'])
                        ->map(fn($c) => ['no' => $c->no_account, 'nama' => $c->account_name])
                        ->values();
                }

                return [
                    'id'           => $d->id,
                    'jenis'        => $d->jenis,
                    'nomor_jo'     => $nomor_jo,
                    'nomor_kasbon' => $info['nomor'] ?? null,
                    'jo_id'        => $info['jo_id'] ?? null,
                    'nilai'        => (float) ($d->nilai   ?? $info['nilai']   ?? 0),
                    'pj'           => (float) ($d->pj      ?? $info['pj']      ?? 0),
                    'selisih'      => (float) ($d->selisih ?? $info['selisih'] ?? 0),
                    'keterangan'   => $d->keterangan,
                    'departemen'   => $info['departemen'] ?? null,
                    'cabang'       => $info['cabang']     ?? null,
                    'release'      => $info['release']    ?? null,
                    'tgl_kasbon'   => $info['tgl_kasbon']  ?? null,
                    'tgl_release'  => $info['tgl_release'] ?? null,
                    'akun'         => $akun,
                    'items'        => $items,
                ];
            })->values();

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('voucherDetails error: ' . $e->getMessage(), [
                'voucherId' => $voucherId,
                'trace'     => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function exportVoucherPdf($voucherId)
    {
        $voucher = MutasiPembayaranVoucher::with(['details', 'coa'])->findOrFail($voucherId);
        $mutasi  = \App\Models\Data\MutasiPembayaran::with('coa')
            ->find($voucher->id_mutasi_pembayaran);
        $voucher->setRelation('mutasi', $mutasi);

        $pdfRows = [];
        foreach ($voucher->details as $detail) {
            $jenis      = $detail->jenis;
            $kasbonDbId = match ($jenis) {
                'tramper'  => $detail->id_kasbon_tram  ?? null,
                'other'    => $detail->id_kasbon_other ?? null,
                'contract' => $detail->id_kasbon_cont  ?? null,
                'general'  => $detail->id_kasbon_gen   ?? null,
                default    => null,
            };
            if (!$kasbonDbId) continue;

            $row = match ($jenis) {
                'tramper'  => $this->voucherPdfRowTramper($kasbonDbId),
                'other'    => $this->voucherPdfRowOther($kasbonDbId),
                'contract' => $this->voucherPdfRowContract($kasbonDbId),
                'general'  => $this->voucherPdfRowGen($kasbonDbId),
                default    => ['paid_to' => null, 'items' => []],
            };

            if (!empty($row['paid_to']) || !empty($row['items'])) {
                $pdfRows[] = $row;
            }
        }
        $voucher->setAttribute('pdfRows', $pdfRows);

        // ← GANTI return view() lama dengan ini:
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'data.mutasi-pembayaran.voucher-pdf',
            ['voucher' => $voucher, 'mutasi' => $mutasi]
        )->setPaper('a4', 'portrait');

        $filename = 'payment-voucher-' . str_replace(['/', '\\'], '-', $voucher->nomor_voucher ?? $voucherId) . '.pdf';
        return $pdf->stream($filename);
    }

    // ─── TRAMPER ───────────────────────────────────────────────────

    private function voucherPdfRowTramper(int $id): array
    {
        $kasbon = KasbonTramper::with([
            'release',
            'items',                           // kasbon items (id_kasbon_tram_item, id_jo_tram_item)
            'joTramper.items.invoice',         // jo items → invoice for category/type
        ])->find($id);

        if (!$kasbon) return ['paid_to' => null, 'items' => []];

        // Map kasbon items by jo_tram_item id
        $kasbonLookup = $kasbon->items->keyBy('id_jo_tram_item');

        // Get all LPJ items for this kasbon (across any LPJ document)
        $kasbonItemIds = $kasbon->items->pluck('id_kasbon_tram_item')->filter();

        // Collect lpj amounts keyed by id_kasbon_tram_item
        $lpjAmounts = collect();
        if ($kasbonItemIds->isNotEmpty()) {
            $lpjIds = LpjKasbonTramper::where('id_kasbon_tram', $kasbon->id_kasbon_tram)
                ->pluck('id_lpj_tram');

            if ($lpjIds->isNotEmpty()) {
                $lpjAmounts = LpjTramperItem::whereIn('id_lpj_tram', $lpjIds)
                    ->whereIn('id_kasbon_tram_item', $kasbonItemIds)
                    ->get()
                    ->groupBy('id_kasbon_tram_item')
                    ->map(fn($rows) => $rows->sum('amount_lpj'));
            }
        }

        // Build flat items from JO items (for category + label)
        $joItems = $kasbon->joTramper ? $kasbon->joTramper->items : collect();
        $items   = [];

        foreach ($joItems as $joItem) {
            $kasbonItem = $kasbonLookup->get($joItem->id_jo_tram_item ?? $joItem->id);
            if (!$kasbonItem) continue;

            $amount = (float) ($lpjAmounts->get($kasbonItem->id_kasbon_tram_item) ?? 0);
            if ($amount <= 0) continue;

            $invoice  = $joItem->invoice ?? null;
            $items[]  = [
                'category' => $invoice->invoice_ctg ?? 'General',
                'label'    => $invoice->invoice_typ ?? ('Item #' . $kasbonItem->id_kasbon_tram_item),
                'amount'   => $amount,
            ];
        }

        // Fallback: no JO structure, just list by kasbon item
        if (empty($items) && $lpjAmounts->isNotEmpty()) {
            foreach ($kasbon->items as $kasbonItem) {
                $amount = (float) ($lpjAmounts->get($kasbonItem->id_kasbon_tram_item) ?? 0);
                if ($amount <= 0) continue;
                $items[] = [
                    'category' => 'Tramper',
                    'label'    => 'Item #' . $kasbonItem->id_kasbon_tram_item,
                    'amount'   => $amount,
                ];
            }
        }

        return [
            'paid_to' => $kasbon->release->nama_release ?? null,
            'items'   => $items,
        ];
    }

    // ─── OTHER ─────────────────────────────────────────────────────

    private function voucherPdfRowOther(int $id): array
    {
        $kasbon = KasbonOther::with([
            'release',
            'items',                          // id_kasbon_other_item, id_jo_other_item
            'joOther.items.invoice',
        ])->find($id);

        if (!$kasbon) return ['paid_to' => null, 'items' => []];

        $kasbonLookup  = $kasbon->items->keyBy('id_jo_other_item');
        $kasbonItemIds = $kasbon->items->pluck('id_kasbon_other_item')->filter();

        $lpjAmounts = collect();
        if ($kasbonItemIds->isNotEmpty()) {
            $lpjIds = LpjKasbonOther::where('id_kasbon_other', $kasbon->id_kasbon_other)
                ->pluck('id_lpj_other');

            if ($lpjIds->isNotEmpty()) {
                $lpjAmounts = LpjOtherItem::whereIn('id_lpj_other', $lpjIds)
                    ->whereIn('id_kasbon_other_item', $kasbonItemIds)
                    ->get()
                    ->groupBy('id_kasbon_other_item')
                    ->map(fn($rows) => $rows->sum('amount_lpj'));
            }
        }

        $joItems = $kasbon->joOther ? $kasbon->joOther->items : collect();
        $items   = [];

        foreach ($joItems as $joItem) {
            $kasbonItem = $kasbonLookup->get($joItem->id_jo_other_item ?? $joItem->id);
            if (!$kasbonItem) continue;

            $amount = (float) ($lpjAmounts->get($kasbonItem->id_kasbon_other_item) ?? 0);
            if ($amount <= 0) continue;

            $invoice = $joItem->invoice ?? null;
            $items[] = [
                'category' => $invoice->invoice_ctg ?? 'General',
                'label'    => $invoice->invoice_typ ?? ('Item #' . $kasbonItem->id_kasbon_other_item),
                'amount'   => $amount,
            ];
        }

        if (empty($items) && $lpjAmounts->isNotEmpty()) {
            foreach ($kasbon->items as $kasbonItem) {
                $amount = (float) ($lpjAmounts->get($kasbonItem->id_kasbon_other_item) ?? 0);
                if ($amount <= 0) continue;
                $items[] = [
                    'category' => 'Other',
                    'label'    => 'Item #' . $kasbonItem->id_kasbon_other_item,
                    'amount'   => $amount,
                ];
            }
        }

        return [
            'paid_to' => $kasbon->release->nama_release ?? null,
            'items'   => $items,
        ];
    }

    // ─── CONTRACT ──────────────────────────────────────────────────

    private function voucherPdfRowContract(int $id): array
    {
        // Adjust model/relationship names to match your codebase
        $kasbon = KasbonContract::with([
            'release',
            'items',                          // id_kasbon_cont_item, id_jo_cont_item
            'joContract.items.invoice',
        ])->find($id);

        if (!$kasbon) return ['paid_to' => null, 'items' => []];

        $kasbonLookup  = $kasbon->items->keyBy('id_jo_cont_item');
        $kasbonItemIds = $kasbon->items->pluck('id_kasbon_cont_item')->filter();

        $lpjAmounts = collect();
        if ($kasbonItemIds->isNotEmpty()) {
            $lpjIds = LpjKasbon::where('id_kasbon_cont', $kasbon->id_kasbon_cont)
                ->pluck('id_lpj_cont');

            if ($lpjIds->isNotEmpty()) {
                $lpjAmounts = LpjContractItem::whereIn('id_lpj_cont', $lpjIds)
                    ->whereIn('id_kasbon_cont_item', $kasbonItemIds)
                    ->get()
                    ->groupBy('id_kasbon_cont_item')
                    ->map(fn($rows) => $rows->sum('amount_lpj'));
            }
        }

        $joItems = $kasbon->joContract ? $kasbon->joContract->items : collect();
        $items   = [];

        foreach ($joItems as $joItem) {
            $kasbonItem = $kasbonLookup->get($joItem->id_jo_cont_item ?? $joItem->id);
            if (!$kasbonItem) continue;

            $amount = (float) ($lpjAmounts->get($kasbonItem->id_kasbon_cont_item) ?? 0);
            if ($amount <= 0) continue;

            $invoice = $joItem->invoice ?? null;
            $items[] = [
                'category' => $invoice->invoice_ctg ?? 'General',
                'label'    => $invoice->invoice_typ ?? ('Item #' . $kasbonItem->id_kasbon_cont_item),
                'amount'   => $amount,
            ];
        }

        if (empty($items) && $lpjAmounts->isNotEmpty()) {
            foreach ($kasbon->items as $kasbonItem) {
                $amount = (float) ($lpjAmounts->get($kasbonItem->id_kasbon_cont_item) ?? 0);
                if ($amount <= 0) continue;
                $items[] = [
                    'category' => 'Contract',
                    'label'    => 'Item #' . $kasbonItem->id_kasbon_cont_item,
                    'amount'   => $amount,
                ];
            }
        }

        return [
            'paid_to' => $kasbon->release->nama_release ?? null,
            'items'   => $items,
        ];
    }

    // ─── GENERAL ───────────────────────────────────────────────────

    private function voucherPdfRowGen(int $id): array
    {
        $kasbon = KasbonGen::with([
            'release',
            'items.invoice',                  // id_kasbon_gen_item + invoice for label/category
        ])->find($id);

        if (!$kasbon) return ['paid_to' => null, 'items' => []];

        $kasbonItemIds = $kasbon->items->pluck('id_kasbon_gen_item')->filter();

        $lpjAmounts = collect();
        if ($kasbonItemIds->isNotEmpty()) {
            $lpjIds = LpjGenKasbon::where('id_kasbon_gen', $kasbon->id_kasbon_gen)
                ->pluck('id_lpj_gen');

            if ($lpjIds->isNotEmpty()) {
                $lpjAmounts = LpjGenItem::whereIn('id_lpj_gen', $lpjIds)
                    ->whereIn('id_kasbon_gen_item', $kasbonItemIds)
                    ->get()
                    ->groupBy('id_kasbon_gen_item')
                    ->map(fn($rows) => $rows->sum('amount_lpj'));
            }
        }

        $items = [];
        foreach ($kasbon->items as $kasbonItem) {
            $amount = (float) ($lpjAmounts->get($kasbonItem->id_kasbon_gen_item) ?? 0);
            if ($amount <= 0) continue;
            $inv = $kasbonItem->invoice;
            $items[] = [
                'category' => $inv?->invoice_ctg ?? $kasbonItem->kategori ?? 'General',
                'label'    => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? $kasbonItem->keterangan ?? $kasbonItem->description ?? ('Item #' . $kasbonItem->id_kasbon_gen_item),
                'amount'   => $amount,
            ];
        }

        return [
            'paid_to' => $kasbon->release->nama_release ?? null,
            'items'   => $items,
        ];
    }

    public function nextVoucherNo()
    {
        return response()->json([
            'success' => true,
            'nomor'   => IdGenerator::generateVoucherNo(),
        ]);
    }
}
