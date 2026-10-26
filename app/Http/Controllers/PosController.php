<?php
namespace App\Http\Controllers;
use App\Models\Order;use App\Models\OrderDetail;use App\Models\Product;
use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PosController extends Controller {
 public function create(){ $products=Product::with('category')->where('status',true)->where('stock','>',0)->orderBy('name')->get();return view('pos.create',compact('products')); }
 public function store(Request $r){
  $d=$r->validate([
   'order_type'=>'required|in:dine_in,takeaway','table_number'=>'nullable|string|max:20','customer_name'=>'nullable|string|max:100',
   'payment_method'=>'required|in:cash,qris,bank_transfer,debit','discount'=>'nullable|integer|min:0',
   'amount_paid'=>'required|integer|min:0','items'=>'required|array|min:1','items.*.product_id'=>'required|integer|exists:products,id',
   'items.*.qty'=>'required|integer|min:1|max:100','items.*.note'=>'nullable|string|max:200'
  ]);
  if($d['order_type']==='dine_in' && blank($d['table_number']??null)) throw ValidationException::withMessages(['table_number'=>'Nomor meja wajib untuk Dine-In.']);
  $dupes=[];foreach($d['items'] as $i){$dupes[$i['product_id']]=($dupes[$i['product_id']]??0)+(int)$i['qty'];}
  $order=DB::transaction(function() use($d,$dupes){
   $products=Product::whereIn('id',array_keys($dupes))->lockForUpdate()->get()->keyBy('id');
   $subtotal=0;
   foreach($dupes as $id=>$qty){$p=$products->get($id);if(!$p||!$p->status)throw ValidationException::withMessages(['items'=>'Menu tidak tersedia.']);if($p->stock<$qty)throw ValidationException::withMessages(['items'=>"Stok {$p->name} tidak mencukupi."]);$subtotal += $p->price*$qty;}
   $discount=min((int)($d['discount']??0),$subtotal);
   $tax=(int)round(($subtotal-$discount)*0.10);
   $grand=$subtotal-$discount+$tax;
   if($d['payment_method']==='cash' && (int)$d['amount_paid']<$grand)throw ValidationException::withMessages(['amount_paid'=>'Uang tunai kurang dari total tagihan.']);
   if($d['payment_method']!=='cash' && (int)$d['amount_paid']<$grand)throw ValidationException::withMessages(['amount_paid'=>'Nominal pembayaran harus minimal sebesar tagihan.']);
   $invoice='INV-POS-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
   // Retry unlikely invoice collision via unique DB constraint; do not silently duplicate.
   $o=Order::create(['invoice_number'=>$invoice,'user_id'=>auth()->id(),'order_type'=>$d['order_type'],'table_number'=>$d['order_type']==='dine_in'?$d['table_number']:null,'customer_name'=>$d['customer_name']??null,'subtotal'=>$subtotal,'tax_amount'=>$tax,'discount_amount'=>$discount,'grand_total'=>$grand,'payment_method'=>$d['payment_method'],'amount_paid'=>$d['amount_paid'],'change_amount'=>max(0,(int)$d['amount_paid']-$grand),'status'=>'paid']);
   foreach($d['items'] as $line){$p=$products[$line['product_id']];$qty=(int)$line['qty'];OrderDetail::create(['order_id'=>$o->id,'product_id'=>$p->id,'product_name'=>$p->name,'unit_price'=>$p->price,'qty'=>$qty,'line_subtotal'=>$p->price*$qty,'special_note'=>$line['note']??null]);}
   foreach($dupes as $id=>$qty)$products[$id]->decrement('stock',$qty);
   return $o;
  },3);
  return redirect()->route('orders.show',$order)->with('success','Transaksi berhasil disimpan.');
 }
 public function show(Order $order){
  if(auth()->user()->role!=='admin' && $order->user_id!==auth()->id())abort(403);
  $order->load('details','user');return view('pos.receipt',compact('order'));
 }
}
