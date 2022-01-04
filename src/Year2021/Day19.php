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
                [3621 => [
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

        $totalTranslation = new Vector( new Point(0, 0, 0), new Point(0, 0, 0));
        $pile = [
            ['vectors' => $vectors[0], 'zone' => 0, 'translation' => $totalTranslation]
        ];

        $zones = [0];

        while (!empty($pile)) {
            /*echo ' >>>> IN PILE <<<<' . PHP_EOL;
            foreach ($pile as $data) {
                echo ' * zone #' . $data['zone'] . PHP_EOL;
            }*/
            [$vectors1, $zone1, $totalTranslation] = array_values(array_pop($pile));

            $zonesWithOverlap = [];
            for ($zone2 = 0; $zone2 < $max; $zone2++) {
                if ($zone2 === $zone1 || in_array($zone2, $zones)) {
                    //echo "Skip zone#{$zone1} <=> zone#{$zone2} (in zones: " . implode(',', $zones) . ")\n";
                    continue; // already compared or is the same zone
                }

                $vectors2 = $vectors[$zone2];

                $commonVectors = $this->getCommonVectors($vectors1, $vectors2);
                $count         = count($commonVectors);
                if ($count < 12) {
                    //echo "skip zone#{$zone1} <=> zone#{$zone2} (count: $count, zones: " . implode(',', $zones) . ")\n";
                    continue;
                }

                $zonesWithOverlap[$zone2] = $commonVectors;
            }

            foreach ($zonesWithOverlap as $zone2 => $commonVectors) {

                [$v1, $v2] = array_values(array_shift($commonVectors));
                $count     = count($commonVectors);

                //echo "scanner #$zone1 overlaps scanner #$zone2 (count: $count)\n";

                //~ re-orientate beacons position in zone 2.
                $orientation = $this->getOrientation($v1, $v2);
                $translation = $this->getTranslationVector($v1, $v2, $orientation);
                //echo "Orientation: " . implode('|', $orientation) . PHP_EOL;

                $newTotalTranslation = $totalTranslation->add($translation);

                $mapBeacons      = $this->searchForNewBeacons($mapBeacons, $beacons[$zone2], $orientation, $newTotalTranslation);
                $beacons[$zone2] = $this->orientateBeacons($beacons[$zone2], $orientation);
                $vectors2        = $this->createVectors($beacons[$zone2]);

                $pile[$zone2] = ['vectors' => $vectors2, 'zone' => $zone2, 'translation' => $newTotalTranslation];

                $zones[] = $zone2;
                sort($zones);
            }
        }

        return count($mapBeacons);
    }

    private function starTwo(array $beacons): int
    {
        $scanners = [0 => new Point(0, 0, 0)];
        $vectors  = $this->beaconsToVectors($beacons);
        $max      = count($vectors);

        $totalTranslation = new Vector( new Point(0, 0, 0), new Point(0, 0, 0));
        $pile = [
            ['vectors' => $vectors[0], 'zone' => 0, 'translation' => $totalTranslation]
        ];

        $zones = [0];

        while (!empty($pile)) {
            echo ' >>>> IN PILE <<<<' . PHP_EOL;
            foreach ($pile as $data) {
                echo ' * zone #' . $data['zone'] . PHP_EOL;
            }
            [$vectors1, $zone1, $totalTranslation] = array_values(array_pop($pile));

            $zonesWithOverlap = [];
            for ($zone2 = 0; $zone2 < $max; $zone2++) {
                if ($zone2 === $zone1 || in_array($zone2, $zones)) {
                    //echo "Skip zone#{$zone1} <=> zone#{$zone2} (in zones: " . implode(',', $zones) . ")\n";
                    continue; // already compared or is the same zone
                }

                $vectors2 = $vectors[$zone2];

                $commonVectors = $this->getCommonVectors($vectors1, $vectors2);
                $count         = count($commonVectors);
                if ($count < 12) {
                    //echo "skip zone#{$zone1} <=> zone#{$zone2} (count: $count, zones: " . implode(',', $zones) . ")\n";
                    continue;
                }

                $zonesWithOverlap[$zone2] = $commonVectors;
            }

            foreach ($zonesWithOverlap as $zone2 => $commonVectors) {

                [$v1, $v2] = array_values(array_shift($commonVectors));
                $count     = count($commonVectors);

                echo "scanner #$zone1 overlaps scanner #$zone2 (count: $count)\n";

                //~ re-orientate beacons position in zone 2.
                $orientation = $this->getOrientation($v1, $v2);
                $translation = $this->getTranslationVector($v1, $v2, $orientation);

                $newTotalTranslation = $totalTranslation->add($translation);

                $beacons[$zone2] = $this->orientateBeacons($beacons[$zone2], $orientation);
                $vectors2        = $this->createVectors($beacons[$zone2]);

                $scanner = (new Point(0, 0, 0))->translate($newTotalTranslation);
                echo "scanner#$zone2 : $scanner\n";
                $scanners[$zone2] = $scanner;

                $pile[$zone2] = ['vectors' => $vectors2, 'zone' => $zone2, 'translation' => $newTotalTranslation];

                $zones[] = $zone2;
                sort($zones);
            }
        }


        /** @var Vector $longest */
        $longest = null;
        $vectors = $this->createVectors($scanners);
        foreach ($vectors as $vector) {
            echo "Vector: $vector\n";
            if ($longest === null || $longest->manhattanDistance() < $vector->manhattanDistance()) {
                $longest = $vector;
            }
        }

        $value = $longest->getX() + $longest->getY() + $longest->getZ();

        echo "longest: {$longest->getX()} + {$longest->getY()} + {$longest->getZ()} = $value (vector: $longest)" . PHP_EOL;

        return $value;
    }

    private function beaconsToVectors(array $beacons): array
    {
        $vectors = [];
        foreach ($beacons as $scanner => $positions) {
            $vectors[$scanner] = $this->createVectors($positions);
        }

        return $vectors;
    }

    /**
     * @param Point[] $points
     * @return Vector[]
     */
    private function createVectors(array $points): array
    {
        $vectors = [];
        $max     = count($points);

        for ($o = 0; $o < $max; $o++) {
            $p1 = $points[$o];
            for ($d = 0; $d < $max; $d++) {
                if ($o === $d || isset($treated["$o.$d"]) || isset($treated["$d.$o"])) {
                    continue;
                }
                $treated["$o.$d"] = 1;
                $treated["$d.$o"] = 1;

                $p2 = $points[$d];

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

    private function getTranslationVector(Vector $v1, Vector $v2, array $orientation): Vector
    {
        $v = $v2
            ->rotateOnAxis($orientation['axis1'], $orientation['angle1'])
            ->rotateOnAxis($orientation['axis2'], $orientation['angle2'])
        ;

        return new Vector($v->origin(), $v1->origin(), false);
    }

    /**
     * Search for the same scanner orientation
     *
     * @param Vector $v1
     * @param Vector $v2
     * @return array
     */
    private function getOrientation(Vector $v1, Vector $v2): array
    {
        $axis1 = 'y';
        $axis  = ['x', 'z', 'x', 'z'];
        for ($angle1 = 0.0; $angle1 < 360.0; $angle1 += 90.0) {
            $v2b   = $v2->rotateOnAxis($axis1, $angle1);
            $axis2 = array_shift($axis);
            for ($angle2 = 0.0; $angle2 < 360; $angle2 += 90) {
                $v = $v2b->rotateOnAxis($axis2, $angle2);

                if ($v->isSameAs($v1)) {
                    return ['axis1' => $axis1, 'angle1' => $angle1, 'axis2' => $axis2, 'angle2' => $angle2];
                }
            }
        }

        $angle2 = 0.0;
        $axis1  = 'z';
        $axis2  = 'y';
        for ($angle1 = 90.0; $angle1 < 360.0; $angle1 += 180.0) {
            $v2b = $v2->rotateOnAxis($axis1, $angle1);
            for ($angle2 = 0.0; $angle2 < 360; $angle2 += 90) {
                $v = $v2b->rotateOnAxis($axis2, $angle2);

                if ($v->isSameAs($v1)) {
                    break 2;
                }
            }
        }

        return ['axis1' => $axis1, 'angle1' => $angle1, 'axis2' => $axis2, 'angle2' => $angle2];
    }

    /**
     * @param Point[] $mapBeacons
     * @param Point[] $beacons
     * @param array $orientation
     * @param Vector $translation
     * @return Point[]
     */
    private function searchForNewBeacons(
        array $mapBeacons,
        array $beacons,
        array $orientation,
        Vector $translation
    ): array {

        foreach ($beacons as $beacon) {
            $beacon = $beacon
                ->rotateOnAxis($orientation['axis1'], $orientation['angle1'])
                ->rotateOnAxis($orientation['axis2'], $orientation['angle2'])
                ->translate($translation)
            ;

            if (!isset($mapBeacons[$beacon->getCoordinates()])) {
                $mapBeacons[$beacon->getCoordinates()] = $beacon;
            }
        }

        return $mapBeacons;
    }

    /**
     * @param Point[] $beacons
     * @param array $orientation
     * @return Point[]
     */
    private function orientateBeacons(
        array $beacons,
        array $orientation
    ): array {

        foreach ($beacons as $index => $beacon) {
            $beacons[$index] = $beacon
                ->rotateOnAxis($orientation['axis1'], $orientation['angle1'])
                ->rotateOnAxis($orientation['axis2'], $orientation['angle2'])
            ;
        }

        return $beacons;
    }
}
