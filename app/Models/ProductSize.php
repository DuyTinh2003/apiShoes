<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;


    protected $fillable = ['size', 'quantity_sold', 'quantity_remaining'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
