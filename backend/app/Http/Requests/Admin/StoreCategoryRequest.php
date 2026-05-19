<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AdminApiFormRequest;

class StoreCategoryRequest extends AdminApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ];
    }
}
