<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'total_price' => $this->total,
            'total_quantity' => $this->orderItems->sum('quantity'),
            'status' => $this->status,
            'items' => OrderItemResource::collection($this->orderItems),
        ];
    }
}
