<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Validator;

final class DeviationValidator implements DeviationValidatorInterface
{
    public function isValidDeviation(
        float $currentLength,
        ?float $lastLength,
        float $tileMinLength,
        float $allowedDifference
    ): bool {
        if (null === $lastLength && $currentLength >= $tileMinLength) {
            return true;
        }

        if (
            $currentLength >= $tileMinLength
            && ($currentLength <= $lastLength - $allowedDifference
            || $currentLength >= $lastLength + $allowedDifference)
        ) {
            return true;
        }

        return false;
    }
}
