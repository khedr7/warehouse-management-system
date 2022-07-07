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

    // one to many
    public function fillBill()
    {
        return $this->belongsTo(FillBill::class,'fill_bill_id');
    }

    // one to many
    public function fillOrderItem()
    {
        return $this->belongsTo(FillOrderItem::class,'fill_order_item_id');
    }

    // one to many
    public function bookIns()
    {
        return $this->hasMany(BookIn::class);
    }
}
