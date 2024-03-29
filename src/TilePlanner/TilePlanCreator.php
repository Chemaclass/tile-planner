<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner;

use TilePlanner\TilePlanner\Creator\RowCreatorInterface;
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

final class TilePlanCreator
{
    private RowCreatorInterface $creator;
    private RestBag $rests;

    public function __construct(RowCreatorInterface $rowCreator, RestBag $rests)
    {
        $this->creator = $rowCreator;
        $this->rests = $rests;
    }

    public function create(TilePlanInput $tileInput): TilePlan
    {
        $plan = new TilePlan();
        $totalRows = $tileInput->getTotalRows();

        for ($i = 1; $i <= $totalRows; ++$i) {
            $row = $this->creator->createRow(
                $tileInput,
                $plan,
                $this->rests
            );

            $plan->addRow($row);
            $plan->setTotalTiles($this->retrieveHighestTileNumberFromRow($row));
        }

        $plan->setTotalArea($tileInput->getRoomWidthWithGaps() * $tileInput->getRoomDepthWithGaps());
        $plan->setTotalPrice($plan->getTotalAreaInSquareMeter() * $tileInput->getCostsPerSquare());
        $plan->setRoomWidth($tileInput->getRoomWidthWithGaps());
        $plan->setRoomDepth($tileInput->getRoomDepthWithGaps());
        $plan->setTrash($this->rests->getNonReusableRests());
        $plan->setRests(
            array_merge(
                $this->rests->getReusableRestsForSide(TilePlannerConstants::RESTS_LEFT),
                $this->rests->getReusableRestsForSide(TilePlannerConstants::RESTS_RIGHT)
            )
        );
        $plan->setTotalRest($this->rests->totalLengthOfAllRests());

        return $plan;
    }

    private function retrieveHighestTileNumberFromRow(Row $row): int
    {
        $numbers = array_map(fn (Tile $tile) => $tile->getNumber(), $row->getTiles());

        return max($numbers);
    }
}
