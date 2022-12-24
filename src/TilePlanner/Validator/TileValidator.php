<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Validator;

use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Validator\Models\ValidatorInterface;

final class TileValidator implements TileValidatorInterface
{
    /**
     * @param list<ValidatorInterface> $tileValidatorStack
     */
    public function __construct(private array $tileValidatorStack) {
    }

    public function isValid(float $tileLength, TilePlanInput $tileInput, TilePlan $plan): bool
    {
        foreach ($this->tileValidatorStack as $validator) {
            if (!$validator->isValid($tileLength, $tileInput, $plan)) {
                return false;
            }
        }

        return true;
    }
}
