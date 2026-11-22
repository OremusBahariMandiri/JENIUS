<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Helpers\IdGenerator;
use App\Models\Data\MutasiPembayaran;
use App\Models\Data\MutasiPembayaranVoucher;
use App\Models\Data\MutasiPembayaranDetail;

use App\Models\Data\JoTramper;
use App\Models\Data\JoTramperItem;
use App\Models\Data\JoOther;
use App\Models\Data\JoContract;

use App\Models\Data\KasbonTramper;
use App\Models\Data\KasbonTramperItem;
use App\Models\Data\KasbonOther;
use App\Models\Data\KasbonOtherItem;
use App\Models\Data\KasbonContract;
use App\Models\Data\KasbonContractItem;
use App\Models\Data\KasbonContractPajak;
use App\Models\Data\KasbonGenPajak;
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
use App\Traits\LogsActivity;

class MutasiPembayaranController extends Controller
{
    use LogsActivity;

    // ═══════════════════════════════════════════════════════
    //  HEADER
    // ═══════════════════════════════════════════════════════

    public function index(Request $request)
    {
        try {
            $query = MutasiPembayaran::with(['vouchers.details', 'coa']);

            if ($request->filled('nomor'))
                $query->where('nomor', 'like', '%' . $request->nomor . '%');
            if ($request->filled('tanggal_from'))
                $query->whereDate('tanggal', '>=', $request->tanggal_from);
            if ($request->filled('tanggal_to'))
                $query->whereDate('tanggal', '<=', $request->tanggal_to);
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

            $chartOfAccounts = ChartOfAccount::whereIn('payment_type', ['Kas', 'Bank'])
                ->orderBy('no_account')->get();
            $currentFilters  = [
                'nomor'                  => $request->get('nomor', ''),
                'tanggal_from'           => $request->get('tanggal_from', ''),
                'tanggal_to'             => $request->get('tanggal_to', ''),
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
        $chartOfAccounts = ChartOfAccount::whereIn('payment_type', ['Kas', 'Bank'])
            ->orderBy('no_account')->get();
        return view('data.mutasi-pembayaran.create', compact('chartOfAccounts'));
    }

    public function store(Request $request)
    {
        Log::info('=== STORE MUTASI START ===', $request->all()); // ← TAMBAH

        $validator = Validator::make($request->all(), [
            'id_md_chart_of_account' => 'required|exists:a13_md_chart_of_account,id_md_chart_of_account',
            'no_cek'                 => 'nullable|string|max:100',
            'tanggal'                => 'required|date',
            'kurs'                   => 'nullable|numeric|min:0',
            'mata_uang'              => 'nullable|string|max:10',
            'tgl_kurs'               => 'nullable|date',
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
            // Generate nomor voucher berdasarkan tipe COA (Kas → AK, Bank → BK)
            $coa = ChartOfAccount::find($request->id_md_chart_of_account);
            $nomor = match ($coa?->payment_type) {
                'Kas'  => IdGenerator::generateVoucherNoKas(),
                'Bank' => IdGenerator::generateVoucherNoBank(),
                default => IdGenerator::generateVoucherNo(),
            };
            Log::info('=== NOMOR VOUCHER GENERATED ===', ['nomor' => $nomor, 'payment_type' => $coa?->payment_type]);

            $mp = MutasiPembayaran::create([
                'nomor'                  => $nomor,
                'id_md_chart_of_account' => $request->id_md_chart_of_account,
                'no_cek'                 => $request->no_cek,
                'tanggal'                => $request->tanggal,
                'kurs'                   => $request->kurs ?? 1,
                'mata_uang'              => $request->mata_uang ?? 'IDR',
                'tgl_kurs'               => $request->tgl_kurs ?: null,
                'memo'                   => $request->memo,
                'created_by'             => auth()->id(),
            ]);

            // Auto-create satu voucher untuk mutasi ini
            MutasiPembayaranVoucher::create([
                'id_mutasi_pembayaran'   => $mp->id,
                'nomor_voucher'          => $nomor,
                'keterangan'             => null,
                'urutan'                 => 1,
                'tgl_keluar'             => $request->tanggal,
                'id_md_chart_of_account' => $request->id_md_chart_of_account,
            ]);

            DB::commit();
            \App\Models\ActivityLog::record('create', 'Data Multi-Payment berhasil ditambahkan', 'Multi-Payment');

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

            $chartOfAccounts = ChartOfAccount::whereIn('payment_type', ['Kas', 'Bank'])
                ->orderBy('no_account')->get();
            $allChartOfAccounts = ChartOfAccount::orderBy('no_account')->get();
            $releaseTos  = \App\Models\Master\ReleaseTo::orderBy('nama_release')->get();
            $departemens = \App\Models\Master\Departemen::orderBy('nama_dep')->get();
            $branchs     = \App\Models\Master\Branch::orderBy('nama_branch')->get();
            $voucher = $mutasiPembayaran->vouchers()->first();
            return view('data.mutasi-pembayaran.edit', compact(
                'mutasiPembayaran', 'chartOfAccounts', 'allChartOfAccounts',
                'releaseTos', 'departemens', 'branchs', 'voucher'
            ));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Mutasi Pembayaran tidak ditemukan');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_md_chart_of_account' => 'required|exists:a13_md_chart_of_account,id_md_chart_of_account',
            'no_cek'                 => 'nullable|string|max:100',
            'tanggal'                => 'required|date',
            'kurs'                   => 'nullable|numeric|min:0',
            'mata_uang'              => 'nullable|string|max:10',
            'tgl_kurs'               => 'nullable|date',
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

            // Jika ada nomor baru (COA berubah tipe), gunakan nomor yang di-generate
            $updateData = [
                'id_md_chart_of_account' => $request->id_md_chart_of_account,
                'no_cek'                 => $request->no_cek,
                'tanggal'                => $request->tanggal,
                'kurs'                   => $request->kurs ?? 1,
                'mata_uang'              => $request->mata_uang ?? 'IDR',
                'tgl_kurs'               => $request->tgl_kurs ?: null,
                'memo'                   => $request->memo,
            ];
            if ($request->filled('new_nomor')) {
                $updateData['nomor'] = $request->new_nomor;
            }
            $mp->update($updateData);

            // Sync COA ke voucher tunggal
            $voucher = $mp->vouchers()->first();
            if ($voucher) {
                $voucherUpdate = [
                    'id_md_chart_of_account' => $request->id_md_chart_of_account,
                    'tgl_keluar'             => $request->tanggal,
                ];
                if ($request->filled('new_nomor')) {
                    $voucherUpdate['nomor_voucher'] = $request->new_nomor;
                }
                $voucher->update($voucherUpdate);
            }

            DB::commit();
            \App\Models\ActivityLog::record('update', 'Data Multi-Payment berhasil diubah', 'Multi-Payment');

            $responseData = ['success' => true, 'message' => 'Header berhasil diupdate'];
            if ($request->filled('new_nomor')) {
                $responseData['nomor'] = $request->new_nomor;
            }
            if ($request->expectsJson())
                return response()->json($responseData);
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
            foreach ($mp->vouchers as $v) {
                // Reverse balance semua detail di setiap voucher
                foreach ($v->details as $detail) {
                    $this->applyBalanceChange(
                        $v->id,
                        (float) $detail->nilai,
                        (float) $detail->selisih,
                        'reverse'
                    );
                }
                $v->details()->delete();
            }
            $mp->vouchers()->delete();
            $mp->delete();
            DB::commit();
            \App\Models\ActivityLog::record('delete', 'Data Multi-Payment berhasil dihapus', 'Multi-Payment');

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
            $coa = $request->id_md_chart_of_account
                ? ChartOfAccount::find($request->id_md_chart_of_account)
                : null;

            if ($coa?->payment_type === 'Kas') {
                $nomorVoucher = IdGenerator::generateVoucherNoKas();
            } elseif ($coa?->payment_type === 'Bank') {
                $nomorVoucher = IdGenerator::generateVoucherNoBank();
            } else {
                $nomorVoucher = IdGenerator::generateVoucherNo(); // fallback
            }
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

            // Reverse balance untuk setiap detail sebelum dihapus
            foreach ($voucher->details as $detail) {
                $this->applyBalanceChange(
                    $voucher->id,
                    (float) $detail->nilai,
                    (float) $detail->selisih,
                    'reverse'
                );
            }

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
            'jenis'             => 'required|in:tramper,other,contract,general,custom',
            'id_jo_tram'        => 'nullable|exists:b03_jo_tram,id_jo_tram',
            'id_jo_other'       => 'nullable|exists:b05_jo_other,id_jo_other',
            'id_jo_cont'        => 'nullable|exists:b01_jo_cont,id_jo_cont',
            'id_kasbon_tram'    => 'nullable|exists:c03_kasbon_tram,id',
            'id_kasbon_other'   => 'nullable|exists:c05_kasbon_other,id',
            'id_kasbon_cont'    => 'nullable|exists:c01_kasbon_cont,id',
            'id_kasbon_gen'     => 'nullable|exists:c07_kasbon_gen,id',
            'keterangan'        => 'nullable|string|max:255',
            'id_md_chart_of_account_detail' => 'nullable|exists:a13_md_chart_of_account,id_md_chart_of_account',
            'custom_id_md_release'  => 'nullable|exists:a10_md_release_to,id_md_release',
            'custom_id_md_dep'      => 'nullable|exists:a08_md_dep,id_md_dep',
            'custom_id_md_branch'   => 'nullable|exists:a09_md_branch,id_md_branch',
            'custom_nilai'          => 'nullable|numeric|min:0',
            'custom_pj'             => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        $dup = $this->checkKasbonDuplicate($request);
        if ($dup)
            return response()->json(['success' => false, 'message' => $dup], 422);

        DB::beginTransaction();
        try {
            if ($request->jenis === 'custom') {
                $customNilai = (float)($request->custom_nilai ?? 0);
                $customPj    = (float)($request->custom_pj ?? 0);
                $amounts = ['nilai' => $customNilai, 'pj' => $customPj, 'selisih' => $customNilai - $customPj];
            } else {
                $amounts = $this->resolveKasbonAmounts($request->jenis, $request);
            }
            $detail = MutasiPembayaranDetail::create([
                'id_mutasi_voucher'      => $request->id_mutasi_voucher,
                'jenis'                  => $request->jenis,
                'id_jo_tram'             => $request->id_jo_tram,
                'id_jo_other'            => $request->id_jo_other,
                'id_jo_cont'             => $request->id_jo_cont,
                'id_kasbon_tram'         => $request->id_kasbon_tram,
                'id_kasbon_other'        => $request->id_kasbon_other,
                'id_kasbon_cont'         => $request->id_kasbon_cont,
                'id_kasbon_gen'          => $request->id_kasbon_gen,
                'nilai'                  => $amounts['nilai'],
                'pj'                     => $amounts['pj'],
                'selisih'                => $amounts['nilai'] - $amounts['pj'],
                'keterangan'             => $request->keterangan,
                'id_md_chart_of_account' => $request->id_md_chart_of_account_detail ?: null,
                'custom_id_md_release'   => $request->custom_id_md_release ?: null,
                'custom_id_md_dep'       => $request->custom_id_md_dep ?: null,
                'custom_id_md_branch'    => $request->custom_id_md_branch ?: null,
                'custom_nilai'           => $request->jenis === 'custom' ? $amounts['nilai'] : null,
                'custom_pj'              => $request->jenis === 'custom' ? $amounts['pj'] : null,
            ]);

            // Update COA balance
            $this->applyBalanceChange(
                $request->id_mutasi_voucher,
                $amounts['nilai'],
                $amounts['nilai'] - $amounts['pj'],
                'deduct'
            );

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
            'jenis'           => 'sometimes|in:tramper,other,contract,general,custom',
            'id_jo_tram'      => 'nullable|exists:b03_jo_tram,id_jo_tram',
            'id_jo_other'     => 'nullable|exists:b05_jo_other,id_jo_other',
            'id_jo_cont'      => 'nullable|exists:b01_jo_cont,id_jo_cont',
            'id_kasbon_tram'  => 'nullable|exists:c03_kasbon_tram,id',
            'id_kasbon_other' => 'nullable|exists:c05_kasbon_other,id',
            'id_kasbon_cont'  => 'nullable|exists:c01_kasbon_cont,id',
            'id_kasbon_gen'   => 'nullable|exists:c07_kasbon_gen,id',
            'keterangan'      => 'nullable|string|max:255',
            'id_md_chart_of_account_detail' => 'nullable|exists:a13_md_chart_of_account,id_md_chart_of_account',
            'custom_id_md_release'  => 'nullable|exists:a10_md_release_to,id_md_release',
            'custom_id_md_dep'      => 'nullable|exists:a08_md_dep,id_md_dep',
            'custom_id_md_branch'   => 'nullable|exists:a09_md_branch,id_md_branch',
            'custom_nilai'          => 'nullable|numeric|min:0',
            'custom_pj'             => 'nullable|numeric|min:0',
        ]);
        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            $detail = MutasiPembayaranDetail::findOrFail($id);

            // Simpan nilai lama sebelum update
            $oldNilai      = (float) $detail->nilai;
            $oldSelisih    = (float) $detail->selisih;
            $voucherId     = $detail->id_mutasi_voucher;

            $jenis   = $request->jenis ?? $detail->jenis;
            if ($jenis === 'custom') {
                $customNilai = (float)($request->custom_nilai ?? $detail->custom_nilai ?? 0);
                $customPj    = (float)($request->custom_pj ?? $detail->custom_pj ?? 0);
                $amounts = ['nilai' => $customNilai, 'pj' => $customPj, 'selisih' => $customNilai - $customPj];
            } else {
                $amounts = $this->resolveKasbonAmounts($jenis, $request, $detail);
            }

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
                'keterangan'             => $request->keterangan ?? $detail->keterangan,
                'id_md_chart_of_account' => $request->has('id_md_chart_of_account_detail') ? ($request->id_md_chart_of_account_detail ?: null) : $detail->id_md_chart_of_account,
                'custom_id_md_release'   => $request->has('custom_id_md_release') ? ($request->custom_id_md_release ?: null) : $detail->custom_id_md_release,
                'custom_id_md_dep'       => $request->has('custom_id_md_dep') ? ($request->custom_id_md_dep ?: null) : $detail->custom_id_md_dep,
                'custom_id_md_branch'    => $request->has('custom_id_md_branch') ? ($request->custom_id_md_branch ?: null) : $detail->custom_id_md_branch,
                'custom_nilai'           => $jenis === 'custom' ? $amounts['nilai'] : $detail->custom_nilai,
                'custom_pj'              => $jenis === 'custom' ? $amounts['pj']    : $detail->custom_pj,
            ]);

            // Reverse balance lama, apply balance baru
            $this->applyBalanceChange($voucherId, $oldNilai,        $oldSelisih,                          'reverse');
            $this->applyBalanceChange($voucherId, $amounts['nilai'], $amounts['nilai'] - $amounts['pj'],  'deduct');

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Detail berhasil diupdate',
                'data'    => $this->formatDetail($detail->fresh())
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyDetail(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $detail = MutasiPembayaranDetail::findOrFail($id);

            // Reverse balance sebelum hapus
            $this->applyBalanceChange(
                $detail->id_mutasi_voucher,
                (float) $detail->nilai,
                (float) $detail->selisih,
                'reverse'
            );

            $detail->delete();
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

                // Jika kasbon_id diberikan, kembalikan satu kasbon saja (backward compat)
                // Jika tidak, kembalikan SEMUA kasbon general yang tersedia
                $usedGen = MutasiPembayaranDetail::whereNotNull('id_kasbon_gen')
                    ->pluck('id_kasbon_gen');

                $query = KasbonGen::with(['departemen', 'cabang', 'release', 'items.invoice', 'pajakItems']);

                if ($kasbonId) {
                    $query->where('id', $kasbonId);
                } else {
                    $query->whereNotIn('id', $usedGen);
                }

                $kasbons = $query->get();

                $result = $kasbons->map(function ($k) use ($usedGen) {
                    $lpjIds = LpjGenKasbon::where('id_kasbon_gen', $k->id_kasbon_gen)->pluck('id_lpj_gen');

                    $items = $k->items->map(function ($item) use ($lpjIds) {
                        $inv = $item->invoice;
                        $pj  = LpjGenItem::whereIn('id_lpj_gen', $lpjIds)
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

                    $taxItems = $k->pajakItems->map(function ($pajak) {
                        return [
                            'id'           => $pajak->id_kasbon_gen_pajak,
                            'label'        => $pajak->nama_pajak,
                            'kategori'     => 'Tax',
                            'nilai_kasbon' => (float) $pajak->nominal_pajak,
                            'pj'           => 0.0,
                            'selisih'      => (float) $pajak->nominal_pajak,
                            'akun'         => '',
                        ];
                    });

                    $allItems   = $items->concat($taxItems);
                    $totalNilai = (float) $allItems->sum('nilai_kasbon');
                    $totalPj    = (float) $allItems->sum('pj');

                    // sudah_dipakai: cek apakah id kasbon ini ada di detail
                    $sudahDipakai = $usedGen->contains((string) $k->id)
                        || $usedGen->contains($k->id);

                    return [
                        'id_kasbon'     => $k->id,
                        'nomor'         => $k->id_kasbon_gen ?? $k->nomor ?? 'C07-' . $k->id,
                        'nilai'         => $totalNilai,
                        'pj'            => $totalPj,
                        'selisih'       => $totalNilai - $totalPj,
                        'departemen'    => $k->departemen?->nama_dep   ?? '—',
                        'cabang'        => $k->cabang?->nama_branch    ?? '—',
                        'release'       => $k->release?->nama_release  ?? '—',
                        'tgl_kasbon'    => optional($k->tgl_kasbon)->format('d/m/Y')  ?? '',
                        'tgl_release'   => optional($k->tgl_release)->format('d/m/Y') ?? '',
                        'sudah_dipakai' => $sudahDipakai,
                        'items'         => $allItems->values()->toArray(),
                    ];
                });

                return response()->json(['success' => true, 'data' => $result->values()->toArray()]);
            }

            // ── TRAMPER / OTHER / CONTRACT ────────────────────────────────────────
            if (!$joId) {
                return response()->json(['success' => false, 'message' => 'Parameter jo_id wajib diisi'], 422);
            }

            $voucherId = $request->get('voucher_id'); // tambah param dari JS

            $usedTram  = MutasiPembayaranDetail::whereNotNull('id_kasbon_tram')
                ->when($voucherId, fn($q) => $q->where('id_mutasi_voucher', '!=', $voucherId))
                ->pluck('id_kasbon_tram');
            $usedOther = MutasiPembayaranDetail::whereNotNull('id_kasbon_other')
                ->when($voucherId, fn($q) => $q->where('id_mutasi_voucher', '!=', $voucherId))
                ->pluck('id_kasbon_other');
            $usedCont  = MutasiPembayaranDetail::whereNotNull('id_kasbon_cont')
                ->when($voucherId, fn($q) => $q->where('id_mutasi_voucher', '!=', $voucherId))
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
        return KasbonContract::with(['departemen', 'cabang', 'release', 'items.joContractItem.invoice', 'items.joContractPajak'])
            ->where('id_jo_cont', $joId)->get()
            ->map(function ($k) use ($usedIds) {
                $lpjIds = LpjKasbon::where('id_kasbon_cont', $k->id_kasbon_cont)->pluck('id_lpj_cont');
                $items  = $k->items->map(function ($item) use ($lpjIds) {
                    $isTax = ($item->item_type ?? 'item') === 'tax';
                    $pj    = LpjContractItem::whereIn('id_lpj_cont', $lpjIds)
                        ->where('id_kasbon_cont_item', $item->id_kasbon_cont_item)->sum('amount_lpj');

                    if ($isTax) {
                        $pajak = $item->joContractPajak;
                        return [
                            'id'           => $item->id_kasbon_cont_item,
                            'label'        => $pajak?->nama_pajak ?? ('Tax #' . $item->id),
                            'kategori'     => 'Tax',
                            'nilai_kasbon' => (float) $item->nilai_kasbon,
                            'pj'           => (float) $pj,
                            'selisih'      => (float) ($item->nilai_kasbon - $pj),
                            'akun'         => '',
                        ];
                    }

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
                        'nilai_kasbon' => (float) $item->nilai_kasbon,
                        'pj'           => (float) $pj,
                        'selisih'      => (float) ($item->nilai_kasbon - $pj),
                        'akun'         => $coa->map(fn($c) => $c->no_account . ' — ' . $c->account_name)->implode(', '),
                    ];
                });
                return $this->buildKasbonSummaryRow($k, $k->id_kasbon_cont, $items, $usedIds);
            });
    }

    private function kasbonItemSummaryTramper($joId, $usedIds): \Illuminate\Support\Collection
    {
        return KasbonTramper::with(['departemen', 'cabang', 'release', 'items.joTramperPajak'])
            ->where('id_jo_tram', $joId)->get()
            ->map(function ($k) use ($usedIds) {
                // JoTramperItem tidak pakai SoftDeletes, query biasa sudah cukup
                $joItemMap = \App\Models\Data\JoTramperItem::query()
                    ->where('id_jo_tram', $k->id_jo_tram)
                    ->with('invoice')
                    ->get()
                    ->keyBy('id_jo_tram_item');

                $lpjIds = LpjKasbonTramper::where('id_kasbon_tram', $k->id_kasbon_tram)->pluck('id_lpj_tram');
                $items  = $k->items->map(function ($item) use ($lpjIds, $joItemMap) {
                    $isTax = ($item->item_type ?? 'item') === 'tax';
                    $pj    = LpjTramperItem::whereIn('id_lpj_tram', $lpjIds)
                        ->where('id_kasbon_tram_item', $item->id_kasbon_tram_item)->sum('amount_lpj');

                    if ($isTax) {
                        $pajak = $item->joTramperPajak;
                        return [
                            'id'           => $item->id_kasbon_tram_item,
                            'label'        => $pajak?->nama_pajak ?? ('Tax #' . $item->id),
                            'kategori'     => 'Tax',
                            'nilai_kasbon' => (float) $item->nilai_kasbon,
                            'pj'           => (float) $pj,
                            'selisih'      => (float) ($item->nilai_kasbon - $pj),
                            'akun'         => '',
                        ];
                    }

                    $jo    = $joItemMap->get($item->id_jo_tram_item);
                    $inv   = $jo?->invoice;
                    $coa   = ChartOfAccount::whereIn(
                        'id_md_chart_of_account',
                        LpjTramperItem::whereIn('id_lpj_tram', $lpjIds)
                            ->where('id_kasbon_tram_item', $item->id_kasbon_tram_item)
                            ->pluck('id_md_chart_of_account')->unique()->filter()
                    )->get(['no_account', 'account_name']);
                    return [
                        'id'           => $item->id_kasbon_tram_item,
                        'label'        => $inv?->invoice_typ ?? $item->id_jo_tram_item ?? $item->id_kasbon_tram_item,
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
        return KasbonOther::with(['departemen', 'cabang', 'release', 'items.joOtherPajak'])
            ->where('id_jo_other', $joId)->get()
            ->map(function ($k) use ($usedIds) {
                // Load JO other items langsung (withTrashed) untuk menghindari masalah soft-delete
                $joItemMap = \App\Models\Data\JoOtherItem::query()
                    ->where('id_jo_other', $k->id_jo_other)
                    ->with('invoice')
                    ->get()
                    ->keyBy('id_jo_other_item');

                $lpjIds = LpjKasbonOther::where('id_kasbon_other', $k->id_kasbon_other)->pluck('id_lpj_other');
                $items  = $k->items->map(function ($item) use ($lpjIds, $joItemMap) {
                    $isTax = ($item->item_type ?? 'item') === 'tax';
                    $pj    = LpjOtherItem::whereIn('id_lpj_other', $lpjIds)
                        ->where('id_kasbon_other_item', $item->id_kasbon_other_item)->sum('amount_lpj');

                    if ($isTax) {
                        $pajak = $item->joOtherPajak;
                        return [
                            'id'           => $item->id_kasbon_other_item,
                            'label'        => $pajak?->nama_pajak ?? ('Tax #' . $item->id),
                            'kategori'     => 'Tax',
                            'nilai_kasbon' => (float) $item->nilai_kasbon,
                            'pj'           => (float) $pj,
                            'selisih'      => (float) ($item->nilai_kasbon - $pj),
                            'akun'         => '',
                        ];
                    }

                    $jo    = $joItemMap->get($item->id_jo_other_item);
                    $inv   = $jo?->invoice;
                    $coa   = ChartOfAccount::whereIn(
                        'id_md_chart_of_account',
                        LpjOtherItem::whereIn('id_lpj_other', $lpjIds)
                            ->where('id_kasbon_other_item', $item->id_kasbon_other_item)
                            ->pluck('id_md_chart_of_account')->unique()->filter()
                    )->get(['no_account', 'account_name']);
                    return [
                        'id'           => $item->id_kasbon_other_item,
                        'label'        => $inv?->invoice_typ ?? $item->id_jo_other_item ?? $item->id_kasbon_other_item,
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
        return KasbonGen::with(['departemen', 'cabang', 'release', 'items.invoice'])
            ->orderBy('tgl_kasbon', 'desc')
            ->get()
            ->map(function ($k) use ($usedIds) {
                $items = $k->items->map(function ($item) {
                    $pj  = (float) LpjGenItem::where('id_kasbon_gen_item', $item->id_kasbon_gen_item)
                        ->sum('amount_lpj');
                    $inv = $item->invoice;
                    return [
                        'id'           => $item->id_kasbon_gen_item,
                        'label'        => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $item->id_kasbon_gen_item),
                        'kategori'     => $inv?->invoice_ctg ?? '—',
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
            'tramper' => (function () use ($kasbon) {
                // JoTramperItem tidak pakai SoftDeletes, query biasa sudah cukup
                $joItemMap = JoTramperItem::query()
                    ->where('id_jo_tram', $kasbon->id_jo_tram)
                    ->with('invoice')
                    ->get()
                    ->keyBy('id_jo_tram_item');

                $lpjIds = LpjKasbonTramper::where('id_kasbon_tram', $kasbon->id_kasbon_tram)->pluck('id_lpj_tram');

                return KasbonTramperItem::where('id_kasbon_tram', $kasbon->id_kasbon_tram)
                    ->with('joTramperPajak')->get()
                    ->map(function ($item) use ($kasbon, $joItemMap, $lpjIds) {
                        $pj     = LpjTramperItem::whereIn('id_lpj_tram', $lpjIds)
                            ->where('id_kasbon_tram_item', $item->id_kasbon_tram_item)->sum('amount_lpj');
                        $isTax  = ($item->item_type ?? 'item') === 'tax';
                        if ($isTax) {
                            $pajak = $item->joTramperPajak;
                            return [
                                'label'        => $pajak?->nama_pajak ?? ('Tax #' . $item->id),
                                'kategori'     => 'Tax',
                                'nilai_kasbon' => (float) $item->nilai_kasbon,
                                'pj'           => (float) $pj,
                                'selisih'      => (float) ($item->nilai_kasbon - $pj),
                                'akun'         => '',
                            ];
                        }
                        $jo  = $joItemMap->get($item->id_jo_tram_item);
                        $inv = $jo?->invoice;
                        $coa = ChartOfAccount::whereIn(
                            'id_md_chart_of_account',
                            LpjTramperItem::whereIn('id_lpj_tram', $lpjIds)
                                ->where('id_kasbon_tram_item', $item->id_kasbon_tram_item)
                                ->pluck('id_md_chart_of_account')->unique()->filter()
                        )->get(['no_account', 'account_name']);
                        return [
                            'label'        => $inv?->invoice_typ ?? $item->id_jo_tram_item ?? ('Item #' . $item->id),
                            'kategori'     => $inv?->invoice_ctg ?? '—',
                            'nilai_kasbon' => (float) $item->nilai_kasbon,
                            'pj'           => (float) $pj,
                            'selisih'      => (float) ($item->nilai_kasbon - $pj),
                            'akun'         => $coa->map(fn($c) => $c->no_account . ' — ' . $c->account_name)->implode(', '),
                        ];
                    })->toArray();
            })(),

            'other' => (function () use ($kasbon) {
                // Build manual map dari JoOtherItem supaya invoice ter-load dengan benar
                // (sama pola dengan kasbonItemSummaryOther)
                $joItemMap = \App\Models\Data\JoOtherItem::query()
                    ->where('id_jo_other', $kasbon->id_jo_other)
                    ->with('invoice')
                    ->get()
                    ->keyBy('id_jo_other_item');

                $lpjIds = LpjKasbonOther::where('id_kasbon_other', $kasbon->id_kasbon_other)->pluck('id_lpj_other');

                return KasbonOtherItem::where('id_kasbon_other', $kasbon->id_kasbon_other)
                    ->with('joOtherPajak')->get()
                    ->map(function ($item) use ($kasbon, $joItemMap, $lpjIds) {
                        $pj     = LpjOtherItem::whereIn('id_lpj_other', $lpjIds)
                            ->where('id_kasbon_other_item', $item->id_kasbon_other_item)->sum('amount_lpj');
                        $isTax  = ($item->item_type ?? 'item') === 'tax';
                        if ($isTax) {
                            $pajak = $item->joOtherPajak;
                            return [
                                'label'        => $pajak?->nama_pajak ?? ('Tax #' . $item->id),
                                'kategori'     => 'Tax',
                                'nilai_kasbon' => (float) $item->nilai_kasbon,
                                'pj'           => (float) $pj,
                                'selisih'      => (float) ($item->nilai_kasbon - $pj),
                                'akun'         => '',
                            ];
                        }
                        $jo  = $joItemMap->get($item->id_jo_other_item);
                        $inv = $jo?->invoice;
                        $coa = ChartOfAccount::whereIn(
                            'id_md_chart_of_account',
                            LpjOtherItem::whereIn('id_lpj_other', $lpjIds)
                                ->where('id_kasbon_other_item', $item->id_kasbon_other_item)
                                ->pluck('id_md_chart_of_account')->unique()->filter()
                        )->get(['no_account', 'account_name']);
                        return [
                            'label'        => $inv?->invoice_typ ?? $item->id_jo_other_item ?? ('Item #' . $item->id),
                            'kategori'     => $inv?->invoice_ctg ?? '—',
                            'nilai_kasbon' => (float) $item->nilai_kasbon,
                            'pj'           => (float) $pj,
                            'selisih'      => (float) ($item->nilai_kasbon - $pj),
                            'akun'         => $coa->map(fn($c) => $c->no_account . ' — ' . $c->account_name)->implode(', '),
                        ];
                    })->toArray();
            })(),

            'contract' => KasbonContractItem::with(['joContractItem.invoice', 'joContractPajak'])
                ->where('id_kasbon_cont', $kasbon->id_kasbon_cont)->get()
                ->map(function ($item) use ($kasbon) {
                    $lpjIds = LpjKasbon::where('id_kasbon_cont', $kasbon->id_kasbon_cont)->pluck('id_lpj_cont');
                    $pj     = LpjContractItem::whereIn('id_lpj_cont', $lpjIds)
                        ->where('id_kasbon_cont_item', $item->id_kasbon_cont_item)->sum('amount_lpj');
                    $isTax  = ($item->item_type ?? 'item') === 'tax';

                    if ($isTax) {
                        $pajak = $item->joContractPajak;
                        return [
                            'label'        => $pajak?->nama_pajak ?? ('Tax #' . $item->id),
                            'kategori'     => 'Tax',
                            'nilai_kasbon' => (float) $item->nilai_kasbon,
                            'pj'           => (float) $pj,
                            'selisih'      => (float) ($item->nilai_kasbon - $pj),
                            'akun'         => '',
                        ];
                    }

                    $inv = $item->joContractItem?->invoice;
                    $coa = ChartOfAccount::whereIn(
                        'id_md_chart_of_account',
                        LpjContractItem::whereIn('id_lpj_cont', $lpjIds)
                            ->where('id_kasbon_cont_item', $item->id_kasbon_cont_item)
                            ->pluck('id_md_chart_of_account')->unique()->filter()
                    )->get(['no_account', 'account_name']);
                    return [
                        'label'        => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $item->id),
                        'kategori'     => $inv?->invoice_ctg ?? '—',
                        'nilai_kasbon' => (float) $item->nilai_kasbon,
                        'pj'           => (float) $pj,
                        'selisih'      => (float) ($item->nilai_kasbon - $pj),
                        'akun'         => $coa->map(fn($c) => $c->no_account . ' — ' . $c->account_name)->implode(', '),
                    ];
                })->toArray(),

            'general' => (function () use ($kasbon) {
                    $lpjIds = LpjGenKasbon::where('id_kasbon_gen', $kasbon->id_kasbon_gen)->pluck('id_lpj_gen');

                    $regularItems = KasbonGenItem::with('invoice')
                        ->where('id_kasbon_gen', $kasbon->id_kasbon_gen)->get()
                        ->map(function ($item) use ($lpjIds) {
                            $inv = $item->invoice;
                            $pj  = LpjGenItem::whereIn('id_lpj_gen', $lpjIds)
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
                        });

                    $taxItems = KasbonGenPajak::where('id_kasbon_gen', $kasbon->id_kasbon_gen)->get()
                        ->map(function ($pajak) {
                            return [
                                'label'        => $pajak->nama_pajak,
                                'kategori'     => 'Tax',
                                'nilai_kasbon' => (float) $pajak->nominal_pajak,
                                'pj'           => 0.0,
                                'selisih'      => (float) $pajak->nominal_pajak,
                                'akun'         => '',
                            ];
                        });

                    return $regularItems->concat($taxItems)->values()->toArray();
                })(),

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
            'nilai'             => $detail->jenis === 'custom' ? (float)($detail->custom_nilai ?? 0) : (float)($resolvedInfo['nilai']   ?? $detail->nilai),
            'pj'                => $detail->jenis === 'custom' ? (float)($detail->custom_pj    ?? 0) : (float)($resolvedInfo['pj']      ?? $detail->pj),
            'selisih'           => $detail->jenis === 'custom' ? (float)(($detail->custom_nilai ?? 0) - ($detail->custom_pj ?? 0)) : (float)($resolvedInfo['selisih'] ?? $detail->selisih),
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
            'id_md_chart_of_account_detail' => $detail->id_md_chart_of_account,
            'coa_detail'        => $detail->id_md_chart_of_account
                ? (function() use ($detail) {
                    $c = \App\Models\Master\ChartOfAccount::find($detail->id_md_chart_of_account);
                    return $c ? ['id' => $c->id_md_chart_of_account, 'no' => $c->no_account, 'nama' => $c->account_name] : null;
                })()
                : null,
            'custom_id_md_release' => $detail->custom_id_md_release,
            'custom_release_nama'  => $detail->customRelease ? $detail->customRelease->nama_release : null,
            'custom_id_md_dep'     => $detail->custom_id_md_dep,
            'custom_dep_nama'      => $detail->customDepartemen ? $detail->customDepartemen->nama_dep : null,
            'custom_id_md_branch'  => $detail->custom_id_md_branch,
            'custom_branch_nama'   => $detail->customCabang ? $detail->customCabang->nama_branch : null,
            'custom_nilai'         => $detail->custom_nilai !== null ? (float)$detail->custom_nilai : null,
            'custom_pj'            => $detail->custom_pj !== null ? (float)$detail->custom_pj : null,
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
                    'nilai'        => $d->jenis === 'custom' ? (float)($d->custom_nilai ?? 0) : (float) ($d->nilai   ?? $info['nilai']   ?? 0),
                    'pj'           => $d->jenis === 'custom' ? (float)($d->custom_pj    ?? 0) : (float) ($d->pj      ?? $info['pj']      ?? 0),
                    'selisih'      => $d->jenis === 'custom' ? (float)(($d->custom_nilai ?? 0) - ($d->custom_pj ?? 0)) : (float) ($d->selisih ?? $info['selisih'] ?? 0),
                    'keterangan'   => $d->keterangan,
                    'departemen'   => $info['departemen'] ?? null,
                    'cabang'       => $info['cabang']     ?? null,
                    'release'      => $info['release']    ?? null,
                    'tgl_kasbon'   => $info['tgl_kasbon']  ?? null,
                    'tgl_release'  => $info['tgl_release'] ?? null,
                    'akun'         => $akun,
                    'items'        => $items,
                    'id_md_chart_of_account_detail' => $d->id_md_chart_of_account,
                    'coa_detail'   => $d->id_md_chart_of_account
                        ? (function() use ($d) {
                            $c = ChartOfAccount::find($d->id_md_chart_of_account);
                            return $c ? ['id' => $c->id_md_chart_of_account, 'no' => $c->no_account, 'nama' => $c->account_name] : null;
                        })()
                        : null,
                    'custom_id_md_release' => $d->custom_id_md_release,
                    'custom_release_nama'  => $d->customRelease ? $d->customRelease->nama_release : null,
                    'custom_id_md_dep'     => $d->custom_id_md_dep,
                    'custom_dep_nama'      => $d->customDepartemen ? $d->customDepartemen->nama_dep : null,
                    'custom_id_md_branch'  => $d->custom_id_md_branch,
                    'custom_branch_nama'   => $d->customCabang ? $d->customCabang->nama_branch : null,
                    'custom_nilai'         => $d->custom_nilai !== null ? (float)$d->custom_nilai : null,
                    'custom_pj'            => $d->custom_pj !== null ? (float)$d->custom_pj : null,
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

    public function exportMutasiPdf(int $id)
    {
        $mutasi     = \App\Models\Data\MutasiPembayaran::with(['coa', 'vouchers.details.customRelease'])->findOrFail($id);
        $allDetails = $mutasi->vouchers->flatMap(fn($v) => $v->details);

        // ── Step 1: group all details by JO (jenis||jo_id) ───────────────────
        $joGroups = [];

        foreach ($allDetails as $detail) {
            $jenis = $detail->jenis;

            switch ($jenis) {
                case 'tramper':
                    if (!$detail->id_kasbon_tram) continue 2;
                    $kasbon      = KasbonTramper::with('release', 'joTramper')->find($detail->id_kasbon_tram);
                    if (!$kasbon) continue 2;
                    $joId        = $kasbon->id_jo_tram;
                    $joNomor     = $kasbon->joTramper?->no_jo_tram ?? null;
                    $releaseName = $kasbon->release->nama_release ?? null;
                    $kasbonKey   = 'tram_' . $kasbon->id_kasbon_tram;
                    break;

                case 'other':
                    if (!$detail->id_kasbon_other) continue 2;
                    $kasbon      = KasbonOther::with('release', 'joOther')->find($detail->id_kasbon_other);
                    if (!$kasbon) continue 2;
                    $joId        = $kasbon->id_jo_other;
                    $joNomor     = $kasbon->joOther?->no_jo_other ?? null;
                    $releaseName = $kasbon->release->nama_release ?? null;
                    $kasbonKey   = 'other_' . $kasbon->id_kasbon_other;
                    break;

                case 'contract':
                    if (!$detail->id_kasbon_cont) continue 2;
                    $kasbon      = KasbonContract::with('release', 'joContract')->find($detail->id_kasbon_cont);
                    if (!$kasbon) continue 2;
                    $joId        = $kasbon->id_jo_cont;
                    $joNomor     = $kasbon->joContract?->no_jo_cont ?? null;
                    $releaseName = $kasbon->release->nama_release ?? null;
                    $kasbonKey   = 'cont_' . $kasbon->id_kasbon_cont;
                    break;

                case 'general':
                    if (!$detail->id_kasbon_gen) continue 2;
                    $kasbon      = KasbonGen::with('release')->find($detail->id_kasbon_gen);
                    if (!$kasbon) continue 2;
                    $joId        = null;
                    $joNomor     = null;
                    $releaseName = $kasbon->release->nama_release ?? null;
                    $kasbonKey   = 'gen_' . $kasbon->id;  // pakai integer PK, bukan id_kasbon_gen (string)
                    break;

                case 'custom':
                    // custom type: tidak ada kasbon, langsung pakai nilai dari detail
                    $joId        = null;
                    $joNomor     = null;
                    $releaseName = $detail->customRelease?->nama_release ?? null;
                    $kasbonKey   = 'custom_' . $detail->id;
                    break;

                default:
                    continue 2;
            }

            $groupKey = $jenis . '||' . ($joId ?? $kasbonKey);

            if (!isset($joGroups[$groupKey])) {
                $joGroups[$groupKey] = [
                    'jenis'      => $jenis,
                    'jo_id'      => $joId,
                    'jo_nomor'   => $joNomor,
                    'kasbon_ids' => [],
                    'releases'   => [],
                ];
            }
            if (!in_array($kasbonKey, $joGroups[$groupKey]['kasbon_ids'], true)) {
                $joGroups[$groupKey]['kasbon_ids'][] = $kasbonKey;
            }
            if ($releaseName && !in_array($releaseName, $joGroups[$groupKey]['releases'], true)) {
                $joGroups[$groupKey]['releases'][] = $releaseName;
            }
        }

        // ── Step 2: build PDF rows — items from JO master, amounts summed across kasbons ──
        $pdfRows = [];

        foreach ($joGroups as $group) {
            $jenis     = $group['jenis'];
            $joId      = $group['jo_id'];
            $joNomor   = $group['jo_nomor'];
            $paidTo    = implode(', ', $group['releases']);
            $kasbonIds = array_map(fn($k) => (int) substr($k, strpos($k, '_') + 1), $group['kasbon_ids']);

            if ($jenis === 'general') {
                $items = [];
                foreach ($kasbonIds as $kasbonId) {
                    $kas = KasbonGen::with('items.invoice')->find($kasbonId);
                    if (!$kas) continue;
                    foreach ($kas->items as $ki) {
                        $amount = (float) ($ki->nilai_kasbon ?? 0);
                        if ($amount <= 0) continue;
                        $inv   = $ki->invoice;
                        $cat   = $inv?->invoice_ctg ?? $ki->kategori ?? 'General';
                        $label = $inv?->invoice_typ ?? $inv?->id_md_invoice ?? $ki->keterangan ?? $ki->description ?? ('Item #' . ($ki->id_kasbon_gen_item ?? $ki->id));
                        $found = false;
                        foreach ($items as &$ex) {
                            if ($ex['category'] === $cat && $ex['label'] === $label) { $ex['amount'] += $amount; $found = true; break; }
                        }
                        unset($ex);
                        if (!$found) $items[] = ['category' => $cat, 'label' => $label, 'amount' => $amount];
                    }
                }
                // pajak for general kasbon in separate table c09_kasbon_gen_pajak
                foreach ($kasbonIds as $kasbonId) {
                    $kas = KasbonGen::find($kasbonId);
                    if (!$kas) continue;
                    foreach ($kas->pajakItems as $kp) {
                        $amount = (float) ($kp->nominal_pajak ?? 0);
                        if ($amount <= 0) continue;
                        $label = $kp->nama_pajak ?? ('Pajak #' . $kp->id_kasbon_gen_pajak);
                        $found = false;
                        foreach ($items as &$ex) {
                            if ($ex['category'] === 'Tax' && $ex['label'] === $label) { $ex['amount'] += $amount; $found = true; break; }
                        }
                        unset($ex);
                        if (!$found) $items[] = ['category' => 'Tax', 'label' => $label, 'amount' => $amount];
                    }
                }
                if (!empty($items)) {
                    $pdfRows[] = ['paid_to' => $paidTo, 'jo_nomor' => null, 'items' => $items];
                }
                continue;
            }

            if ($jenis === 'tramper') {
                $jo = JoTramper::with('items.invoice', 'pajakItems')->find($joId);
                if (!$jo) continue;
                $sums      = [];   // non-pajak: keyed by id_jo_tram_item
                $pajSums   = [];   // pajak:     keyed by id_jo_tram_pajak
                // id_kasbon_tram adalah string biz key (CA/xxxx/xxxxx), bukan integer
                $tramBizKeys = array_map(fn($k) => substr($k, strpos($k, '_') + 1), $group['kasbon_ids']);
                foreach (KasbonTramperItem::whereIn('id_kasbon_tram', $tramBizKeys)->get() as $ki) {
                    $amount = (float) ($ki->nilai_kasbon ?? 0);
                    if ($amount <= 0) continue;
                    if ($ki->item_type === 'tax') {
                        if ($ki->id_jo_tram_pajak) {
                            $pajSums[$ki->id_jo_tram_pajak] = ($pajSums[$ki->id_jo_tram_pajak] ?? 0) + $amount;
                        } else {
                            // pajak tanpa link JO → masukkan sebagai fallback tax
                            $pajFallbackTram[] = [
                                'label'  => $ki->joTramperPajak?->nama_pajak ?? 'Tax',
                                'amount' => $amount,
                            ];
                        }
                    } else {
                        if (!$ki->id_jo_tram_item) continue;
                        $sums[$ki->id_jo_tram_item] = ($sums[$ki->id_jo_tram_item] ?? 0) + $amount;
                    }
                }
                $items = [];
                foreach ($jo->items as $joItem) {
                    $amount = $sums[$joItem->id_jo_tram_item] ?? 0;
                    if ($amount <= 0) continue;
                    $inv     = $joItem->invoice;
                    $items[] = [
                        'category' => $inv?->invoice_ctg ?? 'General',
                        'label'    => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $joItem->id_jo_tram_item),
                        'amount'   => $amount,
                    ];
                }
                foreach ($jo->pajakItems as $pj) {
                    $amount = $pajSums[$pj->id_jo_tram_pajak] ?? 0;
                    if ($amount <= 0) continue;
                    $items[] = [
                        'category' => 'Tax',
                        'label'    => $pj->nama_pajak ?? ('Pajak #' . $pj->id_jo_tram_pajak),
                        'amount'   => $amount,
                    ];
                }
                // fallback: pajak kasbon tanpa link ke JO pajak
                foreach ($pajFallbackTram ?? [] as $pf) {
                    $items[] = ['category' => 'Tax', 'label' => $pf['label'], 'amount' => $pf['amount']];
                }

            } elseif ($jenis === 'other') {
                $jo = JoOther::with('items.invoice', 'pajakItems')->find($joId);
                if (!$jo) continue;
                $sums    = [];
                $pajSums = [];
                // id_kasbon_other adalah string biz key (CA/xxxx/xxxxx), bukan integer
                $otherBizKeys = array_map(fn($k) => substr($k, strpos($k, '_') + 1), $group['kasbon_ids']);
                foreach (KasbonOtherItem::whereIn('id_kasbon_other', $otherBizKeys)->get() as $ki) {
                    $amount = (float) ($ki->nilai_kasbon ?? 0);
                    if ($amount <= 0) continue;
                    if ($ki->item_type === 'tax') {
                        if ($ki->id_jo_other_pajak) {
                            $pajSums[$ki->id_jo_other_pajak] = ($pajSums[$ki->id_jo_other_pajak] ?? 0) + $amount;
                        } else {
                            // pajak tanpa link JO → masukkan sebagai fallback tax
                            $pajFallbackOther[] = [
                                'label'  => $ki->joOtherPajak?->nama_pajak ?? 'Tax',
                                'amount' => $amount,
                            ];
                        }
                    } else {
                        if (!$ki->id_jo_other_item) continue;
                        $sums[$ki->id_jo_other_item] = ($sums[$ki->id_jo_other_item] ?? 0) + $amount;
                    }
                }
                $items = [];
                foreach ($jo->items as $joItem) {
                    $amount = $sums[$joItem->id_jo_other_item] ?? 0;
                    if ($amount <= 0) continue;
                    $inv     = $joItem->invoice;
                    $items[] = [
                        'category' => $inv?->invoice_ctg ?? 'General',
                        'label'    => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $joItem->id_jo_other_item),
                        'amount'   => $amount,
                    ];
                }
                foreach ($jo->pajakItems as $pj) {
                    $amount = $pajSums[$pj->id_jo_other_pajak] ?? 0;
                    if ($amount <= 0) continue;
                    $items[] = [
                        'category' => 'Tax',
                        'label'    => $pj->nama_pajak ?? ('Pajak #' . $pj->id_jo_other_pajak),
                        'amount'   => $amount,
                    ];
                }
                // fallback: pajak kasbon tanpa link ke JO pajak
                foreach ($pajFallbackOther ?? [] as $pf) {
                    $items[] = ['category' => 'Tax', 'label' => $pf['label'], 'amount' => $pf['amount']];
                }

            } elseif ($jenis === 'contract') {
                $jo = JoContract::with('items.invoice', 'pajakItems')->find($joId);
                if (!$jo) continue;
                $sums    = [];
                $pajSums = [];
                foreach (KasbonContractItem::whereIn('id_kasbon_cont', $kasbonIds)->get() as $ki) {
                    $amount = (float) ($ki->nilai_kasbon ?? 0);
                    if ($amount <= 0) continue;
                    if ($ki->item_type === 'tax') {
                        // Tax item disimpan di KasbonContractItem (item_type='tax') dgn id_jo_cont_pajak
                        if ($ki->id_jo_cont_pajak) {
                            $pajSums[$ki->id_jo_cont_pajak] = ($pajSums[$ki->id_jo_cont_pajak] ?? 0) + $amount;
                        } else {
                            $pajFallbackCont[] = ['label' => 'Tax', 'amount' => $amount];
                        }
                        continue;
                    }
                    if ($ki->item_type === 'pajak') continue;
                    if (!$ki->id_jo_cont_item) continue;
                    $sums[$ki->id_jo_cont_item] = ($sums[$ki->id_jo_cont_item] ?? 0) + $amount;
                }
                // pajak contract dari tabel terpisah c03_kasbon_cont_pajak
                // gunakan string business key (bukan int) agar query benar
                $contBizKeys = array_map(fn($k) => substr($k, strpos($k, '_') + 1), $group['kasbon_ids']);
                foreach (KasbonContractPajak::whereIn('id_kasbon_cont', $contBizKeys)->get() as $kp) {
                    $amount = (float) ($kp->nilai_kasbon_pajak ?? 0);
                    if ($amount <= 0) continue;
                    if ($kp->id_jo_cont_pajak) {
                        $pajSums[$kp->id_jo_cont_pajak] = ($pajSums[$kp->id_jo_cont_pajak] ?? 0) + $amount;
                    } else {
                        // pajak tanpa link JO → fallback
                        $pajFallbackCont[] = [
                            'label'  => $kp->nama_pajak ?? 'Tax',
                            'amount' => $amount,
                        ];
                    }
                }
                $items = [];
                foreach ($jo->items as $joItem) {
                    $amount = $sums[$joItem->id_jo_cont_item] ?? 0;
                    if ($amount <= 0) continue;
                    $inv     = $joItem->invoice;
                    $items[] = [
                        'category' => $inv?->invoice_ctg ?? 'General',
                        'label'    => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $joItem->id_jo_cont_item),
                        'amount'   => $amount,
                    ];
                }
                foreach ($jo->pajakItems as $pj) {
                    $amount = $pajSums[$pj->id_jo_cont_pajak] ?? 0;
                    if ($amount <= 0) continue;
                    $items[] = [
                        'category' => 'Tax',
                        'label'    => $pj->nama_pajak ?? ('Pajak #' . $pj->id_jo_cont_pajak),
                        'amount'   => $amount,
                    ];
                }
                // fallback: pajak kasbon tanpa link ke JO pajak
                foreach ($pajFallbackCont ?? [] as $pf) {
                    $items[] = ['category' => 'Tax', 'label' => $pf['label'], 'amount' => $pf['amount']];
                }

            } elseif ($jenis === 'custom') {
                // custom detail: nilai dan pajak langsung dari kolom custom_nilai / custom_pj
                // Ambil detail original berdasarkan kasbonKey
                $detailId = (int) substr(array_values($group['kasbon_ids'])[0] ?? '0', strpos(array_values($group['kasbon_ids'])[0] ?? '0', '_') + 1);
                $origDetail = \App\Models\Data\MutasiPembayaranDetail::find($detailId);
                if (!$origDetail) {
                    continue;
                }
                $customNilai = (float) ($origDetail->custom_nilai ?? $origDetail->nilai ?? 0);
                $customPj    = (float) ($origDetail->custom_pj ?? 0);
                $customLabel = $origDetail->keterangan ?? 'Custom Item';
                $customCat   = 'General';
                $items = [];
                if ($customNilai > 0) {
                    $items[] = ['category' => $customCat, 'label' => $customLabel, 'amount' => $customNilai];
                }
                if ($customPj > 0) {
                    $items[] = ['category' => 'Tax', 'label' => 'Tax (' . $customLabel . ')', 'amount' => $customPj];
                }

            } else {
                continue;
            }

            if (!empty($items)) {
                $pdfRows[] = ['paid_to' => $paidTo, 'jo_nomor' => $joNomor, 'items' => $items];
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'data.mutasi-pembayaran.mutasi-pdf',
            ['mutasi' => $mutasi, 'pdfRows' => $pdfRows]
        )->setPaper('a4', 'portrait');

        $filename = 'cash-out-' . str_replace(['/', '\\'], '-', $mutasi->nomor ?? $id) . '.pdf';
        return $pdf->stream($filename);
    }


    public function exportVoucherPdf($voucherId)
    {
        $voucher = MutasiPembayaranVoucher::with(['details', 'coa'])->findOrFail($voucherId);
        $mutasi  = \App\Models\Data\MutasiPembayaran::with('coa')
            ->find($voucher->id_mutasi_pembayaran);
        $voucher->setRelation('mutasi', $mutasi);

        // ── Step 1: group voucher details by JO (jenis||jo_id) ───────────────
        // Multiple kasbons on the same JO are merged into ONE row.
        $joGroups = [];

        foreach ($voucher->details as $detail) {
            $jenis = $detail->jenis;

            switch ($jenis) {
                case 'tramper':
                    if (!$detail->id_kasbon_tram) continue 2;
                    $kasbon      = KasbonTramper::with('release', 'joTramper')->find($detail->id_kasbon_tram);
                    if (!$kasbon) continue 2;
                    $joId        = $kasbon->id_jo_tram;
                    $joNomor     = $kasbon->joTramper?->no_jo_tram ?? null;
                    $releaseName = $kasbon->release->nama_release ?? null;
                    $kasbonKey   = 'tram_' . $kasbon->id_kasbon_tram;
                    break;

                case 'other':
                    if (!$detail->id_kasbon_other) continue 2;
                    $kasbon      = KasbonOther::with('release', 'joOther')->find($detail->id_kasbon_other);
                    if (!$kasbon) continue 2;
                    $joId        = $kasbon->id_jo_other;
                    $joNomor     = $kasbon->joOther?->no_jo_other ?? null;
                    $releaseName = $kasbon->release->nama_release ?? null;
                    $kasbonKey   = 'other_' . $kasbon->id_kasbon_other;
                    break;

                case 'contract':
                    if (!$detail->id_kasbon_cont) continue 2;
                    $kasbon      = KasbonContract::with('release', 'joContract')->find($detail->id_kasbon_cont);
                    if (!$kasbon) continue 2;
                    $joId        = $kasbon->id_jo_cont;
                    $joNomor     = $kasbon->joContract?->no_jo_cont ?? null;
                    $releaseName = $kasbon->release->nama_release ?? null;
                    $kasbonKey   = 'cont_' . $kasbon->id_kasbon_cont;
                    break;

                case 'general':
                    if (!$detail->id_kasbon_gen) continue 2;
                    $kasbon      = KasbonGen::with('release')->find($detail->id_kasbon_gen);
                    if (!$kasbon) continue 2;
                    $joId        = null;
                    $joNomor     = null;
                    $releaseName = $kasbon->release->nama_release ?? null;
                    $kasbonKey   = 'gen_' . $kasbon->id;  // pakai integer PK
                    break;

                default:
                    continue 2;
            }

            // General: each kasbon is its own group; others: group by JO
            $groupKey = $jenis . '||' . ($joId ?? $kasbonKey);

            if (!isset($joGroups[$groupKey])) {
                $joGroups[$groupKey] = [
                    'jenis'      => $jenis,
                    'jo_id'      => $joId,
                    'jo_nomor'   => $joNomor,
                    'kasbon_ids' => [],
                    'releases'   => [],
                ];
            }
            if (!in_array($kasbonKey, $joGroups[$groupKey]['kasbon_ids'], true)) {
                $joGroups[$groupKey]['kasbon_ids'][] = $kasbonKey;
            }
            if ($releaseName && !in_array($releaseName, $joGroups[$groupKey]['releases'], true)) {
                $joGroups[$groupKey]['releases'][] = $releaseName;
            }
        }

        // ── Step 2: build PDF rows — items come from JO master, amounts summed across kasbons ──
        $pdfRows = [];

        foreach ($joGroups as $group) {
            $jenis   = $group['jenis'];
            $joId    = $group['jo_id'];
            $joNomor = $group['jo_nomor'];
            $paidTo  = implode(', ', $group['releases']);

            // Extract integer IDs from prefixed keys (e.g. "tram_42" → 42)
            $kasbonIds = array_map(fn($k) => (int) substr($k, strpos($k, '_') + 1), $group['kasbon_ids']);

            if ($jenis === 'general') {
                $items = [];
                foreach ($kasbonIds as $kasbonId) {
                    $kas = KasbonGen::with('items.invoice')->find($kasbonId);
                    if (!$kas) continue;
                    foreach ($kas->items as $ki) {
                        $amount = (float) ($ki->nilai_kasbon ?? 0);
                        if ($amount <= 0) continue;
                        $inv   = $ki->invoice;
                        $cat   = $inv?->invoice_ctg ?? $ki->kategori ?? 'General';
                        $label = $inv?->invoice_typ ?? $inv?->id_md_invoice ?? $ki->keterangan ?? $ki->description ?? ('Item #' . ($ki->id_kasbon_gen_item ?? $ki->id));
                        $found = false;
                        foreach ($items as &$ex) {
                            if ($ex['category'] === $cat && $ex['label'] === $label) { $ex['amount'] += $amount; $found = true; break; }
                        }
                        unset($ex);
                        if (!$found) $items[] = ['category' => $cat, 'label' => $label, 'amount' => $amount];
                    }
                }
                // pajak for general kasbon in separate table c09_kasbon_gen_pajak
                foreach ($kasbonIds as $kasbonId) {
                    $kas = KasbonGen::find($kasbonId);
                    if (!$kas) continue;
                    foreach ($kas->pajakItems as $kp) {
                        $amount = (float) ($kp->nominal_pajak ?? 0);
                        if ($amount <= 0) continue;
                        $label = $kp->nama_pajak ?? ('Pajak #' . $kp->id_kasbon_gen_pajak);
                        $found = false;
                        foreach ($items as &$ex) {
                            if ($ex['category'] === 'Tax' && $ex['label'] === $label) { $ex['amount'] += $amount; $found = true; break; }
                        }
                        unset($ex);
                        if (!$found) $items[] = ['category' => 'Tax', 'label' => $label, 'amount' => $amount];
                    }
                }
                if (!empty($items)) {
                    $pdfRows[] = ['paid_to' => $paidTo, 'jo_nomor' => null, 'items' => $items];
                }
                continue;
            }

            // tramper / other / contract: items come from JO master
            if ($jenis === 'tramper') {
                $jo = JoTramper::with('items.invoice', 'pajakItems')->find($joId);
                if (!$jo) continue;
                $sums    = [];
                $pajSums = [];
                // id_kasbon_tram adalah string biz key (CA/xxxx/xxxxx), bukan integer
                $tramBizKeys = array_map(fn($k) => substr($k, strpos($k, '_') + 1), $group['kasbon_ids']);
                foreach (KasbonTramperItem::whereIn('id_kasbon_tram', $tramBizKeys)->get() as $ki) {
                    $amount = (float) ($ki->nilai_kasbon ?? 0);
                    if ($amount <= 0) continue;
                    if ($ki->item_type === 'tax') {
                        if (!$ki->id_jo_tram_pajak) continue;
                        $pajSums[$ki->id_jo_tram_pajak] = ($pajSums[$ki->id_jo_tram_pajak] ?? 0) + $amount;
                    } else {
                        if (!$ki->id_jo_tram_item) continue;
                        $sums[$ki->id_jo_tram_item] = ($sums[$ki->id_jo_tram_item] ?? 0) + $amount;
                    }
                }
                $items = [];
                foreach ($jo->items as $joItem) {
                    $amount = $sums[$joItem->id_jo_tram_item] ?? 0;
                    if ($amount <= 0) continue;
                    $inv   = $joItem->invoice;
                    $items[] = [
                        'category' => $inv?->invoice_ctg ?? 'General',
                        'label'    => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $joItem->id_jo_tram_item),
                        'amount'   => $amount,
                    ];
                }
                foreach ($jo->pajakItems as $pj) {
                    $amount = $pajSums[$pj->id_jo_tram_pajak] ?? 0;
                    if ($amount <= 0) continue;
                    $items[] = ['category' => 'Tax', 'label' => $pj->nama_pajak ?? ('Pajak #' . $pj->id_jo_tram_pajak), 'amount' => $amount];
                }

            } elseif ($jenis === 'other') {
                $jo = JoOther::with('items.invoice', 'pajakItems')->find($joId);
                if (!$jo) continue;
                $sums    = [];
                $pajSums = [];
                // id_kasbon_other adalah string biz key (CA/xxxx/xxxxx), bukan integer
                $otherBizKeys = array_map(fn($k) => substr($k, strpos($k, '_') + 1), $group['kasbon_ids']);
                foreach (KasbonOtherItem::whereIn('id_kasbon_other', $otherBizKeys)->get() as $ki) {
                    $amount = (float) ($ki->nilai_kasbon ?? 0);
                    if ($amount <= 0) continue;
                    if ($ki->item_type === 'tax') {
                        if (!$ki->id_jo_other_pajak) continue;
                        $pajSums[$ki->id_jo_other_pajak] = ($pajSums[$ki->id_jo_other_pajak] ?? 0) + $amount;
                    } else {
                        if (!$ki->id_jo_other_item) continue;
                        $sums[$ki->id_jo_other_item] = ($sums[$ki->id_jo_other_item] ?? 0) + $amount;
                    }
                }
                $items = [];
                foreach ($jo->items as $joItem) {
                    $amount = $sums[$joItem->id_jo_other_item] ?? 0;
                    if ($amount <= 0) continue;
                    $inv   = $joItem->invoice;
                    $items[] = [
                        'category' => $inv?->invoice_ctg ?? 'General',
                        'label'    => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $joItem->id_jo_other_item),
                        'amount'   => $amount,
                    ];
                }
                foreach ($jo->pajakItems as $pj) {
                    $amount = $pajSums[$pj->id_jo_other_pajak] ?? 0;
                    if ($amount <= 0) continue;
                    $items[] = ['category' => 'Tax', 'label' => $pj->nama_pajak ?? ('Pajak #' . $pj->id_jo_other_pajak), 'amount' => $amount];
                }

            } elseif ($jenis === 'contract') {
                $jo = JoContract::with('items.invoice', 'pajakItems')->find($joId);
                if (!$jo) continue;
                $sums    = [];
                $pajSums = [];
                foreach (KasbonContractItem::whereIn('id_kasbon_cont', $kasbonIds)->get() as $ki) {
                    $amount = (float) ($ki->nilai_kasbon ?? 0);
                    if ($amount <= 0 || $ki->item_type === 'pajak') continue;
                    if (!$ki->id_jo_cont_item) continue;
                    $sums[$ki->id_jo_cont_item] = ($sums[$ki->id_jo_cont_item] ?? 0) + $amount;
                }
                // pajak contract stored in separate table c03_kasbon_cont_pajak
                foreach (KasbonContractPajak::whereIn('id_kasbon_cont', $kasbonIds)->get() as $kp) {
                    $amount = (float) ($kp->nilai_kasbon_pajak ?? 0);
                    if ($amount <= 0 || !$kp->id_jo_cont_pajak) continue;
                    $pajSums[$kp->id_jo_cont_pajak] = ($pajSums[$kp->id_jo_cont_pajak] ?? 0) + $amount;
                }
                $items = [];
                foreach ($jo->items as $joItem) {
                    $amount = $sums[$joItem->id_jo_cont_item] ?? 0;
                    if ($amount <= 0) continue;
                    $inv   = $joItem->invoice;
                    $items[] = [
                        'category' => $inv?->invoice_ctg ?? 'General',
                        'label'    => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? ('Item #' . $joItem->id_jo_cont_item),
                        'amount'   => $amount,
                    ];
                }
                foreach ($jo->pajakItems as $pj) {
                    $amount = $pajSums[$pj->id_jo_cont_pajak] ?? 0;
                    if ($amount <= 0) continue;
                    $items[] = ['category' => 'Tax', 'label' => $pj->nama_pajak ?? ('Pajak #' . $pj->id_jo_cont_pajak), 'amount' => $amount];
                }

            } else {
                continue;
            }

            if (!empty($items)) {
                $pdfRows[] = ['paid_to' => $paidTo, 'jo_nomor' => $joNomor, 'items' => $items];
            }
        }

        $voucher->setAttribute('pdfRows', $pdfRows);

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
            'joTramper',
            'items.joTramperItem.invoice',   // direct: kasbon item → jo item → invoice
        ])->find($id);

        if (!$kasbon) return ['paid_to' => null, 'items' => []];

        $items = [];
        foreach ($kasbon->items as $kasbonItem) {
            if (($kasbonItem->item_type ?? '') === 'pajak') continue;

            $amount = (float) ($kasbonItem->nilai_kasbon ?? $kasbonItem->total_kasbon ?? 0);
            if ($amount <= 0) continue;

            $joItem  = $kasbonItem->joTramperItem;
            $invoice = $joItem?->invoice;

            $items[] = [
                'category' => $invoice?->invoice_ctg ?? 'General',
                'label'    => $invoice?->invoice_typ ?? $invoice?->id_md_invoice ?? ('Item #' . ($kasbonItem->id_kasbon_tram_item ?? $kasbonItem->id)),
                'amount'   => $amount,
            ];
        }

        return [
            'paid_to'      => $kasbon->release->nama_release ?? null,
            'jo_nomor'     => $kasbon->joTramper?->no_jo_tram ?? null,
            'kasbon_nomor' => $kasbon->id_kasbon_tram ?? null,
            'items'        => $items,
        ];
    }

    // ─── OTHER ─────────────────────────────────────────────────────

    private function voucherPdfRowOther(int $id): array
    {
        $kasbon = KasbonOther::with([
            'release',
            'joOther',
            'items.joOtherItem.invoice',     // direct: kasbon item → jo item → invoice
        ])->find($id);

        if (!$kasbon) return ['paid_to' => null, 'items' => []];

        $items = [];
        foreach ($kasbon->items as $kasbonItem) {
            if (($kasbonItem->item_type ?? '') === 'pajak') continue;

            $amount = (float) ($kasbonItem->nilai_kasbon ?? $kasbonItem->total_kasbon ?? 0);
            if ($amount <= 0) continue;

            $joItem  = $kasbonItem->joOtherItem;
            $invoice = $joItem?->invoice;

            $items[] = [
                'category' => $invoice?->invoice_ctg ?? 'General',
                'label'    => $invoice?->invoice_typ ?? $invoice?->id_md_invoice ?? ('Item #' . ($kasbonItem->id_kasbon_other_item ?? $kasbonItem->id)),
                'amount'   => $amount,
            ];
        }

        return [
            'paid_to'      => $kasbon->release->nama_release ?? null,
            'jo_nomor'     => $kasbon->joOther?->no_jo_other ?? null,
            'kasbon_nomor' => $kasbon->id_kasbon_other ?? null,
            'items'        => $items,
        ];
    }

    // ─── CONTRACT ──────────────────────────────────────────────────

    private function voucherPdfRowContract(int $id): array
    {
        $kasbon = KasbonContract::with([
            'release',
            'joContract',
            'items.joContractItem.invoice',  // direct: kasbon item → jo item → invoice
        ])->find($id);

        if (!$kasbon) return ['paid_to' => null, 'items' => []];

        $items = [];
        foreach ($kasbon->items as $kasbonItem) {
            if (($kasbonItem->item_type ?? '') === 'pajak') continue;

            $amount = (float) ($kasbonItem->nilai_kasbon ?? $kasbonItem->total_kasbon ?? 0);
            if ($amount <= 0) continue;

            $joItem  = $kasbonItem->joContractItem;
            $invoice = $joItem?->invoice;

            $items[] = [
                'category' => $invoice?->invoice_ctg ?? 'General',
                'label'    => $invoice?->invoice_typ ?? $invoice?->id_md_invoice ?? ('Item #' . ($kasbonItem->id_kasbon_cont_item ?? $kasbonItem->id)),
                'amount'   => $amount,
            ];
        }

        return [
            'paid_to'      => $kasbon->release->nama_release ?? null,
            'jo_nomor'     => $kasbon->joContract?->no_jo_cont ?? null,
            'kasbon_nomor' => $kasbon->id_kasbon_cont ?? null,
            'items'        => $items,
        ];
    }

    // ─── GENERAL ───────────────────────────────────────────────────

    private function voucherPdfRowGen(int $id): array
    {
        $kasbon = KasbonGen::with([
            'release',
            'items.invoice',                 // kasbon gen item → invoice directly
        ])->find($id);

        if (!$kasbon) return ['paid_to' => null, 'items' => []];

        $items = [];
        foreach ($kasbon->items as $kasbonItem) {
            $amount = (float) ($kasbonItem->nilai_kasbon ?? 0);
            if ($amount <= 0) continue;

            $inv = $kasbonItem->invoice;

            $items[] = [
                'category' => $inv?->invoice_ctg ?? $kasbonItem->kategori ?? 'General',
                'label'    => $inv?->invoice_typ ?? $inv?->id_md_invoice ?? $kasbonItem->keterangan ?? $kasbonItem->description ?? ('Item #' . ($kasbonItem->id_kasbon_gen_item ?? $kasbonItem->id)),
                'amount'   => $amount,
            ];
        }

        return [
            'paid_to'      => $kasbon->release->nama_release ?? null,
            'jo_nomor'     => null,
            'kasbon_nomor' => $kasbon->id_kasbon_gen ?? null,
            'items'        => $items,
        ];
    }

    public function nextVoucherNo(Request $request)
    {
        $paymentType = $request->get('payment_type');

        $nomor = match ($paymentType) {
            'Kas'  => IdGenerator::generateVoucherNoKas(),
            'Bank' => IdGenerator::generateVoucherNoBank(),
            default => IdGenerator::generateVoucherNo(),
        };

        return response()->json(['success' => true, 'nomor' => $nomor]);
    }

    /**
     * Update current_balance COA saat detail ditambah/dihapus.
     * $direction = 'deduct'  → kurangi current_balance sebesar nilai (total pembayaran keluar)
     * $direction = 'reverse' → tambah current_balance sebesar nilai (saat dibatalkan/dihapus)
     */
    protected function applyBalanceChange(int $voucherId, float $nilai, float $selisih, string $direction): void
    {
        $voucher = MutasiPembayaranVoucher::find($voucherId);
        if (!$voucher?->id_md_chart_of_account) return;

        $coaId = $voucher->id_md_chart_of_account;

        if ($direction === 'deduct') {
            // Kurangi balance sebesar total nilai pembayaran yang keluar dari kas/bank
            ChartOfAccount::where('id_md_chart_of_account', $coaId)
                ->decrement('current_balance', $nilai);
        } elseif ($direction === 'reverse') {
            // Balikkan: kembalikan nilai ke balance
            ChartOfAccount::where('id_md_chart_of_account', $coaId)
                ->increment('current_balance', $nilai);
        }
    }


    public static function resyncKasbonDetail(string $jenis, $kasbonDbId): void
    {
        $col = match ($jenis) {
            'tramper'  => 'id_kasbon_tram',
            'other'    => 'id_kasbon_other',
            'contract' => 'id_kasbon_cont',
            'general'  => 'id_kasbon_gen',
            default    => null,
        };

        if (!$col) return;

        $details = MutasiPembayaranDetail::where($col, $kasbonDbId)->get();
        if ($details->isEmpty()) return;

        $instance = new self();

        foreach ($details as $detail) {
            // Hitung ulang dari kasbon + LPJ terkini
            $amounts = $instance->resolveKasbonAmounts(
                $detail->jenis,
                new \Illuminate\Http\Request(),
                $detail
            );

            $oldNilai   = (float) $detail->nilai;
            $oldSelisih = (float) $detail->selisih;
            $newNilai   = $amounts['nilai'];
            $newSelisih = $amounts['nilai'] - $amounts['pj'];

            // Skip jika tidak ada perubahan
            if (abs($oldNilai - $newNilai) < 0.001 && abs($oldSelisih - $newSelisih) < 0.001) {
                continue;
            }

            DB::beginTransaction();
            try {
                // Update stored amounts di detail
                $detail->update([
                    'nilai'   => $newNilai,
                    'pj'      => $amounts['pj'],
                    'selisih' => $newSelisih,
                ]);

                // Reverse balance lama, apply balance baru
                $instance->applyBalanceChange($detail->id_mutasi_voucher, $oldNilai, $oldSelisih, 'reverse');
                $instance->applyBalanceChange($detail->id_mutasi_voucher, $newNilai, $newSelisih, 'deduct');

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('resyncKasbonDetail failed', [
                    'detail_id' => $detail->id,
                    'jenis'     => $jenis,
                    'kasbon_id' => $kasbonDbId,
                    'error'     => $e->getMessage(),
                ]);
            }
        }
    }
}
