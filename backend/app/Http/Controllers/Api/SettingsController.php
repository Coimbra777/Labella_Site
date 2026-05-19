<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingsResource;
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
        return response()->json(
            (new SettingsResource(SiteSetting::getSettings()))->resolve(),
        );
    }
}
