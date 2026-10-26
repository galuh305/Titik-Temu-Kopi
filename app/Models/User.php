<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable {
    use HasFactory, Notifiable;
    protected $fillable = ['name','email','password','role','status'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array { return ['password'=>'hashed','email_verified_at'=>'datetime']; }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
}
