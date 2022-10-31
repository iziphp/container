<?php

declare(strict_types=1);

namespace PhpStandard\Container\Tests;

use DateTime;
use PhpStandard\Container\Configurator;
use PhpStandard\Container\Container;
use PhpStandard\Container\Exceptions\NotFoundException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/** @package PhpStandard\Container\Tests */
class ContainerTest extends TestCase
{
    private static Configurator $configurator;
    private static ContainerInterface $container;

    public static function setUpBeforeClass(): void
    {
        self::$configurator = new Configurator();
        self::$container = new Container(self::$configurator);
    }

    /**
     * @test
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function canAutowireInterface()
    {
        $this->assertTrue(self::$container->has(FooInterface::class));
    }

    /**
     * @test
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function canAutowireClass()
    {
        $this->assertTrue(self::$container->has(Foo::class));
    }

    /**
     * @test
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function canAutowireBuiltinClass()
    {
        $this->assertTrue(self::$container->has(DateTime::class));
    }

    /**
     * @test
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function canCheckDefinitions()
    {
        $abstract = uniqid();
        $concrete = uniqid();

        self::$configurator->set($abstract, $concrete);
        $this->assertTrue(self::$container->has($abstract));
    }

    /**
     * @test
     * @return void 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function canResolveBuiltinClasses()
    {
        $this->assertInstanceOf(
            DateTime::class,
            self::$container->get(DateTime::class)
        );
    }

    /**
     * @test
     * @return void 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function canResolveConcreteClass()
    {
        $this->assertInstanceOf(
            Foo::class,
            self::$container->get(Foo::class)
        );
    }

    /**
     * @test
     * @return void 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function canResolveByAbstract()
    {
        self::$configurator->set(FooInterface::class, Foo::class);
        $this->assertInstanceOf(
            Foo::class,
            self::$container->get(FooInterface::class)
        );
    }

    /**
     * @test
     * @depends canResolveByAbstract
     * @return void 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function canInjectAbstract()
    {
        $this->assertInstanceOf(
            Baz::class,
            self::$container->get(Baz::class)
        );
    }

    /**
     * @test
     * @depends canResolveByAbstract
     * @return void 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function canInjectDefaultValue()
    {
        $this->assertInstanceOf(
            Qux::class,
            self::$container->get(Qux::class)
        );
    }

    /**
     * @test
     * @depends canResolveByAbstract
     * @return void 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function canInjectNullValue()
    {
        $this->assertInstanceOf(
            Quux::class,
            self::$container->get(Quux::class)
        );
    }

    /**
     * @test
     * @depends canResolveByAbstract
     * @return void 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     */
    public function canThrowNotFoundException()
    {
        $this->expectException(NotFoundException::class);
        self::$container->get(uniqid());
    }
}
