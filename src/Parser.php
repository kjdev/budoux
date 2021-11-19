<?php

declare(strict_types=1);

namespace BudouX;

final class Parser
{
    private const MODEL_DIR = __DIR__ . '/models';

    private const RESULT_UNKNOWN = 'U';
    private const RESULT_POSITIVE = 'B';
    private const RESULT_NEGATIVE = 'O';

    /** @var array<string,int> */
    private array $model;

    private FeatureExtractor $featureExtractor;

    /**
     * @param array<string,int> $model
     */
    public function __construct(array $model)
    {
        $this->model = $model;

        $this->featureExtractor = new FeatureExtractor();
    }

    public static function loadDefaultJapanese(): self
    {
        $model = [];

        $modelFile = self::MODEL_DIR . '/ja-knbc.json';
        if (is_file($modelFile)) {
            $contents = file_get_contents($modelFile);
            if ($contents !== false) {
                $model = json_decode($contents, true);
            }
            if (! is_array($model)) {
                $model = [];
            }
        }

        return new self($model);
    }

    /**
     * @return array<string>
     */
    public function parse(
        string $sentence,
        int $thres = 100,
        string $encoding = 'utf8'
    ): array {
        if ($sentence === '') {
            return [];
        }

        $p1 = self::RESULT_UNKNOWN;
        $p2 = self::RESULT_UNKNOWN;
        $p3 = self::RESULT_UNKNOWN;

        $chunks = [mb_substr($sentence, 0, 3, $encoding)];

        $length = mb_strlen($sentence, $encoding);
        for ($i = 3; $i < $length; ++$i) {
            $feature = $this->featureExtractor->getFeature(
                mb_substr($sentence, $i - 3, 1, $encoding),
                mb_substr($sentence, $i - 2, 1, $encoding),
                mb_substr($sentence, $i - 1, 1, $encoding),
                mb_substr($sentence, $i, 1, $encoding),
                $i + 1 < $length
                ? mb_substr($sentence, $i + 1, 1, $encoding) : '',
                $i + 2 < $length
                ? mb_substr($sentence, $i + 2, 1, $encoding) : '',
                $p1,
                $p2,
                $p3,
                $encoding
            );
            $score = 0;

            foreach ($feature as $f) {
                if (array_key_exists($f, $this->model)) {
                    $score += $this->model[$f];
                }
            }

            $str = mb_substr($sentence, $i, 1, $encoding);

            if ($score > $thres) {
                $chunks[] = $str;
            } else {
                $chunks[count($chunks) - 1] .= $str;
            }

            $p = $score > 0
                ? self::RESULT_POSITIVE
                : self::RESULT_NEGATIVE;
            $p1 = $p2;
            $p2 = $p3;
            $p3 = $p;
        }

        return $chunks;
    }
}
