<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Trigonometry;

class Point
{
    public function __construct(protected int $x, protected int $y, protected int $z = 0)
    {
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getZ(): int
    {
        return $this->z;
    }

    public function getCoordinates(): string
    {
        return "$this->x,$this->y,$this->z";
    }

    public function rotateOnAxis(string $axis, float $angle): Point
    {
        $angleRad = deg2rad($angle);

        $x = $this->x;
        $y = $this->y;
        $z = $this->z;

        if ($axis === 'x') {
            $y = $this->y * round(cos($angleRad)) + $this->z * round(-sin($angleRad));
            $z = $this->y * round(sin($angleRad)) + $this->z * round(cos($angleRad));
        } elseif ($axis === 'y') {
            $x = $this->x * round(cos($angleRad)) + $this->z * round(sin($angleRad));
            $z = $this->x * round(-sin($angleRad)) + $this->z * round(cos($angleRad));
        } else {
            $x = $this->x * round(cos($angleRad)) + $this->y * round(sin($angleRad));
            $y = $this->x * round(-sin($angleRad)) + $this->y * round(cos($angleRad));
        }

        return new static((int) $x, (int) $y, (int) $z);
    }

    public function translate(Vector $vector): Point
    {
        return new static(
            $this->x + $vector->getX(),
            $this->y + $vector->gety(),
            $this->z + $vector->getZ()
        );
    }

    public function __toString(): string
    {
        return "($this->x,$this->y,$this->z)";
    }
}
