<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Common;

abstract class DayRendering implements RenderingInterface
{
    private string $buffer = '';

    public function render(string $star, array $inputs): string
    {
        $this->buffer = '';

        return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    protected function buffering(string $content): static
    {
        $this->buffer .= $content;

        return $this;
    }

    protected function getBuffer(): string
    {
        return $this->buffer;
    }

    abstract protected function starOne(array $inputs): mixed;

    abstract protected function starTwo(array $inputs): mixed;
}
