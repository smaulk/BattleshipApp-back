<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\PaginateDto;
use App\Parents\Service;
use Illuminate\Support\Collection;

abstract class PaginateService extends Service
{
    /**
     * Указывается в виде LIMIT + 1,
     * т.к. необходимо выполнить проверку, на наличие оставшихся элементов в таблице
     */
    protected const LIMIT = 51;

    /**
     * Возвращает имя поля, которое будет использоваться для получения идентификатора для пагинации.
     */
    abstract protected function getPaginateId(): string;

    /**
     * Пагинация элементов коллекции
     */
    protected function paginate(Collection $items): PaginateDto
    {
        // Проверяем, что коллекция не пуста
        if ($items->isEmpty()) {
            return new PaginateDto($items, null);
        }

        // Проверка, что элементы в таблице еще остались
        $hasMore = $items->count() > self::LIMIT - 1;
        // Если есть больше данных, убираем последний элемент (он был для проверки)
        if ($hasMore) {
            $items->pop();
        }

        $lastId = $hasMore
            ? $items->last()?->{$this->getPaginateId()}
            : null;

        return new PaginateDto($items, $lastId);
    }
}