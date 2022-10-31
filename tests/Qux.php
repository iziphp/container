<?php

declare(strict_types=1);

namespace PhpStandard\Container\Tests;

/** @package PhpStandard\Container\Tests */
class Qux
{
    /**
     * @param FooInterface $foo 
     * @param string $bar 
     * @return void 
     */
    public function __construct(
        private FooInterface $foo,
        private string $bar = 'baz'
    ) {
    }
}
