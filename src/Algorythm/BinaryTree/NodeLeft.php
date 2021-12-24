<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Algorythm\BinaryTree;

class NodeLeft extends Node
{
    public function __construct(Node $parent)
    {
        parent::__construct($parent);

        $this->isLeft = true;
    }
}
