<?php
declare(strict_types=1);

namespace App\Http\Requests;


final class DeleteUserAvatarRequest extends Request
{
    function authorize(): bool
    {
        return $this->user()?->getAuthIdentifier() === (int)$this->route('userId');
    }
}
