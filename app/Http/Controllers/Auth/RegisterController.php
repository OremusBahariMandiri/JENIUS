<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'employee_id_number' => ['required', 'string', 'max:255', 'unique:users'],
            'full_name' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'work_location' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
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
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'employee_code' => 'EMP-' . strtoupper(Str::random(8)),
            'employee_id_number' => $data['employee_id_number'],
            'full_name' => $data['full_name'],
            'department' => $data['department'],
            'position' => $data['position'],
            'work_location' => $data['work_location'],
            'password' => Hash::make($data['password']),
            'is_admin' => false,
        ]);
    }
}