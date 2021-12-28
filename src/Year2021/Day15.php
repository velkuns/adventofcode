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
                [315  => ['1163751742', '1381373672', '2136511328', '3694931569', '7463417111', '1319128137', '1359912421', '3125421639', '1293138521', '2311944581']],
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

    private function starOne(array $inputs): int
    {
        $map = array_map(fn($line) => array_map('intval', str_split($line)), $inputs);

        $pos  = [0, 0];
        $next = [$pos];

        $pathTo["0.0"] = 0;

        while (!empty($next)) {
            [$x, $y] = array_shift($next);
            foreach (self::DIRECTIONS as [$dx, $dy]) {
                $nx = $x + $dx;
                $ny = $y + $dy;
                if (!isset($map[$ny][$nx]) || (isset($pathTo["$nx.$ny"]) && $pathTo["$nx.$ny"] <= $map[$ny][$nx] + $pathTo["$x.$y"])) {
                    continue;
                }
                $pathTo["$nx.$ny"] = $map[$ny][$nx] + $pathTo["$x.$y"];
                $next[]            = [$nx, $ny];
            }
        }

        $mx = count($map[0]) - 1;
        $my = count($map) - 1;

        return $pathTo["$mx.$my"];
    }


    private function starTwo(array $inputs): int
    {
        $map = array_map(fn($line) => array_map('intval', str_split($line)), $inputs);

        $pos  = [0, 0];
        $next = [$pos];
        $size = count($map);

        $pathTo["0.0"] = 0;

        while (!empty($next)) {
            [$x, $y] = array_shift($next);
            foreach (self::DIRECTIONS as [$dx, $dy]) {
                $nx = $x + $dx;
                $ny = $y + $dy;
                if ($nx < 0 || $nx >= $size * 5 || $ny < 0 || $ny >= $size * 5) {
                    continue;
                }

                $v = $this->getValue($map, $size, $nx, $ny);
                if (isset($pathTo["$nx.$ny"]) && $pathTo["$nx.$ny"] <= $v + $pathTo["$x.$y"]) {
                    continue;
                }
                $pathTo["$nx.$ny"] = $v + $pathTo["$x.$y"];
                $next[]            = [$nx, $ny];
            }
        }

        $mx = ($size * 5) - 1;
        $my = ($size * 5) - 1;

        return $pathTo["$mx.$my"];
    }

    public function getValue(&$input, $size, $x, $y): int
    {
        if (isset($input[$y][$x])) {
            return $input[$y][$x];
        }

        $shiftX    = intdiv($x, $size);
        $shiftY    = intdiv($y, $size);
        $originalX = $x - $shiftX * $size;
        $originalY = $y - $shiftY * $size;
        $shiftedValue = $input[$originalY][$originalX] + $shiftX + $shiftY;
        $shiftedValue = $shiftedValue > 9 ? $shiftedValue % 9 : $shiftedValue;
        $input[$y][$x] = $shiftedValue; // save time if looked up again

        return $shiftedValue;
    }
}
