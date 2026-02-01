<?php

declare(strict_types=1);

namespace App\Tests\Simple;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class SimpleLogicTest extends TestCase
{
    public function testStringIsConvertedToUppercase(): void
    {
        $input = 'phpunit';
        $result = strtoupper($input);

        $this->assertSame('PHPUNIT', $result);
    }

    public function testArrayContainsExpectedValue(): void
    {
        $data = [1, 2, 3, 4, 5];

        $this->assertContains(3, $data);
        $this->assertCount(5, $data);
    }

    public function testJsonEncodingProducesValidJson(): void
    {
        $payload = [
            'id' => 1,
            'name' => 'Test',
            'active' => true,
        ];

        $json = json_encode($payload);

        $this->assertJson($json);
        $this->assertStringContainsString('"active":true', $json);
    }

    public function testDateTimeDifferenceIsCalculatedCorrectly(): void
    {
        $start = new DateTimeImmutable('2025-01-01 10:00:00');
        $end   = new DateTimeImmutable('2025-01-01 12:30:00');

        $diffInMinutes = ($end->getTimestamp() - $start->getTimestamp()) / 60;

        $this->assertSame(150, $diffInMinutes);
    }
}
