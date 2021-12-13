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
 * Class Day8
 *
 * @author Romain Cottard
 */
class Day8 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [26 => [
                    'be cfbegad cbdgef fgaecd cgeb fdcge agebfd fecdb fabcd edb | fdgacbe cefdb cefbgd gcbe',
                    'edbfga begcd cbg gc gcadebf fbgde acbgfd abcde gfcbed gfec | fcgedb cgb dgebacf gc',
                    'fgaebd cg bdaec gdafb agbcfd gdcbef bgcad gfac gcb cdgabef | cg cg fdcagb cbg',
                    'fbegcd cbd adcefb dageb afcb bc aefdc ecdab fgdeca fcdbega | efabcd cedba gadfec cb',
                    'aecbfdg fbg gf bafeg dbefa fcge gcbea fcaegb dgceab fcbdga | gecf egdcabf bgf bfgea',
                    'fgeab ca afcebg bdacfeg cfaedg gcfdb baec bfadeg bafgc acf | gebdcfa ecba ca fadegcb',
                    'dbcfg fgd bdegcaf fgec aegbdf ecdfab fbedc dacgb gdcebf gf | cefg dcbef fcge gbcadfe',
                    'bdfegc cbegaf gecbf dfcage bdacg ed bedf ced adcbefg gebcd | ed bcgafe cdgba cbgef',
                    'egadfb cdbfeg cegd fecab cgb gbdefca cg fgcdab egfdb bfceg | gbdfcae bgc cg cgb',
                    'gcafb gcf dcaebfg ecagb gf abcdeg gaef cafbge fdbac fegbdc | fgae cfgab fg bagce',
                ]],
            ],
            '**' => [
                [61229 => [
                    'be cfbegad cbdgef fgaecd cgeb fdcge agebfd fecdb fabcd edb | fdgacbe cefdb cefbgd gcbe',
                    'edbfga begcd cbg gc gcadebf fbgde acbgfd abcde gfcbed gfec | fcgedb cgb dgebacf gc',
                    'fgaebd cg bdaec gdafb agbcfd gdcbef bgcad gfac gcb cdgabef | cg cg fdcagb cbg',
                    'fbegcd cbd adcefb dageb afcb bc aefdc ecdab fgdeca fcdbega | efabcd cedba gadfec cb',
                    'aecbfdg fbg gf bafeg dbefa fcge gcbea fcaegb dgceab fcbdga | gecf egdcabf bgf bfgea',
                    'fgeab ca afcebg bdacfeg cfaedg gcfdb baec bfadeg bafgc acf | gebdcfa ecba ca fadegcb',
                    'dbcfg fgd bdegcaf fgec aegbdf ecdfab fbedc dacgb gdcebf gf | cefg dcbef fcge gbcadfe',
                    'bdfegc cbegaf gecbf dfcage bdacg ed bedf ced adcbefg gebcd | ed bcgafe cdgba cbgef',
                    'egadfb cdbfeg cegd fecab cgb gbdefca cg fgcdab egfdb bfceg | gbdfcae bgc cg cgb',
                    'gcafb gcf dcaebfg ecagb gf abcdeg gaef cafbge fdbac fegbdc | fgae cfgab fg bagce',
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
        return (new Pipeline())
            ->array($inputs)
                ->map(fn(string $value): array => explode(' | ', $value))
                ->map(fn(array $parts): array => array_map('strlen', explode(' ', $parts[1])))
                ->map(fn(array $digits): array => array_filter($digits, fn(int $digit) => in_array($digit, [2, 3, 4, 7])))
                ->reduce(fn(int $nb, array $digits): int => $nb + count($digits), 0)
            //->int()
            ->get();
    }

    private function starTwo(array $inputs): int
    {
        return (new Pipeline())
            ->array($inputs)
                ->map(fn(string $value): array => explode(' | ', $value))
                ->map(fn(array $parts): array => [explode(' ', $parts[0]), explode(' ', $parts[1])])
                ->map([$this, 'digitSolver'])
                ->sum()
            //->int()
            ->get();
    }

    public function digitSolver(array $parts): int
    {
        $segments = [];
        $digits   = [];

        $tmp     = [5 => [], 6 => []];
        $uniques = [2 => 1, 3 => 7, 4 => 4, 7 => 8];

        foreach ($parts[0] as $digit) {
            $len   = strlen($digit);
            $digit = str_split($digit);
            sort($digit);
            if (isset($uniques[$len])) {
                $digits[$uniques[$len]] = $digit;
            } else {
                $tmp[$len][] = $digit;
            }
        }

        //~ Top segment
        $segments['t'] = array_values(array_diff($digits[7], $digits[1]))[0];

        //~ Search for digit 5 to deduce bottom segment + top right & bottom rights
        $new = array_merge($digits[4], [$segments['t']]);
        foreach ($tmp[5] as $index => $digit) {
            $diff = array_diff($digit, $new);
            if (count($diff) > 1 || count(array_diff($digits[1], $digit)) !== 1) {
                continue;
            }

            unset($tmp[5][$index]);
            $digits[5]      = $digit;
            $segments['b']  = reset($diff);
            $segments['tr'] = array_values(array_diff($digits[1], $digits[5]))[0];
            $segments['br'] = array_values(array_intersect($digits[1], $digits[5]))[0];
            break;
        }

        //~ Search for digit 3 do deduce middle segment
        $new  = array_merge($digits[7], [$segments['b']]);
        foreach ($tmp[5] as $index => $digit) {
            $diff = array_diff($digit, $new);
            if (count($diff) > 1) {
                continue;
            }

            unset($tmp[5][$index]);
            $digits[3]     = $digit;
            $segments['m'] = reset($diff);
            break;
        }

        $digits[2] = reset($tmp[5]);
        $segments['bl'] = array_values(array_diff($digits[2], $digits[3]))[0];

        $digits[9] = array_merge($digits[5], [$segments['tr']]);
        $digits[6] = array_merge($digits[5], [$segments['bl']]);
        $digits[0] = array_values(array_diff($digits[8], [$segments['m']]));

        foreach ($digits as $digit => $segments) {
            sort($segments);
            $digits[$digit] = implode('', $segments);
        }

        $digits = array_flip($digits);

        $number = '';
        foreach ($parts[1] as $index => $segments) {
            $segments = str_split($segments);
            sort($segments);
            $segments = implode('', $segments);
            $parts[1][$index] = $segments;
            $number .= $digits[$segments];
        }

        return (int) $number;
    }

    public function digitSolver2(array $parts): int
    {
        $segments = [];
        $digits   = [];

        $tmp     = [5 => [], 6 => []];
        $uniques = [2 => 1, 3 => 7, 4 => 4, 7 => 8];

        foreach ($parts[0] as $digit) {
            $len   = strlen($digit);
            $digit = str_split($digit);
            if (isset($uniques[$len])) {
                $digits[$uniques[$len]] = $digit;
                continue;
            }

            $tmp[$len][] = $digit;
        }

        //~ Top segment
        $segments['t'] = array_values(array_diff($digits[7], $digits[1]))[0];

        //~ Search for digit 5 to deduce bottom segment + top right & bottom rights
        $new = array_merge($digits[4], [$segments['t']]);
        foreach ($tmp[5] as $index => $digit) {
            $diff = array_diff($digit, $new);
            if (count($diff) > 1 || count(array_diff($digits[1], $digit)) !== 1) {
                continue;
            }

            unset($tmp[5][$index]);
            $digits[5]      = $digit;
            $segments['b']  = reset($diff);
            $segments['tr'] = array_values(array_diff($digits[1], $digits[5]))[0];
            $segments['br'] = array_values(array_intersect($digits[1], $digits[5]))[0];
            break;
        }

        //~ Search for digit 3 do deduce middle segment
        $new  = array_merge($digits[7], [$segments['b']]);
        foreach ($tmp[5] as $index => $digit) {
            $diff = array_diff($digit, $new);
            if (count($diff) > 1) {
                continue;
            }

            unset($tmp[5][$index]);
            $digits[3]     = $digit;
            $segments['m'] = reset($diff);
            break;
        }

        $digits[2] = reset($tmp[5]);
        $segments['bl'] = array_values(array_diff($digits[2], $digits[3]))[0];

        $digits[9] = array_merge($digits[5], [$segments['tr']]);
        $digits[6] = array_merge($digits[5], [$segments['bl']]);
        $digits[0] = array_values(array_diff($digits[8], [$segments['m']]));

        foreach ($digits as $digit => $segments) {
            sort($segments);
            $digits[$digit] = implode('', $segments);
        }

        $digits = array_flip($digits);

        $number = '';
        foreach ($parts[1] as $index => $segments) {
            $segments = str_split($segments);
            sort($segments);
            $segments = implode('', $segments);
            $parts[1][$index] = $segments;
            $number .= $digits[$segments];
        }

        return (int) $number;
    }
}
