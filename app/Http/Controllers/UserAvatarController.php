<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DeleteUserAvatarRequest;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\Services\DeleteUserAvatarService;
use App\Services\UpdateUserAvatarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAvatarController extends Controller
{
    public function update(UpdateUserAvatarRequest $request): JsonResponse
    {
        (new UpdateUserAvatarService())->run(
            $request->toDto()
        );

        return $this->json([], 204);
    }

    public function delete(DeleteUserAvatarRequest $request): JsonResponse
    {
        (new DeleteUserAvatarService())->run(
            (int)$request->route('userId')
        );

        return $this->json([], 204);
    }
}
