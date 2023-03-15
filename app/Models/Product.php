<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'price',
        'user_id',
        'image',
        'category_id'
        
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cart_products()
    {
        return $this->belongsToMany(CartProducts::class);
    }
    public function product_comments()
    {
        return $this->hasMany(ProductComment::class);
    }
   
}
