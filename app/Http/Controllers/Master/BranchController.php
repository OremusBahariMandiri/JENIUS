<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\IdGenerator;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:branch')->only('index');
        $this->middleware('check.access:branch,detail')->only('show');
        $this->middleware('check.access:branch,tambah')->only('create', 'store');
        $this->middleware('check.access:branch,ubah')->only('edit', 'update');
        $this->middleware('check.access:branch,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = Branch::query();

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('skt_branch', 'like', "%{$search}%")
                        ->orWhere('nama_branch', 'like', "%{$search}%")
                        ->orWhere('area', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->has('area') && !empty($request->area)) {
                $query->where('area', $request->area);
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $branches = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $branches]);
            }

            $areas = Branch::distinct()->pluck('area')->filter()->sort()->values();

            return view('master.branch.index', compact('branches', 'areas'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving data: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $areas = Branch::distinct()->pluck('area')->filter()->sort()->values();
        return view('master.branch.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skt_branch'  => 'nullable|string|max:100',
            'nama_branch' => 'required|string|max:255',
            'area'        => 'nullable|string|max:100',
            'alamat'      => 'nullable|string|max:500',
            'no_telepon'  => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:255',
            'note'        => 'nullable|string',
        ], [
            'nama_branch.required' => 'Nama cabang wajib diisi',
            'email.email'          => 'Format email tidak valid',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $newId = IdGenerator::generate('A09', 'a09_md_branch', 'id_md_branch');

            $branch = Branch::create([
                'id_md_branch' => $newId,
                'skt_branch'   => $request->skt_branch,
                'nama_branch'  => $request->nama_branch,
                'area'         => $request->area,
                'alamat'       => $request->alamat,
                'no_telepon'   => $request->no_telepon,
                'email'        => $request->email,
                'note'         => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Cabang berhasil ditambahkan', 'data' => $branch], 201);
            }
            return redirect()->route('branch.index')->with('success', 'Cabang berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error creating data: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $branch = Branch::findOrFail($id);
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $branch]);
            }
            return view('master.branch.show', compact('branch'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Data not found'], 404);
            }
            return back()->with('error', 'Data not found');
        }
    }

    public function edit($id)
    {
        try {
            $branch = Branch::findOrFail($id);
            $areas = Branch::distinct()->pluck('area')->filter()->sort()->values();
            return view('master.branch.edit', compact('branch', 'areas'));
        } catch (\Exception $e) {
            return back()->with('error', 'Data not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'skt_branch'  => 'nullable|string|max:100',
            'nama_branch' => 'required|string|max:255',
            'area'        => 'nullable|string|max:100',
            'alamat'      => 'nullable|string|max:500',
            'no_telepon'  => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:255',
            'note'        => 'nullable|string',
        ], [
            'nama_branch.required' => 'Nama cabang wajib diisi',
            'email.email'          => 'Format email tidak valid',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $branch = Branch::findOrFail($id);
            $branch->update([
                'skt_branch'  => $request->skt_branch,
                'nama_branch' => $request->nama_branch,
                'area'        => $request->area,
                'alamat'      => $request->alamat,
                'no_telepon'  => $request->no_telepon,
                'email'       => $request->email,
                'note'        => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Cabang berhasil diupdate', 'data' => $branch]);
            }
            return redirect()->route('branch.index')->with('success', 'Cabang berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error updating data: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $branch = Branch::findOrFail($id);
            $branch->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Cabang berhasil dihapus']);
            }
            return redirect()->route('branch.index')->with('success', 'Cabang berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error deleting data: ' . $e->getMessage());
        }
    }

    public function getForSelect(Request $request)
    {
        try {
            $query = Branch::select('id_md_branch', 'skt_branch', 'nama_branch', 'area');

            if ($request->has('area')) {
                $query->where('area', $request->area);
            }

            $branches = $query->orderBy('nama_branch', 'asc')
                ->get()
                ->map(fn($b) => [
                    'id'          => $b->id_md_branch,
                    'text'        => $b->nama_branch . ($b->area ? ' (' . $b->area . ')' : ''),
                    'nama_branch' => $b->nama_branch,
                    'skt_branch'  => $b->skt_branch,
                    'area'        => $b->area,
                ]);

            return response()->json(['success' => true, 'data' => $branches]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|string|exists:a09_md_branch,id_md_branch',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            Branch::whereIn('id_md_branch', $request->ids)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}