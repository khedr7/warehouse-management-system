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

    // one to many
    public function sellOrder()
    {
        return $this->belongsTo(SellOrder::class,'sell_order_id');
    }

    // one to many
    public function sellBillItems()
    {
        return $this->hasMany(SellBillItem::class);
    }
}
