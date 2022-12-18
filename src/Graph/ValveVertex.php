<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Graph;

class ValveVertex extends Vertex
{
    public function getRate(): int
    {
        return (int) $this->data;
    }

    public function setRate(int $data): static
    {
        $this->data = $data;

        return $this;
    }
}
