<?php
declare(strict_types=1);

namespace App\Classes;

class VerificationManager
{
    protected string $key;

    public function __construct()
    {
        $this->key = config('app.key');
    }

    /**
     * Создает сигнатуру из данных
     */
    public function createSign(array $data): string
    {
        return hash_hmac('sha256', json_encode($data), $this->key);
    }

    /**
     * Проверяет совпадение переданной сигнатуры с данными
     */
    public function checkSign(array $data, string $signature): bool
    {
        return hash_equals($this->createSign($data), $signature);
    }

    /**
     * Возвращает хэшированную строку через sha1
     */
    public function hashString(string $string): string
    {
        return sha1($string);
    }

    /**
     * Вовзращает массив данных
     */
    public function createData(int $id, string $hash, int $exp): array
    {
        return [
            'id'   => $id,
            'hash' => $hash,
            'exp'  => $exp,
        ];
    }

    /**
     * Возвращает время истечения срока верификации в timestamp
     */
    public function getNewExp(): int
    {
        return Timestamp::now()
            ->addMinutes(
                config('auth.verification.expire')
            )->get();
    }
}