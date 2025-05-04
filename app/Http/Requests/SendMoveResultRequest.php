<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\SendMoveResultDto;
use App\Enums\ShipPosition;
use App\Enums\ShotStatus;
use App\Parents\Request;
use Illuminate\Validation\Rules\Enum;

final class SendMoveResultRequest extends Request
{

    public function rules(): array
    {
        return [
            'status'        => ['required', new Enum(ShotStatus::class)],

            'ship'          => 'nullable|array',
            'ship.id'       => 'required_with:ship|integer',
            'ship.size'     => 'required_with:ship|integer',
            'ship.position' => ['required_with:ship', new Enum(ShipPosition::class)],

            'startCell'     => 'nullable|array',
            'startCell.col' => 'required_with:startCell|integer',
            'startCell.row' => 'required_with:startCell|integer',
        ];
    }

    public function toDto(): SendMoveResultDto
    {
        return SendMoveResultDto::fromRequest($this);
    }
}