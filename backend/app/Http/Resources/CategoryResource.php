<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Categoria na API pública. Em `show`, inclui `active_products` com o mesmo formato
 * de {@see ProductResource} para alinhar com a vitrine.
 */
class CategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $category = $this->resource;

        $data = $category->toArray();

        if ($category->relationLoaded('activeProducts')) {
            $data['active_products'] = $category->activeProducts
                ->map(static fn ($product) => (new ProductResource($product))->resolve())
                ->values()
                ->all();
        }

        return $data;
    }
}
