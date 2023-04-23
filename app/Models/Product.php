<?php

namespace App\Models;

// use App\Models\ProductGallery;

use App\Models\ProductSize;
use App\Models\ProductGallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'cate_id', 'image', 'price', 'type', 'description', 'sale'];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function galleries()
    {
        return $this->hasMany(ProductGallery::class);
    }
    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }
}
