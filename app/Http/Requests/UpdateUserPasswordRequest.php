<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\UpdateUserPasswordDto;
use Illuminate\Validation\Rules\Password;

final class UpdateUserPasswordRequest extends Request
{
    public function authorize(): bool
    {
        return $this->user()?->getAuthIdentifier() === (int) $this->route('userId');
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'confirmed',
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
