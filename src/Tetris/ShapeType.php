<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Tetris;

enum ShapeType
{
    case HorizontalBar;
    case VerticalBar;
    case Cross;
    case Square;
    case Angle;
}
