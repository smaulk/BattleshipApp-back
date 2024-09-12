<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\CreateUserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{

    public function create(CreateUserRequest $request): JsonResponse
    {
        $user = (new CreateUserService())->run(
            $request->toDto()
        );

        return $this
            ->resource($user, UserResource::class)
            ->response()
            ->setStatusCode(201);
    }
}
