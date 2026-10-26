@extends('layouts.app') @section('title','Dashboard')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><div class="muted small mb-1">RINGKASAN OPERASIONAL</div><h2 class="fw-bold mb-1">Dashboard</h2><div class="muted">Pantau performa Titik Temu Kopi hari ini.</div></div><a href="{{route('pos.create')}}" class="btn btn-success px-3 py-2"><i class="bi bi-plus-lg me-2"></i>Transaksi Baru</a></div>
<div class="row g-3 mb-4"><div class="col-6 col-xl-3"><div class="card p-3 p-lg-4 h-100"><div class="d-flex justify-content-between align-items-start mb-3"><span class="muted small">Omset Hari Ini</span><span class="rounded-2 px-2 py-1" style="background:#e7f5ee;color:#16845e"><i class="bi bi-currency-exchange"></i></span></div><div class="metric text-nowrap">Rp {{number_format($revenue,0,',','.')}}</div><small class="muted mt-2">Transaksi lunas</small></div></div><div class="col-6 col-xl-3"><div class="card p-3 p-lg-4 h-100"><div class="d-flex justify-content-between align-items-start mb-3"><span class="muted small">Pesanan Lunas</span><span class="rounded-2 px-2 py-1" style="background:#eaf1fb;color:#3974c6"><i class="bi bi-receipt"></i></span></div><div class="metric">{{$count}}</div><small class="muted mt-2">Hari ini</small></div></div><div class="col-6 col-xl-3"><div class="card p-3 p-lg-4 h-100"><div class="d-flex justify-content-between align-items-start mb-3"><span class="muted small">Menu Terlaris</span><span class="rounded-2 px-2 py-1" style="background:#f1edfb;color:#7857bd"><i class="bi bi-stars"></i></span></div><div class="fs-5 fw-bold">{{ $best->product_name ?? '—' }}</div><small class="muted mt-2">{{ $best->qty ?? 0 }} item terjual hari ini</small></div></div><div class="col-6 col-xl-3"><div class="card p-3 p-lg-4 h-100"><div class="d-flex justify-content-between align-items-start mb-3"><span class="muted small">Stok Rendah</span><span class="rounded-2 px-2 py-1" style="background:#fff0e6;color:#d37b35"><i class="bi bi-exclamation-triangle"></i></span></div><div class="metric text-danger">{{$low}}</div><small class="muted mt-2">Produk menyentuh batas minimum</small></div></div></div>
<div class="row g-3"><div class="col-lg-7"><div class="card p-3 p-md-4 h-100"><div class="d-flex justify-content-between align-items-start"><div><h5 class="fw-bold mb-1">Omset 7 Hari Terakhir</h5><small class="muted">Performa penjualan harian</small></div><span class="badge rounded-pill text-bg-light border fw-medium px-3 py-2">7 hari</span></div>@php($weekTotal=$days->sum('total')) @php($activeDays=$days->filter(fn($day)=>$day['total']>0)->count())<div class="d-flex align-items-center gap-4 border-bottom py-3 mt-2"><div><div class="muted small">Total omset</div><strong class="fs-5">Rp {{number_format($weekTotal,0,',','.')}}</strong></div><div class="vr" style="height:34px"></div><div><div class="muted small">Hari ada penjualan</div><strong>{{$activeDays}} <span class="muted fw-normal fs-6">dari 7 hari</span></strong></div></div><div class="position-relative mt-3" style="height:220px"><canvas id="revenueChart" aria-label="Grafik omset tujuh hari terakhir" role="img"></canvas></div></div></div><div class="col-lg-5"><div class="card p-3 p-md-4 h-100"><div class="d-flex justify-content-between align-items-start mb-2"><div><h5 class="fw-bold mb-1">Transaksi Terbaru</h5><small class="muted">Aktivitas pembayaran terakhir</small></div><a href="{{route('pos.create')}}" class="small text-decoration-none">Buka kasir</a></div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Invoice</th><th>Total</th><th>Status</th></tr></thead><tbody>@forelse($latest as $o)<tr><td><a href="{{route('orders.show',$o)}}" class="text-decoration-none fw-semibold">{{Str::limit($o->invoice_number,22)}}</a><div class="small muted">{{$o->user->name}}</div></td><td class="text-nowrap">Rp {{number_format($o->grand_total,0,',','.')}}</td><td><span class="pill">Lunas</span></td></tr>@empty<tr><td colspan="3" class="muted">Belum ada transaksi.</td></tr>@endforelse</tbody></table></div></div></div></div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
const revenueCanvas=document.getElementById('revenueChart');
const revenueContext=revenueCanvas.getContext('2d');
const revenueFill=revenueContext.createLinearGradient(0,0,0,245);
revenueFill.addColorStop(0,'rgba(20,125,101,.22)');
revenueFill.addColorStop(1,'rgba(20,125,101,0)');
new Chart(revenueCanvas,{
	type:'line',
	data:{
		labels:@json($days->pluck('label')->values()),
		datasets:[{
			data:@json($days->pluck('total')->values()),
			borderColor:'#147d65',
			backgroundColor:revenueFill,
			borderWidth:2.5,
			fill:true,
			tension:.38,
			pointRadius:context=>context.raw===0?3:4,
			pointHoverRadius:6,
			pointBackgroundColor:context=>context.raw===0?'#cbd5d1':'#fff',
			pointBorderColor:'#147d65',
			pointBorderWidth:2
		}]
	},
	options:{
		maintainAspectRatio:false,
		interaction:{intersect:false,mode:'index'},
		plugins:{
			legend:{display:false},
			tooltip:{
				displayColors:false,
				backgroundColor:'#202a36',
				padding:12,
				callbacks:{label:context=>'Omset: Rp '+Number(context.parsed.y).toLocaleString('id-ID')}
			}
		},
		scales:{
			x:{grid:{display:false},border:{display:false},ticks:{color:'#788391',font:{size:11}}},
			y:{beginAtZero:true,border:{display:false},grid:{color:'#edf1ef'},ticks:{color:'#788391',font:{size:10},maxTicksLimit:5,callback:value=>value===0?'0':(value>=1000000?(value/1000000)+' jt':(value/1000)+' rb')}}
		}
	}
});
</script>
@endpush
