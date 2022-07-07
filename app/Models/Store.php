<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Store extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable  = [
        'name',
        'capacity',
        'current_capacity',
        'status',
        'location_id',
        'store_category_id'
    ];

    // one to many
    public function category()
    {
        return $this->belongsTo(StoreCategory::class,'store_category_id');
    }

    // many to many
    public function stores()
    {
        return $this->belongsToMany(Store::class ,'store_product','store_id','product_id');
    }

    // many to many
    public function users()
    {
        return $this->belongsToMany(Store::class ,'store_user','store_id','user_id');
    }
}
