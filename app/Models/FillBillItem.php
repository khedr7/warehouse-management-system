<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FillBillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fill_bill_id',
        'fill_order_item_id',
        'price',
        'quantity'
    ];
}
