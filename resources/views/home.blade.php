@extends('layouts.app')

@section('content')

{{-- DASHBOARD HEADER CARD --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3 px-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

            {{-- Title & subtitle --}}
            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-tachometer-alt text-primary me-2"></i>Dashboard
                </h5>
                <p class="text-muted small mb-0 mt-1">
                    Welcome back, <strong>{{ auth()->user()->name }}</strong> &mdash;
                    {{ $now->format('l, d F Y') }}
                </p>
            </div>

            {{-- Date range filter + active users --}}
            <div class="d-flex flex-wrap align-items-center gap-2" id="globalDateBar">
                <span class="text-muted small fw-semibold d-none d-md-inline">Date Range:</span>
                <div class="input-group input-group-sm" style="width:auto">
                    <span class="input-group-text bg-transparent"><i class="fas fa-calendar text-primary"></i></span>
                    <input type="text" id="globalDateFrom" class="form-control form-control-sm"
                           placeholder="From" style="width:120px" value="{{ $defaultFrom }}">
                </div>
                <span class="text-muted small">&mdash;</span>
                <div class="input-group input-group-sm" style="width:auto">
                    <input type="text" id="globalDateTo" class="form-control form-control-sm"
                           placeholder="To" style="width:120px" value="{{ $defaultTo }}">
                </div>
                <button id="btnGlobalApply" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-filter me-1"></i>Apply
                </button>
                <button id="btnGlobalReset" class="btn btn-outline-secondary btn-sm px-2" title="Reset to last 12 months">
                    <i class="fas fa-undo"></i>
                </button>
                <span id="globalDateLabel" class="badge bg-primary small d-none"></span>
                <div id="globalLoadingSpinner" class="d-none">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="vr opacity-25 d-none d-sm-block mx-1"></div>

                {{-- Active users --}}
                <span class="badge bg-info px-3 py-2">
                    <i class="fas fa-users me-1"></i>{{ $activeUsers }} Active (30d)
                </span>
            </div>

        </div>
    </div>
</div>

{{-- KPI ROW 1 --}}
<div class="row g-3 mb-3" id="kpiRow">
    @php
    $kpi1 = [
        ['label'=>'JO Contract','key'=>'jo_contract','color'=>'#10b981','icon'=>'fa-file-contract','month'=>$joThisMonth['contract']],
        ['label'=>'JO Tramper', 'key'=>'jo_tramper', 'color'=>'#3b82f6','icon'=>'fa-truck',         'month'=>$joThisMonth['tramper']],
        ['label'=>'JO Other',   'key'=>'jo_other',   'color'=>'#f59e0b','icon'=>'fa-box',           'month'=>$joThisMonth['other']],
        ['label'=>'JO Total',   'key'=>'jo_total',   'color'=>'#8b5cf6','icon'=>'fa-layer-group',   'month'=>$joThisMonth['contract']+$joThisMonth['tramper']+$joThisMonth['other']],
    ];
    @endphp
    @foreach($kpi1 as $k)
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid {{ $k['color'] }} !important">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">{{ $k['label'] }}</div>
                        <div class="h3 fw-bold mb-0 kpi-value" data-key="{{ $k['key'] }}">{{ number_format($stats[$k['key']]) }}</div>
                        <div class="small mt-1">
                            <span class="text-success"><i class="fas fa-plus"></i> {{ $k['month'] }}</span>
                            <span class="text-muted ms-1">this month</span>
                        </div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:42px;height:42px;background:{{ $k['color'] }}20">
                        <i class="fas {{ $k['icon'] }}" style="color:{{ $k['color'] }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- KPI ROW 2 --}}
<div class="row g-3 mb-4">
    @php
    $kpi2 = [
        ['label'=>'Cash Advance',     'key'=>'kasbon_total',  'icon'=>'fa-money-bill-wave',    'color'=>'text-warning'],
        ['label'=>'LPJ Total',        'key'=>'lpj_total',     'icon'=>'fa-clipboard-check',    'color'=>'text-success'],
        ['label'=>'Invoice Total',    'key'=>'invoice_total', 'icon'=>'fa-file-invoice-dollar','color'=>'text-primary'],
        ['label'=>'Payment Mutation', 'key'=>'mutasi_bayar',  'icon'=>'fa-arrow-circle-up',    'color'=>'text-danger'],
        ['label'=>'Receipt Mutation', 'key'=>'mutasi_terima', 'icon'=>'fa-arrow-circle-down',  'color'=>'text-info'],
        ['label'=>'Customers',        'key'=>'customers',     'icon'=>'fa-building',           'color'=>'text-secondary'],
    ];
    @endphp
    @foreach($kpi2 as $k)
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <i class="fas {{ $k['icon'] }} {{ $k['color'] }} fa-lg mb-2"></i>
                <div class="h5 fw-bold mb-0 kpi-value" data-key="{{ $k['key'] }}">{{ number_format($stats[$k['key']]) }}</div>
                <div class="small text-muted">{{ $k['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- CHART ROW 1: JO Trend + JO Donut --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="fw-bold mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Job Order Trend</h6>
                    <small class="text-muted" id="joTrendRangeLabel"></small>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="d-flex align-items-center gap-1 chart-date-filter" data-chart="jo">
                        <input type="text" class="form-control form-control-sm chart-date-from" placeholder="From" style="width:110px">
                        <span class="text-muted small">-</span>
                        <input type="text" class="form-control form-control-sm chart-date-to" placeholder="To" style="width:110px">
                        <button class="btn btn-sm btn-outline-primary chart-apply-btn px-2" title="Apply"><i class="fas fa-check"></i></button>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active" onclick="setJoType(this,'grouped')">Grouped</button>
                        <button class="btn btn-outline-primary" onclick="setJoType(this,'stacked')">Stacked</button>
                    </div>
                </div>
            </div>
            <div class="card-body"><canvas id="joTrendChart" height="260"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="fw-bold mb-0"><i class="fas fa-chart-pie text-info me-2"></i>JO Distribution</h6>
                    <small class="text-muted" id="joDonutRangeLabel"></small>
                </div>
                <div class="chart-date-filter d-flex align-items-center gap-1" data-chart="jo-donut">
                    <input type="text" class="form-control form-control-sm chart-date-from" placeholder="From" style="width:110px">
                    <span class="text-muted small">-</span>
                    <input type="text" class="form-control form-control-sm chart-date-to" placeholder="To" style="width:110px">
                    <button class="btn btn-sm btn-outline-info chart-apply-btn px-2" title="Apply"><i class="fas fa-check"></i></button>
                </div>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="max-width:280px;width:100%">
                    <canvas id="joDonutChart"></canvas>
                    <div id="joDonutLegend" class="mt-3 d-flex justify-content-center gap-3 flex-wrap small"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CHART ROW 2: Kasbon + Invoice --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="fw-bold mb-0"><i class="fas fa-chart-line text-warning me-2"></i>Cash Advance Trend</h6>
                    <small class="text-muted" id="kasbonRangeLabel"></small>
                </div>
                <div class="chart-date-filter d-flex align-items-center gap-1" data-chart="kasbon">
                    <input type="text" class="form-control form-control-sm chart-date-from" placeholder="From" style="width:110px">
                    <span class="text-muted small">-</span>
                    <input type="text" class="form-control form-control-sm chart-date-to" placeholder="To" style="width:110px">
                    <button class="btn btn-sm btn-outline-warning chart-apply-btn px-2" title="Apply"><i class="fas fa-check"></i></button>
                </div>
            </div>
            <div class="card-body"><canvas id="kasbonTrendChart" height="260"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="fw-bold mb-0"><i class="fas fa-file-invoice text-success me-2"></i>Invoice by Status</h6>
                    <small class="text-muted" id="invoiceRangeLabel"></small>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="chart-date-filter d-flex align-items-center gap-1" data-chart="invoice">
                        <input type="text" class="form-control form-control-sm chart-date-from" placeholder="From" style="width:110px">
                        <span class="text-muted small">-</span>
                        <input type="text" class="form-control form-control-sm chart-date-to" placeholder="To" style="width:110px">
                        <button class="btn btn-sm btn-outline-success chart-apply-btn px-2" title="Apply"><i class="fas fa-check"></i></button>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-success active" onclick="setInvSrc(this,'all')">All</button>
                        <button class="btn btn-outline-success" onclick="setInvSrc(this,'contract')">Contract</button>
                        <button class="btn btn-outline-success" onclick="setInvSrc(this,'tramper')">Tramper</button>
                        <button class="btn btn-outline-success" onclick="setInvSrc(this,'other')">Other</button>
                    </div>
                </div>
            </div>
            <div class="card-body"><canvas id="invoiceStatusChart" height="260"></canvas></div>
        </div>
    </div>
</div>

{{-- BOTTOM ROW --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="fw-bold mb-0"><i class="fas fa-history text-secondary me-2"></i>Recent Activity</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr><th class="ps-3">Time</th><th>User</th><th>Action</th><th>Module</th><th>Description</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogs as $log)
                            <tr>
                                <td class="ps-3 text-nowrap small text-muted">{{ $log->created_at->diffForHumans() }}</td>
                                <td class="small">{{ $log->user_name ?? '-' }}</td>
                                <td><span class="badge bg-{{ $log->action_badge }}">{{ $log->action_label }}</span></td>
                                <td class="small">{{ $log->module ?? '-' }}</td>
                                <td class="small text-truncate" style="max-width:160px">{{ $log->description }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No activity recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(auth()->check() && (auth()->user()->is_admin ?? false))
            <div class="card-footer bg-transparent text-end py-2">
                <a href="{{ route('activity-log.index') }}" class="btn btn-sm btn-outline-secondary">
                    View All <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-trophy text-warning me-2"></i>Top Customers (JO Tramper)</h6>
                <div class="chart-date-filter d-flex align-items-center gap-1" data-chart="customers">
                    <input type="text" class="form-control form-control-sm chart-date-from" placeholder="From" style="width:105px">
                    <span class="text-muted small">-</span>
                    <input type="text" class="form-control form-control-sm chart-date-to" placeholder="To" style="width:105px">
                    <button class="btn btn-sm btn-outline-warning chart-apply-btn px-2" title="Apply"><i class="fas fa-check"></i></button>
                </div>
            </div>
            <div class="card-body" id="topCustomersBody">
                @php
                $custMax    = collect($topCustomers)->max('total') ?: 1;
                $custColors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444'];
                @endphp
                @forelse($topCustomers as $i => $cust)
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-semibold text-truncate" style="max-width:70%">{{ $cust['name'] }}</span>
                        <span class="badge" style="background:{{ $custColors[$i] ?? '#6b7280' }}">{{ $cust['total'] }} JO</span>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar" style="width:{{ round($cust['total']/$custMax*100) }}%;background:{{ $custColors[$i] ?? '#6b7280' }}"></div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>No data in this period
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
.global-date-bar{background:var(--card-bg,#fff);border:1px solid var(--border-color,#e5e7eb);border-radius:10px;padding:12px 18px;box-shadow:0 1px 4px rgba(0,0,0,.06)}
[data-theme="dark"] .global-date-bar{background:#1e293b;border-color:#334155}
.chart-date-filter input{font-size:.75rem}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
/* ── Bootstrap data ───────────────────────────── */
const _joMonthly     = @json($joMonthly);
const _kasbonMonthly = @json($kasbonMonthly);
const _invoiceStatus = @json($invoiceStatus);
const _topCustomers  = @json($topCustomers);
const _defaultFrom   = '{{ $defaultFrom }}';
const _defaultTo     = '{{ $defaultTo }}';
const _chartDataUrl  = '{{ route("home.chart-data") }}';

/* ── Helpers ─────────────────────────────────── */
function isDark(){return document.documentElement.getAttribute('data-theme')==='dark';}
function colors(){return{text:isDark()?'#94a3b8':'#6b7280',grid:isDark()?'rgba(255,255,255,0.07)':'rgba(0,0,0,0.06)'};}
function fmtRange(f,t){
    if(!f||!t)return'';
    const o={day:'numeric',month:'short',year:'numeric'};
    return new Date(f).toLocaleDateString('en-GB',o)+' – '+new Date(t).toLocaleDateString('en-GB',o);
}

/* ── State ───────────────────────────────────── */
let globalFrom=_defaultFrom,globalTo=_defaultTo;
const chartOverrides={jo:null,'jo-donut':null,kasbon:null,invoice:null,customers:null};
let joStacked=false,invSrc='all';
let joChart,donutChart,kasbonChart,invChart;

/* ── Chart builders ──────────────────────────── */
function buildJoChart(data){
    const c=colors();
    const cfg={type:'bar',data:{labels:data.map(d=>d.label),datasets:[
        {label:'Contract',data:data.map(d=>d.contract),backgroundColor:'rgba(16,185,129,0.8)',borderRadius:4},
        {label:'Tramper', data:data.map(d=>d.tramper), backgroundColor:'rgba(59,130,246,0.8)',borderRadius:4},
        {label:'Other',   data:data.map(d=>d.other),   backgroundColor:'rgba(245,158,11,0.8)',borderRadius:4},
    ]},options:{responsive:true,interaction:{mode:'index',intersect:false},
        scales:{x:{stacked:joStacked,ticks:{color:c.text},grid:{color:c.grid}},
                y:{stacked:joStacked,beginAtZero:true,ticks:{color:c.text,stepSize:1},grid:{color:c.grid}}},
        plugins:{legend:{labels:{color:c.text}},tooltip:{backgroundColor:isDark()?'#1e293b':'#fff',titleColor:c.text,bodyColor:c.text,borderColor:c.grid,borderWidth:1}}}};
    if(joChart)joChart.destroy();
    joChart=new Chart(document.getElementById('joTrendChart'),cfg);
}

function buildDonutChart(data){
    const contract=data.reduce((s,d)=>s+d.contract,0),tramper=data.reduce((s,d)=>s+d.tramper,0),other=data.reduce((s,d)=>s+d.other,0),total=contract+tramper+other||1;
    const c=colors();
    if(donutChart)donutChart.destroy();
    donutChart=new Chart(document.getElementById('joDonutChart'),{type:'doughnut',
        data:{labels:['Contract','Tramper','Other'],datasets:[{data:[contract,tramper,other],backgroundColor:['rgba(16,185,129,0.85)','rgba(59,130,246,0.85)','rgba(245,158,11,0.85)'],borderWidth:2,borderColor:isDark()?'#1e293b':'#fff',hoverOffset:8}]},
        options:{cutout:'70%',responsive:true,plugins:{legend:{display:false},tooltip:{backgroundColor:isDark()?'#1e293b':'#fff',titleColor:c.text,bodyColor:c.text,borderColor:c.grid,borderWidth:1}}}});
    const legend=document.getElementById('joDonutLegend'),names=['Contract','Tramper','Other'],vals=[contract,tramper,other],cls=['#10b981','#3b82f6','#f59e0b'];
    legend.innerHTML=names.map((n,i)=>`<div class="d-flex align-items-center gap-1"><span style="width:10px;height:10px;border-radius:2px;background:${cls[i]};display:inline-block"></span><span>${n}: <strong>${vals[i]}</strong> <span class="text-muted">(${Math.round(vals[i]/total*100)}%)</span></span></div>`).join('');
}

function buildKasbonChart(data){
    const c=colors();
    if(kasbonChart)kasbonChart.destroy();
    kasbonChart=new Chart(document.getElementById('kasbonTrendChart'),{type:'line',
        data:{labels:data.map(d=>d.label),datasets:[
            {label:'Contract',data:data.map(d=>d.contract),borderColor:'#10b981',backgroundColor:'rgba(16,185,129,0.1)',fill:true,tension:0.4,pointRadius:4},
            {label:'Tramper', data:data.map(d=>d.tramper), borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,0.1)',fill:true,tension:0.4,pointRadius:4},
            {label:'Other',   data:data.map(d=>d.other),   borderColor:'#f59e0b',backgroundColor:'rgba(245,158,11,0.1)',fill:true,tension:0.4,pointRadius:4},
            {label:'General', data:data.map(d=>d.general), borderColor:'#8b5cf6',backgroundColor:'rgba(139,92,246,0.1)',fill:true,tension:0.4,pointRadius:4},
        ]},
        options:{responsive:true,interaction:{mode:'index',intersect:false},
            scales:{x:{ticks:{color:c.text},grid:{color:c.grid}},y:{beginAtZero:true,ticks:{color:c.text,stepSize:1},grid:{color:c.grid}}},
            plugins:{legend:{labels:{color:c.text}},tooltip:{backgroundColor:isDark()?'#1e293b':'#fff',titleColor:c.text,bodyColor:c.text,borderColor:c.grid,borderWidth:1}}}});
}

function buildInvChart(data){
    const c=colors();let raw={};
    if(invSrc==='all'){['contract','tramper','other'].forEach(src=>{Object.entries(data[src]||{}).forEach(([st,ct])=>{raw[st]=(raw[st]||0)+ct;});});}
    else raw=data[invSrc]||{};
    const statusColors={paid:'#10b981',lunas:'#10b981',unpaid:'#ef4444',belum:'#ef4444',partial:'#f59e0b',pending:'#3b82f6',cancel:'#6b7280',cancelled:'#6b7280'};
    const labels=Object.keys(raw).map(s=>s.charAt(0).toUpperCase()+s.slice(1));
    const values=Object.values(raw);
    const bgColors=Object.keys(raw).map(s=>statusColors[s.toLowerCase()]||'#8b5cf6');
    if(invChart)invChart.destroy();
    invChart=new Chart(document.getElementById('invoiceStatusChart'),{type:'bar',
        data:{labels,datasets:[{label:'Invoices',data:values,backgroundColor:bgColors,borderRadius:5,indexAxis:'y'}]},
        options:{indexAxis:'y',responsive:true,
            scales:{x:{beginAtZero:true,ticks:{color:c.text,stepSize:1},grid:{color:c.grid}},y:{ticks:{color:c.text},grid:{display:false}}},
            plugins:{legend:{display:false},tooltip:{backgroundColor:isDark()?'#1e293b':'#fff',titleColor:c.text,bodyColor:c.text,borderColor:c.grid,borderWidth:1}}}});
}

function renderTopCustomers(data){
    const cols=['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444'],max=Math.max(...data.map(d=>d.total),1);
    document.getElementById('topCustomersBody').innerHTML=data.length===0
        ?'<div class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>No data in this period</div>'
        :data.map((cust,i)=>`<div class="mb-3"><div class="d-flex justify-content-between small mb-1"><span class="fw-semibold text-truncate" style="max-width:70%">${cust.name}</span><span class="badge" style="background:${cols[i]||'#6b7280'}">${cust.total} JO</span></div><div class="progress" style="height:8px"><div class="progress-bar" style="width:${Math.round(cust.total/max*100)}%;background:${cols[i]||'#6b7280'}"></div></div></div>`).join('');
}

/* ── Range labels ────────────────────────────── */
function updateRangeLabel(id,f,t){const el=document.getElementById(id);if(el)el.textContent=fmtRange(f,t);}
function updateAllRangeLabels(f,t){['joTrendRangeLabel','joDonutRangeLabel','kasbonRangeLabel','invoiceRangeLabel'].forEach(id=>updateRangeLabel(id,f,t));}

/* ── AJAX ────────────────────────────────────── */
async function fetchChart(chart,from,to){
    const url=new URL(_chartDataUrl,window.location.origin);
    url.searchParams.set('chart',chart);
    url.searchParams.set('date_from',from);
    url.searchParams.set('date_to',to);
    const res=await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
    if(!res.ok)throw new Error(await res.text());
    return res.json();
}

function effectiveRange(k){return chartOverrides[k]??{from:globalFrom,to:globalTo};}

async function refreshChart(k){
    const{from,to}=effectiveRange(k);
    try{
        if(k==='jo'||k==='jo-donut'){
            const d=await fetchChart('jo',from,to);
            buildJoChart(d);buildDonutChart(d);
            updateRangeLabel('joTrendRangeLabel',from,to);updateRangeLabel('joDonutRangeLabel',from,to);
        }else if(k==='kasbon'){
            const d=await fetchChart('kasbon',from,to);buildKasbonChart(d);updateRangeLabel('kasbonRangeLabel',from,to);
        }else if(k==='invoice'){
            const d=await fetchChart('invoice',from,to);buildInvChart(d);updateRangeLabel('invoiceRangeLabel',from,to);
        }else if(k==='customers'){
            const d=await fetchChart('customers',from,to);renderTopCustomers(d);
        }
    }catch(e){console.error('Chart error:',k,e);}
}

async function refreshAllCharts(){
    document.getElementById('globalLoadingSpinner').classList.remove('d-none');
    try{await Promise.all(['jo','kasbon','invoice','customers'].map(k=>refreshChart(k)));}
    finally{document.getElementById('globalLoadingSpinner').classList.add('d-none');}
}

/* ── Toggle callbacks ────────────────────────── */
function setJoType(btn,type){
    joStacked=(type==='stacked');
    btn.closest('.btn-group').querySelectorAll('.btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    const{from,to}=effectiveRange('jo');
    fetchChart('jo',from,to).then(d=>buildJoChart(d));
}
function setInvSrc(btn,src){
    invSrc=src;
    btn.closest('.btn-group').querySelectorAll('.btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    const{from,to}=effectiveRange('invoice');
    fetchChart('invoice',from,to).then(d=>buildInvChart(d));
}

/* ── DOMContentLoaded ────────────────────────── */
document.addEventListener('DOMContentLoaded',()=>{
    /* Global pickers */
    flatpickr('#globalDateFrom',{dateFormat:'Y-m-d',allowInput:true});
    flatpickr('#globalDateTo',  {dateFormat:'Y-m-d',allowInput:true});

    /* Per-chart pickers */
    document.querySelectorAll('.chart-date-filter').forEach(block=>{
        const fi=block.querySelector('.chart-date-from'),ti=block.querySelector('.chart-date-to');
        const ab=block.querySelector('.chart-apply-btn'),ck=block.dataset.chart;
        fi.value=globalFrom;ti.value=globalTo;
        flatpickr(fi,{dateFormat:'Y-m-d',allowInput:true});
        flatpickr(ti,{dateFormat:'Y-m-d',allowInput:true});
        ab.addEventListener('click',()=>{
            if(!fi.value||!ti.value)return;
            const r={from:fi.value,to:ti.value};
            chartOverrides[ck]=r;
            if(ck==='jo'||ck==='jo-donut'){chartOverrides['jo']=chartOverrides['jo-donut']=r;}
            refreshChart(ck);
        });
    });

    /* Global apply */
    document.getElementById('btnGlobalApply').addEventListener('click',async()=>{
        const f=document.getElementById('globalDateFrom').value,t=document.getElementById('globalDateTo').value;
        if(!f||!t)return;
        globalFrom=f;globalTo=t;
        document.querySelectorAll('.chart-date-filter').forEach(block=>{
            if(!chartOverrides[block.dataset.chart]){
                block.querySelector('.chart-date-from').value=f;
                block.querySelector('.chart-date-to').value=t;
            }
        });
        const lbl=document.getElementById('globalDateLabel');
        lbl.textContent=fmtRange(f,t);lbl.classList.remove('d-none');
        await refreshAllCharts();
    });

    /* Global reset */
    document.getElementById('btnGlobalReset').addEventListener('click',async()=>{
        globalFrom=_defaultFrom;globalTo=_defaultTo;
        document.getElementById('globalDateFrom').value=_defaultFrom;
        document.getElementById('globalDateTo').value=_defaultTo;
        Object.keys(chartOverrides).forEach(k=>chartOverrides[k]=null);
        document.querySelectorAll('.chart-date-filter').forEach(block=>{
            block.querySelector('.chart-date-from').value=_defaultFrom;
            block.querySelector('.chart-date-to').value=_defaultTo;
        });
        document.getElementById('globalDateLabel').classList.add('d-none');
        await refreshAllCharts();
    });

    /* Initial render */
    buildJoChart(_joMonthly);
    buildDonutChart(_joMonthly);
    buildKasbonChart(_kasbonMonthly);
    buildInvChart(_invoiceStatus);
    updateAllRangeLabels(globalFrom,globalTo);

    /* Dark mode reactivity */
    new MutationObserver(()=>{
        const jd=joChart&&joChart.data&&joChart.data.datasets;
        if(jd){
            const reb=jd[0].data.map((_,i)=>({label:joChart.data.labels[i],contract:jd[0].data[i],tramper:jd[1].data[i],other:jd[2].data[i]}));
            buildJoChart(reb);buildDonutChart(reb);
        }else{buildJoChart(_joMonthly);buildDonutChart(_joMonthly);}
        const kd=kasbonChart&&kasbonChart.data&&kasbonChart.data.datasets;
        if(kd){
            const reb=kd[0].data.map((_,i)=>({label:kasbonChart.data.labels[i],contract:kd[0].data[i],tramper:kd[1].data[i],other:kd[2].data[i],general:kd[3].data[i]}));
            buildKasbonChart(reb);
        }else buildKasbonChart(_kasbonMonthly);
        buildInvChart(_invoiceStatus);
    }).observe(document.documentElement,{attributes:true,attributeFilter:['data-theme']});
});
</script>

@endsection
