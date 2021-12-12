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
 * Class Day12
 *
 * @author Romain Cottard
 */
class Day12 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [10  => ['start-A', 'start-b', 'A-c', 'A-b', 'b-d', 'A-end', 'b-end']],
                [19  => ['dc-end', 'HN-start', 'start-kj', 'dc-start', 'dc-HN', 'LN-dc', 'HN-end', 'kj-sa', 'kj-HN', 'kj-dc']],
                [226 => ['fs-end', 'he-DX', 'fs-he', 'start-DX', 'pj-DX', 'end-zg', 'zg-sl', 'zg-pj', 'pj-he', 'RW-he', 'fs-DX', 'pj-RW', 'zg-RW', 'start-pj', 'he-WI', 'zg-he', 'pj-fs', 'start-RW']],
            ],
            '**' => [
                [36   => ['start-A', 'start-b', 'A-c', 'A-b', 'b-d', 'A-end', 'b-end']],
                [103  => ['dc-end', 'HN-start', 'start-kj', 'dc-start', 'dc-HN', 'LN-dc', 'HN-end', 'kj-sa', 'kj-HN', 'kj-dc']],
                [3509 => ['fs-end', 'he-DX', 'fs-he', 'start-DX', 'pj-DX', 'end-zg', 'zg-sl', 'zg-pj', 'pj-he', 'RW-he', 'fs-DX', 'pj-RW', 'zg-RW', 'start-pj', 'he-WI', 'zg-he', 'pj-fs', 'start-RW']],
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
        $graph = $this->getGraph($inputs);

        return $this->countPaths($graph, 'start', false);
    }

    private function starTwo(array $inputs): int
    {
        $graph = $this->getGraph($inputs);

        return $this->countPaths($graph, 'start', true);
    }

    private function getGraph(array $inputs): array
    {
        $graph = [];
        foreach ($inputs as $input) {
            [$from, $to] = explode('-', $input);

            if (!isset($graph[$from])) {
                $graph[$from] = ['visited' => 0, 'is_small' => (bool) preg_match('`[a-z]+`', $from), 'nodes' => []];
            }

            if (!isset($graph[$to])) {
                $graph[$to] = ['visited' => 0, 'is_small' => (bool) preg_match('`[a-z]+`', $to), 'nodes' => []];
            }

            $graph[$from]['nodes'][] = $to;
            $graph[$to]['nodes'][]   = $from;
        }

        return $graph;
    }

    private function countPaths(array $graph, string $nodeName, bool $haveTimeToVisitASmallCaveTwice = true): int
    {
        $graph[$nodeName]['visited']++;

        if ($graph[$nodeName]['is_small'] && $graph[$nodeName]['visited'] > 1) {
            $haveTimeToVisitASmallCaveTwice = false;
        }

        if ($nodeName === 'end') {
            return 1;
        }

        $count = 0;
        foreach ($graph[$nodeName]['nodes'] as $connectedNodeName) {
            if ($connectedNodeName === 'start') {
                continue;
            }

            $node = $graph[$connectedNodeName];

            if ($node['is_small'] && (($node['visited'] === 1 && $haveTimeToVisitASmallCaveTwice === false) || $node['visited'] === 2)) {
                continue;
            }

            $count += $this->countPaths($graph, $connectedNodeName, $haveTimeToVisitASmallCaveTwice);
        }

        return $count;
    }
}
