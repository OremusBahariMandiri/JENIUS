<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function username()
    {
        return 'employee_id_number';
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password'        => 'required|string',
        ], [
            'employee_id_number.required' => 'Employee ID wajib diisi',
            'password.required'           => 'Password wajib diisi',
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => 'Employee ID atau password salah.',
            ]);
    }

    /**
     * Hook setelah login berhasil — catat log
     */
    protected function authenticated(Request $request, $user)
    {
        ActivityLog::create([
            'user_id'            => $user->id,
            'user_name'          => $user->full_name ?? $user->name,
            'employee_id_number' => $user->employee_id_number,
            'action'             => 'login',
            'module'             => 'Auth',
            'model_name'         => 'User',
            'record_id'          => (string) $user->id,
            'description'        => "User {$user->full_name} ({$user->employee_id_number}) berhasil login.",
            'ip_address'         => $request->ip(),
            'user_agent'         => $request->userAgent(),
            'url'                => $request->fullUrl(),
            'method'             => $request->method(),
        ]);
    }

    /**
     * Hook setelah logout — catat log
     */
    protected function loggedOut(Request $request)
    {
        // User sudah diambil sebelum logout via session guard
    }

    /**
     * Override logout untuk catat log sebelum session dihapus
     */
    public function logout(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            ActivityLog::create([
                'user_id'            => $user->id,
                'user_name'          => $user->full_name ?? $user->name,
                'employee_id_number' => $user->employee_id_number,
                'action'             => 'logout',
                'module'             => 'Auth',
                'model_name'         => 'User',
                'record_id'          => (string) $user->id,
                'description'        => "User {$user->full_name} ({$user->employee_id_number}) logout.",
                'ip_address'         => $request->ip(),
                'user_agent'         => $request->userAgent(),
                'url'                => $request->fullUrl(),
                'method'             => $request->method(),
            ]);
        }

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
