<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'user_id' => $this->user_id,
            'items' => CartItemResource::collection($this->whenLoaded('items')), // Load cart items
            'total' => $this->total,
            'item_count' => $this->item_count,
            'created_at'=> $this->human_readable_created_at,
            'updated_at'=> $this->human_readable_updated_at,
        ];
    }
}
