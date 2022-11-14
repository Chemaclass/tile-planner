<?php

namespace TilePlanner\TilePlanner\Validator;

interface DeviationValidatorInterface
{
    public function isValidDeviation(
        float $currentLength,
        ?float $lastLength,
        float $tileMinLength,
        float $allowedDifference
    ): bool;
}
