<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Helpers\IdGenerator;

use App\Models\Data\MutasiPenerimaan;
use App\Models\Data\MutasiPenerimaanVoucher;
use App\Models\Data\MutasiPenerimaanDetail;

use App\Models\Data\JoTramper;
use App\Models\Data\JoOther;
use App\Models\Data\JoContract;

use App\Models\Data\KasbonTramper;
use App\Models\Data\KasbonOther;
use App\Models\Data\KasbonContract;
use App\Models\Data\KasbonGen;

use App\Models\Data\LpjKasbonTramper;
use App\Models\Data\LpjTramperItem;
use App\Models\Data\LpjKasbonOther;
use App\Models\Data\LpjOtherItem;
use App\Models\Data\LpjKasbon;
use App\Models\Data\LpjContractItem;
use App\Models\Data\LpjGenKasbon;
use App\Models\Data\LpjGenItem;

use App\Models\Master\ChartOfAccount;
use App\Models\Master\Departemen;
use App\Models\Master\Branch;
use App\Models\Master\Port;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MutasiPenerimaanController extends Controller
{
    // ═══════════════════════════════════════════════════════
    //  HEADER
    // ═══════════════════════════════════════════════════════

    public function index(Request $request)
    {
        try {
            $query = MutasiPenerimaan::with(['vouchers']);

            if ($request->filled('nomor'))
                $query->where('nomor', 'like', '%' . $request->nomor . '%');
            if ($request->filled('tanggal_dari'))
                $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            if ($request->filled('tanggal_sampai'))
                $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(
                    fn($q) => $q
                        ->where('nomor', 'like', "%{$s}%")
                        ->orWhere('memo', 'like', "%{$s}%")
                        ->orWhere('no_cek', 'like', "%{$s}%")
                );
            }

            $query->orderBy($request->get('sort_by', 'tanggal'), $request->get('sort_order', 'desc'));
            $mutasiPenerimaans = $query->get();

            if ($request->expectsJson())
                return response()->json(['success' => true, 'data' => $mutasiPenerimaans]);

            $chartOfAccounts = ChartOfAccount::whereIn('payment_type', ['Kas', 'Bank'])
                ->orderBy('no_account')->get();

            $currentFilters = [
                'nomor'         => $request->get('nomor', ''),
                'tanggal_dari'  => $request->get('tanggal_dari', ''),
                'tanggal_sampai' => $request->get('tanggal_sampai', ''),
            ];

            return view('data.mutasi-penerimaan.index', compact(
                'mutasiPenerimaans',
                'chartOfAccounts',
                'currentFilters'
            ));
        } catch (\Exception $e) {
            Log::error('MutasiPenerimaan index error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            if ($request->expectsJson())
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $nextNomor = IdGenerator::generatePenerimaanNo();
        return view('data.mutasi-penerimaan.create', compact('nextNomor'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_cek'   => 'nullable|string|max:100',
            'tanggal'  => 'required|date',
            'kurs'     => 'nullable|numeric|min:0',
            'memo'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson())
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $nomor = IdGenerator::generatePenerimaanNo();
            $mp = MutasiPenerimaan::create([
                'nomor'      => $nomor,
                'no_cek'     => $request->no_cek,
                'tanggal'    => $request->tanggal,
                'kurs'       => $request->kurs ?? 1,
                'memo'       => $request->memo,
                'created_by' => auth()->id(),
            ]);
            DB::commit();

            if ($request->expectsJson())
                return response()->json([
                    'success'      => true,
                    'message'      => 'Mutasi Penerimaan berhasil dibuat',
                    'redirect_url' => route('mutasi-penerimaan.edit', $mp->id),
                    'data'         => ['id' => $mp->id, 'nomor' => $mp->nomor],
                ], 201);

            return redirect()->route('mutasi-penerimaan.edit', $mp->id)
                ->with('success', 'Mutasi Penerimaan ' . $nomor . ' berhasil dibuat');
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
            $mp = MutasiPenerimaan::with([
                'vouchers.coa',
                'vouchers.details.customer',
                'vouchers.details.departemen',
                'vouchers.details.branch',
                'vouchers.details.joTramper',
                'vouchers.details.joOther',
                'vouchers.details.joContract',
            ])->findOrFail($id);

            $summary = [
                'total_voucher' => $mp->vouchers->count(),
                'total_nilai'   => $mp->vouchers->flatMap->details->sum('nilai'),
            ];

            if ($request->expectsJson())
                return response()->json(['success' => true, 'data' => $mp, 'summary' => $summary]);

            return view('data.mutasi-penerimaan.show', compact('mp', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson())
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $mutasiPenerimaan = MutasiPenerimaan::with([
                'vouchers'             => fn($q) => $q->orderBy('urutan'),
                'vouchers.coa',
                'vouchers.details.customer',
                'vouchers.details.departemen',
                'vouchers.details.branch',
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
            $allCoa          = ChartOfAccount::orderBy('no_account')->get();
            $departemenList  = Departemen::orderBy('nama_dep')->get();
            $branchList      = Branch::orderBy('nama_branch')->get();

            return view('data.mutasi-penerimaan.edit', compact(
                'mutasiPenerimaan',
                'chartOfAccounts',
                'allCoa',
                'departemenList',
                'branchList'
            ));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Mutasi Penerimaan tidak ditemukan');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'no_cek'   => 'nullable|string|max:100',
            'tanggal'  => 'required|date',
            'kurs'     => 'nullable|numeric|min:0',
            'memo'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson())
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $mp = MutasiPenerimaan::findOrFail($id);
            $mp->update([
                'no_cek'  => $request->no_cek,
                'tanggal' => $request->tanggal,
                'kurs'    => $request->kurs ?? 1,
                'memo'    => $request->memo,
            ]);
            DB::commit();

            if ($request->expectsJson())
                return response()->json(['success' => true, 'message' => 'Header berhasil diupdate']);
            return redirect()->route('mutasi-penerimaan.show', $id)->with('success', 'Data berhasil diupdate');
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
            $mp = MutasiPenerimaan::with('vouchers.details')->findOrFail($id);
            foreach ($mp->vouchers as $v) {
                $this->reverseVoucherBalance($v);
                $v->details()->delete();
            }
            $mp->vouchers()->delete();
            $mp->delete();
            DB::commit();

            if ($request->expectsJson())
                return response()->json(['success' => true, 'message' => 'Mutasi Penerimaan berhasil dihapus']);
            return redirect()->route('mutasi-penerimaan.index')->with('success', 'Mutasi Penerimaan berhasil dihapus');
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
        if ($request->has('id_md_chart_of_account') && in_array($request->id_md_chart_of_account, ['', 'NaN', '0']))
            $request->merge(['id_md_chart_of_account' => null]);
        if ($request->has('tanggal') && $request->tanggal === '')
            $request->merge(['tanggal' => null]);

        $validator = Validator::make($request->all(), [
            'id_mutasi_penerimaan'   => 'required|exists:e04_mutasi_penerimaan,id',
            'nomor_voucher'          => 'nullable|string|max:100',
            'keterangan'             => 'nullable|string',
            'urutan'                 => 'nullable|integer|min:1',
            'tanggal'                => 'nullable|date',
            'id_md_chart_of_account' => 'nullable|string',
        ]);
        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            $maxUrutan = MutasiPenerimaanVoucher::where('id_mutasi_penerimaan', $request->id_mutasi_penerimaan)
                ->max('urutan') ?? 0;

            $coa = $request->id_md_chart_of_account
                ? ChartOfAccount::find($request->id_md_chart_of_account)
                : null;

            $nomorVoucher = match ($coa?->payment_type) {
                'Kas'   => IdGenerator::generateVoucherNoKasPenerimaan(),
                'Bank'  => IdGenerator::generateVoucherNoBankPenerimaan(),
                default => IdGenerator::generateVoucherNoPenerimaan(),
            };

            $voucher = MutasiPenerimaanVoucher::create([
                'id_mutasi_penerimaan'   => $request->id_mutasi_penerimaan,
                'nomor_voucher'          => $nomorVoucher,
                'keterangan'             => $request->keterangan,
                'urutan'                 => $request->urutan ?? ($maxUrutan + 1),
                'tanggal'                => $request->tanggal ?: null,
                'id_md_chart_of_account' => $request->id_md_chart_of_account ?: null,
            ]);
            DB::commit();

            $voucher->load('coa');
            $coaDisplay = $voucher->coa
                ? trim(($voucher->coa->no_account ?? '') . ' ' . ($voucher->coa->account_name ?? ''))
                : null;

            return response()->json(['success' => true, 'message' => 'Voucher berhasil ditambahkan', 'data' => [
                'id'                     => $voucher->id,
                'nomor_voucher'          => $voucher->nomor_voucher,
                'keterangan'             => $voucher->keterangan,
                'urutan'                 => $voucher->urutan,
                'tanggal'                => $voucher->tanggal?->format('Y-m-d'),
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
        if ($request->has('id_md_chart_of_account') && in_array($request->id_md_chart_of_account, ['', 'NaN', '0']))
            $request->merge(['id_md_chart_of_account' => null]);
        if ($request->has('tanggal') && $request->tanggal === '')
            $request->merge(['tanggal' => null]);

        $validator = Validator::make($request->all(), [
            'nomor_voucher'          => 'nullable|string|max:100',
            'keterangan'             => 'nullable|string',
            'urutan'                 => 'nullable|integer|min:1',
            'tanggal'                => 'nullable|date',
            'id_md_chart_of_account' => 'nullable|string',
        ]);
        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            $voucher    = MutasiPenerimaanVoucher::findOrFail($id);
            $oldCoaId   = $voucher->id_md_chart_of_account;
            $newCoaId   = $request->has('id_md_chart_of_account')
                ? ($request->id_md_chart_of_account ?: null)
                : $oldCoaId;

            // Jika COA berubah, pindahkan saldo total detail dari COA lama ke COA baru
            if ($oldCoaId !== $newCoaId) {
                $totalNilai = (float) $voucher->details()->sum('nilai');
                if ($totalNilai > 0) {
                    if ($oldCoaId)
                        ChartOfAccount::where('id_md_chart_of_account', $oldCoaId)
                            ->decrement('current_balance', $totalNilai);
                    if ($newCoaId)
                        ChartOfAccount::where('id_md_chart_of_account', $newCoaId)
                            ->increment('current_balance', $totalNilai);
                }
            }

            $voucher->update([
                'nomor_voucher'          => $request->nomor_voucher ?? $voucher->nomor_voucher,
                'keterangan'             => $request->keterangan,
                'urutan'                 => $request->urutan ?? $voucher->urutan,
                'tanggal'                => $request->has('tanggal') ? ($request->tanggal ?: null) : $voucher->tanggal,
                'id_md_chart_of_account' => $newCoaId,
            ]);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Voucher berhasil diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyVoucher($id)
    {
        DB::beginTransaction();
        try {
            $voucher = MutasiPenerimaanVoucher::with('details')->findOrFail($id);

            // Tolak hapus jika voucher masih ada detail
            if ($voucher->details->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher tidak dapat dihapus karena masih memiliki ' . $voucher->details->count() . ' detail transaksi. Hapus semua detail terlebih dahulu.',
                ], 422);
            }

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
            'id_mutasi_voucher'      => 'required|exists:e05_mutasi_penerimaan_voucher,id',
            'jenis'                  => 'required|in:tramper,other,contract,general',
            'tipe_pembayaran'        => 'required|in:uang_muka_pembayaran,penerimaan_pembayaran,pengembalian_lebih_bayar',
            'id_jo_tram'             => 'nullable|exists:b03_jo_tram,id_jo_tram',
            'id_jo_other'            => 'nullable|exists:b05_jo_other,id_jo_other',
            'id_jo_cont'             => 'nullable|exists:b01_jo_cont,id_jo_cont',
            'id_kasbon_tram'         => 'nullable|exists:c03_kasbon_tram,id',
            'id_kasbon_other'        => 'nullable|exists:c05_kasbon_other,id',
            'id_kasbon_cont'         => 'nullable|exists:c01_kasbon_cont,id',
            'id_kasbon_gen'          => 'nullable|exists:c07_kasbon_gen,id',
            'id_md_dep'              => 'nullable|exists:a08_md_dep,id_md_dep',
            'id_md_branch'           => 'nullable|exists:a09_md_branch,id_md_branch',
            'nilai'                  => 'nullable|numeric|min:0',
            'keterangan'             => 'nullable|string|max:255',
            'release_to'             => 'nullable|string|max:255',
        ]);
        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            $isPengembalian = $request->tipe_pembayaran === MutasiPenerimaanDetail::TIPE_PENGEMBALIAN;

            // Resolve nilai & customer
            if ($isPengembalian) {
                // Nilai dari selisih LPJ (kasbon - lpj, hanya jika positif)
                $kasbonInfo = $this->resolveKasbonInfo($request->jenis, $request);
                if (!$kasbonInfo || !$kasbonInfo['has_lpj']) {
                    return response()->json(['success' => false, 'message' => 'Kasbon belum memiliki data LPJ. Refund tidak dapat dilakukan.'], 422);
                }
                if ($kasbonInfo['selisih'] <= 0) {
                    return response()->json(['success' => false, 'message' => 'Tidak ada selisih positif (sisa kasbon ≤ 0). Tidak ada yang perlu dikembalikan.'], 422);
                }
                $nilai      = $kasbonInfo['selisih'];
                $idMdCust   = $kasbonInfo['id_md_cust'] ?? null;
                // dept & branch dari input user (pilih manual)
                $idMdDep    = $request->id_md_dep;
                $idMdBranch = $request->id_md_branch;
            } else {
                // uang_muka / penerimaan: customer dari JO, dept/branch & nilai dari user
                $joInfo     = $this->resolveJoInfo($request->jenis, $request);
                $nilai      = (float) ($request->nilai ?? 0);
                $idMdCust   = $joInfo['id_md_cust'] ?? null;
                $idMdDep    = $request->id_md_dep;
                $idMdBranch = $request->id_md_branch;
            }

            $detail = MutasiPenerimaanDetail::create([
                'id_mutasi_voucher' => $request->id_mutasi_voucher,
                'jenis'             => $request->jenis,
                'tipe_pembayaran'   => $request->tipe_pembayaran,
                'id_jo_tram'        => $request->id_jo_tram,
                'id_jo_other'       => $request->id_jo_other,
                'id_jo_cont'        => $request->id_jo_cont,
                'id_kasbon_tram'    => $request->id_kasbon_tram,
                'id_kasbon_other'   => $request->id_kasbon_other,
                'id_kasbon_cont'    => $request->id_kasbon_cont,
                'id_kasbon_gen'     => $request->id_kasbon_gen,
                'id_md_cust'        => $idMdCust,
                'id_md_dep'         => $idMdDep,
                'id_md_branch'      => $idMdBranch,
                'release_to'        => $request->jenis === 'general' ? $request->release_to : null,
                'nilai'             => $nilai,
                'keterangan'        => $request->keterangan,
            ]);

            $this->applyBalanceChange($request->id_mutasi_voucher, $nilai, 'add');

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Detail berhasil ditambahkan',
                'data'    => $this->formatDetail($detail),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('storeDetail error', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function showDetail($id)
    {
        try {
            $detail = MutasiPenerimaanDetail::findOrFail($id);
            return response()->json(['success' => true, 'data' => $this->formatDetail($detail)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Detail tidak ditemukan'], 404);
        }
    }

    public function updateDetail(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'keterangan'    => 'nullable|string|max:255',
            'id_md_dep'     => 'nullable|string|max:50',
            'id_md_branch'  => 'nullable|string|max:50',
            'nilai'         => 'nullable|numeric|min:0',
        ]);
        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            $detail = MutasiPenerimaanDetail::findOrFail($id);

            $updateData = ['keterangan' => $request->keterangan];

            if ($detail->tipe_pembayaran !== 'pengembalian_lebih_bayar') {
                // Non-refund: boleh edit dep, branch, nilai
                $oldNilai = (float) $detail->nilai;
                $newNilai = $request->has('nilai') ? (float) $request->nilai : $oldNilai;

                $updateData['id_md_dep']    = $request->id_md_dep    ?: $detail->id_md_dep;
                $updateData['id_md_branch'] = $request->id_md_branch ?: $detail->id_md_branch;
                $updateData['nilai']        = $newNilai;

                // Sesuaikan balance COA voucher jika nilai berubah
                if ($newNilai !== $oldNilai) {
                    $diff = $newNilai - $oldNilai;
                    $voucher = MutasiPenerimaanVoucher::find($detail->id_mutasi_voucher);
                    if ($voucher && $voucher->id_md_chart_of_account) {
                        if ($diff > 0) {
                            ChartOfAccount::where('id_md_chart_of_account', $voucher->id_md_chart_of_account)
                                ->increment('current_balance', abs($diff));
                        } elseif ($diff < 0) {
                            ChartOfAccount::where('id_md_chart_of_account', $voucher->id_md_chart_of_account)
                                ->decrement('current_balance', abs($diff));
                        }
                    }
                }
            } else {
                // Refund: dep & branch boleh diedit, nilai tidak berubah
                $updateData['id_md_dep']    = $request->id_md_dep    ?: $detail->id_md_dep;
                $updateData['id_md_branch'] = $request->id_md_branch ?: $detail->id_md_branch;
            }

            if ($detail->jenis === 'general') {
                $updateData['release_to'] = $request->release_to ?? $detail->release_to;
            }

            $detail->update($updateData);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Detail berhasil diupdate',
                'data'    => $this->formatDetail($detail->fresh()),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyDetail($id)
    {
        DB::beginTransaction();
        try {
            $detail = MutasiPenerimaanDetail::findOrFail($id);
            $this->applyBalanceChange($detail->id_mutasi_voucher, (float) $detail->nilai, 'reverse');
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
                    'nomor' => $j->no_jo ?? $j->id_jo_tram,
                    'label' => $j->title ?? $j->keterangan ?? '',
                ]),
                'other'    => JoOther::orderBy('created_at', 'desc')->get()->map(fn($j) => [
                    'id' => $j->id_jo_other,
                    'nomor' => $j->no_jo ?? $j->id_jo_other,
                    'label' => $j->title ?? $j->keterangan ?? '',
                ]),
                'contract' => JoContract::orderBy('created_at', 'desc')->get()->map(fn($j) => [
                    'id' => $j->id_jo_cont,
                    'nomor' => $j->no_jo_cont ?? $j->no_jo ?? $j->id_jo_cont,
                    'label' => $j->title ?? $j->keterangan ?? '',
                ]),
                default    => collect(),
            };
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function joInfo(Request $request)
    {
        $jenis = $request->get('jenis');
        $joId  = $request->get('jo_id');
        if (!$joId) return response()->json(['success' => false, 'message' => 'jo_id wajib diisi'], 422);
        try {
            $info = $this->resolveJoInfoById($jenis, $joId);
            if (!$info) return response()->json(['success' => false, 'message' => 'JO tidak ditemukan'], 404);
            return response()->json(['success' => true, 'data' => $info]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function kasbonOptions(Request $request)
    {
        $jenis = $request->get('jenis');
        $joId  = $request->get('jo_id'); // opsional: filter kasbon by JO
        try {
            $usedTram  = MutasiPenerimaanDetail::whereNotNull('id_kasbon_tram')->pluck('id_kasbon_tram');
            $usedOther = MutasiPenerimaanDetail::whereNotNull('id_kasbon_other')->pluck('id_kasbon_other');
            $usedCont  = MutasiPenerimaanDetail::whereNotNull('id_kasbon_cont')->pluck('id_kasbon_cont');
            $usedGen   = MutasiPenerimaanDetail::whereNotNull('id_kasbon_gen')->pluck('id_kasbon_gen');

            $mapFn = fn($k, $nomor) => ['id' => $k->id, 'nomor' => $nomor, 'label' => implode(' / ', array_filter([
                $k->departemen?->nama_dep,
                $k->cabang?->nama_branch ?? $k->branch?->nama_branch,
                optional($k->tgl_kasbon)->format('d/m/Y'),
            ]))];

            $data = match ($jenis) {
                'tramper'  => KasbonTramper::with(['departemen', 'cabang'])->whereNotIn('id', $usedTram)
                    ->when($joId, fn($q) => $q->where('id_jo_tram', $joId))
                    ->get()->map(fn($k) => $mapFn($k, $k->id_kasbon_tram ?? 'C03-' . $k->id))->values(),
                'other'    => KasbonOther::with(['departemen', 'cabang'])->whereNotIn('id', $usedOther)
                    ->when($joId, fn($q) => $q->where('id_jo_other', $joId))
                    ->get()->map(fn($k) => $mapFn($k, $k->id_kasbon_other ?? 'C05-' . $k->id))->values(),
                'contract' => KasbonContract::with(['departemen', 'cabang'])->whereNotIn('id', $usedCont)
                    ->when($joId, fn($q) => $q->where('id_jo_cont', $joId))
                    ->get()->map(fn($k) => $mapFn($k, $k->id_kasbon_cont ?? 'C01-' . $k->id))->values(),
                'general'  => KasbonGen::with(['departemen', 'cabang'])->whereNotIn('id', $usedGen)
                    ->get()->map(fn($k) => $mapFn($k, $k->id_kasbon_gen ?? 'C07-' . $k->id))->values(),
                default    => collect(),
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
        if (!$kasbonId) return response()->json(['success' => false, 'message' => 'kasbon_id wajib diisi'], 422);
        try {
            $info = $this->resolveKasbonInfoById($jenis, $kasbonId);
            if (!$info) return response()->json(['success' => false, 'message' => 'Kasbon tidak ditemukan'], 404);
            return response()->json(['success' => true, 'data' => $info]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function voucherDetails($voucherId)
    {
        try {
            $voucher = MutasiPenerimaanVoucher::findOrFail($voucherId);
            $details = $voucher->details()->orderBy('id')->get();
            $data    = $details->map(fn($d) => $this->formatDetail($d))->values();
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function nextVoucherNo(Request $request)
    {
        $nomor = match ($request->get('payment_type')) {
            'Kas'   => IdGenerator::generateVoucherNoKasPenerimaan(),
            'Bank'  => IdGenerator::generateVoucherNoBankPenerimaan(),
            default => IdGenerator::generateVoucherNoPenerimaan(),
        };
        return response()->json(['success' => true, 'nomor' => $nomor]);
    }

    // ═══════════════════════════════════════════════════════
    //  PRIVATE HELPERS — JO
    // ═══════════════════════════════════════════════════════

    private function resolveJoInfo(string $jenis, Request $request): array
    {
        $joId = match ($jenis) {
            'tramper'  => $request->id_jo_tram,
            'other'    => $request->id_jo_other,
            'contract' => $request->id_jo_cont,
            default    => null,
        };
        if (!$joId) return ['id_md_cust' => null];
        return $this->resolveJoInfoById($jenis, $joId) ?? ['id_md_cust' => null];
    }

    private function resolveJoInfoById(string $jenis, $joId): ?array
    {
        return match ($jenis) {
            'tramper'  => $this->getJoTramperInfo($joId),
            'other'    => $this->getJoOtherInfo($joId),
            'contract' => $this->getJoContractInfo($joId),
            default    => null,
        };
    }

    private function getJoTramperInfo($joId): ?array
    {
        $jo = JoTramper::with(['customer', 'vessel', 'port'])->where('id_jo_tram', $joId)->first();
        if (!$jo) return null;
        return [
            'jo_id'      => $jo->id_jo_tram,
            'nomor_jo'   => $jo->no_jo ?? $jo->id_jo_tram,
            'id_md_cust' => $jo->id_md_cust ?? null,
            'customer'   => $jo->customer?->customer ?? null,
            'vessel'     => $jo->vessel?->vessel_name ?? null,
            'id_md_port' => $jo->id_md_port ?? null,
            'port'       => $jo->port?->name_port ?? null,
        ];
    }

    private function getJoOtherInfo($joId): ?array
    {
        $jo = JoOther::with(['customer', 'vessel', 'port'])->where('id_jo_other', $joId)->first();
        if (!$jo) return null;
        return [
            'jo_id'      => $jo->id_jo_other,
            'nomor_jo'   => $jo->no_jo_other ?? $jo->id_jo_other,
            'id_md_cust' => $jo->id_md_cust ?? null,
            'customer'   => $jo->customer?->customer ?? null,
            'vessel'     => $jo->vessel?->vessel_name ?? null,
            'id_md_port' => $jo->id_md_port ?? null,
            'port'       => $jo->port?->name_port ?? null,
        ];
    }

    private function getJoContractInfo($joId): ?array
    {
        $jo = JoContract::with(['contract.customer'])->where('id_jo_cont', $joId)->first();
        if (!$jo) return null;
        return [
            'jo_id'      => $jo->id_jo_cont,
            'nomor_jo'   => $jo->no_jo_cont ?? $jo->id_jo_cont,
            'id_md_cust' => $jo->contract?->id_md_cust ?? null,
            'customer'   => $jo->contract?->customer?->customer ?? $jo->contract?->contract ?? null,
            'vessel'     => null,
            'id_md_port' => null,
            'port'       => null,
        ];
    }

    // ═══════════════════════════════════════════════════════
    //  PRIVATE HELPERS — KASBON
    // ═══════════════════════════════════════════════════════

    private function resolveKasbonInfo(string $jenis, Request $request): ?array
    {
        $id = match ($jenis) {
            'tramper'  => $request->id_kasbon_tram,
            'other'    => $request->id_kasbon_other,
            'contract' => $request->id_kasbon_cont,
            'general'  => $request->id_kasbon_gen,
            default    => null,
        };
        if (!$id) return null;
        return $this->resolveKasbonInfoById($jenis, $id);
    }

    private function resolveKasbonInfoById(string $jenis, $id): ?array
    {
        return match ($jenis) {
            'tramper'  => $this->getKasbonTramperInfo($id),
            'other'    => $this->getKasbonOtherInfo($id),
            'contract' => $this->getKasbonContractInfo($id),
            'general'  => $this->getKasbonGenInfo($id),
            default    => null,
        };
    }

    private function getKasbonTramperInfo($id): ?array
    {
        $k = KasbonTramper::with(['departemen', 'cabang', 'release', 'items', 'joTramper.customer', 'joTramper.vessel', 'joTramper.port'])->where('id', $id)->first();
        if (!$k) return null;
        $nilai   = $k->items->sum('nilai_kasbon');
        $lpjIds  = LpjKasbonTramper::where('id_kasbon_tram', $k->id_kasbon_tram)->pluck('id_lpj_tram');
        $hasLpj  = $lpjIds->count() > 0;
        $pj      = $hasLpj ? LpjTramperItem::whereIn('id_lpj_tram', $lpjIds)->whereIn('id_kasbon_tram_item', $k->items->pluck('id_kasbon_tram_item'))->sum('amount_lpj') : 0;
        $selisih = max(0, (float) $nilai - (float) $pj); // hanya positif
        return [
            'id_kasbon'    => $k->id,
            'nomor'        => $k->id_kasbon_tram ?? $k->nomor,
            'id_jo'        => $k->id_jo_tram,
            'nomor_jo'     => $k->joTramper?->no_jo ?? $k->id_jo_tram,
            'id_md_cust'   => $k->joTramper?->id_md_cust ?? null,
            'customer'     => $k->joTramper?->customer?->customer ?? null,
            'tgl_kasbon'   => optional($k->tgl_kasbon)->format('d/m/Y'),
            'has_lpj'      => $hasLpj,
            'nilai'        => (float) $nilai,
            'pj'           => (float) $pj,
            'selisih'      => $selisih,
            'id_md_dep'    => $k->id_md_dep,
            'departemen'   => $k->departemen?->nama_dep,
            'id_md_branch' => $k->id_md_cabang,
            'branch'       => $k->cabang?->nama_branch,
            'release_to'   => $k->release?->nama_release,
            'vessel'       => $k->joTramper?->vessel?->vessel_name,
            'port'         => $k->joTramper?->port?->name_port,
        ];
    }

    private function getKasbonOtherInfo($id): ?array
    {
        $k = KasbonOther::with(['departemen', 'cabang', 'release', 'items', 'joOther.customer', 'joOther.vessel', 'joOther.port'])->where('id', $id)->first();
        if (!$k) return null;
        $nilai   = $k->items->sum('nilai_kasbon');
        $lpjIds  = LpjKasbonOther::where('id_kasbon_other', $k->id_kasbon_other)->pluck('id_lpj_other');
        $hasLpj  = $lpjIds->count() > 0;
        $pj      = $hasLpj ? LpjOtherItem::whereIn('id_lpj_other', $lpjIds)->whereIn('id_kasbon_other_item', $k->items->pluck('id_kasbon_other_item'))->sum('amount_lpj') : 0;
        $selisih = max(0, (float) $nilai - (float) $pj);
        return [
            'id_kasbon'    => $k->id,
            'nomor'        => $k->id_kasbon_other ?? $k->nomor,
            'id_jo'        => $k->id_jo_other,
            'nomor_jo'     => $k->joOther?->no_jo ?? $k->id_jo_other,
            'id_md_cust'   => $k->joOther?->id_md_cust ?? null,
            'customer'     => $k->joOther?->customer?->customer ?? null,
            'tgl_kasbon'   => optional($k->tgl_kasbon)->format('d/m/Y'),
            'has_lpj'      => $hasLpj,
            'nilai'        => (float) $nilai,
            'pj'           => (float) $pj,
            'selisih'      => $selisih,
            'id_md_dep'    => $k->id_md_dep,
            'departemen'   => $k->departemen?->nama_dep,
            'id_md_branch' => $k->id_md_cabang,
            'branch'       => $k->cabang?->nama_branch,
            'release_to'   => $k->release?->nama_release,
            'vessel'       => $k->joOther?->vessel?->vessel_name,
            'port'         => $k->joOther?->port?->name_port,
        ];
    }

    private function getKasbonContractInfo($id): ?array
    {
        $k = KasbonContract::with([
            'departemen', 'cabang', 'release',
            'items', 'items.joContractItem.invoice', 'items.joContractPajak',
            'joContract.contract.customer',
        ])->where('id', $id)->first();
        if (!$k) return null;
        $lpjIds = LpjKasbon::where('id_kasbon_cont', $k->id_kasbon_cont)->pluck('id_lpj_cont');
        $hasLpj = $lpjIds->count() > 0;

        // Build per-item breakdown (including tax rows)
        $itemsList = $k->items->map(function ($item) use ($lpjIds, $hasLpj) {
            $isTax  = ($item->item_type ?? 'item') === 'tax';
            $pjItem = $hasLpj
                ? LpjContractItem::whereIn('id_lpj_cont', $lpjIds)
                    ->where('id_kasbon_cont_item', $item->id_kasbon_cont_item)
                    ->sum('amount_lpj')
                : 0;
            if ($isTax) {
                $pajak = $item->joContractPajak;
                $label = $pajak?->nama_pajak ?? ('Tax #' . $item->id);
            } else {
                $inv   = $item->joContractItem?->invoice;
                $label = $inv?->invoice_typ ?? ('Item #' . $item->id);
            }
            $nilaiBaris = (float) $item->nilai_kasbon;
            $pjBaris    = (float) $pjItem;
            return [
                'label'        => $label,
                'nilai_kasbon' => $nilaiBaris,
                'pj'           => $pjBaris,
                'selisih'      => $nilaiBaris - $pjBaris,
            ];
        })->values()->toArray();

        $nilai   = (float) $k->items->sum('nilai_kasbon');
        $pj      = (float) array_sum(array_column($itemsList, 'pj'));
        $selisih = max(0, $nilai - $pj);
        return [
            'id_kasbon'    => $k->id,
            'nomor'        => $k->id_kasbon_cont ?? $k->nomor,
            'id_jo'        => $k->id_jo_cont,
            'nomor_jo'     => $k->joContract?->no_jo_cont ?? $k->id_jo_cont,
            'id_md_cust'   => $k->joContract?->contract?->id_md_cust ?? null,
            'customer'     => $k->joContract?->contract?->customer?->customer
                ?? $k->joContract?->contract?->contract
                ?? null,
            'tgl_kasbon'   => optional($k->tgl_kasbon)->format('d/m/Y'),
            'has_lpj'      => $hasLpj,
            'nilai'        => $nilai,
            'pj'           => $pj,
            'selisih'      => $selisih,
            'id_md_dep'    => $k->id_md_dep,
            'departemen'   => $k->departemen?->nama_dep,
            'id_md_branch' => $k->id_md_cabang,
            'branch'       => $k->cabang?->nama_branch,
            'release_to'   => $k->release?->nama_release,
            'vessel'       => null,
            'port'         => null,
            'items'        => $itemsList,
        ];
    }

    private function getKasbonGenInfo($id): ?array
    {
        $k = KasbonGen::with(['departemen', 'cabang', 'release', 'items'])->find($id);
        if (!$k) return null;
        $nilai   = $k->items->sum('nilai_kasbon');
        $lpjIds  = LpjGenKasbon::where('id_kasbon_gen', $k->id_kasbon_gen)->pluck('id_lpj_gen');
        $hasLpj  = $lpjIds->count() > 0;
        $pj      = $hasLpj ? LpjGenItem::whereIn('id_lpj_gen', $lpjIds)
            ->whereIn('id_kasbon_gen_item', $k->items->pluck('id_kasbon_gen_item'))
            ->sum('amount_lpj') : 0;
        $selisih = max(0, (float) $nilai - (float) $pj);
        return [
            'id_kasbon'      => $k->id,
            'nomor'          => $k->id_kasbon_gen ?? $k->nomor,
            'id_jo'          => null,
            'nomor_jo'       => null,
            'id_md_cust'     => null,
            'customer'       => null,
            'tgl_kasbon'     => optional($k->tgl_kasbon)->format('d/m/Y'),
            'has_lpj'        => $hasLpj,
            'nilai'          => (float) $nilai,
            'pj'             => (float) $pj,
            'selisih'        => $selisih,
            // ← baru: auto-fill dari kasbon
            'id_md_dep'      => $k->id_md_dep,
            'departemen'     => $k->departemen?->nama_dep,
            'id_md_branch'   => $k->id_md_cabang,
            'branch'         => $k->cabang?->nama_branch,
            'id_md_release'  => $k->id_md_release,
            'release_to'     => $k->release?->nama_release,
        ];
    }

    // ═══════════════════════════════════════════════════════
    //  PRIVATE HELPERS — FORMAT & BALANCE
    // ═══════════════════════════════════════════════════════

    private function formatDetail(MutasiPenerimaanDetail $detail): array
    {
        $detail->loadMissing(['customer', 'departemen', 'branch']);

        $joNomor     = null;
        $kasbonNomor = null;

        $joId = match ($detail->jenis) {
            'tramper'  => $detail->id_jo_tram,
            'other'    => $detail->id_jo_other,
            'contract' => $detail->id_jo_cont,
            default    => null,
        };
        if ($joId) {
            $joInfo  = $this->resolveJoInfoById($detail->jenis, $joId);
            $joNomor = $joInfo['nomor_jo'] ?? null;
        }

        $kasbonId = match ($detail->jenis) {
            'tramper'  => $detail->id_kasbon_tram,
            'other'    => $detail->id_kasbon_other,
            'contract' => $detail->id_kasbon_cont,
            'general'  => $detail->id_kasbon_gen,
            default    => null,
        };
        if ($kasbonId) {
            $kasbonInfo  = $this->resolveKasbonInfoById($detail->jenis, $kasbonId);
            $kasbonNomor = $kasbonInfo['nomor'] ?? null;
        }

        $tipePembayaranLabel = match ($detail->tipe_pembayaran) {
            'uang_muka_pembayaran'    => 'Down Payment',
            'penerimaan_pembayaran'   => 'Payment Receipt',
            'pengembalian_lebih_bayar' => 'Refund',
            default                   => $detail->tipe_pembayaran,
        };

        return [
            'id'                      => $detail->id,
            'id_mutasi_voucher'       => $detail->id_mutasi_voucher,
            'jenis'                   => $detail->jenis,
            'tipe_pembayaran'         => $detail->tipe_pembayaran,
            'tipe_pembayaran_label'   => $tipePembayaranLabel,
            'id_jo_tram'              => $detail->id_jo_tram,
            'id_jo_other'             => $detail->id_jo_other,
            'id_jo_cont'              => $detail->id_jo_cont,
            'id_kasbon_tram'          => $detail->id_kasbon_tram,
            'id_kasbon_other'         => $detail->id_kasbon_other,
            'id_kasbon_cont'          => $detail->id_kasbon_cont,
            'id_kasbon_gen'           => $detail->id_kasbon_gen,
            'id_md_cust'              => $detail->id_md_cust,
            'customer'                => $detail->customer?->customer,
            'id_md_dep'               => $detail->id_md_dep,
            'departemen'              => $detail->departemen?->nama_dep,
            'id_md_branch'            => $detail->id_md_branch,
            'branch'                  => $detail->branch?->nama_branch,
            'nilai'                   => (float) $detail->nilai,
            'keterangan'              => $detail->keterangan,
            'nomor_jo'                => $joNomor,
            'nomor_kasbon'            => $kasbonNomor,
            'release_to'              => $detail->release_to,
        ];
    }

    private function applyBalanceChange(int $voucherId, float $nilai, string $direction): void
    {
        $voucher = MutasiPenerimaanVoucher::find($voucherId);
        if (!$voucher?->id_md_chart_of_account) return;
        $coaId = $voucher->id_md_chart_of_account;
        if ($direction === 'add')
            ChartOfAccount::where('id_md_chart_of_account', $coaId)->increment('current_balance', $nilai);
        elseif ($direction === 'reverse')
            ChartOfAccount::where('id_md_chart_of_account', $coaId)->decrement('current_balance', $nilai);
    }

    private function reverseVoucherBalance(MutasiPenerimaanVoucher $voucher): void
    {
        foreach ($voucher->details as $detail) {
            $this->applyBalanceChange($voucher->id, (float) $detail->nilai, 'reverse');
        }
    }
}