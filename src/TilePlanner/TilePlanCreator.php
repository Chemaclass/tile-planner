<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner;

use TilePlanner\TilePlanner\Creator\RowCreatorInterface;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\Tile;

final class TilePlanCreator
{
    private RowCreatorInterface $creator;
    private Rests $rests;

    public function __construct(RowCreatorInterface $rowCreator, Rests $rests)
    {
        $this->creator = $rowCreator;
        $this->rests = $rests;
    }

    public function create(TilePlanInput $tileInput): TilePlan
    {
        // TODO validate input
        $plan = new TilePlan();
        $totalRows = $this->getTotalRows($tileInput);

        for ($i = 1; $i <= $totalRows; $i++) {
            $row = $this->creator->createRow(
                $tileInput,
                $plan,
                $this->rests
            );

            $plan->addRow($row);
            $plan->setTotalTiles($this->getHighestTileNumberFromRow($row));
        }

        $plan->setTotalArea($tileInput->getRoomWidthWithGaps() * $tileInput->getRoomDepthWithGaps());
        $plan->setTotalPrice($plan->getTotalAreaInSquareMeter() * $tileInput->getCostsPerSquare());
        $plan->setRoomWidth($tileInput->getRoomWidthWithGaps());
        $plan->setRoomDepth($tileInput->getRoomDepthWithGaps());
        $plan->setTrash($this->mergeTiles($this->rests->getTrash()));
        $plan->setRests(
            array_merge(
                $this->rests->getRests(TilePlannerConstants::RESTS_LEFT),
                $this->rests->getRests(TilePlannerConstants::RESTS_RIGHT)
            )
        );
        $plan->setTotalRest($this->rests->getSumOfAll());

        return $plan;
    }

    private function getTotalRows(TilePlanInput $input): int
    {
        return (int)floor(($input->getRoomDepth() / $input->getTileWidth()));
    }

    private function mergeTiles(array $tiles): array
    {
        $mergedTrash = [];

        foreach ($tiles as $trash) {
            if (!isset($mergedTrash[$trash])) {
                $mergedTrash[$trash] = 0;
            }

            $mergedTrash[$trash]++;
        }

        return $mergedTrash;
    }

    private function getHighestTileNumberFromRow(Row $row): int
    {
        $numbers = array_map(fn(Tile $tile) => $tile->getNumber(), $row->getTiles());

        return max($numbers);
    }
}
