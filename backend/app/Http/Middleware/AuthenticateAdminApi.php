<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\RequestGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permite admin JSON em /api/admin via sessão (web) ou Bearer (Sanctum).
 */
class AuthenticateAdminApi
{
    public function handle(Request $request, Closure $next): Response
    {
        // Limpa cache do RequestGuard do Sanctum entre requisições no mesmo processo
        // (ex.: phpunit). Não chamar forgetUser no guard web — quebra actingAs().
        $sanctum = Auth::guard('sanctum');
        if ($sanctum instanceof RequestGuard) {
            $sanctum->forgetUser();
        }

        $user = null;

        if ($request->bearerToken()) {
            if (! Auth::guard('sanctum')->check()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            $user = Auth::guard('sanctum')->user();
        } elseif (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
        } else {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $request->setUserResolver(static fn () => $user);

        return $next($request);
    }
}
