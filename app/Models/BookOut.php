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
        'date'
    ];
}
