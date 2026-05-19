<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Configurações do site retornadas em GET /api/v1/settings (contrato do frontend).
 *
 * @property array<string, mixed> $resource
 */
class SettingsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $raw = $this->resource;

        $contact = $raw['contact'] ?? [];
        $social = $raw['social'] ?? [];
        $cities = $raw['cities'] ?? [];
        $paymentMethods = $raw['payment_methods'] ?? [];
        $paymentIcons = $raw['payment_icons'] ?? [];

        return [
            'contact' => [
                'instagram' => $contact['instagram'] ?? '@labella',
                'instagramUrl' => $contact['instagram_url'] ?? '',
                'email' => $contact['email'] ?? '',
                'phone' => $contact['phone'] ?? '',
                'whatsapp' => $contact['whatsapp'] ?? '',
                'address' => $contact['address'] ?? '',
            ],
            'social' => [
                'facebook' => $social['facebook'] ?? '',
                'instagram' => $social['instagram'] ?? '',
                'pinterest' => $social['pinterest'] ?? '',
                'whatsapp' => $social['whatsapp'] ?? '',
            ],
            'cities' => $cities,
            'paymentMethods' => $paymentMethods,
            'paymentIcons' => $paymentIcons,
        ];
    }
}
