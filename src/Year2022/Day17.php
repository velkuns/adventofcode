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
use Application\Tetris\Chamber202217;
use Application\Tetris\Shape;
use Application\Tetris\ShapeFactory;
use Application\Tetris\ShapeType;
class Day17 extends Day
{
    private function newShape(int $n, int $maxHeight): Shape
    {
        static $shapeTypes =  [
            ShapeType::HorizontalBar,
            ShapeType::Cross,
            ShapeType::Angle,
            ShapeType::VerticalBar,
            ShapeType::Square,
        ];

        $shapeType = $shapeTypes[($n - 1) % 5];

        return ShapeFactory::from($shapeType, 3, $maxHeight + 4);
    }

    protected function starOne(array $inputs): int
    {
        $deltas  = $this->computeDeltas($inputs[0], 2022);

        return array_sum(array_map('intval', str_split($deltas)));
    }

    protected function starTwo(array $inputs): int
    {
        $deltas  = $this->computeDeltas($inputs[0], 10_000);
        [$header, $period] = $this->findPeriodicity($deltas);

        $totalIteration = 1_000_000_000_000;

        $nbPeriods = (int) (($totalIteration - strlen($header)) / strlen($period));
        $modulo    = (int) (($totalIteration - strlen($header)) % strlen($period));

        $tail = $modulo > 0 ? substr($period, 0, $modulo) : '';

        $headerHeight = array_sum(array_map('intval', str_split($header)));
        $periodHeight = array_sum(array_map('intval', str_split($period)));
        $tailHeight   = array_sum(array_map('intval', str_split($tail)));

        return $headerHeight + ($periodHeight * $nbPeriods) + $tailHeight;
    }

    private function computeDeltas(string $jetPattern, int $iterations): string
    {
        $chamber   = new Chamber202217($jetPattern, 7, 0);

        //~ Try to find periodicity on first 10k items
        $deltas = '';
        $previousHeight = 0;
        //~ Start main loop
        for ($n = 1; $n <= $iterations; $n++) {
            //~ New Rock Shape
            $shape = $this->newShape($n, $chamber->getMaxHeight());

            //~ First jet
            $shape = $chamber->jet($shape);

            //~ Loop on falling + jet
            while (($shape = $chamber->fall($shape)) !== false) {
                $shape = $chamber->jet($shape);
            }

            $deltas .= ($chamber->getMaxHeight() - $previousHeight);
            $previousHeight = $chamber->getMaxHeight();
        }

        return $deltas;
    }

    private function findPeriodicity(string $deltas): array
    {
        $nbMax    = strlen($deltas); // Delta size
        $minItems = 5; // Min item in chunk. As we have 5 fives different shape, start chunk at 5

        //~ Iterate on each char
        for ($i = 0; $i < $nbMax; $i++) {
            //~ We can have a potential header before have a periodicity, so keep n first char aside
            $header = substr($deltas, 0, $i);

            //~ Then, search for a period of N elements (5 at least, 2k max here to accelerate process)
            for ($p = $minItems; $p < 2000; $p++) {
                //~ Split deltas (minus header) into chunks of N elements
                $periods = str_split(substr($deltas, $i), $p);

                //~ Skip last element that could have the wrong size (aka tail)
                array_pop($periods);

                //~ Keep the first element for comparison to other
                $first = array_shift($periods);

                //~ For each other elements in chunk, compare with the first. If at least one is different, not a period
                foreach ($periods as $period) {
                    if ($first !== $period) {
                        continue 2;
                    }
                }

                //~ All chunks are the same, so we found the period \o/. Then return header & and one period strings
                return [$header, $first];
            }
        }

        return ['0', '0'];
    }
}
