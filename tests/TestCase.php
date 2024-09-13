<?php

namespace Tests;

use App\Classes\Auth\Jwt;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    protected Jwt $jwt;

    public function __construct(string $name)
    {
        $this->jwt = new Jwt();
        parent::__construct($name);
    }
}
