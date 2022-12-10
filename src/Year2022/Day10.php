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
use Eureka\Component\Console\Argument\Argument;
use Eureka\Component\Console\IO\Out;
use Fiber;

class Day10 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [0 => ['noop', 'addx 3', 'addx -5']],
                [13140 => [
                    'addx 15', 'addx -11', 'addx 6', 'addx -3', 'addx 5', 'addx -1', 'addx -8', 'addx 13', 'addx 4',
                    'noop', 'addx -1', 'addx 5', 'addx -1', 'addx 5', 'addx -1', 'addx 5', 'addx -1', 'addx 5',
                    'addx -1', 'addx -35', 'addx 1', 'addx 24', 'addx -19', 'addx 1', 'addx 16', 'addx -11', 'noop',
                    'noop', 'addx 21', 'addx -15', 'noop', 'noop', 'addx -3', 'addx 9', 'addx 1', 'addx -3', 'addx 8',
                    'addx 1', 'addx 5', 'noop', 'noop', 'noop', 'noop', 'noop', 'addx -36', 'noop', 'addx 1', 'addx 7',
                    'noop', 'noop', 'noop', 'addx 2', 'addx 6', 'noop', 'noop', 'noop', 'noop', 'noop', 'addx 1',
                    'noop', 'noop', 'addx 7', 'addx 1', 'noop', 'addx -13', 'addx 13', 'addx 7', 'noop', 'addx 1',
                    'addx -33', 'noop', 'noop', 'noop', 'addx 2', 'noop', 'noop', 'noop', 'addx 8', 'noop', 'addx -1',
                    'addx 2', 'addx 1', 'noop', 'addx 17', 'addx -9', 'addx 1', 'addx 1', 'addx -3', 'addx 11', 'noop',
                    'noop', 'addx 1', 'noop', 'addx 1', 'noop', 'noop', 'addx -13', 'addx -19', 'addx 1', 'addx 3',
                    'addx 26', 'addx -30', 'addx 12', 'addx -1', 'addx 3', 'addx 1', 'noop', 'noop', 'noop', 'addx -9',
                    'addx 18', 'addx 1', 'addx 2', 'noop', 'noop', 'addx 9', 'noop', 'noop', 'noop', 'addx -1',
                    'addx 2', 'addx -37', 'addx 1', 'addx 3', 'noop', 'addx 15', 'addx -21', 'addx 22', 'addx -6',
                    'addx 1', 'noop', 'addx 2', 'addx 1', 'noop','addx -10','noop','noop','addx 20','addx 1', 'addx 2',
                    'addx 2', 'addx -6', 'addx -11', 'noop', 'noop', 'noop',
                ]],
            ],
            '**' => [
                [0 => [
                    'addx 15', 'addx -11', 'addx 6', 'addx -3', 'addx 5', 'addx -1', 'addx -8', 'addx 13', 'addx 4',
                    'noop', 'addx -1', 'addx 5', 'addx -1', 'addx 5', 'addx -1', 'addx 5', 'addx -1', 'addx 5',
                    'addx -1', 'addx -35', 'addx 1', 'addx 24', 'addx -19', 'addx 1', 'addx 16', 'addx -11', 'noop',
                    'noop', 'addx 21', 'addx -15', 'noop', 'noop', 'addx -3', 'addx 9', 'addx 1', 'addx -3', 'addx 8',
                    'addx 1', 'addx 5', 'noop', 'noop', 'noop', 'noop', 'noop', 'addx -36', 'noop', 'addx 1', 'addx 7',
                    'noop', 'noop', 'noop', 'addx 2', 'addx 6', 'noop', 'noop', 'noop', 'noop', 'noop', 'addx 1',
                    'noop', 'noop', 'addx 7', 'addx 1', 'noop', 'addx -13', 'addx 13', 'addx 7', 'noop', 'addx 1',
                    'addx -33', 'noop', 'noop', 'noop', 'addx 2', 'noop', 'noop', 'noop', 'addx 8', 'noop', 'addx -1',
                    'addx 2', 'addx 1', 'noop', 'addx 17', 'addx -9', 'addx 1', 'addx 1', 'addx -3', 'addx 11', 'noop',
                    'noop', 'addx 1', 'noop', 'addx 1', 'noop', 'noop', 'addx -13', 'addx -19', 'addx 1', 'addx 3',
                    'addx 26', 'addx -30', 'addx 12', 'addx -1', 'addx 3', 'addx 1', 'noop', 'noop', 'noop', 'addx -9',
                    'addx 18', 'addx 1', 'addx 2', 'noop', 'noop', 'addx 9', 'noop', 'noop', 'noop', 'addx -1',
                    'addx 2', 'addx -37', 'addx 1', 'addx 3', 'noop', 'addx 15', 'addx -21', 'addx 22', 'addx -6',
                    'addx 1', 'noop', 'addx 2', 'addx 1', 'noop','addx -10','noop','noop','addx 20','addx 1', 'addx 2',
                    'addx 2', 'addx -6', 'addx -11', 'noop', 'noop', 'noop',
                ]]
            ],
        ];

        return $examples[$star];
    }

    /**
     * @throws \Throwable
     */
    public function solve(string $star, array $inputs): string
    {
        return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    protected function getSignalStrength(int $cycle, int $x): int
    {
        if (!in_array($cycle, [20, 60, 100, 140, 180, 220])) {
            return 0;
        }

        return ($cycle * $x);
    }

    protected function getPixel(int $cycle, int $x): string
    {
        $pixel  = ($cycle - 1) % 40;
        $isLast = $cycle % 40 === 0;

        return (in_array($pixel, [$x - 1, $x, $x + 1]) ? '#' : '.') . ($isLast ? "\n" : '');
    }

    /**
     * @throws \Throwable
     */
    protected function process(array $inputs, callable $subprocess): int|string|null
    {
        $cycle = 0;
        $x     = 1;
        foreach ($inputs as $input) {
            $cycle++;
            Fiber::suspend($subprocess($cycle, $x));
            if ($input === 'noop') {
                continue;
            }

            $cycle++;
            Fiber::suspend($subprocess($cycle, $x));

            [, $v] = explode(' ', $input);
            $x += (int) $v;
        }

        return null;
    }

    /**
     * @throws \Throwable
     */
    private function starOne(array $inputs): int
    {
        $processor = new Fiber($this->process(...));

        $signalStrength = $processor->start($inputs, $this->getSignalStrength(...));
        while (!$processor->isTerminated()) {
            $signalStrength += $processor->resume();
        }

        return $signalStrength;
    }

    /**
     * @throws \Throwable
     */
    private function starTwo(array $inputs): int
    {
        $processor = new Fiber($this->process(...));

        Out::std($processor->start($inputs, $this->getPixel(...)));
        while (!$processor->isTerminated()) {
            Out::std($processor->resume(), '');
        }

        return 0;
    }
}
