<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Pipeline;

class PipelineArray
{
    use PipelineTrait;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function count(): PipelineInt
    {
        return $this->int(count($this->input));
    }

    public function sum(): PipelineInt
    {
        return $this->int(array_sum($this->input));
    }

    public function product(): PipelineInt
    {
        return $this->int(array_product($this->input));
    }

    public function map(callable $callback): self
    {
        return $this->newPipe(array_map($callback, $this->input));
    }

    public function filter(callable $callback): self
    {
        return $this->newPipe(array_filter($this->input, $callback));
    }

    public function slice(int $offset, ?int $length = null): PipelineArray
    {
        return $this->array(array_slice($this->input, $offset, $length));
    }

    public function reduce(callable $callable, $init)
    {
        return $this->newPipe(array_reduce($this->input, $callable, $init));
    }
}
