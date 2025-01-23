<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product(): BelongsTo{
        return $this->belongsTo(Product::class);
    }

    public function brand(): BelongsTo{
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo{
        return $this->belongsTo(Category::class);
    }

}
