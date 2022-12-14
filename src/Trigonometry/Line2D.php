<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Trigonometry;

class Line2D implements \Iterator
{
    private Point $origin;
    private Point $destination;

    private int $x;
    private int $y;

    private int $indexMax;
    private int $index;

    private float $xFactor;
    private float $yFactor;

    public function __construct(Point2D $p1, Point2D $p2)
    {
        [$this->origin, $this->destination] = $this->sort($p1, $p2);

        $this->x = $this->destination->getX() - $this->origin->getX();
        $this->y = $this->destination->getY() - $this->origin->getY();

        if ($this->x > $this->y) {
            $this->xFactor  = 1.0;
            $this->yFactor  = $this->y === 0 ? 0.0 : (float) ($this->y / $this->x); // Tan x (or 0)
            $this->indexMax = abs($this->x);
        } else {
            $this->xFactor  = $this->x === 0 ? 0.0 : (float) ($this->x / $this->y); // Tan y (or 0)
            $this->yFactor  = 1.0;
            $this->indexMax = abs($this->y);
        }

        $this->index = 0;
    }

    private function sort(Point $p1, Point $p2): array
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

    public function squareSize(): int
    {
        return ($this->x * $this->x) + ($this->y * $this->y);
    }

    public function size(): float
    {
        return sqrt($this->squareSize());
    }

    public function __toString(): string
    {
        return "$this->origin -> $this->destination : (x factor: $this->xFactor | y factor: $this->yFactor) [max: $this->indexMax]";
    }

    public function current(): Point
    {
        return new Point2D(
            (int) ceil($this->origin->getX() + ($this->index * $this->xFactor)),
            (int) ceil($this->origin->getY() + ($this->index * $this->yFactor))
        );
    }

    public function next(): void
    {
        $this->index += 1;
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return $this->index <= $this->indexMax;
    }

    public function rewind(): void
    {
        $this->index = 0;
    }
}
