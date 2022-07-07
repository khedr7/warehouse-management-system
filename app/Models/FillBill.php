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
}
