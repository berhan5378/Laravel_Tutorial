<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $table ='Wishlists';

    protected $fillable = [
        'user_id',
        'product_variant_id'
    ];

    /**
     * Get the user that owns the wishlist item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that belongs to the wishlist item.
     */
    public function variant()
    {
        return $this->belongsTo(Product_Variants::class, 'product_variant_id');
    }
}