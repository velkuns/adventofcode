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
        'haystack_first' => [
            'trim',
            'str_split',
        ],
        'haystack_last' => [
            'explode',
        ]
    ];

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function __call(string $name, array $args): PipelineInt|Pipeline|PipelineBool|PipelineArray|PipelineFloat|PipelineString
    {
        if (in_array($name, $this->allowedMethods['haystack_first'])) {
            return $this->useHaystackFirst($name, $args);
        }

        if (in_array($name, $this->allowedMethods['haystack_last'])) {
            return $this->useHaystackLast($name, $args);
        }

        throw new \LogicException('function currently not supported!');
    }

    private function useHaystackFirst(string $name, array $args): PipelineInt|Pipeline|PipelineBool|PipelineArray|PipelineFloat|PipelineString
    {
        //~ Put input as first argument.
        array_unshift($args, $this->input);

        return $this->newPipe(call_user_func_array($name, $args));
    }

    private function useHaystackLast(string $name, array $args): PipelineInt|Pipeline|PipelineBool|PipelineArray|PipelineFloat|PipelineString
    {
        //~ Put input as first argument.
        $args[] = $this->input;

        return $this->newPipe(call_user_func_array($name, $args));
    }
}
