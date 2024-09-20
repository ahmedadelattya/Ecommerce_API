<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'category_id', // Foreign key for category
        'price',
        'discount_percentage',
        'rating',
        'stock',
        'tags',
        'brand',
        'sku',
        'weight',
        'dimension_width',
        'dimension_height',
        'dimension_depth',
        'warranty_information',
        'shipping_information',
        'availability_status',
        'return_policy',
        'minimum_order_quantity',
        'meta',
        'images',
        'thumbnail'
    ];

    protected $casts = [
        'tags' => 'array',
        'meta' => 'array',
        'images' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
