<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Year2022;

use Application\Common\Day;
use Application\Trigonometry\Matrix;
use Application\Trigonometry\DirectionalVector;
use Application\Trigonometry\Point2D;
use Application\Trigonometry\Vector;

class Day12 extends Day
{
    /**
     * @return Vector[]
     */
    private function directions(): array
    {
        //~ Inverted char key because we process from the End
        static $direction = [
            'v' => new DirectionalVector(new Point2D(0, 0), new Point2D(0, -1)),
            '^' => new DirectionalVector(new Point2D(0, 0), new Point2D(0, 1)),
            '>' => new DirectionalVector(new Point2D(0, 0), new Point2D(-1, 0)),
            '<' => new DirectionalVector(new Point2D(0, 0), new Point2D(1, 0)),
        ];

        return $direction;
    }

    private function try(Matrix $map, Point2D $me, Vector $direction, int $step, array &$visited): void
    {
        $next  = $me->translate($direction);
        $value = $map->get($next);

        $alreadyVisitedWithSteps = $visited[$next->getCoordinates()] ?? null;
        if ($value === null || ($alreadyVisitedWithSteps !== null && $step >= $alreadyVisitedWithSteps)) {
            return;
        }

        $currentHeight = ord($map->get($me) === 'E' ? 'z' : $map->get($me));
        $nextHeight    = ord($value === 'S' ? 'a' : $value);

        if ($nextHeight < ($currentHeight - 1)) {
            return;
        }

        $visited[$next->getCoordinates()] = $step;

        foreach ($this->directions() as $direction) {
            $this->try($map, $next, $direction, $step + 1, $visited);
        }
    }

    protected function starOne(array $inputs): int
    {
        $map = (new Matrix(array_map(str_split(...), $inputs)))->transpose();
        $me  = $map->locate('E'); // Start from end to reuse try() for second part of puzzle

        $visited = [$me->getCoordinates() => 0];
        $step    = 0;

        foreach ($this->directions() as $direction) {
            $this->try($map, $me, $direction, $step + 1, $visited);
        }

        $destination = $map->locate('S');

        return $visited[$destination->getCoordinates()];
    }

    protected function starTwo(array $inputs): int
    {
        $map = (new Matrix(array_map(str_split(...), $inputs)))->transpose();
        $me  = $map->locate('E');

        $visited = [$me->getCoordinates() => 0];
        $step    = 0;

        foreach ($this->directions() as $direction) {
            $this->try($map, $me, $direction, $step + 1, $visited);
        }

        $destinations = [];
        foreach ($map->locateAll('a') as $destination) {
            if (isset($visited[$destination->getCoordinates()])) {
                $destinations[$destination->getCoordinates()] = $visited[$destination->getCoordinates()];
            }
        }

        return min($destinations);
    }
}
