<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderDetail extends Model {
    public $timestamps=false;
    protected $fillable=['order_id','product_id','product_name','unit_price','qty','line_subtotal','special_note'];
    protected function casts(): array { return ['unit_price'=>'integer','qty'=>'integer','line_subtotal'=>'integer']; }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
