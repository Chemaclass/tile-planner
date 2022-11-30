<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

final class TileCounter
{
    private static int $numberCounter = 0;

    public static function next(): int
    {
        self::$numberCounter++;

        return self::$numberCounter;
    }

    public function current(): int
    {
        return self::$numberCounter;
    }
}