<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Trigonometry;

class Point2DCollider extends Point2D
{
    /** @var DirectionalVector[] */
    private array $collideOn;

    /**
     * @param int $x
     * @param int $y
     * @param DirectionalVector[] $collidesOn
     */
    public function __construct(
        int $x,
        int $y,
        array $collidesOn = []
    ) {
        parent::__construct($x, $y);

        foreach ($collidesOn as $vector) {
            $direction = Direction::fromVector($vector);
            $this->collideOn[$direction->value] = $vector;
        }
    }

    public function isCollideOn(Direction $direction): bool
    {
        return match($direction) {
            Direction::Right => $this->isCollideOnRight(),
            Direction::Left  => $this->isCollideOnLeft(),
            Direction::Down  => $this->isCollideOnDown(),
            Direction::Up    => $this->isCollideOnUp(),
        };
    }

    public function isCollideOnLeft(): bool
    {
        return $this->collideOn[Direction::Left->value] !== null;
    }

    public function isCollideOnRight(): bool
    {
        return $this->collideOn[Direction::Right->value] !== null;
    }

    public function isCollideOnUp(): bool
    {
        return $this->collideOn[Direction::Up->value] !== null;
    }

    public function isCollideOnDown(): bool
    {
        return $this->collideOn[Direction::Down->value] !== null;
    }

    public function getCoordinates(): string
    {
        return "$this->x,$this->y";
    }

    public function __toString(): string
    {
        return "($this->x,$this->y)";
    }

    public function translate(Vector $vector): static
    {
        return new static(
            $this->x + $vector->getX(),
            $this->y + $vector->gety(),
            $this->collideOn
        );
    }
}
