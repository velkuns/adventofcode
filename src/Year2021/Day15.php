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
 * Class Day15
 *
 * @author Romain Cottard
 */
class Day15 implements AlgorithmInterface
{
    private const MAX_FORWARD  = 5;
    private const DIRECTIONS = [[-1, 0], [0, -1], [1, 0], [0, 1]];
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [40  => ['1163751742', '1381373672', '2136511328', '3694931569', '7463417111', '1319128137', '1359912421', '3125421639', '1293138521', '2311944581']],
            ],
            '**' => [
                [40  => ['1163751742', '1381373672', '2136511328', '3694931569', '7463417111', '1319128137', '1359912421', '3125421639', '1293138521', '2311944581']],
            ],
        ];

        return $examples[$star];
    }

    // 706 => to high
    public function solve(string $star, array $inputs, bool $functionalMode = false): string
    {
        return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    public function lboulakras(array $inputs): int
    {
        $inputs = array_map(fn($line) => array_map('intval', str_split($line)), $inputs);
        $size = count($inputs);
        $queue = [[0, 0]];
        $distances = array_fill(0, $size, array_fill(0, $size, PHP_INT_MAX));
        $distances[0][0] = 0;

        while(count($queue)) {
            [$y, $x] = array_shift($queue);
            foreach ([[$y - 1, $x], [$y + 1, $x], [$y, $x - 1], [$y, $x + 1]] as [$ay, $ax]) {
                if (isset($inputs[$ay][$ax]) && $distances[$ay][$ax] > $distances[$y][$x] + $inputs[$ay][$ax]) {
                    $queue[] = [$ay, $ax];
                    $distances[$ay][$ax] = $distances[$y][$x] + $inputs[$ay][$ax];
                }
            }
        }
        return $distances[$size - 1][$size - 1];
    }
    public function lboulakras2(array $inputs): int
    {
        $inputs          = array_map(fn($line) => array_map('intval', str_split($line)), $inputs);
        $size            = count($inputs);
        $queue           = [[0, 0]];
        $distances       = array_fill(0, $size * 5, array_fill(0, $size * 5, PHP_INT_MAX));
        $distances[0][0] = 0;

        while (count($queue)) {
            [$y, $x] = array_shift($queue);
            foreach ([[$y - 1, $x], [$y + 1, $x], [$y, $x - 1], [$y, $x + 1]] as [$ay, $ax]) {
                if (!isset($distances[$ay][$ax])) {
                    continue;
                }
                $inputValue = $this->getValue($inputs, $size, $ax, $ay);
                if ($distances[$ay][$ax] > $distances[$y][$x] + $inputValue) {
                    $queue[]             = [$ay, $ax];
                    $distances[$ay][$ax] = $distances[$y][$x] + $inputValue;
                }
            }
        }
        return $distances[$size * 5 - 1][$size * 5 - 1];
    }

    public function getValue(&$input, $size, $x, $y): int
    {
        if (isset($input[$y][$x])) return $input[$y][$x];
        $shiftX = intdiv($x, $size);
        $shiftY = intdiv($y, $size);
        $originalX = $x - $shiftX * $size;
        $originalY = $y - $shiftY * $size;
        $shiftedValue = $input[$originalY][$originalX] + $shiftX + $shiftY;
        $shiftedValue = $shiftedValue > 9 ? $shiftedValue % 9 : $shiftedValue;
        $input[$y][$x] = $shiftedValue; // save time if looked up again

        return $shiftedValue;
    }

    private function starOne(array $inputs): int
    {
        $map = array_map(fn($line) => array_map('intval', str_split($line)), $inputs);

        $endX = count($map[0]) - 1;
        $endY = count($map) - 1;
        $x = $y = 0;

        $map[$y][$x] = 'x';

        $possibilities['0.0.0'] = $map;

        $count = 0;
        $doContinue = true;
        while ($doContinue) {
            $bestRisks = [];
            foreach ($possibilities as $coordinates => $map) {
                [$x, $y, $risk] = array_map('intval', explode('.', $coordinates));

                if ($doContinue && $x === $endX && $y === $endY) {
                    $doContinue = false;
                }
                unset($possibilities[$coordinates]);

                $paths = $this->explore($map, $x, $y, $risk);
                foreach ($paths as $newCoordinates => $newMap) {
                    [$x, $y, $risk] = array_map('intval', explode('.', $newCoordinates));
                    if (!isset($bestRisks["$x.$y"]) || $bestRisks["$x.$y"][0] > $risk) {
                        $bestRisks["$x.$y"] = [$risk, $map];
                    }
                    $possibilities[$newCoordinates] = $newMap;
                }
            }

            foreach ($bestRisks as $xy => [$risk, $map]) {
                unset($bestRisks[$xy]);
                $bestRisks["$xy.$risk"] = $risk;
            }

            asort($bestRisks);
            $bestRisks     = array_slice($bestRisks,0, min(20, count($bestRisks)), true);
            $possibilities = array_intersect_key($possibilities, $bestRisks);

            var_export($bestRisks);

            /*if (++$count === 2) {
                break;
            }*/
        }

        var_export($bestRisks);

        $bestRisk = null;
        $bestMap  = null;
        foreach ($possibilities as $coordinates => $map) {
            [,, $risk] = array_map('intval', explode('.', $coordinates));
            if ($bestRisk === null || $risk < $bestRisk) {
                $bestRisk = $risk;
                $bestMap  = $map;
            }
        }

        //$this->display($bestMap);

        return (int) $bestRisk;
    }

    private function explore(array $map, int $x, int $y, int $risk): array
    {
        $points = $this->getAllPointToDistance($map, $x, $y, self::MAX_FORWARD);
        $paths  = [];
        $maps   = [];
        foreach ($points as [$maxX, $maxY]) {
            //echo "TO REACH: $maxX.$maxY\n";
            $path = $this->exploreBestPathTo($map, $risk, $x, $y, $maxX, $maxY, self::MAX_FORWARD + 2, 99999, "$x.$y");

            if (empty($path)) {
                continue;
            }
            //echo " ==== $path[0].$path[1]: $path[2] ====\n";
            //$this->display($path[3]);
            $paths["$path[0].$path[1]"] = $path[2];
            $maps["$path[0].$path[1].$path[2]"] = $path[3];
        }

        asort($paths);

        $paths = array_slice($paths, 0, 5, true);

        $return = [];
        foreach ($paths as $coordinates => $risk) {
            $return["$coordinates.$risk"] = $maps["$coordinates.$risk"];
        }

        return $return;
    }

    private function exploreBestPathTo(array $map, int $risk, int $x, int $y, int $maxX, int $maxY, int $forward, $bestRisk = 99999, string $pathstring = '0.0'): ?array
    {
        $map[$y][$x] = 'x';
        if ($forward === 0 || ($x === $maxX && $y === $maxY) || $risk > $bestRisk) {
            if ( $x !== $maxX || $y !== $maxY) {
                //echo "$pathstring: 99999 [$bestRisk]\n";
                return [$x, $y, 99999, $map];
            } else {
                //echo "$pathstring: $risk [$bestRisk]\n";
                return [$x, $y, $risk, $map];
            }
            //return $x !== $maxX || $y !== $maxY ? [$x, $y, 99999, $map] : [$x, $y, $risk, $map];
        }

        $bestPath = null;
        foreach (self::DIRECTIONS as [$dirX, $dirY]) {
            $newX     = $x + $dirX;
            $newY     = $y + $dirY;
            $isTooFar = ($this->distance($newX, $newY, $maxX, $maxY) > $forward);
            if (!isset($map[$newY][$newX]) || $map[$newY][$newX] === 'x' || $isTooFar) {
                continue;
            }

            $path = $this->exploreBestPathTo($map, $risk + $map[$newY][$newX], $newX, $newY, $maxX, $maxY, $forward - 1, $bestPath[2] ?? 99999, $pathstring . " -> $newX.$newY");

            if (empty($path)) {
                continue;
            }

            if ($bestPath === null || $path[2] < $bestPath[2]) {
                $bestPath = $path;
            }
        }

        return $bestPath;
    }

    private function distance(int $x, int $y, int $maxX, int $maxY): int
    {
        return abs($maxX - $x) + abs($maxY - $y);
    }

    private function display(array $map): void
    {
        foreach ($map as $line) {
            foreach ($line as $x) {
                echo $x;
            }
            echo PHP_EOL;
        }

        echo PHP_EOL . PHP_EOL;
    }

    private function getAllPointToDistance(array $map, int $x, int $y, int $distance): array
    {
        $points = [];
        if (abs(count($map[0]) - 1 - $x) + abs(count($map) - 1 - $y) < $distance) {
            return [[count($map[0]) - 1, count($map) - 1]];
        }
        for ($posX = $x - $distance; $posX <= $x + $distance; $posX++) {
            for ($posY = $y - $distance; $posY <= $y + $distance; $posY++) {
                if (!isset($map[$posY][$posX]) || $map[$posY][$posX] === 'x') {
                    continue;
                }

                if (abs($posX - $x) + abs($posY - $y) === $distance) {
                    $points[] = [$posX, $posY];
                }
            }
        }

        return $points;
    }

    private function starTwo(array $inputs): int
    {
        return 0;
    }
}
