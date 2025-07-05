<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Facades\Auth;

class search_histories extends Model
{
    use HasFactory;
    protected $table = 'search_histories';

    protected $fillable = [
        'user_id',
        'term',
        'brand',
        'type',
        'category_name',
    ];
}
