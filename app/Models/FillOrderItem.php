<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FillOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fill_order_id',
        'product_id',
        'quantity'
    ];
}
