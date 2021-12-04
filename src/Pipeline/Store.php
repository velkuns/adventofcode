<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Pipeline;

class Store
{
    private static array $store = [];

    public static function flush(): void
    {
        self::$store = [];
    }

    public static function set(string $name, $value): void
    {
        self::$store[$name] = $value;
    }

    public static function get(string $name)
    {
        return self::$store[$name];
    }

    public static function getMany(...$names): array
    {
        $input = [];
        foreach ($names as $name) {
            if (!isset(self::$store[$name])) {
                continue;
            }

            $input[] = self::$store[$name];
        }

        return $input;
    }
}
