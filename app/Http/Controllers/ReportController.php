<?php
namespace App\Http\Controllers;
use App\Models\Order;use Illuminate\Http\Request;use Symfony\Component\HttpFoundation\StreamedResponse;
class ReportController extends Controller {
 private function query(Request $r){
  $d=$r->validate(['from'=>'nullable|date','to'=>'nullable|date|after_or_equal:from','payment'=>'nullable|in:cash,qris,bank_transfer,debit']);
  return Order::with('user','details')->where('status','paid')->when($d['from']??null,fn($q,$v)=>$q->whereDate('created_at','>=',$v))->when($d['to']??null,fn($q,$v)=>$q->whereDate('created_at','<=',$v))->when($d['payment']??null,fn($q,$v)=>$q->where('payment_method',$v));
 }
 public function index(Request $r){$q=$this->query($r);$orders=(clone $q)->latest()->paginate(20)->withQueryString();$sum=(clone $q)->selectRaw('COALESCE(SUM(subtotal),0) subtotal, COALESCE(SUM(discount_amount),0) discount, COALESCE(SUM(tax_amount),0) tax, COALESCE(SUM(grand_total),0) total, COUNT(*) cnt')->first();$avg=$sum->cnt?round($sum->total/$sum->cnt):0;return view('reports.index',compact('orders','sum','avg'));}
 public function csv(Request $r):StreamedResponse{$orders=$this->query($r)->latest()->get();return response()->streamDownload(function()use($orders){$f=fopen('php://output','w');fputcsv($f,['Invoice','Tanggal','Kasir','Tipe','Item Terjual','Subtotal','Diskon','Pajak','Grand Total','Pembayaran','Status']);foreach($orders as $o)fputcsv($f,[$o->invoice_number,$o->created_at,$o->user->name,$o->order_type,$o->details->sum('qty'),$o->subtotal,$o->discount_amount,$o->tax_amount,$o->grand_total,$o->payment_method,$o->status]);fclose($f);},'laporan-pos.csv',['Content-Type'=>'text/csv']);}
}
