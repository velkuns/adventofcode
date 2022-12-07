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

class Day7 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [95437 => [
                    '$ cd /',
                    '$ ls',
                    'dir a',
                    '14848514 b.txt',
                    '8504156 c.dat',
                    'dir d',
                    '$ cd a',
                    '$ ls',
                    'dir e',
                    '29116 f',
                    '2557 g',
                    '62596 h.lst',
                    '$ cd e',
                    '$ ls',
                    '584 i',
                    '$ cd ..',
                    '$ cd ..',
                    '$ cd d',
                    '$ ls',
                    '4060174 j',
                    '8033020 d.log',
                    '5626152 d.ext',
                    '7214296 k',
                ]]
            ],
            '**' => [
                [24933642 => [
                    '$ cd /',
                    '$ ls',
                    'dir a',
                    '14848514 b.txt',
                    '8504156 c.dat',
                    'dir d',
                    '$ cd a',
                    '$ ls',
                    'dir e',
                    '29116 f',
                    '2557 g',
                    '62596 h.lst',
                    '$ cd e',
                    '$ ls',
                    '584 i',
                    '$ cd ..',
                    '$ cd ..',
                    '$ cd d',
                    '$ ls',
                    '4060174 j',
                    '8033020 d.log',
                    '5626152 d.ext',
                    '7214296 k',
                ]]
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs, bool $isFunctional = false): string
    {
        return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    private function list(array $tree, string $input): array
    {
        static $current;
        if (empty($tree)) {
            $current = '';
        }

        if (str_starts_with($input, '$ cd')) {
            $dir = substr($input, 5);
            $sep = ($current !== '/' && $current !== '' ? '/' : '');
            if ($dir === '..') {
                $current = dirname($current);
            } else {
                $current .= $sep . $dir;
                $tree[$current] ??= 0;
            }
        } elseif (str_starts_with($input, 'dir') || str_starts_with($input, '$ ls')) {
            //~ Nothing special to do
        } else {
            sscanf($input, '%d %s', $size, $name);
            $tree[$current] += $size;
        }

        return $tree;
    }

    private function addSizeUp(array $tree): array
    {
        //~ Current works well because input are ordered correctly
        foreach ($tree as $path => $size) {
            while ($path !== '/') {
                $path = dirname($path);
                $tree[$path] += $size;
            }
        }

        return $tree;
    }

    private function starOne(array $inputs): int
    {
        $tree = $this->addSizeUp(array_reduce($inputs, $this->list(...), []));

        return array_sum(array_filter($tree, fn(int $size) => $size < 100_000));
    }

    private function starTwo(array $inputs): int
    {
        $tree = $this->addSizeUp(array_reduce($inputs, $this->list(...), []));
        $totalNeed = abs((70_000_000 - $tree['/']) - 30_000_000);
        asort($tree);
        $tree = array_filter($tree, fn(int $size) => $size > $totalNeed);

        return array_shift($tree);
    }
}
