<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Hanya admin yang boleh akses
        if (!auth()->user()->is_admin) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $query = ActivityLog::with('user')->latest();

        // Filter action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('employee_id_number', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('record_id', 'like', "%{$search}%");
            });
        }

        $logs    = $query->paginate(50)->withQueryString();
        $modules = ActivityLog::select('module')->distinct()->whereNotNull('module')->orderBy('module')->pluck('module');
        $users   = \App\Models\User::select('id', 'full_name', 'employee_id_number')->orderBy('full_name')->get();

        return view('settings.activity-log.index', compact('logs', 'modules', 'users'));
    }

    public function show(ActivityLog $activityLog)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        return view('settings.activity-log.show', compact('activityLog'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $activityLog->delete();

        return redirect()->route('activity-log.index')
            ->with('success', 'Log berhasil dihapus.');
    }

    public function destroyAll(Request $request)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'confirm' => 'required|in:DELETE',
        ]);

        ActivityLog::truncate();

        return redirect()->route('activity-log.index')
            ->with('success', 'Semua log berhasil dihapus.');
    }
}
