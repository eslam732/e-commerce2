<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartProducts extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'cart_id',
        'amount',
        
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->hasOne(Product::class);
    }
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
