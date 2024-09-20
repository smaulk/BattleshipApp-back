<?php
declare(strict_types=1);

namespace App\Parents;

use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    public static function getNotFoundMessage(): string
    {
        return 'Данные не найдены';
    }
}