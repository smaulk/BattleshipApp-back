<?php
declare(strict_types=1);

namespace App\Classes\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;

class JwtGuard implements StatefulGuard
{
    use GuardHelpers;

    private Request $request;
    private ?Authenticatable $last_attempted;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    public function user(): ?Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        if (is_null($token = $this->request->bearerToken())) {
            return null;
        }

        return $this->user = $this->provider->retrieveById([
            (new Jwt())->setToken($token)->getPayload()
        ]);
    }

    public function validate(array $credentials = []): bool
    {
        $this->last_attempted = $user = $this->provider->retrieveByCredentials($credentials);
        return $this->hasValidCredentials($user, $credentials);
    }

    public function attempt(array $credentials = [], $remember = false): string|false
    {
        $this->last_attempted = $user = $this->provider->retrieveByCredentials($credentials);

        return $this->hasValidCredentials($user, $credentials)
            ? $this->login($user, $remember)
            : false;
    }

    public function once(array $credentials = []): bool
    {
        if ($this->validate($credentials)) {
            $this->setUser($this->last_attempted);
            return true;
        }

        return false;
    }

    public function login(Authenticatable $user, $remember = false): string
    {
        $this->setUser($user);
        return (new Jwt())->createToken($user);
    }

    public function loginUsingId($id, $remember = false): string|false
    {
        $user = $this->provider->retrieveById($id);
        return $user !== null
            ? $this->login($user, $remember)
            : false;
    }

    public function onceUsingId($id): bool
    {
        if (!is_null($user = $this->provider->retrieveById($id))) {
            $this->setUser($user);
            return true;
        }

        return false;
    }

    public function viaRemember(): bool
    {
        return false;
    }

    public function logout(): void
    {
        $this->user = null;
    }

    public function hasValidCredentials(?Authenticatable $user, array $credentials): bool
    {
        return $user !== null && $this->provider->validateCredentials($user, $credentials);
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }
}
