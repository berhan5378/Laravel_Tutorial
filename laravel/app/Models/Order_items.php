<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order_items extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'price'
    ];

    /**
     * Get the order that owns the order item.
     */
    public function order()
    {
        return $this->belongsTo(Orders::class);
    }

    /**
     * Get the product that belongs to the order item.
     */
    public function variant()
    {
        return $this->belongsTo(Product_Variants::class, 'product_variant_id');
    }    
 
 
}
