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
use Application\Pipeline\Pipeline;

/**
 * Class Day9
 *
 * @author Romain Cottard
 */
class Day9 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [15 => [
                    '2199943210',
                    '3987894921',
                    '9856789892',
                    '8767896789',
                    '9899965678',
                ]],
            ],
            '**' => [
                [1134 => [
                    '2199943210',
                    '3987894921',
                    '9856789892',
                    '8767896789',
                    '9899965678',
                ]],
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs, bool $functionalMode = false): string
    {
        return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    private function starOne(array $inputs): int
    {
        $heightmap = array_map(fn($line) => array_map('intval', str_split($line)), $inputs);

        $pointValue = function (array $heightmap, int $x, int $y)
        {
            $tuples       = [[-1, 0], [1, 0], [0, -1], [0, 1]];
            $isLowerPoint = true;
            $value        = $heightmap[$x][$y];
            foreach ($tuples as $tuple) {
                $newX = $x + $tuple[0];
                $newY = $y + $tuple[1];
                $isLowerPoint = $isLowerPoint && ((isset($heightmap[$newX][$newY]) && $heightmap[$newX][$newY] > $value) || !isset($heightmap[$newX][$newY]));
            }

            return $isLowerPoint ? $value + 1 : 0;
        };

        $points = [];

        for ($x = 0, $xMax = count($heightmap); $x < $xMax; $x++) {
            for ($y = 0, $yMax = count($heightmap[0]); $y < $yMax; $y++) {
                $points[] = $pointValue($heightmap, $x, $y);
            }
        }

        return array_sum($points);
    }

    public function inputsToHeightmap(array $heightmap, string $input): array
    {
        $y = (count($heightmap) / strlen($input));
        foreach (str_split($input) as $x => $value) {
            $heightmap["$x.$y"] = (int) $value;
        }
        return $heightmap;
    }

    private function starTwo(array $inputs): int
    {
        $heightmap = array_reduce($inputs, [$this, 'inputsToHeightmap'], []);

        return (new Pipeline())
            ->array($heightmap)
                ->walk(function (int $value, string $point) use (&$heightmap) { $this->flagBasins($heightmap, $point, 0);})
            ->array($heightmap) // Re set modified heightmap
                ->countValues()
                ->filter(fn($key) => $key > 9, ARRAY_FILTER_USE_KEY)
                ->rsort()
                ->slice(0, 3)
                ->product()
            ->get()
        ;
    }

    public function flagBasins(array &$heightmap, string $point, int $newValue): void
    {
        static $newPositions = [[-1, 0], [1, 0], [0, -1], [0, 1]];

        if (!isset($heightmap[$point]) || $heightmap[$point] >= 9) {
            return;
        }

        $newValue          = $newValue === 0 ? max($heightmap) + 1 : $newValue;
        $heightmap[$point] = $newValue;
        [$x, $y]           = explode('.', $point);

        foreach ($newPositions as $newPosition) {
            $newPoint = ($x + $newPosition[0]) . '.' . ($y + $newPosition[1]);
            $this->flagBasins($heightmap, $newPoint, $newValue); // Recurse from current new position
        }
    }

    private function starTwoBis(array $inputs): int
    {
        $heightmap = array_reduce($inputs, [$this, 'inputsToHeightmap'], []);

        $basinsSize = [];
        foreach ($heightmap as $point => $value) {
            $basinsSize[] = $this->getBasinSize($heightmap, $point);
        }

        rsort($basinsSize);

        return array_product(array_slice($basinsSize, 0, 3));
    }

    public function getBasinSize(array &$heightmap, string $point): int
    {
        static $newPositions = [[-1, 0], [1, 0], [0, -1], [0, 1]];

        [$x, $y] = explode('.', $point);
        $size    = 0;

        foreach ($newPositions as $newPosition) {
            $newPoint = ($x + $newPosition[0]) . '.' . ($y + $newPosition[1]);

            if (!isset($heightmap[$newPoint]) || $heightmap[$newPoint] >= 9) {
                continue;
            }

            $heightmap[$newPoint] = 10; // Override point

            $size++;

            //~ Recurse from current new position
            $size += $this->getBasinSize($heightmap, $newPoint);
        }

        return $size;
    }
}
