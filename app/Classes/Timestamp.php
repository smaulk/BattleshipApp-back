<?php
declare(strict_types=1);

namespace App\Classes;

class Timestamp
{

    private int $timestamp;

    public function __construct(int $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public static function now(): Timestamp
    {
        return new self(time());
    }

    public function addSeconds(int $seconds): Timestamp
    {
        $this->timestamp += $seconds;
        return $this;
    }

    public function addMinutes(int $minutes): Timestamp
    {
        $this->timestamp += ($minutes * 60);
        return $this;
    }

    public function addHours(int $hours): Timestamp
    {
        $this->timestamp += ($hours * 3600);
        return $this;
    }

    public function addDays(int $days): Timestamp
    {
        $this->timestamp += ($days * 86400);
        return $this;
    }

    public function get(): int
    {
        return $this->timestamp;
    }
}