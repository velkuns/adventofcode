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
                [6   => ['D2FE28']],
                [16  => ['8A004A801A8002F478']],
                [12  => ['620080001611562C8802118E34']],
                [23  => ['C0015000016115A2E0802F182340']],
                [31  => ['A0016C880162017C3686B18A3D4780']],
            ],
            '**' => [
                [3  => ['C200B40A82']],
                [54 => ['04005AC33890']],
                [7  => ['880086C3E88112']],
                [9  => ['CE00C43D881120']],
                [1  => ['D8005AC2A8F0']],
                [0  => ['F600BC2D8F']],
                [0  => ['9C005AC2F8F0']],
                [1  => ['9C0141080250320F1802104A08']],
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
        $packet = $this->readPacket($this->hex2bin($inputs[0]));

        //$this->display($packet['v'], $packet['t'], $packet['e'], $packet['c'], $packet['u']);

        return $this->sumVersions($packet);
    }

    private function starTwo(array $inputs): int
    {
        $packet = $this->readPacket($this->hex2bin($inputs[0]));

        //$this->display($packet['v'], $packet['t'], $packet['e'], $packet['c'], $packet['u']);

        return $this->evaluate($packet);
    }

    public function readPacket(string $packet): array
    {
        $version = (int) bindec(substr($packet, 0, 3));
        $type    = (int) bindec(substr($packet, 3, 3));
        $content = substr($packet, 6);

        if ($type === 4) {
            return $this->readLit($version, $type, $content);
        }

        $typeLength = substr($content, 0, 1);

        if ($typeLength === '0') {
            $length  = bindec(substr($content, 1, 15));
            $content = substr($content, 16);
            return $this->readOpTypeLength($version, $type, $length, $content);
        }

        $number  = bindec(substr($content, 1, 11));
        $content = substr($content, 12);
        return $this->readOpTypeNumber($version, $type, $number, $content);
    }

    private function readLit(int $version, int $type, string $content): array
    {
        $chunks = str_split($content, 5);
        $content = '';
        foreach ($chunks as $index => $chunk) {
            $content .= substr($chunk, 1);
            unset($chunks[$index]);
            if ($chunk[0] === '0') {
                break;
            }
        }

        $content = (int) bindec($content);
        $unread = implode('', $chunks);

        return ['v' => $version, 't' => $type, 'e' => 'lit', 'c' => [$content], 'u' => $unread];
    }

    private function readOpTypeLength(int $version, int $type, int $length, string $content): array
    {
        $packet  = ['v' => $version, 't' => $type, 'e' => 'length (' . $length . ')', 'c' => [], 'u' => substr($content, $length)];
        $content = substr($content, 0, $length);

        while (true) {
            $subpacket = $this->readPacket($content);
            $packet['c'][] = $subpacket;
            if ($subpacket['u'] < 11) {
                break;
            }

            $content = $subpacket['u'];
        }

        return $packet;
    }

    private function readOpTypeNumber(int $version, int $type, int $number, string $content): array
    {
        $packet = ['v' => $version, 't' => $type, 'e' => 'number (' . $number . ')', 'c' => [], 'u' => ''];
        for ($n = 0; $n < $number; $n++) {
            $subpacket     = $this->readPacket($content);
            $packet['c'][] = $subpacket;
            $content       = $subpacket['u'];
        }

        $packet['u'] = $subpacket['u'] ?? '';

        return $packet;
    }

    private function sumVersions(array $packet): int
    {
        $version = $packet['v'];

        foreach ($packet['c'] as $subpacket) {
            if (is_array($subpacket)) {
                $version += $this->sumVersions($subpacket);
            }
        }

        return $version;
    }

    private function evaluate(array $packet)
    {
        $values = [];
        foreach ($packet['c'] as $subpacket) {
            if (is_array($subpacket)) {
                $values[] = $this->evaluate($subpacket);
            } else {
                $values = $subpacket;
            }
        }

        switch ($packet['t']) {
            case 0:
                $value = array_sum($values);
                break;
            case 1:
                $value = array_product($values);
                break;
            case 2:
                $value = min($values);
                break;
            case 3:
                $value = max($values);
                break;
            case 5:
                $value = (int) ($values[0] > $values[1]);
                break;
            case 6:
                $value = (int) ($values[0] < $values[1]);
                break;
            case 7:
                $value = (int) ($values[0] === $values[1]);
                break;
            case 4:
            default:
                $value = $values;
        }

        return $value;
    }

    private function display(int $version, int $type, string $extra, array $subpackets, string $unread, int $depth = 0): void
    {
        $padding = str_repeat('  ', $depth);
        echo "$padding>>>>>>>> PACKET <<<<<<<<<<<\n";
        echo "$padding\$version: $version\n";
        echo "$padding\$type:    $type\n";
        echo "$padding\$extra:   $extra\n";
        foreach ($subpackets as $subpacket) {
            if (is_array($subpacket)) {
                $this->display($subpacket['v'], $subpacket['t'], $subpacket['e'], $subpacket['c'], $subpacket['u'], $depth + 1);
            } else {
                echo "$padding - $subpacket\n";
            }
        }
        echo "$padding\$unread:  $unread\n";
    }

    private function hex2bin(string $hex): string
    {
        return implode('', array_map(fn($char) => str_pad(decbin(hexdec($char)), 4, '0', STR_PAD_LEFT), str_split(strtolower($hex))));
    }
}
