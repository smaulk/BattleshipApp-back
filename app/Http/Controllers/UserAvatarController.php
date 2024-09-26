<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AuthorizedRequest;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\Parents\Controller;
use App\Services\DeleteUserAvatarService;
use App\Services\UpdateUserAvatarService;
use Illuminate\Http\JsonResponse;

final class UserAvatarController extends Controller
{
    public function update(UpdateUserAvatarRequest $request): JsonResponse
    {
        (new UpdateUserAvatarService())->run(
            $request->toDto()
        );

        return $this->json(status: 204);
    }

    public function delete(AuthorizedRequest $request): JsonResponse
    {
        (new DeleteUserAvatarService())->run(
            (int)$request->route('userId')
        );

        return $this->json(status: 204);
    }
}
