<?php

declare(strict_types=1);

namespace PhpStandard\Container\Tests;

/** @package PhpStandard\Container\Tests */
class Baz
{
    /**
     * @param FooInterface $foo 
     * @return void 
     */
    public function __construct(
        private FooInterface $foo
    ) {
    }
}
