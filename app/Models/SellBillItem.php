<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellBillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sell_bill_id',
        'sell_order_item_id',
        'price',
        'quantity'
    ];
}
