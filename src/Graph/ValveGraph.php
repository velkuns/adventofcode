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
    public function mostPressure(string $origin): int
    {
        $visited = [];
        foreach ($this->vertices as $vertex) {
            $visited[(string) $vertex] = false;
        }

        $visited[$origin] = true;

        [$path, $mostPressures] = $this->visit($this->vertices[$origin], $visited, 0, 0, 1, 0, $origin);
        //var_export([$path => $mostPressures]);
        return $mostPressures;
    }

    private function visit(
        ValveVertex $valve,
        array $visited,
        int $pressure,
        int $totalPressure,
        int $time,
        int $pathLength,
        string $parentPath
    ): array|int {
        $visited[(string) $valve] = true;

        //~ Increase time + release pressure during moves
        for ($l = 1; $l <= $pathLength; $l++) {
            $time++;
            $totalPressure += $pressure;

            if ($time > 30) {
                return [$parentPath, $totalPressure];
            }
        }

        //~ Increase time for valve open
        if ($valve->getRate()) {
            $time++;
            $totalPressure += $pressure;
        }

        $pressure += $valve->getRate(); // Open Valve

        if ($time > 30) {
            return [$parentPath, $totalPressure];
        }

        $pressures = [];
        foreach ($this->edges[(string) $valve] as $edge) {
            if ($visited[(string) $edge->to()] === true) {
                continue;
            }

            [$fullPath, $fullPressure] = $this->visit($edge->to(), $visited, $pressure, $totalPressure, $time, $edge->weight(), "$parentPath-{$edge->to()}");
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
