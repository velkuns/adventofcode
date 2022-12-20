<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Graph;

class Graph
{
    /** @var Edge[][] $edges */
    protected array $edges = [];

    /** @var Vertex[] $vertices */
    protected array $vertices = [];

    public function edge(Edge $edge): static
    {
        $this->edges[(string) $edge->from()][(string) $edge->to()] ??= $edge;
        $this->edges[(string) $edge->to()][(string) $edge->from()] ??= $edge->invert();

        $this->vertices[(string) $edge->from()] ??= $edge->from();
        $this->vertices[(string) $edge->to()]   ??= $edge->to();

        return $this;
    }

    public function getOrigin(): Vertex
    {
        return reset($this->vertices);
    }

    public function getVertices(): array
    {
        return $this->vertices;
    }

    public function shortestPath(string $origin, string $destination): \SplStack
    {
        $predecessors = [];
        $distances    = [];

        $origin      = $this->vertices[$origin];
        $destination = $this->vertices[$destination];
        $bfs = $this->bfs($origin, $destination, $predecessors, $distances);

        if ($bfs === false) {
            throw new \RuntimeException('No connection between nodes');
        }

        // LinkedList to store path
        $path    = new \SplStack();
        $current = $destination;
        $path->push($current);

        while ($predecessors[(string) $current] !== null) {
            $path->push($predecessors[(string) $current]);
            $current = $predecessors[(string) $current];
        }

        return $path;
    }

    /**
     * Breadth-first Search modified algorithm.
     *
     * @param Vertex[] $predecessors
     * @param int[] $distances
     */
    private function bfs(Vertex $origin, Vertex $destination, array &$predecessors, array &$distances): bool
    {
        $queue = new \SplQueue();

        //~ Initialize array: visited vertices are all false, no predecessor & max int as distance
        foreach ($this->vertices as $vertex) {
            $visited[(string) $vertex]      = false;
            $predecessors[(string) $vertex] = null;
            $distances[(string) $vertex]    = PHP_INT_MAX;
        }

        //~ Start with origin
        $visited[(string) $origin]   = true;
        $distances[(string) $origin] = 0;
        $queue->push($origin);

        // Breadth-first search Algorithm
        while (!$queue->isEmpty()) {
            $vertex = $queue->pop();
            foreach ($this->edges[(string) $vertex] as $edge) {
                $neighbor = $edge->to();
                if (
                    $visited[(string) $neighbor] === true &&
                    $distances[(string) $neighbor] <= $distances[(string) $vertex] + $edge->weight()
                ) {
                    continue;
                }

                //~ Mark connected node a visited, set distance from origin & add predecessor
                $visited[(string) $neighbor]      = true;
                $distances[(string) $neighbor]    = $distances[(string) $vertex] + $edge->weight();
                $predecessors[(string) $neighbor] = $vertex;

                //~ Enqueue connected node
                $queue->push($neighbor);

                //~ Destination reach, so stop
                if ((string) $neighbor == (string) $destination) {
                    return true;
                }
            }
        }

        return false;
    }
    public function mostPressure(string $origin): int
    {
        $visited = [];
        foreach ($this->vertices as $vertex) {
            $visited[(string) $vertex] = false;
        }

        $visited[$origin] = true;

        [$path, $mostPressures] = $this->visit($this->vertices[$origin], $visited, 0, 0, 1, $origin);
        //var_export([$path => $mostPressures]);
        return $mostPressures;
    }

    private function visit(
        ValveVertex $valve,
        array $visited,
        int $pressure,
        int $totalPressure,
        int $time,
        string $parentPath
    ): array|int {
        $visited[(string) $valve] = true;

        $time++;

        if ($time > 30) {
            return [$parentPath, $totalPressure];
        }

        $totalPressure += $pressure;

        //~ Increase time for valve open
        if ($valve->getRate()) {
            $time++;
        }

        if ($time > 30) {
            return [$parentPath, $totalPressure];
        }

        $totalPressure += $pressure;
        $pressure += $valve->getRate(); // Open Valve

        $pressures = [];
        foreach ($this->edges[(string) $valve] as $edge) {
            if ($visited[(string) $edge->to()] === true) {
                continue;
            }

            [$fullPath, $fullPressure] = $this->visit($edge->to(), $visited, $pressure, $totalPressure, $time, "$parentPath-{$edge->to()}");
            $pressures[$fullPath]      = $fullPressure;
        }

        if (empty($pressures)) {
            for ($i = $time; $i <= 30; $i++) {
                $totalPressure += $pressure;
            }
            return [$parentPath, $totalPressure];
        }

        $mostPressure = max($pressures);
        $path         = array_search($mostPressure, $pressures);

        return [$path, $mostPressure];
    }
}
