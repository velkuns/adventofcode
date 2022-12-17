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
use Application\Trigonometry\DirectionalVector;
use Application\Trigonometry\Point;
use Application\Trigonometry\Vector;
use Eureka\Component\Console\Argument\Argument;
use Eureka\Component\Console\IO\Out;

class Day9 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [13 => ['R 4', 'U 4', 'L 3', 'D 1',  'R 4',  'D 1',  'L 5',  'R 2']],
            ],
            '**' => [
                [1   => ['R 4', 'U 4', 'L 3', 'D 1',  'R 4',  'D 1',  'L 5',  'R 2']],
                [36  => ['R 5', 'U 8', 'L 8', 'D 3',  'R 17',  'D 10',  'L 25',  'U 20']],
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs): string
    {
        if (Argument::getInstance()->has('draw')) {
            return (string) ($star === '*' ? $this->starOneWithDraw($inputs) : $this->starTwoWithDraw($inputs));
        }

        return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    private function getVector(string $direction): DirectionalVector
    {
        static $vectors = [
            'U' => new DirectionalVector(new Point(0, 0), new Point(0, 1)),
            'D' => new DirectionalVector(new Point(0, 0), new Point(0, -1)),
            'L' => new DirectionalVector(new Point(0, 0), new Point(-1, 0)),
            'R' => new DirectionalVector(new Point(0, 0), new Point(1, 0)),
        ];

        return $vectors[$direction];
    }

    private function moveTail(Point $head, Point $tail): Point
    {
        if ((new Vector($tail, $head))->size() < 2) {
            return $tail;
        }

        return $tail->translate(new DirectionalVector($tail, $head));
    }

    private function starOne(array $inputs): int
    {
        $head = new Point(0, 0);
        $tail = new Point(0, 0);

        $positions = [$tail->getCoordinates() => 1];

        foreach ($inputs as $move) {
            sscanf($move, '%s %d', $direction, $steps);

            for ($step = 1; $step <= $steps; $step++) {
                $head = $head->translate($this->getVector($direction));
                $tail = $this->moveTail($head, $tail);
                $positions[$tail->getCoordinates()] = 1;
            }
        }

        return count($positions);
    }

    private function starTwo(array $inputs): int
    {
        $knots = array_fill(0, 10, new Point(0, 0));

        $positions = [$knots[9]->getCoordinates() => 1];

        $this->draw($knots, '', 0, 0);

        foreach ($inputs as $move) {
            sscanf($move, '%s %d', $direction, $steps);
            for ($step = 1; $step <= $steps; $step++) {
                $knots[0] = $knots[0]->translate($this->getVector($direction));
                $this->moveTail($knots[0], $knots[1]);

                for ($k = 1; $k <= 9; $k++) {
                    $knots[$k] = $this->moveTail($knots[$k - 1], $knots[$k]);
                }
                $positions[$knots[9]->getCoordinates()] = 1;
            }
        }

        return count($positions);
    }

    private function starOneWithDraw(array $inputs): int
    {
        $head = new Point(0, 0);
        $tail = new Point(0, 0);

        $positions = [$tail->getCoordinates() => 1];

        $this->draw(['H' => $head, 'T' => $tail], '', 0, 0);
        foreach ($inputs as $instructionNumber => $move) {
            sscanf($move, '%s %d', $direction, $steps);

            for ($step = 1; $step <= $steps; $step++) {
                $head = $head->translate($this->getVector($direction));
                $this->draw(['H' => $head, 'T' => $tail], $direction, $step, $instructionNumber);
                $tail = $this->moveTail($head, $tail);
                $this->draw(['H' => $head, 'T' => $tail], $direction, $step, $instructionNumber);
                $positions[$tail->getCoordinates()] = 1;

                $this->draw(['H' => $head, 'T' => $tail], $direction, $step, $instructionNumber);
            }
        }

        return count($positions);
    }

    private function starTwoWithDraw(array $inputs): int
    {
        $knots = array_fill(0, 10, new Point(0, 0));

        $positions = [$knots[9]->getCoordinates() => 1];

        $this->draw($knots, '', 0, 0);

        foreach ($inputs as $instructionNumber => $move) {
            sscanf($move, '%s %d', $direction, $steps);
            for ($step = 1; $step <= $steps; $step++) {
                $knots[0] = $knots[0]->translate($this->getVector($direction));
                $this->moveTail($knots[0], $knots[1]);

                for ($k = 1; $k <= 9; $k++) {
                    $knots[$k] = $this->moveTail($knots[$k - 1], $knots[$k]);
                }
                $positions[$knots[9]->getCoordinates()] = 1;

                $this->draw($knots, $direction, $step, $instructionNumber);
            }
        }

        return count($positions);
    }

    private function draw(array $knots, string $direction, int $step, int $instructionNumber): void
    {
        if (!Argument::getInstance()->has('draw')) {
            return;
        }

        Out::clear();
        Out::std("#$instructionNumber - $direction $step");

        $yMin = -5;
        $yMax = 15;
        $xMin = -11;
        $xMax = 15;
        $z      = 0;
        $output = '';

        $knots = array_reverse($knots, true);

        for ($y = $yMax; $y >= $yMin; $y--) {
            for ($x = $xMin; $x <= $xMax; $x++) {
                $mark = null;
                foreach ($knots as $k => $knot) {
                    if ($knot->getCoordinates() === "$x,$y,$z") {
                        $mark = $k === 0 ? 'H' : (string) $k;
                    }
                }
                $output .= ' ' . ($mark ?? '.');
            }
            $output .= "\n";
        }
        Out::std($output);
        if (Argument::getInstance()->has('draw-by-step')) {
            //~ Wait for input before continue process
            fscanf(STDIN, '%s', $input);
        } else {
            usleep(100_000);
        }
    }
}
