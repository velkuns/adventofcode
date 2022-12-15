<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Math;

class IntRanges extends Ranges
{
    public function exclude(int $value): static
    {
        foreach ($this->ranges as [$min, $max]) {
            if ($value === $min && $value === $max) {
                unset($this->ranges[$min]);
                break;
            }

            if ($value === $min) {
                $this->ranges[$value + 1] = [$value + 1, $max];
                unset($this->ranges[$min]);
                break;
            }

            if ($value === $max) {
                $this->ranges[$min] = [$min, $value - 1];
                break;
            }

            if ($value > $min && $value < $max) {
                $this->ranges[$min] = [$min, $value - 1];
                $this->ranges[$value + 1] = [$value + 1, $max];
                break;
            }

            //~ Continue to next range
        }
        return $this;
    }
}
