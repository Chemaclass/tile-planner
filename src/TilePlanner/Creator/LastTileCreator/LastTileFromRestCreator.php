<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\LastTileCreator;

use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;

final class LastTileFromRestCreator implements LastTileCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, RestBag $rests, float $usedRowLength): ?Tile
    {
        $restOfRow = $tileInput->getRoomWidth() - $usedRowLength;
        $foundRest = $this->findTileInRests($restOfRow, $rests);

        if (null !== $foundRest) {
            return Tile::create(
                $tileInput->getTileWidth(),
                $foundRest->getLength(),
                $foundRest->getNumber()
            );
        }

        return null;
    }

    private function findTileInRests(float $length, RestBag $restBag): ?Rest
    {
        $rests = $restBag->getReusableRestsForSide(TilePlannerConstants::RESTS_RIGHT);

        if (empty($rests)) {
            return null;
        }

        $possibleRests = [];
        foreach ($rests as $rest) {
            if (!$rest->isReusable()) {
                continue;
            }

            if ($rest->getLength() === $length) {
                $restBag->removeRest($rest->getLength(), TilePlannerConstants::RESTS_RIGHT);

                return $rest;
            }

            if ($rest->getLength() > $length) {
                $possibleRests[] = $rest;
            }
        }

        if (empty($possibleRests)) {
            return null;
        }

        $smallestRest = $this->getRestWithSmallestLength($possibleRests);
        $restBag->removeRest(
            $smallestRest->getLength(),
            TilePlannerConstants::RESTS_RIGHT
        );

        $trash = $smallestRest->getLength() - $length;
        $restBag->addNonReusableRest($trash);

        return $smallestRest->setLength($length);
    }

    /**
     * @param list<Rest> $possibleRests
     */
    private function getRestWithSmallestLength(array $possibleRests): Rest
    {
        if (1 === count($possibleRests)) {
            return array_pop($possibleRests);
        }

        usort($possibleRests, static fn (Rest $a, Rest $b) => $a->getLength() <=> $b->getLength());

        return $possibleRests[0];
    }
}
