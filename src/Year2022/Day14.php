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
use Application\Trigonometry\Line2D;
use Application\Trigonometry\Matrix;
use Application\Trigonometry\DirectionalVector;
use Application\Trigonometry\Point2D;

class Day14 extends Day
{
    private const ROCK = '#';
    private const AIR  = '.';
    private const SAND = 'o';
    private const ORIGIN = '+';

    private function getMatrix(array $inputs): Matrix
    {
        $inputs = array_map(fn(string $line) => explode(' -> ', $line), $inputs);
        $lines  = [];
        $minX   = PHP_INT_MAX;
        $maxX   = PHP_INT_MIN;
        $minY   = PHP_INT_MAX;
        $maxY   = PHP_INT_MIN;

        foreach ($inputs as $input) {
            for ($i = 0, $max = count($input) - 1; $i < $max; $i++) {
                $origin      = new Point2D(...array_map('intval', explode(',', $input[$i])));
                $destination = new Point2D(...array_map('intval', explode(',', $input[$i + 1])));

                $minX = min($origin->getX(), $destination->getX(), $minX);
                $maxX = max($origin->getX(), $destination->getX(), $maxX);
                $minY = min($origin->getY(), $destination->getY(), $minY);
                $maxY = max($origin->getY(), $destination->getY(), $maxY);
                $lines[] = new Line2D($origin, $destination);
            }
        }

        $matrix = Matrix::fromCoordinates(
            new Point2D($minX, 0),
            new Point2D($maxX, $maxY),
            self::AIR
        );

        foreach ($lines as $line) {
            foreach ($line as $point) {
                $matrix->set($point, self::ROCK);
            }
        }

        return $matrix;
    }

    private function sandfall(Matrix $cave, Point2D $sand, bool $isAbyss): bool
    {
        $down      = new DirectionalVector(new Point2D(0, 0), new Point2D(0, 1));
        $downLeft  = new DirectionalVector(new Point2D(0, 0), new Point2D(-1, 1));
        $downRight = new DirectionalVector(new Point2D(0, 0), new Point2D(1, 1));

        while (true) {
            $next = $sand->translate($down);

            if (!$isAbyss && $next->getY() === $cave->getMaxY() + 2) {
                $cave->set($sand, self::SAND);
                return true;
            } elseif ($isAbyss && $next->getY() >= ($cave->getMaxY() + 1)) {
                return false;
            }

            if ($cave->get($next, self::AIR) === self::AIR) {
                $sand = $next;
                continue;
            }

            $next = $sand->translate($downLeft);
            if ($cave->get($next, self::AIR) === self::AIR) {
                $sand = $next;
                continue;
            }

            $next = $sand->translate($downRight);
            if ($cave->get($next, self::AIR) === self::AIR) {
                $sand = $next;
                continue;
            }

            if ($sand->getY() === 0) { // Part 2 break
                return false;
            }

            $cave->set($sand, self::SAND);

            return true;
        }
    }

    protected function starOne(array $inputs): int
    {
        $cave = $this->getMatrix($inputs);
        $cave->set(new Point2D(500, 0), self::ORIGIN);

        $count = 0;
        while ($this->sandfall($cave, new Point2D(500, 0), true)) {
            $count++;
        }

        return $count;
    }

    protected function starTwo(array $inputs): int
    {
        $cave = $this->getMatrix($inputs);
        $cave->set(new Point2D(500, 0), self::ORIGIN);

        $count = 0;
        while ($this->sandfall($cave, new Point2D(500, 0), false)) {
            $count++;
        }

        return ++$count;
    }
}
