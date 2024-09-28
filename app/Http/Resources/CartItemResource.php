<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            'product' => new ProductResource($this->whenLoaded('product')), // Assuming you have a ProductResource
            'quantity' => $this->quantity,
            'price' => $this->price,
            'sub_total' => $this->getTotalAttribute(), // Calculate subtotal for the item
        ];
    }
}
