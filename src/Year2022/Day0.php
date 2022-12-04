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

class Day0 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [0 => ['a', 'b', 'c']]
            ],
            '**' => [
                [1 => ['a', 'b', 'c']]
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
        return 0;
    }

    private function starTwo(array $inputs): int
    {
        return 0;
    }

    private function starOneFunctional(array $inputs): int
    {
        return (int) (new Pipeline())
            ->array($inputs)
            ->int(0)
            ->get()
        ;
    }

    private function starTwoFunctional(array $inputs): int
    {
        return (int) (new Pipeline())
            ->array($inputs)
            ->int(0)
            ->get()
        ;
    }
}
