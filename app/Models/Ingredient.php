<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Ingredient extends Model {
    protected $fillable=['name','unit','stock','min_stock','cost_per_unit'];
    protected function casts(): array { return ['stock'=>'decimal:3','min_stock'=>'decimal:3','cost_per_unit'=>'integer']; }
}
