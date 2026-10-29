<?php

namespace App\Http\Controllers\Rekap;

use App\Http\Controllers\Controller;
use App\Models\Data\InvoicePembayaranContract;
use App\Models\Data\InvoicePembayaranContractItem;
use App\Models\Data\JoContract;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Customer;
use Illuminate\Http\Request;
use App\Models\Data\MutasiPenerimaanDetail;
use Illuminate\Support\Facades\DB;

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

        $lastInvoice  = InvoicePembayaranContract::orderByDesc('id_invoice_contract')->first();
        $nextNum      = $lastInvoice ? ((int) preg_replace('/\D/', '', $lastInvoice->no_invoice) + 1) : 1;
        $suggestedNo  = 'INV-CONT-' . date('Ymd') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        return view('rekap.invoice-contract.create', compact('joContract', 'joContracts', 'suggestedNo'));
    }

    /* ─────────────── STORE (header only → redirect to edit) ─────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'no_invoice'  => 'required|string|unique:f01_invoice_pembayaran_contract,no_invoice',
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
                'contract_no'     => $request->contract_no ?? $jo?->contract?->no_contract,
                'po_number'       => $request->po_number,
                'sa_gr_number'    => $request->sa_gr_number,
                'ba_number'       => $request->ba_number,
                'vat_number'      => $request->vat_number,
                'tgl_invoice'     => $request->tgl_invoice,
                'due_date'        => $request->due_date,
                'eta'             => $request->eta,
                'etd'             => $request->etd,
                'no_voyage'       => $request->no_voyage,
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
                ->with('success', 'Invoice ' . $invoice->no_invoice . ' berhasil dibuat. Silakan lengkapi item di bawah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan invoice: ' . $e->getMessage());
        }
    }

    /* ─────────────── EDIT ─────────────── */
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
                'id_md_chart_of_account'  => $item->id_md_chart_of_account,
                'coa_no'                  => $item->coa?->no_account,
                'coa_name'                => $item->coa?->account_name,
                'is_reimbursement'        => (bool)$item->is_reimbursement,
                'note'                    => $item->joContractItem?->note ?? null,
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

        return view('rekap.invoice-contract.edit', compact(
            'invoice', 'coas', 'kursUsd', 'invoiceItemsJs', 'coaListJs'
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
                'no_voyage','jetty','cond_of_payment','currency','period','bar_code','to_customer',
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
                    $invItem->update([
                        'nominal_invoice'        => (float) str_replace(['.', ','], ['', '.'], $data['nominal_invoice'] ?? 0),
                        'id_md_chart_of_account' => $data['id_md_chart_of_account'] ?? null,
                        'is_reimbursement'       => isset($data['is_reimbursement']) ? 1 : 0,
                        'deskripsi'              => $data['deskripsi'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Invoice berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /* ─────────────── AJAX: UPDATE HEADER ─────────────── */
    public function updateHeader(Request $request, $id)
    {
        $invoice = InvoicePembayaranContract::findOrFail($id);
        try {
            $invoice->update($request->only([
                'no_invoice','tgl_invoice','due_date','status','contract_no','po_number',
                'currency','close_note','reopen_note','to_customer',
            ]));
            return response()->json(['success' => true, 'message' => 'Header berhasil disimpan!']);
        } catch (\Exception $e) {
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
                $item->update([
                    'nominal_invoice'        => (float) ($itemData['nominal_invoice'] ?? 0),
                    'id_md_chart_of_account' => $itemData['id_md_chart_of_account'] ?: null,
                    'is_reimbursement'       => (bool) ($itemData['is_reimbursement'] ?? false),
                ]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Item berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* ─────────────── DESTROY ─────────────── */
    public function destroy($id)
    {
        $invoice = InvoicePembayaranContract::findOrFail($id);
        $no = $invoice->no_invoice;
        $invoice->delete();
        return redirect()->route('invoice-contract.index')->with('success', 'Invoice ' . $no . ' berhasil dihapus.');
    }
}
