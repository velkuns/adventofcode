<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Trigonometry;

class Matrix
{
    public function __construct(array $matrix)
    {
        $this->matrix = $matrix;
    }

    public function transpose(): static
    {
        $columns = [];
        for ($n = 0; $n < count($this->matrix[0]); $n++) {
            $columns[] = array_column($this->matrix, $n);
        }

        return new static($columns);
    }

    public function height(): int
    {
        return count($this->matrix);
    }

    public function width(): int
    {
        return count($this->matrix[0] ?? []);
    }

    public function get(Point $point): mixed
    {
        return $this->matrix[$point->getX()][$point->getY()] ?? null;
    }

    public function set(Point $point, mixed $value): static
    {
        $this->matrix[$point->getX()][$point->getY()] = $value;

        return $this;
    }

    public function locate(mixed $search): Point2D
    {
        $points = $this->locateAll($search);

        if (empty($points)) {
            throw new \RuntimeException('Value not found!');
        }

        return reset($points);
    }

    /**
     * @param mixed $search
     * @return Point2D[]
     */
    public function locateAll(mixed $search): array
    {
        $lines = array_filter($this->matrix, fn ($line) => in_array($search, $line));

        $points = [];
        foreach ($lines as $x => $line) {
            $column = array_filter($line, fn ($char) => $char === $search);
            foreach (array_keys($column) as $y) {
                $points[] = new Point2D($x, $y);
            }
        }

        return $points;
    }

    public function render(): string
    {
        $matrix = $this->transpose();
        return implode("\n", array_map(fn ($line) => implode('', $line), $matrix->matrix));
    }
}
