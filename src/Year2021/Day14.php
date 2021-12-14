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

/**
 * Class Day14
 *
 * @author Romain Cottard
 */
class Day14 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [1588  => ['NNCB', '', 'CH -> B', 'HH -> N', 'CB -> H', 'NH -> C', 'HB -> C', 'HC -> B', 'HN -> C', 'NN -> C', 'BH -> H', 'NC -> B', 'NB -> B', 'BN -> B', 'BB -> N', 'BC -> B', 'CC -> N', 'CN -> C']],
            ],
            '**' => [
                [2188189693529  => ['NNCB', '', 'CH -> B', 'HH -> N', 'CB -> H', 'NH -> C', 'HB -> C', 'HC -> B', 'HN -> C', 'NN -> C', 'BH -> H', 'NC -> B', 'NB -> B', 'BN -> B', 'BB -> N', 'BC -> B', 'CC -> N', 'CN -> C']],
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
        $newTemplate = array_shift($inputs);
        array_shift($inputs);

        $pairs = array_map(fn($input) => explode(' -> ', $input), $inputs);
        $pairs = array_combine(array_column($pairs, 0), array_column($pairs, 1));

        for ($n = 1; $n <= 10; $n++) {
            $template    = $newTemplate;
            $newTemplate = '';
            for ($c = 0, $len = strlen($template) - 1; $c < $len; $c++) {
                $c1 = $template[$c];
                $c2 = $template[$c + 1];
                $cn = $pairs["$c1$c2"];
                $newTemplate .= "$c1$cn";
            }
            $newTemplate .= $c2;
        }

        $chars = count_chars($newTemplate, 1);
        asort($chars);

        return (int) (array_pop($chars) - array_shift($chars));
    }

    private function starTwo(array $inputs): int
    {
        $template = array_shift($inputs);
        array_shift($inputs);

        $dict = array_map(fn($input) => explode(' -> ', $input), $inputs);
        $dict = array_combine(array_column($dict, 0), array_column($dict, 1));

        $pairs = array_count_values(array_merge(
            str_split($template, 2),
            str_split(substr($template, 1, -1), 2)
        ));

        for ($n = 1; $n <= 10; $n++) {
            foreach ($pairs as $pair => $n) {
                [$c1, $c2] = str_split($pair);
                $cn = $dict[$pair];
                $pairs["$c1$cn"] = ($pairs["$c1$cn"] ?? 0) + $n;
                $pairs["$cn$c2"] = ($pairs["$cn$c2"] ?? 0) + $n;
                $pairs[$pair]--;
                if ($pairs[$pair] === 0) {
                    unset($pairs[$pair]);
                }
            }
        }

        $chars = [];
        foreach ($pairs as $pair => $n) {
            [$c1, $c2] = str_split($pair);
            $chars[$c1] = ($chars[$c1] ?? 0) + $n;
            $chars[$c2] = ($chars[$c2] ?? 0) + $n;
        }
        asort($chars);

        var_export($chars);

        return (int) (array_pop($chars) - array_shift($chars));
    }
}
