<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner;

use Gacela\Framework\AbstractFacade;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

/**
 * @method TilePlannerFactory getFactory()
 */
final class TilePlannerFacade extends AbstractFacade implements TilePlannerFacadeInterface
{
    public function createPlan(TilePlanInput $tileInput): TilePlan
    {
        return $this->getFactory()
            ->createTilePlanCreator($tileInput->getLayingType())
            ->create($tileInput)
        ;
    }
}
