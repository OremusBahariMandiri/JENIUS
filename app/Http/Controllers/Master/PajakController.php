<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Pajak;
use App\Helpers\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PajakController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:pajak')->only('index');
        $this->middleware('check.access:pajak,detail')->only('show');
        $this->middleware('check.access:pajak,tambah')->only('create', 'store');
        $this->middleware('check.access:pajak,ubah')->only('edit', 'update');
        $this->middleware('check.access:pajak,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = Pajak::query();

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('nama_pajak', 'like', "%{$s}%")
                      ->orWhere('kode_pajak', 'like', "%{$s}%")
                      ->orWhere('keterangan', 'like', "%{$s}%");
                });
            }

            $sortBy    = $request->get('sort_by', 'nama_pajak');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $pajakList = $query->get();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $pajakList]);
            }

            return view('master.pajak.index', compact('pajakList'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('master.pajak.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pajak'  => 'required|string|max:100',
            'persentase'  => 'required|numeric|min:0|max:100',
            'kode_pajak'  => 'nullable|string|max:50',
            'keterangan'  => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ], [
            'nama_pajak.required' => 'Nama pajak wajib diisi',
            'persentase.required' => 'Persentase wajib diisi',
            'persentase.numeric'  => 'Persentase harus berupa angka',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $newId = IdGenerator::generate('A14', 'a14_md_pajak', 'id_md_pajak');

            $pajak = Pajak::create([
                'id_md_pajak' => $newId,
                'nama_pajak'  => $request->nama_pajak,
                'persentase'  => $request->persentase,
                'kode_pajak'  => $request->kode_pajak,
                'keterangan'  => $request->keterangan,
                'is_active'   => $request->has('is_active') ? 1 : 1, // default active
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Data pajak berhasil ditambahkan', 'data' => $pajak], 201);
            }

            return redirect()->route('pajak.index')->with('success', 'Data pajak berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $pajak = Pajak::findOrFail($id);
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $pajak]);
            }
            return view('master.pajak.show', compact('pajak'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
            }
            return back()->with('error', 'Data pajak tidak ditemukan');
        }
    }

    public function edit($id)
    {
        try {
            $pajak = Pajak::findOrFail($id);
            return view('master.pajak.edit', compact('pajak'));
        } catch (\Exception $e) {
            return back()->with('error', 'Data pajak tidak ditemukan');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_pajak'  => 'required|string|max:100',
            'persentase'  => 'required|numeric|min:0|max:100',
            'kode_pajak'  => 'nullable|string|max:50',
            'keterangan'  => 'nullable|string',
        ], [
            'nama_pajak.required' => 'Nama pajak wajib diisi',
            'persentase.required' => 'Persentase wajib diisi',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $pajak = Pajak::findOrFail($id);
            $pajak->update([
                'nama_pajak' => $request->nama_pajak,
                'persentase' => $request->persentase,
                'kode_pajak' => $request->kode_pajak,
                'keterangan' => $request->keterangan,
                'is_active'  => $request->filled('is_active') ? 1 : 0,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Data pajak berhasil diupdate', 'data' => $pajak]);
            }

            return redirect()->route('pajak.index')->with('success', 'Data pajak berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Gagal mengupdate: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $pajak = Pajak::findOrFail($id);
            $pajak->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Data pajak berhasil dihapus']);
            }

            return redirect()->route('pajak.index')->with('success', 'Data pajak berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * API: get active pajak list for select/dropdown
     */
    public function getForSelect(Request $request)
    {
        try {
            $list = Pajak::where('is_active', true)
                ->orderBy('nama_pajak')
                ->get()
                ->map(fn($p) => [
                    'id'         => $p->id_md_pajak,
                    'text'       => $p->nama_pajak . ' (' . number_format($p->persentase, 2) . '%)',
                    'persentase' => (float) $p->persentase,
                    'kode'       => $p->kode_pajak,
                ]);

            return response()->json(['success' => true, 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'required|string|exists:a14_md_pajak,id_md_pajak',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            Pajak::whereIn('id_md_pajak', $request->ids)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data pajak berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
