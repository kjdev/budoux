<?php

declare(strict_types=1);

use BudouX\Parser;

class ParseCest
{
    private const TEST_SENTENCE = 'abcdeabcd';

    public function test1(UnitTester $I): void
    {
        $p = new Parser([
            'UW4:a' => 10000,
        ]);
        $chunks = $p->parse(self::TEST_SENTENCE);
        $I->assertSame(
            ['abcde', 'abcd'],
            $chunks
        );
    }

    public function test2(UnitTester $I): void
    {
        $p = new Parser([
            'BP2:UU' => 10000,
        ]);
        $chunks = $p->parse(self::TEST_SENTENCE);
        $I->assertSame(
            ['abc', 'deabcd'],
            $chunks
        );
    }

    public function test3(UnitTester $I): void
    {
        $p = new Parser([
            'UW4:a' => 10,
        ]);
        $chunks = $p->parse(self::TEST_SENTENCE);
        $I->assertSame(
            [self::TEST_SENTENCE],
            $chunks
        );
    }

    public function test4(UnitTester $I): void
    {
        $p = new Parser([]);
        $chunks = $p->parse('');
        $I->assertSame(
            [],
            $chunks
        );
    }

}
