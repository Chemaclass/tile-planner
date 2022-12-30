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

    private function findTileInRests(float $length, RestBag $rests): ?Rest
    {
        if ($rests->hasRest(TilePlannerConstants::RESTS_RIGHT)) {
            $possibleRests = [];
            foreach ($rests->getRests(TilePlannerConstants::RESTS_RIGHT) as $rest) {
                if ($rest->getLength() === $length) {
                    $rests->removeRest($rest->getLength(), TilePlannerConstants::RESTS_RIGHT);

                    return $rest;
                }

                if ($rest->getLength() > $length) {
                    $possibleRests[] = $rest;
                }
            }

            if (!empty($possibleRests)) {
                $smallestRest = $this->getRestWithSmallestLength($possibleRests);
                $rests->removeRest($smallestRest->getLength(), TilePlannerConstants::RESTS_RIGHT);

                $trash = $smallestRest->getLength() - $length;
                $rests->addThrash($trash);

                return $smallestRest->setLength($length);
            }
        }

        return null;
    }

    /**
     * @param list<Rest> $possibleRests
     */
    private function getRestWithSmallestLength(array $possibleRests): Rest
    {
        if (1 === \count($possibleRests)) {
            return array_pop($possibleRests);
        }

        usort($possibleRests, static fn (Rest $a, Rest $b) => $a->getLength() <=> $b->getLength());

        return $possibleRests[0];
    }
}
