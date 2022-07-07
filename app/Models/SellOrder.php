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
}
