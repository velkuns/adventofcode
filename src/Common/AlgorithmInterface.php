<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Common;

interface AlgorithmInterface
{
    public function getExamples(string $star): array;

    public function solve(string $star, array $inputs): string;
}
