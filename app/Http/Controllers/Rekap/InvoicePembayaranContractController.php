<?php

namespace App\Http\Controllers\Rekap;

use App\Http\Controllers\Controller;
use App\Models\Data\InvoicePembayaranContract;
use App\Models\Master\Vessel;
use App\Models\Master\Port;
use App\Models\Data\InvoicePembayaranContractItem;
use App\Models\Data\JoContract;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\CompanyProfile;
use App\Models\Master\Customer;
use Illuminate\Http\Request;
use App\Models\Data\MutasiPenerimaanDetail;
use Illuminate\Support\Facades\DB;
use App\Helpers\IdGenerator;

class InvoicePembayaranContractController extends Controller
{
    /* ─────────────── INDEX ─────────────── */
    public function index(Request $request)
    {
        $query = InvoicePembayaranContract::with([
            'joContract.contract.customer',
            'items',
        ]);

        if ($request->filled('no_invoice')) {
            $query->where('no_invoice', 'like', '%' . $request->no_invoice . '%');
        }
        if ($request->filled('id_md_cust')) {
            $query->where('id_md_cust', $request->id_md_cust);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderByDesc('created_at')->get();

        // Ambil semua id_jo_cont dari invoice yang ada
        $joContIds = $invoices->pluck('id_jo_cont')->filter()->unique()->values()->toArray();

        // Hitung total diterima per id_jo_cont dari mutasi penerimaan (DP + Penerimaan)
        $diterimaPerJo = MutasiPenerimaanDetail::query()
            ->whereIn('id_jo_cont', $joContIds)
            ->where('jenis', 'contract')
            ->whereIn('tipe_pembayaran', [
                MutasiPenerimaanDetail::TIPE_UANG_MUKA,
                MutasiPenerimaanDetail::TIPE_PENERIMAAN,
            ])
            ->groupBy('id_jo_cont')
            ->selectRaw('id_jo_cont, SUM(nilai) as total_diterima')
            ->pluck('total_diterima', 'id_jo_cont');

        $rows = $invoices->map(function ($inv) use ($diterimaPerJo) {
            $dpp   = $inv->items->where('item_type', 'item')->where('is_reimbursement', false)->sum('nominal_invoice');
            $ppn   = $inv->items->where('item_type', 'tax')->sum('nominal_invoice');
            $reimb = $inv->items->where('is_reimbursement', true)->sum('nominal_invoice');
            $total = $dpp + $ppn + $reimb;
            return [
                'id'           => $inv->id_invoice_contract,
                'no_invoice'   => $inv->no_invoice,
                'no_jo'        => $inv->joContract?->no_jo_cont ?? $inv->no_jo,
                'id_jo_cont'   => $inv->id_jo_cont,
                'customer'     => $inv->joContract?->contract?->customer?->customer ?? $inv->to_customer ?? '-',
                'tgl_invoice'  => $inv->tgl_invoice,
                'due_date'     => $inv->due_date,
                'dpp'          => (float) $dpp,
                'ppn'          => (float) $ppn,
                'reimbursement'=> (float) $reimb,
                'total_invoice'=> (float) $total,
                'diterima'     => (float) ($diterimaPerJo[$inv->id_jo_cont] ?? 0),
                'close_note'   => $inv->close_note,
                'reopen_note'  => $inv->reopen_note,
                'status'       => $inv->status,
            ];
        });

        $customers = Customer::orderBy('customer')->get();
        $currentFilters = $request->only(['no_invoice', 'id_md_cust', 'status']);

        return view('rekap.invoice-contract.index', compact('rows', 'customers', 'currentFilters'));
    }

    /* ─────────────── CREATE (header form) ─────────────── */
    public function create(Request $request)
    {
        $id_jo_cont  = $request->get('id_jo_cont');
        $joContract  = $id_jo_cont ? JoContract::with(['contract.customer'])->find($id_jo_cont) : null;
        $joContracts = JoContract::with(['contract.customer'])->orderByDesc('id_jo_cont')->get();

        $suggestedNo = IdGenerator::generateInvoiceNo();

        $vessels = Vessel::orderBy('vessel_name')->get();
        $ports    = Port::orderBy('name_port')->get();
        $coas     = ChartOfAccount::orderBy('no_account')->get();

        return view('rekap.invoice-contract.create', compact('joContract', 'joContracts', 'suggestedNo', 'vessels', 'ports', 'coas'));
    }

    /* ─────────────── STORE (header only → redirect to edit) ─────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'no_invoice'  => 'required|string',
            'id_jo_cont'  => 'required',
            'tgl_invoice' => 'nullable|date',
            'due_date'    => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $jo = JoContract::with(['contract', 'items', 'pajakItems'])->find($request->id_jo_cont);

            $invoice = InvoicePembayaranContract::create([
                'no_invoice'      => $request->no_invoice,
                'id_jo_cont'      => $request->id_jo_cont,
                'id_md_cust'      => $jo?->contract?->id_md_cust,
                'kapal'           => $request->kapal,
                'title'           => $request->title ?? $jo?->title,
                'no_jo'           => $jo?->no_jo_cont,
                'vendor_number'   => $request->vendor_number,
                'contract_no'     => $request->contract_no ?? $jo?->contract?->no_contractract,
                'po_number'       => $request->po_number,
                'sa_gr_number'    => $request->sa_gr_number,
                'ba_number'       => $request->ba_number,
                'id_md_chart_of_account' => $request->id_md_chart_of_account ?: null,
                'vat_number'      => $request->vat_number,
                'tgl_invoice'     => $request->tgl_invoice,
                'due_date'        => $request->due_date,
                'eta'             => $request->eta,
                'etd'             => $request->etd,
                'no_voyage'       => $request->no_voyage,
                'grt'             => $request->grt,
                'jetty'           => $request->jetty,
                'cond_of_payment' => $request->cond_of_payment,
                'currency'        => $request->currency ?? 'IDR',
                'period'          => $request->period,
                'bar_code'        => $request->bar_code,
                'to_customer'     => $request->to_customer ?? $jo?->contract?->customer?->customer,
                'address'         => $request->address,
                'att'             => $request->att,
                'office_phone'    => $request->office_phone,
                'mobile_phone'    => $request->mobile_phone,
                'status'          => 'draft',
                'created_by'      => auth()->id(),
            ]);

            if ($jo) {
                foreach ($jo->items as $joItem) {
                    InvoicePembayaranContractItem::create([
                        'id_invoice_contract' => $invoice->id_invoice_contract,
                        'id_jo_cont_item'     => $joItem->id_jo_cont_item,
                        'item_type'           => 'item',
                        'nominal_invoice'     => 0,
                        'is_reimbursement'    => false,
                    ]);
                }
                foreach ($jo->pajakItems as $pajak) {
                    InvoicePembayaranContractItem::create([
                        'id_invoice_contract' => $invoice->id_invoice_contract,
                        'id_jo_cont_pajak'    => $pajak->id_jo_cont_pajak,
                        'item_type'           => 'tax',
                        'nominal_invoice'     => $pajak->nominal_pajak ?? 0,
                        'is_reimbursement'    => false,
                        'deskripsi'           => $pajak->nama_pajak,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('invoice-contract.edit', $invoice->id_invoice_contract)
                ->with('success', 'Invoice Contract successfully created. Please complete the items below.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    /* ─────────────── EDIT ─────────────── */
    public function show($id)
    {
        $invoice = InvoicePembayaranContract::with([
            'items.joContractItem.invoice',
            'items.joContractPajak',
            'items.coa',
            'joContract.contract.customer',
            'joContract.items.invoice',
            'joContract.pajakItems',
            'coa',
        ])->findOrFail($id);

        $coas = ChartOfAccount::orderBy('no_account')->get();

        $invoiceItemsJs = $invoice->items->values()->map(function ($item) {
            $isTax = $item->item_type === 'tax';
            return [
                'id_invoice_contract_item' => $item->id_invoice_contract_item,
                'item_type'               => $item->item_type,
                'invoice_ctg'             => $isTax ? '__TAX__'
                    : ($item->joContractItem?->invoice?->invoice_ctg ?? 'Uncategorized'),
                'invoice_typ'             => $isTax
                    ? ($item->deskripsi ?? $item->joContractPajak?->nama_pajak ?? ('Pajak #' . $item->id_invoice_contract_item))
                    : ($item->joContractItem?->invoice?->invoice_typ ?? ('Item #' . $item->id_invoice_contract_item)),
                'sell_idr'                => $isTax
                    ? (float)($item->joContractPajak?->nominal_pajak ?? 0)
                    : (float)($item->joContractItem?->hargajual_idr ?? 0),
                'sell_usd'                => (float)($item->joContractItem?->pendapatan_usd ?? 0),
                'nominal_invoice'         => (float)$item->nominal_invoice,
                'nominal_invoice_usd'     => (float)($item->nominal_invoice_usd ?? 0),
                'id_md_chart_of_account'  => $item->id_md_chart_of_account,
                'coa_no'                  => $item->coa?->no_account,
                'coa_name'                => $item->coa?->account_name,
                'is_reimbursement'        => (bool)$item->is_reimbursement,
                'note'                    => $item->joContractItem?->note ?? null,
                'persen_pajak'            => $isTax ? ($item->joContractPajak?->persentase ?? null) : null,
            ];
        });

        $coaListJs = $coas->map(fn($c) => [
            'id'   => $c->id_md_chart_of_account,
            'no'   => $c->no_account,
            'name' => $c->account_name,
        ])->values()->toArray();

        return view('rekap.invoice-contract.show', compact(
            'invoice', 'coas', 'invoiceItemsJs', 'coaListJs'
        ));
    }

        public function edit($id)
    {
        $invoice = InvoicePembayaranContract::with([
            'items.joContractItem.invoice',
            'items.joContractPajak',
            'items.coa',
            'joContract.contract.customer',
            'joContract.items.invoice',
            'joContract.pajakItems',
        ])->findOrFail($id);

        $coas = ChartOfAccount::orderBy('no_account')->get();

        // Build items JS data
        $invoiceItemsJs = $invoice->items->values()->map(function ($item) {
            $isTax = $item->item_type === 'tax';
            return [
                'id_invoice_contract_item' => $item->id_invoice_contract_item,
                'item_type'               => $item->item_type,
                'invoice_ctg'             => $isTax ? '__TAX__'
                    : ($item->joContractItem?->invoice?->invoice_ctg ?? 'Uncategorized'),
                'invoice_typ'             => $isTax
                    ? ($item->deskripsi ?? $item->joContractPajak?->nama_pajak ?? ('Pajak #' . $item->id_invoice_contract_item))
                    : ($item->joContractItem?->invoice?->invoice_typ ?? ('Item #' . $item->id_invoice_contract_item)),
                'sell_idr'                => $isTax
                    ? (float)($item->joContractPajak?->nominal_pajak ?? 0)
                    : (float)($item->joContractItem?->hargajual_idr ?? 0),
                'sell_usd'                => (float)($item->joContractItem?->pendapatan_usd ?? 0),
                'nominal_invoice'         => (float)$item->nominal_invoice,
                'nominal_invoice_usd'     => (float)($item->nominal_invoice_usd ?? 0),
                'id_md_chart_of_account'  => $item->id_md_chart_of_account,
                'coa_no'                  => $item->coa?->no_account,
                'coa_name'                => $item->coa?->account_name,
                'is_reimbursement'        => (bool)$item->is_reimbursement,
                'note'                    => $item->joContractItem?->note ?? null,
                'persen_pajak'            => $isTax ? ($item->joContractPajak?->persentase ?? null) : null,
            ];
        });

        // Kurs info from JO
        $itemWithUsd = $invoice->joContract?->items?->first(fn($i) => (float)$i->pendapatan_usd > 0);
        $kursUsd     = $itemWithUsd ? (float)$itemWithUsd->kurs_usd : null;

        $coaListJs = $coas->map(fn($c) => [
            'id'   => $c->id_md_chart_of_account,
            'no'   => $c->no_account,
            'name' => $c->account_name,
        ])->values()->toArray();

        $vessels = Vessel::orderBy('vessel_name')->get();
        $ports    = Port::orderBy('name_port')->get();

        $joContracts = \App\Models\Data\JoContract::with(['contract.customer'])->orderByDesc('id_jo_cont')->get();
        return view('rekap.invoice-contract.edit', compact(
            'invoice', 'coas', 'kursUsd', 'invoiceItemsJs', 'coaListJs', 'vessels', 'ports', 'joContracts'
        ));
    }

    /* ─────────────── UPDATE (traditional form — fallback) ─────────────── */
    public function update(Request $request, $id)
    {
        $invoice = InvoicePembayaranContract::findOrFail($id);

        DB::beginTransaction();
        try {
            $headerFields = ['no_invoice','tgl_invoice','due_date','eta','etd','kapal','title',
                'vendor_number','contract_no','po_number','sa_gr_number','ba_number','vat_number',
                'no_voyage','grt','jetty','cond_of_payment','currency','period','bar_code','to_customer',
                'address','att','office_phone','mobile_phone','status','close_note','reopen_note'];

            $updateData = [];
            foreach ($headerFields as $f) {
                if ($request->has($f)) $updateData[$f] = $request->get($f);
            }
            if (!empty($updateData)) $invoice->update($updateData);

            if ($request->has('items')) {
                foreach ($request->items as $itemId => $data) {
                    $invItem = InvoicePembayaranContractItem::where('id_invoice_contract', $invoice->id_invoice_contract)
                        ->where('id_invoice_contract_item', $itemId)->first();
                    if (!$invItem) continue;

                    $oldNominal = (float) ($invItem->nominal_invoice ?? 0);
                    $oldCoaId   = $invItem->id_md_chart_of_account;
                    $newNominal = (float) str_replace(['.', ','], ['', '.'], $data['nominal_invoice'] ?? 0);
                    $newCoaId   = $data['id_md_chart_of_account'] ?: null;

                    $invItem->update([
                        'nominal_invoice'        => $newNominal,
                        'id_md_chart_of_account' => $newCoaId,
                        'is_reimbursement'       => isset($data['is_reimbursement']) ? 1 : 0,
                        'deskripsi'              => $data['deskripsi'] ?? null,
                    ]);

                    // Sinkronisasi current_balance COA
                    if ($oldCoaId !== $newCoaId) {
                        if ($oldCoaId && $oldNominal > 0) {
                            ChartOfAccount::where('id_md_chart_of_account', $oldCoaId)
                                ->decrement('current_balance', $oldNominal);
                        }
                        if ($newCoaId && $newNominal > 0) {
                            ChartOfAccount::where('id_md_chart_of_account', $newCoaId)
                                ->increment('current_balance', $newNominal);
                        }
                    } elseif ($newCoaId) {
                        $diff = $newNominal - $oldNominal;
                        if ($diff > 0) {
                            ChartOfAccount::where('id_md_chart_of_account', $newCoaId)
                                ->increment('current_balance', $diff);
                        } elseif ($diff < 0) {
                            ChartOfAccount::where('id_md_chart_of_account', $newCoaId)
                                ->decrement('current_balance', abs($diff));
                        }
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Invoice Contract successfully updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }

    /* ─────────────── AJAX: UPDATE HEADER ─────────────── */
    public function updateHeader(Request $request, $id)
    {
        $invoice = InvoicePembayaranContract::findOrFail($id);
        DB::beginTransaction();
        try {
            $newJoId = $request->get('id_jo_cont');
            $joChanged = $newJoId && (int)$newJoId !== (int)$invoice->id_jo_cont;

            $invoice->update($request->only([
                'id_jo_cont','no_invoice','tgl_invoice','due_date','status','title','contract_no','po_number',
                'currency','kurs_invoice','tgl_kurs_invoice','vendor_number','sa_gr_number','ba_number','eta','etd','no_voyage',
                'grt','kapal','jetty','cond_of_payment','period','att','address','vat_number',
                'bar_code','office_phone','mobile_phone','close_note','reopen_note','to_customer',
                'id_md_chart_of_account',
            ]));

            if ($joChanged) {
                $jo = \App\Models\Data\JoContract::with(['items', 'pajakItems'])->find($newJoId);
                if ($jo) {
                    $invoice->items()->delete();
                    foreach ($jo->items as $joItem) {
                        $invoice->items()->create([
                            'id_jo_cont_item'        => $joItem->id_jo_cont_item,
                            'id_jo_cont_pajak'       => null,
                            'item_type'              => 'item',
                            'nominal_invoice'        => 0,
                            'is_reimbursement'       => 0,
                            'id_md_chart_of_account' => null,
                        ]);
                    }
                    foreach ($jo->pajakItems as $pajak) {
                        $invoice->items()->create([
                            'id_jo_cont_item'        => null,
                            'id_jo_cont_pajak'       => $pajak->id_jo_cont_pajak,
                            'item_type'              => 'tax',
                            'nominal_invoice'        => 0,
                            'is_reimbursement'       => 0,
                            'id_md_chart_of_account' => null,
                        ]);
                    }
                }
            }

            DB::commit();
            $msg = $joChanged ? 'Header saved and items reset to new JO.' : 'Header saved successfully!';
            return response()->json(['success' => true, 'message' => $msg, 'jo_changed' => $joChanged]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* ─────────────── AJAX: BULK SAVE ITEMS ─────────────── */
    public function bulkSaveItems(Request $request, $id)
    {
        $invoice = InvoicePembayaranContract::findOrFail($id);
        DB::beginTransaction();
        try {
            foreach ($request->input('items', []) as $itemData) {
                $item = InvoicePembayaranContractItem::where('id_invoice_contract', $invoice->id_invoice_contract)
                    ->where('id_invoice_contract_item', $itemData['id_invoice_contract_item'])
                    ->first();
                if (!$item) continue;

                // Simpan nilai lama sebelum update
                $oldNominal = (float) ($item->nominal_invoice ?? 0);
                $oldCoaId   = $item->id_md_chart_of_account;

                $newNominal = (float) ($itemData['nominal_invoice'] ?? 0);
                $newCoaId   = $itemData['id_md_chart_of_account'] ?: null;

                $item->update([
                    'nominal_invoice'        => $newNominal,
                    'nominal_invoice_usd'    => (float) ($itemData['nominal_invoice_usd'] ?? 0),
                    'id_md_chart_of_account' => $newCoaId,
                    'is_reimbursement'       => (bool) ($itemData['is_reimbursement'] ?? false),
                ]);

                // Sinkronisasi current_balance COA
                // Invoice = uang masuk (IN), jadi increment COA
                if ($oldCoaId !== $newCoaId) {
                    // COA berubah: reverse nominal lama ke COA lama, apply nominal baru ke COA baru
                    if ($oldCoaId && $oldNominal > 0) {
                        ChartOfAccount::where('id_md_chart_of_account', $oldCoaId)
                            ->decrement('current_balance', $oldNominal);
                    }
                    if ($newCoaId && $newNominal > 0) {
                        ChartOfAccount::where('id_md_chart_of_account', $newCoaId)
                            ->increment('current_balance', $newNominal);
                    }
                } elseif ($newCoaId) {
                    // COA sama: sesuaikan selisih
                    $diff = $newNominal - $oldNominal;
                    if ($diff > 0) {
                        ChartOfAccount::where('id_md_chart_of_account', $newCoaId)
                            ->increment('current_balance', $diff);
                    } elseif ($diff < 0) {
                        ChartOfAccount::where('id_md_chart_of_account', $newCoaId)
                            ->decrement('current_balance', abs($diff));
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Item saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* ─────────────── DESTROY ─────────────── */
    public function destroy($id)
    {
        $invoice = InvoicePembayaranContract::with('items')->findOrFail($id);
        $no = $invoice->no_invoice;

        DB::beginTransaction();
        try {
            // Reverse current_balance COA untuk semua item sebelum hapus
            foreach ($invoice->items as $item) {
                if ($item->id_md_chart_of_account && $item->nominal_invoice > 0) {
                    ChartOfAccount::where('id_md_chart_of_account', $item->id_md_chart_of_account)
                        ->decrement('current_balance', (float) $item->nominal_invoice);
                }
            }
            $invoice->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }

        return redirect()->route('invoice-contract.index')->with('success', 'Invoice Contract successfully deleted.');
    }
    /* ─────────────── AJAX: GET JO INFO (create page, no invoice context) ─────────────── */
    public function getJoInfoCreate(Request $request)
    {
        return $this->getJoInfo($request, null);
    }

        /* ─────────────── AJAX: GET JO INFO ─────────────── */
    public function getJoInfo(Request $request, $id)
    {
        $joId = $request->get('id_jo_cont');
        $jo   = \App\Models\Data\JoContract::with([
            'contract.customer',
            'items.invoice',
            'pajakItems',
        ])->find($joId);
        if (!$jo) return response()->json(['success' => false, 'message' => 'JO not found'], 404);

        // ── Kurs from items ──
        $kursUsd  = null; $tglKurs = null;
        $itemKurs = $jo->items->whereNotNull('kurs_usd')->where('kurs_usd', '>', 0)->first();
        if ($itemKurs) {
            $kursUsd = $itemKurs->kurs_usd;
            $tglKurs = $itemKurs->tgl_kurs_usd ? \Carbon\Carbon::parse($itemKurs->tgl_kurs_usd)->format('d M Y') : null;
        }

        // ── Items list ──
        $items = $jo->items->map(fn($i) => [
            'category'   => $i->invoice?->invoice_ctg ?? '-',
            'name'       => $i->invoice?->invoice_typ ?? '-',
            'hpp'        => number_format($i->hpp_ops ?? 0, 2, ',', '.'),
            'sell_idr'   => number_format($i->hargajual_idr ?? 0, 2, ',', '.'),
            'pend_idr'   => number_format($i->pendapatan_idr ?? 0, 2, ',', '.'),
            'pend_usd'   => number_format($i->pendapatan_usd ?? 0, 2, ',', '.'),
            'kurs_usd'   => $i->kurs_usd ? number_format($i->kurs_usd, 0, ',', '.') : null,
            'note'       => $i->note ?? null,
        ]);
        $pajakItems = $jo->pajakItems->map(fn($p) => [
            'name'        => $p->nama_pajak ?? '-',
            'persen'      => $p->persen_pajak ?? null,
            'nominal_idr' => number_format($p->nominal_pajak ?? 0, 2, ',', '.'),
            'nominal_hpp' => number_format($p->nominal_pajak_hpp ?? 0, 2, ',', '.'),
        ]);

        // ── Kasbon ──
        $kasbons = \App\Models\Data\KasbonContract::with('items')
            ->where('id_jo_cont', $joId)->get();
        $kasbonList = $kasbons->map(fn($k) => [
            'nomor'   => $k->nomor ?? $k->id_kasbon_cont ?? '-',
            'tgl'     => $k->tgl_kasbon ? $k->tgl_kasbon->format('d M Y') : '-',
            'status'  => $k->ca_release_status ?? '-',
            'total'   => number_format($k->items->sum('total_kasbon'), 2, ',', '.'),
        ]);
        $kasbonTotal = $kasbons->sum(fn($k) => $k->items->sum('total_kasbon'));

        // ── LPJ ──
        $lpjList = \App\Models\Data\LpjContract::with('items')
            ->where('id_jo_cont', $joId)->get()->map(fn($l) => [
                'no_lpj'      => $l->no_lpj_cont ?? '-',
                'tgl'         => $l->date ? \Carbon\Carbon::parse($l->date)->format('d M Y') : '-',
                'ca_amount'   => number_format($l->amount ?? 0, 2, ',', '.'),
                'pj_amount'   => number_format($l->items->sum('amount_lpj'), 2, ',', '.'),
                'balance'     => number_format(($l->amount ?? 0) - $l->items->sum('amount_lpj'), 2, ',', '.'),
            ]);

        // ── Mutasi Penerimaan (via detail where id_jo_cont = joId) ──
        $mutasiPenerimaanDetails = \App\Models\Data\MutasiPenerimaanDetail::with('voucher.mutasiPenerimaan')
            ->where('id_jo_cont', $joId)->get();
        $mutasiPenerimaan = $mutasiPenerimaanDetails->map(fn($d) => [
            'nomor'  => $d->voucher?->mutasiPenerimaan?->nomor ?? '-',
            'tgl'    => $d->voucher?->mutasiPenerimaan?->tanggal
                        ? \Carbon\Carbon::parse($d->voucher->mutasiPenerimaan->tanggal)->format('d M Y') : '-',
            'tipe'   => $d->tipe_pembayaran ?? '-',
            'nilai'  => number_format($d->nilai ?? 0, 2, ',', '.'),
        ]);

        // ── Mutasi Pembayaran (via detail where id_jo_cont = joId) ──
        $mutasiPembayaranDetails = \App\Models\Data\MutasiPembayaranDetail::with('voucher.mutasiPembayaran')
            ->where('id_jo_cont', $joId)->get();
        $mutasiPembayaran = $mutasiPembayaranDetails->map(fn($d) => [
            'nomor'  => $d->voucher?->mutasiPembayaran?->nomor ?? '-',
            'tgl'    => $d->voucher?->mutasiPembayaran?->tanggal
                        ? \Carbon\Carbon::parse($d->voucher->mutasiPembayaran->tanggal)->format('d M Y') : '-',
            'jenis'  => $d->jenis ?? '-',
            'nilai'  => number_format($d->nilai ?? 0, 2, ',', '.'),
        ]);

        return response()->json([
            'success'           => true,
            'no_jo_cont'        => $jo->no_jo_cont,
            'title'             => $jo->title,
            'customer'          => $jo->contract?->customer?->customer ?? '-',
            'contract_no'       => $jo->contract?->no_contract ?? '-',
            'kurs_usd'          => $kursUsd ? number_format($kursUsd, 0, ',', '.') : null,
            'tgl_kurs_usd'      => $tglKurs,
            'items'             => $items,
            'pajak_items'       => $pajakItems,
            'kasbon'            => $kasbonList,
            'kasbon_total'      => number_format($kasbonTotal, 2, ',', '.'),
            'lpj'               => $lpjList,
            'mutasi_penerimaan' => $mutasiPenerimaan,
            'mutasi_pembayaran' => $mutasiPembayaran,
        ]);
    }

    /* ─────────────── AJAX: CHANGE JO (replace items) ─────────────── */
    public function changeJo(Request $request, $id)
    {
        $invoice = \App\Models\Data\InvoicePembayaranContract::findOrFail($id);
        $request->validate(['id_jo_cont' => 'required|integer']);

        $jo = \App\Models\Data\JoContract::with(['contract', 'items', 'pajakItems'])->find($request->id_jo_cont);
        if (!$jo) return response()->json(['success' => false, 'message' => 'JO not found'], 404);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Update invoice header with new JO info
            $invoice->update([
                'id_jo_cont'  => $jo->id_jo_cont,
                'no_jo'       => $jo->no_jo_cont,
                'title'       => $jo->title,
                'to_customer' => $jo->contract?->customer?->customer ?? $invoice->to_customer,
                'contract_no' => $jo->contract?->no_contract ?? $invoice->contract_no,
            ]);

            // Delete old items
            $invoice->items()->delete();

            // Recreate items from new JO items
            foreach ($jo->items as $joItem) {
                $invoice->items()->create([
                    'id_jo_cont_item'         => $joItem->id_jo_cont_item,
                    'id_jo_cont_pajak'        => null,
                    'nominal_invoice'         => 0,
                    'item_type'               => 'item',
                    'is_reimbursement'        => 0,
                    'id_md_chart_of_account'  => null,
                ]);
            }
            foreach ($jo->pajakItems as $pajak) {
                $invoice->items()->create([
                    'id_jo_cont_item'         => null,
                    'id_jo_cont_pajak'        => $pajak->id_jo_cont_pajak,
                    'nominal_invoice'         => 0,
                    'item_type'               => 'tax',
                    'is_reimbursement'        => 0,
                    'id_md_chart_of_account'  => null,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['success' => true, 'message' => 'JO changed successfully. Items have been reset.']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* ─────────────── EXPORT VDA (PDF) ─────────────── */
    public function exportVda($id)
    {
        try {
            $invoice = InvoicePembayaranContract::with([
                'items.joContractItem.invoice',
                'items.joContractPajak',
                'joContract.contract.customer',
                'coa',
            ])->findOrFail($id);

            $grandTotalIdr  = (float) $invoice->items->sum('nominal_invoice');
            $grandTotalUsd  = (float) $invoice->items->sum('nominal_invoice_usd');
            $isUsdMode      = $grandTotalUsd > 0;
            if ($isUsdMode) {
                $terbilang = $this->toTerbilang((int) round($grandTotalUsd)) . ' US Dollar';
            } else {
                $terbilang = $this->toTerbilang((int) round($grandTotalIdr)) . ' Rupiah';
            }
            $grandTotal     = $isUsdMode ? $grandTotalUsd : $grandTotalIdr;
            $companyProfile = CompanyProfile::first();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'rekap.invoice-contract.vda-pdf',
                compact('invoice', 'terbilang', 'companyProfile')
            )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => false,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'Arial',
                'dpi'                  => 150,
            ]);

            $noInv    = str_replace(['/', '\\'], '-', $invoice->no_invoice ?? $id);
            $filename = 'VDA-' . $noInv . '.pdf';
            return $pdf->stream($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Invoice Contract not found');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('VDA Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export VDA PDF: ' . $e->getMessage());
        }
    }

    public function exportInvoice($id)
    {
        try {
            $invoice = InvoicePembayaranContract::with([
                'items.joContractItem.invoice',
                'items.joContractPajak',
                'joContract.contract.customer',
                'customer',
                'coa',
            ])->findOrFail($id);

            $items      = $invoice->items;
            $regular    = $items->where('item_type', 'item')->where('is_reimbursement', false);
            $reimb      = $items->where('item_type', 'item')->where('is_reimbursement', true);
            $taxItems   = $items->where('item_type', 'tax');

            $dpp          = (float) $regular->sum('nominal_invoice');
            $reimbTotal   = (float) $reimb->sum('nominal_invoice');
            $ppnTotal     = (float) $taxItems->sum('nominal_invoice');
            $subTotal     = $dpp + $reimbTotal;
            $totalTagihan = $subTotal + $ppnTotal;

            $ppnPct = $taxItems->first()?->joContractPajak?->persentase ?? 11;

            $terbilang      = $this->toTerbilang((int) round($totalTagihan)) . ' Rupiah';
            $companyProfile = CompanyProfile::first();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'rekap.invoice-contract.invoice-pdf',
                compact('invoice', 'terbilang', 'dpp', 'reimbTotal', 'ppnTotal', 'subTotal', 'totalTagihan', 'ppnPct', 'companyProfile')
            )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => false,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'Arial',
                'dpi'                  => 150,
            ]);

            $noInv    = str_replace(['/', chr(92)], '-', $invoice->no_invoice ?? $id);
            $filename = 'INVOICE-' . $noInv . '.pdf';
            return $pdf->stream($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Invoice Contract not found');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Invoice Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export Invoice PDF: ' . $e->getMessage());
        }
    }

    public function exportReimbursement($id)
    {
        try {
            $invoice = InvoicePembayaranContract::with([
                'items.joContractItem.invoice',
                'items.joContractPajak',
                'joContract.contract.customer',
                'customer',
                'coa',
            ])->findOrFail($id);

            $reimbItems = $invoice->items->where('item_type', 'item')->where('is_reimbursement', true)->values();
            $grandTotal = (float) $reimbItems->sum('nominal_invoice');
            $terbilang  = $this->toTerbilang((int) round($grandTotal)) . ' Rupiah';
            $companyProfile = \App\Models\Master\CompanyProfile::first();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'rekap.invoice-contract.reimbursement-pdf',
                compact('invoice', 'terbilang', 'companyProfile')
            )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => false,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'Arial',
                'dpi'                  => 150,
            ]);

            $noInv    = str_replace(['/', chr(92)], '-', $invoice->no_invoice ?? $id);
            $filename = 'REIMBURSEMENT-' . $noInv . '.pdf';
            return $pdf->stream($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Invoice Contract not found');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Reimbursement Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export Reimbursement PDF: ' . $e->getMessage());
        }
    }


    public function exportKwitansi($id)
    {
        try {
            $invoice = InvoicePembayaranContract::with([
                'items.joContractItem.invoice',
                'items.joContractPajak',
                'joContract.contract.customer',
                'customer',
                'coa',
            ])->findOrFail($id);

            $companyProfile = \App\Models\Master\CompanyProfile::first();

            // Closure terbilang untuk tiap item
            $terbilangFn = function (int $n): string {
                return $this->toTerbilang($n);
            };

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'rekap.invoice-contract.kwitansi-pdf',
                compact('invoice', 'companyProfile', 'terbilangFn')
            )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => false,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'Arial',
                'dpi'                  => 150,
            ]);

            $noInv    = str_replace(['/', chr(92)], '-', $invoice->no_invoice ?? $id);
            $filename = 'KWITANSI-' . $noInv . '.pdf';
            return $pdf->stream($filename);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Invoice Contract not found');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Kwitansi Export PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export Kwitansi PDF: ' . $e->getMessage());
        }
    }

    public function exportAll($id)
    {
        try {
            $invoice = InvoicePembayaranContract::with([
                'items.joContractItem.invoice',
                'items.joContractPajak',
                'joContract.contract.customer',
                'customer',
                'coa',
            ])->findOrFail($id);

            $items      = $invoice->items;
            $regular    = $items->where('item_type', 'item')->where('is_reimbursement', false);
            $reimb      = $items->where('item_type', 'item')->where('is_reimbursement', true);
            $taxItems   = $items->where('item_type', 'tax');

            $dpp          = (float) $regular->sum('nominal_invoice');
            $reimbTotal   = (float) $reimb->sum('nominal_invoice');
            $ppnTotal     = (float) $taxItems->sum('nominal_invoice');
            $subTotal     = $dpp + $reimbTotal;
            $totalTagihan = $subTotal + $ppnTotal;
            $ppnPct       = $taxItems->first()?->joContractPajak?->persentase ?? 11;

            $grandVda   = (float) $items->sum('nominal_invoice');
            $grandReimb = (float) $reimb->sum('nominal_invoice');

            // Render each section to HTML
            $companyProfile = \App\Models\Master\CompanyProfile::first();
            $htmlVda = view(
                'rekap.invoice-contract.vda-pdf',
                ['invoice' => $invoice, 'terbilang' => $this->toTerbilang((int) round($grandVda)) . ' Rupiah', 'companyProfile' => $companyProfile]
            )->render();

            $htmlInvoice = view(
                'rekap.invoice-contract.invoice-pdf',
                array_merge(
                    compact('invoice', 'dpp', 'reimbTotal', 'ppnTotal', 'subTotal', 'totalTagihan', 'ppnPct', 'companyProfile'),
                    ['terbilang' => $this->toTerbilang((int) round($totalTagihan)) . ' Rupiah']
                )
            )->render();

            $htmlReimb = view(
                'rekap.invoice-contract.reimbursement-pdf',
                ['invoice' => $invoice, 'terbilang' => $this->toTerbilang((int) round($grandReimb)) . ' Rupiah', 'companyProfile' => $companyProfile]
            )->render();

            // Extract <body> content and <style> blocks separately
            $stripOuter = function (string $html): string {
                if (preg_match('/<body[^>]*>(.*?)<\/body>/si', $html, $m)) {
                    return $m[1];
                }
                return $html;
            };

            // Extract all <style> blocks from each blade's HTML
            $extractStyles = function (string $html): string {
                preg_match_all('/<style[^>]*>(.*?)<\/style>/si', $html, $m);
                return implode("\n", $m[1]);
            };

            $pb = '<div style="page-break-before:always;margin:0;padding:0;"></div>';

            $combinedHtml = '<!DOCTYPE html>'
                . '<html lang="en"><head>'
                . '<meta charset="UTF-8">'
                . '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'
                . '<style>' . $extractStyles($htmlVda)     . '</style>'
                . '<style>' . $extractStyles($htmlInvoice) . '</style>'
                . '<style>' . $extractStyles($htmlReimb)   . '</style>'
                . '<style>'
                . '@page { size: A4 portrait; margin: 0; }'
                . '* { box-sizing:border-box; }'
                . 'body { margin:0 !important; padding:0 !important; font-family: Arial, sans-serif; font-size: 9pt; color:#000; background:#fff; }'
                . '.pdf-page { padding: 14mm; }'
                . '</style>'
                . '</head><body>'
                . '<div class="pdf-page">' . $stripOuter($htmlVda)     . '</div>'
                . $pb
                . '<div class="pdf-page">' . $stripOuter($htmlInvoice) . '</div>'
                . $pb
                . '<div class="pdf-page">' . $stripOuter($htmlReimb)   . '</div>'
                . '</body></html>';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHtml($combinedHtml)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => false,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'Arial',
                    'dpi'                  => 150,
                ]);

            $noInv    = str_replace(['/', '\\'], '-', $invoice->no_invoice ?? $id);
            $filename = 'ALL-' . $noInv . '.pdf';
            return $pdf->stream($filename);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Invoice Contract not found');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Export All PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export All PDF: ' . $e->getMessage());
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
        if ($number < 1000000)     return $this->toTerbilang((int)($number / 1000)) . ' Ribu' . ($number % 1000 ? ' ' . $this->toTerbilang($number % 1000) : '');
        if ($number < 1000000000)  return $this->toTerbilang((int)($number / 1000000)) . ' Juta' . ($number % 1000000 ? ' ' . $this->toTerbilang($number % 1000000) : '');
        if ($number < 1000000000000) return $this->toTerbilang((int)($number / 1000000000)) . ' Miliar' . ($number % 1000000000 ? ' ' . $this->toTerbilang($number % 1000000000) : '');
        return $this->toTerbilang((int)($number / 1000000000000)) . ' Triliun' . ($number % 1000000000000 ? ' ' . $this->toTerbilang($number % 1000000000000) : '');
    }


}