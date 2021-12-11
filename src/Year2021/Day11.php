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
 * Class Day11
 *
 * @author Romain Cottard
 */
class Day11 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [1656 => [
                    '5483143223', '2745854711', '5264556173', '6141336146', '6357385478', '4167524645', '2176841721', '6882881134', '4846848554', '5283751526',
                ]],
            ],
            '**' => [
                [195 => [
                    '5483143223', '2745854711', '5264556173', '6141336146', '6357385478', '4167524645', '2176841721', '6882881134', '4846848554', '5283751526',
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
        $octopuses = array_map(fn($line) => array_map('intval', str_split($line)), $inputs);

        $totalFlashes = 0;

        for ($step = 1; $step <= 100; $step++) {
            for ($line = 0; $line < 10; $line++) {
                for ($col = 0; $col < 10; $col++) {
                    $octopuses = $this->energize($octopuses, $line, $col);
                }
            }

            for ($line = 0; $line < 10; $line++) {
                for ($col = 0; $col < 10; $col++) {
                    if ($octopuses[$line][$col] === 'F') {
                        $octopuses[$line][$col] = 0;
                        $totalFlashes++;
                    }
                }
            }

        }

        return $totalFlashes;
    }

    private function energize(array $octopuses, int $line, int $col): array
    {
        static $energizePosition = [[-1, -1], [-1, 0], [-1, 1], [0, -1], [0, 1], [1, -1], [1, 0], [1, 1]];
        if (!isset($octopuses[$line][$col]) || $octopuses[$line][$col] === 'F') {
            return $octopuses;
        }

        $octopuses[$line][$col]++;

        if ($octopuses[$line][$col] < 10) {
            return $octopuses;
        }

        //~ Octopus flash
        $octopuses[$line][$col] = 'F';

        foreach ($energizePosition as [$newLine, $newCol]) {
            $octopuses = $this->energize($octopuses, $line + $newLine, $col + $newCol);
        }

        return $octopuses;
    }

    private function starTwo(array $inputs): int
    {
        $octopuses = array_map(fn($line) => array_map('intval', str_split($line)), $inputs);

        for ($step = 1; $step <= 1000; $step++) {
            for ($line = 0; $line < 10; $line++) {
                for ($col = 0; $col < 10; $col++) {
                    $octopuses = $this->energize($octopuses, $line, $col);
                }
            }

            $totalFlashes = 0;
            for ($line = 0; $line < 10; $line++) {
                for ($col = 0; $col < 10; $col++) {
                    if ($octopuses[$line][$col] === 'F') {
                        $octopuses[$line][$col] = 0;
                        $totalFlashes++;
                    }
                }
            }

            if ($totalFlashes === 100) {
                return $step;
            }

        }

        return 0;
    }
}
