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

class Day5 implements AlgorithmInterface
{
    private bool $useExample = false;

    public function getExamples(string $star): array
    {
        $this->useExample = true;

        $examples = [
            '*'  => [
                ['CMZ' => [
                    'move 1 from 2 to 1',
                    'move 3 from 1 to 3',
                    'move 2 from 2 to 1',
                    'move 1 from 1 to 2',
                ]]
            ],
            '**' => [
                ['MCD' => [
                    'move 1 from 2 to 1',
                    'move 3 from 1 to 3',
                    'move 2 from 2 to 1',
                    'move 1 from 1 to 2',
                ]]
            ],
        ];

        return $examples[$star];
    }

    private function getStacks(): array
    {
        if ($this->useExample) {
            $this->useExample = false;
            return [1 => ['Z', 'N'], 2 => ['M', 'C', 'D'], 3 => ['P']];
        }

        return [
            1 => ['V', 'C', 'D', 'R', 'Z', 'G', 'B', 'W'],
            2 => ['G', 'W', 'F', 'C', 'B', 'S', 'T', 'V'],
            3 => ['C', 'B', 'S', 'N', 'W'],
            4 => ['Q', 'G', 'M', 'N', 'J', 'V', 'C', 'P'],
            5 => ['T', 'S', 'L', 'F', 'D', 'H', 'B'],
            6 => ['J', 'V', 'T', 'W', 'M', 'N'],
            7 => ['P', 'F', 'L', 'C', 'S', 'T', 'G'],
            8 => ['B', 'D', 'Z'],
            9 => ['M', 'N', 'Z', 'W'],
        ];
    }

    public function solve(string $star, array $inputs): string
    {
        return ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    private function starOne(array $inputs): string
    {
        $stacks = $this->getStacks();

        array_walk($inputs, function (string $input) use (&$stacks) {
            [, $length, , $from, , $to] = explode(' ', $input);
            $stacks[$to] = array_merge($stacks[$to], array_reverse(array_splice($stacks[$from], -$length, (int) $length)));
        });

        return implode('', array_map(fn(array $stack) => array_pop($stack), $stacks));
    }

    private function starTwo(array $inputs): string
    {
        $stacks = $this->getStacks();

        array_walk($inputs, function (string $input) use (&$stacks) {
            [, $length, , $from, , $to] = explode(' ', $input);
            $stacks[$to] = array_merge($stacks[$to], array_splice($stacks[$from], -$length, (int) $length));
        });

        return implode('', array_map(fn(array $stack) => array_pop($stack), $stacks));
    }
}
