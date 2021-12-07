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
use Eureka\Component\Console\Progress\Progress;

/**
 * Class Day7
 *
 * @author Romain Cottard
 */
class Day7 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [37 => ['16,1,2,0,4,2,7,1,2,14']],
            ],
            '**' => [
                [168 => ['16,1,2,0,4,2,7,1,2,14']],
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs, bool $functionalMode = false): string
    {
        return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    private function starOne(array $inputs): int
    {
        $positions = array_map('intval', explode(',', $inputs[0]));
        $median = $this->median($positions);

        return (int) min(
            array_reduce($positions, fn ($value, $position) => $value + abs($position - floor($median)), 0),
            array_reduce($positions, fn ($value, $position) => $value + abs($position - ceil($median)), 0)
        );
    }

    private function starTwo(array $inputs): int
    {
        $positions = array_map('intval', explode(',', $inputs[0]));
        $average   = array_sum($positions) / count($positions);

        return (int) min(
            array_reduce($positions, fn ($value, $position) => $value + array_sum(range(1, abs($position - floor($average)))), 0),
            array_reduce($positions, fn ($value, $position) => $value + array_sum(range(1, abs($position - ceil($average)))), 0)
        );
    }

    private function median(array $array): float
    {
        $array = array_values($array);
        sort($array);

        $nb = count($array);
        if (count($array) % 2 === 0) {
            $median = ($array[(int) (($nb / 2) - 1)] + $array[(int) ($nb / 2)]) / 2;
        } else {
            $median = $array[(int) floor($nb / 2)];
        }

        return (float) $median;
    }
}
