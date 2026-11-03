import os
path = os.path.expandvars('$HOME/mnt/jeniusApp/resources/views/data/mutasi-pembayaran/create.blade.php')

with open(path, 'r') as f:
    content = f.read()

old_js = """            // When COA changes, fetch next voucher number
            $('#select_coa').on('change', function() {
                const selected = $(this).find('option:selected');
                const paymentType = selected.data('payment-type');
                const $preview = $('#voucher_no_preview');
                const $hint = $('#voucher_no_hint');

                if (!$(this).val() || !paymentType) {
                    $preview.val('Auto-generate on save');
                    $hint.html('<i class="fas fa-info-circle me-1"></i>Select Bank / Cash first to preview format');
                    return;
                }

                $preview.val('Generating...').css('opacity', 0.5);

                fetch('/data/mutasi-pembayaran/next-voucher-no?payment_type=' + encodeURIComponent(paymentType))
                    .then(r => r.json())
                    .then(data => {
                        $preview.val(data.success ? data.nomor : 'Auto-generate on save').css('opacity', 1);
                        $hint.html('<i class="fas fa-check-circle text-success me-1"></i>Voucher number will be auto-generated when saved');
                    })
                    .catch(() => {
                        $preview.val('Auto-generate on save').css('opacity', 1);
                    });
            });"""

new_js = """            // When COA changes, fetch next voucher number
            $('#select_coa').on('change', function() {
                const val = $(this).val();
                const $preview = $('#voucher_no_preview');
                const $hint = $('#voucher_no_hint');

                if (!val) {
                    $preview.val('Auto-generate on save');
                    $hint.html('<i class="fas fa-info-circle me-1"></i>Select Bank / Cash first to preview format');
                    return;
                }

                // Read payment type directly from the original <option> element
                const paymentType = $('select#select_coa option[value="' + val + '"]').data('payment-type')
                                 || $('select#select_coa option[value="' + val + '"]').attr('data-payment-type');

                if (!paymentType) {
                    $preview.val('Auto-generate on save');
                    $hint.html('<i class="fas fa-info-circle me-1"></i>Cannot determine account type');
                    return;
                }

                $preview.val('Generating...').css('opacity', 0.5);

                fetch('/data/mutasi-pembayaran/next-voucher-no?payment_type=' + encodeURIComponent(paymentType), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        $preview.val(data.success ? data.nomor : 'Auto-generate on save').css('opacity', 1);
                        $hint.html('<i class="fas fa-check-circle text-success me-1"></i>Voucher number will be auto-generated when saved');
                    })
                    .catch(err => {
                        console.error('[VoucherNo]', err);
                        $preview.val('Auto-generate on save').css('opacity', 1);
                        $hint.html('<i class="fas fa-exclamation-circle text-warning me-1"></i>Could not preview – will auto-generate on save');
                    });
            });"""

if old_js in content:
    content = content.replace(old_js, new_js)
    with open(path, 'w') as f:
        f.write(content)
    print("OK: JS updated")
else:
    print("FAIL: block not found")
    idx = content.find("$('#select_coa').on('change'")
    if idx >= 0:
        print("Found change handler at char", idx)
        print(repr(content[idx:idx+200]))
