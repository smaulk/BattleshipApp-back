<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\UpdateUserAvatarDto;

final class UpdateUserAvatarRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'avatar' => 'required|image',
        ];
    }

    public function toDto(): UpdateUserAvatarDto
    {
        return UpdateUserAvatarDto::fromRequest($this);
    }
}
