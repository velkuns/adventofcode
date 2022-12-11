<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Year2022;

use Application\Common\Day;

class Day11 extends Day
{
    private function getMonkeys(array $inputs, int $star): array
    {
        $part2   = [];
        $monkeys = [];
        foreach (array_chunk($inputs, 7) as $data) {
            //~ id & items
            sscanf($data[0], 'Monkey %d:', $id);
            $items = array_map('intval', explode(', ', substr(trim($data[1]), 16)));

            //~ Operation
            $operation = str_replace('old', '$level', substr(trim($data[2]), 17));
            eval("\$inspect = fn(int \$level) => (int) floor(($operation) / 3);");

            //~ Test
            sscanf(trim($data[3]), 'Test: divisible by %d', $test);
            sscanf(trim($data[4]), 'If true: throw to monkey %d', $true);
            sscanf(trim($data[5]), 'If false: throw to monkey %d', $false);
            eval("\$throwTo = fn(int \$level) => (\$level % $test) === 0 ? $true : $false;");

            $monkeys[$id] = [
                'items'       => $items,
                'inspect'     => $inspect,
                'throwTo'     => $throwTo,
                'inspected'   => 0,
            ];

            $part2[$id] = [
                'testDivisor' => $test,
                'operation'   => $operation,
            ];
        }

        //~ For part 2, override inspection with reducer operation
        if ($star === 2) {
            $worry = 1;
            foreach ($part2 as $id => $data) {
                $worry *= $data['testDivisor'];
            }
            foreach ($part2 as $id => $data) {
                $operation = $data['operation'];
                eval("\$inspect = fn(int \$level) => (int) floor(($operation) % $worry);");
                $monkeys[$id]['inspect'] = $inspect;
            }
        }

        return $monkeys;
    }

    protected function starOne(array $inputs): int
    {
        $monkeys    = $this->getMonkeys($inputs, 1);
        $inspected = array_fill(0, count($monkeys), 0);

        for ($round = 1; $round <= 20; $round++) {
            for ($id = 0, $maxId = count($monkeys); $id < $maxId; $id++) {
                $items = array_map($monkeys[$id]['inspect'], $monkeys[$id]['items']);
                foreach ($items as $level) {
                    $inspected[$id]++;
                    $throwTo = $monkeys[$id]['throwTo']($level);
                    $monkeys[$throwTo]['items'][] = $level;
                }
                $monkeys[$id]['items'] = [];
            }
        }

        rsort($inspected);
        return (int) array_product(array_slice($inspected, 0, 2));
    }

    protected function starTwo(array $inputs): int
    {
        $monkeys    = $this->getMonkeys($inputs, 2);
        $inspected = array_fill(0, count($monkeys), 0);

        for ($round = 1; $round <= 10_000; $round++) {
            for ($id = 0, $maxId = count($monkeys); $id < $maxId; $id++) {
                $items = array_map($monkeys[$id]['inspect'], $monkeys[$id]['items']);
                foreach ($items as $level) {
                    $inspected[$id]++;
                    $throwTo = $monkeys[$id]['throwTo']($level);
                    $monkeys[$throwTo]['items'][] = $level;
                }
                $monkeys[$id]['items'] = [];
            }
        }

        rsort($inspected);
        return (int) array_product(array_slice($inspected, 0, 2));
    }
}
