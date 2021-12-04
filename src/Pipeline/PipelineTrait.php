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
    /** @var mixed $input */
    protected $input;

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

    public function store(string $name)
    {
        Store::set($name, $this->input);

        return $this;
    }

    public function retrieve(...$names): PipelineArray
    {
        return $this->array(Store::getMany(...$names));
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->input;
    }

    protected function newPipe($input)
    {
        switch (true) {
            case is_string($input):
                return new PipelineString($input);
            case is_array($input):
                return new PipelineArray($input);
            case is_int($input):
                return new PipelineInt($input);
            case is_float($input):
                return new PipelineFloat($input);
            case is_bool($input):
                return new PipelineBool($input);
            default:
                return new Pipeline($input);
        }
    }
}
