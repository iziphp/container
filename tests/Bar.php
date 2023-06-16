<?php

declare(strict_types=1);

namespace PhpStandard\Container\Tests;

use PhpStandard\Container\Attributes\Inject;

class Bar
{
    public function __construct(
        public readonly Foo $foo,

        #[Inject('injected_primitive_value')]
        public readonly string $primitive
    ) {
    }
}
