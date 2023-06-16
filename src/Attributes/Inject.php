<?php

namespace PhpStandard\Container\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Inject
{
    public function __construct(public mixed $abstract)
    {
    }
}
