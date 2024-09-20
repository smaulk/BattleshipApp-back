<?php
declare(strict_types=1);

namespace App\Parents;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @method Authenticatable|null user($guard = null)
 */
abstract class Request extends FormRequest
{
    //
}
