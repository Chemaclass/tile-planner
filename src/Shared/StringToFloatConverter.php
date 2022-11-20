<?php

declare(strict_types=1);

namespace TilePlanner\Shared;

final class StringToFloatConverter
{
    public function toFloat(?string $string): ?float
    {
        if ($string === null) {
            return null;
        }

        return (float)str_replace(',', '.', $string);
    }
}