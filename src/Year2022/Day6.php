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

class Day6 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [
                    5  => ['bvwbjplbgvbhsrlpgdmjqwftvncz'],
                    6  => ['nppdvjthqldpwncqszvftbrmjlhg'],
                    10 => ['nznrnfrfntjfmvfwmzdfjlvtqnbhcprsg'],
                    11 => ['zcfzfwzzqfrljwzlrfnpqdbhtmscgvjw']
                ]
            ],
            '**' => [
                [
                    19  => ['mjqjpqmgbljsphdztnvjfqwrcgsmlb'],
                    23  => ['bvwbjplbgvbhsrlpgdmjqwftvncz'],
                    //'23' => ['nppdvjthqldpwncqszvftbrmjlhg'],
                    29  => ['nznrnfrfntjfmvfwmzdfjlvtqnbhcprsg'],
                    26  => ['zcfzfwzzqfrljwzlrfnpqdbhtmscgvjw'],
                ]
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs): string
    {
        return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    private function starOne(array $inputs): int
    {
        $input  = reset($inputs);
        $length = 4;
        for ($c = 0, $max = strlen($input); $c < $max; $c++) {
            if (count(count_chars(substr($input, $c, $length), 1)) === $length) {
                return $c + $length;
            }
        }

        return 0;
    }

    private function starTwo(array $inputs): int
    {
        $input  = reset($inputs);
        $length = 14;
        for ($c = 0, $max = strlen($input); $c < $max; $c++) {
            if (count(count_chars(substr($input, $c, $length), 1)) === $length) {
                return $c + $length;
            }
        }

        return 0;
    }
}
