<?php

namespace App\Http\Controllers\Rekap;

use App\Http\Controllers\Controller;
use App\Models\Data\InvoicePembayaranOther;
use App\Models\Data\InvoicePembayaranOtherItem;
use App\Models\Data\JoOther;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\CompanyProfile;
use App\Models\Master\Customer;
use App\Models\Master\Vessel;
use App\Models\Master\Port;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\IdGenerator;
use App\Models\Data\MutasiPenerimaanDetail;
use App\Models\Data\MutasiPembayaranDetail;
use App\Models\Data\KasbonOther;
use App\Models\Data\LpjOther;

class InvoicePembayaranOtherController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoicePembayaranOther::with(['joOther.customer', 'items']);
        if ($request->filled('no_invoice')) $query->where('no_invoice', 'like', '%' . $request->no_invoice . '%');
        if ($request->filled('id_md_cust'))  $query->where('id_md_cust', $request->id_md_cust);
        if ($request->filled('status'))      $query->where('status', $request->status);
        $invoices = $query->orderByDesc('created_at')->get();

        $joOtherIds = $invoices->pluck('id_jo_other')->filter()->unique()->values()->toArray();
        $diterimaPerJo = MutasiPenerimaanDetail::query()
            ->whereIn('id_jo_other', $joOtherIds)
            ->where('jenis', 'other')
            ->whereIn('tipe_pembayaran', [
                MutasiPenerimaanDetail::TIPE_UANG_MUKA,
                MutasiPenerimaanDetail::TIPE_PENERIMAAN,
            ])
            ->groupBy('id_jo_other')
            ->selectRaw('id_jo_other, SUM(nilai) as total_diterima')
            ->pluck('total_diterima', 'id_jo_other');

        $rows = $invoices->map(function ($inv) use ($diterimaPerJo) {
            $dpp   = $inv->items->where('item_type', 'item')->where('is_reimbursement', false)->sum('nominal_invoice');
            $ppn   = $inv->items->where('item_type', 'tax')->sum('nominal_invoice');
            $reimb = $inv->items->where('is_reimbursement', true)->sum('nominal_invoice');
            return [
                'id'           => $inv->id_invoice_other,
                'no_invoice'   => $inv->no_invoice,
                'no_jo'        => $inv->joOther?->no_jo_other ?? $inv->no_jo,
                'id_jo_other'   => $inv->id_jo_other,
                'customer'     => $inv->joOther?->customer?->customer ?? $inv->to_customer ?? '-',
                'tgl_invoice'  => $inv->tgl_invoice,
                'due_date'     => $inv->due_date,
                'dpp'          => (float) $dpp,
                'ppn'          => (float) $ppn,
                'reimbursement'=> (float) $reimb,
                'total_invoice'=> (float) ($dpp + $ppn + $reimb),
                'diterima'     => (float) ($diterimaPerJo[$inv->id_jo_other] ?? 0),
                'close_note'   => $inv->close_note,
                'reopen_note'  => $inv->reopen_note,
                'status'       => $inv->status,
            ];
        });
        $customers = Customer::orderBy('customer')->get();
        $currentFilters = $request->only(['no_invoice', 'id_md_cust', 'status']);
        return view('rekap.invoice-other.index', compact('rows', 'customers', 'currentFilters'));
    }

    public function create(Request $request)
    {
        $id_jo_other  = $request->get('id_jo_other');
        $joOther   = $id_jo_other ? JoOther::with(['customer'])->find($id_jo_other) : null;
        $joOthers  = JoOther::with(['customer'])->orderByDesc('id_jo_other')->get();
        $suggestedNo = IdGenerator::generateInvoiceNo();
        $vessels = Vessel::orderBy('vessel_name')->get();
        $ports   = Port::orderBy('name_port')->get();
        $coas    = ChartOfAccount::orderBy('no_account')->get();
        return view('rekap.invoice-other.create', compact('joOther', 'joOthers', 'suggestedNo', 'vessels', 'ports', 'coas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_invoice'  => 'required|string',
            'id_jo_other'  => 'required',
            'tgl_invoice' => 'nullable|date',
            'due_date'    => 'nullable|date',
        ]);
        DB::beginTransaction();
        try {
            $jo = JoOther::with(['customer', 'items', 'pajakItems'])->find((string) $request->id_jo_other);
            $invoice = InvoicePembayaranOther::create([
                'no_invoice'      => $request->no_invoice,
                'id_jo_other'      => $request->id_jo_other,
                'id_md_cust'      => $jo?->id_md_cust,
                'kapal'           => $request->kapal,
                'title'           => $request->title ?? $jo?->title,
                'no_jo'           => $jo?->no_jo_other,
                'vendor_number'   => $request->vendor_number,
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
                'to_customer'     => $request->to_customer ?? $jo?->customer?->customer,
                'address'         => $request->address,
                'att'             => $request->att,
                'office_phone'    => $request->office_phone,
                'mobile_phone'    => $request->mobile_phone,
                'status'          => 'draft',
                'created_by'      => auth()->id(),
            ]);
            if ($jo) {
                    foreach ($jo->items as $joItem) {
                    $invoice->items()->create([
                        'id_jo_other_item'       => $joItem->id_jo_other_item,
                        'item_type'              => 'item',
                        'nominal_invoice'        => 0,
                        'nominal_invoice_usd'    => 0,
                        'is_reimbursement'       => false,
                        'id_md_chart_of_account' => null,
                    ]);
                }
                foreach ($jo->pajakItems as $pajak) {
                    $invoice->items()->create([
                        'id_jo_other_item'       => null,
                        'id_jo_other_pajak'      => $pajak->id_jo_other_pajak,
                        'item_type'              => 'tax',
                        'nominal_invoice'        => $pajak->nominal_pajak ?? 0,
                        'nominal_invoice_usd'    => 0,
                        'is_reimbursement'       => false,
                        'deskripsi'              => $pajak->nama_pajak,
                        'id_md_chart_of_account' => null,
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('invoice-other.edit', $invoice->id_invoice_other)
                ->with('success', 'Invoice Other successfully created.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $invoice = InvoicePembayaranOther::with([
            'items.joOtherItem.invoice', 'items.joOtherPajak', 'items.coa',
            'joOther.customer', 'joOther.items.invoice', 'joOther.pajakItems', 'coa',
        ])->findOrFail($id);
        $coas = ChartOfAccount::orderBy('no_account')->get();
        $invoiceItemsJs = $this->buildItemsJs($invoice->items);
        $coaListJs = $coas->map(fn($c) => ['id' => $c->id_md_chart_of_account, 'no' => $c->no_account, 'name' => $c->account_name])->values()->toArray();
        return view('rekap.invoice-other.show', compact('invoice', 'coas', 'invoiceItemsJs', 'coaListJs'));
    }

    public function edit($id)
    {
        $invoice = InvoicePembayaranOther::with([
            'items.joOtherItem.invoice', 'items.joOtherPajak', 'items.coa',
            'joOther.customer', 'joOther.items.invoice', 'joOther.pajakItems',
        ])->findOrFail($id);
        $coas = ChartOfAccount::orderBy('no_account')->get();
        $invoiceItemsJs = $this->buildItemsJs($invoice->items);
        $itemWithUsd = $invoice->joOther?->items?->first(fn($i) => (float)($i->pendapatan_usd ?? 0) > 0);
        $kursUsd     = $itemWithUsd ? (float)$itemWithUsd->kurs_usd : null;
        $coaListJs = $coas->map(fn($c) => ['id' => $c->id_md_chart_of_account, 'no' => $c->no_account, 'name' => $c->account_name])->values()->toArray();
        $vessels    = Vessel::orderBy('vessel_name')->get();
        $ports      = Port::orderBy('name_port')->get();
        $joOthers = JoOther::with(['customer'])->orderByDesc('id_jo_other')->get();
        return view('rekap.invoice-other.edit', compact('invoice', 'coas', 'kursUsd', 'invoiceItemsJs', 'coaListJs', 'vessels', 'ports', 'joOthers'));
    }

    public function update(Request $request, $id)
    {
        $invoice = InvoicePembayaranOther::findOrFail($id);
        DB::beginTransaction();
        try {
            $headerFields = ['no_invoice','tgl_invoice','due_date','eta','etd','kapal','title',
                'vendor_number','po_number','sa_gr_number','ba_number','vat_number',
                'no_voyage','grt','jetty','cond_of_payment','currency','period','bar_code','to_customer',
                'address','att','office_phone','mobile_phone','status','close_note','reopen_note'];
            $updateData = [];
            foreach ($headerFields as $f) {
                if ($request->has($f)) $updateData[$f] = $request->get($f);
            }
            if (!empty($updateData)) $invoice->update($updateData);
            if ($request->has('items')) {
                foreach ($request->items as $itemId => $data) {
                    $invItem = InvoicePembayaranOtherItem::where('id_invoice_other', $invoice->id_invoice_other)
                        ->where('id_invoice_other_item', $itemId)->first();
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
            return back()->with('success', 'Invoice Other successfully updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }

    public function updateHeader(Request $request, $id)
    {
        $invoice = InvoicePembayaranOther::findOrFail($id);
        DB::beginTransaction();
        try {
            $newJoId   = $request->get('id_jo_other');
            $joChanged = $newJoId && $newJoId !== $invoice->id_jo_other;
            $invoice->update($request->only([
                'id_jo_other','no_invoice','tgl_invoice','due_date','status','title','po_number',
                'currency','vendor_number','sa_gr_number','ba_number','eta','etd','no_voyage',
                'grt','kapal','jetty','cond_of_payment','period','att','address','vat_number',
                'bar_code','office_phone','mobile_phone','close_note','reopen_note','to_customer',
                'id_md_chart_of_account','kurs_invoice','tgl_kurs_invoice',
            ]));
            if ($joChanged) {
                $jo = JoOther::with(['items', 'pajakItems'])->find($newJoId);
                if ($jo) {
                    $invoice->items()->delete();
                    foreach ($jo->items as $joItem) {
                        $invoice->items()->create(['id_jo_other_item' => $joItem->id_jo_other_item, 'id_jo_other_pajak' => null, 'item_type' => 'item', 'nominal_invoice' => 0, 'is_reimbursement' => 0, 'id_md_chart_of_account' => null]);
                    }
                    foreach ($jo->pajakItems as $pajak) {
                        $invoice->items()->create(['id_jo_other_item' => null, 'id_jo_other_pajak' => $pajak->id_jo_other_pajak, 'item_type' => 'tax', 'nominal_invoice' => 0, 'is_reimbursement' => 0, 'id_md_chart_of_account' => null]);
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

    public function bulkSaveItems(Request $request, $id)
    {
        $invoice = InvoicePembayaranOther::findOrFail($id);
        DB::beginTransaction();
        try {
            foreach ($request->input('items', []) as $itemData) {
                $item = InvoicePembayaranOtherItem::where('id_invoice_other', $invoice->id_invoice_other)
                    ->where('id_invoice_other_item', $itemData['id_invoice_other_item'])->first();
                if (!$item) continue;
                $item->update([
                    'nominal_invoice'        => (float) ($itemData['nominal_invoice'] ?? 0),
                    'id_md_chart_of_account' => $itemData['id_md_chart_of_account'] ?: null,
                    'is_reimbursement'       => (bool) ($itemData['is_reimbursement'] ?? false),
                ]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Item saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $invoice = InvoicePembayaranOther::findOrFail($id);
        $invoice->delete();
        return redirect()->route('invoice-other.index')->with('success', 'Invoice Other successfully deleted.');
    }

    public function getJoInfoCreate(Request $request) { return $this->getJoInfo($request, null); }

    public function getJoInfo(Request $request, $id)
    {
        try {
        $joId = $request->get('id_jo_other');
        \Illuminate\Support\Facades\Log::info('getJoInfo called', ['joId' => $joId]);
        $jo   = JoOther::with(['customer', 'items.invoice', 'pajakItems'])->find($joId);
        \Illuminate\Support\Facades\Log::info('getJoInfo jo loaded', ['found' => !!$jo, 'items' => $jo ? $jo->items->count() : 0]);
        if (!$jo) return response()->json(['success' => false, 'message' => 'JO not found'], 404);
        $kursUsd  = null;
        $itemKurs = $jo->items->whereNotNull('kurs_usd')->where('kurs_usd', '>', 0)->first();
        if ($itemKurs) $kursUsd = $itemKurs->kurs_usd;
        $items = $jo->items->map(fn($i) => [
            'category' => $i->invoice?->invoice_ctg ?? '-',
            'name'     => $i->invoice?->invoice_typ ?? '-',
            'sell_idr' => number_format($i->hargajual_idr ?? 0, 2, ',', '.'),
            'pend_idr' => number_format($i->pendapatan_idr ?? 0, 2, ',', '.'),
            'pend_usd' => number_format($i->pendapatan_usd ?? 0, 2, ',', '.'),
            'kurs_usd' => $i->kurs_usd ? number_format($i->kurs_usd, 0, ',', '.') : null,
            'note'     => $i->note ?? null,
        ]);
        $pajakItems = $jo->pajakItems->map(fn($p) => [
            'name'        => $p->nama_pajak ?? '-',
            'persen'      => $p->persentase ?? null,
            'nominal_idr' => number_format($p->nominal_pajak ?? 0, 2, ',', '.'),
            'nominal_hpp' => number_format($p->nominal_pajak_hpp ?? 0, 2, ',', '.'),
        ]);
        // ── Kasbon ──
        $kasbons = KasbonOther::with('items')
            ->where('id_jo_other', $joId)->get();
        $kasbonList = $kasbons->map(fn($k) => [
            'nomor'  => $k->nomor ?? $k->id_kasbon_other ?? '-',
            'tgl'    => $k->tgl_kasbon ? $k->tgl_kasbon->format('d M Y') : '-',
            'status' => $k->ca_release_status ?? '-',
            'total'  => number_format($k->items->sum('total_kasbon'), 2, ',', '.'),
        ]);
        $kasbonTotal = $kasbons->sum(fn($k) => $k->items->sum('total_kasbon'));

        // ── LPJ ──
        $lpjList = LpjOther::with('items')
            ->where('id_jo_other', $joId)->get()->map(fn($l) => [
                'no_lpj'    => $l->no_lpj_other ?? '-',
                'tgl'       => $l->date ? \Carbon\Carbon::parse($l->date)->format('d M Y') : '-',
                'ca_amount' => number_format($l->amount ?? 0, 2, ',', '.'),
                'pj_amount' => number_format($l->items->sum('amount_lpj'), 2, ',', '.'),
                'balance'   => number_format(($l->amount ?? 0) - $l->items->sum('amount_lpj'), 2, ',', '.'),
            ]);

        // ── Mutasi Penerimaan ──
        $mutasiPenerimaan = MutasiPenerimaanDetail::with('voucher.mutasiPenerimaan')
            ->where('id_jo_other', $joId)->get()->map(fn($d) => [
                'nomor' => $d->voucher?->mutasiPenerimaan?->nomor ?? '-',
                'tgl'   => $d->voucher?->mutasiPenerimaan?->tanggal
                            ? \Carbon\Carbon::parse($d->voucher->mutasiPenerimaan->tanggal)->format('d M Y') : '-',
                'tipe'  => $d->tipe_pembayaran ?? '-',
                'nilai' => number_format($d->nilai ?? 0, 2, ',', '.'),
            ]);

        // ── Mutasi Pembayaran ──
        $mutasiPembayaran = MutasiPembayaranDetail::with('voucher.mutasiPembayaran')
            ->where('id_jo_other', $joId)->get()->map(fn($d) => [
                'nomor' => $d->voucher?->mutasiPembayaran?->nomor ?? '-',
                'tgl'   => $d->voucher?->mutasiPembayaran?->tanggal
                            ? \Carbon\Carbon::parse($d->voucher->mutasiPembayaran->tanggal)->format('d M Y') : '-',
                'jenis' => $d->jenis ?? '-',
                'nilai' => number_format($d->nilai ?? 0, 2, ',', '.'),
            ]);

        return response()->json([
            'success'           => true,
            'no_jo_other'       => $jo->no_jo_other,
            'title'             => $jo->title,
            'customer'          => $jo->customer?->customer ?? '-',
            'kurs_usd'          => $kursUsd ? number_format($kursUsd, 0, ',', '.') : null,
            'items'             => $items,
            'pajak_items'       => $pajakItems,
            'kasbon'            => $kasbonList,
            'kasbon_total'      => number_format($kasbonTotal, 2, ',', '.'),
            'lpj'               => $lpjList,
            'mutasi_penerimaan' => $mutasiPenerimaan,
            'mutasi_pembayaran' => $mutasiPembayaran,
        ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getJoInfo error', ['msg' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function changeJo(Request $request, $id)
    {
        $invoice = InvoicePembayaranOther::findOrFail($id);
        $request->validate(['id_jo_other' => 'required']);
        $jo = JoOther::with(['customer', 'items', 'pajakItems'])->find($request->id_jo_other);
        if (!$jo) return response()->json(['success' => false, 'message' => 'JO not found'], 404);
        DB::beginTransaction();
        try {
            $invoice->update(['id_jo_other' => $jo->id_jo_other, 'no_jo' => $jo->no_jo_other, 'title' => $jo->title, 'to_customer' => $jo->customer?->customer ?? $invoice->to_customer]);
            $invoice->items()->delete();
            foreach ($jo->items as $joItem) {
                $invoice->items()->create(['id_jo_other_item' => $joItem->id_jo_other_item, 'id_jo_other_pajak' => null, 'nominal_invoice' => 0, 'item_type' => 'item', 'is_reimbursement' => 0, 'id_md_chart_of_account' => null]);
            }
            foreach ($jo->pajakItems as $pajak) {
                $invoice->items()->create(['id_jo_other_item' => null, 'id_jo_other_pajak' => $pajak->id_jo_other_pajak, 'nominal_invoice' => 0, 'item_type' => 'tax', 'is_reimbursement' => 0, 'id_md_chart_of_account' => null]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'JO changed successfully. Items have been reset.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function exportVda($id)
    {
        try {
            $invoice = InvoicePembayaranOther::with(['items.joOtherItem.invoice', 'items.joOtherPajak', 'joOther.customer', 'coa'])->findOrFail($id);
            $grandTotal = $invoice->items->sum('nominal_invoice');
            $terbilang  = $this->toTerbilang((int) round($grandTotal)) . ' Rupiah';
            $companyProfile = CompanyProfile::first();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rekap.invoice-other.vda-pdf', compact('invoice', 'terbilang', 'companyProfile'))
                ->setPaper('a4', 'portrait')->setOptions(['isHtml5ParserEnabled' => false, 'isRemoteEnabled' => false, 'defaultFont' => 'Arial', 'dpi' => 150]);
            $noInv = str_replace(['/', '\\'], '-', $invoice->no_invoice ?? $id);
            return $pdf->stream('VDA-' . $noInv . '.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Invoice Other not found');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('VDA Other PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export VDA PDF: ' . $e->getMessage());
        }
    }

    public function exportInvoice($id)
    {
        try {
            $invoice = InvoicePembayaranOther::with(['items.joOtherItem.invoice', 'items.joOtherPajak', 'joOther.customer', 'customer', 'coa'])->findOrFail($id);
            $items = $invoice->items;
            $regular  = $items->where('item_type', 'item')->where('is_reimbursement', false);
            $reimb    = $items->where('item_type', 'item')->where('is_reimbursement', true);
            $taxItems = $items->where('item_type', 'tax');
            $dpp = (float) $regular->sum('nominal_invoice');
            $reimbTotal = (float) $reimb->sum('nominal_invoice');
            $ppnTotal = (float) $taxItems->sum('nominal_invoice');
            $subTotal = $dpp + $reimbTotal;
            $totalTagihan = $subTotal + $ppnTotal;
            $ppnPct = $taxItems->first()?->joOtherPajak?->persentase ?? 11;
            $terbilang = $this->toTerbilang((int) round($totalTagihan)) . ' Rupiah';
            $companyProfile = CompanyProfile::first();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rekap.invoice-other.invoice-pdf', compact('invoice', 'terbilang', 'dpp', 'reimbTotal', 'ppnTotal', 'subTotal', 'totalTagihan', 'ppnPct', 'companyProfile'))
                ->setPaper('a4', 'portrait')->setOptions(['isHtml5ParserEnabled' => false, 'isRemoteEnabled' => false, 'defaultFont' => 'Arial', 'dpi' => 150]);
            $noInv = str_replace(['/', chr(92)], '-', $invoice->no_invoice ?? $id);
            return $pdf->stream('INVOICE-' . $noInv . '.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Invoice Other not found');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Invoice Other PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export Invoice PDF: ' . $e->getMessage());
        }
    }

    public function exportReimbursement($id)
    {
        try {
            $invoice = InvoicePembayaranOther::with(['items.joOtherItem.invoice', 'items.joOtherPajak', 'joOther.customer', 'customer', 'coa'])->findOrFail($id);
            $reimbItems = $invoice->items->where('item_type', 'item')->where('is_reimbursement', true)->values();
            $grandTotal = (float) $reimbItems->sum('nominal_invoice');
            $terbilang  = $this->toTerbilang((int) round($grandTotal)) . ' Rupiah';
            $companyProfile = CompanyProfile::first();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rekap.invoice-other.reimbursement-pdf', compact('invoice', 'terbilang', 'companyProfile'))
                ->setPaper('a4', 'portrait')->setOptions(['isHtml5ParserEnabled' => false, 'isRemoteEnabled' => false, 'defaultFont' => 'Arial', 'dpi' => 150]);
            $noInv = str_replace(['/', chr(92)], '-', $invoice->no_invoice ?? $id);
            return $pdf->stream('REIMBURSEMENT-' . $noInv . '.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Invoice Other not found');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Reimbursement Other PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export Reimbursement PDF: ' . $e->getMessage());
        }
    }

    public function exportKwitansi($id)
    {
        try {
            $invoice = InvoicePembayaranOther::with(['items.joOtherItem.invoice', 'items.joOtherPajak', 'joOther.customer', 'customer', 'coa'])->findOrFail($id);
            $companyProfile = CompanyProfile::first();
            $terbilangFn = fn(int $n): string => $this->toTerbilang($n);
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rekap.invoice-other.kwitansi-pdf', compact('invoice', 'companyProfile', 'terbilangFn'))
                ->setPaper('a4', 'portrait')->setOptions(['isHtml5ParserEnabled' => false, 'isRemoteEnabled' => false, 'defaultFont' => 'Arial', 'dpi' => 150]);
            $noInv = str_replace(['/', chr(92)], '-', $invoice->no_invoice ?? $id);
            return $pdf->stream('KWITANSI-' . $noInv . '.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Invoice Other not found');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Kwitansi Other PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export Kwitansi PDF: ' . $e->getMessage());
        }
    }

    public function exportAll($id)
    {
        try {
            $invoice = InvoicePembayaranOther::with(['items.joOtherItem.invoice', 'items.joOtherPajak', 'joOther.customer', 'customer', 'coa'])->findOrFail($id);
            $items = $invoice->items;
            $regular  = $items->where('item_type', 'item')->where('is_reimbursement', false);
            $reimb    = $items->where('item_type', 'item')->where('is_reimbursement', true);
            $taxItems = $items->where('item_type', 'tax');
            $dpp = (float) $regular->sum('nominal_invoice');
            $reimbTotal = (float) $reimb->sum('nominal_invoice');
            $ppnTotal = (float) $taxItems->sum('nominal_invoice');
            $subTotal = $dpp + $reimbTotal;
            $totalTagihan = $subTotal + $ppnTotal;
            $ppnPct = $taxItems->first()?->joOtherPajak?->persentase ?? 11;
            $grandVda = (float) $items->sum('nominal_invoice');
            $grandReimb = (float) $reimb->sum('nominal_invoice');
            $companyProfile = CompanyProfile::first();
            $htmlVda = view('rekap.invoice-other.vda-pdf', ['invoice' => $invoice, 'terbilang' => $this->toTerbilang((int) round($grandVda)) . ' Rupiah', 'companyProfile' => $companyProfile])->render();
            $htmlInvoice = view('rekap.invoice-other.invoice-pdf', array_merge(compact('invoice', 'dpp', 'reimbTotal', 'ppnTotal', 'subTotal', 'totalTagihan', 'ppnPct', 'companyProfile'), ['terbilang' => $this->toTerbilang((int) round($totalTagihan)) . ' Rupiah']))->render();
            $htmlReimb = view('rekap.invoice-other.reimbursement-pdf', ['invoice' => $invoice, 'terbilang' => $this->toTerbilang((int) round($grandReimb)) . ' Rupiah', 'companyProfile' => $companyProfile])->render();
            $stripOuter = fn(string $h): string => preg_match('/<body[^>]*>(.*?)<\/body>/si', $h, $m) ? $m[1] : $h;
            $extractStyles = function (string $h): string { preg_match_all('/<style[^>]*>(.*?)<\/style>/si', $h, $m); return implode("\n", $m[1]); };
            $pb = '<div style="page-break-before:always;margin:0;padding:0;"></div>';
            $combinedHtml = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'
                . '<style>' . $extractStyles($htmlVda) . '</style><style>' . $extractStyles($htmlInvoice) . '</style><style>' . $extractStyles($htmlReimb) . '</style>'
                . '<style>@page{size:A4 portrait;margin:0;}*{box-sizing:border-box;}body{margin:0!important;padding:0!important;font-family:Arial,sans-serif;font-size:9pt;color:#000;background:#fff;}.pdf-page{padding:14mm;}</style>'
                . '</head><body><div class="pdf-page">' . $stripOuter($htmlVda) . '</div>' . $pb
                . '<div class="pdf-page">' . $stripOuter($htmlInvoice) . '</div>' . $pb
                . '<div class="pdf-page">' . $stripOuter($htmlReimb) . '</div></body></html>';
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHtml($combinedHtml)->setPaper('a4', 'portrait')
                ->setOptions(['isHtml5ParserEnabled' => false, 'isRemoteEnabled' => false, 'defaultFont' => 'Arial', 'dpi' => 150]);
            $noInv = str_replace(['/', '\\'], '-', $invoice->no_invoice ?? $id);
            return $pdf->stream('ALL-' . $noInv . '.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Invoice Other not found');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Export All Other PDF Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export All PDF: ' . $e->getMessage());
        }
    }

    private function buildItemsJs($items)
    {
        return $items->values()->map(function ($item) {
            $isTax = $item->item_type === 'tax';
            return [
                'id_invoice_other_item' => $item->id_invoice_other_item,
                'item_type'               => $item->item_type,
                'invoice_ctg'             => $isTax ? '__TAX__' : ($item->joOtherItem?->invoice?->invoice_ctg ?? 'Uncategorized'),
                'invoice_typ'             => $isTax ? ($item->deskripsi ?? $item->joOtherPajak?->nama_pajak ?? ('Pajak #' . $item->id_invoice_other_item)) : ($item->joOtherItem?->invoice?->invoice_typ ?? ('Item #' . $item->id_invoice_other_item)),
                'sell_idr'                => $isTax ? (float)($item->joOtherPajak?->nominal_pajak ?? 0) : (float)($item->joOtherItem?->hargajual_idr ?? 0),
                'sell_usd'                => (float)($item->joOtherItem?->pendapatan_usd ?? 0),
                'nominal_invoice'         => (float)$item->nominal_invoice,
                'nominal_invoice_usd'     => (float)($item->nominal_invoice_usd ?? 0),
                'id_md_chart_of_account'  => $item->id_md_chart_of_account,
                'coa_no'                  => $item->coa?->no_account,
                'coa_name'                => $item->coa?->account_name,
                'is_reimbursement'        => (bool)$item->is_reimbursement,
                'note'                    => $item->joOtherItem?->note ?? null,
                'persen_pajak'            => $isTax ? ($item->joOtherPajak?->persentase ?? null) : null,
            ];
        });
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
