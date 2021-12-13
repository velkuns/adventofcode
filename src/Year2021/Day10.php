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

/**
 * Class Day10
 *
 * @author Romain Cottard
 */
class Day10 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [26397 => [
                    '[({(<(())[]>[[{[]{<()<>>',
                    '[(()[<>])]({[<{<<[]>>(',
                    '{([(<{}[<>[]}>{[]{[(<()>',
                    '(((({<>}<{<{<>}{[]{[]{}',
                    '[[<[([]))<([[{}[[()]]]',
                    '[{[{({}]{}}([{[{{{}}([]',
                    '{<[[]]>}<{[{[{[]{()[[[]',
                    '[<(<(<(<{}))><([]([]()',
                    '<{([([[(<>()){}]>(<<{{',
                    '<{([{{}}[<[[[<>{}]]]>[]]',
                ]],
            ],
            '**' => [
                [288957 => [
                    '[({(<(())[]>[[{[]{<()<>>',
                    '[(()[<>])]({[<{<<[]>>(',
                    '{([(<{}[<>[]}>{[]{[(<()>',
                    '(((({<>}<{<{<>}{[]{[]{}',
                    '[[<[([]))<([[{}[[()]]]',
                    '[{[{({}]{}}([{[{{{}}([]',
                    '{<[[]]>}<{[{[{[]{()[[[]',
                    '[<(<(<(<{}))><([]([]()',
                    '<{([([[(<>()){}]>(<<{{',
                    '<{([{{}}[<[[[<>{}]]]>[]]',
                ]],
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
        static $opened = ['>' => '<', ')' => '(', '}' => '{', ']' => '['], $closed = [')' => 3, ']' => 57, '}' => 1197, '>' => 25137];

        $scores = [];
        foreach ($inputs as $input) {
            $stack = [];
            for ($pos = 0, $max = strlen($input); $pos < $max; $pos++) {
                $char = $input[$pos];
                if (in_array($char, $opened)) {
                    $stack[] = $char;
                    continue;
                }

                if ($opened[$char] !== end($stack)) {
                    $scores[$char] = ($scores[$char] ?? 0) + $closed[$char];
                    break;
                }
                array_pop($stack);
            }
        }

        return array_sum($scores);
    }

    private function starTwo(array $inputs): int
    {
        static $opened = ['>' => '<', ')' => '(', '}' => '{', ']' => '['], $closed = ['(' => 1, '[' => 2, '{' => 3, '<' => 4];

        $scores = [];
        foreach ($inputs as $input) {
            $stack = [];
            for ($pos = 0, $max = strlen($input); $pos < $max; $pos++) {
                $char = $input[$pos];
                if (in_array($char, $opened)) {
                    $stack[] = $char;
                    continue;
                }

                if ($opened[$char] !== end($stack)) {
                    continue 2; // Skip invalid line
                }

                array_pop($stack);
            }

            $score = 0;
            $stack = array_reverse($stack);
            foreach ($stack as $char) {
                $score = ($score * 5) + $closed[$char];
            }
            $scores[] = $score;
        }

        return (int) $this->median($scores);
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
