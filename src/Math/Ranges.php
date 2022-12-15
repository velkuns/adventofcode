<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Math;

class Ranges implements \Countable
{
    protected array $ranges = [];

    public function add(int|float $min, int|float $max): static
    {
        if (!isset($this->ranges[$min]) || $this->ranges[$min][1] < $max) {
            $this->ranges[$min] = [$min, $max];
        }

        return $this;
    }

    public function simplify(): static
    {
        ksort($this->ranges);

        [$minX, $maxX] = array_shift($this->ranges);

        $ranges = [];
        foreach ($this->ranges as [$min, $max]) {
            if ($min <= $maxX + 1) {
                //~ Overlap or continuity
                $maxX = max($max, $maxX);
            } elseif ($min > $maxX + 1) {
                //~ New range
                $ranges[$minX] = [$minX, $maxX];
                $minX = $min;
                $maxX = $max;
            }

            //~ If not one of previous if, range it is included, so skip
        }

        $ranges[$minX] = [$minX, $maxX];

        $this->ranges = $ranges;

        return $this;
    }

    public function reduce(callable $callback, int|float|null $initial = null): int|float
    {
        return array_reduce($this->ranges, $callback, $initial);
    }

    public function count(): int
    {
        return count($this->ranges);
    }
}
