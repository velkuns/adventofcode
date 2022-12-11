<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Common;

abstract class Day implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        return [];
    }

    public function solve(string $star, array $inputs, bool $isFunctional = false): string
    {
        if ($isFunctional) {
            return (string) ($star === '*' ? $this->starOneFunctional($inputs) : $this->starTwoFunctional($inputs));
        } else {
            return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
        }
    }

    abstract protected function starOne(array $inputs): mixed;

    abstract protected function starTwo(array $inputs): mixed;

    private function starOneFunctional(): int
    {
        return 0;
    }

    private function starTwoFunctional(): int
    {
        return 0;
    }
}
