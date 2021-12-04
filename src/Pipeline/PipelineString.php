<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Pipeline;

class PipelineString
{
    use PipelineTrait;

    private array $allowedMethods = [
        'trim', 'explode'
    ];

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function __call(string $name, array $args)
    {
        if (!in_array($name, $this->allowedMethods)) {
            return $this;
        }

        //~ Put input as first argument.
        array_unshift($this->input, $args);

        $input = call_user_func_array($name, array_merge($args));

        return $this;
    }
}
