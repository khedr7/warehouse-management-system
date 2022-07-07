<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FillBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'description',
        'fill_order_id'
    ];

    // one to many
    public function fillOrder()
    {
        return $this->belongsTo(FillOrder::class,'fill_order_id');
    }

    // one to many
    public function fillBillItems()
    {
        return $this->hasMany(FillBillItem::class);
    }
}
