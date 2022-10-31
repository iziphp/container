<?php

namespace PhpStandard\Container;

/** @package PhpStandard\Container */
class Configurator
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

    /**
     * @param string $abstract
     * @param mixed $concrete
     * @return Configurator
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
     * @return Configurator
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
     * @param string $abstract
     * @return bool
     */
    public function hasDefition(string $abstract): bool
    {
        return isset($this->definitions[$abstract]);
    }

    /**
     * @param string $abstract
     * @return mixed
     */
    public function getDefinition(string $abstract): mixed
    {
        return $this->definitions[$abstract];
    }

    /**
     * @param string $abstract
     * @return bool
     */
    public function isShared(string $abstract): bool
    {
        return isset($this->shared[$abstract]);
    }

    /**
     * @param string $abstract
     * @return bool
     */
    public function isResolved(string $abstract): bool
    {
        return isset($this->resolved[$abstract]);
    }

    /**
     * @param string $abstract
     * @param mixed $concrete
     * @return Configurator
     */
    public function setResolved(string $abstract, mixed $concrete): self
    {
        $this->resolved[$abstract] = $concrete;

        return $this;
    }

    /**
     * @param string $abstract
     * @return mixed
     */
    public function getResolved(string $abstract): mixed
    {
        return $this->resolved[$abstract];
    }
}
