<?php
namespace App\Http\Controllers;
use App\Models\Category;use Illuminate\Http\Request;use Illuminate\Support\Str;
class CategoryController extends Controller {
 public function index(){ $categories=Category::withCount('products')->latest()->paginate(12);return view('categories.index',compact('categories')); }
 public function create(){return view('categories.form',['category'=>new Category]);}
 public function store(Request $r){$d=$r->validate(['name'=>'required|string|max:100|unique:categories,name','status'=>'nullable|boolean']);Category::create(['name'=>$d['name'],'slug'=>Str::slug($d['name']).'-'.Str::lower(Str::random(4)),'status'=>$r->boolean('status',true)]);return redirect()->route('categories.index')->with('success','Kategori ditambahkan.');}
 public function edit(Category $category){return view('categories.form',compact('category'));}
 public function update(Request $r,Category $category){$d=$r->validate(['name'=>'required|string|max:100|unique:categories,name,'.$category->id,'status'=>'nullable|boolean']);$category->update(['name'=>$d['name'],'status'=>$r->boolean('status')]);return redirect()->route('categories.index')->with('success','Kategori diperbarui.');}
 public function destroy(Category $category){if($category->products()->exists())return back()->withErrors('Kategori masih digunakan produk.');$category->delete();return back()->with('success','Kategori dihapus.');}
}
