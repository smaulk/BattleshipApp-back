<?php

namespace App\Classes\Auth;

class JwtHeader
{
    private ?string $header = null;
    private array $decoded_header = [];

    public function __construct(?string $header = null)
    {
        $this->setHeader($header);
    }

    public function setHeader(?string $header): void
    {
        if (!$header) {
            return;
        }

        $decoded_header = base64_decode($header);
        if (!$decoded_header) {
            return;
        }

        $decoded_header = json_decode($decoded_header, true);
        if (!$decoded_header) {
            return;
        }

        $this->header = $header;
        $this->decoded_header = $decoded_header;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function getAlgo(): string
    {
        return $this->decoded_header['algo'];
    }

    public function setDecodedHeader(array $decoded_header): void
    {
        $this->header = base64_encode(json_encode($decoded_header));
        $this->decoded_header = $decoded_header;
    }

    public function validated(): bool
    {
        return array_key_exists('algo', $this->decoded_header) &&
            in_array($this->decoded_header['algo'], hash_hmac_algos());
    }
}
