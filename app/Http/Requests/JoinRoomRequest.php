<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Parents\Request;

final class JoinRoomRequest extends Request
{
    public function rules(): array
    {
        return [
            'roomId' => 'required|string',
        ];
    }

}