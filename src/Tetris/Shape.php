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
use Application\Trigonometry\Point2DCollider;

class Shape
{
    /** @var Point2DCollider[][] */
    protected array $pointsCollideOn = [];

    public static function from(ShapeType $char, int $startX = 0, int $startY = 0): static
    {
        return match ($char) {
            ShapeType::Cross         => static::cross($startX, $startY),
            ShapeType::HorizontalBar => static::horizontalBar($startX, $startY),
            ShapeType::VerticalBar   => static::verticalBar($startX, $startY),
            ShapeType::Square        => static::square($startX, $startY),
            ShapeType::Angle         => static::angle($startX, $startY),
        };
    }

    public static function cross(int $startX = 0, int $startY = 0): static
    {
        return new static([
            new Point2DCollider($startX + 1, $startY + 0, [DirectionalVector::left(), DirectionalVector::right(), DirectionalVector::down()]),
            new Point2DCollider($startX + 0, $startY + 1, [DirectionalVector::left(), DirectionalVector::down()]),
            new Point2DCollider($startX + 1, $startY + 1),
            new Point2DCollider($startX + 2, $startY + 1, [DirectionalVector::right(), DirectionalVector::down()]),
            new Point2DCollider($startX + 1, $startY + 2, [DirectionalVector::left(), DirectionalVector::right()]),
        ]);
    }

    public static function horizontalBar(int $startX = 0, int $startY = 0): static
    {
        return new static([
            new Point2DCollider($startX + 0, $startY + 0, [DirectionalVector::left(), DirectionalVector::down()]),
            new Point2DCollider($startX + 1, $startY + 0, [DirectionalVector::down()]),
            new Point2DCollider($startX + 2, $startY + 0, [DirectionalVector::down()]),
            new Point2DCollider($startX + 3, $startY + 0, [DirectionalVector::right(), DirectionalVector::down()]),
        ]);
    }

    public static function verticalBar(int $startX = 0, int $startY = 0): static
    {
        return new static([
            new Point2DCollider($startX + 0, $startY + 0, [DirectionalVector::left(), DirectionalVector::right(), DirectionalVector::down()]),
            new Point2DCollider($startX + 0, $startY + 1, [DirectionalVector::left(), DirectionalVector::right()]),
            new Point2DCollider($startX + 0, $startY + 2, [DirectionalVector::left(), DirectionalVector::right()]),
            new Point2DCollider($startX + 0, $startY + 3, [DirectionalVector::left(), DirectionalVector::right()]),
        ]);
    }

    public static function square(int $startX = 0, int $startY = 0): static
    {
        return new static([
            new Point2DCollider($startX + 0, $startY + 0, [DirectionalVector::left(), DirectionalVector::down()]),
            new Point2DCollider($startX + 1, $startY + 0, [DirectionalVector::right(), DirectionalVector::down()]),
            new Point2DCollider($startX + 0, $startY + 1, [DirectionalVector::left()]),
            new Point2DCollider($startX + 1, $startY + 1, [DirectionalVector::right()]),
        ]);
    }

    public static function angle(int $startX = 0, int $startY = 0): static
    {
        return new static([
            new Point2DCollider($startX + 0, $startY + 0, [DirectionalVector::left(), DirectionalVector::down()]),
            new Point2DCollider($startX + 1, $startY + 0, [DirectionalVector::down()]),
            new Point2DCollider($startX + 2, $startY + 0, [DirectionalVector::right(), DirectionalVector::down()]),
            new Point2DCollider($startX + 2, $startY + 1, [DirectionalVector::left(), DirectionalVector::right()]),
            new Point2DCollider($startX + 2, $startY + 2, [DirectionalVector::left(), DirectionalVector::right()]),
        ]);
    }

    /**
     * @param Point2DCollider[] $points
     */
    public function __construct(protected readonly array $points)
    {
        $this->pointsCollideOn = [
            Direction::Down->value  => array_filter($points, fn ($point) => $point->isCollideOnLeft()),
            Direction::Left->value  => array_filter($points, fn ($point) => $point->isCollideOnRight()),
            Direction::Right->value => array_filter($points, fn ($point) => $point->isCollideOnDown()),
        ];
    }

    public function move(DirectionalVector $vector): static
    {
        $points = [];
        foreach ($this->points as $point) {
            $points[] = $point->translate($vector);
        }

        return new static($points);
    }

    public function getPointsCollideOn(Direction $direction): array
    {
        return $this->pointsCollideOn[$direction->value];
    }
}
