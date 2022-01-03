<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Year2021;

use Application\Common\AlgorithmInterface;
use Application\Trigonometry\Point;
use Application\Trigonometry\Vector;

/**
 * Class Day19
 *
 * @author Romain Cottard
 */
class Day19 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                /*[3  => [
                    '--- scanner 0 ---','0,2','4,1','3,3','',
                    '--- scanner 1 ---','-1,-1','-5,0','-2,1',
                ]],*/
                [79 => [
                    '--- scanner 0 ---',
                    '404,-588,-901',
                    '528,-643,409',
                    '-838,591,734',
                    '390,-675,-793',
                    '-537,-823,-458',
                    '-485,-357,347',
                    '-345,-311,381',
                    '-661,-816,-575',
                    '-876,649,763',
                    '-618,-824,-621',
                    '553,345,-567',
                    '474,580,667',
                    '-447,-329,318',
                    '-584,868,-557',
                    '544,-627,-890',
                    '564,392,-477',
                    '455,729,728',
                    '-892,524,684',
                    '-689,845,-530',
                    '423,-701,434',
                    '7,-33,-71',
                    '630,319,-379',
                    '443,580,662',
                    '-789,900,-551',
                    '459,-707,401',
                    '',
                    '--- scanner 1 ---',
                    '686,422,578',
                    '605,423,415',
                    '515,917,-361',
                    '-336,658,858',
                    '95,138,22',
                    '-476,619,847',
                    '-340,-569,-846',
                    '567,-361,727',
                    '-460,603,-452',
                    '669,-402,600',
                    '729,430,532',
                    '-500,-761,534',
                    '-322,571,750',
                    '-466,-666,-811',
                    '-429,-592,574',
                    '-355,545,-477',
                    '703,-491,-529',
                    '-328,-685,520',
                    '413,935,-424',
                    '-391,539,-444',
                    '586,-435,557',
                    '-364,-763,-893',
                    '807,-499,-711',
                    '755,-354,-619',
                    '553,889,-390',
                    '',
                    '--- scanner 2 ---',
                    '649,640,665',
                    '682,-795,504',
                    '-784,533,-524',
                    '-644,584,-595',
                    '-588,-843,648',
                    '-30,6,44',
                    '-674,560,763',
                    '500,723,-460',
                    '609,671,-379',
                    '-555,-800,653',
                    '-675,-892,-343',
                    '697,-426,-610',
                    '578,704,681',
                    '493,664,-388',
                    '-671,-858,530',
                    '-667,343,800',
                    '571,-461,-707',
                    '-138,-166,112',
                    '-889,563,-600',
                    '646,-828,498',
                    '640,759,510',
                    '-630,509,768',
                    '-681,-892,-333',
                    '673,-379,-804',
                    '-742,-814,-386',
                    '577,-820,562',
                    '',
                    '--- scanner 3 ---',
                    '-589,542,597',
                    '605,-692,669',
                    '-500,565,-823',
                    '-660,373,557',
                    '-458,-679,-417',
                    '-488,449,543',
                    '-626,468,-788',
                    '338,-750,-386',
                    '528,-832,-391',
                    '562,-778,733',
                    '-938,-730,414',
                    '543,643,-506',
                    '-524,371,-870',
                    '407,773,750',
                    '-104,29,83',
                    '378,-903,-323',
                    '-778,-728,485',
                    '426,699,580',
                    '-438,-605,-362',
                    '-469,-447,-387',
                    '509,732,623',
                    '647,635,-688',
                    '-868,-804,481',
                    '614,-800,639',
                    '595,780,-596',
                    '',
                    '--- scanner 4 ---',
                    '727,592,562',
                    '-293,-554,779',
                    '441,611,-461',
                    '-714,465,-776',
                    '-743,427,-804',
                    '-660,-479,-426',
                    '832,-632,460',
                    '927,-485,-438',
                    '408,393,-506',
                    '466,436,-512',
                    '110,16,151',
                    '-258,-428,682',
                    '-393,719,612',
                    '-211,-452,876',
                    '808,-476,-593',
                    '-575,615,604',
                    '-485,667,467',
                    '-680,325,-822',
                    '-627,-443,-432',
                    '872,-547,-609',
                    '833,512,582',
                    '807,604,487',
                    '839,-516,451',
                    '891,-625,532',
                    '-652,-548,-490',
                    '30,-46,-14',
                ]],
            ],
            '**' => [
                //[3993 => []],
            ],
        ];

        return $examples[$star];
    }

    public function solve(string $star, array $inputs, bool $functionalMode = false): string
    {
        $beacons = $this->inputsToBeacons($inputs);
        return (string) ($star === '*' ? $this->starOne($beacons) : $this->starTwo($beacons));
    }

    private function inputsToBeacons(array $inputs): array
    {
        $beacons = [];
        $scanner = 0;
        foreach ($inputs as $input) {
            if (empty($input)) {
                continue;
            }

            if (preg_match('`--- scanner (\d+) ---`', $input, $matches)) {
                $scanner = (int) $matches[1];
                continue;
            }

            $beacons[$scanner][] = new Point(...array_map('intval', explode(',', $input)));
        }

        return $beacons;
    }

    private function starOne(array $beacons): int
    {
        $vectors       = $this->beaconsToVectors($beacons);
        $mapBeacons    = [];

        foreach ($beacons[0] as $beacon) {
            $mapBeacons[$beacon->getCoordinates()] = $beacon;
        }

        $max = count($vectors);

        $zones    = [0];
        $zone1    = 0;
        $vectors1 = $vectors[0];
        unset($vectors[0]);

        while (!empty($vectors)) {
            for ($zone2 = 0; $zone2 < $max; $zone2++) {
                if ($zone2 === $zone1 || in_array($zone2, $zones)) {
                    continue; // already compared or is the same zone
                }

                $vectors2 = $vectors[$zone2];

                $commonVectors = $this->getCommonVectors($vectors1, $vectors2);
                if (count($commonVectors) < 12) {
                    continue;
                }
                echo "scanner #$zone1 overlaps scanner #$zone2 (\$zones: " . implode(',', $zones) . ")\n";

                $zones[] = $zone2;

                //~ re-orientate beacons position in zone 2.
                $orientation = $this->getOrientation($commonVectors);
                echo "Orientation: " . var_export($orientation, true) . PHP_EOL;
                $translation = $this->getTranslationVector($commonVectors, $orientation);

                $mapBeacons = $this->orientateAndTranslate($mapBeacons, $beacons[$zone2], $orientation, $translation);
                $vectors2   = $this->createVectors($beacons[$zone2]);

                unset($vectors[$zone2]);
                $vectors1 = $vectors2;
                $zone1    = $zone2;

                //echo "$translation\n";
            }
        }

        return count($mapBeacons);
    }

    private function starTwo(array $beacons): int
    {
        return 0;
    }

    private function beaconsToVectors(array $beacons): array
    {
        $vectors = [];
        foreach ($beacons as $scanner => $positions) {
            $vectors[$scanner] = $this->createVectors($positions);
        }

        return $vectors;
    }

    private function createVectors(array $beacons): array
    {
        $vectors = [];
        $max     = count($beacons);

        for ($o = 0; $o < $max; $o++) {
            $p1 = $beacons[$o];
            for ($d = 0; $d < $max; $d++) {
                if ($o === $d || isset($treated["$o.$d"]) || isset($treated["$d.$o"])) {
                    continue;
                }
                $treated["$o.$d"] = 1;
                $treated["$d.$o"] = 1;

                $p2 = $beacons[$d];

                $vector = new Vector($p1, $p2);
                $vectors[spl_object_id($vector)] = $vector;
            }
        }

        return $vectors;
    }

    /**
     * @param Vector[] $vectors1
     * @param Vector[] $vectors2
     * @return array
     */
    private function getCommonVectors(array $vectors1, array $vectors2): array
    {
        $commonVectors = [];
        foreach ($vectors1 as $index1 => $vector1) {
            foreach ($vectors2 as $index2 => $vector2) {
                if ($vector1->squareSize() === $vector2->squareSize()) {
                    $commonVectors[] = [$index1 => $vector1, $index2 => $vector2];
                }
            }
        }

        return $commonVectors;
    }

    private function getTranslationVector(array $vectors, array $orientation): Vector
    {
        [$v1, $v2] = array_values(array_shift($vectors));

        if ($orientation['angle'] > 0) {
            $v1 = $v1->rotateOnAxis($orientation['axis'], $orientation['angle']);
            $v2 = $v2->rotateOnAxis($orientation['axis'], $orientation['angle']);
        }
        if ($orientation['angle']) {
            $v1 = $v1->mirrorOnAxis($orientation['axis']);
            $v2 = $v2->mirrorOnAxis($orientation['axis']);
        }

        return new Vector($v2->origin(), $v1->origin());
    }

    private function getOrientation(array $vectors): array
    {
        //~ Search for the same scanner orientation
        /**
         * @var Vector $v1
         * @var Vector $v2
         */
        [$v1, $v2] = array_values(array_shift($vectors));

        $axis     = 'x';
        $isMirror = false;

        for ($angle = 0.0; $angle < 360.0; $angle += 90.0) {
            foreach (['x', 'y', 'z'] as $axis) {
                $isMirror = false;
                $v = $v2->rotateOnAxis($axis, $angle);
                if ($v->isSameAs($v1)) {
                    break 2;
                }

                $v = $v->mirrorOnAxis($axis);
                if ($v->isSameAs($v1)) {
                    $isMirror = true;
                    break 2;
                }
            }
        }

        return ['axis' => $axis, 'angle' => $angle, 'isMirror' => $isMirror];
    }

    private function orientateAndTranslate(
        array $mapBeacons,
        array $beacons,
        array $orientation,
        Vector $translation
    ): array {

        foreach ($beacons as $index => $beacon) {
            if ($orientation['angle'] > 0) {
                $beacon = $beacon->rotateOnAxis($orientation['axis'], $orientation['angle']);
            }
            if ($orientation['angle']) {
                $beacon = $beacon->mirrorOnAxis($orientation['axis']);
            }

            $beacon = $beacon->translate($translation);
            $beacons[$index] = $beacon;

            if (!isset($mapBeacons[$beacon->getCoordinates()])) {
                $mapBeacons[$beacon->getCoordinates()] = $beacon;
            }
        }

        return $mapBeacons;
    }
}
