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

class Board
{
    private int $maxHeight = 0;
    private Shape $currentShape;

    public function __construct(private readonly int $width, private readonly ?int $height = 4)
    {
    }

    public function newShape(ShapeType $shapeType): void
    {
        $this->currentShape = Shape::from($shapeType, 2, $this->maxHeight + 4);
    }

    public function move(Direction $direction): void
    {
        $shape = $this->currentShape->move(DirectionalVector::fromDirection($direction));
    }
}
