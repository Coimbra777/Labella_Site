<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AdminTokenController extends Controller
{
    /**
     * Emite token Sanctum para administradores (sem criar sessão web).
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (! $user->is_admin) {
            return response()->json(['message' => 'Acesso restrito a administradores.'], 403);
        }

        $deviceName = $validated['device_name'] ?? 'admin-api';
        $plainTextToken = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token' => $plainTextToken,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
            ],
        ]);
    }

    /**
     * Revoga o token atual (somente quando a autenticação foi por Bearer).
     */
    public function destroy(Request $request): JsonResponse|Response
    {
        /** @var PersonalAccessToken|\Laravel\Sanctum\TransientToken|null $token */
        $token = $request->user()->currentAccessToken();

        if (! $token instanceof PersonalAccessToken) {
            return response()->json([
                'message' => 'Nenhum token de API ativo nesta requisição.',
            ], 400);
        }

        $token->delete();

        return response()->noContent();
    }
}
