<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\UpdateUserPasswordDto;
use App\Rules\Confirmed;
use Illuminate\Validation\Rules\Password;

final class UpdateUserPasswordRequest extends AuthorizedRequest
{

    public function rules(): array
    {
        return [
            'currentPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', new Confirmed,
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ];
    }

    public function toDto(): UpdateUserPasswordDto
    {
        return UpdateUserPasswordDto::fromRequest($this);
    }
}
