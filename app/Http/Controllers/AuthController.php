<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshRequest;
use App\Services\LoginService;
use App\Services\RefreshService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    public function login(LoginRequest $request): JsonResponse
    {
        [$accessToken, $refreshToken] = (new LoginService())
            ->run($request->toDto());

        return $this->json([
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken,
        ]);
    }

    public function refresh(RefreshRequest $request): JsonResponse
    {
        [$accessToken, $refreshToken] = (new RefreshService())
            ->run($request->toDto());

        return $this->json([
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken,
        ]);
    }
}
