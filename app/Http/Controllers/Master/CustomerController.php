<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Customer::query();

            // Include trashed records if requested
            if ($request->has('with_trashed') && $request->with_trashed) {
                $query->withTrashed();
            }

            // Only trashed records if requested
            if ($request->has('only_trashed') && $request->only_trashed) {
                $query->onlyTrashed();
            }

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhere('customer', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('npwp', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $customers = $query->paginate($perPage);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $customers
                ]);
            }

            return view('master.customer.index', compact('customers'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving customers: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving customers: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.customer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:a01_md_customer,code',
            'customer' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:255',
            'npwp' => 'nullable|string|max:50',
            'note' => 'nullable|string',
        ], [
            'code.required' => 'Kode customer wajib diisi',
            'code.unique' => 'Kode customer sudah digunakan',
            'customer.required' => 'Nama customer wajib diisi',
            'email.email' => 'Format email tidak valid',
            'website.url' => 'Format website tidak valid',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Generate ID
            $lastCustomer = Customer::withTrashed()->orderBy('id_md_cust', 'desc')->first();
            $newId = $lastCustomer ? $lastCustomer->id_md_cust + 1 : 1;

            $customer = Customer::create([
                'id_md_cust' => $newId,
                'code' => strtoupper($request->code),
                'customer' => $request->customer,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'website' => $request->website,
                'npwp' => $request->npwp,
                'note' => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer berhasil ditambahkan',
                    'data' => $customer
                ], 201);
            }

            return redirect()
                ->route('customer.index')
                ->with('success', 'Customer berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating customer: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating customer: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $query = Customer::with(['contracts.joContracts']);

            if ($request->has('with_trashed') && $request->with_trashed) {
                $query->withTrashed();
            }

            $customer = $query->findOrFail($id);

            // Calculate summary
            $summary = [
                'total_contracts' => $customer->contracts->count(),
                'active_contracts' => $customer->contracts->filter(function($contract) {
                    return $contract->date_end >= now();
                })->count(),
                'total_expenditure' => $customer->contracts->sum('expenditure'),
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $customer,
                    'summary' => $summary
                ]);
            }

            return view('master.customer.show', compact('customer', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            return back()->with('error', 'Customer not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            return view('master.customer.edit', compact('customer'));
        } catch (\Exception $e) {
            return back()->with('error', 'Customer not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:a01_md_customer,code,' . $id . ',id_md_cust,deleted_at,NULL',
            'customer' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:255',
            'npwp' => 'nullable|string|max:50',
            'note' => 'nullable|string',
        ], [
            'code.required' => 'Kode customer wajib diisi',
            'code.unique' => 'Kode customer sudah digunakan',
            'customer.required' => 'Nama customer wajib diisi',
            'email.email' => 'Format email tidak valid',
            'website.url' => 'Format website tidak valid',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($id);

            $customer->update([
                'code' => strtoupper($request->code),
                'customer' => $request->customer,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'website' => $request->website,
                'npwp' => $request->npwp,
                'note' => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer berhasil diupdate',
                    'data' => $customer
                ]);
            }

            return redirect()
                ->route('customer.index')
                ->with('success', 'Customer berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating customer: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating customer: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($id);

            // Check if customer has contracts
            if ($customer->contracts()->count() > 0) {
                DB::rollBack();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Customer tidak dapat dihapus karena masih memiliki kontrak terkait'
                    ], 422);
                }

                return back()->with('error', 'Customer tidak dapat dihapus karena masih memiliki kontrak terkait');
            }

            // Soft delete
            $customer->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer berhasil dihapus'
                ]);
            }

            return redirect()
                ->route('customer.index')
                ->with('success', 'Customer berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting customer: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting customer: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft deleted customer
     */
    public function restore(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $customer = Customer::withTrashed()->findOrFail($id);

            if (!$customer->trashed()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Customer tidak dalam status terhapus'
                    ], 422);
                }

                return back()->with('error', 'Customer tidak dalam status terhapus');
            }

            $customer->restore();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer berhasil dipulihkan',
                    'data' => $customer
                ]);
            }

            return redirect()
                ->route('customer.index')
                ->with('success', 'Customer berhasil dipulihkan');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error restoring customer: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error restoring customer: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a customer
     */
    public function forceDelete(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $customer = Customer::withTrashed()->findOrFail($id);

            // Check if customer has contracts
            if ($customer->contracts()->count() > 0) {
                DB::rollBack();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Customer tidak dapat dihapus permanen karena masih memiliki kontrak terkait'
                    ], 422);
                }

                return back()->with('error', 'Customer tidak dapat dihapus permanen karena masih memiliki kontrak terkait');
            }

            // Force delete (permanent)
            $customer->forceDelete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer berhasil dihapus permanen'
                ]);
            }

            return redirect()
                ->route('customer.index')
                ->with('success', 'Customer berhasil dihapus permanen');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error permanently deleting customer: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error permanently deleting customer: ' . $e->getMessage());
        }
    }

    /**
     * Get customers for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $customers = Customer::select('id_md_cust', 'code', 'customer')
                ->orderBy('customer', 'asc')
                ->get()
                ->map(function($customer) {
                    return [
                        'id' => $customer->id_md_cust,
                        'text' => $customer->code . ' - ' . $customer->customer
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving customers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete customers
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:a01_md_customer,id_md_cust'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $customers = Customer::whereIn('id_md_cust', $request->ids)->get();

            // Check if any customer has contracts
            $hasRelations = false;
            foreach ($customers as $customer) {
                if ($customer->contracts()->count() > 0) {
                    $hasRelations = true;
                    break;
                }
            }

            if ($hasRelations) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa customer tidak dapat dihapus karena masih memiliki kontrak terkait'
                ], 422);
            }

            Customer::whereIn('id_md_cust', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customers berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting customers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk restore customers
     */
    public function bulkRestore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            Customer::withTrashed()
                ->whereIn('id_md_cust', $request->ids)
                ->restore();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customers berhasil dipulihkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error restoring customers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export customers to Excel/CSV
     */
    public function export(Request $request)
    {
        try {
            $query = Customer::query();

            if ($request->has('with_trashed') && $request->with_trashed) {
                $query->withTrashed();
            }

            $customers = $query->orderBy('customer', 'asc')->get();

            // Here you would implement export logic using Laravel Excel or similar
            // For now, returning JSON
            return response()->json([
                'success' => true,
                'data' => $customers,
                'message' => 'Export feature - implement with Laravel Excel'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting customers: ' . $e->getMessage()
            ], 500);
        }
    }
}