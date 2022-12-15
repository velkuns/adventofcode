<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Trigonometry;

class Vector
{
    public const TYPE_MANHATTAN = 0;
    public const TYPE_CIRCLE = 1;

    protected Point $origin;
    protected Point $destination;
    protected int $x;
    protected int $y;
    protected int $z;

    public function __construct(Point $p1, Point $p2, bool $sort = true)
    {
        if ($sort) {
            [$this->origin, $this->destination] = $this->sort($p1, $p2);
        } else {
            $this->origin      = $p1;
            $this->destination = $p2;
        }

        $this->x = $this->destination->getX() - $this->origin->getX();
        $this->y = $this->destination->getY() - $this->origin->getY();
        $this->z = $this->destination->getZ() - $this->origin->getZ();
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

    public function isSameAs(Vector $vector): bool
    {
        return $this->getX() === $vector->getX() && $this->getY() === $vector->getY() && $this->getZ() === $vector->getZ();
    }

    public function origin(): Point
    {
        return $this->origin;
    }

    public function destination(): Point
    {
        return $this->destination;
    }

    public function add(Vector $v): self
    {
        $p1 = $this->origin();
        $p2 = $this->destination()->translate($v);

        return new static($p1, $p2, false);
    }

    public function rotateOnAxis(string $axis, float $angle): Vector
    {
        $p1 = $this->origin->rotateOnAxis($axis, $angle);
        $p2 = $this->destination->rotateOnAxis($axis, $angle);

        return new static($p1, $p2);
    }

    public function squareSize(): int
    {
        return ($this->x * $this->x) + ($this->y * $this->y) + ($this->z * $this->z);
    }

    public function size(): float
    {
        return sqrt($this->squareSize());
    }

    public function manhattanDistance(): int
    {
        return abs($this->x) + abs($this->y) + abs($this->z);
    }

    public function __toString(): string
    {
        return $this->origin . ' -> ' . $this->destination . " : ($this->x $this->y $this->z) [" . $this->manhattanDistance() . "]";
    }

    protected function sort(Point $p1, Point $p2): array
    {
        if (
            $p1->getX() < $p2->getX() ||
            ($p1->getX() === $p2->getX() && $p1->getY() < $p2->getY()) ||
            ($p1->getX() === $p2->getX() && $p1->getY() === $p2->getY() && $p1->getZ() < $p2->getZ())
        ) {
            $origin      = $p1;
            $destination = $p2;
        } else {
            $origin      = $p2;
            $destination = $p1;
        }

        return [$origin, $destination];
    }
}
