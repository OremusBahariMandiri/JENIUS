<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:area')->only('index');
        $this->middleware('check.access:area,detail')->only('show');
        $this->middleware('check.access:area,tambah')->only('create', 'store');
        $this->middleware('check.access:area,ubah')->only('edit', 'update');
        $this->middleware('check.access:area,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = Area::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('area', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $areas = $query->paginate($perPage);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $areas
                ]);
            }

            return view('master.area.index', compact('areas'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving areas: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving areas: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.area.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'code' => 'required|string|max:50|unique:a03_md_area,code',
            'area' => 'required|string|max:255',
            'note' => 'nullable|string',
        ], [
            // 'code.required' => 'Kode area wajib diisi',
            // 'code.unique' => 'Kode area sudah digunakan',
            'area.required' => 'Nama area wajib diisi',
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
            $lastArea = Area::orderBy('id_md_area', 'desc')->first();
            $newId = $lastArea ? $lastArea->id_md_area + 1 : 1;

            $area = Area::create([
                'id_md_area' => $newId,
                'code' => strtoupper($request->code),
                'area' => $request->area,
                'note' => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Area berhasil ditambahkan',
                    'data' => $area
                ], 201);
            }

            return redirect()
                ->route('area.index')
                ->with('success', 'Area berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating area: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating area: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $area = Area::with(['joContracts.contract.customer', 'joContracts.department'])
                ->findOrFail($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $area
                ]);
            }

            return view('master.area.show', compact('area'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Area not found'
                ], 404);
            }

            return back()->with('error', 'Area not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $area = Area::findOrFail($id);
            return view('master.area.edit', compact('area'));
        } catch (\Exception $e) {
            return back()->with('error', 'Area not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // 'code' => 'required|string|max:50|unique:a03_md_area,code,' . $id . ',id_md_area',
            'area' => 'required|string|max:255',
            'note' => 'nullable|string',
        ], [
            // 'code.required' => 'Kode area wajib diisi',
            // 'code.unique' => 'Kode area sudah digunakan',
            'area.required' => 'Nama area wajib diisi',
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
            $area = Area::findOrFail($id);

            $area->update([
                'code' => strtoupper($request->code),
                'area' => $request->area,
                'note' => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Area berhasil diupdate',
                    'data' => $area
                ]);
            }

            return redirect()
                ->route('area.index')
                ->with('success', 'Area berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating area: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating area: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $area = Area::findOrFail($id);

            // Check if area has related jo_contracts
            if ($area->joContracts()->count() > 0) {
                DB::rollBack();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Area tidak dapat dihapus karena masih memiliki kontrak terkait'
                    ], 422);
                }

                return back()->with('error', 'Area tidak dapat dihapus karena masih memiliki kontrak terkait');
            }

            $area->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Area berhasil dihapus'
                ]);
            }

            return redirect()
                ->route('area.index')
                ->with('success', 'Area berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting area: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting area: ' . $e->getMessage());
        }
    }

    /**
     * Get areas for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $areas = Area::select('id_md_area', 'code', 'area')
                ->orderBy('area', 'asc')
                ->get()
                ->map(function ($area) {
                    return [
                        'id' => $area->id_md_area,
                        'text' => $area->code . ' - ' . $area->area
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $areas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving areas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete areas
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:a03_md_area,id_md_area'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $areas = Area::whereIn('id_md_area', $request->ids)->get();

            // Check if any area has related contracts
            $hasRelations = false;
            foreach ($areas as $area) {
                if ($area->joContracts()->count() > 0) {
                    $hasRelations = true;
                    break;
                }
            }

            if ($hasRelations) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa area tidak dapat dihapus karena masih memiliki kontrak terkait'
                ], 422);
            }

            Area::whereIn('id_md_area', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Areas berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting areas: ' . $e->getMessage()
            ], 500);
        }
    }
}
