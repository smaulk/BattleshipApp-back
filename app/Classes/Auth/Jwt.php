<?php
declare(strict_types=1);

namespace App\Classes\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Carbon;

class Jwt
{
    private ?bool $authorized = null;
    private JwtHeader $header;
    private JwtPayload $payload;
    private JwtSignature $signature;

    public function __construct()
    {
        $this->header = new JwtHeader();
        $this->payload = new JwtPayload();
        $this->signature = new JwtSignature();
    }

    /**
     * Создание jwt токена из объекта Authenticatable
     */
    public function createToken(Authenticatable $user): string
    {
        $this->header->setDecodedHeader([
            'algo' => 'sha256',
            'type' => 'JWT'
        ]);
        $this->payload->setDecodedPayload([
            'id'       => $user->getAuthIdentifier(),
            'nickname' => $user->nickname,
            'exp'      => Carbon::now()->addMinutes($this->getTtl()),
        ]);

        $key = $this->getSecret();
        return $this->signature->create($this->header, $this->payload, $key);
    }

    private function getTtl(): int
    {
        return (int)config('auth.jwt.ttl');
    }

    private function getSecret(): string
    {
        return config('auth.jwt.secret');
    }

    /**
     * Устанавливает данный токен в класс
     */
    public function setToken(string $token): self
    {
        $token = $this->getTokenAsArray($token);
        if (!$token || count($token) !== 3) {
            $this->throwException();
        }

        $this->header->setHeader($token[0]);
        $this->payload->setPayload($token[1]);
        $this->signature->setSignature($token[2]);
        return $this;
    }

    private function getTokenAsArray(string $token): array
    {
        return explode('.', $token);
    }

    /**
     * Проверяет валидность токена
     */
    public function validate(): bool
    {
        if (!$this->header->validated() || !$this->payload->validated()) {
            return false;
        }
        $key = $this->getSecret();

        return $this->authorized = $this->signature->check($this->header, $this->payload, $key);
    }

    /**
     * Возвращает полезную нагрузку при успешной проверке валидности токена, иначе выбрасывает ошибку
     */
    public function getPayload(): array
    {
        if ($this->authorized === null && !$this->validate()) {
            $this->throwException();
        }
        return $this->payload->getDecodedPayload();
    }


    private function throwException(): void
    {
        throw new HttpResponseException(response()->json([
            'code' => 'FAILED_VALIDATION',
        ], 401));
    }
}
