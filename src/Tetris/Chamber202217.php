<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Tetris;

use Application\Trigonometry\Direction;
use Application\Trigonometry\DirectionalVector;
use Application\Trigonometry\Matrix;
use Application\Trigonometry\Point2D;
use Eureka\Component\Console\IO\Out;

class Chamber202217
{
    private Matrix $matrix;
    private int $maxHeight;
    private int $jetNumber = 0;
    private int $jetPatternSize;

    private int $shapeCount = 0;

    public function __construct(
        private readonly string $jet,
        private readonly int $width,
        private readonly int $height = 0
    ) {
        $this->maxHeight = $this->height;
        $this->matrix    = new Matrix(
            [
                //0 => ['+', '|', '|', '|', '|', '|', '|', '|'],
                1 => [], //['-', ' ', ' ', ' ', ' ', ' ', ' ', ' '],
                2 => [], //['-', ' ', ' ', ' ', ' ', ' ', ' ', ' '],
                3 => [], //['-', ' ', ' ', ' ', ' ', ' ', ' ', ' '],
                4 => [], //['-', ' ', ' ', ' ', ' ', ' ', ' ', ' '],
                5 => [], //['-', ' ', ' ', ' ', ' ', ' ', ' ', ' '],
                6 => [], //['-', ' ', ' ', ' ', ' ', ' ', ' ', ' '],
                7 => [], //['-', ' ', ' ', ' ', ' ', ' ', ' ', ' '],
                //8 => ['+', '|', '|', '|', '|', '|', '|', '|'],
            ]
        );

        $this->jetPatternSize = strlen($this->jet);
    }

    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    public function jet(Shape $shape): Shape
    {
        $direction = Direction::from($this->jet[$this->jetNumber]);
        $vector    = DirectionalVector::fromDirection($direction);

        $this->jetNumber++;
        if ($this->jetNumber >= $this->jetPatternSize) {
            $this->jetNumber = 0;
        }

        $nextPosition = $shape->move($vector);

        if ($this->try($nextPosition, $direction)) {
            return $nextPosition;
        }

        return $shape;
    }

    public function fall(Shape $shape): Shape|false
    {
        $direction = Direction::Down;
        $down      = DirectionalVector::fromDirection($direction);

        $nextPosition = $shape->move($down);

        if ($this->try($nextPosition, $direction)) {
            //$this->update($nextPosition, '@', true);
            return $nextPosition;
        }

        //$this->update($shape, '#', true);
        $this->update($shape);

        return false;
    }

    private function update(Shape $shape, string $char = '#', bool $withRendering = false): void
    {
        if ($withRendering) {
            $matrix = clone $this->matrix;
        } else {
            $matrix = $this->matrix;
            $this->maxHeight = max($this->maxHeight, $shape->getHighestPoint()->getY());
        }

        foreach ($shape->getPoints() as $point) {
            $matrix->set($point, $char);
        }

        if (!$withRendering && $this->shapeCount++ > 100) {
            $this->shapeCount = 0;
            $this->matrix = $matrix->sliceOnY(-30);
        }
    }

    public function try(Shape $shape, Direction $direction): bool
    {
        foreach ($shape->getPointsCollideOn($direction) as $point) {
            if ($point->getX() < 1 || $point->getX() > $this->width) {
                return false;
            }

            if ($point->getY() < 1) {
                return false;
            }

            $value = $this->matrix->get($point);
            if (!in_array($value, [null, '.', ' '])) {
                return false;
            }
        }

        return true;
    }
}
