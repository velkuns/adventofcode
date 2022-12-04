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

class Day1 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [24000 => ['1000', '2000', '3000', '', '4000', '', '5000', '6000', '', '7000', '8000', '9000', '', '10000']]
            ],
            '**' => [
                [45000 => ['1000', '2000', '3000', '', '4000', '', '5000', '6000', '', '7000', '8000', '9000', '', '10000']]
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
        return max(array_reduce(
            $inputs,
            function (array $return, string $item) {
                (empty($item) ? ($return[] = 0) : $return[count($return) - 1] += (int) $item);
                return $return;
            },
            [0]
        ));
    }

    private function starTwo(array $inputs): int
    {
        $elves = array_reduce(
            $inputs,
            function (array $return, string $item) {
                (empty($item) ? ($return[] = 0) : $return[count($return) - 1] += (int) $item);
                return $return;
            },
            [0]
        );

        rsort($elves);

        return array_sum(array_slice($elves, 0, 3));
    }

    private function starOneFunctional(array $inputs): int
    {
        return (int) (new Pipeline())
            ->array($inputs)
            ->reduce(
                function (array $return, string $item) {
                    (empty($item) ? ($return[] = 0) : $return[count($return) - 1] += (int) $item);
                    return $return;
                },
                [0]
            )
            ->max()
            ->get()
        ;
    }

    private function starTwoFunctional(array $inputs): int
    {
        return (int) (new Pipeline())
            ->array($inputs)
            ->reduce(
                function (array $return, string $item) {
                    (empty($item) ? ($return[] = 0) : $return[count($return) - 1] += (int) $item);
                    return $return;
                },
                [0]
            )
            ->rsort()
            ->slice(0, 3)
            ->sum()
            ->get()
        ;
    }
}
