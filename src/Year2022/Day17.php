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
use Application\Tetris\ShapeFactory;
use Application\Tetris\ShapeType;
use Eureka\Component\Console\Progress\Progress;

class Day17 extends Day
{
    protected function starOne(array $inputs): int
    {
        $shapeTypes = [
            ShapeType::HorizontalBar,
            ShapeType::Cross,
            ShapeType::Angle,
            ShapeType::VerticalBar,
            ShapeType::Square,
        ];

        $chamber = new Chamber202217($inputs[0], 7, 0);

        //~ Start main loop
        for ($n = 1; $n < 2023; $n++) {
            //~ New Rock Shape
            $shape = ShapeFactory::from($shapeTypes[($n - 1) % 5], 3, $chamber->getMaxHeight() + 4);
            $shape = $chamber->jet($shape);
            while (($shape = $chamber->fall($shape)) !== false) {
                $shape = $chamber->jet($shape);
            }
        }

        return $chamber->getMaxHeight();
    }

    protected function starTwo(array $inputs): int
    {
        $deltas = $this->computeDeltas($inputs[0], 10_000);
        [$header, $period] = $this->findPeriodicity($deltas);

        //$totalIteration = 1_000_000_000_000;
        $totalIteration = 2023;

        echo "Seems we have period:\n";
        echo " - header: " . strlen($header) . "\n";
        echo " - period: " . strlen($period) . "\n";

        $nbPeriods = (int) (($totalIteration - strlen($header)) / strlen($period));
        $modulo    = (int) (($totalIteration - strlen($header)) % strlen($period));

        $tail = substr($deltas, -$modulo);

        echo "Tail: $tail\n";

        $headerHeight = array_sum(array_map('intval', str_split($header)));
        $periodHeight = array_sum(array_map('intval', str_split($period)));
        $tailHeight   = array_sum(array_map('intval', str_split($tail)));


        echo "header height: $headerHeight\n";
        echo "period height: $periodHeight\n";
        echo "tail height: $tailHeight\n";
        echo "nb periods: $nbPeriods\n";


        return $headerHeight + ($periodHeight * $nbPeriods) + $tailHeight;
    }

    private function computeDeltas(string $jetPattern, int $iterations): string
    {
        $shapeTypes = [
            ShapeType::HorizontalBar,
            ShapeType::Cross,
            ShapeType::Angle,
            ShapeType::VerticalBar,
            ShapeType::Square,
        ];

        $chamber   = new Chamber202217($jetPattern, 7, 0);

        //~ Try to find periodicity on first 10k items
        $deltas = '';
        $previousHeight = 0;
        for ($n = 1; $n < 10_000; $n++) {
            //~ New Rock Shape
            $shape = ShapeFactory::from($shapeTypes[($n - 1) % 5], 3, $chamber->getMaxHeight() + 4);
            $shape = $chamber->jet($shape);;
            while (($shape = $chamber->fall($shape)) !== false) {
                $shape = $chamber->jet($shape);
            }

            $deltas .= $chamber->getMaxHeight() - $previousHeight;
            $previousHeight = $chamber->getMaxHeight();
        }

        return $deltas;
    }

    private function findPeriodicity(string $deltas): array
    {
        echo "Find Periodicity:\n";
        $periods  = [];
        $nbMax    = strlen($deltas);
        $minItems = 5;

        $progress = new Progress('Shapes', $nbMax);
        $progress->setTypeDisplay(Progress::TYPE_PERCENT);

        for ($i = 0; $i < $nbMax; $i++) {
            $progress->display((string)$i, $i);
            $header = substr($deltas, 0, $i);

            for ($p = $minItems; $p < 2000; $p++) {
                $periods = str_split(substr($deltas, $i + 1), $p);
                array_pop($periods); // Skip last element that could have the wrong size
                $first = array_shift($periods);
                foreach ($periods as $period) {
                    if ($first !== $period) {
                        continue 2;
                    }
                }

                return [$header, $first];
            }
        }

        return [0, 0];
    }
}
