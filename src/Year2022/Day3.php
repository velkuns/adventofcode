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

class Day3 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [157 => [
                    'vJrwpWtwJgWrhcsFMMfFFhFp',
                    'jqHRNqRjqzjGDLGLrsFMfFZSrLrFZsSL',
                    'PmmdzqPrVvPwwTWBwg',
                    'wMqvLMZHhHMvwLHjbvcjnnSBnvTQFn',
                    'ttgJtRGJQctTZtZT',
                    'CrZsJsPPZsGzwwsLwLmpwMDw',
                ]]
            ],
            '**' => [
                [70 => [
                    'vJrwpWtwJgWrhcsFMMfFFhFp',
                    'jqHRNqRjqzjGDLGLrsFMfFZSrLrFZsSL',
                    'PmmdzqPrVvPwwTWBwg',
                    'wMqvLMZHhHMvwLHjbvcjnnSBnvTQFn',
                    'ttgJtRGJQctTZtZT',
                    'CrZsJsPPZsGzwwsLwLmpwMDw',
                ]]
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
        return array_reduce($inputs, function ($carry, $input) {
                [$a, $b] = array_map(str_split(...), str_split($input, strlen($input) / 2));
                $char = current(array_intersect(array_unique($a), array_unique($b)));
                return $carry + ord($char) - (ctype_lower($char) ? 96 : 38);
            }, 0);
    }

    private function starTwo(array $inputs): int
    {
        return array_reduce(array_chunk($inputs, 3) , function ($carry, $input) {
                [$a, $b, $c] = array_map(str_split(...), $input);
                $char = current(array_intersect(array_unique($a), array_unique($b), array_unique($c)));
                return $carry + ord($char) - (ctype_lower($char) ? 96 : 38);
            }, 0);
    }

    private function starOneFunctional(array $inputs): int
    {
        return (int) (new Pipeline())
            ->array($inputs)
            ->map(fn(string $input) => str_split($input, strlen($input) / 2))
            ->each()
                ->map(str_split(...))
                ->map(array_unique(...))
                ->intersect()
                ->current()
            ->end()
            ->map(fn($char) => ord($char) - (ctype_lower($char) ? 96 : 38))
            ->sum()
            ->get()
        ;
    }

    private function starTwoFunctional(array $inputs): int
    {
        return (int) (new Pipeline())
            ->array($inputs)
            ->chunk(3)
            ->each()
                ->map(str_split(...))
                ->map(array_unique(...))
                ->intersect()
                ->current()
            ->end()
            ->map(fn($char) => ord($char) - (ctype_lower($char) ? 96 : 38))
            ->sum()
            ->get()
        ;
    }
}
