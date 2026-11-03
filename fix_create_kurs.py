import os
path = os.path.expandvars('$HOME/mnt/jeniusApp/resources/views/data/mutasi-pembayaran/create.blade.php')
with open(path, 'r') as f:
    content = f.read()

old = '                                    <input type="text" name="kurs" id="kurs_display" class="form-control"'
new = '                                    <input type="text" id="kurs_display" class="form-control"'

if old in content:
    content = content.replace(old, new, 1)
    with open(path, 'w') as f:
        f.write(content)
    print("OK: kurs display name removed")
else:
    print("FAIL: pattern not found")
