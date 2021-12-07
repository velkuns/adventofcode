<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
--- Day 2: Dive! ---

Now, you need to figure out how to pilot this thing.

It seems like the submarine can take a series of commands like forward 1, down 2, or up 3:

    forward X increases the horizontal position by X units.
    down X increases the depth by X units.
    up X decreases the depth by X units.

Note that since you're on a submarine, down and up affect your depth, and so they have the opposite result of what you might expect.

The submarine seems to already have a planned course (your puzzle input). You should probably figure out where it's going. For example:

forward 5
down 5
forward 8
up 3
down 8
forward 2

Your horizontal position and depth both start at 0. The steps above would then modify them as follows:

    forward 5 adds 5 to your horizontal position, a total of 5.
    down 5 adds 5 to your depth, resulting in a value of 5.
    forward 8 adds 8 to your horizontal position, a total of 13.
    up 3 decreases your depth by 3, resulting in a value of 2.
    down 8 adds 8 to your depth, resulting in a value of 10.
    forward 2 adds 2 to your horizontal position, a total of 15.

After following these instructions, you would have a horizontal position of 15 and a depth of 10. (Multiplying these together produces 150.)

Calculate the horizontal position and depth you would have after following the planned course. What do you get if you multiply your final horizontal position by your final depth?

--- Part Two ---

Based on your calculations, the planned course doesn't seem to make any sense. You find the submarine manual and discover that the process is actually slightly more complicated.

In addition to horizontal position and depth, you'll also need to track a third value, aim, which also starts at 0. The commands also mean something entirely different than you first thought:

    down X increases your aim by X units.
    up X decreases your aim by X units.
    forward X does two things:
        It increases your horizontal position by X units.
        It increases your depth by your aim multiplied by X.

Again note that since you're on a submarine, down and up do the opposite of what you might expect: "down" means aiming in the positive direction.

Now, the above example does something different:

    forward 5 adds 5 to your horizontal position, a total of 5. Because your aim is 0, your depth does not change.
    down 5 adds 5 to your aim, resulting in a value of 5.
    forward 8 adds 8 to your horizontal position, a total of 13. Because your aim is 5, your depth increases by 8*5=40.
    up 3 decreases your aim by 3, resulting in a value of 2.
    down 8 adds 8 to your aim, resulting in a value of 10.
    forward 2 adds 2 to your horizontal position, a total of 15. Because your aim is 10, your depth increases by 2*10=20 to a total of 60.

After following these new instructions, you would have a horizontal position of 15 and a depth of 60. (Multiplying these produces 900.)

Using this new interpretation of the commands, calculate the horizontal position and depth you would have after following the planned course. What do you get if you multiply your final horizontal position by your final depth?

 */

declare(strict_types=1);

namespace Application\Year2021;

use Application\Common\AlgorithmInterface;
use Application\Pipeline\Pipeline;

/**
 * Class Day2
 *
 * @author Romain Cottard
 */
class Day2 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [150 => ['forward 5', 'down 5', 'forward 8', 'up 3', 'down 8', 'forward 2']]
            ],
            '**' => [
                [900 => ['forward 5', 'down 5', 'forward 8', 'up 3', 'down 8', 'forward 2']]
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs, bool $functionalMode = false): string
    {
        if (!$functionalMode) {
            return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
        } else {
            return (string) ($star === '*' ? $this->starOneFunctional($inputs) : $this->starTwoFunctional($inputs));
        }
    }

    private function starOne(array $inputs): int
    {
        $pos = ['forward' => 0, 'up' => 0, 'down' => 0];
        foreach ($inputs as $instruction) {
            [$cmd, $qty] = explode(' ', $instruction);
            $pos[$cmd] += $qty;
        }

        return ($pos['down'] - $pos['up']) * $pos['forward'];
    }

    private function starOneFunctional(array $inputs): int
    {
        return (int) (new Pipeline())
            ->array($inputs)
                ->map(fn($value) => explode(' ', $value))
                ->store('a')
                ->filter(fn($data) => $data[0] === 'forward')
                ->map(fn($data) => (int) $data[1])
                ->sum()
            //->int()
                ->store('forward')
            ->restore('a')
            //->array()
                ->filter(fn($data) => $data[0] === 'up')
                ->map(fn($data) => (int) $data[1])
                ->sum()
            //->int()
                ->negate()
                ->store('up')
            ->restore('a')
            //->array()
                ->filter(fn($data) => $data[0] === 'down')
                ->map(fn($data) => (int) $data[1])
                ->sum()
                ->store('down')
            ->retrieve('up', 'down')
            //->array()
                ->sum()
                ->store('depth')
            ->retrieve('forward', 'depth')
            //->array()
                ->product()
            ->get();
    }

    private function starTwo(array $inputs): int
    {
        $pos   = 0;
        $depth = 0;
        $aim   = 0;
        foreach ($inputs as $instruction) {
            [$cmd, $qty] = explode(' ', $instruction);
            if ($cmd === 'forward') {
                $pos   += $qty;
                $depth += $qty * $aim;
            } else {
                $aim += ($cmd === 'up' ? -$qty : $qty);
            }
        }

        return $pos * $depth;
    }

    private function starTwoFunctional(array $inputs): int
    {
        $reducer = function($output, $data) {
            if ($data[0] !== 'forward') {
                $output['aim'] += ($data[0] === 'up' ? -$data[1] : $data[1]);
            } else {
                $output['pos']   += $data[1];
                $output['depth'] += $data[1] * $output['aim'];
            }

            return $output;
        };

        return (int) (new Pipeline())
            ->array($inputs)
                ->map(fn($value) => explode(' ', $value))
                ->reduce($reducer, ['pos' => 0, 'depth' => 0, 'aim' => 0])
                ->slice(0, 2)
                ->product()
            //->int()
            ->get();
    }
}
