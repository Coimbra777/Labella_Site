<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Pedido exposto na resposta pública de POST /api/v1/orders (sem dados internos de produto/admin).
 */
class OrderPublicResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'total' => (float) $this->total,
            'created_at' => $this->created_at?->toIso8601String(),
            'items' => OrderItemPublicResource::collection($this->whenLoaded('items')),
        ];
    }
}
