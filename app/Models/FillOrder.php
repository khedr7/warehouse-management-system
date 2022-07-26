<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class FillOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'description',
        'fill_order_id'
    ];

    // one to many
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    // one to many
    public function fillBills()
    {
        return $this->hasMany(FillBill::class);
    }

    // one to many
    public function fillOrderItems()
    {
        return $this->hasMany(FillOrderItem::class);
    }

    // many to many
    public function products()
    {
        return $this->belongsToMany(Product::class ,'fill_order_items','fill_order_id','product_id');
    }
}
