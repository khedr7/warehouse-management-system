<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'state_id'
    ];

    // one to many
    public function state()
    {
        return $this->belongsTo(State::class,'state_id');
    }

    // one to many
    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    // one to many
    public function distributionCenters()
    {
        return $this->hasMany(DistributionCenter::class);
    }
}
