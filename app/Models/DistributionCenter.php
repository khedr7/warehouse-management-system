<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DistributionCenter extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'location_id',
        'user_id'
    ];

    // one to many
    public function location()
    {
        return $this->belongsTo(Location::class,'location_id');
    }

    // one to many
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    // one to many
    public function sellOrders()
    {
        return $this->hasMany(SellOrder::class);
    }
}
