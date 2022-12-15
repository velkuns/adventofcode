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
use Application\Math\IntRanges;
use Application\Math\Ranges;
use Application\Trigonometry\Point;
use Application\Trigonometry\Point2D;
use Application\Trigonometry\Vector;
use Eureka\Component\Console\Progress\Progress;

class Day15 extends Day
{
    private const AXIS_X = 'x';
    private const AXIS_Y = 'y';

    /**
     * @param string[] $inputs
     * @return Vector[]
     */
    protected function parseInputs(array $inputs): array
    {
        $map = function (string $input) {
            $format = 'Sensor at x=%d, y=%d: closest beacon is at x=%d, y=%d';
            sscanf($input, $format, $sensorX, $sensorY, $beaconX, $beaconY);
            return new Vector(new Point2D($sensorX, $sensorY), new Point2D($beaconX, $beaconY), false);
        };
        return array_map($map, $inputs);
    }

    public function isVectorAreaTraversedByLine(Vector $vector, string $axisName, int $x, int $y): bool
    {
        if ($axisName === self::AXIS_Y) {
            $min = $vector->origin()->getY() - $vector->manhattanDistance();
            $max = $vector->origin()->getY() + $vector->manhattanDistance();

            return ($y >= $min && $y <= $max);
        }

        $min = $vector->origin()->getX() - $vector->manhattanDistance();
        $max = $vector->origin()->getX() + $vector->manhattanDistance();

        return ($x >= $min && $x <= $max);
    }

    /**
     * @param Vector[] $vectors
     * @param int $axisValue
     * @param string $axisName
     * @param array|null $beacons
     * @param array $bound
     * @return IntRanges
     */
    private function getRanges(array $vectors, int $axisValue, string $axisName, array|null &$beacons, array $bound = []): IntRanges
    {
        $ranges  = new IntRanges();

        foreach ($vectors as $vector) {
            //~ Check if vector area (with "radius" == manhattan distance from sensor to the closest beacon
            //~ is traversed by the line we want to check. If not, skip this vector
            $x = $axisName === self::AXIS_X ? $axisValue : 0;
            $y = $axisName === self::AXIS_Y ? $axisValue : 0;

            if (!$this->isVectorAreaTraversedByLine($vector, $axisName, $x, $y)) {
                continue;
            }

            //~ Keep the eligible beacons for future check - part 1 only
            if ($beacons !== null) {
                $beacons[(string) $vector->destination()] = $vector->destination();
            }

            //~ Then, we store range (min x, max x) of each vector that cover line
            $sensor = $vector->origin();
            if ($axisName === self::AXIS_Y) {
                $delta = $vector->manhattanDistance() - abs($sensor->getY() - $y);
                $ranges->add($sensor->getX() - $delta, $sensor->getX() + $delta);
            } else {
                $delta = $vector->manhattanDistance() - abs($sensor->getX() - $x);
                $ranges->add($sensor->getY() - $delta, $sensor->getY() + $delta);
            }
        }

        //~ Simplify all range (remove overlap & included ranges)
        $ranges->simplify();

        return $ranges;
    }

    /**
     * You guessed 5_564_013. That's not the right answer; your answer is too low.
     * @param array $inputs
     * @return float
     */
    protected function starOne(array $inputs): float
    {
        array_pop($inputs);               // x & y value for part 2
        $y = (int) array_pop($inputs); // y value for part 1

        $vectors = $this->parseInputs($inputs);
        $beacons = [];

        //~ For each vector, get all ranges (min x, max x) covered by sensors on the given line
        $ranges = $this->getRanges($vectors, $y, self::AXIS_Y, $beacons);

        //~ For each beacon covered by "eligible" vectors, we try to exclude it from range (if any)
        foreach ($beacons as $beacon) {
            if ($beacon->getY() === $y) {
                $ranges->exclude($beacon->getX());
            }
        }

        //~ Then reduce all ranges to unique value
        return (int) $ranges->reduce(fn (int $count, array $range): int => $count + ($range[1] - $range[0]) + 1, 0);
    }

    protected function starTwo(array $inputs): int
    {
        $maxX = $maxY = (int) array_pop($inputs); // x & y value for part 2
        array_pop($inputs);                       // y value for part 1

        $vectors = $this->parseInputs($inputs);
        $beacons = null;

        //~ For each vector, get all ranges (min y, max y) covered by sensors on the given line
        $columns = [];
        for ($x = 0; $x <= $maxX; $x++) {
            $ranges = $this->getRanges($vectors, $x, self::AXIS_X, $beacons);
            if (count($ranges) > 1) {
                $columns[$x] = $ranges;
            }
        }

        //~ For each vector, get all ranges (min y, max y) covered by sensors on the given line
        $lines = [];
        for ($y = 0; $y <= $maxY; $y++) {
            $ranges = $this->getRanges($vectors, $y, self::AXIS_Y, $beacons);
            if (count($ranges) > 1) {
                $lines[$y] = $ranges;
            }
        }

        return key($columns) * 4_000_000 + key($lines);
    }
}
