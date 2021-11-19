<?php

declare(strict_types=1);

namespace BudouX;

final class FeatureExtractor
{
    /** @var array<mixed> */
    private array $blockStarts = [];

    public function __construct()
    {
        $jsonFile = __DIR__ . '/unicode_blocks.json';
        if (is_file($jsonFile)) {
            $contents = file_get_contents($jsonFile);
            if ($contents !== false) {
                $data = json_decode($contents, true);
                if (is_array($data)) {
                    $this->blockStarts = $data;
                }
            }
        }
    }

    /**
     * @return array<string>
     */
    public function getFeature(
        string $w1,
        string $w2,
        string $w3,
        string $w4,
        string $w5,
        string $w6,
        string $p1,
        string $p2,
        string $p3,
        string $encoding = 'utf8'
    ): array {
        $b1 = $this->b($w1, $encoding);
        $b2 = $this->b($w2, $encoding);
        $b3 = $this->b($w3, $encoding);
        $b4 = $this->b($w4, $encoding);
        $b5 = $this->b($w5, $encoding);
        $b6 = $this->b($w6, $encoding);

        $rawFeature = [
            'UP1' => $p1,
            'UP2' => $p2,
            'UP3' => $p3,
            'BP1' => $p1 . $p2,
            'BP2' => $p2 . $p3,
            'UW1' => $w1,
            'UW2' => $w2,
            'UW3' => $w3,
            'UW4' => $w4,
            'UW5' => $w5,
            'UW6' => $w6,
            'BW1' => $w2 . $w3,
            'BW2' => $w3 . $w4,
            'BW3' => $w4 . $w5,
            'TW1' => $w1 . $w2 . $w3,
            'TW2' => $w2 . $w3 . $w4,
            'TW3' => $w3 . $w4 . $w5,
            'TW4' => $w4 . $w5 . $w6,
            'UB1' => $b1,
            'UB2' => $b2,
            'UB3' => $b3,
            'UB4' => $b4,
            'UB5' => $b5,
            'UB6' => $b6,
            'BB1' => $b2 . $b3,
            'BB2' => $b3 . $b4,
            'BB3' => $b4 . $b5,
            'TB1' => $b1 . $b2 . $b3,
            'TB2' => $b2 . $b3 . $b4,
            'TB3' => $b3 . $b4 . $b5,
            'TB4' => $b4 . $b5 . $b6,
            'UQ1' => $p1 . $b1,
            'UQ2' => $p2 . $b2,
            'UQ3' => $p3 . $b3,
            'BQ1' => $p2 . $b2 . $b3,
            'BQ2' => $p2 . $b3 . $b4,
            'BQ3' => $p3 . $b2 . $b3,
            'BQ4' => $p3 . $b3 . $b4,
            'TQ1' => $p2 . $b1 . $b2 . $b3,
            'TQ2' => $p2 . $b2 . $b3 . $b4,
            'TQ3' => $p3 . $b1 . $b2 . $b3,
            'TQ4' => $p3 . $b2 . $b3 . $b4,
        ];
        if ($rawFeature['UW4'] === '') {
            unset($rawFeature['UW4']);
        }
        if ($rawFeature['UW5'] === '') {
            unset($rawFeature['UW5']);
        }
        if ($rawFeature['UW6'] === '') {
            unset($rawFeature['UW6']);
        }
        if ($rawFeature['BW3'] === '') {
            unset($rawFeature['BW3']);
        }
        if ($rawFeature['TW4'] === '') {
            unset($rawFeature['TW4']);
        }
        if ($rawFeature['UB4'] === '999') {
            unset($rawFeature['UB4']);
        }
        if ($rawFeature['UB5'] === '999') {
            unset($rawFeature['UB5']);
        }
        if ($rawFeature['UB6'] === '999') {
            unset($rawFeature['UB6']);
        }
        if ($rawFeature['BB3'] === '999999') {
            unset($rawFeature['BB3']);
        }
        if ($rawFeature['TB4'] === '999999999') {
            unset($rawFeature['TB4']);
        }

        return array_map(
            static fn (string $key, string $val): string => "{$key}:{$val}",
            array_keys($rawFeature),
            array_values($rawFeature)
        );
    }

    private function b(string $w, string $encoding = ''): string
    {
        return $w !== ''
            ? sprintf('%03d', $this->unicodeBlockIndex($w, $encoding))
            : '999';
    }

    private function unicodeBlockIndex(
        string $w,
        string $encoding = ''
    ): int {
        $unocode = mb_ord($w, $encoding);
        if ($unocode === false) {
            return 0;
        }
        return $this->bisectRight($this->blockStarts, $unocode);
    }

    /**
     * @param array<mixed> $arr
     */
    private function bisectRight(array $arr, int $unocode): int
    {
        $left = 0;
        $right = count($arr) - 1;

        while ($left < $right) {
            $mid = (int) floor(($left + $right) / 2);

            if ($arr[$mid] > $unocode) {
                $right = $mid;
            } else {
                $left = $mid + 1;
            }
        }

        return $left;
    }
}
