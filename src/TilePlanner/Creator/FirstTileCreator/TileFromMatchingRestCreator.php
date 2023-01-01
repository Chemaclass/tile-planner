<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\TileValidatorInterface;

final class TileFromMatchingRestCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private TileValidatorInterface $tileValidator,
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, RestBag $restBag): ?Tile
    {
        $rests = $restBag->getReusableRestsForSide(TilePlannerConstants::RESTS_LEFT);

        if (empty($rests)) {
            return null;
        }

        $matchingRest = $this->findMatchingRest(
            $rests,
            $plan,
            $tileInput
        );

        if (null !== $matchingRest) {
            $restBag->removeRest($matchingRest->getLength(), TilePlannerConstants::RESTS_LEFT);

            return Tile::create(
                $tileInput->getTileWidth(),
                $matchingRest->getLength(),
                $matchingRest->getNumber()
            );
        }

        return null;
    }

    private function findMatchingRest(
        array $rests,
        TilePlan $plan,
        TilePlanInput $tileInput
    ): ?Rest {
        foreach ($rests as $rest) {
            $restLength = $rest->getLength();

            if ($restLength === $plan->getRowBeforeLastLength()) {
                continue;
            }

            if ($this->tileValidator->isValid($restLength, $tileInput, $plan)) {
                return $rest;
            }
        }

        return null;
    }
}
