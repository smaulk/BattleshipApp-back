<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @method Authenticatable|null user($guard = null)
 */
class Request extends FormRequest
{
    //
}
