<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Graph;

class Vertex implements \Stringable
{
    private string $name;
    private array $neighbors;

    protected mixed $data;

    public function __construct(string $name)
    {
        $this->name  = $name;
    }

    public function addNeighbor(Vertex $vertex)
    {
        $this->neighbors[(string) $vertex] = $vertex;
    }

    /**
     * @return Vertex[]
     */
    public function neighbors(): array
    {
        return $this->neighbors;
    }

    public function setData(mixed $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
