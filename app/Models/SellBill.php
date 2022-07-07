<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'description',
        'sell_order_id'
    ];
}
