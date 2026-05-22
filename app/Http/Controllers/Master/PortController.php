<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Port;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PortController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:port')->only('index');
        $this->middleware('check.access:port,detail')->only('show');
        $this->middleware('check.access:port,tambah')->only('create', 'store');
        $this->middleware('check.access:port,ubah')->only('edit', 'update');
        $this->middleware('check.access:port,hapus')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        try {
            $query = Port::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('no_port', 'like', "%{$search}%")
                        ->orWhere('name_port', 'like', "%{$search}%")
                        ->orWhere('kota', 'like', "%{$search}%")
                        ->orWhere('provinsi', 'like', "%{$search}%")
                        ->orWhere('negara', 'like', "%{$search}%");
                });
            }

            // Filter by country
            if ($request->has('negara') && !empty($request->negara)) {
                $query->where('negara', $request->negara);
            }

            // Filter by province
            if ($request->has('provinsi') && !empty($request->provinsi)) {
                $query->where('provinsi', $request->provinsi);
            }

            // Filter by port type
            if ($request->has('port_type') && !empty($request->port_type)) {
                $query->where('port_type', $request->port_type);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $ports = $query->paginate($perPage);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $ports
                ]);
            }

            // Get unique values for filters
            $countries = Port::distinct()->pluck('negara')->filter()->sort()->values();
            $provinces = Port::distinct()->pluck('provinsi')->filter()->sort()->values();
            $portTypes = Port::distinct()->pluck('port_type')->filter()->sort()->values();

            return view('master.port.index', compact('ports', 'countries', 'provinces', 'portTypes'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving ports: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving ports: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get unique values for reference
        $countries = Port::distinct()->pluck('negara')->filter()->sort()->values();
        $provinces = Port::distinct()->pluck('provinsi')->filter()->sort()->values();
        $portTypes = Port::distinct()->pluck('port_type')->filter()->sort()->values();

        return view('master.port.create', compact('countries', 'provinces', 'portTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_port' => 'nullable|string|max:50',
            'name_port' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'negara' => 'nullable|string|max:100',
            'port_type' => 'nullable|string|max:100',
            'operator_port' => 'nullable|string|max:255',
            'draft' => 'nullable|string|max:50',
            'panjang_kapal' => 'nullable|string|max:50',
            'lebar_kapal' => 'nullable|string|max:50',
            'dwt' => 'nullable|string|max:50',
            'panjang_dermaga' => 'nullable|string|max:50',
            'pasang_surut' => 'nullable|string|max:100',
            'jam_ops' => 'nullable|string|max:100',
        ], [
            'name_port.required' => 'Nama pelabuhan wajib diisi',
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
            $lastPort = Port::orderBy('id_md_port', 'desc')->first();
            $newId = $lastPort ? $lastPort->id_md_port + 1 : 1;

            $port = Port::create([
                'id_md_port' => $newId,
                'no_port' => $request->no_port,
                'name_port' => $request->name_port,
                'alamat' => $request->alamat,
                'kota' => $request->kota,
                'provinsi' => $request->provinsi,
                'negara' => $request->negara,
                'port_type' => $request->port_type,
                'operator_port' => $request->operator_port,
                'draft' => $request->draft,
                'panjang_kapal' => $request->panjang_kapal,
                'lebar_kapal' => $request->lebar_kapal,
                'dwt' => $request->dwt,
                'panjang_dermaga' => $request->panjang_dermaga,
                'pasang_surut' => $request->pasang_surut,
                'jam_ops' => $request->jam_ops,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Port berhasil ditambahkan',
                    'data' => $port
                ], 201);
            }

            return redirect()
                ->route('port.index')
                ->with('success', 'Port berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating port: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating port: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $port = Port::findOrFail($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $port
                ]);
            }

            return view('master.port.show', compact('port'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Port not found'
                ], 404);
            }

            return back()->with('error', 'Port not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $port = Port::findOrFail($id);

            // Get unique values for reference
            $countries = Port::distinct()->pluck('negara')->filter()->sort()->values();
            $provinces = Port::distinct()->pluck('provinsi')->filter()->sort()->values();
            $portTypes = Port::distinct()->pluck('port_type')->filter()->sort()->values();

            return view('master.port.edit', compact('port', 'countries', 'provinces', 'portTypes'));
        } catch (\Exception $e) {
            return back()->with('error', 'Port not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'no_port' => 'nullable|string|max:50',
            'name_port' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'negara' => 'nullable|string|max:100',
            'port_type' => 'nullable|string|max:100',
            'operator_port' => 'nullable|string|max:255',
            'draft' => 'nullable|string|max:50',
            'panjang_kapal' => 'nullable|string|max:50',
            'lebar_kapal' => 'nullable|string|max:50',
            'dwt' => 'nullable|string|max:50',
            'panjang_dermaga' => 'nullable|string|max:50',
            'pasang_surut' => 'nullable|string|max:100',
            'jam_ops' => 'nullable|string|max:100',
        ], [
            'name_port.required' => 'Nama pelabuhan wajib diisi',
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
            $port = Port::findOrFail($id);

            $port->update([
                'no_port' => $request->no_port,
                'name_port' => $request->name_port,
                'alamat' => $request->alamat,
                'kota' => $request->kota,
                'provinsi' => $request->provinsi,
                'negara' => $request->negara,
                'port_type' => $request->port_type,
                'operator_port' => $request->operator_port,
                'draft' => $request->draft,
                'panjang_kapal' => $request->panjang_kapal,
                'lebar_kapal' => $request->lebar_kapal,
                'dwt' => $request->dwt,
                'panjang_dermaga' => $request->panjang_dermaga,
                'pasang_surut' => $request->pasang_surut,
                'jam_ops' => $request->jam_ops,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Port berhasil diupdate',
                    'data' => $port
                ]);
            }

            return redirect()
                ->route('port.index')
                ->with('success', 'Port berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating port: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating port: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $port = Port::findOrFail($id);

            // Check if port has related records (uncomment if needed)
            // if ($port->joContracts()->count() > 0) {
            //     DB::rollBack();
            //     if ($request->expectsJson()) {
            //         return response()->json([
            //             'success' => false,
            //             'message' => 'Port tidak dapat dihapus karena masih memiliki data terkait'
            //         ], 422);
            //     }
            //     return back()->with('error', 'Port tidak dapat dihapus karena masih memiliki data terkait');
            // }

            $port->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Port berhasil dihapus'
                ]);
            }

            return redirect()
                ->route('port.index')
                ->with('success', 'Port berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting port: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting port: ' . $e->getMessage());
        }
    }

    /**
     * Get ports for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $query = Port::select('id_md_port', 'no_port', 'name_port', 'kota', 'negara');

            // Filter by country if provided
            if ($request->has('negara')) {
                $query->where('negara', $request->negara);
            }

            // Filter by province if provided
            if ($request->has('provinsi')) {
                $query->where('provinsi', $request->provinsi);
            }

            $ports = $query->orderBy('name_port', 'asc')
                ->get()
                ->map(function ($port) {
                    return [
                        'id' => $port->id_md_port,
                        'text' => $port->name_port . ($port->kota ? ', ' . $port->kota : '') . ($port->negara ? ' (' . $port->negara . ')' : ''),
                        'name_port' => $port->name_port,
                        'no_port' => $port->no_port,
                        'kota' => $port->kota,
                        'negara' => $port->negara,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $ports
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving ports: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get countries
     */
    public function getCountries(Request $request)
    {
        try {
            $countries = Port::distinct()
                ->pluck('negara')
                ->filter()
                ->sort()
                ->values()
                ->map(function ($country) {
                    return [
                        'id' => $country,
                        'text' => $country
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $countries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving countries: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get provinces
     */
    public function getProvinces(Request $request)
    {
        try {
            $query = Port::distinct();

            // Filter by country if provided
            if ($request->has('negara')) {
                $query->where('negara', $request->negara);
            }

            $provinces = $query->pluck('provinsi')
                ->filter()
                ->sort()
                ->values()
                ->map(function ($province) {
                    return [
                        'id' => $province,
                        'text' => $province
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $provinces
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving provinces: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get port statistics
     */
    public function statistics(Request $request)
    {
        try {
            $stats = [
                'total_ports' => Port::count(),
                'total_countries' => Port::distinct('negara')->count('negara'),
                'total_provinces' => Port::distinct('provinsi')->count('provinsi'),
                'by_country' => Port::select('negara', DB::raw('count(*) as total'))
                    ->whereNotNull('negara')
                    ->groupBy('negara')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'country' => $item->negara,
                            'total' => $item->total
                        ];
                    }),
                'by_type' => Port::select('port_type', DB::raw('count(*) as total'))
                    ->whereNotNull('port_type')
                    ->groupBy('port_type')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'type' => $item->port_type,
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
     * Bulk delete ports
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:a06_md_port,id_md_port'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            Port::whereIn('id_md_port', $request->ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ports berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting ports: ' . $e->getMessage()
            ], 500);
        }
    }
}