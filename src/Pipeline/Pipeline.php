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
 * Class Pipe
 *
 * @author Romain Cottard
 */
class Pipeline
{
    use PipelineTrait;

    private array $instances;

    public function __construct(array $instances = [])
    {
        $this->instances = $instances;

        Store::flush();
    }

}
