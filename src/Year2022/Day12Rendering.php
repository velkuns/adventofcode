<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Year2022;

use Application\Common\Day;
use Application\Common\DayRendering;
use Application\Trigonometry\Matrix;
use Application\Trigonometry\NormalizedVector;
use Application\Trigonometry\Point2D;
use Application\Trigonometry\Vector;
use Eureka\Component\Console\Argument\Argument;
use Eureka\Component\Console\IO\Out;
use Eureka\Component\Console\Style\Color;
use Eureka\Component\Console\Style\Style;

class Day12Rendering extends DayRendering
{
    /**
     * @return Vector[]
     */
    private function directions(): array
    {
        static $direction = [
            '^' => new NormalizedVector(new Point2D(0, 0), new Point2D(0, -1)),
            'v' => new NormalizedVector(new Point2D(0, 0), new Point2D(0, 1)),
            '<' => new NormalizedVector(new Point2D(0, 0), new Point2D(-1, 0)),
            '>' => new NormalizedVector(new Point2D(0, 0), new Point2D(1, 0)),
        ];

        return $direction;
    }

    private function try(Matrix $map, Point2D $me, Vector $direction, string $dir, int $step, array &$visited, Matrix $render): void
    {
        $bad = (new Style('x'))->colorForeground(Color::RED)->bold();
        $can = (new Style('?'))->colorForeground(Color::YELLOW)->bold();
        $ok  = (new Style())->colorForeground(Color::GREEN)->bold();

        $this->renderStep($render);
        $next  = $me->translate($direction);
        $value = $map->get($next);

        if ($value === null) {
            return;
        }

        $previous = $render->get($next);

        $render->set($next, $can);
        $this->renderStep($render);
        $alreadyVisitedWithSteps = $visited[$next->getCoordinates()] ?? null;
        if ($alreadyVisitedWithSteps !== null && $step >= $alreadyVisitedWithSteps) {
            $render->set($next, $bad);
            $this->renderStep($render);
            $render->set($next, $previous);
            return;
        }

        $currentHeight = ord($map->get($me) === 'E' ? 'z' : $map->get($me));
        $nextHeight    = ord($value === 'S' ? 'a' : $value);

        if ($nextHeight < ($currentHeight - 1)) {
            $render->set($next, $bad);
            $this->renderStep($render);
            $render->set($next, $previous);
            return;
        }

        $visited[$next->getCoordinates()] = $step;
        $render->set($next, $ok->setText($dir));
        $this->renderStep($render);

        $render->set($next, $dir);
        foreach ($this->directions() as $dir => $direction) {
            $this->try($map, $next, $direction, $dir, $step + 1, $visited, $render);
        }
    }

    protected function starOne(array $inputs): mixed
    {
        $map = (new Matrix(array_map(str_split(...), $inputs)))->transpose();
        $me  = $map->locate('E'); // Start from end to reuse try() for second part of puzzle

        $render  = new Matrix(array_fill(0, $map->height(), array_fill(0, $map->width(), ' ')));
        $render->set($me, 'E');

        $visited = [$me->getCoordinates() => 0];
        $step    = 0;

        foreach ($this->directions() as $dir => $direction) {
            $this->try($map, $me, $direction, $dir, $step + 1, $visited, $render);
        }

        $destination = $map->locate('S');
        $render->set($destination, 'S');

        $this->renderStep($render);

        return null;
    }

    protected function starTwo(array $inputs): mixed
    {
        $map = (new Matrix(array_map(str_split(...), $inputs)))->transpose();

        $me  = $map->locate('E');

        $render  = new Matrix(array_fill(0, $map->height(), array_fill(0, $map->width(), ' ')));
        $render->set($me, 'E');
        $visited = [$me->getCoordinates() => 0];
        $step    = 0;

        $this->renderStep($render);

        foreach ($this->directions() as $dir => $direction) {
            $this->try($map, $me, $direction, $dir, $step + 1, $visited, $render);
        }
        $this->renderStep($render);

        $destinations = [];
        foreach ($map->locateAll('a') as $destination) {
            if (isset($visited[$destination->getCoordinates()])) {
                $destinations[$destination->getCoordinates()] = $visited[$destination->getCoordinates()];
            }
        }

        $val = min($destinations);
        [$x, $y] = explode(',', array_search($val, $destinations));
        $render->set(new Point2D((int) $x, (int) $y), 'S');

        $this->renderStep($render);

        return null;
    }

    private function renderStep(Matrix $render, int $tick = 10_000): void
    {
        if (!Argument::getInstance()->has('render')) {
            return;
        }

        Out::clear();
        Out::std($render->render());
        usleep($tick);
    }
}
