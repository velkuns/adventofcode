<?php

/*
 * Copyright (c) Romain Cottard
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

        $this->config = [
            '2020' => __DIR__ . '/../../data/2020',
            '2021' => __DIR__ . '/../../data/2021',
            '2022' => __DIR__ . '/../../data/2022',
        ];

        Argument::getInstance()->add('color', true);
    }

    public function help(): void
    {
        $help = new Help('algo');
        $help->addArgument('y', 'year', 'Year to solve', true);
        $help->addArgument('d', 'day', 'Day to solve', true);
        $help->addArgument('e', 'example', 'Example Only');
        $help->addArgument('f', 'functional', 'Activate Algo in functional programming style');
        $help->addArgument('s', 'skip-empty-lines', 'Remove empty lines from inputs');
        $help->addArgument('', 'render', 'Use render script instead of solver script (if present)');
        $help->display();
    }

    public function run(): void
    {
        $arguments = Argument::getInstance();
        $year = (int) $arguments->get('y', 'year', (int) date('Y'));
        $day  = (int) $arguments->get('d', 'day', (int) date('d'));

        $doRendering = $arguments->has('render');
        $class = '\Application\Year' . $year . '\Day' . $day . ($doRendering ? 'Rendering' : '');

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

        if ($doRendering) {
            $this->render($class, $file, $year, $day);
        } else {
            $this->solve($class, $file, $year, $day);
        }

    }

    private function solve(string $class, string $file, int $year, int $day): void
    {
        /** @var AlgorithmInterface $solver */
        $solver = new $class();
        $arguments = Argument::getInstance();

        $white  = (new Style())->bold();
        $yellow = (new Style())->colorForeground(Color::YELLOW);
        $cyan   = (new Style())->colorForeground(Color::CYAN);
        $red    = (new Style())->colorForeground(Color::RED);

        $functionalSuffix = $arguments->has('f', 'functional') ? ' (FUNCTIONAL)' : '';

        $line = str_repeat('-', 42);
        Out::std($white->setText("$line EXAMPLES $functionalSuffix $line"));
        foreach (['*', '**'] as $star) {
            $examples = $this->getExamples($year, $day, $star);
            foreach (($examples ?? $solver->getExamples($star)) as $data) {
                foreach ($data as $expected => $inputs) {
                    $inputs = $this->cleanEmptyLines($inputs);
                    $answer = $solver->solve($star, $inputs, $arguments->has('f', 'functional'));
                    Out::std(
                        $yellow->setText(str_pad($star, 2)) . ':  ' .
                        $cyan->setText($answer) . ' - expected: ' . $expected
                    );
                }
            }
        }

        $inputs = file($file);
        $inputs = $this->cleanEmptyLines(array_map('trim', $inputs)); // remove trailing chars

        if (!$arguments->has('e', 'example')) {
            $timeOneStar   = -microtime(true);
            $solveOneStar  = $solver->solve('*', $inputs, $arguments->has('f', 'functional'));
            $timeOneStar   = '[' . round($timeOneStar + microtime(true), 5) . 's]';
            $memoryOneStar = '[' . round(memory_get_peak_usage() / 1024 / 1024, 1) . 'MB]';
            $timeTwoStar   = -microtime(true);
            $solveTwoStar  = $solver->solve('**', $inputs, $arguments->has('f', 'functional'));
            $timeTwoStar   = '[' . round($timeTwoStar + microtime(true), 5) . 's]';
            $memoryTwoStar = '[' . round(memory_get_peak_usage() / 1024 / 1024, 1) . 'MB]';

            Out::std($white->setText("$line OUTPUT $functionalSuffix $line"));
            Out::std(
                $yellow->setText('*') . ' : ' .
                $cyan->setText($solveOneStar) . ' - ' .
                $red->setText($timeOneStar) . ' - ' .
                $yellow->setText($memoryOneStar)
            );
            Out::std(
                $yellow->setText('**') . ': ' .
                $cyan->setText($solveTwoStar) . ' - ' .
                $red->setText($timeTwoStar) . ' - ' .
                $yellow->setText($memoryTwoStar)
            );
        }
    }

    private function render(string $class, string $file, int $year, int $day): void
    {
        /** @var AlgorithmInterface $solver */
        $rendering = new $class();

        $arguments = Argument::getInstance();

        foreach (['*'] as $star) {
            $examples = $this->getExamples($year, $day, $star);
            foreach (($examples ?? $solver->getExamples($star)) as $index => $data) {
                foreach ($data as $inputs) {
                    $inputs = $this->cleanEmptyLines($inputs);
                    Out::clear();
                    Out::std(
                        "\n\n\n\t\tExample - " . (new Style($star))->colorForeground(Color::YELLOW)->bold() . ' - ' . $index . "\n\n\n"
                    );
                    usleep(1_000_000);
                    Out::clear();
                    Out::std($rendering->render($star, $inputs));
                }
            }
        }


        $inputs = file($file);
        $inputs = $this->cleanEmptyLines(array_map('trim', $inputs)); // remove trailing chars

        if ($arguments->has('e', 'example')) {
            return;
        }

        Out::clear();
        Out::std(
            "\n\n\n\t\tAdvent Of Code - $year - Day $day - " . (new Style('*'))->colorForeground(Color::YELLOW)->bold() . " - \n\n\n"
        );
        usleep(1_000_000);

        Out::clear();
        Out::std($rendering->render('*', $inputs));

        usleep(2_000_000);
        Out::clear();
        Out::std(
            "\n\n\n\t\tAdvent Of Code - $year - Day $day - " . (new Style('**'))->colorForeground(Color::YELLOW)->bold() . " - \n\n\n"
        );
        usleep(1_000_000);
        Out::clear();
        Out::std($rendering->render('*', $inputs));
    }

    private function getExamples(int $year, int $day, string $star): array|null
    {
        $starNumber = ($star === '*' ? 1 : 2);
        $filesInputs   = glob("{$this->config[$year]}/day-$day-star-$starNumber-example-*.txt");
        $filesExpected = glob("{$this->config[$year]}/day-$day-star-$starNumber-expected-*.txt");

        if (empty($filesInputs) || empty($filesExpected) || count($filesInputs) !== count($filesExpected)) {
            return null;
        }

        $examples = [];
        for ($f = 0; $f < count($filesInputs); $f++) {
            $expected = trim((string) file_get_contents($filesExpected[$f]));
            if (ctype_digit($expected)) {
                $expected = (int) $expected;
            } elseif (is_numeric($expected)) {
                $expected = (float) $expected;
            }
            $examples[] = [$expected => array_map('trim', (array) file($filesInputs[$f]))];
        }

        return $examples;
    }

    private function cleanEmptyLines(array $inputs): array
    {
        if (!Argument::getInstance()->has('skip-empty-lines', 's')) {
            return $inputs;
        }

        return array_filter($inputs, fn(string $input) => $input !== '');
    }
}
