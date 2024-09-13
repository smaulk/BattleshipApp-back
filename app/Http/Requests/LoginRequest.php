<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\LoginDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }

    public function toDto(): LoginDto
    {
        return LoginDto::fromRequest($this);
    }
}
