<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table ='products';

    protected $fillable = [
        'name',
        'description',
        'original_price',
        'discount_price',
        'quantity',
        'category',
        'image',
    ];
 

    public function variants()
    {
        return $this->hasMany(Product_Variants::class);
    }
    public function randomVariant()
    {
        return $this->hasOne(Product_Variants::class)
                    ->where('stock', '>', 0) // or 'quantity', 'available', etc.
                    ->inRandomOrder();
    }
    
    // Accessor for final price
    public function FinalPrice(): float
    {
        return $this->discount_price ?? $this->original_price;
    }

    // Check if product is in stock
    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }
}
