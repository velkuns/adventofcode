<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Trigonometry;

class Point2D extends Point
{
    public function getCoordinates(): string
    {
        return "$this->x,$this->y";
    }

    public function __toString(): string
    {
        return "($this->x,$this->y)";
    }
}
