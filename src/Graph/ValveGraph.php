<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Graph;

class ValveGraph extends Graph
{
    public function mostPressure(ValveVertex $origin): int
    {
        $queue  = new \SplQueue();
        $minute = 0;

        $pressures[(string) $origin] = 0;
        $visited[(string) $origin]   = true;

        foreach ($origin->neighbors() as $neighbor) {
            if (!isset($this->vertices[(string) $neighbor])) {
                continue;
            }
            $this->edges[(string) $origin][(string) $neighbor] = new Edge($origin, $neighbor);
        }

        $queue->push($origin);

        // Breadth-first search Algorithm
        while (!$queue->isEmpty() && $minute <= 30) {
            $vertex = $queue->pop();
            foreach ($this->edges[(string) $vertex] as $edge) {
                $neighbor = $edge->to();
                if (
                    $visited[(string) $neighbor] === true &&
                    ($pressures[(string) $neighbor] ?? 0) >= ($pressures[(string) $vertex] * $edge->weight())
                ) {
                    continue;
                }

                //~ Mark connected node a visited, set distance from origin & add predecessor
                $visited[(string) $neighbor]      = true;
                $pressures[(string) $neighbor]   += ($pressures[(string) $vertex] * $edge->weight());
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
}
