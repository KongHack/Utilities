<?php

namespace GCWorld\Utilities\Tests;

use DateInterval;
use GCWorld\Utilities\DoubleMetaphone;
use GCWorld\Utilities\Tests\Fixtures\UtilitiesHarness;
use PHPUnit\Framework\TestCase;

final class UtilitiesTest extends TestCase
{
    public function testBase64UrlRoundTrip(): void
    {
        $encoded = UtilitiesHarness::base64url_encode('Hello, utilities!');

        self::assertSame('SGVsbG8sIHV0aWxpdGllcyE', $encoded);
        self::assertSame('Hello, utilities!', UtilitiesHarness::base64url_decode($encoded));
    }

    public function testStringAndArrayHelpers(): void
    {
        self::assertSame('*alpha*beta*', UtilitiesHarness::starImplode(['alpha', 'beta']));
        self::assertSame(['two', 'Words'], UtilitiesHarness::getNamePieces('twoWords'));
        self::assertSame(['one', 'two words'], UtilitiesHarness::searchSplit('one "two words"'));
    }

    public function testGeneralHelpers(): void
    {
        self::assertSame(795, UtilitiesHarness::encode(1));
        self::assertSame(1.0, UtilitiesHarness::decode(795));
        self::assertSame('01:01:01', UtilitiesHarness::integerToTime(3661));
        self::assertSame('file.txt', UtilitiesHarness::getFileNameFromPath('/tmp/file.txt'));
        self::assertNull(UtilitiesHarness::getLastArrayValue([]));
        self::assertSame(2, UtilitiesHarness::getLastArrayValue(['first' => 1, 'last' => 2]));
    }

    public function testTimeHelpers(): void
    {
        self::assertSame('01:05', UtilitiesHarness::formatTime('1:5:30'));
        self::assertSame('2:05', UtilitiesHarness::minutesToHoursAndMinutes(125));
        self::assertTrue(UtilitiesHarness::isProperDate('2026-09-16'));
        self::assertFalse(UtilitiesHarness::isProperDate('2026-02-30'));
        self::assertSame(
            '1 day 2 hours',
            UtilitiesHarness::formatInterval(new DateInterval('P1DT2H'), ['d', 'h'])
        );
        self::assertSame('', UtilitiesHarness::formatInterval(new DateInterval('P0D')));
        self::assertSame('1 year 1 month', UtilitiesHarness::formatInterval(new DateInterval('P1Y1M')));
        self::assertSame('1 day', UtilitiesHarness::timeAgo('12/31/2025', '01/01/2026'));
    }

    public function testJsonAndDisplayHelpers(): void
    {
        self::assertSame(['enabled' => true], UtilitiesHarness::safe_json_decode('{"enabled":true}'));
        self::assertSame('{"enabled":true}', UtilitiesHarness::json_encode(['enabled' => true]));
        self::assertSame('0', UtilitiesHarness::json_encode(new class implements \JsonSerializable {
            public function jsonSerialize(): mixed
            {
                return 0;
            }
        }));
        self::assertStringContainsString('value', UtilitiesHarness::renderFancyArrayValue(['key' => 'value']));
        self::assertSame(0x336699, UtilitiesHarness::HTMLToRGB('#336699'));
    }

    public function testDoubleMetaphoneProducesBothCodes(): void
    {
        $metaphone = new DoubleMetaphone('Smith');

        self::assertNotSame('', $metaphone->getPrimary());
        self::assertNotSame('', $metaphone->getSecondary());
    }
}
