<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\LastTileCreator;

use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;

final class LastTileFromRestForChessTypeCreator implements LastTileCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests, float $usedRowLength): ?Tile
    {
        $restOfRow = $tileInput->getRoomWidth() - $usedRowLength;
        $foundRest = $this->findTileInRests($restOfRow, $rests);

        if ($foundRest !== null) {
            return Tile::create(
                $tileInput->getTileWidth(),
                $foundRest->getLength(),
                $foundRest->getNumber()
            );
        }

        return null;
    }

    private function findTileInRests(float $length, Rests $rests): ?Rest
    {
        if ($rests->hasRest(TilePlannerConstants::RESTS_LEFT)) {
            $possibleRests = [];
            foreach ($rests->getRests(TilePlannerConstants::RESTS_LEFT) as $rest) {
                if ($rest->getLength() === $length) {
                    $rests->removeRest($rest->getLength(), TilePlannerConstants::RESTS_LEFT);

                    return $rest;
                }

                if ($rest->getLength() > $length) {
                    $possibleRests[] = $rest;
                }
            }

            if (!empty($possibleRests)) {
                $smallestRest = $this->getRestWithSmallestLength($possibleRests);
                $rests->removeRest($smallestRest->getLength(), TilePlannerConstants::RESTS_LEFT);

                $trash = $smallestRest->getLength() - $length;
                $rests->addThrash($trash);

                return $smallestRest->setLength($length);
            }
        }

        return null;
    }

    /**
     * @param  list<Rest> $possibleRests
     * @return Rest
     */
    private function getRestWithSmallestLength(array $possibleRests): Rest
    {
        if (count($possibleRests) === 1) {
            return array_pop($possibleRests);
        }

        usort($possibleRests, static fn(Rest $a, Rest $b) => $a->getLength() <=> $b->getLength());

        return $possibleRests[0];
    }
}
