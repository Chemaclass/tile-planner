<?php

declare(strict_types=1);

namespace TilePlanner;

use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerFactory;

final class TilePlanner
{
    public static function createPlan(TilePlanInput $planInput): TilePlan
    {
        $factory = new TilePlannerFactory();

        return $factory
            ->createTilePlanCreator($planInput->getLayingType())
            ->create($planInput)
        ;
    }
}
