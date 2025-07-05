<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;

    protected $table = 'shipments';

    protected $fillable = [
        'user_id',
        'address',
        'city',
        'sub_city',
        'zip_code',
        'country',
        'status',
        'contact_name',
        'contact_phone',
    ];

    /**
     * Get the user that owns the shipment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
