<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'total_amount',
        'shipping_address_id',
        'status'
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
/**
     * Get the items for the order.
     */
    public function items()
    {
        return $this->hasMany(Order_Items::class, 'order_id');
    }
    

}
