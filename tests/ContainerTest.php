<?php

declare(strict_types=1);

namespace PhpStandard\Container\Tests;

use PhpStandard\Container\Container;
use PhpStandard\Container\Exceptions\ContainerException;
use PhpStandard\Container\Exceptions\NotFoundException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Throwable;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/** @package PhpStandard\Container\Tests */
class ContainerTest extends TestCase
{
    /**
     * @return void 
     * @throws NotFoundException 
     * @throws Throwable 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function testGetReturnsInstanceOfRequestedClass(): void
    {
        $container = new Container();
        $container->set('foo', Foo::class);

        $foo = $container->get('foo');

        $this->assertInstanceOf(Foo::class, $foo);
    }

    /**
     * @return void 
     * @throws NotFoundException 
     * @throws Throwable 
     */
    public function testGetThrowsNotFoundExceptionForUnknownIdentifier(): void
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);

        $container->get('unknown');
    }

    /**
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function testHasReturnsTrueForRegisteredIdentifier(): void
    {
        $container = new Container();
        $container->set('foo', Foo::class);

        $this->assertTrue($container->has('foo'));
    }

    /**
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function testHasReturnsFalseForUnknownIdentifier(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('unknown'));
    }

    /**
     * @return void 
     * @throws NotFoundException 
     * @throws Throwable 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function testSetSharedRegistersSharedInstance(): void
    {
        $container = new Container();
        $container->setShared('shared', Shared::class);

        $shared1 = $container->get('shared');
        $shared2 = $container->get('shared');

        $this->assertInstanceOf(Shared::class, $shared1);
        $this->assertSame($shared1, $shared2);
    }

    /**
     * @return void 
     * @throws NotFoundException 
     * @throws Throwable 
     * @throws ContainerException 
     * @throws ReflectionException 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function testCallMethodInvokesMethodOnInstance(): void
    {
        $container = new Container();
        $container->set('foo', Foo::class);

        $result = $container->callMethod('foo', 'bar');

        $this->assertSame('Hello, World!', $result);
    }

    /**
     * @return void 
     * @throws NotFoundException 
     * @throws Throwable 
     * @throws ContainerException 
     * @throws ReflectionException 
     */
    public function testCallMethodThrowsNotFoundExceptionForUnknownInstance(): void
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);

        $container->callMethod('unknown', 'bar');
    }

    /**
     * @return void 
     * @throws NotFoundException 
     * @throws Throwable 
     * @throws ContainerException 
     * @throws ReflectionException 
     */
    public function testCallMethodThrowsContainerExceptionForInvalidMethod(): void
    {
        $container = new Container();
        $container->set('invalid', Invalid::class);

        $this->expectException(ContainerException::class);

        $container->callMethod('invalid', 'invalidMethod');
    }
}
