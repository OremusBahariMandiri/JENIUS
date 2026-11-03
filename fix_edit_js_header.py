import os
path = os.path.expandvars('$HOME/mnt/jeniusApp/resources/views/data/mutasi-pembayaran/edit.blade.php')
with open(path, 'r') as f:
    content = f.read()

# Ganti blok $(document).ready dan $('#btnSaveHeader') sekaligus
old_ready = """        $(document).ready(function(){
            $('#edit_coa').select2({theme:'bootstrap-5',placeholder:'Select Bank / Cash account...',allowClear:true,width:'100%'});
            const kursEl=document.getElementById('edit_kurs_display');
            if(kursEl){
                kursEl.addEventListener('input',function(){this.value=formatRupiah(this.value);});
                kursEl.addEventListener('blur',function(){if(this.value&&!this.value.includes(','))this.value+=',00';});
            }
            $('#edit_kurs_display').on('input',function(){$('#edit_kurs_value').val(parseRupiah($(this).val()));});
            if(voucherId){initVoucherPanelSelects(voucherId);loadVoucherDetails(voucherId);}
        });

        $('#btnSaveHeader').on('click',function(){
            const tanggal=$('#edit_tanggal').val(),coaId=$('#edit_coa').val();
            if(!tanggal){showFloatingAlert('error','Date is required');return;}
            if(!coaId){showFloatingAlert('error','Bank / Cash account is required');return;}
            showFloatingAlert('saving','Menyimpan header...');
            $.ajax({url:ROUTES.headerUpdate,method:'PUT',
                data:{tanggal,id_md_chart_of_account:coaId,no_cek:$('#edit_no_cek').val(),
                      kurs:parseRupiah($('#edit_kurs_display').val()),memo:$('#edit_memo').val(),_token:CSRF},
                success:r=>r.success?showFloatingAlert('success','Header updated!'):showFloatingAlert('error',r.message||'Gagal'),
                error:xhr=>{const msg=xhr.responseJSON?.errors?Object.values(xhr.responseJSON.errors).flat().join(', '):(xhr.responseJSON?.message||'Gagal');showFloatingAlert('error',msg);}
            });
        });"""

new_ready = """        // Track nomor baru yang di-fetch saat COA berubah
        let _newNomor = null;

        $(document).ready(function(){
            $('#edit_coa').select2({theme:'bootstrap-5',placeholder:'Select Bank / Cash account...',allowClear:true,width:'100%'});
            const kursEl=document.getElementById('edit_kurs_display');
            if(kursEl){
                kursEl.addEventListener('input',function(){this.value=formatRupiah(this.value);});
                kursEl.addEventListener('blur',function(){if(this.value&&!this.value.includes(','))this.value+=',00';});
            }
            $('#edit_kurs_display').on('input',function(){$('#edit_kurs_value').val(parseRupiah($(this).val()));});
            if(voucherId){initVoucherPanelSelects(voucherId);loadVoucherDetails(voucherId);}

            // Preview nomor saat COA berubah
            $('#edit_coa').on('change', function(){
                const val = $(this).val();
                const $disp = $('#edit_voucher_no_display');
                const $hint = $('#edit_voucher_no_hint');
                _newNomor = null;

                if(!val){
                    $hint.html('<i class="fas fa-info-circle me-1"></i>Nomor akan diperbarui jika tipe akun berubah');
                    return;
                }

                const paymentType = $('select#edit_coa option[value="'+val+'"]').data('payment-type')
                                 || $('select#edit_coa option[value="'+val+'"]').attr('data-payment-type');

                if(!paymentType){ return; }

                $hint.html('<i class="fas fa-circle-notch fa-spin me-1"></i>Mengambil preview nomor...');

                fetch('/data/mutasi-pembayaran/next-voucher-no?payment_type='+encodeURIComponent(paymentType), {
                    headers:{'X-Requested-With':'XMLHttpRequest'}
                })
                .then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
                .then(data=>{
                    if(data.success){
                        _newNomor = data.nomor;
                        $disp.val(data.nomor).css({'background-color':'#fef9c3','color':'#854d0e'});
                        $hint.html('<i class="fas fa-exclamation-triangle text-warning me-1"></i>Nomor akan berubah ke <strong>'+data.nomor+'</strong> saat disimpan');
                    }
                })
                .catch(()=>{
                    $hint.html('<i class="fas fa-info-circle me-1"></i>Nomor akan diperbarui jika tipe akun berubah');
                });
            });
        });

        $('#btnSaveHeader').on('click',function(){
            const tanggal=$('#edit_tanggal').val(),coaId=$('#edit_coa').val();
            if(!tanggal){showFloatingAlert('error','Date is required');return;}
            if(!coaId){showFloatingAlert('error','Bank / Cash account is required');return;}
            showFloatingAlert('saving','Menyimpan header...');

            const payload = {
                tanggal, id_md_chart_of_account:coaId,
                no_cek:$('#edit_no_cek').val(),
                kurs:parseRupiah($('#edit_kurs_display').val()),
                memo:$('#edit_memo').val(),
                _token:CSRF
            };
            // Kirim nomor baru jika COA berubah
            if(_newNomor){ payload.new_nomor = _newNomor; }

            $.ajax({url:ROUTES.headerUpdate,method:'PUT', data:payload,
                success:function(r){
                    if(r.success){
                        showFloatingAlert('success','Header updated!');
                        if(r.nomor){
                            $('#edit_voucher_no_display').val(r.nomor).css({'background-color':'#f0fdf4','color':'#14532d'});
                            $('#edit_voucher_no_hint').html('<i class="fas fa-check-circle text-success me-1"></i>Nomor berhasil diperbarui');
                            _newNomor = null;
                        }
                    } else {
                        showFloatingAlert('error',r.message||'Gagal');
                    }
                },
                error:xhr=>{const msg=xhr.responseJSON?.errors?Object.values(xhr.responseJSON.errors).flat().join(', '):(xhr.responseJSON?.message||'Gagal');showFloatingAlert('error',msg);}
            });
        });"""

if old_ready in content:
    content = content.replace(old_ready, new_ready, 1)
    with open(path, 'w') as f:
        f.write(content)
    print("OK: JS updated")
else:
    print("FAIL: block not found")
    idx = content.find("$(document).ready(function(){")
    print(f"  ready found at: {idx}")
    idx2 = content.find("'#btnSaveHeader'")
    print(f"  btnSaveHeader found at: {idx2}")
