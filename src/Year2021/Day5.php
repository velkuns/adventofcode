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
 * Class Day5
 *
 * @author Romain Cottard
 */
class Day5 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [5 => [
                    '0,9 -> 5,9',
                    '8,0 -> 0,8',
                    '9,4 -> 3,4',
                    '2,2 -> 2,1',
                    '7,0 -> 7,4',
                    '6,4 -> 2,0',
                    '0,9 -> 2,9',
                    '3,4 -> 1,4',
                    '0,0 -> 8,8',
                    '5,5 -> 8,2',
                ]],
            ],
            '**' => [
                [12 => [
                    '0,9 -> 5,9',
                    '8,0 -> 0,8',
                    '9,4 -> 3,4',
                    '2,2 -> 2,1',
                    '7,0 -> 7,4',
                    '6,4 -> 2,0',
                    '0,9 -> 2,9',
                    '3,4 -> 1,4',
                    '0,0 -> 8,8',
                    '5,5 -> 8,2',
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
        $inputs  = array_map(fn($line) => explode(' -> ', $line), $inputs);
        $inputs  = array_map(fn($line) => ['o' => explode(',', $line[0]), 'e' => explode(',', $line[1])], $inputs);
        $vectors = array_filter($inputs, fn($vector) => $vector['o'][0] === $vector['e'][0] || $vector['o'][1] === $vector['e'][1]);

        $map = [];
        array_walk($vectors, function(array $vector) use (&$map) {
            $startX = min($vector['o'][0], $vector['e'][0]);
            $endX   = max($vector['o'][0], $vector['e'][0]);
            $startY = min($vector['o'][1], $vector['e'][1]);
            $endY   = max($vector['o'][1], $vector['e'][1]);
            for($x = $startX; $x <= $endX; $x++) {
                for($y = $startY; $y <= $endY; $y++) {
                    $map["$x.$y"] = ($map["$x.$y"] ?? 0) + 1;
                }
            }
        });

        return count(array_filter($map, fn($val) => $val > 1));
    }

    private function starTwo(array $inputs): int
    {
        $inputs  = array_map(fn($line) => explode(' -> ', $line), $inputs);
        $vectors = array_map(fn($line) => ['o' => explode(',', $line[0]), 'e' => explode(',', $line[1])], $inputs);

        $map = [];
        array_walk($vectors, function(array $vector) use (&$map) {
            $x    = (int) $vector['o'][0];
            $y    = (int) $vector['o'][1];
            $endX = (int) $vector['e'][0];
            $endY = (int) $vector['e'][1];
            $map["$x.$y"] = ($map["$x.$y"] ?? 0) + 1;
            while($x !== $endX || $y !== $endY) {
                $x += ($x !== $endX ? ($x > $endX ? -1 : +1) : 0);
                $y += ($y !== $endY ? ($y > $endY ? -1 : +1) : 0);
                $map["$x.$y"] = ($map["$x.$y"] ?? 0) + 1;
            }
        });

        /*for ($y = 0; $y < 10; $y++) {
            for ($x = 0; $x < 10; $x++) {
                echo ($map["$x.$y"] ?? '.');
            }
            echo PHP_EOL;
        }*/

        return count(array_filter($map, fn($val) => $val > 1));
    }

    public function vec2map(array $map, array $vector): array
    {
        $x    = (int) $vector['s'][0];
        $y    = (int) $vector['s'][1];
        $endX = (int) $vector['e'][0];
        $endY = (int) $vector['e'][1];
        $map["$x.$y"] = ($map["$x.$y"] ?? 0) + 1;
        while($x !== $endX || $y !== $endY) {
            $x += ((int) ($x !== $endX)) * ($x > $endX ? -1 : +1);
            $y += ((int) ($y !== $endY)) * ($y > $endY ? -1 : +1);
            $map["$x.$y"] = ($map["$x.$y"] ?? 0) + 1;
        }

        return $map;
    }

    private function starOneFunctional(array $inputs): int
    {
        return (int) (new Pipeline())
            ->array($inputs)
                ->map(fn($line) => explode(' -> ', $line))
                ->map(fn($line) => ['s' => explode(',', $line[0]), 'e' => explode(',', $line[1])])
                ->filter(fn($vector) => $vector['s'][0] === $vector['e'][0] || $vector['s'][1] === $vector['e'][1])
                ->reduce([$this, 'vec2map'], [])
                ->filter(fn($val) => $val > 1)
                ->count()
            //->int()
            ->get();
    }

    private function starTwoFunctional(array $inputs): int
    {
        return (int) (new Pipeline())
            ->array($inputs)
                ->map(fn($line) => explode(' -> ', $line))
                ->map(fn($line) => ['s' => explode(',', $line[0]), 'e' => explode(',', $line[1])])
                ->reduce([$this, 'vec2map'], [])
                ->filter(fn($val) => $val > 1)
                ->count()
            //->int()
            ->get();
    }
}
