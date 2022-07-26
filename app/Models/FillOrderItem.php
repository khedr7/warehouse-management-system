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

    // one to many
    public function fillBillItems()
    {
        return $this->hasMany(FillBillItem::class);
    }

    // one to many
    public function fillOrder()
    {
        return $this->belongsTo(FillOrder::class,'fill_order_id');
    }
}
