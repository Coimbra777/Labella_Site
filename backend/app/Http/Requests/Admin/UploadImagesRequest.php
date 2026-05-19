<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AdminApiFormRequest;

class UploadImagesRequest extends AdminApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'folder' => 'nullable|string|max:255',
        ];
    }
}
