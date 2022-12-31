<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

final class Rest
{
    private function __construct(
        private $length,
        private ?int $number,
        private ?string $side,
        private bool $isReusable,
    ) {
    }

    public static function createReusable(float $length, int $number, string $side): self
    {
        return new self($length, $number, $side, true);
    }

    public static function createNonReusable(
        float $length,
        ?int $number = null,
        ?string $side = null
    ): self {
        return new self($length, $number, $side, false);
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function isReusable(): bool
    {
        return $this->isReusable;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function getSide(): ?string
    {
        return $this->side;
    }

    public function setLength(float $length): self
    {
        $this->length = $length;

        return $this;
    }
}
