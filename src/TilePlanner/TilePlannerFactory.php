<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner;

use Gacela\Framework\AbstractFactory;
use TilePlanner\Form\TilePlannerType;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\ChessTileCreator;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\FullTileCreator;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\MaximumTileCreator;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\MaximumPossibleTileIncludingOffsetCreator;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\MinimumTileCreator;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\TileFromMatchingRestCreator;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\TileFromSmallestRestCreator;
use TilePlanner\TilePlanner\Creator\FirstTileLengthCreator;
use TilePlanner\TilePlanner\Creator\Helper\SmallestRestFinder;
use TilePlanner\TilePlanner\Creator\Helper\SmallestRestFinderInterface;
use TilePlanner\TilePlanner\Creator\LastTileCreator\LastTileCreatorInterface;
use TilePlanner\TilePlanner\Creator\LastTileCreator\LastTileFittingCreator;
use TilePlanner\TilePlanner\Creator\LastTileCreator\LastTileFromRestCreator;
use TilePlanner\TilePlanner\Creator\LastTileCreator\LastTileFromRestForChessTypeCreator;
use TilePlanner\TilePlanner\Creator\LastTileLengthCreator;
use TilePlanner\TilePlanner\Creator\RowCreator;
use TilePlanner\TilePlanner\Creator\TileLengthRangeCreator;
use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Validator\OffsetValidator;
use TilePlanner\TilePlanner\Validator\RangeValidator;
use TilePlanner\TilePlanner\Validator\RangeValidatorInterface;

final class TilePlannerFactory extends AbstractFactory
{
    public function createTilePlanCreator(string $layingType): TilePlanCreator
    {
        return new TilePlanCreator(
            $this->createRowCreator($layingType),
            $this->createRest()
        );
    }

    private function createRowCreator(string $layingType): RowCreator
    {
        $firstTileCalculator = $this->createFirstTileLengthCalculator();
        $lastTileCalculator = $this->createLastTileLengthCalculator();

        if (TilePlannerType::TYPE_CHESS === $layingType) {
            $firstTileCalculator = $this->createFirstTileLengthCalculatorForChessType();
            $lastTileCalculator = $this->createLastTileLengthCalculatorForChessType();
        }

        return new RowCreator($firstTileCalculator, $lastTileCalculator);
    }

    private function createRest(): Rests
    {
        return new Rests();
    }

    private function createTileLengthRangeCalculator(): TileLengthRangeCreatorInterface
    {
        return new TileLengthRangeCreator();
    }

    private function createFirstTileLengthCalculator(): FirstTileLengthCreator
    {
        return new FirstTileLengthCreator(
            [
                $this->createTileFromMatchingRestCalculator(),
                $this->createTileFromSmallestRestCalculator(),
                $this->createMaximumPossibleTileIncludingOffsetCreator(),
                $this->createFullTileCalculator(),
                $this->createMaximumTileCreator(),
                $this->createMinimumTileCalculator(),
            ],
        );
    }

    private function createFirstTileLengthCalculatorForChessType(): FirstTileLengthCreator
    {
        return new FirstTileLengthCreator(
            [
                $this->createChessTileCalculator(),
            ]
        );
    }

    private function createTileFromMatchingRestCalculator(): FirstTileCreatorInterface
    {
        return new TileFromMatchingRestCreator(
            $this->createRangeValidator(),
            $this->createOffsetValidator(),
            $this->createTileLengthRangeCalculator(),
        );
    }

    private function createTileFromSmallestRestCalculator(): TileFromSmallestRestCreator
    {
        return new TileFromSmallestRestCreator(
            $this->createTileLengthRangeCalculator(),
            $this->createSmallestRestFinder(),
            $this->createRangeValidator(),
        );
    }

    private function createFullTileCalculator(): FirstTileCreatorInterface
    {
        return new FullTileCreator(
            $this->createRangeValidator(),
            $this->createOffsetValidator(),
            $this->createTileLengthRangeCalculator(),
        );
    }

    private function createMinimumTileCalculator(): FirstTileCreatorInterface
    {
        return new MinimumTileCreator(
            $this->createTileLengthRangeCalculator(),
            $this->createOffsetValidator()
        );
    }

    private function createMaximumTileCreator(): FirstTileCreatorInterface
    {
        return new MaximumTileCreator(
            $this->createOffsetValidator(),
            $this->createTileLengthRangeCalculator()
        );
    }

    private function createMaximumPossibleTileIncludingOffsetCreator(): FirstTileCreatorInterface
    {
        return new MaximumPossibleTileIncludingOffsetCreator(
            $this->createOffsetValidator(),
            $this->createTileLengthRangeCalculator()
        );
    }

    private function createRangeValidator(): RangeValidatorInterface
    {
        return new RangeValidator();
    }

    private function createOffsetValidator(): OffsetValidator
    {
        return new OffsetValidator();
    }

    private function createChessTileCalculator(): FirstTileCreatorInterface
    {
        return new ChessTileCreator(
            $this->createRangeValidator(),
            $this->createTileLengthRangeCalculator()
        );
    }

    private function createLastTileLengthCalculator(): LastTileLengthCreator
    {
        return new LastTileLengthCreator(
            [
                $this->createLastTileFromRestCreator(),
                $this->createLastTileFittingCreator(),
            ]
        );
    }

    private function createLastTileFromRestCreator(): LastTileCreatorInterface
    {
        return new LastTileFromRestCreator();
    }

    private function createLastTileFittingCreator(): LastTileCreatorInterface
    {
        return new LastTileFittingCreator();
    }

    private function createLastTileLengthCalculatorForChessType(): LastTileLengthCreator
    {
        return new LastTileLengthCreator(
            [
                $this->createLastTileFromRestForChessTypeCreator(),
                $this->createLastTileFittingCreator(),
            ]
        );
    }

    private function createLastTileFromRestForChessTypeCreator(): LastTileCreatorInterface
    {
        return new LastTileFromRestForChessTypeCreator();
    }

    private function createSmallestRestFinder(): SmallestRestFinderInterface
    {
        return new SmallestRestFinder(
            $this->createRest()
        );
    }
}
