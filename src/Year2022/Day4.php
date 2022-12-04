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

class Day4 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [2 => ['2-4,6-8', '2-3,4-5', '5-7,7-9', '2-8,3-7', '6-6,4-6', '2-6,4-8']]
            ],
            '**' => [
                [4 => ['2-4,6-8', '2-3,4-5', '5-7,7-9', '2-8,3-7', '6-6,4-6', '2-6,4-8']]
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
        return count(
            array_filter(
                array_map(fn ($input) => array_map(fn($range) => explode('-', $range), explode(',', $input)), $inputs),
                fn ($r) => (min($r[0]) >= min($r[1]) && max($r[0]) <= max($r[1])) || (min($r[1]) >= min($r[0]) && max($r[1]) <= max($r[0]))
            )
        );
    }

    private function starTwo(array $inputs): int
    {
        return count(
            array_filter(
                array_map(fn ($input) => array_map(fn($range) => explode('-', $range), explode(',', $input)), $inputs),
                fn ($r) => (min($r[0]) >= min($r[1]) && min($r[0]) <= max($r[1])) || (min($r[1]) >= min($r[0]) && min($r[1]) <= max($r[0]))
            )
        );
    }

    private function starOneFunctional(array $inputs): int
    {
        return (int) (new Pipeline())
            ->array($inputs)
            ->each()
                ->explode(',')
                ->map(fn($input) => explode('-', $input))
            ->end()
            ->filter(fn($r) => (min($r[0]) >= min($r[1]) && max($r[0]) <= max($r[1])) || (min($r[1]) >= min($r[0]) && max($r[1]) <= max($r[0])))
            ->count()
            ->get()
        ;
    }

    private function starTwoFunctional(array $inputs): int
    {
        return (int) (new Pipeline())
            ->array($inputs)
            ->each()
                ->explode(',')
                ->map(fn($input) => explode('-', $input))
            ->end()
            ->filter(fn($r) => (min($r[0]) >= min($r[1]) && min($r[0]) <= max($r[1])) || (min($r[1]) >= min($r[0]) && min($r[1]) <= max($r[0])))
            ->count()
            ->get()
        ;
    }
}
