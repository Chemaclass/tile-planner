<?php

declare(strict_types=1);

namespace TilePlanner\Shared;

final class StringToFloatConverter
{
    public function toFloat(?string $string): ?float
    {
        if (null === $string) {
            return null;
        }

        return (float) str_replace(',', '.', $string);
    }
}
