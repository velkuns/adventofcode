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
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [45 => ['target area: x=20..30, y=-10..-5']],
            ],
            '**' => [
                [45 => ['target area: x=20..30, y=-10..-5']],
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs, bool $functionalMode = false): string
    {
        preg_match('`target area: x=([0-9-]+)\.\.([0-9-]+), y=([0-9-]+)\.\.([0-9-]+)`', $inputs[0], $matches);
        $x = [$matches[1], $matches[2]];
        $y = [$matches[3], $matches[4]];
        $area = [
            'x' => [min($x), max($x)],
            'y' => [$y[0] > 0 ? min($y) : max($y), $y[0] > 0 ? max($y) : min($y)]
        ];
        return (string) ($star === '*' ? $this->starOne($area) : $this->starTwo($area));
    }

    private function starOne(array $area): int
    {
        $vx = 7;
        $vy = abs(0 - $area['y'][1]) - 1;
        //$this->display($vx, $vy, $area);

        return ($vy * ($vy + 1)) / 2;
    }

    private function starTwo(array $area): int
    {
        return 0;
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
}
