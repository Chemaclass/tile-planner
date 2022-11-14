<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner;

use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use Gacela\Framework\AbstractFacade;

/**
 * @method TilePlannerFactory getFactory()
 */
final class TilePlannerFacade extends AbstractFacade implements TilePlannerFacadeInterface
{
    public function createPlan(TilePlanInput $tileInput): TilePlan
    {
        return $this->getFactory()
            ->createTilePlanCreator($tileInput->getLayingType())
            ->create($tileInput);
    }
}