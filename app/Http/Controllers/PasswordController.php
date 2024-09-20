<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Parents\Controller;
use App\Services\ForgotPasswordService;
use App\Services\ResetPasswordService;
use App\Services\UpdatePasswordService;
use Illuminate\Http\JsonResponse;

final class PasswordController extends Controller
{
    public function update(UpdateUserPasswordRequest $request): JsonResponse
    {
        (new UpdatePasswordService())->run(
            $request->toDto()
        );

        return $this->json([], 204);
    }

    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        (new ForgotPasswordService())->run(
            $request->validated('email')
        );

        return $this->json([], 204);
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        (new ResetPasswordService())->run(
            $request->toDto()
        );

        return $this->json([], 204);
    }
}
