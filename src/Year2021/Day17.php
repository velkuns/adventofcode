<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Year2021;

use Application\Common\AlgorithmInterface;

/**
 * Class Day17
 *
 * @author Romain Cottard
 */
class Day17 implements AlgorithmInterface
{
    private const SAMPLES = [
        '23,-10',
        '25,-9',
        '27,-5',
        '29,-6',
        '22,-6',
        '21,-7',
        '9,0',
        '27,-7',
        '24,-5',
        '25,-7',
        '26,-6',
        '25,-5',
        '6,8',
        '11,-2',
        '20,-5',
        '29,-10',
        '6,3',
        '28,-7',
        '8,0',
        '30,-6',
        '29,-8',
        '20,-10',
        '6,7',
        '6,4',
        '6,1',
        '14,-4',
        '21,-6',
        '26,-10',
        '7,-1',
        '7,7',
        '8,-1',
        '21,-9',
        '6,2',
        '20,-7',
        '30,-10',
        '14,-3',
        '20,-8',
        '13,-2',
        '7,3',
        '28,-8',
        '29,-9',
        '15,-3',
        '22,-5',
        '26,-8',
        '25,-8',
        '25,-6',
        '15,-4',
        '9,-2',
        '15,-2',
        '12,-2',
        '28,-9',
        '12,-3',
        '24,-6',
        '23,-7',
        '25,-10',
        '7,8',
        '11,-3',
        '26,-7',
        '7,1',
        '23,-9',
        '6,0',
        '22,-10',
        '27,-6',
        '8,1',
        '22,-8',
        '13,-4',
        '7,6',
        '28,-6',
        '11,-4',
        '12,-4',
        '26,-9',
        '7,4',
        '24,-10',
        '23,-8',
        '30,-8',
        '7,0',
        '9,-1',
        '10,-1',
        '26,-5',
        '22,-9',
        '6,5',
        '7,5',
        '23,-6',
        '28,-10',
        '10,-2',
        '11,-1',
        '20,-9',
        '14,-2',
        '29,-7',
        '13,-3',
        '23,-5',
        '24,-8',
        '27,-9',
        '30,-7',
        '28,-5',
        '21,-10',
        '7,9',
        '6,6',
        '21,-5',
        '27,-10',
        '7,2',
        '30,-9',
        '21,-8',
        '22,-7',
        '24,-9',
        '20,-6',
        '6,9',
        '29,-5',
        '8,-2',
        '27,-8',
        '30,-5',
        '24,-7',
    ];

    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [45 => ['target area: x=20..30, y=-10..-5']],
            ],
            '**' => [
                [112 => ['target area: x=20..30, y=-10..-5']],
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs, bool $functionalMode = false): string
    {
        preg_match('`target area: x=([0-9-]+)\.\.([0-9-]+), y=([0-9-]+)\.\.([0-9-]+)`', $inputs[0], $matches);
        $x = [(int) $matches[1], (int) $matches[2]];
        $y = [(int) $matches[3], (int) $matches[4]];
        $area = [
            'x' => [min($x), max($x)],
            'y' => [$y[0] > 0 ? min($y) : max($y), $y[0] > 0 ? max($y) : min($y)]
        ];
        return (string) ($star === '*' ? $this->starOne($area) : $this->starTwo($area));
    }

    private function starOne(array $area): int
    {
        $vy = abs(0 - $area['y'][1]) - 1;

        return ($vy * ($vy + 1)) / 2;
    }

    private function starTwo(array $area): int
    {
        $vyMin = $area['y'][1];
        $vyMax = abs(0 - $area['y'][1]) - 1;

        $vxMin = $this->nthTriangleNumberRevert($area['x'][0]);
        $vxMax = $area['x'][1];

        //~ Precalculate all Y velocities possible for probe
        [$areaStartY, $areaEndY] = $area['y'];
        $listVY = [];
        for ($vy = $vyMax; $vy >= $vyMin; $vy--) {
            $highY = $this->nthTriangleNumber($vy);
            $vn    = $this->nthTriangleNumberRevert($highY - $areaStartY);
            $y     = $highY - $this->nthTriangleNumber($vn);

            $next = 0;
            while ($y >= $areaEndY) {
                if ($this->inAreaAxe($area, 'y', $y)) {
                    $n = $vn + $vy + 1 + $next;
                    $listVY["$vy.$n"] = $n;
                }
                $next++;
                $y -= ($vn + $next);
            }
        }

        $areaEndX   = $area['x'][1];
        $all = [];
        //~ For each X velocity probe between min & max X velocities
        for ($vx = $vxMin; $vx <= $vxMax; $vx++) {
            $n = 0;
            $x = 0;
            $v = $vx;

            //~ Increase probe position while probe not in area and velocity is not 0
            while ($x <= $areaEndX && $v > 0) {
                $x += $v;
                $n++;
                $v--;

                //~ Check if probe is in x range and have Y velocity valid equal to number of move on X axis
                if ($this->inAreaAxe($area, 'x', $x) && (in_array($n, $listVY) || $v === 0)) {
                    //~ Get keys (y velocities + number of move)
                    $keys = $v === 0 ? array_keys(array_filter($listVY, fn($value) => $value >= $n)) : array_keys($listVY, $n, true);
                    //~ For each Y velocity => save pair (X,Y)
                    foreach ($keys as $key) {
                        [$vy,] = explode('.', $key);
                        $all[] = "$vx,$vy";
                    }
                }
            }
        }

        return count(array_unique($all));
    }

    private function inAreaAxe(array $area, string $axe, int $value): bool
    {
        $value     = abs($value);
        $areaStart = abs($area[$axe][0]);
        $areaEnd   = abs($area[$axe][1]);

        return $value >= $areaStart && $value <= $areaEnd;
    }

    private function display(int $vx, int $vy, array $area): void
    {
        $grid = ['0.0' => '#'];
        $yMin = $yMax = $xMin = $xMax = 0;
        $x = $y = 0;

        echo "$vx,$vy\n";
        while ($vy >= $area['y'][1]) {
            $x += $vx;
            $y += $vy;

            $xMax = max($xMax, $x);
            $yMax = max($yMax, $y);
            $yMin = min($yMin, $y);

            $grid["$x.$y"] = '#';
            $vx += (0 <=> $vx);
            $vy -= 1;
        }
        //var_export($grid);

        echo "$yMin,$yMax|$xMin,$xMax\n";
        //*
        for ($y = $yMax; $y >= $yMin; $y--) {
            for($x = $xMin; $x <= $xMax; $x++) {
                $inArea = ($x >= $area['x'][0] && $x <= $area['x'][1]) && ($y >= $area['y'][1] && $y <= $area['y'][0]);
                echo $grid["$x.$y"] ?? ($inArea ? 'T' : '.');
            }

            echo "\n";
        }
        // */

        //var_export($area);
    }

    private function nthTriangleNumber(int $number): int
    {
        return ($number * ($number + 1)) / 2;
    }

    private function nthTriangleNumberRevert(int $sum): int
    {
        return (int) sqrt($sum * 2);
    }
}
