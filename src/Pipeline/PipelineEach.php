<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Pipeline;

/**
 * Class PipelineEach
 *
 * @method self explode(string $glue)
 * @method self map(callable $callable)
 * @method self unique()
 * @method self intersect()
 * @method self current()
 */
class PipelineEach
{
    use PipelineTrait;

    private array $pipeQueue = [];

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    public function __call($name, $arguments): self
    {
        $this->pipeQueue[] = [$name, $arguments];

        return $this;
    }

    /**
     * Start new SubPipeline. So we need to advance on the current "each pipeline", then start new one with current
     * updated input.
     */
    public function each(): PipelineEach
    {
        throw new \LogicException('Each inner existing each is currently not supported!');
    }

    public function end(): PipelineArray
    {
        foreach ($this->pipeQueue as [$name, $args]) {
            foreach ($this->input as $index => $input) {
                $this->input[$index] = $this->newPipe($input)->$name(...$args)->get();
            }
        }

        return $this->array($this->input);
    }
}
