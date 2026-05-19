<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AdminApiFormRequest;

class DeleteUploadImageRequest extends AdminApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'path' => 'required|string',
        ];
    }
}
