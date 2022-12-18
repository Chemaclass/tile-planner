<?php

namespace TilePlanner\TilePlanner\Validator;

interface OffsetValidatorInterface
{
    public function isValidOffset(
        float $currentLength,
        ?float $lastLength,
        float $tileMinLength,
        float $allowedDifference
    ): bool;
}
