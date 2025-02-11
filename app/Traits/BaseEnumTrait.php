<?php
declare(strict_types=1);

namespace App\Traits;

trait BaseEnumTrait
{
    public static function names(): array
    {
        return array_map(fn($status) => $status->name, self::cases());
    }

    public static function fromName(string $name): self
    {
        return constant("self::$name");
    }
}