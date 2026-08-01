<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\CostType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\IdGenerator;

class CostTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:cost_type')->only('index');
        $this->middleware('check.access:cost_type,detail')->only('show');
        $this->middleware('check.access:cost_type,tambah')->only('create', 'store');
        $this->middleware('check.access:cost_type,ubah')->only('edit', 'update');
        $this->middleware('check.access:cost_type,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = CostType::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $costTypes = $query->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $costTypes
                ]);
            }

            return view('master.cost_type.index', compact('costTypes'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving cost types: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving cost types: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.cost_type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'note' => 'nullable|string',
        ], [
            'name.required' => 'Nama cost type wajib diisi',
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
            $newId = IdGenerator::generate('A11', 'a11_md_cost_type', 'id_md_cost_type');

            $costType = CostType::create([
                'id_md_cost_type' => $newId,
                'name'            => $request->name,
                'note'            => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cost Type berhasil ditambahkan',
                    'data'    => $costType
                ], 201);
            }

            return redirect()
                ->route('cost-type.index')
                ->with('success', 'Cost Type berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating cost type: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating cost type: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $costType = CostType::findOrFail($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data'    => $costType
                ]);
            }

            return view('master.cost_type.show', compact('costType'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cost Type not found'
                ], 404);
            }

            return back()->with('error', 'Cost Type not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $costType = CostType::findOrFail($id);

            return view('master.cost_type.edit', compact('costType'));
        } catch (\Exception $e) {
            return back()->with('error', 'Cost Type not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'note' => 'nullable|string',
        ], [
            'name.required' => 'Nama cost type wajib diisi',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $costType = CostType::findOrFail($id);

            $costType->update([
                'name' => $request->name,
                'note' => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cost Type berhasil diupdate',
                    'data'    => $costType
                ]);
            }

            return redirect()
                ->route('cost-type.index')
                ->with('success', 'Cost Type berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating cost type: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating cost type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $costType = CostType::findOrFail($id);
            $costType->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cost Type berhasil dihapus'
                ]);
            }

            return redirect()
                ->route('cost-type.index')
                ->with('success', 'Cost Type berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting cost type: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting cost type: ' . $e->getMessage());
        }
    }

    /**
     * Get cost types for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $costTypes = CostType::select('id_md_cost_type', 'name')
                ->orderBy('name', 'asc')
                ->get()
                ->map(function ($ct) {
                    return [
                        'id'   => $ct->id_md_cost_type,
                        'text' => $ct->name,
                        'name' => $ct->name,
                    ];
                });

            return response()->json([
                'success' => true,
                'data'    => $costTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving cost types: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete cost types
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|string|exists:a11_md_cost_type,id_md_cost_type'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            CostType::whereIn('id_md_cost_type', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cost Types berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting cost types: ' . $e->getMessage()
            ], 500);
        }
    }
}