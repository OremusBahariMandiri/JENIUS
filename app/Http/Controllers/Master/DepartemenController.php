<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\IdGenerator;

class DepartemenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:departemen')->only('index');
        $this->middleware('check.access:departemen,detail')->only('show');
        $this->middleware('check.access:departemen,tambah')->only('create', 'store');
        $this->middleware('check.access:departemen,ubah')->only('edit', 'update');
        $this->middleware('check.access:departemen,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = Departemen::query();

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('skt_dep', 'like', "%{$search}%")
                        ->orWhere('nama_dep', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $departemens = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $departemens]);
            }

            return view('master.departemen.index', compact('departemens'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving data: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('master.departemen.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skt_dep'    => 'nullable|string|max:100',
            'nama_dep'   => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'nama_dep.required' => 'Nama departemen wajib diisi',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $newId = IdGenerator::generate('A08', 'a08_md_dep', 'id_md_dep');

            $departemen = Departemen::create([
                'id_md_dep'  => $newId,
                'skt_dep'    => $request->skt_dep,
                'nama_dep'   => $request->nama_dep,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Departemen berhasil ditambahkan', 'data' => $departemen], 201);
            }
            return redirect()->route('departemen.index')->with('success', 'Departemen berhasil ditambahkan');
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
            $departemen = Departemen::findOrFail($id);
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $departemen]);
            }
            return view('master.departemen.show', compact('departemen'));
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
            $departemen = Departemen::findOrFail($id);
            return view('master.departemen.edit', compact('departemen'));
        } catch (\Exception $e) {
            return back()->with('error', 'Data not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'skt_dep'    => 'nullable|string|max:100',
            'nama_dep'   => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'nama_dep.required' => 'Nama departemen wajib diisi',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $departemen = Departemen::findOrFail($id);
            $departemen->update([
                'skt_dep'    => $request->skt_dep,
                'nama_dep'   => $request->nama_dep,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Departemen berhasil diupdate', 'data' => $departemen]);
            }
            return redirect()->route('departemen.index')->with('success', 'Departemen berhasil diupdate');
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
            $departemen = Departemen::findOrFail($id);
            $departemen->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Departemen berhasil dihapus']);
            }
            return redirect()->route('departemen.index')->with('success', 'Departemen berhasil dihapus');
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
            $departemens = Departemen::select('id_md_dep', 'skt_dep', 'nama_dep')
                ->orderBy('nama_dep', 'asc')
                ->get()
                ->map(fn($d) => [
                    'id'      => $d->id_md_dep,
                    'text'    => $d->nama_dep,
                    'skt_dep' => $d->skt_dep,
                ]);

            return response()->json(['success' => true, 'data' => $departemens]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|string|exists:a08_md_dep,id_md_dep',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            Departemen::whereIn('id_md_dep', $request->ids)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}