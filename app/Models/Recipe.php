<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Recipe extends Model {
    protected $fillable=['product_id','ingredient_id','quantity'];
    protected function casts(): array { return ['quantity'=>'decimal:3']; }
}
