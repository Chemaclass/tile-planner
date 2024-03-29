<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator;

use TilePlanner\TilePlanner\Models\LengthRange;
use TilePlanner\TilePlanner\Models\LengthRangeBag;
use TilePlanner\TilePlanner\Models\TilePlanInput;

final class TileLengthRangeCreator implements TileLengthRangeCreatorInterface
{
    public static ?LengthRangeBag $rangeBag = null;

    public function calculateRanges(TilePlanInput $tileInput): LengthRangeBag
    {
        if (isset(self::$rangeBag)) {
            return self::$rangeBag;
        }

        $rangeBag = new LengthRangeBag();

        $minTileWidth = $tileInput->getMinTileLength();
        $roomWidth = $tileInput->getRoomWidth();
        $tileLength = $tileInput->getTileLength();

        $tileLengthWhenLastTileHasMinLength = fmod($roomWidth - $minTileWidth, $tileLength);
        $fallbackMinLength = 0;

        if ($tileLengthWhenLastTileHasMinLength < $minTileWidth) {
            $fallbackMinLength = $tileLengthWhenLastTileHasMinLength + $minTileWidth;
            $rangeBag->addRange(
                LengthRange::withMinAndMax(
                    $fallbackMinLength,
                    $tileLength
                )
            );
        }

        if ($minTileWidth < $tileLengthWhenLastTileHasMinLength) {
            $rangeBag->addRange(
                LengthRange::withMinAndMax(
                    $minTileWidth,
                    $tileLengthWhenLastTileHasMinLength
                )
            );
        }

        $nextMin = ($tileLengthWhenLastTileHasMinLength + $minTileWidth) >= $tileLength
            ? $tileLength
            : $tileLengthWhenLastTileHasMinLength + $minTileWidth;

        if (
            $nextMin !== $fallbackMinLength
            && ($roomWidth % $tileLength > $minTileWidth
            || 0 === $roomWidth % $tileLength)
        ) {
            $rangeBag->addRange(LengthRange::withMinAndMax($nextMin, $tileLength));
        }

        self::$rangeBag = $rangeBag;

        return $rangeBag;
    }
}
