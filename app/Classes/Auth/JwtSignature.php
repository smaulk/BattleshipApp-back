<?php
declare(strict_types=1);

namespace App\Classes\Auth;

class JwtSignature
{
    private string $signature;

    public function setSignature(string $signature): void
    {
        $this->signature = $signature;
    }

    public function check(JwtHeader $header, JwtPayload $payload, string $key): bool
    {
        $algo = $header->getAlgo();
        $header = $header->getHeader();
        $payload = $payload->getPayload();

        return hash_hmac($algo, "$header.$payload", $key) === $this->signature;
    }

    public function create(JwtHeader $header, JwtPayload $payload, string $key): string
    {
        $algo = $header->getAlgo();
        $header = $header->getHeader();
        $payload = $payload->getPayload();
        $signature = hash_hmac($algo, "$header.$payload", $key);

        return "$header.$payload.$signature";
    }
}
