<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Pipeline;

trait PipelineTrait
{
    protected mixed $input;

    public function array(?array $input): PipelineArray
    {
        return new PipelineArray($input ?? (array) $this->input);
    }

    public function string(?string $input): PipelineString
    {
        return new PipelineString($input ?? (string) $this->input);
    }

    public function int(?int $input): PipelineInt
    {
        return new PipelineInt($input ?? (int) $this->input);
    }

    public function float(?int $input): PipelineFloat
    {
        return new PipelineFloat($input ?? (float) $this->input);
    }

    public function bool(?bool $input): PipelineBool
    {
        return new PipelineBool($input ?? (float) $this->input);
    }

    public function restore(string $name)
    {
        return $this->newPipe(Store::get($name));
    }

    public function store(string $name): self
    {
        Store::set($name, $this->input);

        return $this;
    }

    public function retrieve(...$names): PipelineArray
    {
        return $this->array(Store::getMany(...$names));
    }

    public function get(): mixed
    {
        return $this->input;
    }

    public function debug(): self
    {
        var_export($this->input);

        return $this;
    }

    protected function newPipe($input): PipelineFloat|PipelineString|PipelineBool|PipelineArray|PipelineInt|Pipeline
    {
        return match (true) {
            is_string($input) => new PipelineString($input),
            is_array($input)  => new PipelineArray($input),
            is_int($input)    => new PipelineInt($input),
            is_float($input)  => new PipelineFloat($input),
            is_bool($input)   => new PipelineBool($input),
            default           => new Pipeline($input),
        };
    }
}
