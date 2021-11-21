<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script;

use Application\Common\AlgorithmInterface;
use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Argument\Argument;
use Eureka\Component\Console\Help;
use Eureka\Component\Console\IO\Out;
use Eureka\Component\Console\Style\Color;
use Eureka\Component\Console\Style\Style;

class Algo extends AbstractScript
{
    private array $config;

    public function __construct()
    {
        $this->setExecutable();
        $this->setDescription('Execute Advent Of Code Algorithm!');

        $this->config = ['2020' => __DIR__ . '/../../data/2020'];
    }

    public function help(): void
    {
        $help = new Help('algo');
        $help->addArgument('y', 'year', 'Year to solve', true, true);
        $help->addArgument('d', 'day', 'Day to solve', true, true);
        $help->addArgument('e', 'example', 'Example Only', false, false);
        $help->display();
    }

    public function run(): void
    {
        $arguments = Argument::getInstance();
        $year = $arguments->get('y', 'year', (int) date('Y'));
        $day  = $arguments->get('d', 'day', 1);

        $class = '\Application\Year' . $year . '\Day' . $day;

        if (!class_exists($class)) {
            throw new \RuntimeException("Algorithm class does not exists for year $year & day $day!");
        }

        if (!isset($this->config[$year])) {
            throw new \RuntimeException("No config for $year!");
        }

        $file = $this->config[$year] . '/day-' . $day . '.txt';

        if (!file_exists($file)) {
            throw new \RuntimeException("No file for $year!");
        }

        /** @var AlgorithmInterface $solver */
        $solver = new $class();

        $white  = (new Style())->bold();
        $yellow = (new Style())->colorForeground(Color::YELLOW);
        $cyan   = (new Style())->colorForeground(Color::CYAN);

        Out::std($white->setText('------------------------------------------ EXAMPLES ------------------------------------------'));
        foreach (['*', '**'] as $star) {
            foreach ($solver->getExamples($star) as $data) {
                foreach ($data as $expected => $inputs) {
                    Out::std($yellow->setText($star) . ':  ' . $cyan->setText($solver->solve($star, $inputs)) . ' - expected: ' . $expected);
                }
            }
        }

        $inputs = file($file);
        $inputs = array_map('trim', $inputs); // remove trailing chars

        if (!$arguments->has('e', 'example')) {
            Out::std($white->setText('------------------------------------------- OUTPUT -------------------------------------------'));
            Out::std($yellow->setText('*') . '  : ' . $cyan->setText($solver->solve('*', $inputs)));
            Out::std($yellow->setText('**') . ' : ' . $cyan->setText($solver->solve('**', $inputs)));
        }
    }
}
