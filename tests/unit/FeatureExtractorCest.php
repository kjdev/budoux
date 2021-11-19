<?php


declare(strict_types=1);

use BudouX\FeatureExtractor;

class FeatureExtractorCest
{
    public function test1(UnitTester $I): void
    {
        // 'a' falls the 1st block 'Basic Latin'.
        $this->unicodeBlockIndex($I, 1, 'a');
        // 'あ' falls the 108th block 'Hiragana'.
        $this->unicodeBlockIndex($I, 108, 'あ');
        // '安' falls the 120th block 'Kanji'.
        $this->unicodeBlockIndex($I, 120, '安');
    }

    public function test2(UnitTester $I): void
    {
        $featureExtractor = new FeatureExtractor();
        $feature = $featureExtractor->getFeature(
            'a', 'b', 'c', 'd', 'e', 'f', 'x', 'y' ,'z'
        );
        $I->assertEqualsCanonicalizing(
            [
                // Unigram of Words (UW)
                'UW1:a',
                'UW2:b',
                'UW3:c',
                'UW4:d',
                'UW5:e',
                'UW6:f',
                // Unigram of Previous Results (UP)
                'UP1:x',
                'UP2:y',
                'UP3:z',
                // Unigram of Unicode Blocks (UB)
                'UB1:001',
                'UB2:001',
                'UB3:001',
                'UB4:001',
                'UB5:001',
                'UB6:001',
                // Combination of UW and UP
                'UQ1:x001',
                'UQ2:y001',
                'UQ3:z001',
                // Bigram of Words (BW), Previous Results (BP),
                // Unicode Blocks (BB), and its combination (BQ)
                'BW1:bc',
                'BW2:cd',
                'BW3:de',
                'BP1:xy',
                'BP2:yz',
                'BB1:001001',
                'BB2:001001',
                'BB3:001001',
                'BQ1:y001001',
                'BQ2:y001001',
                'BQ3:z001001',
                'BQ4:z001001',
                // Trigram of Words (BW), Previous Results (BP),
                // Unicode Blocks (BB), and its combination (BQ)
                'TW1:abc',
                'TW2:bcd',
                'TW3:cde',
                'TW4:def',
                'TB1:001001001',
                'TB2:001001001',
                'TB3:001001001',
                'TB4:001001001',
                'TQ1:y001001001',
                'TQ2:y001001001',
                'TQ3:z001001001',
                'TQ4:z001001001',
            ],
            $feature
        );
    }

    public function test3(UnitTester $I): void
    {
        $featureExtractor = new FeatureExtractor();
        $feature = $featureExtractor->getFeature(
            '', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a'
        );
        $I->assertContains('UW1:', $feature);
        $I->assertContains('UB1:999', $feature);
    }

    public function test4(UnitTester $I): void
    {
        $featureExtractor = new FeatureExtractor();
        $feature = $featureExtractor->getFeature(
            'a', 'a', 'a', '', '', '', 'b', 'b', 'b'
        );
        $I->assertNotContains('UW4:', $feature);
        $I->assertNotContains('UB4:999', $feature);
        $I->assertNotContains('BB3:999999', $feature);
    }

    private function unicodeBlockIndex(
        UnitTester $I,
        int $expected,
        string $character
    ): void {
        $featureExtractor = new FeatureExtractor();
        $reflection = new ReflectionClass($featureExtractor);
        $method = $reflection->getMethod('unicodeBlockIndex');
        $method->setAccessible(true);

        $I->assertSame(
            $expected,
            $method->invoke($featureExtractor, $character, 'utf8')
        );
    }
}
