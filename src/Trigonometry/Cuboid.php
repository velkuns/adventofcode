<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Trigonometry;

class Cuboid
{
    private Point $origin;
    private Point $destination;
    private int $volume;

    public function __construct(Point $p1, Point $p2)
    {
        $this->origin      = $p1;
        $this->destination = $p2;

        $this->volume = ($p2->getX() - $p1->getX()) * ($p2->getY() - $p1->getY()) * ($p2->getZ() - $p1->getZ());
    }

    public function volume(): int
    {
        return $this->volume;
    }

    public function vertex(int $index = 0): Point
    {
        $y     = $index < 4 ? $this->origin->getY() : $this->destination->getY();
        $point = $this->origin ?? $this->destination;

        return new Point(
            $this->origin->getX() + $this->destination->getX() * sin($index * 90),
            $y,
            $point->getZ() * $index
        );
    }

    public function hasOverlap(Cuboid $cuboid): bool
    {
        return false;
    }

    public function origin(): Point
    {
        return $this->origin;
    }

    public function destination(): Point
    {
        return $this->destination;
    }

    public function getId(): int
    {
        return spl_object_id($this);
    }
}
