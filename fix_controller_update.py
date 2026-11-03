import os
path = os.path.expandvars('$HOME/mnt/jeniusApp/app/Http/Controllers/Data/MutasiPembayaranController.php')
with open(path, 'r') as f:
    content = f.read()

old = """            $mp = MutasiPembayaran::findOrFail($id);
            $mp->update([
                'id_md_chart_of_account' => $request->id_md_chart_of_account,
                'no_cek'                 => $request->no_cek,
                'tanggal'                => $request->tanggal,
                'kurs'                   => $request->kurs ?? 1,
                'memo'                   => $request->memo,
            ]);

            // Sync COA ke voucher tunggal
            $voucher = $mp->vouchers()->first();
            if ($voucher) {
                $voucher->update([
                    'id_md_chart_of_account' => $request->id_md_chart_of_account,
                    'tgl_keluar'             => $request->tanggal,
                ]);
            }

            DB::commit();

            if ($request->expectsJson())
                return response()->json(['success' => true, 'message' => 'Header berhasil diupdate']);"""

new = """            $mp = MutasiPembayaran::findOrFail($id);

            // Jika ada nomor baru (COA berubah tipe), gunakan nomor yang di-generate
            $updateData = [
                'id_md_chart_of_account' => $request->id_md_chart_of_account,
                'no_cek'                 => $request->no_cek,
                'tanggal'                => $request->tanggal,
                'kurs'                   => $request->kurs ?? 1,
                'memo'                   => $request->memo,
            ];
            if ($request->filled('new_nomor')) {
                $updateData['nomor'] = $request->new_nomor;
            }
            $mp->update($updateData);

            // Sync COA ke voucher tunggal
            $voucher = $mp->vouchers()->first();
            if ($voucher) {
                $voucherUpdate = [
                    'id_md_chart_of_account' => $request->id_md_chart_of_account,
                    'tgl_keluar'             => $request->tanggal,
                ];
                if ($request->filled('new_nomor')) {
                    $voucherUpdate['nomor_voucher'] = $request->new_nomor;
                }
                $voucher->update($voucherUpdate);
            }

            DB::commit();

            $responseData = ['success' => true, 'message' => 'Header berhasil diupdate'];
            if ($request->filled('new_nomor')) {
                $responseData['nomor'] = $request->new_nomor;
            }
            if ($request->expectsJson())
                return response()->json($responseData);"""

if old in content:
    content = content.replace(old, new, 1)
    with open(path, 'w') as f:
        f.write(content)
    print("OK: controller update() patched")
else:
    print("FAIL: block not found")
    # debug
    idx = content.find("MutasiPembayaran::findOrFail($id);")
    print(f"  findOrFail at: {idx}")
