<?php
declare(strict_types=1);

if (! function_exists('sort_nums')) {
    /**
     * @param $a number
     * @param $b number
     * @return array [min, max]
     */
    function sort_nums($a, $b): array
    {
        return $a < $b ? [$a, $b] : [$b, $a];
    }
}