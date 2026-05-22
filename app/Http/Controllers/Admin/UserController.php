<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('employee_code', 'like', "%{$search}%")
                      ->orWhere('employee_id_number', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%")
                      ->orWhere('department', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%")
                      ->orWhere('work_location', 'like', "%{$search}%");
                });
            }

            // Filter by department
            if ($request->has('department') && !empty($request->department)) {
                $query->where('department', $request->department);
            }

            // Filter by work location
            if ($request->has('work_location') && !empty($request->work_location)) {
                $query->where('work_location', $request->work_location);
            }

            // Filter by admin status
            if ($request->has('is_admin') && $request->is_admin !== '') {
                $query->where('is_admin', $request->is_admin);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $users
                ]);
            }

            // Get unique departments and work locations for filter
            $departments = User::distinct()->pluck('department')->filter()->sort()->values();
            $workLocations = User::distinct()->pluck('work_location')->filter()->sort()->values();

            return view('admin.user.index', compact('users', 'departments', 'workLocations'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving users: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error retrieving users: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get unique departments and work locations for reference
        $departments = User::distinct()->pluck('department')->filter()->sort()->values();
        $workLocations = User::distinct()->pluck('work_location')->filter()->sort()->values();
        $positions = User::distinct()->pluck('position')->filter()->sort()->values();

        return view('admin.user.create', compact('departments', 'workLocations', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id_number' => 'required|string|max:255|unique:users,employee_id_number',
            'full_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'work_location' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'is_admin' => 'nullable|boolean',
        ], [
            'employee_id_number.required' => 'Employee ID wajib diisi',
            'employee_id_number.unique' => 'Employee ID sudah terdaftar',
            'full_name.required' => 'Nama Lengkap wajib diisi',
            'department.required' => 'Departemen wajib diisi',
            'position.required' => 'Jabatan wajib diisi',
            'work_location.required' => 'Wilayah Kerja wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
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
            $user = User::create([
                'employee_code' => 'EMP-' . strtoupper(Str::random(8)),
                'employee_id_number' => $request->employee_id_number,
                'full_name' => $request->full_name,
                'department' => $request->department,
                'position' => $request->position,
                'work_location' => $request->work_location,
                'password' => Hash::make($request->password),
                'is_admin' => $request->has('is_admin') ? true : false,
                'created_by' => auth()->user()->employee_code ?? null,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil ditambahkan',
                    'data' => $user
                ], 201);
            }

            return redirect()
                ->route('user.index')
                ->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating user: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $user = User::with('userAccess')->findOrFail($id);

            // Calculate summary
            $summary = [
                'total_access' => $user->userAccess->count(),
                'created_at' => $user->created_at->format('d M Y H:i'),
                'updated_at' => $user->updated_at->format('d M Y H:i'),
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $user,
                    'summary' => $summary
                ]);
            }

            return view('admin.user.show', compact('user', 'summary'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return back()->with('error', 'User not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);

            // Get unique values for reference
            $departments = User::distinct()->pluck('department')->filter()->sort()->values();
            $workLocations = User::distinct()->pluck('work_location')->filter()->sort()->values();
            $positions = User::distinct()->pluck('position')->filter()->sort()->values();

            return view('admin.user.edit', compact('user', 'departments', 'workLocations', 'positions'));
        } catch (\Exception $e) {
            return back()->with('error', 'User not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id_number' => 'required|string|max:255|unique:users,employee_id_number,' . $id,
            'full_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'work_location' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'is_admin' => 'nullable|boolean',
        ], [
            'employee_id_number.required' => 'Employee ID wajib diisi',
            'employee_id_number.unique' => 'Employee ID sudah terdaftar',
            'full_name.required' => 'Nama Lengkap wajib diisi',
            'department.required' => 'Departemen wajib diisi',
            'position.required' => 'Jabatan wajib diisi',
            'work_location.required' => 'Wilayah Kerja wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
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
            $user = User::findOrFail($id);

            $updateData = [
                'employee_id_number' => $request->employee_id_number,
                'full_name' => $request->full_name,
                'department' => $request->department,
                'position' => $request->position,
                'work_location' => $request->work_location,
                'is_admin' => $request->has('is_admin') ? true : false,
                'updated_by' => auth()->user()->employee_code ?? null,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil diupdate',
                    'data' => $user
                ]);
            }

            return redirect()
                ->route('user.index')
                ->with('success', 'User berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating user: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            // Prevent deleting own account
            if (auth()->check() && auth()->id() == $id) {
                DB::rollBack();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak dapat menghapus akun Anda sendiri'
                    ], 422);
                }

                return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri');
            }

            // Check if user has related user access
            if ($user->userAccess()->count() > 0) {
                // Hapus user access dulu
                $user->userAccess()->delete();
            }

            $user->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil dihapus'
                ]);
            }

            return redirect()
                ->route('user.index')
                ->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting user: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Get users for select dropdown (API)
     */
    public function getForSelect(Request $request)
    {
        try {
            $query = User::select('id', 'employee_code', 'employee_id_number', 'full_name', 'department', 'position');

            // Filter by department if provided
            if ($request->has('department')) {
                $query->where('department', $request->department);
            }

            // Filter by work location if provided
            if ($request->has('work_location')) {
                $query->where('work_location', $request->work_location);
            }

            $users = $query->orderBy('full_name', 'asc')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'text' => $user->full_name . ' (' . $user->employee_code . ')',
                        'employee_code' => $user->employee_code,
                        'department' => $user->department,
                        'position' => $user->position,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function statistics(Request $request)
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'total_admins' => User::where('is_admin', true)->count(),
                'total_departments' => User::distinct('department')->count('department'),
                'total_locations' => User::distinct('work_location')->count('work_location'),
                'by_department' => User::select('department', DB::raw('count(*) as total'))
                    ->groupBy('department')
                    ->get()
                    ->map(function($item) {
                        return [
                            'department' => $item->department,
                            'total' => $item->total
                        ];
                    }),
                'by_location' => User::select('work_location', DB::raw('count(*) as total'))
                    ->groupBy('work_location')
                    ->get()
                    ->map(function($item) {
                        return [
                            'location' => $item->work_location,
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
}