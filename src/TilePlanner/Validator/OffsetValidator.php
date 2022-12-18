<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Validator;

final class OffsetValidator implements OffsetValidatorInterface
{
    public function isValidOffset(
        float $currentLength,
        ?float $lastLength,
        float $tileMinLength,
        float $minOffset
    ): bool {
        if (null === $lastLength && $currentLength >= $tileMinLength) {
            return true;
        }

        if (
            $currentLength >= $tileMinLength
            && ($currentLength <= $lastLength - $minOffset
            || $currentLength >= $lastLength + $minOffset)
        ) {
            return true;
        }

        return false;
    }
}
