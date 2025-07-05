<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $table ='carts';
    protected $fillable = [
        'user_id',
        'product_variant_id',
        'quantity'
    ];

    /**
     * Get the user that owns the cart item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the product that belongs to the cart item.
     */

     public function variant()
     {
         return $this->belongsTo(Product_Variants::class, 'product_variant_id');
     }
       
  
 
    
}
