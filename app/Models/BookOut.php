<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'sell_bill_item_id  ',
        'date',
        'quantity'
    ];

    // one to many
    public function store()
    {
        return $this->belongsTo(Store::class,'store_id');
    }

    // one to many
    public function sellBillItem()
    {
        return $this->belongsTo(SellBillItem::class,'sell_bill_item_id');
    }
}
