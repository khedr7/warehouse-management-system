<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'fill_bill_item_id',
        'date'
    ];
}
