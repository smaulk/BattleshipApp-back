<?php

namespace App\Http\Requests;


use App\Dto\UpdateUserAvatarDto;

final class UpdateUserAvatarRequest extends Request
{
    public function authorize(): bool
    {
        return $this->user()?->getAuthIdentifier() === (int) $this->route('userId');
    }

    public function rules(): array
    {
        return [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,svg',
        ];
    }

    public function toDto(): UpdateUserAvatarDto
    {
        return UpdateUserAvatarDto::fromRequest($this);
    }
}
