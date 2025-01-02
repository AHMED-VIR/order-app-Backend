<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'store_id',
        'stock',
        'price',
        'description',
        'image'
    ];
    
    public function store(){
        return $this->belongsTo(Store::class);
    }

    public function carts(){
        return $this->hasMany(Cart::class);
    }

    public function wishLists(){
        return $this->hasMany(WishList::class);
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }
    
    
}
