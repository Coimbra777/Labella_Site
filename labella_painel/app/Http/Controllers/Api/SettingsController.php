<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Retorna as configurações do site para uso no frontend.
     * Formato compatível com LABELLA_SITE_CONFIG.
     */
    public function index(): JsonResponse
    {
        $raw = SiteSetting::getSettings();

        // Normaliza para o formato esperado pelo site (camelCase e estrutura)
        $contact = $raw['contact'] ?? [];
        $social = $raw['social'] ?? [];
        $paymentMethods = $raw['payment_methods'] ?? [];
        $paymentIcons = $raw['payment_icons'] ?? [];

        return response()->json([
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
            ],
            'paymentMethods' => $paymentMethods,
            'paymentIcons' => $paymentIcons,
        ]);
    }
}
