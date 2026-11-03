import os
p = os.path.expandvars("$HOME/mnt/jeniusApp/resources/views/data/mutasi-pembayaran/edit.blade.php")
chunk = """
        function showKasbonInfo(btn){
            const d=btn.dataset;
            document.getElementById('ki-nomor-title').textContent=d.nomor||'-';
            document.getElementById('ki-release').textContent=d.rel||'-';
            document.getElementById('ki-dept').textContent=d.dep||'-';
            document.getElementById('ki-cabang').textContent=d.cab||'-';
            document.getElementById('ki-tgl').textContent=d.tgl||'-';
            document.getElementById('ki-tgl-release').textContent=d.tglRel||'-';
            const kw=document.getElementById('ki-keterangan-wrap'),ket=d.keterangan||'';
            if(ket){document.getElementById('ki-keterangan').textContent=ket;kw.style.display='block';}
            else kw.style.display='none';
            let items=[];
            try{items=JSON.parse(d.items||'[]');}catch(e){}
            document.getElementById('ki-item-count').textContent=items.length+' item';
            const tbody=document.getElementById('ki-tbody'),tfoot=document.getElementById('ki-tfoot');
            if(!items.length){tbody.innerHTML='<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada item</td></tr>';tfoot.innerHTML='';}
            else{
                let totN=0,totP=0,totS=0,html='',rowNo=0;
                const groups=[];
                items.forEach(item=>{
                    const last=groups[groups.length-1];
                    if(last&&last.cat===(item.kategori||'-'))last.rows.push(item);
                    else groups.push({cat:item.kategori||'-',rows:[item]});
                });
                groups.forEach(g=>{
                    g.rows.forEach((item,ii)=>{
                        rowNo++;
                        const n=parseFloat(item.nilai_kasbon||0),pv=parseFloat(item.pj||0),s=parseFloat(item.selisih||0);
                        totN+=n;totP+=pv;totS+=s;
                        const sC=s>0.001?'clr-pos':s<-0.001?'clr-neg':'clr-zero';
                        html+='<tr>';
                        if(ii===0){html+=`<td rowspan="${g.rows.length}" class="text-center fw-bold" style="vertical-align:middle;">${rowNo}</td><td rowspan="${g.rows.length}" class="text-muted" style="vertical-align:middle;background:#f0fdf4;">${escHtml(g.cat)}</td>`;}
                        html+=`<td style="font-size:.8rem;">${escHtml(item.label||'-')}</td><td class="num">${formatNumber(n)}</td><td class="num">${formatNumber(pv)}</td><td class="num ${sC}">${formatNumber(s)}</td></tr>`;
                    });
                });
                tbody.innerHTML=html;
                tfoot.innerHTML=`<tr><td colspan="3" class="text-end fw-bold">GRAND TOTAL</td><td class="num">${formatNumber(totN)}</td><td class="num">${formatNumber(totP)}</td><td class="num">${formatNumber(totS)}</td></tr>`;
            }
            new bootstrap.Modal(document.getElementById('modalKasbonInfo')).show();
        }
    </script>
@endpush
"""
with open(p, 'a') as f:
    f.write(chunk)
sz = os.path.getsize(p)
print(f"chunk5g (final) ok, size: {sz} bytes")
# Count lines
with open(p) as f:
    lines = f.readlines()
print(f"Total lines: {len(lines)}")
