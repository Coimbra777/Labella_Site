<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\AdminApiFormRequest;

class LoginRequest extends AdminApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'sometimes|string|max:255',
        ];
    }
}
