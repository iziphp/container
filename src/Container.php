<?php

declare(strict_types=1);

namespace PhpStandard\Container;

use Closure;
use PhpStandard\Container\Exceptions\ContainerException;
use PhpStandard\Container\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Throwable;

/** @package PhpStandard\Container */
class Container implements ContainerInterface
{
    /**
     * Registered definitions
     * @var array<string,mixed>
     */
    private array $definitions = [];

    /**
     * Identifier for the registered shared (singleton) services
     * @var array<string,mixed>
     */
    private array $shared = [];

    /**
     * Resolved shared services
     * @var array<string,mixed>
     */
    private array $resolved = [];

    /** @return void  */
    public function __construct()
    {
        $this
            ->set(ContainerInterface::class, $this)
            ->set(Container::class, $this);

        $this->resolved[ContainerInterface::class] = $this;
        $this->resolved[Container::class] = $this;
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @inheritDoc
     */
    public function get(string $id)
    {
        try {
            if (isset($this->resolved[$id])) {
                return $this->resolved[$id];
            }

            return $this->resolve($id);
        } catch (Throwable $th) {
            if (!$this->has($id)) {
                throw new NotFoundException($id, 0, $th);
            }

            throw $th;
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        if (isset($this->definitions[$id])) {
            return true;
        }

        try {
            $this->getReflector($id);
        } catch (Throwable $th) {
            return false;
        }

        return true;
    }

    /**
     * @param string $abstract
     * @param mixed $concrete
     * @return Container
     */
    public function set(
        string $abstract,
        mixed $concrete = null
    ): self {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->definitions[$abstract] = $concrete;
        return $this;
    }

    /**
     * @param string $abstract
     * @param mixed $concrete
     * @return Container
     */
    public function setShared(
        string $abstract,
        mixed $concrete = null
    ): self {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->definitions[$abstract] = $concrete;

        if (!isset($this->shared[$abstract])) {
            $this->shared[$abstract] = true;
        }

        return $this;
    }

    /**
     * @param string|object $instance
     * @param string $methodName
     * @return mixed
     * @throws NotFoundException
     * @throws Throwable
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function callMethod(
        string|object $instance,
        string $methodName
    ): mixed {
        if (is_string($instance)) {
            $instance = $this->get($instance);
        }

        $reflector = new ReflectionClass($instance);

        try {
            $method = $reflector->getMethod($methodName);
        } catch (Throwable $th) {
            throw new ContainerException(sprintf(
                "%s does not exists on",
                get_class($instance) . '::' . $methodName . '()'
            ), 0, $th);
        }

        $params = $this->getResolvedParameters($method);
        return $method->invokeArgs($instance, $params);
    }

    /**
     * @param string $abstract
     * @return mixed
     * @throws ContainerException
     * @throws ReflectionException
     * @throws NotFoundException
     * @throws Throwable
     */
    private function resolve(string $abstract): mixed
    {
        $isShared = isset($this->shared[$abstract]);
        $isDefined = isset($this->definitions[$abstract]);

        $entry = $abstract;
        if ($isDefined) {
            $entry = $this->definitions[$abstract];

            if (is_object($entry)) {
                return $entry;
            }

            /** @phpstan-ignore-next-line */
            if ($entry instanceof Closure) {
                return $entry($this);
            }

            if (is_callable($entry)) {
                return $entry();
            }
        }

        try {
            $reflector = $this->getReflector($entry);
            $instance = $this->getInstance($reflector);
        } catch (Throwable $th) {
            if ($isDefined) {
                return $this->definitions[$abstract];
            }

            throw new ContainerException("{$abstract} is not resolvable", 0, $th);
        }

        if ($isShared || !$isDefined) {
            // Save shared or autowired instances to resolved cache
            $this->resolved[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Get a ReflectionClass object representing the entry's class
     *
     * @param string $entry
     * @return ReflectionClass
     */
    private function getReflector(string $entry): ReflectionClass
    {
        return new ReflectionClass($entry);
    }

    /**
     * Get an instance for the entry
     *
     * @param ReflectionClass $item
     * @return object
     * @throws ContainerException
     * @throws ReflectionException
     * @throws NotFoundException
     * @throws Throwable
     */
    private function getInstance(ReflectionClass $item): object
    {
        if (!$item->isInstantiable()) {
            throw new ContainerException("{$item->name} is not instantiable");
        }

        $constructor = $item->getConstructor();

        if (is_null($constructor)) {
            return $item->newInstance();
        }

        $params = $this->getResolvedParameters($constructor);
        return $item->newInstanceArgs($params);
    }

    /**
     * Get array of the resolved params
     *
     * @param ReflectionMethod $method
     * @return array
     * @throws NotFoundException
     * @throws Throwable
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function getResolvedParameters(ReflectionMethod $method): array
    {
        $params = [];
        foreach ($method->getParameters() as $param) {
            $params[] = $this->resolveParameter($param);
        }

        return $params;
    }

    /**
     * Resolve constructor parameter
     *
     * @param ReflectionParameter $parameter
     * @return mixed
     * @throws NotFoundException
     * @throws Throwable
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function resolveParameter(ReflectionParameter $parameter)
    {
        if ($this->has($parameter->name)) {
            return $this->get($parameter->name);
        }

        $type = $parameter->getType();

        if ($type !== null) {
            assert($type instanceof ReflectionNamedType);

            if (!$type->isBuiltin() && $this->has($type->getName())) {
                return $this->get($type->getName());
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->allowsNull()) {
            return null;
        }

        throw new ContainerException(
            "Parameter \"{$type->getName()} \${$parameter->name}\" can't be instatiated and yet has no default value"
        );
    }
}
