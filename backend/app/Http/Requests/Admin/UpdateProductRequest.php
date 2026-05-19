<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AdminApiFormRequest;

class UpdateProductRequest extends AdminApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = $this->route('product');

        return [
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'sometimes|required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku,'.$id,
            'barcode' => 'nullable|string|max:255',
            'quantity' => 'sometimes|required|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ];
    }
}
