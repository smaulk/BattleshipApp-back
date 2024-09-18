<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserPasswordRequest;
use App\Services\UpdateUserPasswordService;
use Illuminate\Http\JsonResponse;

class UserPasswordController extends Controller
{
    public function update(UpdateUserPasswordRequest $request): JsonResponse
    {
        (new UpdateUserPasswordService())->run(
            $request->toDto()
        );

        return $this->json([], 204);
    }
}
