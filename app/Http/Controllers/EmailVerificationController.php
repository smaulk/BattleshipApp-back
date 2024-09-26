<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorizedRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Parents\Controller;
use App\Services\SendEmailVerificationService;
use App\Services\VerifyEmailService;
use Illuminate\Http\JsonResponse;


final class EmailVerificationController extends Controller
{
    public function verify(VerifyEmailRequest $request): JsonResponse
    {
        (new VerifyEmailService())->run(
            $request->toDto()
        );

        return $this->json(status: 204);
    }

    public function resend(AuthorizedRequest $request): JsonResponse
    {
        (new SendEmailVerificationService())->run(
            (int)$request->route('userId')
        );

        return $this->json(status: 204);
    }
}
