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

    // one to many
    public function sellBill()
    {
        return $this->belongsTo(SellBill::class,'sell_bill_id');
    }

    // one to many
    public function sellOrderItem()
    {
        return $this->belongsTo(SellOrderItem::class,'sell_order_item_id');
    }

    // one to many
    public function bookOuts()
    {
        return $this->hasMany(BookOut::class);
    }
}
