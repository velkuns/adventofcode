<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Year2021;

use Application\Algorythm\BinaryTree\Snailfish;
use Application\Common\AlgorithmInterface;

/**
 * Class Day18
 *
 * @author Romain Cottard
 */
class Day18 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                [3488 => [
                    '[[[0,[4,5]],[0,0]],[[[4,5],[2,6]],[9,5]]]',
                    '[7,[[[3,7],[4,3]],[[6,3],[8,8]]]]',
                    '[[2,[[0,8],[3,4]]],[[[6,7],1],[7,[1,6]]]]',
                    '[[[[2,4],7],[6,[0,5]]],[[[6,8],[2,8]],[[2,1],[4,5]]]]',
                    '[7,[5,[[3,8],[1,4]]]]',
                    '[[2,[2,2]],[8,[8,1]]]',
                    '[2,9]',
                    '[1,[[[9,3],9],[[9,0],[0,7]]]]',
                    '[[[5,[7,4]],7],1]',
                    '[[[[4,2],2],6],[8,7]]',
                ]],
                [4140 => [
                    '[[[0,[5,8]],[[1,7],[9,6]]],[[4,[1,2]],[[1,4],2]]]',
                    '[[[5,[2,8]],4],[5,[[9,9],0]]]',
                    '[6,[[[6,2],[5,6]],[[7,6],[4,7]]]]',
                    '[[[6,[0,7]],[0,9]],[4,[9,[9,0]]]]',
                    '[[[7,[6,4]],[3,[1,3]]],[[[5,5],1],9]]',
                    '[[6,[[7,3],[3,2]]],[[[3,8],[5,7]],4]]',
                    '[[[[5,4],[7,7]],8],[[8,3],8]]',
                    '[[9,3],[[9,9],[6,[4,9]]]]',
                    '[[2,[[7,7],7]],[[5,8],[[9,3],[0,2]]]]',
                    '[[[[5,2],5],[8,[3,7]]],[[5,[7,5]],[4,4]]]',
                ]],
            ],
            '**' => [
                [3993 => [
                    '[[[0,[5,8]],[[1,7],[9,6]]],[[4,[1,2]],[[1,4],2]]]',
                    '[[[5,[2,8]],4],[5,[[9,9],0]]]',
                    '[6,[[[6,2],[5,6]],[[7,6],[4,7]]]]',
                    '[[[6,[0,7]],[0,9]],[4,[9,[9,0]]]]',
                    '[[[7,[6,4]],[3,[1,3]]],[[[5,5],1],9]]',
                    '[[6,[[7,3],[3,2]]],[[[3,8],[5,7]],4]]',
                    '[[[[5,4],[7,7]],8],[[8,3],8]]',
                    '[[9,3],[[9,9],[6,[4,9]]]]',
                    '[[2,[[7,7],7]],[[5,8],[[9,3],[0,2]]]]',
                    '[[[[5,2],5],[8,[3,7]]],[[5,[7,5]],[4,4]]]',
                ]],
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs, bool $functionalMode = false): string
    {
        return (string) ($star === '*' ? $this->starOne($inputs) : $this->starTwo($inputs));
    }

    private function starOne(array $inputs): int
    {
        $snailfish = Snailfish::fromString(array_shift($inputs));

        foreach ($inputs as $input) {
            $snailfish->add(Snailfish::fromString($input));
            $snailfish->reduce();
        }

        return $snailfish->magnitude();
    }

    private function starTwo(array $inputs): int
    {
        $magnitudes = [];
        for ($i = 0; $i < count($inputs); $i++) {
            for ($j = 0; $j < count($inputs); $j++) {
                if ($i === $j) {
                    continue;
                }

                $snailfish = Snailfish::fromString($inputs[$i]);
                $snailfish->add(Snailfish::fromString($inputs[$j]));
                $snailfish->reduce();

                $magnitudes[] = $snailfish->magnitude();
            }
        }

        return max($magnitudes);
    }
}
