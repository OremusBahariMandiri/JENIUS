import os
path = os.path.expandvars('$HOME/mnt/jeniusApp/resources/views/data/mutasi-pembayaran/edit.blade.php')
with open(path, 'r') as f:
    content = f.read()

# 1) Tambah data-payment-type ke option di edit_coa
old_opt = '''                            <option value="{{ $coa->id_md_chart_of_account }}" {{ $mutasiPembayaran->id_md_chart_of_account == $coa->id_md_chart_of_account ? 'selected' : '' }}>
                                [{{ $coa->payment_type }}] {{ $coa->no_account }} - {{ $coa->account_name ?? ($coa->nama_account ?? '') }}
                            </option>'''
new_opt = '''                            <option value="{{ $coa->id_md_chart_of_account }}"
                                data-payment-type="{{ $coa->payment_type }}"
                                {{ $mutasiPembayaran->id_md_chart_of_account == $coa->id_md_chart_of_account ? 'selected' : '' }}>
                                [{{ $coa->payment_type }}] {{ $coa->no_account }} - {{ $coa->account_name ?? ($coa->nama_account ?? '') }}
                            </option>'''

if old_opt in content:
    content = content.replace(old_opt, new_opt, 1)
    print("OK step1: data-payment-type added to options")
else:
    print("FAIL step1")

# 2) Ubah tampilan Voucher No field — tambah id dan hint
old_voucher_field = '''                    <div class="col-md-6 mb-3">
                        <label class="form-label">Voucher No.</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#16a34a;color:white;"><i class="fas fa-hashtag"></i></span>
                            <input type="text" class="form-control fw-bold" value="{{ $mutasiPembayaran->nomor ?? \'-\' }}" readonly style="background-color:#f0fdf4;color:#14532d;letter-spacing:1px;">
                        </div>
                    </div>'''
new_voucher_field = '''                    <div class="col-md-6 mb-3">
                        <label class="form-label">Voucher No.</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#16a34a;color:white;"><i class="fas fa-hashtag"></i></span>
                            <input type="text" id="edit_voucher_no_display" class="form-control fw-bold"
                                value="{{ $mutasiPembayaran->nomor ?? \'-\' }}" readonly
                                style="background-color:#f0fdf4;color:#14532d;letter-spacing:1px;">
                        </div>
                        <small class="text-muted mt-1 d-block" id="edit_voucher_no_hint">
                            <i class="fas fa-info-circle me-1"></i>Nomor akan diperbarui jika tipe akun berubah
                        </small>
                    </div>'''

if old_voucher_field in content:
    content = content.replace(old_voucher_field, new_voucher_field, 1)
    print("OK step2: voucher no field updated with id and hint")
else:
    print("FAIL step2 - trying without escape")
    # Try without the escaped quote
    old2 = "value=\"{{ $mutasiPembayaran->nomor ?? '-' }}\" readonly style=\"background-color:#f0fdf4;color:#14532d;letter-spacing:1px;\">"
    idx = content.find(old2)
    print(f"  Found at: {idx}")

with open(path, 'w') as f:
    f.write(content)
print("Written.")
