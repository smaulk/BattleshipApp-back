<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AuthorizedRequest;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\Http\Resources\UserResource;
use App\Parents\Controller;
use App\Services\DeleteUserAvatarService;
use App\Services\UpdateUserAvatarService;
use Illuminate\Http\JsonResponse;

final class UserAvatarController extends Controller
{
    public function update(UpdateUserAvatarRequest $request): JsonResponse
    {
        $user = (new UpdateUserAvatarService())->run(
            $request->toDto()
        );

        return $this
            ->resource($user, UserResource::class)
            ->response();
    }

    public function delete(AuthorizedRequest $request): JsonResponse
    {
        (new DeleteUserAvatarService())->run(
            $request->getUserId()
        );

        return $this->json(status: 204);
    }
}
