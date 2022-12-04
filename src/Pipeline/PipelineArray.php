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

    public function countValues(): PipelineArray
    {
        return $this->array(array_count_values($this->input));
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

    public function max(): PipelineInt
    {
        return $this->int((int) max($this->input));
    }

    public function filter(callable $callback, int $mode = 0): self
    {
        return $this->newPipe(array_filter($this->input, $callback, $mode));
    }

    public function slice(int $offset, ?int $length = null): PipelineArray
    {
        return $this->array(array_slice($this->input, $offset, $length));
    }

    public function reduce(callable $callable, $init): PipelineInt|Pipeline|PipelineBool|PipelineArray|PipelineFloat|PipelineString
    {
        return $this->newPipe(array_reduce($this->input, $callable, $init));
    }

    public function walk(callable $callable, $arg = null): static
    {
        array_walk($this->input, $callable, $arg);

        return $this;
    }

    public function chunk(int $length): PipelineArray
    {
        return $this->array(array_chunk($this->input, $length));
    }

    public function unique(): PipelineArray
    {
        return $this->array(array_unique($this->input));
    }

    public function intersect(): PipelineArray
    {
        return $this->array(array_intersect(...$this->input));
    }

    public function current(): PipelineInt|Pipeline|PipelineBool|PipelineArray|PipelineFloat|PipelineString
    {
        return $this->newPipe(current($this->input));
    }

    public function sort(): static
    {
        sort($this->input);

        return $this;
    }

    public function rsort(): static
    {
        rsort($this->input);

        return $this;
    }

    public function each(): PipelineEach
    {
        return new PipelineEach($this->input);
    }
}
