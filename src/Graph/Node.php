<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Graph;

class Node
{
    private bool $isMarked = false;
    private array $connections = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function setConnections(array $nodes): self
    {
        $this->connections = $nodes;
        return $this;
    }

    public function mark(): self
    {
        $this->isMarked = true;
        return $this;
    }

    public function isMarked(): bool
    {
        return $this->isMarked;
    }

    public function getConnections(): array
    {
        return $this->connections;
    }

    public function getValue(string $name): int
    {
        return $this->data[$name];
    }
}
