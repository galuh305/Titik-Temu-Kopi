<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class DashboardController extends Controller {
 public function index(){
  $today=Order::whereDate('created_at',today())->where('status','paid');
  $revenue=(clone $today)->sum('grand_total');$count=(clone $today)->count();
  $best=DB::table('order_details')->join('orders','orders.id','=','order_details.order_id')->where('orders.status','paid')->whereDate('orders.created_at',today())->select('order_details.product_name',DB::raw('SUM(order_details.qty) qty'))->groupBy('order_details.product_name')->orderByDesc('qty')->first();
  $low=Product::where('status',true)->whereColumn('stock','<=','min_stock')->count();
  $latest=Order::with('user')->latest()->take(8)->get();
  $days=collect(range(6,0))->map(function($n){$date=Carbon::today()->subDays($n);return ['label'=>$date->format('d/m'),'total'=>Order::whereDate('created_at',$date)->where('status','paid')->sum('grand_total')];});
  return view('dashboard',compact('revenue','count','best','low','latest','days'));
 }
}
