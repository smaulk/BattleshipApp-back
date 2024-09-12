<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\RefreshDto;
use Illuminate\Foundation\Http\FormRequest;

class RefreshRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'refreshToken' => 'required|string',
        ];
    }

    public function toDto(): RefreshDto
    {
        return RefreshDto::fromRequest($this);
    }
}
