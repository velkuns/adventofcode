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

class Day8 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [21 => ['30373', '25512', '65332', '33549', '35390']]
            ],
            '**' => [
                [8 => ['30373', '25512', '65332', '33549', '35390']]
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs): string
    {
        return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    /**
     * @param string[][] $forest
     * @param int $posRow
     * @param int $posColumn
     * @return array{left: string[], right: string[], top: string[], bottom: string[]}
     */
    private function getViews(array $forest, int $posRow, int $posColumn): array
    {
        $row = $forest[$posRow];
        $col = array_column($forest, $posColumn);

        return [
            'left'   => array_slice($row, 0, $posColumn),
            'right'  => array_slice($row, $posColumn + 1),
            'top'    => array_slice($col, 0, $posRow),
            'bottom' => array_slice($col, $posRow + 1),
        ];
    }

    /**
     * @param int $tree
     * @param string[] $left
     * @param string[] $right
     * @param string[] $top
     * @param string[] $bottom
     * @return bool
     */
    private function isVisible(int $tree, array $left, array $right, array $top, array $bottom): bool
    {
        return (max($left) < $tree || max($right) < $tree || max($top) < $tree || max($bottom) < $tree);
    }

    private function starOne(array $inputs): int
    {
        $forest = array_map(str_split(...), $inputs);

        $nbTreeVisible = ((count($forest) - 2) * 2) + ((count($forest[0]) - 2) * 2) + 4;

        for ($r = 1, $maxRow = count($forest) - 1; $r < $maxRow; $r++) {
            for ($c = 1, $maxCol = count($forest[0]) - 1; $c < $maxCol; $c++) {
                $tree  = (int) $forest[$r][$c];
                $views = $this->getViews($forest, $r, $c);
                $nbTreeVisible += $this->isVisible($tree, ...$views) ? 1 : 0;
            }
        }

        return $nbTreeVisible;
    }

    /**
     * @param array{left: string[], right: string[], top: string[], bottom: string[]} $views
     * @param int $referenceTree
     * @return int
     */
    private function scenicScore(array $views, int $referenceTree): int
    {
        $viewsScore = [];
        foreach ($views as $view => $rangeOfTrees) {
            if ($view === 'left' || $view === 'top') {
                $rangeOfTrees = array_reverse($rangeOfTrees);
            }

            $viewsScore[$view] = 0;
            foreach ($rangeOfTrees as $currentTree) {
                $viewsScore[$view]++;
                if ((int) $currentTree >= $referenceTree) {
                    continue 2;
                }
            }
        }

        return array_product($viewsScore);
    }

    private function starTwo(array $inputs): int
    {
        $forest = array_map(str_split(...), $inputs);

        $bestScenicScore = 0;

        for ($r = 1, $maxRow = count($forest) - 1; $r < $maxRow; $r++) {
            for ($c = 1, $maxCol = count($forest[0]) - 1; $c < $maxCol; $c++) {
                $tree  = (int) $forest[$r][$c];
                $views = $this->getViews($forest, $r, $c);

                if (!$this->isVisible($tree, ...$views)) {
                    continue;
                }

                $scenicScore = $this->scenicScore($views, $tree);

                if ($scenicScore > $bestScenicScore) {
                    $bestScenicScore = $scenicScore;
                }
            }
        }

        return $bestScenicScore;
    }
}
