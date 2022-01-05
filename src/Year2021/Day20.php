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
 * Class Day20
 *
 * @author Romain Cottard
 */
class Day20 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [35 => [
                    '..#.#..#####.#.#.#.###.##.....###.##.#..###.####..#####..#....#..#..##..###..######.###...####..#..#####..##..#.#####...##.#.#..#.##..#.#......#.###.######.###.####...#.##.##..#..#..#####.....#.#....###..#.##......#.....#..#..#..##..#...##.######.####.####.#.#...#.......#..#.#.#...####.##.#......#..#...##.#.##..#...##.#.##..###.#......#.#.......#.#.#.####.###.##...#.....####.#..#..#.##.#....##..#.####....##...##..#...#......#.#.......#.......##..####..#...#.#.#...##..#.#..###..#####........#..####......#..#',
                    '','#..#.','#....','##..#','..#..','..###',
                ]],
            ],
            '**' => [
                [35 => [
                    '..#.#..#####.#.#.#.###.##.....###.##.#..###.####..#####..#....#..#..##..###..######.###...####..#..#####..##..#.#####...##.#.#..#.##..#.#......#.###.######.###.####...#.##.##..#..#..#####.....#.#....###..#.##......#.....#..#..#..##..#...##.######.####.####.#.#...#.......#..#.#.#...####.##.#......#..#...##.#.##..#...##.#.##..###.#......#.#.......#.#.#.####.###.##...#.....####.#..#..#.##.#....##..#.####....##...##..#...#......#.#.......#.......##..####..#...#.#.#...##..#.#..###..#####........#..####......#..#',
                    '','#..#.','#....','##..#','..#..','..###',
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
        $algo = array_shift($inputs);
        array_shift($inputs);

        $image  = array_map(fn(string $input) => str_split($input), $inputs);

        $image = $this->enhance($image, $algo, 1);
        $image = $this->enhance($image, $algo, 2);
        $this->display($image);

        $output = array_map(fn($line) => count(array_filter($line, fn($char) => $char === '#')), $image);

        return array_sum($output);
    }

    private function starTwo(array $inputs): int
    {
        $algo = array_shift($inputs);
        array_shift($inputs);

        $image  = array_map(fn(string $input) => str_split($input), $inputs);

        for ($i = 1; $i <= 50; $i++) {
            $image = $this->enhance($image, $algo, $i);
        }

        $output = array_map(fn($line) => count(array_filter($line, fn($char) => $char === '#')), $image);

        return array_sum($output);
    }

    private function enhance(array $image, string $algo, int $iteration): array
    {
        $backgroundChar = $algo[0] === '#' && $iteration % 2 === 0 ? '#' : '.';

        $output = [];

        $image = array_values(array_map(fn($line) => array_values($line), $image));

        $width  = count($image[0]) + 1;
        $height = count($image) + 1;
        for ($h = -1; $h < $height; $h++) {
            for ($w = -1; $w < $width; $w++) {
                $output[$h][$w] = $this->getPixel($image, $h, $w, $algo, $backgroundChar);
            }
        }

        return $output;
    }

    private function getPixel(array $image, int $h, int $w, string $algo, string $backgroundChar = '.'): string
    {
        $bin = '';
        for ($y = $h - 1; $y <= $h + 1; $y++) {
            for ($x = $w - 1; $x <= $w + 1; $x++) {
                $pixel = $image[$y][$x] ?? $backgroundChar;
                $bin  .= $pixel === '#' ? '1' : '0';
            }
        }

        return $algo[bindec($bin)];
    }

    private function display(array $image): void
    {
        foreach ($image as $line) {
            echo implode('', $line) . PHP_EOL;
        }
    }
}
