<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\Rests;
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

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        if (!$rests->hasRest(TilePlannerConstants::RESTS_LEFT)) {
            return null;
        }

        $matchingRest = $this->findMatchingRest(
            $rests,
            $plan,
            $tileInput
        );

        if ($matchingRest !== null) {
            $rests->removeRest($matchingRest->getLength(), TilePlannerConstants::RESTS_LEFT);

            return Tile::create(
                $tileInput->getTileWidth(),
                $matchingRest->getLength(),
                $matchingRest->getNumber()
            );
        }

        return null;
    }

    private function findMatchingRest(
        Rests $rests,
        TilePlan $plan,
        TilePlanInput $tileInput
    ): ?Rest {

        foreach ($rests->getRests(TilePlannerConstants::RESTS_LEFT) as $rest) {
            $restLength = $rest->getLength();

            if ($restLength === $plan->getRowBeforeLastLength()) {
                continue;
            }

            if ($this->tileValidator->isValid($restLength, $tileInput, $plan)) {
                dump($restLength . " is OK " . get_class($this) . PHP_EOL);

                return $rest;
            }
        }

        return null;
    }
}
