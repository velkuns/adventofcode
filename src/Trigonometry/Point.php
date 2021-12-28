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
    private int $x;
    private int $y;
    private int $z;

    public function __construct(int $x, int $y, int $z = 0)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
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
        $x = $this->x;
        $y = $this->y;
        $z = $this->z;

        if ($axis === 'x') {
            $y = $this->y * cos($angle) + $this->z * -sin($angle);
            $z = $this->y * sin($angle) + $this->z * cos($angle);
        } elseif ($axis === 'y') {
            $x = $this->x * cos($angle) + $this->z * sin($angle);
            $z = $this->x * -sin($angle) + $this->z * cos($angle);
        } else {
            $x = $this->x * cos($angle) + $this->y * sin($angle);
            $y = $this->x * -sin($angle) + $this->y * cos($angle);
        }

        return new Point((int) $x, (int) $y, (int) $z);
    }

    public function mirrorOnAxis(string $axis): Point
    {
        $x = $axis === 'x' ? -$this->x : $this->x;
        $y = $axis === 'y' ? -$this->y : $this->y;
        $z = $axis === 'z' ? -$this->z : $this->z;

        return new Point($x, $y, $z);
    }

    public function translate(Vector $vector): Point
    {
        return new Point(
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
