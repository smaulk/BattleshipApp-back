<?php

namespace App\Http\Requests;


use App\Parents\Request;

final class ForgotPasswordRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => 'required|email'
        ];
    }
}
