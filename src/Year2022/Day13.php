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

class Day13 extends Day
{
    private function compare(array|int $left, array|int $right): Ordered
    {
        if ($left === -1) { // Left ran out of items, so ordered
            return Ordered::Yes;
        }

        if ($right === -1) { // Rights ran out of items, so not ordered
            return Ordered::No;
        }

        if (is_int($left) && is_int($right)) {
            return Ordered::from($left <=> $right); // Spaceship comparison (1 if $right > $left, 0 if =, else -1)
        }

        $left  = is_int($left) ? [$left] : $left;
        $right = is_int($right) ? [$right] : $right;

        //~ Sub part of packet are array, so compare on each element in sub part
        for ($i = 0, $max = max(count($left), count($right)); $i < $max; $i++) {
            $compare = $this->compare($left[$i] ?? -1, $right[$i] ?? -1);
            if ($compare !== Ordered::Unknown) {
                return $compare;
            }
        }

        return Ordered::Unknown;
    }

    protected function starOne(array $inputs): int
    {
        //~ Get packets (by pairs)
        $packets = array_chunk(array_map('json_decode', $inputs), 2);

        //~ Filter on packets that Ordered (Ordered::Yes)
        $orderedPackets = array_filter($packets, fn (array $packet) => $this->compare(...$packet) === Ordered::Yes);

        //~ Get key, add 1 (in php, list start from 0), then sum keys
        return array_sum(array_map(fn(int $key) => $key + 1, array_keys($orderedPackets)));
    }

    protected function starTwo(array $inputs): int
    {
        $dividers = ['[[2]]', '[[6]]'];
        $packets  = array_map('json_decode', array_merge($inputs, $dividers)); // Packets + dividers
        usort($packets, fn($left, $right) => $this->compare($left, $right)->value); // Sort the packets

        //~ Filter on dividers
        $list = array_filter(array_map('json_encode', $packets), fn($packet) => in_array($packet, $dividers));

        //~ Get key, add 1 & multiply them together
        return array_product(array_map(fn(int $key) => $key + 1, array_keys($list)));
    }
}

enum Ordered: int
{
    case No = 1;
    case Yes = -1;
    case Unknown = 0;
}
