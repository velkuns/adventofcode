<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Trigonometry;

class DirectionalVector extends Vector
{
    public static function fromDirection(Direction $direction): static
    {
        return match ($direction) {
            Direction::Right => static::right(),
            Direction::Left  => static::left(),
            Direction::Down  => static::down(),
            Direction::Up    => static::up(),
        };
    }

    public static function left(): static
    {
        return new static(new Point(0, 0, 0), new Point(-1, 0, 0));
    }

    public static function right(): static
    {
        return new static(new Point(0, 0, 0), new Point(1, 0, 0));
    }

    public static function up(): static
    {
        return new static(new Point(0, 0, 0), new Point(0, -1, 0));
    }

    public static function down(): static
    {
        return new static(new Point(0, 0, 0), new Point(0, 1, 0));
    }

    public function __construct(Point $p1, Point $p2, int $norm = 1)
    {
        $x = $p2->getX() - $p1->getX();
        $y = $p2->getY() - $p1->getY();
        $z = $p2->getZ() - $p1->getZ();

        $origin = new Point(0, 0, 0);
        $destination = new Point(
            $x !== 0 ? $norm * ($x / abs($x)) : 0,
            $y !== 0 ? $norm * ($y / abs($y)) : 0,
            $z !== 0 ? $norm * ($z / abs($z)) : 0,
        );

        parent::__construct($origin, $destination, false);
    }

    public function onUp(): bool
    {
        return $this->getY() === 1;
    }

    public function onDown(): bool
    {
        return $this->getY() === -1;
    }

    public function onLeft(): bool
    {
        return $this->getX() === -1;
    }

    public function onRight(): bool
    {
        return $this->getX() === 1;
    }
}
