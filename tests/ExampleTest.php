<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /** @test */
    public function assertTrueValue(): void
    {
        self::assertTrue(true);
    }
}
