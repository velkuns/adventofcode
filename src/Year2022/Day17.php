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
        $shapeTypes = [
            ShapeType::HorizontalBar,
            ShapeType::Cross,
            ShapeType::Angle,
            ShapeType::VerticalBar,
            ShapeType::Square,
        ];

        $chamber = new Chamber202217($inputs[0], 7, 0);

        //~ Start main loop
        $progress = new Progress('Shapes', 1_000_000_000_000);
        $progress->setTypeDisplay(Progress::TYPE_PERCENT);

        $delta = [0 => 0];
        for ($n = 1; $n < 10_000; $n++) {
            //~ New Rock Shape
            $shape = ShapeFactory::from($shapeTypes[($n - 1) % 5], 3, $chamber->getMaxHeight() + 4);
            $shape = $chamber->jet($shape);
            while (($shape = $chamber->fall($shape)) !== false) {
                $shape = $chamber->jet($shape);
            }
            $delta[$n] = $chamber->getMaxHeight() - $delta[$n - 1];
        }

        $this->findPeriodicity($delta);

        return $chamber->getMaxHeight();
    }

    private function findPeriodicity(array $int): void
    {
        echo "Find Periodicity:\n";
        $periods = [];
        $progress = new Progress('Shapes', count($int));
        $progress->setTypeDisplay(Progress::TYPE_PERCENT);
        $nbMax = count($int);
        for ($i = 0; $i < $nbMax; $i++) {
            $progress->display((string) $i, $i);
            for ($n = $i + 1; $n < $i + 2000; $n++) {
                $periods["$i-$n"] = md5(array_reduce(array_slice($int, $i, $n - $i), fn($carry, $v) => $carry . $v, ''));
            }
        }

        foreach ($periods as $in => $period) {
            $test = array_intersect([$in => $period], $periods);
            if (count($test) > 1) {
                echo "$in\n";
            }
        }
    }
}
