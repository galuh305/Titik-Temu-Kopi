<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Order extends Model {
    protected $fillable=['invoice_number','user_id','order_type','table_number','customer_name','subtotal','tax_amount','discount_amount','grand_total','payment_method','amount_paid','change_amount','status','notes'];
    protected function casts(): array { return ['subtotal'=>'integer','tax_amount'=>'integer','discount_amount'=>'integer','grand_total'=>'integer','amount_paid'=>'integer','change_amount'=>'integer']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function details(): HasMany { return $this->hasMany(OrderDetail::class); }
}
