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
 * Class Day21
 *
 * @author Romain Cottard
 */
class Day21 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [739785 => [
                    'Player 1 starting position: 4',
                    'Player 2 starting position: 8',
                ]],
            ],
            '**' => [
                [444356092776315 => [
                    'Player 1 starting position: 4',
                    'Player 2 starting position: 8',
                ]],
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs, bool $functionalMode = false): string
    {
        $players = [];

        $array     = explode(' ', $inputs[0]);
        $players[1] = ['pos' => (int) array_pop($array), 'score' => 0, 'win' => 0, 'depth' => 0];

        $array     = explode(' ', $inputs[1]);
        $players[2] = ['pos' => (int) array_pop($array), 'score' => 0, 'win' => 0, 'depth' => 0];

        return (string) ($star === '*' ? $this->starOne($players) : $this->starTwo($players));
    }

    private function starOne(array $players): int
    {
        $dice = 0;

        while (true) {
            foreach ([1, 2] as $player) {
                $move = ($dice * 3) + 6;
                $pos  = ($players[$player]['pos'] + $move) % 10;

                $players[$player]['pos']   = $pos === 0 ? 10 : $pos;
                $players[$player]['score'] += $players[$player]['pos'];

                $dice += 3;

                if ($players[$player]['score'] >= 1000) {
                    break 2;
                }
            }
        }

        $looserScore = min($players[1]['score'], $players[2]['score']);

        return $dice * $looserScore;
    }

    private function starTwo(array $players): int
    {
        $wins = [1 => 0, 2 => 0];
        $this->rollDices($players, $wins, 1, $this->moves(), 1);

        echo 'player #1: ' . $wins[1] . PHP_EOL;
        echo 'player #2: ' . $wins[2] . PHP_EOL;

        return max($wins);
    }

    private function rollDices(array $players, array &$wins, int $player, array $moves, int $universes): void
    {
        foreach ($moves as $move => $number) {
            $this->splitUniverse($players, $wins, $player, $moves, $universes * $number, $move);
        }
    }

    private function splitUniverse(array $players, array &$wins, int $player, array $moves, int $universes, int $move): void
    {
        $pos  = ($players[$player]['pos'] + $move) % 10;

        $players[$player]['pos']   = $pos === 0 ? 10 : $pos;
        $players[$player]['score'] += $players[$player]['pos'];

        if ($players[$player]['score'] >= 21) {
            $wins[$player] += $universes;

            return;
        }

        $player = $player === 1 ? 2 : 1;

        $this->rollDices($players, $wins, $player, $moves, $universes);
    }

    /**
     * Pre-calculate moves results of quantum dices to limit the number of iterations
     *
     * @return array
     */
    private function moves(): array
    {
        $moves = [];
        for ($d1 = 1; $d1 < 4; $d1++) {
            for ($d2 = 1; $d2 < 4; $d2++) {
                for ($d3 = 1; $d3 < 4; $d3++) {
                    $move = $d1 + $d2 + $d3;
                    $moves[$move] = ($moves[$move] ?? 0) + 1;
                }
            }
        }

        return $moves;
    }
}
