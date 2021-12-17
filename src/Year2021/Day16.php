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

/**
 * Class Day16
 *
 * @author Romain Cottard
 */
class Day16 implements AlgorithmInterface
{
    public function getExamples(string $star): array
    {
        $examples = [
            '*'  => [
                //[6   => ['D2FE28']],
                //[1   => ['38006F45291200']],
                //[7   => ['EE00D40C823060']],
                [16  => ['8A004A801A8002F478']],
                [12  => ['620080001611562C8802118E34']],
                [23  => ['C0015000016115A2E0802F182340']],
                [31  => ['A0016C880162017C3686B18A3D4780']],
            ],
            '**' => [
                [16  => ['8A004A801A8002F478']],
                [12  => ['620080001611562C8802118E34']],
                [23  => ['C0015000016115A2E0802F182340']],
                [31  => ['A0016C880162017C3686B18A3D4780']],
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
        $BITS = $this->hex2bin($inputs[0]);

        echo ">> $BITS <<\n";

        [$version, $type, $subpackets] = $this->readPacket($BITS);

        echo "\$version: $version\n";
        echo "\$type:    $type\n";
        echo "\$content: " . implode('|', $subpackets) . "\n";

        return 0;
    }

    public function readPacket(string $packet): array
    {
        $version = (int) bindec(substr($packet, 0, 3));
        $type    = (int) bindec(substr($packet, 3, 3));
        $content = substr($packet, 6);

        if ($type === 4) {
            $chunks = str_split($content, 5);
            $content = '';
            foreach ($chunks as $chunk) {
                $content .= substr($chunk, 1);
                if ($chunk[0] === '0') {
                    break;
                }
            }
            return [$version, $type, [$content]];
        }

        $typeLength = substr($content, 0, 1);

        if ($typeLength === '0') {
            $length = bindec(substr($content, 1, 15));
            echo '$length: ' . $length . PHP_EOL;
            $content = substr($content, 16, $length);
            return [$version, $type, [
                substr($content, 0, 11),
                substr($content, 11)]
            ];
        }

        $number  = bindec(substr($content, 1, 11));
        echo '$number: ' . $number . PHP_EOL;
        $content = substr($content, 12);
        return [$version, $type, array_slice(str_split($content, 11), 0, $number)];
    }

    private function readPacketLiteral(string $bits)
    {
        $packets = str_split($bits, 5);

        $value = '';
        foreach ($packets as $packet) {
            $value .= substr($packet, 1);
            if ($packet[0] === '0') {
                break;
            }
        }

        return $value;
    }

    private function hex2bin(string $hex): string
    {
        return implode('', array_map(fn($char) => str_pad(decbin(hexdec($char)), 4, '0', STR_PAD_LEFT), str_split(strtolower($hex))));
    }

    private function starTwo(array $inputs): int
    {
        return 0;
    }
}
