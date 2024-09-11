<?php

namespace App\Classes\Auth;

class JwtPayload
{

    private ?string $payload = null;
    private array $decoded_payload = [];

    public function __construct(?string $payload = null)
    {
        $this->setPayload($payload);
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function setPayload(?string $payload): void
    {
        if (!$payload) {
            return;
        }

        $decoded_payload = base64_decode($payload);
        if (!$decoded_payload) {
            return;
        }

        $decoded_payload = json_decode($decoded_payload, true);
        if (!$decoded_payload) {
            return;
        }

        $this->payload = $payload;
        $this->decoded_payload = $decoded_payload;
    }

    public function getDecodedPayload(): array
    {
        return $this->decoded_payload;
    }

    public function setDecodedPayload(array $decoded_payload): void
    {
        $this->payload = base64_encode(json_encode($decoded_payload));
        $this->decoded_payload = $decoded_payload;
    }

    public function validated(): bool
    {
        if (!array_key_exists('exp', $this->decoded_payload)) {
            return false;
        }

        $exp = $this->decoded_payload['exp'];
        return $exp > time();
    }
}
