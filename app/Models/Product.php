<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Product extends Model {
    protected $fillable=['category_id','sku','name','price','cost_price','stock','min_stock','unit','status'];
    protected function casts(): array { return ['price'=>'integer','cost_price'=>'integer','stock'=>'integer','min_stock'=>'integer']; }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
}
