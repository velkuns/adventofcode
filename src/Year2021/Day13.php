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
 * Class Day13
 *
 * @author Romain Cottard
 */
class Day13 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [17  => ['6,10', '0,14', '9,10', '0,3', '10,4', '4,11', '6,0', '6,12', '4,1', '0,13', '10,12', '3,4', '3,0', '8,4', '1,10', '2,14', '8,10', '9,0', '', 'fold along y=7', 'fold along x=5']],
            ],
            '**' => [
                [17  => ['6,10', '0,14', '9,10', '0,3', '10,4', '4,11', '6,0', '6,12', '4,1', '0,13', '10,12', '3,4', '3,0', '8,4', '1,10', '2,14', '8,10', '9,0', '', 'fold along y=7', 'fold along x=5']],
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
        $paper = $this->getPaper($inputs);
        $paper = $this->fold($paper, reset($paper['folds']));

        return count($paper['dots']);
    }

    private function starTwo(array $inputs): int
    {
        $paper = $this->getPaper($inputs);

        foreach ($paper['folds'] as $fold) {
            $paper = $this->fold($paper, $fold);
        }

        $this->displayPaper($paper);

        return 0;
    }

    private function fold(array $paper, string $instruction): array
    {
        $instruction = explode(' ', $instruction);
        $instruction = array_pop($instruction);

        [$axe, $number] = explode('=', $instruction);

        foreach ($paper['dots'] as $coordinates => $dot) {

            [$x, $y] = array_map('intval', explode('.', $coordinates));

            if (($axe === 'y' && $y <= $number) || ($axe === 'x' && $x <= $number)) {
                continue;
            }

            $x = $axe === 'x' ? $paper['x_max'] - $x : $x;
            $y = $axe === 'y' ? $paper['y_max'] - $y : $y;

            $paper['dots']["$x.$y"] = $dot;
            unset($paper['dots'][$coordinates]);
        }

        $paper['x_max'] = $axe === 'x' ? $number - 1 : $paper['x_max'];
        $paper['y_max'] = $axe === 'y' ? $number - 1 : $paper['y_max'];

        return $paper;
    }

    private function getPaper(array $inputs): array
    {
        $allX = $allY = $dots = [];

        foreach ($inputs as $index => $input) {
            if (empty($input)) {
                unset($inputs[$index]);
                break;
            }

            [$x, $y] = explode(',', $input);

            $allX[] = $x;
            $allY[] = $y;
            $dots["$x.$y"] = '#';

            unset($inputs[$index]);
        }

        return ['dots' => $dots, 'x_max' => max($allX), 'y_max' => max($allY), 'folds' => $inputs];
    }

    private function displayPaper(array $paper): void
    {
        for ($y = 0; $y <= $paper['y_max']; $y++) {
            for ($x = 0; $x <= $paper['x_max']; $x++) {
                echo $paper['dots']["$x.$y"] ?? '.';
            }
            echo PHP_EOL;
        }

        echo PHP_EOL . PHP_EOL;
    }

}
