<?php
namespace Database\Seeders;
use App\Models\User;use App\Models\Category;use App\Models\Product;
use Illuminate\Database\Seeder;use Illuminate\Support\Str;
class DatabaseSeeder extends Seeder {
 public function run():void {
  User::updateOrCreate(['email'=>'admin@titiktemu.test'],['name'=>'Administrator','password'=>'password','role'=>'admin','status'=>true]);
  User::updateOrCreate(['email'=>'kasir@titiktemu.test'],['name'=>'Kasir Demo','password'=>'password','role'=>'kasir','status'=>true]);
  $items=['Coffee'=>[['ESP001','Espresso',18000,12000,50],['LAT001','Cafe Latte',26000,17000,40],['AME001','Americano',22000,14000,40]],'Non-Coffee'=>[['MAT001','Matcha Latte',28000,18000,25],['CHO001','Chocolate',25000,16000,25]],'Food'=>[['RCE001','Chicken Rice',32000,22000,20]],'Pastry'=>[['CRS001','Croissant',19000,11000,15]]];
  foreach($items as $cat=>$products){$c=Category::firstOrCreate(['name'=>$cat],['slug'=>Str::slug($cat),'status'=>true]);foreach($products as [$sku,$name,$price,$cost,$stock])Product::updateOrCreate(['sku'=>$sku],['category_id'=>$c->id,'name'=>$name,'price'=>$price,'cost_price'=>$cost,'stock'=>$stock,'min_stock'=>5,'unit'=>'pcs','status'=>true]);}
 }
}
