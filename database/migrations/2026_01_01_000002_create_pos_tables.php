<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('categories', function(Blueprint $t){$t->id();$t->string('name')->unique();$t->string('slug')->unique();$t->boolean('status')->default(true);$t->timestamps();});
  Schema::create('products', function(Blueprint $t){$t->id();$t->foreignId('category_id')->constrained()->restrictOnDelete();$t->string('sku')->unique();$t->string('name');$t->unsignedInteger('price');$t->unsignedInteger('cost_price')->default(0);$t->unsignedInteger('stock')->default(0);$t->unsignedInteger('min_stock')->default(5);$t->string('unit')->default('pcs');$t->boolean('status')->default(true);$t->timestamps();});
  Schema::create('orders', function(Blueprint $t){$t->id();$t->string('invoice_number')->unique();$t->foreignId('user_id')->constrained()->restrictOnDelete();$t->string('order_type');$t->string('table_number')->nullable();$t->string('customer_name')->nullable();$t->unsignedBigInteger('subtotal');$t->unsignedBigInteger('tax_amount');$t->unsignedBigInteger('discount_amount')->default(0);$t->unsignedBigInteger('grand_total');$t->string('payment_method');$t->unsignedBigInteger('amount_paid');$t->unsignedBigInteger('change_amount')->default(0);$t->string('status')->default('paid');$t->text('notes')->nullable();$t->timestamps();$t->index(['created_at','status']);});
  Schema::create('order_details', function(Blueprint $t){$t->id();$t->foreignId('order_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->nullable()->constrained()->nullOnDelete();$t->string('product_name');$t->unsignedInteger('unit_price');$t->unsignedInteger('qty');$t->unsignedBigInteger('line_subtotal');$t->string('special_note')->nullable();});
  Schema::create('ingredients', function(Blueprint $t){$t->id();$t->string('name')->unique();$t->string('unit');$t->decimal('stock',12,3)->default(0);$t->decimal('min_stock',12,3)->default(0);$t->unsignedInteger('cost_per_unit')->default(0);$t->timestamps();});
  Schema::create('recipes', function(Blueprint $t){$t->id();$t->foreignId('product_id')->constrained()->cascadeOnDelete();$t->foreignId('ingredient_id')->constrained()->cascadeOnDelete();$t->decimal('quantity',12,3);$t->timestamps();$t->unique(['product_id','ingredient_id']);});
 }
 public function down(): void { Schema::dropIfExists('recipes');Schema::dropIfExists('ingredients');Schema::dropIfExists('order_details');Schema::dropIfExists('orders');Schema::dropIfExists('products');Schema::dropIfExists('categories');}
};
