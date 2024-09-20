<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\GetUsersRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Parents\Controller;
use App\Parents\Request;
use App\Services\CreateUserService;
use App\Services\FindUserService;
use App\Services\GetUsersService;
use App\Services\UpdateUserService;
use Illuminate\Http\JsonResponse;

final class UserController extends Controller
{

    public function get(GetUsersRequest $request): JsonResponse
    {
        $users = (new GetUsersService())->run(
            $request->toDto()
        );

        return $this
            ->collection($users, UserResource::class)
            ->response();
    }

    public function find(Request $request): JsonResponse
    {
        $user = (new FindUserService())->run(
            (int)$request->route('userId')
        );

        return $this
            ->resource($user, UserResource::class)
            ->response();
    }

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

    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = (new UpdateUserService())->run(
            $request->toDto()
        );

        return $this
            ->resource($user, UserResource::class)
            ->response();
    }
}
