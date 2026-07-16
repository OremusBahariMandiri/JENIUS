<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\ReleaseTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\IdGenerator;

class ReleaseToController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:release_to')->only('index');
        $this->middleware('check.access:release_to,detail')->only('show');
        $this->middleware('check.access:release_to,tambah')->only('create', 'store');
        $this->middleware('check.access:release_to,ubah')->only('edit', 'update');
        $this->middleware('check.access:release_to,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = ReleaseTo::query();

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_release', 'like', "%{$search}%")
                        ->orWhere('tujuan', 'like', "%{$search}%")
                        ->orWhere('rekening', 'like', "%{$search}%")
                        ->orWhere('no_telepon', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                });
            }

            if ($request->has('tujuan') && !empty($request->tujuan)) {
                $query->where('tujuan', $request->tujuan);
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $releases = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $releases]);
            }

            $tujuans = ReleaseTo::distinct()->pluck('tujuan')->filter()->sort()->values();

            return view('master.release_to.index', compact('releases', 'tujuans'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error retrieving data: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $tujuans = ReleaseTo::distinct()->pluck('tujuan')->filter()->sort()->values();
        return view('master.release_to.create', compact('tujuans'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_release' => 'required|string|max:255',
            'tujuan'       => 'nullable|string|max:255',
            'rekening'     => 'nullable|string|max:100',
            'no_telepon'   => 'nullable|string|max:50',
            'alamat'       => 'nullable|string|max:500',
            'note'         => 'nullable|string',
        ], [
            'nama_release.required' => 'Nama release wajib diisi',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $newId = IdGenerator::generate('A10', 'a10_md_release_to', 'id_md_release');

            $release = ReleaseTo::create([
                'id_md_release' => $newId,
                'nama_release'  => $request->nama_release,
                'tujuan'        => $request->tujuan,
                'rekening'      => $request->rekening,
                'no_telepon'    => $request->no_telepon,
                'alamat'        => $request->alamat,
                'note'          => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Release To berhasil ditambahkan', 'data' => $release], 201);
            }
            return redirect()->route('release_to.index')->with('success', 'Release To berhasil ditambahkan');
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
            $release = ReleaseTo::findOrFail($id);
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $release]);
            }
            return view('master.release_to.show', compact('release'));
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
            $release = ReleaseTo::findOrFail($id);
            $tujuans = ReleaseTo::distinct()->pluck('tujuan')->filter()->sort()->values();
            return view('master.release_to.edit', compact('release', 'tujuans'));
        } catch (\Exception $e) {
            return back()->with('error', 'Data not found');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_release' => 'required|string|max:255',
            'tujuan'       => 'nullable|string|max:255',
            'rekening'     => 'nullable|string|max:100',
            'no_telepon'   => 'nullable|string|max:50',
            'alamat'       => 'nullable|string|max:500',
            'note'         => 'nullable|string',
        ], [
            'nama_release.required' => 'Nama release wajib diisi',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $release = ReleaseTo::findOrFail($id);
            $release->update([
                'nama_release' => $request->nama_release,
                'tujuan'       => $request->tujuan,
                'rekening'     => $request->rekening,
                'no_telepon'   => $request->no_telepon,
                'alamat'       => $request->alamat,
                'note'         => $request->note,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Release To berhasil diupdate', 'data' => $release]);
            }
            return redirect()->route('release_to.index')->with('success', 'Release To berhasil diupdate');
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
            $release = ReleaseTo::findOrFail($id);
            $release->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Release To berhasil dihapus']);
            }
            return redirect()->route('release_to.index')->with('success', 'Release To berhasil dihapus');
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
            $query = ReleaseTo::select('id_md_release', 'nama_release', 'tujuan', 'rekening');

            if ($request->has('tujuan')) {
                $query->where('tujuan', $request->tujuan);
            }

            $releases = $query->orderBy('nama_release', 'asc')
                ->get()
                ->map(fn($r) => [
                    'id'           => $r->id_md_release,
                    'text'         => $r->nama_release . ($r->tujuan ? ' - ' . $r->tujuan : ''),
                    'nama_release' => $r->nama_release,
                    'tujuan'       => $r->tujuan,
                    'rekening'     => $r->rekening,
                ]);

            return response()->json(['success' => true, 'data' => $releases]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|string|exists:a10_md_release_to,id_md_release',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            ReleaseTo::whereIn('id_md_release', $request->ids)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}