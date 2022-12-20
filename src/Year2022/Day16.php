<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Year2022;

use Application\Common\Day;
use Application\Graph\Edge;
use Application\Graph\Graph;
use Application\Graph\ValveGraph;
use Application\Graph\ValveVertex;

class Day16 extends Day
{
    private function getCaveGraph(array $inputs): Graph
    {
        $pattern = '`Valve (?<valve>[A-Z]{2}) has flow rate=(?<rate>\d+); tunnels? leads? to valves? (?<neighbors>.+)`';
        $cave  = new Graph();

        $valves = [];
        foreach ($inputs as $input) {
            if (preg_match($pattern, $input, $matches)) {
                $name          = $matches['valve'];
                $neighborNames = explode(', ', $matches['neighbors']);

                $valves[$name] ??= new ValveVertex($name);
                $valves[$name]->setRate((int) $matches['rate']);

                foreach ($neighborNames as $neighborName) {
                    $valves[$neighborName] ??= new ValveVertex($neighborName);
                    $cave->edge(new Edge($valves[$name], $valves[$neighborName]));
                }
            }
        }

        return $cave;
    }

    /**
     * Build graph of each valve to open as vertex that have all others valves to open as neighbors and weight edge
     * between each is the shortest path length.
     */
    private function getValveGraph(Graph $cave): ValveGraph
    {
        //~ Get only list of valve to open
        /** @var ValveVertex[] $valvesToOpen */
        $valvesToOpen = array_filter($cave->getVertices(), fn (ValveVertex $vertex) => $vertex->getRate() > 0);

        //~ Start with origin for puzzle
        $origin       = $cave->getOrigin();
        $valvesToOpen = [(string) $origin => $origin] + $valvesToOpen;

        $valves     = [];
        foreach ($valvesToOpen as $vertex) {
            $valve = new ValveVertex((string) $vertex);
            $valve->setRate($vertex->getRate());
            $valves[] = $valve;
        }

        //~ Build new graph
        $valveGraph = new ValveGraph();

        //~ Add edge between each vertex with weight equals to the real min length between vertices
        foreach ($valves as $index => $valve) {
            for ($i = 0; $i < count($valves); $i++) {
                if ($i === $index) {
                    continue;
                }

                $neighbor = $valves[$i];
                $distance = $cave->shortestPath((string) $valve, (string) $neighbor)->count() - 1;
                $valveGraph->edge(new Edge($valve, $neighbor, $distance));
            }
        }

        return $valveGraph;
    }

    /**
     * 1809 too low
     * @param array $inputs
     * @return int
     */
    protected function starOne(array $inputs): int
    {
        $cave       = $this->getCaveGraph($inputs);
        //$valveGraph = $this->getValveGraph($cave);

        return $cave->mostPressure((string) $cave->getOrigin());
    }

    protected function starTwo(array $inputs): int
    {
        return 0;
    }
}
