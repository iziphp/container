<?php

declare(strict_types=1);

namespace PhpStandard\Container\Tests;

use PhpStandard\Container\Configurator;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/** @package PhpStandard\Container\Tests */
class ConfiguratorTest extends TestCase
{
    /**
     * @test
     * @return Configurator 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function canCreateInstance(): Configurator
    {
        $configurator = new Configurator();
        $this->assertInstanceOf(Configurator::class, $configurator);
        return $configurator;
    }

    /**
     * @test 
     * @depends canCreateInstance
     * @param Configurator $configurator 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function canSetAbstract(Configurator $configurator): void
    {
        $this->assertInstanceOf(
            Configurator::class,
            $configurator->set('foo')
        );
    }

    /**
     * @test 
     * @depends canCreateInstance
     * @param Configurator $configurator 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function canSetConcrete(Configurator $configurator): void
    {
        $this->assertInstanceOf(
            Configurator::class,
            $configurator->set('bar', 'baz')
        );
    }

    /**
     * @test 
     * @depends canCreateInstance
     * @param Configurator $configurator 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function canSetShared(Configurator $configurator): void
    {
        $this->assertInstanceOf(
            Configurator::class,
            $configurator->setShared('qux', 'quux')
        );
    }

    /**
     * @test 
     * @depends canCreateInstance
     * @param Configurator $configurator 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function hasDefitionMethod(Configurator $configurator): void
    {
        $this->assertTrue($configurator->hasDefition('foo'));
        $this->assertTrue($configurator->hasDefition('bar'));
        $this->assertTrue($configurator->hasDefition('qux'));

        $configurator->set('corge', false);
        $this->assertTrue($configurator->hasDefition('corge'));

        $this->assertFalse($configurator->hasDefition('grault'));
    }

    /**
     * @test 
     * @depends canCreateInstance
     * @param Configurator $configurator 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function canGetDefinition(Configurator $configurator): void
    {
        $this->assertEquals('foo', $configurator->getDefinition('foo'));
        $this->assertEquals('baz', $configurator->getDefinition('bar'));
        $this->assertEquals('quux', $configurator->getDefinition('qux'));
    }

    /**
     * @test 
     * @depends canCreateInstance
     * @param Configurator $configurator 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function mustBeShared(Configurator $configurator): void
    {
        $this->assertTrue($configurator->isShared('qux'));
    }

    /**
     * @test 
     * @depends canCreateInstance
     * @param Configurator $configurator 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function mustNotBeShared(Configurator $configurator): void
    {
        $this->assertFalse($configurator->isShared('foo'));
        $this->assertFalse($configurator->isShared('bar'));
        $this->assertFalse($configurator->isShared('corge'));
    }

    /**
     * @test 
     * @depends canCreateInstance
     * @param Configurator $configurator 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws Exception 
     * @throws ExpectationFailedException 
     */
    public function canMarkAsResolved(Configurator $configurator): void
    {
        $this->assertInstanceOf(
            Configurator::class,
            $configurator->setResolved('foo', 'grault')
        );
    }

    /**
     * @test 
     * @depends canCreateInstance
     * @param Configurator $configurator 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function canGetResolved(Configurator $configurator): void
    {
        $this->assertEquals('grault', $configurator->getResolved('foo'));
    }

    /**
     * @test 
     * @depends canCreateInstance
     * @param Configurator $configurator 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function canCheckResolved(Configurator $configurator): void
    {
        $this->assertFalse($configurator->isResolved('bar'));
        $this->assertFalse($configurator->isResolved('qux'));
        $this->assertFalse($configurator->isResolved('corge'));
        $this->assertTrue($configurator->isResolved('foo'));
    }
}
