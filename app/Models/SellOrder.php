<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'description',
        'distribution_center_id'
    ];

    // one to many
    public function distributionCenter()
    {
        return $this->belongsTo(DistributionCenter::class,'distribution_center_id');
    }

    // one to many
    public function sellBills()
    {
        return $this->hasMany(SellBill::class);
    }

    // many to many
    public function products()
    {
        return $this->belongsToMany(Product::class ,'sell_order_items','sell_order_id','product_id');
    }
}
