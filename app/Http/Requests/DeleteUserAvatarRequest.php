<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class DeleteUserAvatarRequest extends FormRequest
{
    function authorize(): bool
    {
        return $this->user()?->getAuthIdentifier() === (int)$this->route('userId');
    }
}
