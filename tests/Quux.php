<?php

declare(strict_types=1);

namespace PhpStandard\Container\Tests;

/** @package PhpStandard\Container\Tests */
class Quux
{
    /**
     * @param FooInterface $foo 
     * @param null|string $bar 
     * @return void 
     */
    public function __construct(
        private FooInterface $foo,
        private ?string $bar
    ) {
    }
}
