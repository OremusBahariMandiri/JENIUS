<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\CompanyProfile;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    public function edit()
    {
        $profile = CompanyProfile::firstOrCreate([], [
            'nama_perusahaan' => '',
            'nama_direktur'   => '',
            'jabatan'         => '',
            'alamat'          => '',
            'nama_ttd_kwitansi'     => '',
            'jabatan_ttd_kwitansi'  => '',
        ]);

        return view('master.company-profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'nullable|string|max:255',
            'nama_direktur'   => 'nullable|string|max:255',
            'jabatan'         => 'nullable|string|max:255',
            'alamat'          => 'nullable|string',
            'nama_ttd_kwitansi'     => 'nullable|string|max:255',
            'jabatan_ttd_kwitansi'  => 'nullable|string|max:255',
        ]);

        $profile = CompanyProfile::firstOrCreate([], [
            'nama_perusahaan' => '',
            'nama_direktur'   => '',
            'jabatan'         => '',
            'alamat'          => '',
            'nama_ttd_kwitansi'     => '',
            'jabatan_ttd_kwitansi'  => '',
        ]);

        $profile->update($request->only(['nama_perusahaan', 'nama_direktur', 'jabatan', 'alamat', 'nama_ttd_kwitansi', 'jabatan_ttd_kwitansi']));

        return back()->with('success', 'Company Profile berhasil disimpan.');
    }
}
