<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable  = [
        'name',
        'description',
        'product_category_id'
    ];

    // one to many
    public function category()
    {
        return $this->belongsTo(ProductCategory::class,'product_category_id');
    }

    // many to many
    public function stores()
    {
        return $this->belongsToMany(Store::class ,'store_product','product_id','store_id');
    }
}
