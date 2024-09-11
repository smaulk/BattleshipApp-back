<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $data = (new LoginService())->login($dto);
        return response()->json($data);
    }

//    public function refresh(Request $request): JsonResponse
//    {
//
//    }
}
