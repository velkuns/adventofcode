<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Trigonometry;

class Matrix
{
    private array $matrix;

    private int $minX;
    private int $maxX;
    private int $minY;
    private int $maxY;

    public static function fromCoordinates(Point2D $p1, Point2D $p2, mixed $default): static
    {
        return new static(
            array_fill(
                $p1->getX(),
                ($p2->getX() - $p1->getX() + 1),
                array_fill($p1->getY(), ($p2->getY() - $p1->getY() + 1), $default)
            )
        );
    }

    public function __construct(array $matrix = [0 => []])
    {
        $this->matrix = $matrix;

        $keys = array_keys($this->matrix);
        $this->minX = min($keys);
        $this->maxX = max($keys);

        $this->minY = min(array_map(fn($line) => empty($line) ? 0 : min(array_keys($line)), $this->matrix));
        $this->maxY = max(array_map(fn($line) => empty($line) ? 0 : max(array_keys($line)), $this->matrix));
    }

    public function transpose(): static
    {
        $array = [];
        foreach ($this->matrix as $x => $column) {
            foreach ($column as $y => $value) {
                $array[$y][$x] = $value;
            }
        }

        return new static($array);
    }

    public function invert(): static
    {
        $matrix = $this->matrix;
        krsort($matrix);

        return new static($matrix);
    }

    public function height(): int
    {
        return count($this->matrix);
    }

    public function getMaxY(): int
    {
        return $this->maxY;
    }

    public function width(): int
    {
        return count($this->matrix[0] ?? []);
    }

    public function get(Point $point, mixed $default = null): mixed
    {
        return $this->matrix[$point->getX()][$point->getY()] ?? $default;
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

    public function sliceOnY(int $offset, ?int $length = null): static
    {
        $matrix = array_map(fn ($line) => array_slice($line, $offset, $length, true), $this->matrix);

        return new static($matrix);
    }

    public function render(bool $invert = false): string
    {
        $matrix = $this->transpose();
        if ($invert) {
            $matrix = $matrix->invert();
        }

        return implode("\n", array_map(fn ($line) => implode('', $line), $matrix->matrix));
    }

    public function renderIncompleteMatrix(bool $invert = false): string
    {
        $keys = array_keys($this->matrix);
        $minX = 0;min($keys);
        $maxX = 8;max($keys);
        $minY = min(array_map(fn($line) => empty($line) ? 0 : min(array_keys($line)), $this->matrix));
        $maxY = max(array_map(fn($line) => empty($line) ? 0 : max(array_keys($line)), $this->matrix));

        $buffer = '';
        if ($invert) {
            for ($y = $maxY; $y >= $minY; $y--) {
                for ($x = $minX; $x <= $maxX; $x++) {
                    $buffer .= $this->matrix[$x][$y] ?? ($x === 0 || $x === 8 ? '|' : '.');
                }
                $buffer .= "\n";
            }
            return $buffer;
        }

        for ($y = $minY; $y <= $maxY; $y++) {
            for ($x = $minX; $x <= $maxX; $x++) {
                $buffer .= $this->matrix[$x][$y] ?? ($x === 0 || $x === 8 ? '|' : ' ');
            }
            $buffer .= "\n";
        }
        return $buffer;
    }
}
