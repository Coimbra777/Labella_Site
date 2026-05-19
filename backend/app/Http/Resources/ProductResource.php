<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Produto na API pública (vitrine). Mantém o mesmo shape de {@see Product::toArray()}
 * com URLs absolutas em `images` e `main_image`, compatível com o frontend React.
 */
class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Product $product */
        $product = $this->resource;

        $data = $product->toArray();
        $images = $product->images ?? [];

        if (is_array($images) && $images !== []) {
            $data['images'] = array_map(static function ($path) {
                if (empty($path) || str_starts_with((string) $path, 'http')) {
                    return $path;
                }

                return Storage::disk('public')->url($path);
            }, $images);
            $data['main_image'] = $data['images'][0] ?? null;
        } else {
            $data['main_image'] = null;
        }

        return $data;
    }
}
