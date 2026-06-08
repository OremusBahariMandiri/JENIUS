<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Other;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\IdGenerator;

class OtherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:other')->only('index');
        $this->middleware('check.access:other,detail')->only('show');
        $this->middleware('check.access:other,tambah')->only('create', 'store');
        $this->middleware('check.access:other,ubah')->only('edit', 'update');
        $this->middleware('check.access:other,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = Other::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('other', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%");
                });
            }

            // Filter by code
            if ($request->has('code') && !empty($request->code)) {
                $query->where('code', $request->code);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $others = $query->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $others
                ]);
            }

            // Get unique codes for filter
            $codes = Other::distinct()->pluck('code')->filter()->sort()->values();

            return view('master.other.index', compact('others', 'codes'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving others: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving others: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get unique codes for reference
        $codes = Other::distinct()->pluck('code')->filter()->sort()->values();

        return view('master.other.create', compact('codes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'nullable|string|max:100',
            'other' => 'required|string|max:255',
            'note' => 'nullable|string',
        ], [
            'other.required' => 'Other type wajib diisi',
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
            $newId = IdGenerator::generate('A07', 'a07_md_other', 'id_md_other');

            $other = Other::create([
                'id_md_other' => $newId,
                'code' => strtoupper($request->code),
                'other' => $request->other,
                'note' => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Other berhasil ditambahkan',
                    'data' => $other
                ], 201);
            }

            return redirect()
                ->route('other.index')
                ->with('success', 'Other berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating other: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating other: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $other = Other::findOrFail($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $other
                ]);
            }

            return view('master.other.show', compact('other'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Other not found'
                ], 404);
            }

            return back()->with('error', 'Other not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $other = Other::findOrFail($id);

            // Get unique codes for reference
            $codes = Other::distinct()->pluck('code')->filter()->sort()->values();

            return view('master.other.edit', compact('other', 'codes'));
        } catch (\Exception $e) {
            return back()->with('error', 'Other not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'nullable|string|max:100',
            'other' => 'required|string|max:255',
            'note' => 'nullable|string',
        ], [
            'other.required' => 'Other type wajib diisi',
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
            $other = Other::findOrFail($id);

            $other->update([
                'code' => strtoupper($request->code),
                'other' => $request->other,
                'note' => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Other berhasil diupdate',
                    'data' => $other
                ]);
            }

            return redirect()
                ->route('other.index')
                ->with('success', 'Other berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating other: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating other: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $other = Other::findOrFail($id);

            // Check if other has related records (uncomment if needed)
            // if ($other->joContractItems()->count() > 0) {
            //     DB::rollBack();
            //     if ($request->expectsJson()) {
            //         return response()->json([
            //             'success' => false,
            //             'message' => 'Other tidak dapat dihapus karena masih memiliki data terkait'
            //         ], 422);
            //     }
            //     return back()->with('error', 'Other tidak dapat dihapus karena masih memiliki data terkait');
            // }

            $other->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Other berhasil dihapus'
                ]);
            }

            return redirect()
                ->route('other.index')
                ->with('success', 'Other berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting other: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting other: ' . $e->getMessage());
        }
    }

    /**
     * Get others for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $query = Other::select('id_md_other', 'code', 'other');

            // Filter by code if provided
            if ($request->has('code')) {
                $query->where('code', $request->code);
            }

            $others = $query->orderBy('code', 'asc')
                ->get()
                ->map(function ($other) {
                    return [
                        'id' => $other->id_md_other,
                        'text' => ($other->code ? $other->code . ' - ' : '') . $other->other,
                        'code' => $other->code,
                        'other' => $other->other,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $others
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving others: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get codes
     */
    public function getCodes(Request $request)
    {
        try {
            $codes = Other::distinct()
                ->pluck('code')
                ->filter()
                ->sort()
                ->values()
                ->map(function ($code) {
                    return [
                        'id' => $code,
                        'text' => $code
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $codes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving codes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get other statistics
     */
    public function statistics(Request $request)
    {
        try {
            $stats = [
                'total_others' => Other::count(),
                'total_codes' => Other::distinct('code')->count('code'),
                'by_code' => Other::select('code', DB::raw('count(*) as total'))
                    ->whereNotNull('code')
                    ->groupBy('code')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'code' => $item->code,
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
     * Bulk delete others
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:a07_md_other,id_md_other'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            Other::whereIn('id_md_other', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Others berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting others: ' . $e->getMessage()
            ], 500);
        }
    }
}
