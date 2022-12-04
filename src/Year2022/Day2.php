<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Year2022;

use Application\Common\AlgorithmInterface;
use Application\Pipeline\Pipeline;

class Day2 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [15 => ['A Y', 'B X', 'C Z']]
            ],
            '**' => [
                [12 => ['A Y', 'B X', 'C Z']]
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs, bool $isFunctional = false): string
    {
        if ($isFunctional) {
            return (string) ($star === '*' ? $this->starOneFunctional($inputs) : $this->starTwoFunctional($inputs));
        } else {
            return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
        }
    }

    private function starOne(array $inputs): int
    {
        $config = [
            'score' => ['X' => 1, 'Y' => 2, 'Z' => 3],
            'beat'  => ['AX' => 3, 'BY' => 3, 'CZ' => 3, 'AY' => 6, 'BZ' => 6, 'CX' => 6],
        ];

        return array_sum(
            array_map(
                fn($a) => ($config['score'][$a[1]] + (int) ($config['beat'][$a[0].$a[1]] ?? 0)),
                array_map(fn(string $input) => explode(' ', $input), $inputs)
            )
        );
    }

    private function starTwo(array $inputs): int
    {
        $config = [
            'score' => ['X' => 0, 'Y' => 3, 'Z' => 6],
            'item'  => ['A' => 1, 'B' => 2, 'C' => 3],
            'beat'  => ['A' => 'C', 'B' => 'A', 'C' => 'B'],
        ];

        return array_sum(
            array_map(
                fn ($a) => $config['score'][$a[1]] + match($a[1]) {
                    'X' => $config['item'][$config['beat'][$a[0]]],
                    'Y' => $config['item'][$a[0]],
                    'Z' => $config['item'][array_flip($config['beat'])[$a[0]]],
                },
                array_map(fn(string $input) => explode(' ', $input), $inputs)
            )
        );
    }

    private function starOneFunctional(array $inputs): int
    {
        $config = [
            'score' => ['X' => 1, 'Y' => 2, 'Z' => 3],
            'beat'  => ['AX' => 3, 'BY' => 3, 'CZ' => 3, 'AY' => 6, 'BZ' => 6, 'CX' => 6],
        ];

        return (int) (new Pipeline())
            ->array($inputs)
            ->map(fn(string $input) => explode(' ', $input))
            ->map(fn(array $a) => ($config['score'][$a[1]] + (int) ($config['beat'][$a[0].$a[1]] ?? 0)))
            ->sum()
            ->get()
        ;
    }

    private function starTwoFunctional(array $inputs): int
    {
        $config = [
            'score' => ['X' => 0, 'Y' => 3, 'Z' => 6],
            'item'  => ['A' => 1, 'B' => 2, 'C' => 3],
            'beat'  => ['A' => 'C', 'B' => 'A', 'C' => 'B'],
        ];

        return (int) (new Pipeline())
            ->array($inputs)
            ->map(fn(string $input) => explode(' ', $input))
            ->map(fn ($a) => $config['score'][$a[1]] + match($a[1]) {
                    'X' => $config['item'][$config['beat'][$a[0]]],
                    'Y' => $config['item'][$a[0]],
                    'Z' => $config['item'][array_flip($config['beat'])[$a[0]]],
                })
            ->sum()
            ->get()
        ;
    }
}
