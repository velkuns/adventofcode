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
use Application\Trigonometry\Point2D;
use Application\Trigonometry\Point2DCollider;

class Shape
{
    /** @var Point2DCollider[][] */
    protected array $pointsCollideOn = [];

    private Point2DCollider $highestPoint;

    /**
     * @param Point2DCollider[] $points
     */
    public function __construct(protected readonly array $points)
    {
        $this->pointsCollideOn = [
            Direction::Down->value  => array_filter($points, fn ($point) => $point->isCollideOn(Direction::Down)),
            Direction::Left->value  => array_filter($points, fn ($point) => $point->isCollideOn(Direction::Left)),
            Direction::Right->value => array_filter($points, fn ($point) => $point->isCollideOn(Direction::Right)),
        ];

        $maxY = null;
        foreach ($this->points as $point) {
            if ($maxY === null || $maxY < $point->getY()) {
                $this->highestPoint = $point;
            }
        }
    }

    /**
     * @return Point2DCollider[]
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    public function getHighestPoint(): Point2DCollider
    {
        return $this->highestPoint;
    }

    public function move(DirectionalVector $vector): static
    {
        $points = [];
        foreach ($this->points as $point) {
            $points[] = $point->translate($vector);
        }

        return new static($points);
    }

    /**
     * @param Direction $direction
     * @return Point2DCollider[]
     */
    public function getPointsCollideOn(Direction $direction): array
    {
        return $this->pointsCollideOn[$direction->value];
    }

    public function debug(): void
    {
        foreach ($this->points as $point) {
            echo "$point\n";
        }
        echo "----------------\n";
    }
}
