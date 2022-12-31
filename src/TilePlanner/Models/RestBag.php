<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

final class RestBag
{
    /**
     * @var array<Rest>
     */
    private static array $rests;

    public static function setRest(array $rests): void
    {
        self::$rests = $rests;
    }

    /**
     * @return list<Rest>
     */
    public function getReusableRestsForSide(string $side): array
    {
        if (empty(self::$rests)) {
            return [];
        }

        return array_filter(self::$rests, static fn(Rest $rest) =>
            $rest->getSide() === $side
            && $rest->isReusable()
        );
    }

    public function addRest(float $length, float $tileMinLength, string $side, int $number): void
    {
        if ($length >= $tileMinLength) {
            self::$rests[] = Rest::createReusable($length, $number, $side);
        } else {
            self::$rests[] = Rest::createNonReusable($length, $number, $side);
        }
    }

    public function addNonReusableRest(float $length): void
    {
        self::$rests[] = Rest::createNonReusable($length);
    }

    public function removeRest(float $length, string $side): self
    {
        foreach (self::$rests as $key => $rest) {
            if ($rest->getLength() === $length && $rest->getSide() === $side) {
                unset(self::$rests[$key]);

                break;
            }
        }

        return $this;
    }

    public function totalLengthOfAllRests(): float
    {
        if (empty(self::$rests)) {
            return 0;
        }

        return array_sum(array_map(static function (Rest $rest) {
            return $rest->getLength();
        }, self::$rests));
    }
}
