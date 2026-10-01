<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'status' => $this->status,
            'currency' => $this->currency,
            'issued_at' => $this->issued_at?->toDateString(),
            'due_at' => $this->due_at?->toDateString(),
            'client' => new ClientResource($this->whenLoaded('client')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'total' => $this->when($this->relationLoaded('items'), fn () => $this->total),
        ];
    }
}