<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
--- Day 7: Handy Haversacks ---

You land at the regional airport in time for your next flight. In fact, it looks like you'll even have time to grab some food: all flights are currently delayed due to issues in luggage processing.

Due to recent aviation regulations, many rules (your puzzle input) are being enforced about bags and their contents; bags must be color-coded and must contain specific quantities of other color-coded bags. Apparently, nobody responsible for these regulations considered how long they would take to enforce!

For example, consider the following rules:

light red bags contain 1 bright white bag, 2 muted yellow bags.
dark orange bags contain 3 bright white bags, 4 muted yellow bags.
bright white bags contain 1 shiny gold bag.
muted yellow bags contain 2 shiny gold bags, 9 faded blue bags.
shiny gold bags contain 1 dark olive bag, 2 vibrant plum bags.
dark olive bags contain 3 faded blue bags, 4 dotted black bags.
vibrant plum bags contain 5 faded blue bags, 6 dotted black bags.
faded blue bags contain no other bags.
dotted black bags contain no other bags.

These rules specify the required contents for 9 bag types. In this example, every faded blue bag is empty, every vibrant plum bag contains 11 bags (5 faded blue and 6 dotted black), and so on.

You have a shiny gold bag. If you wanted to carry it in at least one other bag, how many different bag colors would be valid for the outermost bag? (In other words: how many colors can, eventually, contain at least one shiny gold bag?)

In the above rules, the following options would be available to you:

    A bright white bag, which can hold your shiny gold bag directly.
    A muted yellow bag, which can hold your shiny gold bag directly, plus some other bags.
    A dark orange bag, which can hold bright white and muted yellow bags, either of which could then hold your shiny gold bag.
    A light red bag, which can hold bright white and muted yellow bags, either of which could then hold your shiny gold bag.

So, in this example, the number of bag colors that can eventually contain at least one shiny gold bag is 4.

How many bag colors can eventually contain at least one shiny gold bag? (The list of rules is quite long; make sure you get all of it.)

 */
declare(strict_types=1);

namespace Application\Year2020;

use Application\Common\AlgorithmInterface;

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
                [
                    4 => [
                        'light red bags contain 1 bright white bag, 2 muted yellow bags.',
                        'dark orange bags contain 3 bright white bags, 4 muted yellow bags.',
                        'bright white bags contain 1 shiny gold bag.',
                        'muted yellow bags contain 2 shiny gold bags, 9 faded blue bags.',
                        'shiny gold bags contain 1 dark olive bag, 2 vibrant plum bags.',
                        'dark olive bags contain 3 faded blue bags, 4 dotted black bags.',
                        'vibrant plum bags contain 5 faded blue bags, 6 dotted black bags.',
                        'faded blue bags contain no other bags.',
                        'dotted black bags contain no other bags.',
                    ],
                ]
            ],
            '**' => [
                [
                    32 => [
                        'light red bags contain 1 bright white bag, 2 muted yellow bags.',
                        'dark orange bags contain 3 bright white bags, 4 muted yellow bags.',
                        'bright white bags contain 1 shiny gold bag.',
                        'muted yellow bags contain 2 shiny gold bags, 9 faded blue bags.',
                        'shiny gold bags contain 1 dark olive bag, 2 vibrant plum bags.',
                        'dark olive bags contain 3 faded blue bags, 4 dotted black bags.',
                        'vibrant plum bags contain 5 faded blue bags, 6 dotted black bags.',
                        'faded blue bags contain no other bags.',
                        'dotted black bags contain no other bags.',
                    ],
                    126 => [
                        'shiny gold bags contain 2 dark red bags.',
                        'dark red bags contain 2 dark orange bags.',
                        'dark orange bags contain 2 dark yellow bags.',
                        'dark yellow bags contain 2 dark green bags.',
                        'dark green bags contain 2 dark blue bags.',
                        'dark blue bags contain 2 dark violet bags.',
                        'dark violet bags contain no other bags.',
                    ],
                ]
            ],
        ];

        return $examples[$star];
    }

    /**
     * *  : Your puzzle answer was 348.
     * ** : Your puzzle answer was xxx.
     */
    public function solve(string $star, array $inputs): string
    {
        return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    private function starOne(array $inputs): int
    {
        $data = [];
        foreach ($inputs as $input) {
            [$color, $contains] = $this->parseInput($input);
            $data[$color] = $contains;
        }

        $count = 0;
        foreach ($data as $color => $contains) {
            $count += $this->resolve('shiny gold', $color, $contains, $data, 0);
        }

        return $count;
    }

    private function starTwo(array $inputs): int
    {
        $data = [];
        foreach ($inputs as $input) {
            [$color, $contains] = $this->parseInput($input);
            $data[$color] = $contains;
        }

        $count = 0;
        foreach ($data as $color => $contains) {
            $count += $this->resolve('shiny gold', $color, $contains, $data, 0);
        }

        return $count;
    }

    private function resolve(string $search, string $color, ?array $contains, array $data, int $depth): int
    {
        if ($search === $color || $contains === null) {
            return 0;
        }

        if (in_array($search, $contains)) {
            return 1;
        }

        $count = 0;
        foreach ($contains as $subColor) {
            $count += $this->resolve($search, $subColor, $data[$subColor], $data, $depth + 1);
        }

        return ($count >= 1 ? 1 : 0);
    }

    private function parseInput(string $input): array
    {
        [$color, $tmp] = explode(' bags contain ', $input);
        $output   = explode(', ', $tmp);
        $contains = [];
        foreach ($output as $item) {
            if ($item === 'no other bags.') {
                $contains = null;
                continue;
            }

            $tmp = explode(' ', $item);
            [$qty, $prefix, $suffix] = array_splice($tmp, 0, 3);
            $contains[] = ['color' => $prefix . ' ' . $suffix, 'qty' => $qty];
        }

        return [$color, $contains];
    }
}
