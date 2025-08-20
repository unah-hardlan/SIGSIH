<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->authService->attempt($data['usuario'], $data['contrasena']);
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['code']);
        }
        $response = response()->json($result, 200);
        $response->cookie('auth_token', $result['token'], 60, '/', null, false, true, false, 'Lax');
        return $response;
    }

    public function logout(): JsonResponse
    {
        return response()->json(['ok' => true])->cookie('auth_token', null, -1, '/', null, false, true, false, 'Lax');
    }

    // register opcional según necesidades futuras
    public function register() {}
}
