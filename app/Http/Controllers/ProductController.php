<?php
namespace App\Http\Controllers;
use App\Models\Product;use App\Models\Category;use Illuminate\Http\Request;
class ProductController extends Controller {
 public function index(){ $products=Product::with('category')->latest()->paginate(15);return view('products.index',compact('products')); }
 public function create(){return view('products.form',['product'=>new Product,'categories'=>Category::where('status',true)->get()]);}
 public function store(Request $r){$d=$r->validate($this->rules());$d['status']=$r->boolean('status',true);Product::create($d);return redirect()->route('products.index')->with('success','Produk ditambahkan.');}
 public function edit(Product $product){return view('products.form',compact('product')+['categories'=>Category::where('status',true)->get()]);}
 public function update(Request $r,Product $product){$d=$r->validate($this->rules($product));$d['status']=$r->boolean('status');$product->update($d);return redirect()->route('products.index')->with('success','Produk diperbarui.');}
 public function destroy(Product $product){$product->update(['status'=>false]);return back()->with('success','Produk dinonaktifkan. Riwayat transaksi dipertahankan.');}
 private function rules($p=null):array{return ['category_id'=>'required|exists:categories,id','sku'=>'required|string|max:50|unique:products,sku'.($p?','.$p->id:''),'name'=>'required|string|max:150','price'=>'required|integer|min:0','cost_price'=>'required|integer|min:0','stock'=>'required|integer|min:0','min_stock'=>'required|integer|min:0','unit'=>'required|string|max:20','status'=>'nullable|boolean'];}
}
