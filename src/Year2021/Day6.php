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
use Eureka\Component\Console\Progress\Progress;

/**
 * Class Day6
 *
 * @author Romain Cottard
 */
class Day6 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [5934 => ['3,4,3,1,2']],
            ],
            '**' => [
                [26984457539 => ['3,4,3,1,2']],
                //[26984457539 => ['3,4,3,1,2']],
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
        $lanternfish = str_replace(',', '', $inputs[0]);

        return $this->cycles($lanternfish, 80);
    }

    private function starTwo(array $inputs): int
    {
        $lanternfish = str_replace(',', '', $inputs[0]);

        return $this->cycles($lanternfish, 256);
    }

    private function cycles(string $lanternfish, int $nbDays): int
    {
        $nbCycles    = (int) ($nbDays / 7);
        $nbDaysLeft  = $nbDays % 7;
        $populations = [$lanternfish => 1];

        for ($cycle = 0; $cycle < $nbCycles; $cycle++) {
            $populations = $this->cycle($populations, 7);
        }

        if ($nbDaysLeft > 0) {
            $populations = $this->cycle($populations, $nbDaysLeft);
        }

        $total = 0;
        foreach ($populations as $population => $count) {
            $total += (strlen((string) $population) * $count);
        }

        return $total;
    }

    private function cycle(array $populations, $nbDays): array
    {
        $newPopulations = [];
        foreach ($populations as $population => $count) {
            $population     = (string) $population;
            $newPopulation  = '';
            for ($day = 1; $day <= $nbDays; $day++) {
                $this->newDay($population, $newPopulation);
            }

            $newPopulations[$population] = ($newPopulations[$population] ?? 0) + $count;
            if ($newPopulation !== '') {
                $newPopulations[$newPopulation] = ($newPopulations[$newPopulation] ?? 0) + $count;
            }
        }

        return $newPopulations;
    }

    private function newDay(string &$population, string &$newPopulation): void
    {
        //~ Change new Population
        for ($i = 0, $max = strlen($newPopulation); $i < $max; $i++) {
            $timer = (int) $newPopulation[$i];

            $newPopulation[$i] = (string) ($timer === 0 ? 6 : $timer - 1);
        }

        //~ After, change current population
        for ($i = 0, $max = strlen($population); $i < $max; $i++) {
            $timer = (int) $population[$i];

            $newPopulation .= ($timer === 0 ? '8' : '');
            $population[$i] = (string) ($timer === 0 ? 6 : $timer - 1);
        }
    }
}
