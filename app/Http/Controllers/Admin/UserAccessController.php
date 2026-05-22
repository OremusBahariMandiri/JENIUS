<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserAccessController extends Controller
{
    /**
     * Show the form for editing user access
     */
    public function edit($userId)
    {
        try {
            $user = User::with('userAccess')->findOrFail($userId);

            // Get all available menus
            $menus = config('menu.items');
            $permissions = config('menu.permissions');

            // Get user's current access
            $userAccess = $user->userAccess->keyBy('menu_access');

            return view('admin.user-access.edit', compact('user', 'menus', 'permissions', 'userAccess'));
        } catch (\Exception $e) {
            return back()->with('error', 'User not found');
        }
    }

    /**
     * Update user access
     */
    public function update(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'is_admin' => 'nullable|boolean',
            'access' => 'nullable|array',
            'access.*.menu_access' => 'required|string',
            'access.*.permissions' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $user = User::findOrFail($userId);

            // Update admin status
            $user->update([
                'is_admin' => $request->has('is_admin') ? true : false,
                'updated_by' => auth()->user()->employee_code ?? null,
            ]);

            // Delete all existing access for this user
            UserAccess::where('user_id', $userId)->delete();

            // If user is admin, no need to set individual permissions
            if (!$request->has('is_admin')) {
                // Create new access based on submitted data
                if ($request->has('access') && is_array($request->access)) {
                    foreach ($request->access as $accessData) {
                        // Skip if menu not enabled
                        if (!isset($accessData['enabled']) || !$accessData['enabled']) {
                            continue;
                        }

                        $permissions = $accessData['permissions'] ?? [];

                        UserAccess::create([
                            'user_id' => $userId,
                            'menu_access' => $accessData['menu_access'],
                            'can_create' => in_array('create', $permissions),
                            'can_update' => in_array('update', $permissions),
                            'can_delete' => in_array('delete', $permissions),
                            'can_download' => in_array('download', $permissions),
                            'can_view_detail' => in_array('view_detail', $permissions),
                            'can_monitor' => in_array('monitor', $permissions),
                            'updated_by' => auth()->id(),
                            'created_by' => auth()->id(),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('user-access.edit', $userId)
                ->with('success', 'User access and admin status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error updating user access: ' . $e->getMessage());
        }
    }

    /**
     * Grant full access to user
     */
    public function grantFullAccess($userId)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($userId);

            // Delete existing access
            UserAccess::where('user_id', $userId)->delete();

            // Grant access to all menus with all permissions
            $menus = config('menu.items');

            foreach ($menus as $menu) {
                UserAccess::create([
                    'user_id' => $userId,
                    'menu_access' => $menu['key'],
                    'can_create' => true,
                    'can_update' => true,
                    'can_delete' => true,
                    'can_download' => true,
                    'can_view_detail' => true,
                    'can_monitor' => true,
                    'updated_by' => auth()->id(),
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('user-access.edit', $userId)
                ->with('success', 'Full access granted successfully to all menus');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error granting access: ' . $e->getMessage());
        }
    }

    /**
     * Revoke all access from user
     */
    public function revokeAllAccess($userId)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($userId);

            // Delete all access
            UserAccess::where('user_id', $userId)->delete();

            // Also remove admin status
            $user->update([
                'is_admin' => false,
                'updated_by' => auth()->user()->employee_code ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('user-access.edit', $userId)
                ->with('success', 'All access revoked successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error revoking access: ' . $e->getMessage());
        }
    }

    /**
     * Copy access from another user
     */
    public function copyAccess(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'source_user_id' => 'required|exists:users,id|different:user_id',
        ], [
            'source_user_id.required' => 'Please select a user to copy from',
            'source_user_id.different' => 'Cannot copy access from the same user',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $targetUser = User::findOrFail($userId);
            $sourceUser = User::findOrFail($request->source_user_id);

            // Delete existing access
            UserAccess::where('user_id', $userId)->delete();

            // Copy admin status
            $targetUser->update([
                'is_admin' => $sourceUser->is_admin,
                'updated_by' => auth()->user()->employee_code ?? null,
            ]);

            // Copy access from source user
            $sourceAccess = UserAccess::where('user_id', $request->source_user_id)->get();

            foreach ($sourceAccess as $access) {
                UserAccess::create([
                    'user_id' => $userId,
                    'menu_access' => $access->menu_access,
                    'can_create' => $access->can_create,
                    'can_update' => $access->can_update,
                    'can_delete' => $access->can_delete,
                    'can_download' => $access->can_download,
                    'can_view_detail' => $access->can_view_detail,
                    'can_monitor' => $access->can_monitor,
                    'updated_by' => auth()->id(),
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('user-access.edit', $userId)
                ->with('success', 'Access and admin status copied successfully from ' . $sourceUser->full_name);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error copying access: ' . $e->getMessage());
        }
    }
}