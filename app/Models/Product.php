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
        'category_id',
        'price',
        'discount_percentage',
        'stock',
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
        'barcode',
        'qr_code'
    ];

    protected $appends = ['meta', 'thumbnail', 'dimensions', 'average_rating'];

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

    // Accessor to get the thumbnail image
    public function getThumbnailAttribute()
    {
        $thumbnail = $this->hasOne(ProductImage::class)->where('is_thumbnail', true)->first();
        return $thumbnail ? $thumbnail->url : null;
    }
    // Accessor to calculate the average rating
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;  // Default to 0 if no reviews
    }

    // Accessor for product dimensions
    public function getDimensionsAttribute()
    {
        return [
            'width' => $this->dimension_width,
            'height' => $this->dimension_height,
            'depth' => $this->dimension_depth,
        ];
    }
    // Accessor for custom meta
    public function getMetaAttribute()
    {
        return [
            'createdAt' => $this->created_at->diffForHumans(),
            'updatedAt' => $this->updated_at->diffForHumans(),
            'barcode' => $this->barcode,
            'qrCode' => $this->qrCode(),
        ];
    }
    public function qrCode()
    {
        return 'https://example.com/qrcode/' . $this->id;
    }
}
