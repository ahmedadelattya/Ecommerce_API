<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'rating' => $this->rating,
            'comment' => $this->comment,
            'date' => $this->human_readable_created_at,
            'reviewerName' => $this->user->name,
            'reviewerEmail' => $this->user->email,
            'product_reviewed'=>$this->product->title,
        ];
    }
}
