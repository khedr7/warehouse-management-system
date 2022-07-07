<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sell_order_id',
        'product_id',
        'quantity'
    ];


    // one to many
    public function sellBillItems()
    {
        return $this->hasMany(SellBillItem::class);
    }


}
