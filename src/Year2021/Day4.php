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
 * Class Day4
 *
 * @author Romain Cottard
 */
class Day4 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [4512 => [
                    '7,4,9,5,11,17,23,2,0,14,21,24,10,16,13,6,15,25,12,22,18,20,8,19,3,26,1',
                    '',
                    '22 13 17 11  0',
                    ' 8  2 23  4 24',
                    '21  9 14 16  7',
                    ' 6 10  3 18  5',
                    ' 1 12 20 15 19',
                    '',
                    ' 3 15  0  2 22',
                    ' 9 18 13 17  5',
                    '19  8  7 25 23',
                    '20 11 10 24  4',
                    '14 21 16 12  6',
                    '',
                    '14 21 17 24  4',
                    '10 16 15  9 19',
                    '18  8 23 26 20',
                    '22 11 13  6  5',
                    ' 2  0 12  3  7',
                ]],
            ],
            '**' => [
                [1924 => [
                    '7,4,9,5,11,17,23,2,0,14,21,24,10,16,13,6,15,25,12,22,18,20,8,19,3,26,1',
                    '',
                    '22 13 17 11  0',
                    ' 8  2 23  4 24',
                    '21  9 14 16  7',
                    ' 6 10  3 18  5',
                    ' 1 12 20 15 19',
                    '',
                    ' 3 15  0  2 22',
                    ' 9 18 13 17  5',
                    '19  8  7 25 23',
                    '20 11 10 24  4',
                    '14 21 16 12  6',
                    '',
                    '14 21 17 24  4',
                    '10 16 15  9 19',
                    '18  8 23 26 20',
                    '22 11 13  6  5',
                    ' 2  0 12  3  7',
                ]],
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs, bool $functionalMode = false): string
    {
        if (!$functionalMode) {
            return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
        } else {
            return (string) ($star === '*' ? $this->starOneFunctional($inputs) : $this->starTwoFunctional($inputs));
        }
    }

    private function starOne(array $inputs): int
    {
        $numbers = array_map('intval', explode(',', array_shift($inputs)));
        $grids   = $this->parseGrids($inputs);

        foreach ($numbers as $number) {
            foreach ($grids as $grid) {
                if ($grid->check($number)) {
                    return $grid->solve($number);
                }
            }
        }

        return 0;
    }

    private function starTwo(array $inputs): int
    {
        $numbers = array_map('intval', explode(',', array_shift($inputs)));
        $grids   = $this->parseGrids($inputs);

        $winningGrids = array_fill(0, count($grids), 0);
        foreach ($numbers as $number) {
            $isLast = (array_sum($winningGrids) === count($grids) - 1);
            foreach ($grids as $gridNumber => $grid) {
                $isBingo = $grid->check($number);

                if ($isLast && $isBingo && $winningGrids[$gridNumber] === 0) {
                    return $grid->solve($number);
                }

                if ($isBingo) {
                    $winningGrids[$gridNumber] = 1;
                }
            }
        }

        return 0;
    }

    private function newGrid(array $lines): object
    {
        $grid = new class {
            private array $lines   = [];
            private array $columns = [];

            public function set(array $lines): self
            {
                $this->lines = $lines;

                return $this;
            }

            public function check(int $number): bool
            {
                foreach ($this->lines as &$line) {
                    if (isset($line[$number])) {
                        $line[$number] = 1;
                        $isBingo = (array_sum($line) === 5);
                        for ($i = 0, $max = count($line); $i < $max; $i++) {
                            $noIndexedLines = array_map(fn($currentLine) => array_values($currentLine), $this->lines);
                            $columns = array_column($noIndexedLines, $i);
                            $isBingo = $isBingo || (array_sum($columns) === 5);
                        }

                        return $isBingo;
                    }
                }

                return false;
            }

            public function display(?array $grid = null): void
            {
                $grid = $grid ?? $this->lines;
                foreach ($grid as $line) {
                    echo implode(' ', $line) . PHP_EOL;
                }
                foreach ($grid as $line) {
                    echo implode(' ', array_keys($line)) . PHP_EOL;
                }
            }

            public function solve(int $number): int
            {
                $sum = 0;
                foreach ($this->lines as $line) {
                    foreach ($line as $gridNumber => $check) {
                        if ($check === 0) {
                            $sum += $gridNumber;
                        }
                    }
                }

                return $sum * $number;
            }
        };

        return $grid->set($lines);
    }

    private function parseGrids(array $inputs): array
    {
        $grids = [];
        $lines = [];
        foreach ($inputs as $line) {
            if (empty($line)) {
                if (!empty($lines)) {
                    $grids[] = $this->newGrid($lines);
                }
                $lines = [];
                continue;
            }

            $line    = array_map('intval', array_filter(explode(' ', $line), fn($number) => $number !== ''));
            $lines[] = array_combine(array_values($line), array_fill(0, count($line), 0));
        }

        $grids[] = $this->newGrid($lines);

        return $grids;
    }


    private function starOneFunctional(array $inputs): int
    {
        return 0;
    }

    private function starTwoFunctional(array $inputs): int
    {
        return 0;
    }
}
