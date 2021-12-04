<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Pipeline;

class PipelineBool
{
    use PipelineTrait;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function negate(): self
    {
        $this->input = !$this->input;

        return $this;
    }
}
