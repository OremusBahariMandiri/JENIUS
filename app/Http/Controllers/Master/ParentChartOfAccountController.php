<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\ParentChartOfAccount;
use App\Models\Master\CostType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ParentChartOfAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:parent_coa')->only('index');
        $this->middleware('check.access:parent_coa,detail')->only('show');
        $this->middleware('check.access:parent_coa,tambah')->only('create', 'store');
        $this->middleware('check.access:parent_coa,ubah')->only('edit', 'update');
        $this->middleware('check.access:parent_coa,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = ParentChartOfAccount::with('costType');

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('kode_perkiraan', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhereHas('costType', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            }

            $sortBy    = $request->get('sort_by', 'kode_perkiraan');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $parentCoas = $query->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data'    => $parentCoas
                ]);
            }

            return view('master.parent_coa.index', compact('parentCoas'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving data: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving data: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $costTypes = CostType::orderBy('name')->get();
        return view('master.parent_coa.create', compact('costTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_md_cost_type' => 'required|string|exists:a11_md_cost_type,id_md_cost_type',
            'kode_perkiraan'  => 'required|string|max:50|unique:a12_md_parent_chart_of_account,kode_perkiraan',
            'nama'            => 'required|string|max:255',
        ], [
            'id_md_cost_type.required' => 'Tipe akun wajib dipilih',
            'id_md_cost_type.exists'   => 'Tipe akun tidak ditemukan',
            'kode_perkiraan.required'  => 'Kode perkiraan wajib diisi',
            'kode_perkiraan.unique'    => 'Kode perkiraan sudah digunakan',
            'nama.required'            => 'Nama akun wajib diisi',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $parentCoa = ParentChartOfAccount::create([
                'id_md_cost_type' => $request->id_md_cost_type,
                'kode_perkiraan'  => $request->kode_perkiraan,
                'nama'            => $request->nama,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Parent COA berhasil ditambahkan',
                    'data'    => $parentCoa->load('costType')
                ], 201);
            }

            return redirect()->route('parent-coa.index')->with('success', 'Parent COA berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating data: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Error creating data: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $parentCoa = ParentChartOfAccount::with('costType')->findOrFail($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data'    => $parentCoa
                ]);
            }

            return view('master.parent_coa.show', compact('parentCoa'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ], 404);
            }

            return back()->with('error', 'Data not found');
        }
    }

    public function edit($id)
    {
        try {
            $parentCoa = ParentChartOfAccount::findOrFail($id);
            $costTypes  = CostType::orderBy('name')->get();

            return view('master.parent_coa.edit', compact('parentCoa', 'costTypes'));
        } catch (\Exception $e) {
            return back()->with('error', 'Data not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_md_cost_type' => 'required|string|exists:a11_md_cost_type,id_md_cost_type',
            'kode_perkiraan'  => 'required|string|max:50|unique:a12_md_parent_chart_of_account,kode_perkiraan,' . $id,
            'nama'            => 'required|string|max:255',
        ], [
            'id_md_cost_type.required' => 'Tipe akun wajib dipilih',
            'id_md_cost_type.exists'   => 'Tipe akun tidak ditemukan',
            'kode_perkiraan.required'  => 'Kode perkiraan wajib diisi',
            'kode_perkiraan.unique'    => 'Kode perkiraan sudah digunakan',
            'nama.required'            => 'Nama akun wajib diisi',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $parentCoa = ParentChartOfAccount::findOrFail($id);

            $parentCoa->update([
                'id_md_cost_type' => $request->id_md_cost_type,
                'kode_perkiraan'  => $request->kode_perkiraan,
                'nama'            => $request->nama,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Parent COA berhasil diupdate',
                    'data'    => $parentCoa->load('costType')
                ]);
            }

            return redirect()->route('parent-coa.index')->with('success', 'Parent COA berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating data: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Error updating data: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $parentCoa = ParentChartOfAccount::findOrFail($id);
            $parentCoa->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Parent COA berhasil dihapus'
                ]);
            }

            return redirect()->route('parent-coa.index')->with('success', 'Parent COA berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting data: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting data: ' . $e->getMessage());
        }
    }

    /**
     * Get for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $query = ParentChartOfAccount::with('costType')->orderBy('kode_perkiraan');

            // Filter by cost type jika ada
            if ($request->has('id_md_cost_type') && !empty($request->id_md_cost_type)) {
                $query->where('id_md_cost_type', $request->id_md_cost_type);
            }

            $data = $query->get()->map(fn($p) => [
                'id'              => $p->kode_perkiraan,
                'text'            => "[{$p->kode_perkiraan}] {$p->nama}",
                'kode_perkiraan'  => $p->kode_perkiraan,
                'nama'            => $p->nama,
                'tipe_akun'       => $p->costType?->name,
                'id_md_cost_type' => $p->id_md_cost_type,
            ]);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:a12_md_parent_chart_of_account,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            ParentChartOfAccount::whereIn('id', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Parent COA berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting data: ' . $e->getMessage()
            ], 500);
        }
    }
}