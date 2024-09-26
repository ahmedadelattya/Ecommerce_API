<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category->name,
            'price' => $this->price,
            'discountPercentage' => $this->discount_percentage,
            'rating' => $this->reviews->avg('rating') ?? 0,
            'stock' => $this->stock,
            'tags' => $this->tags->pluck('name'),  // Assuming you have a tags relationship
            'brand' => $this->brand,
            'sku' => $this->sku,
            'weight' => $this->weight,
            'dimensions' => [
                'width' => $this->dimension_width,
                'height' => $this->dimension_height,
                'depth' => $this->dimension_depth,
            ],
            'warrantyInformation' => $this->warranty_information,
            'shippingInformation' => $this->shipping_information,
            'availabilityStatus' => $this->availability_status,
            'reviews' => $this->reviews->map(function ($review) {
                return [
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'date' => $review->created_at,
                    'reviewerName' => $review->reviewer_name,
                    'reviewerEmail' => $review->reviewer_email,
                ];
            }),
            'returnPolicy' => $this->return_policy,
            'minimumOrderQuantity' => $this->minimum_order_quantity,
            'meta' => [
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
                'barcode' => $this->barcode,
                'qrCode' => $this->qrCode(),
            ],
            'images' => $this->images->where('is_thumbnail', false)->pluck('url'),
            'thumbnail' => $this->thumbnail,
        ];
    }
}
