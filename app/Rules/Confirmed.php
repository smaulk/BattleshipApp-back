<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class Confirmed implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $confirmField = $attribute . 'Confirmation';
        if(Arr::get($this->data, $confirmField) !== $value) {
            $fail('validation.confirmed')->translate();
        }
    }

    public function setData(array $data): Confirmed
    {
        $this->data = $data;
        return $this;
    }
}
