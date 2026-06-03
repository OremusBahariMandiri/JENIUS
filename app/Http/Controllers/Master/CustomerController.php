<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:customer')->only('index');
        $this->middleware('check.access:customer,detail')->only('show');
        $this->middleware('check.access:customer,tambah')->only('create', 'store');
        $this->middleware('check.access:customer,ubah')->only('edit', 'update');
        $this->middleware('check.access:customer,hapus')->only('destroy', 'bulkDelete');
    }

    /**
     * Build base query with filter applied (reusable).
     */
    private function buildFilteredQuery(Request $request)
    {
        $query = Customer::query();

        if ($request->has('with_trashed') && $request->with_trashed) {
            $query->withTrashed();
        }
        if ($request->has('only_trashed') && $request->only_trashed) {
            $query->onlyTrashed();
        }

        // Search / filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('customer', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('npwp', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        try {
            $query = $this->buildFilteredQuery($request);

            // Sorting
            $sortBy    = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Current active filters (for view)
            $currentFilters = [
                'search' => $request->get('search', ''),
            ];

            // Pagination
            $perPage   = $request->get('per_page', 15);
            $customers = $query->paginate($perPage)->withQueryString();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data'    => $customers,
                ]);
            }

            return view('master.customer.index', compact('customers', 'currentFilters'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving customers: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Error retrieving customers: ' . $e->getMessage());
        }
    }

    /**
     * Export customers — supports format=excel|pdf, respects active filter.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');

        try {
            $customers = $this->buildFilteredQuery($request)
                ->orderBy('customer', 'asc')
                ->get();

            if ($format === 'pdf') {
                return $this->exportPdf($customers);
            }

            // Default: Excel
            return $this->exportExcel($customers);
        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting customers: ' . $e->getMessage());
        }
    }

    /**
     * Export to Excel using PhpSpreadsheet (no Laravel Excel package needed).
     */
    private function exportExcel($customers)
    {
        // Use PhpSpreadsheet if available, otherwise fallback to CSV
        if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return $this->exportXlsx($customers);
        }

        // Fallback: CSV
        $filename = 'customers_' . date('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($customers) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, ['No', 'Customer', 'Email', 'Phone', 'NPWP', 'Address', 'Website', 'Note', 'Created At']);

            foreach ($customers as $i => $customer) {
                fputcsv($handle, [
                    $i + 1,
                    $customer->customer     ?? '',
                    $customer->email        ?? '',
                    $customer->phone        ?? '',
                    $customer->npwp         ?? '',
                    $customer->address      ?? '',
                    $customer->website      ?? '',
                    $customer->note         ?? '',
                    $customer->created_at ? $customer->created_at->format('d/m/Y H:i') : '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to XLSX using PhpSpreadsheet.
     */
    private function exportXlsx($customers)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Customer');

        // Header
        $headers = ['No', 'Customer', 'Email', 'Phone', 'NPWP', 'Address', 'Website', 'Note', 'Created At'];
        foreach ($headers as $col => $header) {
            $cellCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue("{$cellCol}1", $header);
            $sheet->getStyle("{$cellCol}1")->getFont()->setBold(true);
            $sheet->getStyle("{$cellCol}1")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D1FAE5');
        }

        // Data rows
        foreach ($customers as $i => $customer) {
            $row = $i + 2;
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("C{$row}", $customer->customer   ?? '');
            $sheet->setCellValue("D{$row}", $customer->email      ?? '');
            $sheet->setCellValue("E{$row}", $customer->phone      ?? '');
            $sheet->setCellValue("F{$row}", $customer->npwp       ?? '');
            $sheet->setCellValue("G{$row}", $customer->address    ?? '');
            $sheet->setCellValue("H{$row}", $customer->website    ?? '');
            $sheet->setCellValue("I{$row}", $customer->note       ?? '');
            $sheet->setCellValue("J{$row}", $customer->created_at ? $customer->created_at->format('d/m/Y H:i') : '');
        }

        // Auto-width
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'customers_' . date('Ymd_His') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export to PDF using DomPDF (Laravel default) or fallback HTML.
     */
    private function exportPdf($customers)
    {
        $filename = 'customers_' . date('Ymd_His') . '.pdf';

        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('master.customer.export_pdf', compact('customers'))
                ->setPaper('a4', 'portrait');

            return $pdf->download($filename);
        }

        // Fallback: return printable HTML (opens in browser, user can Ctrl+P)
        $html = $this->buildPdfHtml($customers);
        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Build PDF HTML for fallback — simple B&W portrait.
     */
    private function buildPdfHtml($customers)
    {
        $total     = $customers->count();
        $printedAt = now()->format('d/m/Y H:i');

        $rows = '';
        foreach ($customers as $i => $customer) {
            $bg = ($i % 2 === 1) ? '#f5f5f5' : '#fff';
            $rows .= '<tr style="background:' . $bg . ';">'
                . '<td style="text-align:center;padding:4px 6px;border:1px solid #ccc;">' . ($i + 1) . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . e($customer->code       ?? '-') . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . e($customer->customer   ?? '-') . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . e($customer->email      ?? '-') . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . e($customer->phone      ?? '-') . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . e($customer->npwp       ?? '-') . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #ccc;">' . e(\Str::limit($customer->address ?? '-', 55)) . '</td>'
                . '</tr>';
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customer List</title>
    <style>
        @page { size: A4 portrait; margin: 15mm 12mm; }
        @media print { .no-print { display: none; } }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 20px; }
        h2 { font-size: 13px; font-weight: bold; margin: 0 0 2px 0; }
        p.sub { font-size: 8.5px; color: #555; margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #000; color: #fff; font-size: 8.5px; font-weight: bold; text-transform: uppercase; padding: 5px 6px; border: 1px solid #000; text-align: left; }
        .footer { margin-top: 10px; font-size: 8px; color: #555; text-align: right; }
        .print-btn { position: fixed; top: 12px; right: 12px; padding: 7px 16px; background: #111; color: #fff; border: none; cursor: pointer; font-size: 11px; border-radius: 2px; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Print / Save PDF</button>
    <h2>Customer List</h2>
    <p class="sub">Printed: {$printedAt} &mdash; Total: {$total} records</p>
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="9%">Code</th>
                <th width="22%">Customer Name</th>
                <th width="19%">Email</th>
                <th width="12%">Phone</th>
                <th width="13%">NPWP</th>
                <th width="21%">Address</th>
            </tr>
        </thead>
        <tbody>{$rows}</tbody>
    </table>
    <div class="footer">{$total} customer(s) &mdash; {$printedAt}</div>
</body>
</html>
HTML;
    }

    private function now()
    {
        return now()->format('d/m/Y H:i:s');
    }

    // =====================================================================
    //  REMAINING CRUD METHODS (unchanged from original)
    // =====================================================================

    public function create()
    {
        return view('master.customer.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer' => 'required|string|max:255',
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|max:100',
            'website'  => 'nullable|max:255',
            'npwp'     => 'nullable|string|max:50',
            'note'     => 'nullable|string',
        ], [
            'customer.required' => 'Nama customer wajib diisi',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $lastCustomer = Customer::withTrashed()->orderBy('id_md_cust', 'desc')->first();
            $newId = $lastCustomer ? $lastCustomer->id_md_cust + 1 : 1;

            $customer = Customer::create([
                'id_md_cust' => $newId,
                'code'       => strtoupper($request->code),
                'customer'   => $request->customer,
                'address'    => $request->address,
                'phone'      => $request->phone,
                'email'      => $request->email,
                'website'    => $request->website,
                'npwp'       => $request->npwp,
                'note'       => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Customer berhasil ditambahkan', 'data' => $customer], 201);
            }
            return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error creating customer: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error creating customer: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $query = Customer::with(['contracts.joContracts']);
            if ($request->has('with_trashed') && $request->with_trashed) {
                $query->withTrashed();
            }
            $customer = $query->findOrFail($id);

            $summary = [
                'total_contracts'  => $customer->contracts->count(),
                'active_contracts' => $customer->contracts->filter(fn($c) => $c->date_end >= now())->count(),
                'total_expenditure'=> $customer->contracts->sum('expenditure'),
            ];

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $customer, 'summary' => $summary]);
            }
            return view('master.customer.show', compact('customer', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
            }
            return back()->with('error', 'Customer not found');
        }
    }

    public function edit($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            return view('master.customer.edit', compact('customer'));
        } catch (\Exception $e) {
            return back()->with('error', 'Customer not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer' => 'required|string|max:255',
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|max:100',
            'website'  => 'nullable|max:255',
            'npwp'     => 'nullable|string|max:50',
            'note'     => 'nullable|string',
        ], [
            'customer.required' => 'Nama customer wajib diisi',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($id);
            $customer->update([
                'code'    => strtoupper($request->code),
                'customer'=> $request->customer,
                'address' => $request->address,
                'phone'   => $request->phone,
                'email'   => $request->email,
                'website' => $request->website,
                'npwp'    => $request->npwp,
                'note'    => $request->note,
            ]);
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Customer berhasil diupdate', 'data' => $customer]);
            }
            return redirect()->route('customer.index')->with('success', 'Customer berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating customer: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error updating customer: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($id);

            if ($customer->contracts()->count() > 0) {
                DB::rollBack();
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Customer tidak dapat dihapus karena masih memiliki kontrak terkait'], 422);
                }
                return back()->with('error', 'Customer tidak dapat dihapus karena masih memiliki kontrak terkait');
            }

            $customer->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Customer berhasil dihapus']);
            }
            return redirect()->route('customer.index')->with('success', 'Customer berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting customer: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting customer: ' . $e->getMessage());
        }
    }

    public function restore(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $customer = Customer::withTrashed()->findOrFail($id);
            if (!$customer->trashed()) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Customer tidak dalam status terhapus'], 422);
                }
                return back()->with('error', 'Customer tidak dalam status terhapus');
            }
            $customer->restore();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Customer berhasil dipulihkan', 'data' => $customer]);
            }
            return redirect()->route('customer.index')->with('success', 'Customer berhasil dipulihkan');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error restoring customer: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error restoring customer: ' . $e->getMessage());
        }
    }

    public function forceDelete(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $customer = Customer::withTrashed()->findOrFail($id);
            if ($customer->contracts()->count() > 0) {
                DB::rollBack();
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Customer tidak dapat dihapus permanen karena masih memiliki kontrak terkait'], 422);
                }
                return back()->with('error', 'Customer tidak dapat dihapus permanen karena masih memiliki kontrak terkait');
            }
            $customer->forceDelete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Customer berhasil dihapus permanen']);
            }
            return redirect()->route('customer.index')->with('success', 'Customer berhasil dihapus permanen');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error permanently deleting customer: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error permanently deleting customer: ' . $e->getMessage());
        }
    }

    public function getForSelect(Request $request)
    {
        try {
            $customers = Customer::select('id_md_cust', 'code', 'customer')
                ->orderBy('customer', 'asc')
                ->get()
                ->map(fn($c) => ['id' => $c->id_md_cust, 'text' => $c->code . ' - ' . $c->customer]);

            return response()->json(['success' => true, 'data' => $customers]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving customers: ' . $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:a01_md_customer,id_md_cust',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $customers   = Customer::whereIn('id_md_cust', $request->ids)->get();
            $hasRelations = false;

            foreach ($customers as $customer) {
                if ($customer->contracts()->count() > 0) {
                    $hasRelations = true;
                    break;
                }
            }

            if ($hasRelations) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Beberapa customer tidak dapat dihapus karena masih memiliki kontrak terkait'], 422);
            }

            Customer::whereIn('id_md_cust', $request->ids)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Customers berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting customers: ' . $e->getMessage()], 500);
        }
    }

    public function bulkRestore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            Customer::withTrashed()->whereIn('id_md_cust', $request->ids)->restore();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Customers berhasil dipulihkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error restoring customers: ' . $e->getMessage()], 500);
        }
    }
}