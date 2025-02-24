<?php
declare(strict_types=1);

namespace App\Dto;

class SendNotifyDto
{
    public int $senderId;
    public int $receiverId;
    public string $event;
    public ?string $message;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->senderId = (int)$data['senderId'];
        $dto->receiverId = (int)$data['receiverId'];
        $dto->event = $data['event'];
        $dto->message = $data['message'] ?? null;
        return $dto;
    }
}