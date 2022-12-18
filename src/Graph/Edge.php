<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Graph;

class Edge
{
    public function __construct(
        protected readonly Vertex $from,
        protected readonly Vertex $to,
        protected readonly int $weight = 1
    ) {
        $this->from->addNeighbor($this->to);
        $this->to->addNeighbor($this->from);
    }

    public function from(): Vertex
    {
        return $this->from;
    }

    public function to(): Vertex
    {
        return $this->to;
    }

    public function weight(): int
    {
        return $this->weight;
    }

    public function invert(): static
    {
        return new static($this->to, $this->from, $this->weight);
    }
}
