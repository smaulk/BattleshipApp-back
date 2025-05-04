<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\SendMoveDataDto;
use App\Parents\Request;

final class SendMoveDataRequest extends Request
{
    public function rules(): array
    {
        return [
            'col' => 'required|integer',
            'row' => 'required|integer',
        ];
    }

    public function toDto(): SendMoveDataDto
    {
        return SendMoveDataDto::fromRequest($this);
    }
}