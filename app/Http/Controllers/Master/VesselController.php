<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VesselController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:vessel')->only('index');
        $this->middleware('check.access:vessel,detail')->only('show');
        $this->middleware('check.access:vessel,tambah')->only('create', 'store');
        $this->middleware('check.access:vessel,ubah')->only('edit', 'update');
        $this->middleware('check.access:vessel,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = Vessel::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('vessel_name', 'like', "%{$search}%")
                        ->orWhere('vessel_type', 'like', "%{$search}%")
                        ->orWhere('no_imo', 'like', "%{$search}%")
                        ->orWhere('no_mmsi', 'like', "%{$search}%")
                        ->orWhere('call_sign', 'like', "%{$search}%")
                        ->orWhere('flag', 'like', "%{$search}%");
                });
            }

            // Filter by vessel type
            if ($request->has('vessel_type') && !empty($request->vessel_type)) {
                $query->where('vessel_type', $request->vessel_type);
            }

            // Filter by flag
            if ($request->has('flag') && !empty($request->flag)) {
                $query->where('flag', $request->flag);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $vessels = $query->paginate($perPage);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $vessels
                ]);
            }

            // Get unique vessel types and flags for filter
            $vesselTypes = Vessel::distinct()->pluck('vessel_type')->filter()->sort()->values();
            $flags = Vessel::distinct()->pluck('flag')->filter()->sort()->values();

            return view('master.vessel.index', compact('vessels', 'vesselTypes', 'flags'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving vessels: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving vessels: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get unique vessel types and flags for reference
        $vesselTypes = Vessel::distinct()->pluck('vessel_type')->filter()->sort()->values();
        $flags = Vessel::distinct()->pluck('flag')->filter()->sort()->values();

        return view('master.vessel.create', compact('vesselTypes', 'flags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vessel_name' => 'required|string|max:255',
            'vessel_type' => 'nullable|string|max:100',
            'no_imo' => 'nullable|string|max:50',
            'no_mmsi' => 'nullable|string|max:50',
            'call_sign' => 'nullable|string|max:50',
            'gt' => 'nullable|string|max:50',
            'dwt' => 'nullable|string|max:50',
            'year_built' => 'nullable|string|max:4',
            'breadth' => 'nullable|string|max:50',
            'loa' => 'nullable|string|max:50',
            'flag' => 'nullable|string|max:100',
            'ga' => 'nullable|string|max:255',
            'ship_particular' => 'nullable|string',
        ], [
            'vessel_name.required' => 'Nama kapal wajib diisi',
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
            $lastVessel = Vessel::orderBy('id_md_vessel', 'desc')->first();
            $newId = $lastVessel ? $lastVessel->id_md_vessel + 1 : 1;

            $vessel = Vessel::create([
                'id_md_vessel' => $newId,
                'vessel_name' => $request->vessel_name,
                'vessel_type' => $request->vessel_type,
                'no_imo' => $request->no_imo,
                'no_mmsi' => $request->no_mmsi,
                'call_sign' => strtoupper($request->call_sign),
                'gt' => $request->gt,
                'dwt' => $request->dwt,
                'year_built' => $request->year_built,
                'breadth' => $request->breadth,
                'loa' => $request->loa,
                'flag' => $request->flag,
                'ga' => $request->ga,
                'ship_particular' => $request->ship_particular,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vessel berhasil ditambahkan',
                    'data' => $vessel
                ], 201);
            }

            return redirect()
                ->route('vessel.index')
                ->with('success', 'Vessel berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating vessel: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating vessel: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $vessel = Vessel::findOrFail($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $vessel
                ]);
            }

            return view('master.vessel.show', compact('vessel'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vessel not found'
                ], 404);
            }

            return back()->with('error', 'Vessel not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $vessel = Vessel::findOrFail($id);

            // Get unique vessel types and flags for reference
            $vesselTypes = Vessel::distinct()->pluck('vessel_type')->filter()->sort()->values();
            $flags = Vessel::distinct()->pluck('flag')->filter()->sort()->values();

            return view('master.vessel.edit', compact('vessel', 'vesselTypes', 'flags'));
        } catch (\Exception $e) {
            return back()->with('error', 'Vessel not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'vessel_name' => 'required|string|max:255',
            'vessel_type' => 'nullable|string|max:100',
            'no_imo' => 'nullable|string|max:50',
            'no_mmsi' => 'nullable|string|max:50',
            'call_sign' => 'nullable|string|max:50',
            'gt' => 'nullable|string|max:50',
            'dwt' => 'nullable|string|max:50',
            'year_built' => 'nullable|string|max:4',
            'breadth' => 'nullable|string|max:50',
            'loa' => 'nullable|string|max:50',
            'flag' => 'nullable|string|max:100',
            'ga' => 'nullable|string|max:255',
            'ship_particular' => 'nullable|string',
        ], [
            'vessel_name.required' => 'Nama kapal wajib diisi',
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
            $vessel = Vessel::findOrFail($id);

            $vessel->update([
                'vessel_name' => $request->vessel_name,
                'vessel_type' => $request->vessel_type,
                'no_imo' => $request->no_imo,
                'no_mmsi' => $request->no_mmsi,
                'call_sign' => strtoupper($request->call_sign),
                'gt' => $request->gt,
                'dwt' => $request->dwt,
                'year_built' => $request->year_built,
                'breadth' => $request->breadth,
                'loa' => $request->loa,
                'flag' => $request->flag,
                'ga' => $request->ga,
                'ship_particular' => $request->ship_particular,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vessel berhasil diupdate',
                    'data' => $vessel
                ]);
            }

            return redirect()
                ->route('vessel.index')
                ->with('success', 'Vessel berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating vessel: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating vessel: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $vessel = Vessel::findOrFail($id);

            // Check if vessel has related records (uncomment if needed)
            // if ($vessel->joContracts()->count() > 0) {
            //     DB::rollBack();
            //     if ($request->expectsJson()) {
            //         return response()->json([
            //             'success' => false,
            //             'message' => 'Vessel tidak dapat dihapus karena masih memiliki data terkait'
            //         ], 422);
            //     }
            //     return back()->with('error', 'Vessel tidak dapat dihapus karena masih memiliki data terkait');
            // }

            $vessel->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vessel berhasil dihapus'
                ]);
            }

            return redirect()
                ->route('vessel.index')
                ->with('success', 'Vessel berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting vessel: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting vessel: ' . $e->getMessage());
        }
    }

    /**
     * Get vessels for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $query = Vessel::select('id_md_vessel', 'vessel_name', 'vessel_type', 'no_imo');

            // Filter by vessel type if provided
            if ($request->has('vessel_type')) {
                $query->where('vessel_type', $request->vessel_type);
            }

            // Filter by flag if provided
            if ($request->has('flag')) {
                $query->where('flag', $request->flag);
            }

            $vessels = $query->orderBy('vessel_name', 'asc')
                ->get()
                ->map(function ($vessel) {
                    return [
                        'id' => $vessel->id_md_vessel,
                        'text' => $vessel->vessel_name . ($vessel->no_imo ? ' (IMO: ' . $vessel->no_imo . ')' : ''),
                        'vessel_name' => $vessel->vessel_name,
                        'vessel_type' => $vessel->vessel_type,
                        'no_imo' => $vessel->no_imo,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $vessels
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving vessels: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vessel types
     */
    public function getVesselTypes(Request $request)
    {
        try {
            $vesselTypes = Vessel::distinct()
                ->pluck('vessel_type')
                ->filter()
                ->sort()
                ->values()
                ->map(function ($type) {
                    return [
                        'id' => $type,
                        'text' => $type
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $vesselTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving vessel types: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get flags
     */
    public function getFlags(Request $request)
    {
        try {
            $flags = Vessel::distinct()
                ->pluck('flag')
                ->filter()
                ->sort()
                ->values()
                ->map(function ($flag) {
                    return [
                        'id' => $flag,
                        'text' => $flag
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $flags
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving flags: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vessel statistics
     */
    public function statistics(Request $request)
    {
        try {
            $stats = [
                'total_vessels' => Vessel::count(),
                'total_types' => Vessel::distinct('vessel_type')->count('vessel_type'),
                'total_flags' => Vessel::distinct('flag')->count('flag'),
                'by_type' => Vessel::select('vessel_type', DB::raw('count(*) as total'))
                    ->whereNotNull('vessel_type')
                    ->groupBy('vessel_type')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'type' => $item->vessel_type,
                            'total' => $item->total
                        ];
                    }),
                'by_flag' => Vessel::select('flag', DB::raw('count(*) as total'))
                    ->whereNotNull('flag')
                    ->groupBy('flag')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'flag' => $item->flag,
                            'total' => $item->total
                        ];
                    }),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete vessels
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:a05_md_vessel,id_md_vessel'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            Vessel::whereIn('id_md_vessel', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vessels berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting vessels: ' . $e->getMessage()
            ], 500);
        }
    }
}